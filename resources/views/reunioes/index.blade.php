@extends('layouts.app')

@section('title', 'Reuniões - Sistema de Associados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Reuniões</h1>
        <p class="text-muted mb-0">Veja as reuniões futuras e passadas com filtros rápidos.</p>
    </div>
    <a href="{{ route('reunioes.create') }}" class="btn btn-success">Nova reunião</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reunioes.index', ['type' => 'all']) }}" class="btn btn-outline-secondary btn-sm">Todas</a>
            <a href="{{ route('reunioes.index', ['type' => 'futuras']) }}" class="btn btn-outline-primary btn-sm">Futuras</a>
            <a href="{{ route('reunioes.index', ['type' => 'passadas']) }}" class="btn btn-outline-secondary btn-sm">Passadas</a>
            <a href="{{ route('reunioes.index', ['period' => 'mes_atual']) }}" class="btn btn-outline-success btn-sm">Mês atual</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="section-header mb-3">
            <h2 class="section-title">📅 Reuniões Futuras</h2>
        </div>
        <div class="reuniao-cards-container">
            @forelse($reunioesFuturas as $reuniao)
            <div class="reuniao-card">
                <div class="reuniao-card__header">
                    <h3 class="reuniao-card__title">{{ $reuniao->titulo }}</h3>
                    <span class="reuniao-card__badge bg-primary">Em breve</span>
                </div>

                <div class="reuniao-card__body">
                    <div class="reuniao-info">
                        <span class="info-label">📅 Data:</span>
                        <span class="info-value">{{ $reuniao->data->format('d/m/Y') }}</span>
                    </div>
                    <div class="reuniao-info">
                        <span class="info-label">🕐 Horário:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</span>
                    </div>
                    <div class="reuniao-info">
                        <span class="info-label">📄 Ata:</span>
                        @if($reuniao->ata_path)
                        <span class="info-value"><span class="badge bg-success">Disponível</span></span>
                        @else
                        <span class="info-value text-muted">Sem ata</span>
                        @endif
                    </div>
                </div>

                <div class="reuniao-card__footer">
                    @if($reuniao->ata_path)
                    <button type="button" class="btn btn-sm btn-primary btn-visualizar-ata" data-url="{{ route('reunioes.ata.view', $reuniao) }}">Visualizar</button>
                    <a href="{{ route('reunioes.ata.download', $reuniao) }}" class="btn btn-sm btn-outline-success">Baixar</a>
                    @endif
                    <a href="{{ route('reunioes.show', $reuniao) }}" class="btn btn-sm btn-info">Ver</a>
                    <a href="{{ route('reunioes.edit', $reuniao) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form method="POST" action="{{ route('reunioes.destroy', $reuniao) }}" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar esta reunião?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Deletar</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <p class="text-muted">Nenhuma reunião futura encontrada.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="col-lg-6">
        <div class="section-header mb-3">
            <h2 class="section-title">📋 Reuniões Passadas</h2>
        </div>
        <div class="reuniao-cards-container">
            @forelse($reunioesPassadas as $reuniao)
            <div class="reuniao-card reuniao-card--past">
                <div class="reuniao-card__header">
                    <h3 class="reuniao-card__title">{{ $reuniao->titulo }}</h3>
                    <span class="reuniao-card__badge bg-secondary">Passada</span>
                </div>

                <div class="reuniao-card__body">
                    <div class="reuniao-info">
                        <span class="info-label">📅 Data:</span>
                        <span class="info-value">{{ $reuniao->data->format('d/m/Y') }}</span>
                    </div>
                    <div class="reuniao-info">
                        <span class="info-label">🕐 Horário:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</span>
                    </div>
                    <div class="reuniao-info">
                        <span class="info-label">📄 Ata:</span>
                        @if($reuniao->ata_path)
                        <span class="info-value"><span class="badge bg-success">Disponível</span></span>
                        @else
                        <span class="info-value text-muted">Sem ata</span>
                        @endif
                    </div>
                </div>

                <div class="reuniao-card__footer">
                    @if($reuniao->ata_path)
                    <button type="button" class="btn btn-sm btn-primary btn-visualizar-ata" data-url="{{ route('reunioes.ata.view', $reuniao) }}">Visualizar</button>
                    <a href="{{ route('reunioes.ata.download', $reuniao) }}" class="btn btn-sm btn-outline-success">Baixar</a>
                    @endif
                    <a href="{{ route('reunioes.show', $reuniao) }}" class="btn btn-sm btn-info">Ver</a>
                    <a href="{{ route('reunioes.edit', $reuniao) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form method="POST" action="{{ route('reunioes.destroy', $reuniao) }}" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar esta reunião?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Deletar</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <p class="text-muted">Nenhuma reunião passada encontrada.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:95%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">Visualizador de Ata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-0" style="height:80vh;">
                <iframe id="pdfViewer" src="" frameborder="0" style="width:100%;height:100%;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-visualizar-ata');
        if (!btn) return;
        var url = btn.getAttribute('data-url');
        var iframe = document.getElementById('pdfViewer');
        iframe.src = url;
        var modalEl = document.getElementById('pdfModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
        modalEl = modalEl;
        modalEl.addEventListener('hidden.bs.modal', function() {
            iframe.src = '';
        }, {
            once: true
        });
    });
</script>
@endsection