@extends('app')

@section('content')
    @include('report.filter')

    <div class="card-group">
        <div class="card">
            <div class="card-body">

                <ul class="nav nav-tabs">
                    <li class="nav-item active">
                        <a class="nav-link active" href="#activity" data-toggle="tab">Activity</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#appointments" data-toggle="tab">Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#campaign" data-toggle="tab">Campaign Results</a>
                    </li>
                </ul>

            </div>
            <div id="collapse1" class="">

                <div class="">

                    <div class="tab-content clearfix">
                        <div class="tab-pane active" id="activity">
                            <div class="row">
                                <div class="col-sm-8 text-center">
                                    <div class="col-sm-12 text-center section-header">
                                        Prospecting Activity
                                    </div>
                                    <hr>
                                    <div class="col-sm-12 text-center">

                                        <div id="prospectingLoader" class="ball-beat" style="margin: 100px 0;">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>

                                        <div id="prospectingSection" class="row" style="display: none">
                                            <div class="col-sm-12 text-left">
                                                @include('report.activity')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-center">

                                    <div class="">

                                        <div class="col-sm-12 text-center section-header">
                                            Appointment Snapshot
                                        </div>
                                        <hr>
                                        <div class="col-sm-12">

                                            <div id="snapshotLoader" class="ball-beat" style="margin: 100px 0;">
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>

                                            <div id="snapshotSection" class="row" style="display: none">
                                                <div class="col-sm-12 text-left">
                                                    @include('report.snapshot')
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div  class="">
                                        <div class="col-sm-12 text-center section-header">
                                            Report Delivery
                                        </div>
                                        <hr>
                                        <div class="col-sm-12">

                                            <div id="reportDeliveryLoader" class="ball-beat" style="margin: 70px 0;">
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>

                                            <div id="reportDeliverySection" class="row" style="display: none">

                                                <div class="col-sm-12 text-left">
                                                    @include('report.delivery')
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12  text-center">
                                    <div class="col-sm-12 text-center section-header">
                                        Total Appointments
                                    </div>
                                    <hr>
                                    <div class="col-sm-12">

                                        <div id="totalActivityLoader" class="ball-beat" style="margin: 100px 0;">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>

                                        <div id="totalActivitySection" class="row" style="display: none">
                                            <div class="col-sm-12 text-left">
                                                @include('report.total_activity')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="appointments">
                            <div class="row">
                                <div class="col-sm-12 text-center">

                                    <div id="appointmentsLoader" class="ball-beat" style="margin: 100px 0;">
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                    </div>

                                    <div id="appointmentsSection" class="row" style="display: none">
                                        <div class="col-sm-12 text-left">
                                            @include('report.appointments')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="campaign">
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="col-sm-12 text-center section-header">
                                        Cumulative Opportunity Value
                                    </div>
                                    <hr>
                                    <div class="col-sm-12 text-center">

                                        <div id="opportunityLoader" class="ball-beat" style="margin: 100px 0;">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div id="opportunitySection" class="row" style="display: none;">
                                            @include('report.campaign')
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="col-sm-12 text-center section-header">
                                        Positive Appointment
                                    </div>
                                    <hr>
                                    <div class="col-sm-12 text-center">

                                        <div id="apointmentLoader" class="ball-beat" style="margin: 100px 0;">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div id="apointmentSection" class="row" style="display: none;">
                                            @include('report.positive_apointments')
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="col-sm-12 text-center section-header">
                                        Second Appointment Scheduled
                                    </div>
                                    <hr>
                                    <div class="col-sm-12 text-center">

                                        <div id="secondApointmentLoader" class="ball-beat" style="margin: 100px 0;">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div id="secondApointmentSection" class="row" style="display: none;">
                                            @include('report.second_apointment')
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="col-sm-12 text-center section-header">
                                        Opportunity Timelines
                                    </div>
                                    <hr>
                                    <div class="col-sm-12 text-center">

                                        <div id="apointmentHeldLoader" class="ball-beat" style="margin: 100px 0;">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div id="apointmentHeldSection" class="row" style="display: none;">
                                            @include('report.appointments_held')
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="col-sm-12 text-center section-header">
                                        Opportunity Values
                                    </div>
                                    <hr>
                                    <div class="col-sm-12 text-center">

                                        <div id="apointmentMaxValueLoader" class="ball-beat" style="margin: 100px 0;">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div id="apointmentMaxValueSection" class="row" style="display: none;">
                                            @include('report.appointments_max_value')
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('report.appointment_details')

@endsection
