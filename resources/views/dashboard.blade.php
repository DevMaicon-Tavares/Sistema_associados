@extends('layouts.app')

@section('title', 'Painel - Sistema de Associados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">📊 Painel</h1>
        <p class="text-muted mb-0">Bem-vindo, <strong>{{ Auth::user()->name }}</strong>!</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('associados.index') }}" class="btn btn-info">👥 Associados</a>
        <a href="{{ route('reunioes.index') }}" class="btn btn-primary">📅 Reuniões</a>
        <a href="{{ route('reunioes.create') }}" class="btn btn-success">✚ Nova Reunião</a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card kpi-card--success">
            <div class="kpi-card__icon">✓</div>
            <div class="kpi-card__content">
                <p class="kpi-card__label">Associados em dia</p>
                <p class="kpi-card__value">{{ $associadosEmDia }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card kpi-card--danger">
            <div class="kpi-card__icon">⚠</div>
            <div class="kpi-card__content">
                <p class="kpi-card__label">Associados atrasados</p>
                <p class="kpi-card__value">{{ $associadosAtrasados }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card kpi-card--primary">
            <div class="kpi-card__icon">📅</div>
            <div class="kpi-card__content">
                <p class="kpi-card__label">Próximas reuniões</p>
                <p class="kpi-card__value">{{ $proximasReunioes->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Próximas Reuniões -->
<div class="section-header mb-3">
    <h2 class="section-title">📅 Próximas Reuniões</h2>
</div>
<div class="reuniao-mini-cards">
    @forelse($proximasReunioes as $reuniao)
    <div class="reuniao-mini-card">
        <div class="reuniao-mini-card__title">{{ $reuniao->titulo }}</div>
        <div class="reuniao-mini-card__meta">
            <span class="meta-item">📅 {{ $reuniao->data->format('d/m/Y') }}</span>
            <span class="meta-item">🕐 {{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</span>
        </div>
        <div class="reuniao-mini-card__action">
            <a href="{{ route('reunioes.show', $reuniao) }}" class="btn btn-sm btn-primary">Ver detalhes →</a>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <p class="text-muted">Nenhuma reunião futura cadastrada.</p>
        <a href="{{ route('reunioes.create') }}" class="btn btn-primary mt-2">Agendar primeira reunião</a>
    </div>
    @endforelse
</div>
@endsection