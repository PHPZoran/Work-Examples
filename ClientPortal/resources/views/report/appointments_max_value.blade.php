<div class="col-sm-6">
    <div id="appointmentsMaxValuePie" style="width: auto; height: 300px; background: url(img/white-bg.png);"></div>

    <hr>

    <div class="row">
        <table class="table table-striped text-left">
            <tbody>
            <tr>
                <td>Deal Size</td>
                <td>Appointments Held</td>
            </tr>
            <tr>
                <td>$0-35K</td>
                <td><span id="0_35k"></span></td>
            </tr>
            <tr>
                <td>$35-75K</td>
                <td><span id="35_75k"></span></td>
            </tr>
            <tr>
                <td>$75-150K</td>
                <td><span id="75_150k"></span></td>
            </tr>
            <tr>
                <td>$150K - 400K</td>
                <td><span id="150_400k"></span></td>
            </tr>
            <tr>
                <td>$400K - 1 M</td>
                <td><span id="400_1m"></span></td>
            </tr>
            <tr>
                <td>$1M +</td>
                <td><span id="1m_plus"></span></td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
<div class="col-sm-6">
    <div id="appointmentsMaxValueBar" style="width: auto; height: 300px; background: url(img/white-bg.png);"></div>

    <hr>

    <div class="row">
        <table class="table table-striped text-left">
            <tbody>
            <tr>
                <td>Deal Size</td>
                <td>Appointments Held</td>
            </tr>
            <tr>
                <td>$0-35K</td>
                <td><span id="0_35k_bar"></span></td>
            </tr>
            <tr>
                <td>$35-75K</td>
                <td><span id="35_75k_bar"></span></td>
            </tr>
            <tr>
                <td>$75-150K</td>
                <td><span id="75_150k_bar"></span></td>
            </tr>
            <tr>
                <td>$150K - 400K</td>
                <td><span id="150_400k_bar"></span></td>
            </tr>
            <tr>
                <td>$400K - 1 M</td>
                <td><span id="400_1m_bar"></span></td>
            </tr>
            <tr>
                <td>$1M +</td>
                <td><span id="1m_plus_bar"></span></td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
<hr>

<div id="et_max_value_appointments_details" class="modal fade modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="etModalLabel4"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content" style="padding-bottom: 20px">

            <div class="modal-header">
                <h4 class="modal-title" id="etModalLabel4">Appointments Max Value Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <hr>

            {{--loader--}}
            <div id="et_max_value_appointments_loader" class="container-fluid">

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
            <div id="et_max_value_appointments_content" class="container-fluid">

                <div class="row">

                    <div class="col-sm-3">
                        Select Range: <select id="et_max_value_appointments_select" class="custom-select"></select>
                    </div>

                </div>

                <div class="row" style="margin-top: 20px;">

                    <div class="col-sm-12">

                        <table class="table table-striped text-left table-responsive-lg"
                               id="et_max_value_appointments_details_table"></table>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>