<div class="modal fade modal-lg app-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myLargeModalLabel">Detail View
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-left: 15px;">
                        Close
                    </button>
                </h4>

            </div>
            <hr>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
			<h5 style="text-align:center;color:blue;">Appointment Information</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4 font-weight-bold">Contact Name:</div>
                                    <div class="col-sm-8">
                                        <span id="con_first"></span>
                                        <span id="con_last"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 font-weight-bold">Account Name:</div>
                                    <div class="col-sm-8">
                                        <span id="acc_name"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-striped text-left table-responsive-lg">
                                            <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Format</th>
                                                <th>Date</th>
                                                <th>Campaign Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><span id="appointment_status"></span></td>
                                                <td><span id="appointment_place"></span></td>
                                                <td><span id="appointment_date"></span></td>
                                                <td><span id="campaign_name"></span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-striped text-left table-responsive-lg">
                                            <tbody>
                                            <tr>
                                                <td class="font-weight-bold">Title</td>
                                                <td><span id="title"></span></td>
                                                <td class="font-weight-bold">Street</td>
                                                <td><span id="primary_address_street"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Direct Phone</td>
                                                <td><span id="title"></span></td>
                                                <td class="font-weight-bold">City</td>
                                                <td><span id="primary_address_city"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Office Phone</td>
                                                <td><span id="phone_work"></span></td>
                                                <td class="font-weight-bold">State</td>
                                                <td><span id="primary_address_state"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Email Address</td>
                                                <td><span id="contact_email"></span></td>
                                                <td class="font-weight-bold">Postal Code</td>
                                                <td><span id="primary_address_postalcode"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">SDR</td>
                                                <td><span id="ISE"></span></td>
                                                <td class="font-weight-bold">Country</td>
                                                <td><span id="primary_address_country"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Campaign Results Admin</td>
                                                <td><span id="Acc_Man"></span></td>
                                                <td class="font-weight-bold">Date Created</td>
                                                <td><span id="date_entered"></span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                    </div>

                    <div class="col-sm-6" style="border-right: 1px solid lightgrey">
                        <div class="row">
                            <div class="col-sm-7">
				<h5 style="text-align:center;color:blue;">ISS Appointment Notes</h5>
                                <div class="row">

                                    <div class="col-sm-12 font-weight-bold">
                                        Notes
                                    </div>

                                </div>

                                <hr>

                                <div class="row">

                                    <div class="col-sm-12">

                                        <div class="card">
                                            <div class="card-body">
                                                <span id="description" class="card-text"></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="col-sm-5">
 		                   <h5 style="text-align:center;color:blue;">Post Meeting Rep Feedback</h5>

                                <div class="row">
                                    <div class="col-sm-6 font-weight-bold">
                                        Sales Rep
                                    </div>
                                    <div class="col-sm-6">
                                        <span id="rep_first"></span>
                                        <span id="rep_last"></span>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-sm-6 font-weight-bold">
                                        Decision Maker Present
                                    </div>
                                    <div class="col-sm-6">
                                        <select id="dm_qualified_c"
                                                class="form-control ms-editable"
                                                disabled></select>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-sm-6 font-weight-bold">
                                        Positive Appointment
                                    </div>
                                    <div class="col-sm-6">
                                        <select id="positive_appointment_c"
                                                class="form-control ms-editable"
                                                disabled></select>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-sm-6 font-weight-bold">
                                        Second Appointment
                                    </div>

                                    <div class="col-sm-6">
                                        <select id="second_appointment_c"
                                                class="form-control ms-editable"
                                                disabled></select>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-sm-6 font-weight-bold">
                                        Opportunity Timeline
                                    </div>
                                    <div class="col-sm-6">
                                        <select id="appointment_result_c" class="form-control ms-editable" disabled>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-sm-6 font-weight-bold">
                                        Opportunity Amount
                                    </div>
                                    <div class="col-sm-6">
                                        <select id="opportunity_amount" class="form-control ms-editable" disabled>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <div class="row" style="margin-top: 30px;">

                                    <div class="col-sm-12 font-weight-bold">
                                        Appointment Feedback
                                    </div>

                                </div>

                                <hr>

                                <div class="row">

                                    <div class="col-sm-12">
                                        <span id="appointment_feedback"></span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-sm-12" style="margin: 15px 0 15px 0">

                                <button id="ms_save_appointment" type="button" class="btn btn-primary pull-right"
                                        style="margin-left: 10px;">Save
                                </button>

                                <button type="button" class="btn btn-secondary pull-right" data-dismiss="modal"
                                        style="margin-left: 10px;">Close
                                </button>

                                <button id="ms_edit_appointment" type="button" class="btn btn-primary pull-right"
                                        style="margin-left: 10px;">Edit
                                </button>

                                <button hidden id="ms_cancel_saving_appointment" type="button" class="btn btn-light pull-right"
                                        style="margin-left: 10px;">Cancel
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
