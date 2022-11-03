@extends('app')

@section('content')

        <div class="row">
            <div class="col-sm-4 offset-sm-4 col-md-4 offset-md-4">
                <div class="card card-group" style="margin-top: 50px; border: none">
                    <div class="card-body">
                        <div class="row text-center logo-container">
                            <img src="img/company_logo.png" alt="Inside Sales Solutions" class="scale-image">
                        </div>

                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <fieldset>
                                <div class="row" style="margin-top: 20px">
                                    <div class="col-md-10 offset-md-1 ">

                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                  <i class="fa fa-user"></i>
                                                </span>
                                                <input class="form-control" placeholder="Username" name="username" type="text" autofocus>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-lock"></i>
                                                    </span>
                                                <input class="form-control" placeholder="Password" name="password" autocomplete="off" type="password" value="">
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">
                                        </div>

                                        <div class="row text-center">
                                            <span class="col-md-12" style="color: red">
                                                @if($errors->has(null))
                                                    @foreach ($errors->all() as $error)
                                                        <p>{{ $error }}</p>
                                                    @endforeach
                                                @endif
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>

        </div>

@endsection