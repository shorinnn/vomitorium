@extends('layouts.master')

@section('content')
<div class="container">

    <div class="row">

        <div class="col-lg-12">
            <?php
                switch( rand(1, 4)){
                    case 1: $line1 = 'Sorry about that'; $line2 = 'An error occurred.'; break;
                    case 2: $line1 = 'Oh no! Not this again'; $line2 = 'Seems like something went wrong'; break;
                    case 3: $line1 = 'An error, no biggie.'; $line2 = 'We got this covered, we already have a hunch about what went wrong'; break;
                    case 4: $line1 = 'ERROR-B-GONE!'; $line2 = 'Thanks to the information sent by your browser we will solve in a matter of minutes (or hours)'; break;
                }
            ?>
            <h1 class="page-header">{{$line1}} -
                <small>{{$line2}}</small>
            </h1>
            <p>Please let us know what happened by emailing us at cocorium@gmail.com</p>
            <p>We'll get back to you pretty soon and get you going.</p>
            <br />
<!--            <h3>Debug Details</h3>
            <div style='overflow-y: scroll; height:100px; border:1px solid silver; padding:10px'>
                <p>{{$code}}</p>
                <p>{{$exception}}</p>
            </div>-->
        </div>
    </div>
</div>
@stop