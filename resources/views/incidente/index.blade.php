@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Incidentes

                        <a href="{{route('incidente.create')}}" class="btn btn-primary float-right">
                            Registrar
                        </a>
                    
                    </div>

                    <div class="card-body">

                        <incidente-list></incidente-list>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection