@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Nivel Agua

                        <a href="{{route('nivel-agua.create')}}" class="btn btn-primary float-right">
                            Registrar
                        </a>
                    
                    </div>

                    <div class="card-body">

                        <nivel-agua-list></nivel-agua-list>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection