<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Associado;
use App\Models\Reuniao;

class AssociadoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $associados = Associado::query();

        if (in_array($status, ['em dia', 'atrasado'])) {
            $associados->where('status_pagamento', $status);
        }

        $associados = $associados->orderBy('nome')->get();

        return view('associados.index', compact('associados', 'status'));
    }

    public function create()
    {
        return view('associados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:associados',
            'telefone' => 'nullable|string|max:20',
            'status_pagamento' => 'required|in:em dia,atrasado',
        ]);

        Associado::create($request->all());

        return redirect()->route('associados.index')->with('success', 'Associado cadastrado com sucesso!');
    }

    public function show(Associado $associado)
    {
        $reunioesFuturas = Reuniao::where('data', '>=', today())->orderBy('data')->orderBy('horario')->get();
        $reunioesPassadas = Reuniao::where('data', '<', today())->orderBy('data', 'desc')->orderBy('horario')->get();

        return view('associados.show', compact('associado', 'reunioesFuturas', 'reunioesPassadas'));
    }

    public function updateStatus(Request $request, Associado $associado)
    {
        $request->validate([
            'status_pagamento' => 'required|in:em dia,atrasado',
        ]);

        $associado->update([
            'status_pagamento' => $request->status_pagamento,
        ]);

        return redirect()->back()->with('success', 'Status financeiro atualizado com sucesso!');
    }

    public function edit(Associado $associado)
    {
        return view('associados.edit', compact('associado'));
    }

    public function update(Request $request, Associado $associado)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:associados,cpf,' . $associado->id,
            'telefone' => 'nullable|string|max:20',
            'status_pagamento' => 'required|in:em dia,atrasado',
        ]);

        $associado->update($request->all());

        return redirect()->route('associados.index')->with('success', 'Associado atualizado com sucesso!');
    }

    public function destroy(Associado $associado)
    {
        $nome = $associado->nome;
        $associado->delete();

        return redirect()->route('associados.index')->with('success', "Associado \"$nome\" deletado com sucesso!");
    }
}
