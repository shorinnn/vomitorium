@extends('layouts.master')

@section('content')
<div class="container">

    <div class="row">

        <div class="col-lg-12">
            <h1 class="page-header">404
                <small>Page Not Found</small>
            </h1>
            <p class="error-404">Oops... hmm... wait...</p>
            <div class="text-center">
                {{HTML::image('assets/img/oops.gif','Oops')}}
            </div>
        </div>
    </div>
</div>
@stop