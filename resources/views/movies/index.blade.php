@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="container-fluid">
                
                <form method="GET" action="" class="row">

                    {{ csrf_field() }}

                    <input type="hidden" value="{{ request()->get('genre') }}" name="genre">

                    <div class="col-md-10 form-group">
                        <input value="{{ request()->get('title') }}" type="text" name="title" placeholder="Procure pelo título do filme aqui" class="form-control">
                    </div>

                    <div class="col-md-2 form-group">
                        <button type="submit" class="btn btn-block btn-primary pull-right"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>

                <hr>
                
                <movies movies="{{ json_encode($movies) }}" ></movies>
            </div>

            <hr>
            <div class="text-right">
                <div class="row">
                    <pagination source="{{ json_encode($movies) }}" ></pagination>
                </div>
            </div>

        </div>
    </div>
@endsection
