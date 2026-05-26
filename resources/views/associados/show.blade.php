@extends('layouts.app')

@section('title', 'Área do Associado - ' . $associado->nome)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">👤 Detalhes do Associado</h1>
        <p class="text-muted mb-0">Informações de <strong>{{ $associado->nome }}</strong> e suas reuniões.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('associados.edit', $associado) }}" class="btn btn-warning">✎ Editar</a>
        <a href="{{ route('associados.index') }}" class="btn btn-outline-secondary">← Voltar</a>
    </div>
</div>

<!-- Painel de Informações do Associado -->
<div class="reuniao-detail-panel mb-4">
    <!-- Header -->
    <div class="detail-panel__header">
        <div>
            <h2 class="detail-panel__title">{{ $associado->nome }}</h2>
            <p class="detail-panel__subtitle">Associado desde {{ $associado->created_at->format('d/m/Y') }}</p>
        </div>
        @if($associado->status_pagamento === 'em dia')
        <span class="status-badge status-badge--em-dia">✓ Em dia</span>
        @else
        <span class="status-badge status-badge--atrasado">⚠ Atrasado</span>
        @endif
    </div>

    <!-- Conteúdo -->
    <div class="detail-panel__content">
        <!-- Informações -->
        <div class="detail-section">
            <h3 class="detail-section__title">📋 Informações Pessoais</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="metadata-item">
                        <span class="metadata-label">🆔 CPF</span>
                        <span class="metadata-value">{{ $associado->cpf }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="metadata-item">
                        <span class="metadata-label">📞 Telefone</span>
                        <span class="metadata-value">{{ $associado->telefone ?? '—' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="metadata-item">
                        <span class="metadata-label">💰 Status Financeiro</span>
                        <span class="metadata-value">
                            @if($associado->status_pagamento === 'em dia')
                            <span class="badge bg-success">✓ Em dia</span>
                            @else
                            <span class="badge bg-danger">⚠ Atrasado</span>
                            @endif
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="metadata-item">
                        <span class="metadata-label">📅 Registrado em</span>
                        <span class="metadata-value">{{ $associado->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atualizar Status -->
        <form method="POST" action="{{ route('associados.updateStatus', $associado) }}" class="detail-section">
            @csrf
            @method('PATCH')
            <h3 class="detail-section__title">🔄 Atualizar Status Financeiro</h3>
            <div class="d-flex gap-2 align-items-end">
                <div class="flex-grow-1">
                    <select id="status_pagamento" name="status_pagamento" class="form-select">
                        <option value="em dia" @selected($associado->status_pagamento === 'em dia')>✓ Em dia</option>
                        <option value="atrasado" @selected($associado->status_pagamento === 'atrasado')>⚠ Atrasado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Atualizar</button>
            </div>
        </form>
    </div>

    <!-- Metadata -->
    <div class="detail-panel__metadata">
        <div class="metadata-item">
            <span class="metadata-label">Total de reuniões:</span>
            <span class="metadata-value">{{ count($reunioesFuturas) + count($reunioesPassadas) }}</span>
        </div>
        <div class="metadata-item">
            <span class="metadata-label">Próximas reuniões:</span>
            <span class="metadata-value">{{ count($reunioesFuturas) }}</span>
        </div>
        <div class="metadata-item">
            <span class="metadata-label">Reuniões passadas:</span>
            <span class="metadata-value">{{ count($reunioesPassadas) }}</span>
        </div>
    </div>
</div>
<h3 class="section-title">📋 Reuniões Passadas</h3>
</div>
<div class="reuniao-mini-cards">
    @forelse($reunioesPassadas as $reuniao)
    <div class="reuniao-mini-card reuniao-mini-card--past">
        <div class="reuniao-mini-card__title">{{ $reuniao->titulo }}</div>
        <div class="reuniao-mini-card__meta">
            <span class="meta-item">📅 {{ $reuniao->data->format('d/m/Y') }}</span>
            <span class="meta-item">🕐 {{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</span>
        </div>
    </div>
    @empty
    <div class="text-center py-4">
        <p class="text-muted">Nenhuma reunião passada registrada.</p>
    </div>
    @endforelse
</div>
</div>
</div>
@endsection