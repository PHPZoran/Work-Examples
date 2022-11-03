<div class="list-data" style="display: none">
    <div class="row">
        <div class="col-sm-6">
            <input type="button" class="btn btn-danger remove-accounts" value="Remove Accounts from List">
            <input type="button" class="btn btn-info lock-list" value="Save Final List">
            <input type="button" class="btn btn-info delete-list" value="Delete List">
            <input id="save-to-list" type="button" class="btn btn-group btn-info save-to-list" style="display: none" value="Save To List">
            <input id="submit-final-list" type="button" class="btn btn-group btn-info save-to-list" style="display: none" value="Save Accounts and Submit final List">
            <input id="show-filter" type="button" class="btn btn-group btn-info save-to-list" style="display: none" value="Show Filter">
        </div>
        <div class="col-sm-3">
            <label for="num-companies">Number of Companies: <div class="num-companies"></div></label>
        </div>
        <div class="col-sm-3">
            <label for="num-companies">Number of Selections: <div class="num-selections"></div></label>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12">
            <table id="target_list_dt" class="table table-striped table-responsive"></table>
        </div>
    </div>
</div>

<div id="filterset" class="text-left" style="display: none">

        <div class="row">
            <div class="col-sm-12">
                <strong><h4>Choose 1 or more Filters and Save Selections to your List.  Use tooltips for directions, or message your Campaign Director.</h4></strong>
            </div>
        </div>
        <hr>
        <div style="display:none;">
            <input id="sortcolumn" type="text">
            <input id="sortdirection" type="text">
        </div>
        <div class="row">
            <div class="col-sm-12">
                <input id="clear_filter" class="btn btn-info pull-right" type="button" value="Clear Filters">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <label><h5>Company Size</h5></label>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <label for="employees_max" class="col-sm-12">Employees Max:</label>
                    <div class=" col-sm-6">
                        <input id="employees_max"  class="form-control" type="number">
                    </div>
                </div>
                <div class="row">
                    <label for="employees_min" class="col-sm-12">Employees Min</label>
                    <div class=" col-sm-6">
                        <input id="employees_min" class="form-control" type="number">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <label for="revenue_max" class="col-sm-12">Revenue Max:</label>
                    <div class=" col-sm-4">
                        <input id="revenue_max" class="form-control" type="number">
                    </div>
                    <div class=" col-sm-4">
                        <select id="revenue_max_select" class="form-control">
                            <option value="1000000">Million</option>
                            <option value="1000000000">Billion</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <label for="revenue_min" class="col-sm-12" type="number">Revenue Min:</label>
                    <div class=" col-sm-4">
                        <input id="revenue_min"  class="form-control" type="number">
                    </div>
                    <div class=" col-sm-4">
                        <select id="revenue_min_select"  class="form-control">
                            <option value="1000000">Million</option>
                            <option value="1000000000">Billion</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-sm-12" style="display:inline;">
                <strong><h5 style="display:inline;">Company Location</h5><h6 style="display:inline;">  (HQ Addresses)</h6></strong>
            </div>
        </div>

        <div class="row">

            <div class="col-sm-6">
                <label for="state" class="col-sm-12">States</label>
                <div class=" col-sm-6">
                    <select name="state" multiple="multiple" id="state" style="display: none;" class="form-control">
                        <option value="AL">AL</option>
                        <option value="AK">AK</option>
                        <option value="AZ">AZ</option>
                        <option value="AR">AR</option>
                        <option id="CA" value="CA">CA</option>
                        <option id="CAN" value="CA (North)">CA (North)</option>
                        <option id="CAS" value="CA (South)">CA (South)</option>
                        <option value="CO">CO</option>
                        <option value="CT">CT</option>
                        <option value="DE">DE</option>
                        <option value="DC">DC</option>
                        <option value="FL">FL</option>
                        <option value="GA">GA</option>
                        <option value="HI">HI</option>
                        <option value="ID">ID</option>
                        <option value="IL">IL</option>
                        <option value="IN">IN</option>
                        <option value="IA">IA</option>
                        <option value="KS">KS</option>
                        <option value="KY">KY</option>
                        <option value="LA">LA</option>
                        <option value="ME">ME</option>
                        <option value="MD">MD</option>
                        <option value="MA">MA</option>
                        <option value="MI">MI</option>
                        <option value="MN">MN</option>
                        <option value="MS">MS</option>
                        <option value="MO">MO</option>
                        <option value="MT">MT</option>
                        <option value="NE">NE</option>
                        <option value="NV">NV</option>
                        <option value="NH">NH</option>
                        <option value="NJ">NJ</option>
                        <option value="NM">NM</option>
                        <option value="NY">NY</option>
                        <option value="NC">NC</option>
                        <option value="ND">ND</option>
                        <option value="OH">OH</option>
                        <option value="OK">OK</option>
                        <option value="OR">OR</option>
                        <option value="PA">PA</option>
                        <option value="RI">RI</option>
                        <option value="SC">SC</option>
                        <option value="SD">SD</option>
                        <option value="TN">TN</option>
                        <option value="TX">TX</option>
                        <option value="UT">UT</option>
                        <option value="VT">VT</option>
                        <option value="VA">VA</option>
                        <option value="WA">WA</option>
                        <option value="WV">WV</option>
                        <option value="WI">WI</option>
                        <option value="WY">WY</option>
                        <option value="AB">AB</option>
                        <option value="BC">BC</option>
                        <option value="MB">MB</option>
                        <option value="NB">NB</option>
                        <option value="NL">NL</option>
                        <option value="NS">NS</option>
                        <option value="NT">NT</option>
                        <option value="NU">NU</option>
                        <option value="ON">ON</option>
                        <option value="PE">PE</option>
                        <option value="QC">QC</option>
                        <option value="SK">SK</option>
                        <option value="YT">YT</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label for="zipcode" class="col-sm-6">Zip Code</label>
                <div class=" col-sm-6">
                    <input id="zipcode" class="form-control" type="text">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <label for="city" class="col-sm-6">City</label>
                <div class=" col-sm-6">
                    <input id="city" class="form-control" type="text">
                </div>
            </div>
            <div class="col-sm-6">
                <label for="area_code" class="col-sm-6">Area Code</label>
                <div class=" col-sm-6">
                    <input id="area_code" class="form-control" type="text">
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12" style="display:inline;">
                <h5 style="display:inline;">Industries</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <label for="industry" class="col-sm-6">Industry</label>
                <div class=" col-sm-6">
                    <select name="industry" id="industry" multiple="multiple" title="" style="display: none;" class="form-control">
                        <option label="Aerospace and Defense" value="aerospace and defense">Aerospace and Defense</option>
                        <option label="Agriculture and Forestry" value="agriculture and forestry">Agriculture and Forestry</option>
                        <option label="Automotive" value="automotive">Automotive</option>
                        <option label="Banks" value="banks">Banks</option>
                        <option label="Chemicals" value="chemicals">Chemicals</option>
                        <option label="Civic Non Profit and Membership Groups" value="civic non profit and membership groups">Civic Non Profit and Membership Groups</option>
                        <option label="Computer Hardware" value="computer hardware">Computer Hardware</option>
                        <option label="Computer Software" value="computer software">Computer Software</option>
                        <option label="Construction and Building Materials" value="construction and building materials">Construction and Building Materials</option>
                        <option label="Consumer Product Manufacturing" value="consumer product manufacturing">Consumer Product Manufacturing</option>
                        <option label="Consumer Services" value="consumer services">Consumer Services</option>
                        <option label="Corporate Services" value="corporate services">Corporate Services</option>
                        <option label="Electronics" value="electronics">Electronics</option>
                        <option label="Energy and Environmental" value="energy and environmental">Energy and Environmental</option>
                        <option label="Financial Services" value="financial services">Financial Services</option>
                        <option label="Food and Beverage" value="food and beverage">Food and Beverage</option>
                        <option label="Government" value="government">Government</option>
                        <option label="Holding Companies" value="holding companies">Holding Companies</option>
                        <option label="Hospitals and Healthcare" value="hospitals and healthcare">Hospitals and Healthcare</option>
                        <option label="Industrial Manufacturing and Services" value="industrial manufacturing and services">Industrial Manufacturing and Services</option>
                        <option label="Insurance" value="insurance">Insurance</option>
                        <option label="Leisure Sports and Recreation" value="leisure sports and recreation">Leisure Sports and Recreation</option>
                        <option label="Media" value="media">Media</option>
                        <option label="Mining and Metals" value="mining and metals">Mining and Metals</option>
                        <option label="Pharmaceuticals and Biotechnology" value="pharmaceuticals and biotechnology">Pharmaceuticals and Biotechnology</option>
                        <option label="Real Estate" value="real estate">Real Estate</option>
                        <option label="Retail" value="retail">Retail</option>
                        <option label="Schools and Education" value="schools and education">Schools and Education</option>
                        <option label="Telecommunications" value="telecommunications">Telecommunications</option>
                        <option label="Transportation" value="transportation">Transportation</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label for="sub_industry" class="col-sm-6">Sub Industry</label>
                <div class=" col-sm-6">
                    <select name="sub_industry" multiple="multiple" title="" id="sub_industry" disabled="" style="display: none;"></select>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12" style="display:inline;">
                <h5 style="display:inline;">Company Name</h5>
            </div>
        </div>
        <div class="row">
            <div class=" col-sm-2">
                <label for="company_name" style="display:inline-block;">Company Name </label>
            </div>
            <div class="col-sm-4">
                <input id="company_name" class="form-control" type="text">

            </div>
            <div class="col-sm-4">
                <label for="full_text" data-placement="bottom" data-toggle="popover" title="" data-trigger="hover" data-html="true" data-content="Full text search will return any companies which contain the text entered here.  Unchecked will return only companies starting with the entered text." data-original-title="Help">Full Text Search
                    <input id="full_text" class="form-control" name="full_text" type="checkbox" value="1" checked="">
                </label>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-12 text-center">
                <input id="filter_new_list" class="btn btn-info" type="button" value="Search Companies">
            </div>
        </div>
</div>