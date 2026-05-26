@extends('layouts.app')

@section('title', 'Editar Reunião - Sistema de Associados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">✎ Editar Reunião</h1>
        <p class="text-muted mb-0">Atualize os dados e documentos da reunião.</p>
    </div>
    <a href="{{ route('reunioes.index') }}" class="btn btn-outline-secondary">← Voltar</a>
</div>

<div class="form-panel">
    @if ($errors->any())
    <div class="alert alert-danger mb-4">
        <strong>⚠ Erros encontrados:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
    $ataExists = $reuniao->ata_path && Storage::disk('public')->exists($reuniao->ata_path);
    @endphp

    <form method="POST" action="{{ route('reunioes.update', $reuniao) }}" enctype="multipart/form-data" class="form-container">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h3 class="form-section__title">Informações da Reunião</h3>

            <div class="mb-3">
                <label for="titulo" class="form-label">📌 Título *</label>
                <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo"
                    value="{{ old('titulo', $reuniao->titulo) }}" required maxlength="255"
                    placeholder="Ex: Reunião de planejamento 2026">
                <small class="text-muted">Máximo 255 caracteres.</small>
                @error('titulo')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">📝 Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="5" maxlength="2000">{{ old('descricao', $reuniao->descricao) }}</textarea>
                <small class="text-muted">Máximo 2000 caracteres.</small>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="data" class="form-label">📅 Data *</label>
                    <input type="date" class="form-control @error('data') is-invalid @enderror" id="data"
                        name="data" value="{{ old('data', $reuniao->data->format('Y-m-d')) }}" required>
                    @error('data')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="horario" class="form-label">🕐 Horário *</label>
                    <select class="form-select @error('horario') is-invalid @enderror" id="horario" name="horario" required>
                        @foreach(range(0, 23) as $hour)
                        @php $value = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                        <option value="{{ $value }}" {{ old('horario', $reuniao->horario) === $value ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('horario')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="form-section__title">Documentação</h3>

            <div class="mb-3">
                <label for="ata" class="form-label">📄 Ata da Reunião (PDF)</label>
                <div class="form-file-input">
                    <input type="file" class="form-control @error('ata') is-invalid @enderror" id="ata"
                        name="ata" accept="application/pdf">
                </div>
                <small class="text-muted">Máximo 10 MB. Deixe em branco para manter o arquivo atual.</small>
                @error('ata')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                @if($ataExists)
                <div class="mt-3 alert alert-success py-2">
                    <span class="small">✓ Arquivo atual disponível</span>
                    <a href="{{ route('reunioes.ata.download', $reuniao) }}" class="btn btn-sm btn-success ms-2">Baixar</a>
                </div>
                @elseif($reuniao->ata_path)
                <div class="mt-3 alert alert-warning py-2">
                    <span class="small">⚠ Registro existe, mas arquivo não encontrado</span>
                </div>
                @else
                <div class="mt-3 text-muted small">
                    <span>Nenhuma ata anexada. Você pode adicionar uma agora.</span>
                </div>
                @endif
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">✓ Salvar Alterações</button>
            <a href="{{ route('reunioes.index') }}" class="btn btn-secondary btn-lg">Cancelar</a>
        </div>
    </form>
</div>
@endsection