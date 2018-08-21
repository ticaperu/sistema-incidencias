@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Territorio Vecinal

                        <a href="{{route('territorio-vecinal.create')}}" class="btn btn-primary float-right">
                            Registrar
                        </a>
                        <a href="{{route('territorio-vecinal.view-all')}}" class="btn btn-primary float-right" style="margin-right: 10px">
                            Ver Mapa
                        </a>
                    
                    </div>

                    <div class="card-body">

                        <territorio-vecinal-list></territorio-vecinal-list>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection