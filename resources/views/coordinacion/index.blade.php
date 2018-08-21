@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                       Coordinaciones

                        <a href="{{route('coordinacion.create')}}" class="btn btn-primary float-right">
                            Registrar
                        </a>
                    
                    </div>

                    <div class="card-body">

                        <coordinacion-list></coordinacion-list>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection