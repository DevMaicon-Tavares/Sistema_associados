@extends('layouts.app')

@section('title', 'Detalhes da Reunião - Sistema de Associados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">📅 Detalhes da Reunião</h1>
        <p class="text-muted mb-0">Veja os dados completos da reunião e a ata registrada.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reunioes.edit', $reuniao) }}" class="btn btn-warning">✎ Editar</a>
        <a href="{{ route('reunioes.index') }}" class="btn btn-outline-secondary">← Voltar</a>
    </div>
</div>

<div class="reuniao-detail-panel">
    @php
    $ataExists = $reuniao->ata_path && Storage::disk('public')->exists($reuniao->ata_path);
    @endphp

    <!-- Header -->
    <div class="detail-panel__header">
        <div>
            <h2 class="detail-panel__title">{{ $reuniao->titulo }}</h2>
            <p class="detail-panel__subtitle">{{ $reuniao->data->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</p>
        </div>
        <span class="status-badge status-badge--primary">📅 Agendada</span>
    </div>

    <!-- Conteúdo -->
    <div class="detail-panel__content">
        <!-- Descrição -->
        <div class="detail-section">
            <h3 class="detail-section__title">📝 Descrição</h3>
            <p class="detail-section__text">{{ $reuniao->descricao ?: 'Nenhuma descrição foi adicionada para esta reunião.' }}</p>
        </div>

        <!-- Ata -->
        <div class="detail-section">
            <h3 class="detail-section__title">📄 Ata da Reunião</h3>
            @if($ataExists)
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <button type="button" class="btn btn-primary btn-visualizar-ata" data-url="{{ route('reunioes.ata.view', $reuniao) }}">👁 Visualizar</button>
                <a href="{{ route('reunioes.ata.download', $reuniao) }}" class="btn btn-success">📥 Baixar (PDF)</a>
                <span class="text-muted small">Disponível para consulta futura.</span>
            </div>
            @elseif($reuniao->ata_path)
            <p class="text-warning mb-0">⚠ O registro de ata existe, mas o arquivo não foi encontrado.</p>
            @else
            <p class="text-muted mb-0">Nenhuma ata foi anexada para esta reunião.</p>
            @endif
        </div>
    </div>

    <!-- Metadata -->
    <div class="detail-panel__metadata">
        <div class="metadata-item">
            <span class="metadata-label">Registrado em:</span>
            <span class="metadata-value">{{ $reuniao->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="metadata-item">
            <span class="metadata-label">Última atualização:</span>
            <span class="metadata-value">{{ $reuniao->updated_at->format('d/m/Y H:i') }}</span>
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
        modalEl.addEventListener('hidden.bs.modal', function() {
            iframe.src = '';
        }, {
            once: true
        });
    });
</script>
@endsection