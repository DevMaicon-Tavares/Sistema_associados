@extends('layouts.app')

@section('title', 'Área do Associado - ' . $associado->nome)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Área do Associado</h1>
        <p class="text-muted mb-0">Detalhes e reuniões para {{ $associado->nome }}.</p>
    </div>
    <a href="{{ route('associados.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">{{ $associado->nome }}</h5>
        <div class="row gy-3">
            <div class="col-md-4">
                <p class="mb-1 text-muted">CPF</p>
                <p class="fw-semibold">{{ $associado->cpf }}</p>
            </div>
            <div class="col-md-4">
                <p class="mb-1 text-muted">Telefone</p>
                <p class="fw-semibold">{{ $associado->telefone ?? 'Não informado' }}</p>
            </div>
            <div class="col-md-4">
                <p class="mb-1 text-muted">Status financeiro</p>
                @if($associado->status_pagamento === 'em dia')
                <span class="badge bg-success py-2 px-3">Em dia</span>
                @else
                <span class="badge bg-danger py-2 px-3">Atrasado</span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('associados.updateStatus', $associado) }}" class="row g-3 mt-4">
            @csrf
            @method('PATCH')
            <div class="col-md-4">
                <label for="status_pagamento" class="form-label">Atualizar status</label>
                <select id="status_pagamento" name="status_pagamento" class="form-select">
                    <option value="em dia" @selected($associado->status_pagamento === 'em dia')>Em dia</option>
                    <option value="atrasado" @selected($associado->status_pagamento === 'atrasado')>Atrasado</option>
                </select>
            </div>
            <div class="col-md-auto align-self-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">Reuniões Futuras</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Data</th>
                                <th>Horário</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reunioesFuturas as $reuniao)
                            <tr>
                                <td>{{ $reuniao->titulo }}</td>
                                <td>{{ $reuniao->data->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Nenhuma reunião futura.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-secondary text-white">Reuniões Passadas</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Data</th>
                                <th>Horário</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reunioesPassadas as $reuniao)
                            <tr>
                                <td>{{ $reuniao->titulo }}</td>
                                <td>{{ $reuniao->data->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Nenhuma reunião passada.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection