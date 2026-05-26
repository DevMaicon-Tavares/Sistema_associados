<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reuniao;
use App\Models\Associado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function show(Reuniao $reuniao)
    {
        return view('reunioes.show', compact('reuniao'));
    }

    protected function validationRules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:2000',
            'data' => 'required|date',
            'horario' => 'required|date_format:H:i',
            'ata' => 'nullable|file|mimes:pdf|max:10240',
        ];
    }

    protected function validationMessages(): array
    {
        return [
            'titulo.required' => 'O título é obrigatório.',
            'titulo.string' => 'O título deve ser um texto válido.',
            'titulo.max' => 'O título não pode ter mais que 255 caracteres.',
            'descricao.string' => 'A descrição deve ser um texto válido.',
            'descricao.max' => 'A descrição não pode ter mais que 2000 caracteres.',
            'data.required' => 'A data é obrigatória.',
            'data.date' => 'A data deve ser válida.',
            'horario.required' => 'O horário é obrigatório.',
            'horario.date_format' => 'O horário deve seguir o formato 24h: 00:00 até 23:00.',
            'ata.file' => 'O arquivo da ata deve ser um arquivo válido.',
            'ata.mimes' => 'A ata deve ser um arquivo PDF.',
            'ata.max' => 'O PDF não pode ter mais que 10 MB.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules(), $this->validationMessages());

        $data = $request->only(['titulo', 'descricao', 'data', 'horario']);

        if ($request->hasFile('ata')) {
            $file = $request->file('ata');
            $filename = 'ata_reuniao_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $data['ata_path'] = $file->storeAs('atas', $filename, 'public');
        }

        $reuniao = Reuniao::create($data);

        // Enviar convites via WhatsApp Web
        try {
            $associados = Associado::whereNotNull('telefone')->get();
            Log::info('Associados encontrados: ' . count($associados));

            if (count($associados) === 0) {
                Log::warning('Nenhum associado com telefone cadastrado');
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
                Log::info('Chamando serviço WhatsApp em: ' . $whatsappUrl);
                Log::info('Enviando ' . count($mensagens) . ' mensagens');

                $response = Http::timeout(30)->post($whatsappUrl, ['mensagens' => $mensagens]);

                Log::info('Resposta do serviço WhatsApp: ' . $response->status());
                Log::info('Detalhes: ' . $response->body());

                if (!$response->successful()) {
                    Log::error('Erro na resposta do WhatsApp: ' . $response->body());
                }
            } else {
                Log::warning('Nenhuma mensagem para enviar');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao enviar convites WhatsApp: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return redirect()->route('reunioes.index')->with('success', 'Reunião cadastrada com sucesso! Convites enviados via WhatsApp.');
    }

    public function edit(Reuniao $reuniao)
    {
        return view('reunioes.edit', compact('reuniao'));
    }

    public function downloadAta(Reuniao $reuniao)
    {
        if (!$reuniao->ata_path || !Storage::disk('public')->exists($reuniao->ata_path)) {
            return redirect()->back()->with('error', 'Arquivo de ata não encontrado.');
        }

        return response()->download(Storage::disk('public')->path($reuniao->ata_path), basename($reuniao->ata_path));
    }

    public function viewAta(Reuniao $reuniao)
    {
        if (!$reuniao->ata_path || !Storage::disk('public')->exists($reuniao->ata_path)) {
            abort(404, 'Arquivo de ata não encontrado.');
        }

        $path = Storage::disk('public')->path($reuniao->ata_path);

        return response()->file($path);
    }

    public function update(Request $request, Reuniao $reuniao)
    {
        $request->validate($this->validationRules(), $this->validationMessages());

        $data = $request->only(['titulo', 'descricao', 'data', 'horario']);

        if ($request->hasFile('ata')) {
            if ($reuniao->ata_path) {
                Storage::disk('public')->delete($reuniao->ata_path);
            }

            $file = $request->file('ata');
            $filename = 'ata_reuniao_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $data['ata_path'] = $file->storeAs('atas', $filename, 'public');
        }

        $reuniao->update($data);

        return redirect()->route('reunioes.index')->with('success', 'Reunião atualizada com sucesso!');
    }

    public function destroy(Reuniao $reuniao)
    {
        $titulo = $reuniao->titulo;

        if ($reuniao->ata_path) {
            Storage::disk('public')->delete($reuniao->ata_path);
        }

        $reuniao->delete();

        return redirect()->route('reunioes.index')->with('success', "Reunião \"$titulo\" deletada com sucesso!");
    }
}
