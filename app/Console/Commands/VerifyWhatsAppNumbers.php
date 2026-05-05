<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Associado;
use Illuminate\Support\Facades\Http;

class VerifyWhatsAppNumbers extends Command
{
    protected $signature = 'whatsapp:verify-numbers';
    protected $description = 'Verifica quais números de associados são válidos no WhatsApp';

    public function handle()
    {
        $associados = Associado::whereNotNull('telefone')->get();

        if ($associados->isEmpty()) {
            $this->error('Nenhum associado com telefone encontrado.');
            return;
        }

        $this->info("Verificando " . count($associados) . " número(s)...\n");

        $validos = [];
        $invalidos = [];

        foreach ($associados as $associado) {
            try {
                $response = Http::timeout(10)->post(
                    env('WHATSAPP_SERVICE_URL', 'http://localhost:3001') . '/api/check-number',
                    ['numero' => $associado->telefone]
                );

                $dados = $response->json();

                if ($dados['existe']) {
                    $validos[] = $associado;
                    $this->line("✅ {$associado->nome}: {$associado->telefone}");
                } else {
                    $invalidos[] = $associado;
                    $this->line("❌ {$associado->nome}: {$associado->telefone} - {$dados['dica']}");
                }
            } catch (\Exception $e) {
                $invalidos[] = $associado;
                $this->error("⚠️ {$associado->nome}: {$associado->telefone} - Erro: {$e->getMessage()}");
            }
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Resumo:");
        $this->info("✅ Válidos: " . count($validos));
        $this->info("❌ Inválidos: " . count($invalidos));
        $this->info(str_repeat('=', 50));

        if (!empty($invalidos)) {
            $this->warn("\nAssociados com números inválidos no WhatsApp:");
            foreach ($invalidos as $associado) {
                $this->warn("  - {$associado->nome} ({$associado->telefone})");
            }
        }
    }
}
