@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Tipo de Obstaculo
                        <a href="{{route('tipo-obstaculo.create')}}" class="btn btn-primary float-right">
                            Registrar
                        </a>
                    </div>
                    <div class="card-body">
                        <tipo-obstaculo-list></tipo-obstaculo-list>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection