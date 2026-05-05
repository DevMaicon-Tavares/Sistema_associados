<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Associado;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function status()
    {
        try {
            $response = Http::timeout(5)->get(
                env('WHATSAPP_SERVICE_URL', 'http://localhost:3001') . '/api/status'
            );

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'conectado' => false,
                'status' => 'offline',
                'erro' => 'Serviço não acessível'
            ], 500);
        }
    }
}
