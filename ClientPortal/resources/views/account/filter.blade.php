<div class="card-group">
    <div class="card">

        <div id="collapse1" class="card-collapse">

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <span class="pull-left">Campaigns:</span>
                    </div>
                    <div class="col-sm-3">
                        <span class="pull-left">Your Campaign Results Director:</span>
                    </div>
                    <div class="col-sm-5">
                        <span class="pull-left">Actions:</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <select id="acc_campaigns" class="form-control" data-from="campaigns">
                            <option value="" selected>Please Choose a Campaign</option>
                            @foreach($data['campaigns'] as $campaign)
                                <option value="{{$campaign->campid}}">{{$campaign->campname}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div id="contact_info_container" style="display: none">
                            <input class="contact_id" type="text" style="display: none">
                            <input class="client_id" type="text" style="display: none">
                            <div class="row">
                                <div class="col-sm-12">
                                    <span id="first_name"></span>
                                    <span id="last_name"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="">Telephone:</label>
                                    <span id="phone_work"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="">E-mail:</label>
                                    <span id="email_address"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="row" id="actions_list_container" style="display: none">
                            <div class="col-sm-12">
                                <label for="target_lists">List Name:
                                    <select name="target_lists" id="target_lists" class="form-control"></select>
                                </label>
                            </div>
                        </div>
                        <div id="list_create" class="form-group" width="100%" style="display:none;">

                            <div class="row" id="div-newlistname" style="width:400px;padding-bottom:20px;">
                                <div class="col-sm-10">
                                    <input type="text" id="newlistname" class="form-control">
                                </div>
                                <div style="display:inline-block" id="div-newlisthelp" class="col-sm-2">
                                    <button type="button" class="btn btn-link" data-placement="bottom"
                                            data-toggle="popover" title="Help" data-trigger="hover" data-html="true"
                                            data-content="Name and Save List, then Select Companies to add.">&#9432;
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input id="create_new_list" class="btn btn-info" type="button"
                                           value="Save This List">

                                    <div id="create_new_list_loader" class="ball-beat ball-beat-small"
                                         style="display: none; margin: 10px 0 0 25px">
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                    </div>

                                </div>
                                <div class="col-sm-6" style="float:right; float:right;clear:right;align:right;">
                                    <input id="cancel_new_list" class="btn btn-info" type="button" value="Cancel">
                                </div>
                            </div>
                        </div>
                        <div id="action_buttons_container" class="row" style="display: none;">
                            <div class="col-sm-3">
                                <input type="button" id="create_list" class="btn btn-info" value="Create a List">
                            </div>
                            <div class="col-sm-5">
                                <input type="button" id="editList" class="btn btn-info" style="display: none;"
                                       value="Review / Remove Accounts">
                            </div>
                            <div class="col-sm-4">
                                <input type="button" id="edit_list" class="btn btn-info" value="Review and Edit Lists">
                                <input type="button" id="show_filter" class="btn btn-info" style="display: none;"
                                       value="Add Accounts">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>