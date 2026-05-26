@extends('layouts.app')

@section('title', 'Cadastrar Reunião - Sistema de Associados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">✚ Cadastrar Reunião</h1>
        <p class="text-muted mb-0">Preencha os dados da reunião e anexe a ata em PDF se houver.</p>
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

    <form method="POST" action="{{ route('reunioes.store') }}" enctype="multipart/form-data" class="form-container">
        @csrf

        <div class="form-section">
            <h3 class="form-section__title">Informações da Reunião</h3>

            <div class="mb-3">
                <label for="titulo" class="form-label">📌 Título *</label>
                <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo"
                    value="{{ old('titulo') }}" required maxlength="255"
                    placeholder="Ex: Reunião de planejamento 2026">
                <small class="text-muted">Máximo 255 caracteres.</small>
                @error('titulo')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">📝 Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="5" maxlength="2000"
                    placeholder="Descreva os tópicos principais da reunião...">{{ old('descricao') }}</textarea>
                <small class="text-muted">Máximo 2000 caracteres.</small>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="data" class="form-label">📅 Data *</label>
                    <input type="date" class="form-control @error('data') is-invalid @enderror" id="data"
                        name="data" value="{{ old('data') }}" required>
                    @error('data')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="horario" class="form-label">🕐 Horário *</label>
                    <select class="form-select @error('horario') is-invalid @enderror" id="horario" name="horario" required>
                        <option value="">Selecione um horário</option>
                        @foreach(range(0, 23) as $hour)
                        @php $value = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                        <option value="{{ $value }}" {{ old('horario') === $value ? 'selected' : '' }}>{{ $value }}</option>
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
                <small class="text-muted">Opcionalmente, anexe o PDF da ata. Máximo 10 MB.</small>
                @error('ata')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">✓ Cadastrar Reunião</button>
            <a href="{{ route('reunioes.index') }}" class="btn btn-secondary btn-lg">Cancelar</a>
        </div>
    </form>
</div>
@endsection