@extends('layouts.app')

@section('title', 'Editar Associado - Sistema de Associados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Editar Associado</h1>
        <p class="text-muted mb-0">Atualize os dados do associado.</p>
    </div>
    <a href="{{ route('associados.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card p-4">
    <form method="POST" action="{{ route('associados.update', $associado) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $associado->nome) }}" required>
        </div>
        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" class="form-control" id="cpf" name="cpf" value="{{ old('cpf', $associado->cpf) }}" required>
        </div>
        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" class="form-control" id="telefone" name="telefone" value="{{ old('telefone', $associado->telefone) }}">
        </div>
        <div class="mb-3">
            <label for="status_pagamento" class="form-label">Status de pagamento</label>
            <select id="status_pagamento" name="status_pagamento" class="form-select" required>
                <option value="em dia" @selected(old('status_pagamento', $associado->status_pagamento) === 'em dia')>Em dia</option>
                <option value="atrasado" @selected(old('status_pagamento', $associado->status_pagamento) === 'atrasado')>Atrasado</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="{{ route('associados.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection