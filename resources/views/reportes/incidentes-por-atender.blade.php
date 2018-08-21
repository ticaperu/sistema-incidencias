@extends('layouts.app')
@section('content')
    <div class="container">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Dirección</th>
                    <th>Urbanización</th>
                    <th>Territorio Vecinal</th>
                    <th>Alcalce Vecinal</th>
                </tr>
            </thead>
            <tbody>
                @if(count($incidentes) === 0)
                    <tr>
                        <td colspan="5">No existen registros</td>
                    </tr>
                @else
                    @foreach($incidentes as $incidente)
                    <tr>
                        <td>{{ $incidente->fecha }}</td>
                        <td>{{ $incidente->direccion }}</td>
                        <td>{{ $incidente->urbanizacion->descripcion }}</td>
                        <td>{{ $incidente->urbanizacion->territorioVecinal->descripcion }}</td>
                        <td>{{ $incidente->persona->nombres }} {{ $incidente->persona->ape_paterno }} {{ $incidente->persona->ape_materno }}</td>
                    </tr>
                    @endforeach
                @endif 
            </tbody>
        </table>
    </div>
@endsection