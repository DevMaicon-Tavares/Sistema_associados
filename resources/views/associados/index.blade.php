@extends('layouts.app')

@section('title', 'Associados - Sistema de Associados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Associados</h1>
        <p class="text-muted mb-0">Lista de associados e controle de status financeiro.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('associados.create') }}" class="btn btn-primary">Novo associado</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label">Filtrar por status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="em dia" @selected(request('status')==='em dia' )>Em dia</option>
                    <option value="atrasado" @selected(request('status')==='atrasado' )>Atrasado</option>
                </select>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-secondary">Aplicar filtro</button>
            </div>
        </form>
    </div>
</div>

<div class="section-header mb-4">
    <h2 class="section-title">👥 Lista de Associados</h2>
</div>

<div class="associado-cards-container">
    @forelse($associados as $associado)
    <div class="associado-card @if($associado->status_pagamento === 'atrasado') associado-card--overdue @endif">
        <div class="associado-card__header">
            <div>
                <h3 class="associado-card__name">{{ $associado->nome }}</h3>
                <p class="associado-card__cpf">CPF: {{ $associado->cpf }}</p>
            </div>
            @if($associado->status_pagamento === 'em dia')
            <span class="associado-card__badge bg-success">✓ Em dia</span>
            @else
            <span class="associado-card__badge bg-danger">⚠ Atrasado</span>
            @endif
        </div>

        <div class="associado-card__body">
            <div class="associado-info">
                <span class="info-label">📞 Telefone:</span>
                <span class="info-value">{{ $associado->telefone ?? '—' }}</span>
            </div>
            <div class="associado-info">
                <span class="info-label">💰 Status:</span>
                <span class="info-value">
                    @if($associado->status_pagamento === 'em dia')
                    <span class="badge bg-success" style="font-size: 0.8rem;">Em dia</span>
                    @else
                    <span class="badge bg-danger" style="font-size: 0.8rem;">Atrasado</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="associado-card__footer">
            <a href="{{ route('associados.show', $associado) }}" class="btn btn-sm btn-info">Ver Área</a>
            <a href="{{ route('associados.edit', $associado) }}" class="btn btn-sm btn-warning">Editar</a>
            <form method="POST" action="{{ route('associados.destroy', $associado) }}" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar este associado?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Deletar</button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-5 col-12">
        <p class="text-muted">Nenhum associado encontrado.</p>
        <p class="text-muted small">Clique em "Novo associado" para cadastrar um.</p>
    </div>
    @endforelse
</div>
@endsection