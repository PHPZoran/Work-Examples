<div class="card-group">
    <div class="card">

        <div class="card-header">
            <div class="pull-left">
                <a class="nav-link" href="" id="ms_toggle_filters">Hide filters</a>
            </div>

            <div class="pull-right">
                <select id="campaign_type" class="custom-select">
                    <option value="all" selected="">All Campaigns</option>
                    <option value="active">Active Campaigns</option>
                    <option value="completed">Completed Campaigns</option>
                    <option value="event">Event Campaigns</option>
                </select>
            </div>
        </div>


        <div id="ms_filters_area" class="card-collapse">

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <span class="pull-left">Campaigns</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="pull-left">Sales Reps</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <select id="campaigns" class="form-control" multiple="multiple" data-from="campaigns">

                            @foreach($data['campaigns'] as $campaign)
                                <option value="{{$campaign->id}}" selected>{{$campaign->name}}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-sm-6">
                        <select id="salesreps" class="form-control" multiple="multiple" data-from="salesreps">
                            @foreach($data['salesreps'] as $salesrep)
                                <option value="{{$salesrep->id}}" selected>{{$salesrep->last_name}}
                                    , {{$salesrep->first_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>