<div class="col-sm-6">
    <div id="ChartSecondPie" style="width: auto; height: 300px; background: url(img/white-bg.png);"></div>

    <hr>

    <div class="row">
        <table class="table table-striped text-left">
            <tbody>
            <tr>
                <td>Total Held</td>
                <td><span id="total_held_second"></span></td>
            </tr>
            <tr>
                <td>Current Month</td>
                <td><span id="current_month_second"></span></td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
<div class="col-sm-6">
    <div id="ChartSecondBar" style="width: auto; height: 300px; background: url(img/white-bg.png);"></div>

    <hr>

    <div class="row">
        <table class="table table-striped text-left">
            <tbody>
            <tr>
                <td>Monthly Average</td>
                <td><span id="monthly_average_second"></span></td>
            </tr>
            <tr>
                <td>Previous Month</td>
                <td><span id="previous_month_second"></span></td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
<hr>

<div id="et_second_appointment_details" class="modal fade modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="etModalLabel2"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content" style="padding-bottom: 20px">

            <div class="modal-header">
                <h4 class="modal-title" id="etModalLabel2">Second Appointment Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <hr>

            {{--loader--}}
            <div id="et_second_appointment_loader" class="container-fluid">

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
            <div id="et_second_appointment_content" class="container-fluid">

                <div class="row">

                    <div class="col-sm-3">
                        Select Activity: <select id="et_second_appointment_select" class="custom-select"></select>
                    </div>

                </div>

                <div class="row" style="margin-top: 20px;">

                    <div class="col-sm-12">

                        <table class="table table-striped text-left table-responsive-lg"
                               id="et_second_appointment_details_table"></table>

                    </div>

                </div>


            </div>
        </div>
    </div>
</div>
