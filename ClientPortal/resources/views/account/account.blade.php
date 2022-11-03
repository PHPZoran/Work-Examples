@extends('app')

    @section('content')
        @include('account.filter')

        <div class="card-group">
            <div class="card">

                <div class="card-header">
                    Accounts:
                </div>


                <div id="collapse1" class="card-collapse">
                    <div class="card-body">
                        <div class="container-fluid text-center">
                            <div id="accountsLoader" class="ball-beat" style="margin: 100px 0; display: none">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                            <div id="accountsSection" class="row">
                                <div class="col-sm-12">
                                    @include('account.target_list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection