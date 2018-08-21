@extends('layouts.app')
@section('content')
    <div class="container">
        <a href="{{ route('reportes.ciudadanos_puntuacion.excel') }}">Exportar</a>
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>DNI</th>
                    <th>Puntaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result as $item)
                <tr>
                    <td>{{ $item->nombres }}</td>
                    <td>{{ $item->ape_paterno }}</td>
                    <td>{{ $item->ape_materno }}</td>
                    <td>{{ $item->dni }}</td>
                    <td>{{ $item->puntaje }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection