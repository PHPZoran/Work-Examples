<div class="row">
    <div class="col-sm-3">
        <label for="date_created_filter">Date Created</label>
        <select id="date_created_filter" class="form-control" name="date_created_filter">
            <option value=""></option>
			<option value="today">Today</option>
			<option value="yesterday">Yesterday</option>			
            <option value="last_7_days">Last 7 days</option>
            <option value="last_30_days">Last 30 days</option>
            <option value="this_month">This Month</option>
            <option value="last_month">Last Month</option>
        </select>
        <div style="margin-top:10px;background-color:#F2D1D1;">Appointments Created within last 7 days</div>
    </div>
    <div class="col-sm-3">
        <label for="appointment_date_filter">Appointment Date</label>
        <select id="appointment_date_filter" class="form-control" name="appointment_date_filter">
            <option value=""></option>
			<option value="today">Today</option>
			<option value="tomorrow">Tomorrow</option>	
			<option value="yesterday">Yesterday</option>			
            <option value="last_7_days">Last 7 days</option>
            <option value="last_30_days">Last 30 days</option>
            <option value="this_month">This Month</option>
            <option value="last_month">Last Month</option>
            <option value="future">Future Appointments</option>
        </select>
    </div>
    <div class="col-sm-3 offset-sm-3">
        <div class="row">
            <div class="col-sm-12">
                <label for="total_appointments">Total Appointments:</label>
                <span id="appointments_total_appointments" name="total_appointments"></span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <label for="attended_appointments">Attended Appointments:</label>
                <span id="appointments_total_attended" name="attended_appointments"></span>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-9">
                <input type="button" class="btn btn-link pull-right ms-export-excel" value="Export Excel">
            </div>
        </div>
    </div>
</div>
<br>
<hr>
<div class="row">
    <div class="col-sm-12">
        <table class="table table-striped text-left table-responsive-lg" id="appointment_list_view"></table>
    </div>
</div>
<hr>

<div id="ms_universal_modal" class="modal fade error-modal-interactive" tabindex="-1" role="alertdialog"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: aliceblue;">
            <div class="modal-header">
                <h5 class="modal-title ms-title"></h5>
            </div>
            <div class="container-fluid modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <p class="ms-body"></p>

                        <div class="ball-beat ms-loader" style="text-align: center;">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer ms-footer">
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
