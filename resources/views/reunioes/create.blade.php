@extends('layouts.app')

@section('title', 'Cadastrar Reunião - Sistema de Associados')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Cadastrar Reunião</h1>
            <p class="text-muted mb-0">Cadastre uma nova reunião para exibir aos associados.</p>
        </div>
        <a href="{{ route('reunioes.index') }}" class="btn btn-outline-secondary">Voltar</a>
    </div>

    <div class="card p-4">
        <form method="POST" action="{{ route('reunioes.store') }}">
            @csrf
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="4">{{ old('descricao') }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="data" class="form-label">Data</label>
                    <input type="date" class="form-control" id="data" name="data" value="{{ old('data') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="horario" class="form-label">Horário</label>
                    <input type="time" class="form-control" id="horario" name="horario" value="{{ old('horario') }}" required>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
                <a href="{{ route('reunioes.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
