// init global vars
var contactInformation,
    targetListTable,
    accountTable,
    newlyCreatedListId = null;

// add listener for campaigns change
$('#acc_campaigns').on('change', function (event) {

    // make sure that there are campaigns to select from
    if ($(this).val().length > 0) {



        // fetch filter data (contact info and target_lists)
        fetchFilterData($(this).val(), true);
    }
    else {
        $('#contact_info_container').hide();
        $('#target_lists').html('');
    }


});

$('.delete-list').on('click', function () {
    delete_lists();
});

$('#target_lists').on('change', function (event) {
    if (!$('#editList').is(':visible'))
        displayTargetListData();
});

// add listener for review/edit list
$('#edit_list').on('click', function (event) {

    // display target list
    $('#actions_list_container').show();

    $('#edit_list').hide();
    $('#editList').show();
    $('#target_lists').show();
    $('#show_filter').show();
});

// add listener for review and remove accounts
$('#editList').on('click', function (event) {
    displayTargetListData();
    $('.save-to-list').hide();
    $('.remove-accounts').show();
    $('.lock-list').show();
    $('#editList').hide();
    $('#create_list').show();
    $('#show_filter').show();
});

// add listener for remove accounts button
$('.remove-accounts').on('click', function () {
    removeAccounts();
});

// add listener for remove accounts button
$('#show_filter').on('click', function () {
    $('#editList').show();
    $('#show_filter').hide();
    createAccount();
});

// add listener to 'Clear Filters' button
$('#clear_filter').on('click', function () {
    clearFilters();
});

$('#submit-final-list').on('click', function () {
    var tl = $('#target_lists');

    var edit_disabled = tl.find('option:selected').attr('edit_disabled');

    // fetch selected data
    var accounts = accountTable.rows({selected: true}).data(),
        target_list = tl.val(),
        record_ids = [];

    alert('info', 'Warning!', 'Are you sure you want to submit? Once done, it can only be edited through your Campaign Results Director. (' + accounts.length + ' Accounts selected)', function (alert_modal) {

        // remove alert modal
        alert_modal.modal('hide');

        // show loader
        $('#accountsLoader').show();
        $('#accountsSection').hide();

        // go through each selected row
        $.each(accounts, function (index, row) {

            // fetch record id
            record_ids.push($(row[11]).attr('id'));
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if (accounts > 0) {
            $.ajax({
                type: 'POST',
                url: '/add-to-list',
                data: {
                    target_list_id: target_list,
                    accounts: record_ids
                },
                dataType: 'json',
                success: function (data) {

                    // show the target list's data
                    displayTargetListData();

                    // show/hide appropriate actions
                    $('.save-to-list').hide();
                    $('.remove-accounts').show();
                    $('.lock-list').show();


                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: '/lock-list',
                        data: {
                            target_list_id: $('#target_lists').val(),
                        },
                        dataType: 'json',
                        success: function (data) {

                            fetchFilterData($('#acc_campaigns').val());

                            $('.list-data').hide();
                            $('#accountsLoader').hide();
                            $('#create_list').show();
                            $('#edit_list').show();
                            $('#editList').hide();

                            alert('alert-success', 'Success!', 'Accounts added to the list.');

                        },
                        error: function (data) {
                            fetchFilterData($('#acc_campaigns').val());

                            $('.list-data').hide();
                            $('#accountsLoader').hide();
                            $('#create_list').show();
                            $('#edit_list').show();
                            $('#editList').hide();

                            alert('alert-error', 'Error!', 'There was an error while locking the list, please contact your Portal Administrator.');
                        }
                    });
                },
                error: function (data) {

                    // display error in console
                    alert('alert-error', 'Error!', 'There was an error while adding Accounts to the list, please contact your Portal Administrator.');
                }
            });
        }
        else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/lock-list',
                data: {
                    target_list_id: $('#target_lists').val(),
                },
                dataType: 'json',
                success: function (data) {

                    fetchFilterData($('#acc_campaigns').val());

                    $('.list-data').hide();
                    $('#accountsLoader').hide();
                    $('#create_list').show();
                    $('#edit_list').show();
                    $('#editList').hide();

                    alert('alert-success', 'Success!', 'Accounts added to the list.');

                },
                error: function (data) {
                    fetchFilterData($('#acc_campaigns').val());

                    $('.list-data').hide();
                    $('#accountsLoader').hide();
                    $('#create_list').show();
                    $('#edit_list').show();
                    $('#editList').hide();

                    alert('alert-error', 'Error!', 'There was an error while locking the list, please contact your Portal Administrator.');
                }
            });
        }

    });

});

$('#industry').on('change', function () {
    load_subindustries();
});

// init multiselect fields
$('#industry').multiselect({
    texts: {
        selectAll: "Select All",
        placeholder: 'Choose Industries'
    },
    selectAll: true,
    maxHeight: 200
});
$('#sub_industry').multiselect({
    texts: {
        placeholder: 'Choose SubIndustries',
        selectAll: "Select All"
    },
    searchOptions: {
        showOptGroups: true
    },
    selectAll: true,
    selectGroup: true,
    maxHeight: 200
});
$('#state').multiselect({
    texts: {
        selectAll: "Select All",
        placeholder: 'Choose States'
    },
    selectAll: true,
    maxHeight: 200
});

$('#filter_new_list').on('click', function () {
    filter('filter_new_list');
});

$('#show-filter').on('click', function () {
    $('#save-to-list').hide();
    $('.list-data').hide();
    $('#filterset').show();
});

$('#create_list').on('click', function () {
    showCreate();
    $('#actions_list_container').hide();
    $('#accountsSection').hide();
});

$('.lock-list').on('click', function () {
    lockList();
});

$('#cancel_new_list').click(function () {
    $('#editList-div').show();
    $('#show_filter-div').show();
    $('#list_select').show();
    $('#list_create').hide();
    $('#action_buttons_container').show();
    $('#edit_list').show();
    $('#show_filter').hide();
    $('#editList').hide();
});

$('.modal-cancel').on('click', function () {
    $('.alert-modal-interactive').modal('hide');
});

$('.modal-close').on('click', function () {
    $('.success-modal-interactive').modal('hide');
    $('.error-modal-interactive').modal('hide');
});

$('#save-to-list').on('click', function () {
    add_list();
});

/**
 * Fetches filter data
 * contact_information, target_lists
 *
 * @param campaign
 * @param default_view
 */
function fetchFilterData(campaign, default_view) {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: 'POST',
        url: '/target-lists',
        data: {
            campaign_id: campaign
        },
        dataType: 'json',
        success: function (data) {

            // display contact info
            $('#contact_info_container').show();

            // display action buttons
            $('#action_buttons_container').show();

            // every time a campaign is selected in the dropdown default_view flag will be passed.
            // when that happens we need to show 'default view' (e.g hide create list field etc.)
            if (default_view) {

                $('#edit_list').show();

                $('#list_create').hide();

                $('#editList').hide();

                $('#show_filter').hide();

                $('#actions_list_container').hide();

                $('#accountsSection').hide();
            }

            // store contact information data (we might need it later)
            contactInformation = data.contact_info;

            // set contact info data
            setContactInformation(data.contact_info);

            // set target list data
            setTargetListDropdown(data.target_list);
        },
        error: function (data) {

            console.log('Error:', data.responseText);
        }
    });
}

/**
 * Sets contact information details
 *
 * @param contact_info
 */
function setContactInformation(contact_info) {

    // go through each field
    $.each(contact_info, function (field_name, field_value) {

        // set values for each field
        $('#' + field_name).text(field_value);
    });

    $('.contact_id').val(contact_info.id);
    $('.client_id').val(contact_info.client_id);
}

/**
 * Sets dropdown options for target list dropdown
 *
 * @param target_lists
 */
function setTargetListDropdown(target_lists) {

    var targetList = $('#target_lists');

    // empty the target list
    targetList.html('');
    var option,
        selected = false;

    $.each(target_lists, function (key, data) {

        if (!data.edit_disabled && !selected) {
            // build option
            option = $('<option selected>').prop('value', data.list_id).text(data.list_name);
            selected = true;
        }
        else {
            option = $('<option>').prop('value', data.list_id).text(data.list_name);
        }

        // check if list is disabled
        if (data.edit_disabled) {

            // set the list as disabled - prevent selecting
            option.attr("disabled", "disabled");
        }

        // add option to dropdown
        targetList.append(option);
    });

    // check if new list has just been created
    if (newlyCreatedListId) {

        // set the newly created list as selected
        targetList.val(newlyCreatedListId);

        // reset the var
        newlyCreatedListId = null;
    }

    if ($('.list-data').is(":visible")) {
        $('#target_lists').change();
    }
}

/**
 * Displays DataTable target list data
 */
function displayTargetListData() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // display loader
    $('#accountsLoader').show();
    $('#accountsSection').hide();

    $.ajax({
        type: 'POST',
        url: '/target-lists-dt',
        data: {
            target_list_id: $('#target_lists').val()
        },
        dataType: 'json',
        success: function (data) {

            // hide loader loader
            $('#accountsLoader').hide();
            $('#accountsSection').show();

            // display actions and table
            $('.list-data').show();

            // hide filters
            $('#filterset').hide();

            // display contact info
            $('#contact_info_container').show();

            // display action buttons
            $('#action_buttons_container').show();

            var DTable = $('#target_list_dt');

            if (DTable.hasClass('dataTable')) {
                DTable.DataTable().destroy();
            }

            targetListTable = DTable.DataTable({
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                data: data,
                order: [[1, "asc"]],
                searching: false,
                pageLength: 100,
                bLengthChange: false,
                columns: [
                    {title: "", targets: 'no-sort', orderable: false},
                    {title: "Name"},
                    {title: "Area Code"},
                    {title: "Employees"},
                    {title: "Revenue"},
                    {title: "City"},
                    {title: "State"},
                    {title: "Zip Code"},
                    {title: "Industry"},
                    {title: "Sub Industry"},
                    {title: "Website"},
                    {title: "More Info"}
                ]
            });

            // deselecte all records!
            $('.select-all').removeClass('selected');

            // total number of records
            $('.num-companies').text(targetListTable.rows().count());
            $('.num-selections').text(0);

            targetListTable.on('select deselect', function (e, dt, type, indexes) {

                if (type === 'row') {
                    // number of selected records
                    $('.num-selections').text(targetListTable.rows({selected: true}).count());
                }
            });

            // init tooltips
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });
        },
        error: function (data) {

            // display error in console
            console.log('Error:', data.responseText);
        }
    });
}

/**
 * Removes selected accounts from target list
 */
function removeAccounts() {

    // fetch selected data
    var data = targetListTable.rows({selected: true}).data(),
        record_ids = [];

    // go through each selected row
    $.each(data, function (index, row) {

        // fetch record id
        record_ids.push($(row[11]).attr('id'));
    });

    // make sure that we have atleast 1 selected row
    if (record_ids.length == 0) {

    }
    else {
        alert('info', 'Warning!', 'Are you sure that you want to remove selected records?', function (alert_modal) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // remove alert modal
            alert_modal.modal('hide');

            $('#accountsLoader').show();
            $('#accountsSection').hide();

            $.ajax({
                type: 'POST',
                url: '/remove-accounts',
                data: {
                    target_list_id: $('#target_lists').val(),
                    accounts: record_ids
                },
                dataType: 'json',
                success: function (data) {

                    // update datatables
                    displayTargetListData();
                },
                error: function (data) {

                    // display error in console
                    console.log('Error:', data.responseText);
                }
            });
        });
    }
}

/**
 * Displays alert
 *
 * @param type
 * @param title
 * @param msg
 * @param callback
 */
function alert(type, title, msg, callback) {

    switch (type) {
        case 'info':
            // $('.modal-content').css('background-color', '#ffd08d');
            // TODO: set heder
            $('.alert-heading').text(title);
            // TODO: set body
            $('.alert-modal-body').text(msg);

            $('.alert-modal-interactive').modal('show');
            break;
        case 'alert-success':

            $('.success-heading').text(title);
            // TODO: set body
            $('.success-modal-body').text(msg);

            $('.success-modal-interactive').modal('show');

            break;
        case 'alert-error':

            $('.error-heading').text(title);
            // TODO: set body
            $('.error-modal-body').text(msg);

            $('.error-modal-interactive').modal('show');

            break;
    }

    $('.modal-confirm').unbind('click');

    $('.modal-confirm').on('click', function () {
        // TODO: add ajax
        // $('.alert-modal-interactive').modal('hide');

        callback($('.alert-modal-interactive'));
    });

}

function createAccount() {
    $('#filterset').show();
    $('.list-data').hide();
    $('#accountsSection').show();
}

/**
 * Clears currently set filters
 */
function clearFilters() {

    // clear the form
    $('#employees_max').val('');
    $('#employees_min').val('');
    $('#revenue_max').val('');
    $('#revenue_max_select').val('1000000');
    $('#revenue_min').val('');
    $('#revenue_min_select').val('1000000');
    $('#state').multiselect('reset');
    $('#zipcode').val('');
    $('#city').val('');
    $('#area_code').val('');
    $('#industry').multiselect('reset');
    $('#sub_industry').multiselect('reset');
    $('#company_name').val('');
    $('#full_text').prop('checked', true);

    $('#sortcolumn').val('');
    $('#sortdirection').val('');
}

function load_subindustries() {

    var industries = {};
    industries['aerospace and defense'] = [
        'Aircraft Manufacturing',
        'Aerospace Products and Parts',
        'Space Vehicles Satellites and Related',
        'Ordnance Missiles Weaponry and Related',
        'Military Vehicles Manufacturing',
        'Aerospace Research and Development',
    ];
    industries['automotive'] = [
        'Motor Vehicle Manufacturing',
        'Truck Bus and Big Rig Manufacturing',
        'Automobile Parts Manufacturing',
        'Truck and Bus Parts Manufacturing',
        'Motor Vehicle Parts Suppliers',
        'Motor Vehicle Repair and Servicing',
    ];
    industries['banks'] = [
        'Regional Banks',
        'Savings Institutions',
        'Credit Unions',
        'Banking Transaction Processing',
        'Credit Agencies',
        'ShortTerm Business Loans and Credit',
        'Commercial Banking',
        'Central Banks',
        'Mortgage Banking',
        'Automated Teller Machine Operators',
        'Trust Fiduciary and Custody Activities',
        'Offshore Banks',
        'Islamic Banks',
        'Landesbanken',
    ];
    industries['civic non profit and membership groups'] = [
        'Social Services Institutions',
        'Charities',
        'Religious Organizations',
        'Labor Unions',
        'Public Policy Research and Advocacy',
        'Political Organizations',
        'Business Associations',
        'Environmental and Wildlife Organizations',
        'Foundations',
        'Humanitarian and Emergency Relief',
    ];
    industries['chemicals'] = [
        'Medicinal Chemicals and Botanicals',
        'Commodity Chemicals',
        'Agricultural Chemicals',
        'Diversified Chemicals',
        'Paints Dyes Varnishes and Lacquers',
        'Sealants and Adhesives',
        'Specialty Chemicals',
        'Plastic and Fiber Manufacturing',
        'Industrial Fluid Manufacturing',
        'Explosives and Petrochemicals',
        'Polymers and Films',
        'Fine Chemicals',
    ];
    industries['computer hardware'] = [
        'POS and Electronic Retail Equipment',
        'Printing and Imaging Equipment',
        'Servers and Mainframes',
        'Computer Storage Devices',
        'Computer Monitors and Projectors',
        'ATMs and SelfService Kiosks',
        'Routing and Switching Devices',
        'Wireless Networking Equipment',
        'Computer Networking Equipment',
        'Network Security Devices',
        'Computer Peripherals and Accessories',
        'Semiconductors',
        'CD and Other Optical Discs Production',
        'Magnetic and Optical Recording',
        'Handheld Devices and Smartphones',
        'Desktop and Laptop Computers',
    ];
    industries['computer software'] = [
        'Accounting and Tax Software',
        'Agriculture Industry Software',
        'Application Service Providers ASPs',
        'Financial Services Software',
        'Automotive Industry Software',
        'Billing and Service Provision Software',
        'Budgeting and Forecasting Software',
        'Business Intelligence Software',
        'Casino Management Software',
        'Catalog Management Software',
        'Channel Partner Management Software',
        'Collaborative Software',
        'Construction and Architecture Software',
        'Content Management Software',
        'Customer Relationship Management',
        'Database Management Software',
        'Development Tools and Utilities Software',
        'Distribution Software',
        'Advertising Industry Software',
        'Ecommerce Software',
        'Education and Training Software',
        'Engineering Scientific and CAD Software',
        'Enterprise Application Integration EAI',
        'Enterprise Resource Planning Software',
        'Event Planning Industry Software',
        'Food and Beverage Industry Software',
        'Health Care Management Software',
        'Hotel Management Industry Software',
        'Human Resources Software',
        'Insurance Industry Software',
        'Marketing Automation Software',
        'Retail Management Software',
        'Law Enforcement Industry Software',
        'Legal Industry Software',
        'Manufacturing and Industrial Software',
        'Message Conference and Communications',
        'Multimedia and Graphics Software',
        'Networking and Connectivity Software',
        'Order Management Software',
        'Procurement Software',
        'Project Management Software',
        'Purchasing Software',
        'Quality Assurance Software',
        'Real Estate Industry Software',
        'Restaurant Industry Software',
        'Data Warehousing',
        'Sales Force Automation Software',
        'Sales Intelligence Software',
        'Service Industry Software',
        'Security Software',
        'Storage and Systems Management Software',
        'Supply Chain and Logistics Software',
        'Textiles Industry Software',
        'Tourism Industry Software',
        'Trading and Order Management Software',
        'Warehousing Software',
        'Transportation Industry Software',
        'Wireless Communication Software',
        'Document Management Software',
        'File Management Software',
        'Desktop Publishing Software',
        'Defense and Military Software',
        'Banking Software',
        'Telecommunication Software',
        'Pharma and Biotech Software',
        'Smart Home Software',
        'Electronics Industry Software',
        'Management Consulting Software',
        'Mobile Application Software',
        'Operating System Software',
        'Analytics and Reporting Software',
        'ComputerAided Manufacturing Software',
        'Compliance and Governance Software',
    ];
    industries['construction and building materials'] = [
        'Apartment and Condominium Construction',
        'Window and Door Manufacturing',
        'Residential General Contractors',
        'SingleFamily Housing Builders',
        'NonResidential General Contractors',
        'Heavy Construction',
        'Water Sewer and Power Line',
        'Specialty Construction',
        'Electrical Contractors',
        'Architecture Services',
        'Engineering Services',
        'Construction Equipment manufacturing',
        'Plumbing and HVAC Equipment',
        'Hardware Wholesalers',
        'Construction Equipment Sales',
        'Wood Products manufacturing',
        'Sawmills and Other Mill Operations',
        'Plywood Veneer and Particle Board',
        'Prefabricated Buildings',
        'Aggregates Concrete and Cement',
        'Construction Materials',
        'Ceramic Tile Roofing and Clay Products',
        'Carpentry and Floor Work',
        'Asphalt and Roofing Materials',
        'Electric Lighting and Wiring',
        'Stone Products',
        'Sheet Metal',
        'Infrastructure Construction',
        'Oil and Gas Pipeline Construction',
        'Specialty Trade Contractors',
    ];
    industries['consumer product manufacturing'] = [
        'Household Products',
        'Tobacco Products and Distributors',
        'Textile Products',
        'Apparel',
        'Childrens Clothing',
        'Mens Clothing',
        'Womens Clothing',
        'Fashion Accessories',
        'Carpets Rugs and Floor Coverings',
        'Household Furniture',
        'Mattresses and Bed Manufacturers',
        'Outdoor Furniture and Storage Products',
        'Soaps and Detergents',
        'Specialty Cleaning Products',
        'Perfumes Cosmetics and Toiletries',
        'Footwear',
        'Major Appliances',
        'Watches and Clocks',
        'Jewelry and Gemstones',
        'Office Furniture and Fixtures',
        'Paper Products',
        'Leather Products',
        'Pottery',
        'Garden Equipment and Mowers',
        'Home Furnishings',
        'Collectibles and Giftware',
        'Linens',
        'Window Coverings and Wall Coverings',
        'Baby Supplies and Accessories',
        'Home Storage Products',
        'Pet Products',
        'Photographic Equipment and Supplies',
        'Hand and Power Tools',
        'Dinnerware Cookware and Cutlery',
        'Personal Products',
        'Consumer Electronics',
        'Electronic Gaming Products',
        'Stationery and Related Products',
        'Games and Toys',
        'Bicycles and Accessories',
        'Musical Instruments',
        'Sporting Goods Outdoor Gear and Apparel',
        'Sports Equipment',
        'Art Supplies',
        'Dietary supplements',
        'Pet Food Products',
        'Costume Makers',
        'Security and Alarm Systems',
        'Guns and Ammunition',
    ];
    industries['consumer services'] = [
        'Child Care Services',
        'Personal Services',
        'Motor Vehicle Rental and Leasing',
        'Death Care Products and Services',
        'Beauty Salons',
        'Laundry and Dry Cleaning Services',
        'Photographic Services',
        'Travel and Tourism',
        'Veterinary Care',
        'Weight and Health Management',
        'Gym Spa and Fitness',
        'Courier Messenger and Delivery Services',
        'Moving Services',
        'Taxi and Limousine Services',
        'Motor Vehicle Parking and Garages',
        'Auctions and Internet Auctions',
        'Consumer Electronics Repair Services',
        'Landscaping and Gardening Services',
        'Carpenters',
        'Electricians',
        'Plumbing Services',
        'Catering Services',
        'Wedding Planners',
    ];
    industries['corporate services'] = [
        'Commercial Printing Services',
        'Services for the Printing Trade',
        'Commercial Design Services',
        'Direct Marketing',
        'Sales Promotion',
        'Executive Search',
        'Online Staffing and Recruitment Services',
        'Talent and Modeling Agencies',
        'Human Resources and Staffing',
        'Outsourced Human Resources Services',
        'Integrated Computer Systems Design',
        'Programming and Data Processing Services',
        'Records Management Services',
        'Computer Facilities',
        'Computer Related Services',
        'Uniform Supplies',
        'Detective and Security Services',
        'Business Services',
        'Legal Services',
        'Mediation and Arbitration',
        'Management Consulting',
        'IT Services and Consulting',
        'Marketing and Advertising',
        'Advertising Agencies',
        'Billboards and Outdoor Advertising',
        'Equipment Rental and Leasing',
        'Repair and Maintenance Services',
        'Testing Lab and Scientific Research',
        'Public Relations',
        'Market Research Services',
        'Trade Show Event Planning and Support',
        'Cleaning and Facilities Management',
        'Mobile Application Developers',
        'Transcription Services',
        'Translation Services',
        'Data and Analytics Services',
        'Call Centers',
    ];
    industries['agriculture and forestry'] = [
        'Fruits and Vegetables Farming',
        'Grains Farming',
        'Livestock and Husbandry',
        'Agriculture Services',
        'Forestry and Logging',
        'Fishing and Aquaculture',
        'Farming Materials and Supplies',
        'Agricultural Machinery and Equipment',
        'Pulses and Legume Farming',
        'Oilseeds Farming',
        'Coffee Tea and Cocoa Farming',
        'Cotton and Textile Farming',
        'Sericulture and Beekeeping',
        'Horticulture',
        'Agricultural Product Distribution',
        'Irrigation and Drainage Districts',
    ];
    industries['electronics'] = [
        'Electronic Components and Accessories',
        'Printed Circuit Boards',
        'Electronic Coils and Transformers',
        'Electronic Connectors',
        'Miscellaneous Electrical Equipment',
        'Electric Testing Equipment',
        'Sound and Lighting Equipment',
        'Heavy Electrical Equipment',
        'Motors and Generators',
        'Vending Machines',
    ];
    industries['energy and environmental'] = [
        'Energy Equipment',
        'Crude Petroleum Production',
        'Oil and Gas Production and Exploration',
        'Oil and Gas Field Services',
        'Oil and Gas Refining',
        'Oil and Gas Transport and Storage',
        'Liquefied Petroleum Gas Dealers',
        'Oil and Gas Equipment',
        'Petroleum Pipelines',
        'Electricity Transmission',
        'Electric Utilities',
        'Electricity Distribution',
        'Coal Energy Generation',
        'Hydroelectric Power Generation',
        'Nuclear Power Generation',
        'Cogeneration and Small Power Producers',
        'Natural Gas Pipelines',
        'Natural Gas Transmission',
        'Natural Gas Utilities',
        'Multiline Utilities',
        'Water Supply and Utilities',
        'Sanitation Services',
        'Solid Waste and Refuse Systems',
        'Hazardous Waste Management',
        'Wholesale Petroleum and Related Products',
        'Alternative Energy Sources',
        'Energy Trading and Marketing',
        'Environmental Services',
        'Remediation and Environmental Cleanup',
        'Recycling Services',
        'Wastewater Treatment',
        'Conservation Districts',
        'Sanitary and Sewage Districts',
        'Waste Management Districts',
        'Wind Power Generation',
        'Solar Power Generation',
    ];
    industries['financial services'] = [
        'Investment Services and Advice',
        'Diversified Financial Services',
        'Specialty Financial Services',
        'Accounting Tax Bookkeeping and Payroll',
        'Asset Management',
        'Pension and Retirement Funds',
        'Securities Brokers and Traders',
        'Currency Commodity  Futures Trading',
        'Currency and Forex Brokers',
        'Blank Check Companies',
        'Miscellaneous Investment Firms',
        'Patent Owners and Lessors',
        'Investment Banking',
        'Investment Trusts',
        'Venture Capital',
        'Investment Management and Fund Operators',
        'Diversified Lending',
        'Credit Cards',
        'Forfeiting and Factoring',
        'Market Makers and Trade Clearing',
        'Stock Exchanges',
        'Trade Facilitation',
        'Credit and Collection Services',
        'Consumer Credit Reporting',
        'Electronic Payment Systems',
        'Financial Transaction Settlement',
        'Energy Exchanges',
        'Finance Authorities',
        'Credit Rating Agency',
        'Private Equity',
        'Crowdfunding',
        'Mortgage Brokers',
        'Hedge Funds',
        'Royalty Trusts',
        'Leveraged Finance',
        'Mobile Payment Systems',
        'Electronic Funds Transfer',
        'Credit Intermediation',
        'Financial Leasing Companies',
        'Endowment Funds',
    ];
    industries['food and beverage'] = [
        'Food Products',
        'Meat Packing and Meat Products',
        'Poultry',
        'Dairy Products',
        'Canned and Frozen Fruits and Vegetables',
        'Flours Sugar and Mixes',
        'Pastas and Cereals',
        'Baked Goods',
        'Processed Food Products',
        'Candy and Confections',
        'Food Oils',
        'Breweries',
        'Flavorings Spices and Other Ingredients',
        'Food Wholesale Distributors',
        'NonAlcoholic Beverages',
        'Fresh and Frozen Seafood',
        'Beverage Distillers',
        'Alcoholic Beverage Distribution',
        'Wineries',
        'Beverage Bottling',
        'Ice creams and Frozen Desserts',
        'Coffee and Tea Manufacturers',
        'Fruits and Nuts',
        'Food and Beverage Processing Machinery',
        'Infant food',
    ];
    industries['government'] = [
        'Federal Government Agencies',
        'State Provincial or Regional Government',
        'Executive Government Offices',
        'Legislatures',
        'International Government Agencies',
        'National Security',
        'County Governments',
        'Cities Towns and Municipalities',
        'Villages and Small Municipalities',
        'Fire Protection',
        'Police Protection',
        'Courts of Law',
        'Correctional Facilities',
        'Legal Counsel and Prosecution',
        'Special Districts',
        'Regional Promotion Agencies',
    ];
    industries['hospitals and healthcare'] = [
        'Medical Practice Management and Services',
        'General Physicians and Clinics',
        'Dentists',
        'Nursing Homes and Extended Care',
        'General Medical and Surgical Hospitals',
        'Psychiatric Hospitals',
        'Other Specialty Hospitals',
        'Medical and Dental Laboratories',
        'Home Healthcare',
        'Ophthalmic Equipment',
        'Surgical and Medical Devices',
        'Orthopedic and Prosthetic Equipment',
        'Dental Equipment and Supplies',
        'Xray Equipment',
        'General Healthcare Equipment',
        'Integrated Healthcare Networks',
        'Ambulance Services',
        'Substance Abuse Rehabilitation Centers',
        'Emergency Medical Services',
        'Childrens Hospitals',
        'Specialty Surgical Hospitals',
        'Cardiologists',
        'Chiropractors',
        'Dermatologists',
        'Obstetricians and Gynecologists',
        'Ophthalmologists and Optometrists',
        'Pediatricians',
        'Mental Health Practitioners',
        'Radiology Services',
        'Oncology Services',
        'Orthopedic Services',
        'Podiatrists',
        'Assisted Living Facilities',
        'Hospice Services',
        'Diagnostic Imaging Centers',
        'Blood and Organ Banks',
        'Kidney Dialysis Centers',
        'Fertility Clinics',
        'Electromedical and Therapeutic Equipment',
        'Family Planning Clinics',
        'Urgent Care Centers',
        'Healthcare Districts',
        'Physical Therapy Facilities',
        'Neurologists',
        'Cosmetic Surgery',
        'Anesthesiologists',
    ];
    industries['holding companies'] = [
        'Industrial Conglomerates',
    ];
    industries['industrial manufacturing and services'] = [
        'Paper Board and Paper Products',
        'Pulp Paper and Paperboard Mills',
        'Paper Containers and Packaging',
        'Recycled and Converted Paper Products',
        'Plastics Foil and Coated Paper Bags',
        'Plastic Materials and Synthetic Resins',
        'Petroleum Products',
        'Tires and Inner Tubes',
        'Gaskets and Sealing Devices',
        'Fabricated Rubber Products',
        'Flat Glass',
        'Glass Products',
        'Heating Equipment',
        'Screw Machines',
        'Bolts Nuts Screws Rivets and Washers',
        'Fabricated Wire Products',
        'Engines and Turbines',
        'Metalworking Machinery',
        'Printing Press Machinery',
        'Specialty Industrial Machinery',
        'Industrial Equipment and Machinery',
        'Pumping Equipment',
        'Ball Bearings and Roller Bearing',
        'Industrial Fans',
        'Industrial Furnaces and Ovens',
        'Building Climate Control and HVAC',
        'Power Distribution and Transformers',
        'Switching and Switchboard Equipment',
        'Relays and Industrial Controls',
        'Laboratory Equipment',
        'Industrial Measurement Devices',
        'Totalizing Fluid Meters',
        'Measuring Devices and Controllers',
        'Commercial Equipment and Supplies',
        'Industrial Machinery Distribution',
        'Air and Gas Compressors',
        'Steel Pipes and Tubes',
        'Metal Cans',
        'Shipping Barrels Drums Kegs and Pails',
        'Fabricated Structural Metal',
        'Metal Forgings',
        'Coating and Engravings',
        'Machine Tools and Metal Equipment',
        'Cotton Fiber and Silk Mills',
        'Industrial Contractors',
        'Steel Wire Drawing Nails and Spikes',
        'Injection Molding and Die Casting',
    ];
    industries['insurance'] = [
        'Risk Management',
        'Life Insurance',
        'Multiline Insurance',
        'Workers Compensation',
        'Health Insurance',
        'Property and Casualty Insurance',
        'Fire and Marine Insurance',
        'Automobile Insurance',
        'Credit Insurance',
        'Surety Insurance',
        'Commercial Insurance',
        'Liability Insurance',
        'Mortgage Insurance',
        'Homeowners and Title Insurance',
        'Reinsurance',
        'Specialty Insurance',
        'Insurance Agents and Brokers',
        'Claims Administration and Processing',
        'Disability Insurance',
        'Travel Insurance',
        'Insurance Financing',
    ];
    industries['leisure sports and recreation'] = [
        'Accommodation',
        'Casinos and Gambling',
        'Hotels and Motels',
        'Museums and Art Galleries',
        'Performing Arts',
        'Amusement and Recreation',
        'Professional Sports Teams',
        'Racetracks',
        'Athletic Facilities',
        'Sports and Recreations Clubs',
        'Motion Picture Theaters',
        'Restaurants Bars and Eateries',
        'Park and Recreation Districts',
        'Convention Centers Arenas and Stadiums',
        'Internet Gaming Arcades',
        'Zoos and Parks',
        'Amusement Parks and Theme Parks',
        'Cruise Lines',
        'Golf Courses and Country Clubs',
        'Ski Resorts',
    ];
    industries['media'] = [
        'Diversified Media',
        'Newspapers and Online News Organizations',
        'Magazine Publishers',
        'Book Publishers',
        'Directories and Yellow Pages Publishers',
        'Greeting Cards',
        'Trading Cards and Comic Books',
        'Music Production and Distribution',
        'Radio and Online Music Broadcasting',
        'Television Broadcasting',
        'Cable Television Networks',
        'Movie and TV Broadcasting Equipment',
        'Movie Production and Distribution',
        'Internet Information Services',
        'Internet Content Providers',
        'Internet Search and Navigation Services',
        'Video Game Production',
        'Social Media',
        'Motion Picture PostProduction Services',
    ];
    industries['mining and metals'] = [
        'Precious Metals and Minerals',
        'Specialty Mining and Metals',
        'Gold and Silver Mining',
        'Diversified Metals and Mining',
        'Coal Mining',
        'NonMetallic Mining and Quarrying',
        'Concrete and Gypsum',
        'Steel Works Furnaces and Coke Ovens',
        'Iron and Steel Foundries',
        'Metal Smelting and Refining',
        'Aluminum Mining and Foundries',
        'Metal Rolling and Extruding',
        'NonFerrous Foundries',
        'Miscellaneous Metal Products',
        'Mining and Quarrying Machinery',
        'Metals and Minerals Distribution',
        'Metal Ore Mining',
        'Limestone Granite and Stone',
        'Sand And Gravel',
        'Diamond and Other Precious Stone Mining',
    ];
    industries['pharmaceuticals and biotechnology'] = [
        'Diversified Pharmaceuticals',
        'Generic Pharmaceuticals',
        'Drug Delivery Systems',
        'Diagnostic Substances',
        'Pharmaceuticals Wholesale',
        'Scientific Research Services',
        'Biopharmaceuticals and Biotherapeutics',
        'Biotechnology Research Equipment',
        'Biotechnology Research',
        'Biological Products',
        'Specialty Pharmaceuticals',
        'Veterinary Drugs',
        'Life Sciences Research and Development',
    ];
    industries['real estate'] = [
        'Real Estate Development',
        'Real Estate Operating Company REOC',
        'Real Estate Property Management',
        'Mobile Home Parks',
        'Real Estate Leasing',
        'Real Estate Appraisers',
        'Real Estate Brokers',
        'Real Estate Investment Trusts REITs',
        'Other Real Estate Investors',
        'Office REITs',
        'Health Care REITs',
        'Hotel and Motel REITs',
        'Industrial REITs',
        'Residential REITs',
        'Retail REITs',
        'Leisure and Entertainment REITs',
        'Mortgage REITs',
        'Land REITs',
        'Title Abstract Offices',
        'Housing Authorities',
        'Industrial Development Authorities',
        'Planning and Development Authorities',
        'School and Public Building Authorities',
        'Cemetery Developers and Operators',
    ];
    industries['retail'] = [
        'Building Materials Retail',
        'Lumber Retail',
        'Home Improvement and Hardware Stores',
        'Mobile Home Dealers',
        'Department Stores',
        'Discount and Variety Stores',
        'General Retailers',
        'Warehouse Clubs and Superstores',
        'Hyper and Supermarkets',
        'Mini Markets',
        'Convenience Stores and Gas Stations',
        'Motor Vehicle Dealers',
        'Boat Retail',
        'Apparel Retail',
        'Internet Retail',
        'Womens Clothing Retail',
        'Perfume Cosmetics and Toiletries Retail',
        'Footwear Retail',
        'Home Furnishing Retail',
        'Furniture Retail',
        'Electronics Retail',
        'Computer and Software Retail',
        'Musical Equipment Retail',
        'Specialty Retailers',
        'Pharmacies and Drug Stores',
        'Sports and Recreational Equipment Retail',
        'Music Video and Book Retail',
        'Jewelry and Gemstone Retail',
        'Clock and Watch Retail',
        'Hobby Toy and Game Stores',
        'Arts Gifts and Novelties Retail',
        'Florists and Nurseries',
        'Mail and Catalog Order Retail',
        'Party and Supply Stores',
        'Office Supplies and Stationery Retail',
        'Audiovisual Equipment Sales and Services',
        'Gardening Supplies',
        'Fashion Accessories Retail',
        'Childrens Clothing Retail',
        'Mens Clothing Retail',
        'Appliances Retail',
        'Maternity and Baby Supplies Retail',
        'Pet Shops',
        'Beer Wine and Liquor Retail',
        'Vending Machines and Automated Kiosks',
        'Dinnerware Cookware and Cutlery Retail',
        'Luggage and Leather Goods Stores',
        'Tobacco and Pipe Stores',
    ];
    industries['schools and education'] = [
        'Educational Services',
        'Elementary and Secondary Schools',
        'Colleges and Universities',
        'Training Institutions and Services',
        'Libraries',
        'Vocational and Technical Schools',
        'Internet Educational Services',
        'Medical Schools',
        'Community Colleges',
        'Graduate and Professional Schools',
        'Law Schools',
        'School Districts',
    ];
    industries['telecommunications'] = [
        'Wireless Network Operators',
        'Telecommunications Services',
        'Local Exchange Carriers',
        'LongDistance Carriers',
        'Telecommunications Resellers',
        'Wired Telecommunications Carriers',
        'Internet and Online Services Providers',
        'Managed Network Services',
        'Messaging Services Providers',
        'Teleconferencing Services Providers',
        'Telemetry and Telematics Services',
        'Telecommunications Equipment',
        'Telecom Switching and Transmission',
        'Videoconferencing Equipment',
        'Satellite and Broadcasting Equipment',
        'Cable and Satellite Services',
        'Electronic Communications Networks',
        'Web Hosting Services',
    ];
    industries['transportation'] = [
        'Ship and Boat Repair',
        'Ship and Boat Manufacturing',
        'Railroad Equipment Manufacturing',
        'Ferries and Water Transport',
        'Marine Shipping',
        'Airlines and Scheduled Air Transport',
        'Air Freight Transportation',
        'NonScheduled and Charter Air Transport',
        'Airport and Terminal Management',
        'Bus Services',
        'Helicopter Services',
        'Highways and Toll Road Management',
        'Storage and Warehousing',
        'Postal Services',
        'Container Leasing',
        'Freight Railroads',
        'Passenger Railroads',
        'Truck Transportation and Services',
        'Truck Rental and Leasing',
        'Railroad Terminal Management',
        'Ports Harbors and Marinas',
        'Terminal Facilities For Motor Vehicles',
        'Aircraft Leasing',
        'Ship and Boat Parts Manufacturing',
        'Marine Transportation Support',
        'Shipping Equipment',
        'Locomotive and Rail Car Manufacturing',
        'Railroads and Rail Tracks Maintenance',
        'Transportation Authorities',
    ];

    $('#sub_industry').empty();

    var industries_list = ($('#industry').val());

    industries_list.forEach(function (industry) {

        var optgroup = $('<optgroup>');
        optgroup.attr('label', toTitleCase(industry));

        industries[industry].forEach(function (subindustry) {
            var option = $('<option></option>');
            option.val(subindustry);
            option.text(subindustry);
            optgroup.append(option);
        });

        $('#sub_industry').append(optgroup);


    });

    $('#sub_industry').multiselect('reload');
    $('#sub_industry').next('.ms-options-wrap').find('> button:first-child').prop('disabled', false);

};

function toTitleCase(str) {
    return str.replace(/\w\S*/g, function (txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

function filter(page_control) {

    // TODO: hide loader loader
    $('#accountsLoader').show();
    $('#accountsSection').hide();

    if (page_control == 'filter_new_list') {
        offset = 1;
    }
    else {
        offset = page_control;
    }

    if ($('#full_text').is(":checked")) {
        fts = 1;
    } else {
        fts = 0;
    }

    var state = $("#state").val();

    filtercollection = $("#company_name").val() + $("#area_code").val() + $("#employees_max").val() + $("#employees_min").val() + $("#revenue_max").val() + $("#revenue_min").val() + $("#city").val() + $("#industry").val() + $("#zipcode").val() + $("#sub_industry").val() + $("#state").val();

    if ($('#CA').is(":checked")) {
        state.push('CA (North)');
        state.push('CA (South)');
    }

    if (!$('#target_lists').val()) {
        // TODO: implement our alert
        window.alert("Please select a list");
    }
    else if (filtercollection.length == 0) {
        window.alert("Please add a filter.");
    }
    else {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/get-account-lists-dt',
            type: 'POST',
            data: {
                filters: {
                    campaign: $('#acc_campaigns').val(),
                    company_name: $("#company_name").val(),
                    area_code: $("#area_code").val(),
                    employees_min: $("#employees_min").val(),
                    employees_max: $("#employees_max").val(),
                    revenue_min: $("#revenue_min").val() * $('#revenue_min_select').val(),
                    revenue_max: $("#revenue_max").val() * $("#revenue_max_select").val(),
                    city: $("#city").val(),
                    fts: fts,
                    state: state,
                    zipcode: $("#zipcode").val(),
                    industry: $("#industry").val(),
                    sub_industry: $("#sub_industry").val(),
                    offset: offset,
                    sortcolumn: $('#sortcolumn').val(),
                    sortdirection: $('#sortdirection').val()
                }
            },
            success: function (data) {

                // hide loader loader
                $('#accountsLoader').hide();
                $('#accountsSection').show();

                // display actions and table
                $('.list-data').show();

                $('.remove-accounts').hide();

                $('.lock-list').hide();

                $('.save-to-list').show();

                // hide filters
                $('#filterset').hide();

                // display contact info
                $('#contact_info_container').show();

                // display action buttons
                $('#action_buttons_container').show();

                var DTable = $('#target_list_dt');

                if (DTable.hasClass('dataTable')) {
                    DTable.DataTable().destroy();
                }

                accountTable = DTable.DataTable({
                    columnDefs: [{
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    }],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child'
                    },
                    data: data,
                    order: [[1, "asc"]],
                    searching: false,
                    pageLength: 100,
                    bLengthChange: false,
                    columns: [
                        {title: "", targets: 'no-sort', orderable: false},
                        {title: "Name"},
                        {title: "Area Code"},
                        {title: "Employees"},
                        {title: "Revenue"},
                        {title: "City"},
                        {title: "State"},
                        {title: "Zip Code"},
                        {title: "Industry"},
                        {title: "Sub Industry"},
                        {title: "Website"},
                        {title: "More Info"}
                    ]
                });

                // deselecte all records!
                $('.select-all').removeClass('selected');

                // total number of records
                $('.num-companies').text(accountTable.rows().count());
                $('.num-selections').text(0);

                accountTable.on('select deselect', function (e, dt, type, indexes) {
                    if (type === 'row') {

                        // number of selected records
                        $('.num-selections').text(accountTable.rows({selected: true}).count());
                    }
                });

                // init tooltips
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip()
                });
            }
        });
    }

}

function showCreate() {
    $('#list_modify').hide();
    $('#list_select').hide();
    $('#filterset').hide();
    $('#list_remove').hide();
    $('#response_area').hide();
    $('#results_area').hide();
    $('#list_create').show();
    $('#action_buttons_container').hide();
}

/**
 * Creates new list
 */
$('#create_new_list').click(function () {

    // make sure that we have list name
    if (!$('#newlistname').val()) {

        // alert user about missing value
        alert('alert-error', 'Warning!', 'Please enter a list name before saving.');

        return;
    }

    // hide the button (while list is being created)
    $('#create_new_list').hide();

    // show loader instead of the button
    $('#create_new_list_loader').show();

    // hide 'cancel' button while list is being created
    // (it's too late for canceling now!)
    $('#cancel_new_list').hide();

    // prevent selecting other campaign (while list is being saved)
    $('#acc_campaigns').prop('disabled', true);

    // disable changing the list's name (while list is being saved)
    $("#newlistname").prop('disabled', true);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // introduce the list's name
    var list_name = $('#newlistname').val();

    $.ajax({
        url: '/create-new-list',
        type: 'POST',
        data: {
            campaign_id: $('#acc_campaigns').val(),
            listname: list_name,
            campaigndirector: $('.contact_id').val(),
            client_id: $('.client_id').val()
        },
        success: function (response) {

            // hide the button (while list is being created)
            $('#create_new_list').show();

            // show loader instead of the button
            $('#create_new_list_loader').hide();

            // show cancel button
            $('#cancel_new_list').show();

            // enable selecting campaigns
            $('#acc_campaigns').prop('disabled', false);

            // enable 'new list name' input
            $("#newlistname").prop('disabled', false);

            // remove the old value from input
            $("#newlistname").val('');

            // display success message
            alert('alert-success', 'Success', "List '" + list_name + "' has been successfully created.");

            // wait 5 seconds before closing success message
            setTimeout(
                function () {
                    $('.success-modal-interactive').modal('hide');
                }, 5000);

            $('#edit_list').click();

            // simulate click on 'Add Accounts' button
            // (hides the button and shows accounts section)
            $('#show_filter').click();

            // fetch filter data (contact info and target_lists)
            fetchFilterData($('#acc_campaigns').val());

            // make sure that list has an ID
            if (response.id) {

                // set global var
                newlyCreatedListId = response.id;
            }

            $('#list_create').hide();
        }
    });

});

function lockList() {

    alert('info', 'Warning!', 'Are you sure you want to submit? Once done, it can only be edited through your Campaign Results Director.', function (alert_modal) {

        // remove alert modal
        alert_modal.modal('hide');

        // show loader
        $('#accountsLoader').show();
        $('#accountsSection').hide();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: '/lock-list',
            data: {
                target_list_id: $('#target_lists').val(),
            },
            dataType: 'json',
            success: function (data) {

                $('#accountsLoader').hide();

                fetchFilterData($('#acc_campaigns').val());

                $('#target_lists').hide();
                $('#show_filter').hide();
                $('#edit_list').show();

                alert('alert-success', 'Success!', 'List submitted successfully.');

            },
            error: function (data) {

                // display error in console
                console.log('Error:', data.responseText);
            }
        });
    });

}

function add_list() {

    // show loader
    $('#accountsLoader').show();
    $('#accountsSection').hide();

    var tl = $('#target_lists');

    var edit_disabled = tl.find('option:selected').attr('edit_disabled');

    // fetch selected data
    var accounts = accountTable.rows({selected: true}).data(),
        target_list = tl.val(),
        record_ids = [];

    // go through/ each selected row
    $.each(accounts, function (index, row) {

        // fetch record id
        record_ids.push($(row[11]).attr('id'));
    });

    if (record_ids.length < 1) {
        alert('alert-error', 'Error!', 'You must select at least one Account record.');
    }

    else {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: '/add-to-list',
            data: {
                target_list_id: target_list,
                accounts: record_ids
            },
            dataType: 'json',
            success: function (data) {

                // show the target list's data
                displayTargetListData();

                // show/hide appropriate actions
                $('.save-to-list').hide();
                $('.remove-accounts').show();
                $('.lock-list').show();

            },
            error: function (data) {

                // display error in console
                console.log('Error:', data.responseText);
            }
        });


        if (edit_disabled == 1) {

            // todo:set eontek alert
            alert('alert-success', 'Success!', "This list has been submitted.");
        }
        else {
            if (tl.val().length > 0) {


            }
            else {
                //todo: eontek alert
                alert('alert-error', 'Error!', "No List Selected.");
            }
        }
    }
}

function delete_lists() {

    if ($("#targetlists").val() !== "") {

        alert('info', 'Warning!', 'Are you sure you want to delete the selected list?  \n This cannot be undone.', function (alert_modal) {

            // remove alert modal
            alert_modal.modal('hide');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/delete-list",
                type: "POST",
                async: false,
                data: {
                    listid: $("#target_lists").val()
                },
                success: function (response) {

                    // fetch filter data (contact info and target_lists)
                    window.location = '/account';

                    alert('alert-success', 'Success!', 'List deleted successfully.');
                },
                error: function (response) {
                    alert('alert-error', 'Error!', 'Failed to delete list. Please contact your portal Administrator.');
                }
            });
        });
    }
    else {
        alert('alert-error', 'Error!', 'No valid list selected.');
    }
}
