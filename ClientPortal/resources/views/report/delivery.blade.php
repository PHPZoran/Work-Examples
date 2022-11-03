<div class="row" style="padding-top: 10px">

    <div class="col-sm-6">
        <label for="et_email_address" class="control-label pull-right">Send to email address:</label>
    </div>
    <div class="col-sm-6">
        <input title="Separate multiple email addresses with a semicolon ( ; )" id="et_email_address" class="ms-delivery-configuration" type="text" style="width: 100%">
    </div>

</div>

<div class="row">

    <div class="col-sm-6">
        <label for="et_email_address_cc" class="control-label pull-right">CC:</label>
    </div>
    <div class="col-sm-6">
        <input  title="Separate multiple email addresses with a semicolon ( ; )"  id="et_email_address_cc" class="ms-delivery-configuration" type="text" style="width: 100%">
    </div>

</div>

<div class="row" style="padding-top: 10px">

    <br>

    <div class="col-sm-12">

        Send report every
        <select id="et_send_at_day" class="custom-select ms-delivery-configuration">
	    <option value="" selected disabled></option>
            <option value="1">Monday</option>
            <option value="2">Tuesday</option>
            <option value="3">Wednesday</option>
            <option value="4">Thursday</option>
            <option value="5" >Friday</option>
            <option value="6">Saturday</option>
            <option value="7">Sunday</option>
        </select>
        at
        <select id="et_send_at_time" class="custom-select ms-delivery-configuration">
	    <option value="" selected disabled></option>
            <option value="01:00">01:00</option>
            <option value="01:15">01:15</option>
            <option value="01:30">01:30</option>
            <option value="01:45">01:45</option>
            <option value="02:00">02:00</option>
            <option value="02:15">02:15</option>
            <option value="02:30">02:30</option>
            <option value="02:45">02:45</option>
            <option value="03:00">03:00</option>
            <option value="03:15">03:15</option>
            <option value="03:30">03:30</option>
            <option value="03:45">03:45</option>
            <option value="04:00">04:00</option>
            <option value="04:15">04:15</option>
            <option value="04:30">04:30</option>
            <option value="04:45">04:45</option>
            <option value="05:00">05:00</option>
            <option value="05:15">05:15</option>
            <option value="05:30">05:30</option>
            <option value="05:45">05:45</option>
            <option value="06:00">06:00</option>
            <option value="06:15">06:15</option>
            <option value="06:30">06:30</option>
            <option value="06:45">06:45</option>
            <option value="07:00">07:00</option>
            <option value="07:15">07:15</option>
            <option value="07:30">07:30</option>
            <option value="07:45">07:45</option>
            <option value="08:00">08:00</option>
            <option value="08:15">08:15</option>
            <option value="08:30">08:30</option>
            <option value="08:45">08:45</option>
            <option value="09:00">09:00</option>
            <option value="09:15">09:15</option>
            <option value="09:30">09:30</option>
            <option value="09:45">09:45</option>
            <option value="10:00">10:00</option>
            <option value="10:15">10:15</option>
            <option value="10:30">10:30</option>
            <option value="10:45">10:45</option>
            <option value="11:00">11:00</option>
            <option value="11:15">11:15</option>
            <option value="11:30">11:30</option>
            <option value="11:45">11:45</option>
            <option value="12:00">12:00</option>
            <option value="12:15">12:15</option>
            <option value="12:30">12:30</option>
            <option value="12:45">12:45</option>
        </select>

        <select id="et_send_at_period" class="custom-select ms-delivery-configuration">
            <option value="" disabled selected></option>
	    <option value="AM">AM</option>
            <option value="PM">PM</option>
        </select>
	<label>EST</label>
    </div>

</div>

<div class="row" style="padding-top: 10px">

    <div class="col-sm-12">

        <input id="send_snapshot_report" class="btn btn-primary" type="button" value="Send Report">

        <input id="cancel_save_configuration" class="btn btn-light" type="button" value="Cancel"
               style="display: none">

        <input id="save_delivery_configuration" class="btn btn-primary" type="button" value="Save Configuration"
               style="display: none">

    </div>

</div>

<div id="reportDeliveryModal" class="modal fade error-modal-interactive" tabindex="-1" role="alertdialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
