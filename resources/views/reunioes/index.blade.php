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
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reunioesFuturas as $reuniao)
                            <tr>
                                <td>{{ $reuniao->titulo }}</td>
                                <td>{{ $reuniao->data->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('reunioes.edit', $reuniao) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                                    <form method="POST" action="{{ route('reunioes.destroy', $reuniao) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja deletar esta reunião?')">Deletar</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Nenhuma reunião futura encontrada.</td>
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
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reunioesPassadas as $reuniao)
                            <tr>
                                <td>{{ $reuniao->titulo }}</td>
                                <td>{{ $reuniao->data->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('reunioes.edit', $reuniao) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                                    <form method="POST" action="{{ route('reunioes.destroy', $reuniao) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja deletar esta reunião?')">Deletar</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Nenhuma reunião passada encontrada.</td>
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