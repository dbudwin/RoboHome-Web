@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="container">
                <div class="jumbotron">
                    <h2>
                        Welcome to RoboHome
                    </h2>
                    <p>
                        RoboHome is a SaaS tool that also integrates with Amazon's Echo to enable control of semi-connected devices (think IR, and RF) in your house over wifi!
                    </p>
                    <p>
                        <a class="btn btn-primary btn-large" href="{{ route('login') }}"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Log In</a>
                        <a href="{{ route('register') }}">Register</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
