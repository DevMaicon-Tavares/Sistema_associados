@extends('layouts.app')

@section('title', 'Editar Associado - Sistema de Associados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">✎ Editar Associado</h1>
        <p class="text-muted mb-0">Atualize os dados de <strong>{{ $associado->nome }}</strong>.</p>
    </div>
    <a href="{{ route('associados.index') }}" class="btn btn-outline-secondary">← Voltar</a>
</div>

<div class="form-panel">
    <form method="POST" action="{{ route('associados.update', $associado) }}" class="form-container">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h3 class="form-section__title">Informações Pessoais</h3>

            <div class="mb-3">
                <label for="nome" class="form-label">👤 Nome *</label>
                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome"
                    value="{{ old('nome', $associado->nome) }}" required placeholder="Nome completo">
                @error('nome')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="cpf" class="form-label">🆔 CPF *</label>
                <input type="text" class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf"
                    value="{{ old('cpf', $associado->cpf) }}" required placeholder="000.000.000-00">
                @error('cpf')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="telefone" class="form-label">📞 Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone"
                    value="{{ old('telefone', $associado->telefone) }}" placeholder="(11) 99999-9999">
            </div>
        </div>

        <div class="form-section">
            <h3 class="form-section__title">Status Financeiro</h3>

            <div class="mb-3">
                <label for="status_pagamento" class="form-label">💰 Status de Pagamento *</label>
                <select id="status_pagamento" name="status_pagamento" class="form-select @error('status_pagamento') is-invalid @enderror" required>
                    <option value="em dia" @selected(old('status_pagamento', $associado->status_pagamento) === 'em dia')>✓ Em dia</option>
                    <option value="atrasado" @selected(old('status_pagamento', $associado->status_pagamento) === 'atrasado')>⚠ Atrasado</option>
                </select>
                @error('status_pagamento')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">✓ Salvar Alterações</button>
            <a href="{{ route('associados.index') }}" class="btn btn-secondary btn-lg">Cancelar</a>
        </div>
    </form>
</div>
@endsection