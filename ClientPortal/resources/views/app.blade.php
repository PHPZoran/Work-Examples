<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('title')
    <title>Inside Sales Solutions - Client Portal</title>


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	@include('analytics')

    {{--<script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>--}}

</head>
<body>

@if(Request::route()->uri !== 'login')
    <nav class="navbar navbar-expand-lg bg-light navbar-fixed-top">
        <div class="navbar-header navbar-brand">
            <img src="img/company_logo.png" alt="ISS Portal" style="max-width: 125px; max-height: 50px;">
        </div>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item ml-aut">
                    <a class="nav-link" href="http://isaless.com">Inside Sales Website</a>
                </li>
                <li class="nav-item ml-aut">
                    <a class="nav-link" href="/report" @if(Request::route()->uri !== 'account')style="color: blue" @else style="" @endif>Inside Sales Solutions Dashboard</a>
                </li>
                <li class="nav-item ml-aut">
                    <a class="nav-link" href="/account" @if(Request::route()->uri !== 'report')style="color: blue" @else style="" @endif>Inside Sales Solutions List Builder</a>
                </li>
                <li class="nav-item ml-aut">
                    <a class="nav-link" >CALL US: 347-918-4747</a>
                </li>
                <li class="pull-left ml-aut">
                    <a class="nav-link" ><script type="IN/FollowCompany" data-id="2512818" data-counter="none"></script></a>
                </li>
                <li class="nav-item ml-aut">
                    <a class="nav-link pull-right" href="/logout">Logout</a>
                </li>
            </ul>
        </div>

    </nav>
@endif

<div class="container-fluid">

    <div class="modal fade alert-modal-interactive" tabindex="-1" role="alertdialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title alert-heading"></h5>
                </div>
                <div class="container-fluid modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="alert-modal-body"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6 text-center">
                                <input type="button" class="btn btn-secondary modal-cancel" value="Cancel">
                            </div>
                            <div class="col-sm-6 text-center">
                                <input type="button" class="btn btn-danger modal-confirm" value="Confirm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade success-modal-interactive" tabindex="-1" role="alertdialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title success-heading"></h5>
                </div>
                <div class="container-fluid modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="success-modal-body"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <input type="button" class="btn btn-secondary modal-close" value="Close">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade error-modal-interactive" tabindex="-1" role="alertdialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title error-heading"></h5>
                </div>
                <div class="container-fluid modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="error-modal-body"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <input type="button" class="btn btn-secondary modal-close" value="Close">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('content')
</div>

<div class="navbar-fixed-bottom text-center">
    <div class="row">
        <div class="col-sm-12">
            <ul class="list-inline">
                <li class="list-inline-item"><a style="color: white; text-decoration: none;" href="https://isaless.com/#services">SERVICE OFFERINGS | </a></li>
                <li class="list-inline-item"><a style="color: white; text-decoration: none;" href="https://isaless.com/#testimonials">CLIENTS | </a></li>
                <li class="list-inline-item"><a style="color: white; text-decoration: none;" href="https://isaless.com/#aboutus">ABOUT US | </a></li>
                <li class="list-inline-item"><a style="color: white; text-decoration: none;" href="https://isaless.com/#contact">CONTACT US</a><br></li>
            </ul>

        </div>
    </div>
</div>
</body>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
</html>
