<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Associado;

class AssociadoController extends Controller
{
    public function index()
    {
        $associados = Associado::all();
        return view('associados.index', compact('associados'));
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
        ]);

        Associado::create($request->all());

        return redirect()->route('associados.index')->with('success', 'Associado cadastrado com sucesso!');
    }
}