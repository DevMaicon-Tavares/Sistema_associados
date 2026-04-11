@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Associados')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="text-muted mb-0">Bem-vindo, {{ Auth::user()->name }}!</p>
        </div>
        <div>
            <a href="{{ route('associados.index') }}" class="btn btn-primary me-2">Associados</a>
            <a href="{{ route('reunioes.index') }}" class="btn btn-secondary me-2">Reuniões</a>
            <a href="{{ route('reunioes.create') }}" class="btn btn-success">Nova reunião</a>
        </div>
    </div>

    <div class="row gy-3">
        <div class="col-md-4">
            <div class="card h-100 border-success">
                <div class="card-body text-success">
                    <h5 class="card-title">Associados em dia</h5>
                    <p class="display-6 mb-0">{{ $associadosEmDia }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-danger">
                <div class="card-body text-danger">
                    <h5 class="card-title">Associados atrasados</h5>
                    <p class="display-6 mb-0">{{ $associadosAtrasados }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-primary">
                <div class="card-body text-primary">
                    <h5 class="card-title">Próximas reuniões</h5>
                    <p class="display-6 mb-0">{{ $proximasReunioes->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Próximas reuniões</div>
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
                        @forelse($proximasReunioes as $reuniao)
                            <tr>
                                <td>{{ $reuniao->titulo }}</td>
                                <td>{{ $reuniao->data->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($reuniao->horario)->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Nenhuma reunião futura cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection