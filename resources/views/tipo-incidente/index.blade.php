@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Tipos de Incidentes

                        <a href="{{route('tipo-incidente.create')}}" class="btn btn-primary float-right">
                            Registrar
                        </a>
                    
                    </div>

                    <div class="card-body">

                        <tipo-incidente-list></tipo-incidente-list>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection