@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="/css/404.css">

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="container notfound-container">
                <div class="notfound">
                    <div class="notfound-404">
                        <h1 dusk="404">404</h1>
                    </div>
                    <h2 dusk="404-message">Oops, the page you are looking for could not be found!</h2>
                    <a href="{{ url()->previous() }}"><span class="arrow"></span>Go Back</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
