<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reuniao;
use App\Models\Associado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ReuniaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Reuniao::query();
        $type = $request->query('type');
        $period = $request->query('period');

        if ($type === 'futuras') {
            $query->where('data', '>=', Carbon::today());
        }

        if ($type === 'passadas') {
            $query->where('data', '<', Carbon::today());
        }

        if ($period === 'mes_atual') {
            $query->whereBetween('data', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }

        $reunioesFuturas = (clone $query)->where('data', '>=', Carbon::today())->orderBy('data')->orderBy('horario')->get();
        $reunioesPassadas = (clone $query)->where('data', '<', Carbon::today())->orderBy('data', 'desc')->orderBy('horario')->get();

        return view('reunioes.index', compact('reunioesFuturas', 'reunioesPassadas', 'type', 'period'));
    }

    public function create()
    {
        return view('reunioes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:2000',
            'data' => 'required|date',
            'horario' => 'required|date_format:H:i',
        ]);

        $reuniao = Reuniao::create($request->all());

        // Enviar convites via WhatsApp Web
        try {
            $associados = Associado::whereNotNull('telefone')->get();
            \Log::info('Associados encontrados: ' . count($associados));

            if (count($associados) === 0) {
                \Log::warning('Nenhum associado com telefone cadastrado');
            }

            $mensagens = [];

            foreach ($associados as $associado) {
                $mensagem = "Olá {$associado->nome}, você está convidado para a reunião: *{$reuniao->titulo}*\n\n";
                $mensagem .= "📅 Data: {$reuniao->data->format('d/m/Y')}\n";
                $mensagem .= "🕐 Hora: {$reuniao->horario}\n";
                if ($reuniao->descricao) {
                    $mensagem .= "📝 Descrição: {$reuniao->descricao}\n";
                }

                $mensagens[] = [
                    'numero' => $associado->telefone,
                    'mensagem' => $mensagem
                ];
            }

            // Chamar o serviço Node.js para enviar as mensagens
            if (!empty($mensagens)) {
                $whatsappUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3001') . '/api/send-messages';
                \Log::info('Chamando serviço WhatsApp em: ' . $whatsappUrl);
                \Log::info('Enviando ' . count($mensagens) . ' mensagens');

                $response = Http::timeout(30)->post($whatsappUrl, ['mensagens' => $mensagens]);

                \Log::info('Resposta do serviço WhatsApp: ' . $response->status());
                \Log::info('Detalhes: ' . $response->body());

                if (!$response->successful()) {
                    \Log::error('Erro na resposta do WhatsApp: ' . $response->body());
                }
            } else {
                \Log::warning('Nenhuma mensagem para enviar');
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar convites WhatsApp: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return redirect()->route('reunioes.index')->with('success', 'Reunião cadastrada com sucesso! Convites enviados via WhatsApp.');
    }

    public function edit(Reuniao $reuniao)
    {
        return view('reunioes.edit', compact('reuniao'));
    }

    public function update(Request $request, Reuniao $reuniao)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:2000',
            'data' => 'required|date',
            'horario' => 'required|date_format:H:i',
        ]);

        $reuniao->update($request->all());

        return redirect()->route('reunioes.index')->with('success', 'Reunião atualizada com sucesso!');
    }

    public function destroy(Reuniao $reuniao)
    {
        $titulo = $reuniao->titulo;
        $reuniao->delete();

        return redirect()->route('reunioes.index')->with('success', "Reunião \"$titulo\" deletada com sucesso!");
    }
}
