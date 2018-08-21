@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Tipo de Persona

                        <a href="{{route('tipo-persona.create')}}" class="btn btn-primary float-right">
                            Registrar
                        </a>
                    
                    </div>

                    <div class="card-body">

                        <tipo-persona-list></tipo-persona-list>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection