@extends('layouts.app')

@section('title', 'Associados - Sistema de Associados')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Associados</h1>
            <p class="text-muted mb-0">Lista de associados e controle de status financeiro.</p>
        </div>
        <a href="{{ route('associados.create') }}" class="btn btn-primary">Novo associado</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Filtrar por status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="em dia" @selected(request('status') === 'em dia')>Em dia</option>
                        <option value="atrasado" @selected(request('status') === 'atrasado')>Atrasado</option>
                    </select>
                </div>
                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-secondary">Aplicar filtro</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($associados as $associado)
                    <tr>
                        <td>{{ $associado->nome }}</td>
                        <td>{{ $associado->cpf }}</td>
                        <td>{{ $associado->telefone ?? '—' }}</td>
                        <td>
                            @if($associado->status_pagamento === 'em dia')
                                <span class="badge bg-success">Em dia</span>
                            @else
                                <span class="badge bg-danger">Atrasado</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('associados.show', $associado) }}" class="btn btn-sm btn-outline-primary">Área</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Nenhum associado encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection