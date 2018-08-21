@extends('layouts.app')
@section('content')
    <div class="container">
        <div>
            <form action="{{ route('reportes.incidentes-por-tipo-incidente') }}">
                <div class="form-group row">
                    <select name="tipo_incidente_id" id="tipo_incidente_id" class="col-md-3">
                        <option value="0">-- Seleccionar --</option>
                        @foreach($tipoIncidente as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->descripcion }}</option>
                        @endforeach
                    </select>
                    <input type="date" class="form-control datepicker col-md-3" name="fecha_inicio" id="fecha_inicio" placeholder="Fecha de Inicio"/>
                    <input type="date" class="form-control datepicker col-md-3" name="fecha_final" id="fecha_final" placeholder="Fecha de Fin"/>
                    <input type="submit" class="btn btn-primary">
                </div>
            </form>
        </div>
        <div>
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
    </div>
@endsection