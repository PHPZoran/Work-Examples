<div id="ChartCalls" style="width: auto; height: 300px; background: url(img/white-bg.png);"></div>

<hr>
<div class="row text-left">
    <div class="col-sm-6">
        <label for="total_activity" class="col-sm-8 control-label">Total Activity</label>
        <div class="col-sm-4">
            <span id="total_activity"></span>
        </div>
    </div>
    <div class="col-sm-6">
        <label for="monthly_average" class="col-sm-8 control-label">Monthly Average</label>
        <div class="col-sm-4">
            <span id="monthly_average"></span>
        </div>
    </div>
</div>
<hr>
<div class="row text-left">
    <div class="col-sm-6">
        <label for="current_month" class="col-sm-8 control-label">Current Month</label>
        <div class="col-sm-4">
            <span id="current_month"></span>
        </div>
    </div>
    <div class="col-sm-6">
        <label for="previous_month" class="col-sm-8 control-label">Previous Month</label>
        <div class="col-sm-4">
            <span id="previous_month"></span>
        </div>
    </div>
</div>
<hr>


<div id="et_activity_details" class="modal fade modal-lg" tabindex="-1" role="dialog" aria-labelledby="etModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">

        <div class="modal-content" style="padding-bottom: 20px">

            <div class="modal-header">
                <h4 class="modal-title" id="etModalLabel">Activity Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <hr>

            {{--loader--}}
            <div id="et_activity_details_loader" class="container-fluid">

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
            <div id="et_activity_details_content" class="container-fluid">

                <div class="row">

                    <div class="col-sm-3">
                        Select Activity: <select id="et_activities_select" class="custom-select"></select>
                    </div>

                </div>

                <div class="row" style="margin-top: 20px;">

                    <div id="et_activity_details_data" class="col-sm-12">

                        <table class="table table-striped text-left table-responsive-lg" id="et_activity_details_table"></table>

                    </div>

                </div>


            </div>
        </div>
    </div>
</div>