<div class="col-sm-6">
    <div id="ChartHeldPie" style="width: auto; height: 300px; background: url(img/white-bg.png);"></div>

    <hr>

    <div class="row">
        <table class="table table-striped text-left">
            <tbody>
            <tr>
                <td>Total Held</td>
                <td><span id="total_held_held"></span></td>
            </tr>
            <tr>
                <td>Current Month</td>
                <td><span id="current_month_held"></span></td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
<div class="col-sm-6">
    <div id="ChartAppHeld" style="width: auto; height: 300px; background: url(img/white-bg.png);"></div>

    <hr>

    <div class="row">
        <table class="table table-striped text-left">
            <tbody>
            <tr>
                <td>Monthly Average</td>
                <td><span id="monthly_average_held"></span></td>
            </tr>
            <tr>
                <td>Previous Month</td>
                <td><span id="previous_month_held"></span></td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
<hr>

<div id="et_held_appointments_details" class="modal fade modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="etModalLabel3"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content" style="padding-bottom: 20px">

            <div class="modal-header">
                <h4 class="modal-title" id="etModalLabel3">Held Appointments Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <hr>

            {{--loader--}}
            <div id="et_held_appointments_loader" class="container-fluid">

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
            <div id="et_held_appointments_content" class="container-fluid">

                <div class="row">

                    <div class="col-sm-3">
                        Select Range: <select id="et_held_appointments_select" class="custom-select"></select>
                    </div>

                </div>

                <div class="row" style="margin-top: 20px;">

                    <div class="col-sm-12">

                        <table class="table table-striped text-left table-responsive-lg"
                               id="et_held_appointments_details_table"></table>

                    </div>

                </div>


            </div>
        </div>
    </div>
</div>
