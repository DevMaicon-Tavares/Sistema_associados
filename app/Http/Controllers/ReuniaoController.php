<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reuniao;
use Carbon\Carbon;

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

        Reuniao::create($request->all());

        return redirect()->route('reunioes.index')->with('success', 'Reunião cadastrada com sucesso!');
    }
}
