<div id="ChartAppSet" style="width: 100%; height: 300px; background: url(img/white-bg.png);"></div>

<hr>
<div class="row">
    <div class="col-sm-3">
        <label for="totalAppointments">Total Appointments</label>
    </div>
    <div class="col-sm-1">
        <span id="totalAppointments"></span>
    </div>
    <div class="col-sm-3">
        <label for="totalAccepted">Total Upcoming</label>
    </div>
    <div class="col-sm-1">
        <span id="totalAccepted"></span>
    </div>
    <div class="col-sm-3">
        <label for="totalReschedules">Total Reschedules</label>
    </div>
    <div class="col-sm-1">
        <span id="totalReschedules"></span>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-sm-3">
        <label for="totalAttended">Total Attended</label>
    </div>
    <div class="col-sm-1">
        <span id="totalAttended"></span>
    </div>
    <div class="col-sm-3">
        <label for="totalCanceled">Total Cancelled</label>
    </div>
    <div class="col-sm-1">
        <span id="totalCanceled"></span>
    </div>
    <div class="col-sm-3">
        <label for="totalConfirmed">Total Confirmed</label>
    </div>
    <div class="col-sm-1">
        <span id="totalConfirmed"></span>
    </div>
</div>
<hr>

<div id="et_total_appointments_details" class="modal fade modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="etModalLabel7"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content" style="padding-bottom: 20px">

            <div class="modal-header">
                <h4 class="modal-title" id="etModalLabel7">Total Appointments Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <hr>

            {{--loader--}}
            <div id="et_total_appointments_loader" class="container-fluid">

                <div class="row">

                    <div class="col-sm-12" style="text-align: center">

                        <div class="ball-beat">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>

                    </div>

                </div>

            </div>

            {{--content--}}
            <div id="et_total_appointments_content" class="container-fluid">

                <div class="row">

                    <div class="col-sm-3">
                        Select Appointment Status: <select id="et_total_appointments_select" class="custom-select">
                            <option value="Upcoming">Upcoming</option>
                            <option value="Reschedule">Reschedule</option>
                            <option value="Attended">Attended</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Confirmed">Confirmed</option>
                        </select>
                    </div>

                </div>

                <div class="row" style="margin-top: 20px;">

                    <div class="col-sm-12">

                        <table class="table table-striped text-left table-responsive-lg"
                               id="et_total_appointments_details_table"></table>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>