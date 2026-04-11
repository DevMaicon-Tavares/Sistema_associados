@extends('layouts.app')

@section('title', 'Novo Associado - Sistema de Associados')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Cadastrar Associado</h1>
            <p class="text-muted mb-0">Preencha os dados do associado e defina o status financeiro.</p>
        </div>
        <a href="{{ route('associados.index') }}" class="btn btn-outline-secondary">Voltar</a>
    </div>

    <div class="card p-4">
        <form method="POST" action="{{ route('associados.store') }}">
            @csrf
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" required>
            </div>
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" value="{{ old('cpf') }}" required>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" value="{{ old('telefone') }}">
            </div>
            <div class="mb-3">
                <label for="status_pagamento" class="form-label">Status de pagamento</label>
                <select id="status_pagamento" name="status_pagamento" class="form-select" required>
                    <option value="em dia" @selected(old('status_pagamento') === 'em dia')>Em dia</option>
                    <option value="atrasado" @selected(old('status_pagamento') === 'atrasado')>Atrasado</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="{{ route('associados.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection