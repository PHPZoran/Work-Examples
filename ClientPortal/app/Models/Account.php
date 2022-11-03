<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTimeZone;
use DateTime;
use Spinegar\Sugar7Wrapper\Rest;

/**
 * Class Account
 *
 * @package App\Models
 */
class Account extends Model{

    /**
     * Fetches list of campaigns for current user
     *
     * @return mixed
     */
    public static function fetchCampaigns(){

        // build query
        $query = "SELECT atc_isscampaigns.id 'campid',atc_isscampaigns.name 'campname'
                  FROM atc_isscampaigns
                  INNER JOIN atc_isscampaigns_cstm on atc_isscampaigns.id=atc_isscampaigns_cstm.id_c
                  INNER JOIN atc_isscampaigns_cp_client_users_1_c cc on atc_isscampaigns_cp_client_users_1atc_isscampaigns_ida = atc_isscampaigns.id AND cc.deleted = 0
                  WHERE (campaign_finish_date_c is NULL or campaign_finish_date_c = '') AND (campaign_start_date_c is NULL OR campaign_start_date_c != '')
                  AND cc.atc_isscampaigns_cp_client_users_1cp_client_users_idb IN ('".implode("','",session('users'))."') 
                  ORDER BY atc_isscampaigns.name";

        // fetch results
        $results = DB::select($query, [1]);

        // return response
        return $results;
    }

    /**
     * Fetch Target Lists depending on selected campaign (dropdown)
     *
     * @param $selected_campaign
     * @return mixed
     */
    public static function fetchLists($selected_campaign){

        $response = [];

        // build query
        $query = "SELECT prospect_lists.id 'listid', prospect_lists.name 'listname', prospect_lists_cstm.client_edit_disabled_c edit_disabled
                  FROM prospect_lists
                  LEFT JOIN prospect_lists_cstm on prospect_lists.id = prospect_lists_cstm.id_c
                  INNER JOIN atc_isscampaigns_prospectlists_1_c on prospect_lists.id = atc_isscampaigns_prospectlists_1prospectlists_idb
                  where atc_isscampaigns_prospectlists_1atc_isscampaigns_ida='{$selected_campaign}' and prospect_lists.deleted='0' ORDER BY prospect_lists.name";

        // fetch results
        $results = DB::select($query, [1]);

        // go through each result
        foreach($results as $result){

            // prepare response
            $response[] = [
                'list_id' => $result->listid,
                'list_name' => $result->listname,
                'edit_disabled' => $result->edit_disabled
            ];
        }

        // return response
        return $response;
    }

    /**
     * Fetches Contact information for current campaign
     *
     * @param $selected_campaign
     * @return mixed
     */
    public static function fetchContactInfo($selected_campaign){

        $query = "select users.id as 'id', first_name, last_name, phone_work,  email_address,  atc_clients_atc_isscampaignsatc_clients_ida as 'client_id'
                  from users
                  inner join users_atc_isscampaigns_1_c on users_atc_isscampaigns_1users_ida = users.id and users_atc_isscampaigns_1_c.deleted=0
                  inner join atc_isscampaigns on atc_isscampaigns.id = users_atc_isscampaigns_1atc_isscampaigns_idb
                  left join email_addr_bean_rel on bean_id = users.id AND email_addr_bean_rel.deleted=0 and email_addr_bean_rel.primary_address = '1'
                  left join email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id
                  left join atc_clients_atc_isscampaigns_c on atc_clients_atc_isscampaignsatc_isscampaigns_idb = atc_isscampaigns.id AND atc_clients_atc_isscampaigns_c.deleted=0
                  where atc_isscampaigns.id = '{$selected_campaign}' limit 1;";

        // fetch results
        $results = DB::select($query, [1]);

        // return response
        return reset($results);
    }

    /**
     * Fetches list data in DataTables format
     *
     * @param $selected_target_list
     * @param $filters
     * @return array
     */
    public static function getList($selected_target_list){
	$industry_list=array (
	  '' => '',
	  'aerospace and defense' => 'Aerospace and Defense',
	  'automotive' => 'Automotive',
	  'banks' => 'Banks',
	  'civic non profit and membership groups' => 'Civic, Non-Profit and Membership Groups',
	  'chemicals' => 'Chemicals',
	  'computer hardware' => 'Computer Hardware',
	  'computer software' => 'Computer Software',
	  'construction and building materials' => 'Construction and Building Materials',
	  'consumer product manufacturing' => 'Consumer Product Manufacturing',
	  'consumer services' => 'Consumer Services',
	  'corporate services' => 'Corporate Services',
	  'agriculture and forestry' => 'Agriculture and Forestry',
	  'electronics' => 'Electronics',
	  'energy and environmental' => 'Energy and Environmental',
	  'financial services' => 'Financial Services',
	  'food and beverage' => 'Food and Beverage',
	  'government' => 'Government',
	  'hospitals and healthcare' => 'Hospitals and Healthcare',
	  'holding companies' => 'Holding Companies',
	  'industrial manufacturing and services' => 'Industrial Manufacturing and Services',
	  'insurance' => 'Insurance',
	  'leisure sports and recreation' => 'Leisure, Sports and Recreation',
	  'media' => 'Media',
	  'mining and metals' => 'Mining and Metals',
	  'pharmaceuticals and biotechnology' => 'Pharmaceuticals and Biotechnology',
	  'real estate' => 'Real Estate',
	  'retail' => 'Retail',
	  'schools and education' => 'Schools and Education',
	  'telecommunications' => 'Telecommunications',
	  'transportation' => 'Transportation',
	  'banking' => 'Banking',
	  'not for profit' => 'Not for Profit',
	  'ComputersandElectronics' => 'Computers & Electronics',
	  'SoftwareandInternet' => 'Software & Internet',
	  'ArchitectureandEngineeringServices' => 'Architecture and Engineering Services',
	  'ConsumerProducts' => 'Consumer Products',
	  'ConsumerServices' => 'Consumer Services',
	  'BusinessServices' => 'Business Services',
	  'EnergyandUtilities' => 'Energy & Utilities',
	  'FinancialServices' => 'Financial Services',
	  'FoodandBeverage' => 'Food & Beverage',
	  'Manufacturing' => 'Manufacturing',
	  'TravelRecreationandLeisure' => 'Travel, Recreation & Leisure',
	  'MediaandEntertainment' => 'Media & Entertainment',
	  'HealthcarePharmaceuticalsandBiotech' => 'Healthcare, Pharmaceuticals & Biotech',
	  'RealEstateandConstruction' => 'Real Estate & Construction',
	  'Education' => 'Education',
	  'TransportationandStorage' => 'Transportation & Storage',
	  'Other' => 'Other',
	  'NetworkingEquipmentandSystems' => 'Networking Equipment & Systems',
	  'WholesaleandDistribution' => 'Wholesale & Distribution',
	);


        // init vars
        $response = [];

        // build query
        $query = "SELECT a.id, a.description, a.name, a.phone_office, a.name, a.employees, a.annual_revenue, billing_address_city, billing_address_state, billing_address_postalcode, industry, sub_industry_1_c, ac.contact_count_c, pc.client_edit_disabled_c, a.website
                  FROM accounts a
                  INNER JOIN accounts_cstm ac on a.id = ac.id_c
                  INNER JOIN prospect_lists_prospects plp on plp.related_id = a.id and plp.deleted=0 
	              INNER JOIN prospect_lists_cstm pc   on plp.prospect_list_id = pc.id_c
	              INNER JOIN prospect_lists pl on plp.prospect_list_id = pl.id and pl.deleted =0
	              WHERE plp.prospect_list_id = '{$selected_target_list}'";

        // fetch results
        $results = DB::select($query, [1]);

        // go through each result
        foreach($results as $result){

            // make sure that we have office phone
            if($result->phone_office != ''){

                // fetch area code
                $area_code = trim(preg_replace("/[^0-9]/", "", $result->phone_office));

                // make sure that areacode starts with 1
                if ($area_code[0] == '1'){

                    // set area code
                    $area = substr($area_code, 1);
                }
                else
                    // set area code
                    $area = substr($area_code, 0, 3);
            }
            else
                // do not have area code
                $area = "";

	    if (array_key_exists($result->industry,$industry_list)){
		$ind = $industry_list[$result->industry];
	    }
	    else{
		$ind = $result->industry;
	    }

            // build response
            $response[] = [
                "",
                $result->name,
                $area,
                $result->employees,
                $result->annual_revenue,
                $result->billing_address_city,
                $result->billing_address_state,
                $result->billing_address_postalcode,
                $ind,
                $result->sub_industry_1_c,
                $result->website,
                "<button id='$result->id' type='button' class='btn btn-info' data-toggle='tooltip' data-placement='left' title='{$result->description}'>ⓘ</button>"
            ];
        }

        //return response
        return $response;
    }

    /**
     * Removes relationship between accounts/contacts and target list
     *
     * @param $selected_target_list
     * @param $selected_accounts
     * @return bool
     */
    public static function removeAccounts($selected_target_list, $selected_accounts){

        // go through each selected record
        foreach($selected_accounts as $record_id)
        {
            // build query for accounts
            $account_query = "update prospect_lists_prospects set deleted = 1 where related_id = '{$record_id}' and related_type = 'Accounts' and prospect_list_id = '{$selected_target_list}'";

            // run query
            $result_acc = DB::select($account_query, [1]);

            // build query for contacts
            $contact_query = "update prospect_lists_prospects set deleted = 1 where related_id in (select contact_id from accounts_contacts where account_id = '{$record_id}' and deleted = 0) and related_type = 'Contacts' and prospect_list_id = '{$selected_target_list}'";

            // run query
            $result_con = DB::select($contact_query, [1]);
        }

        return true;
    }

    /**
     * Fetches filtered Accounts data
     *
     * @param $filters
     * @return array
     */
    public static function getAccountList($filters){



      $industry_list=array (
          '' => '',
          'aerospace and defense' => 'Aerospace and Defense',
          'automotive' => 'Automotive',
          'banks' => 'Banks',
          'civic non profit and membership groups' => 'Civic, Non-Profit and Membership Groups',
          'chemicals' => 'Chemicals',
          'computer hardware' => 'Computer Hardware',
          'computer software' => 'Computer Software',
          'construction and building materials' => 'Construction and Building Materials',
          'consumer product manufacturing' => 'Consumer Product Manufacturing',
          'consumer services' => 'Consumer Services',
          'corporate services' => 'Corporate Services',
          'agriculture and forestry' => 'Agriculture and Forestry',
          'electronics' => 'Electronics',
          'energy and environmental' => 'Energy and Environmental',
          'financial services' => 'Financial Services',
          'food and beverage' => 'Food and Beverage',
          'government' => 'Government',
          'hospitals and healthcare' => 'Hospitals and Healthcare',
          'holding companies' => 'Holding Companies',
          'industrial manufacturing and services' => 'Industrial Manufacturing and Services',
          'insurance' => 'Insurance',
          'leisure sports and recreation' => 'Leisure, Sports and Recreation',
          'media' => 'Media',
          'mining and metals' => 'Mining and Metals',
          'pharmaceuticals and biotechnology' => 'Pharmaceuticals and Biotechnology',
          'real estate' => 'Real Estate',
          'retail' => 'Retail',
          'schools and education' => 'Schools and Education',
          'telecommunications' => 'Telecommunications',
          'transportation' => 'Transportation',
          'banking' => 'Banking',
          'not for profit' => 'Not for Profit',
          'ComputersandElectronics' => 'Computers & Electronics',
          'SoftwareandInternet' => 'Software & Internet',
          'ArchitectureandEngineeringServices' => 'Architecture and Engineering Services',
          'ConsumerProducts' => 'Consumer Products',
          'ConsumerServices' => 'Consumer Services',
          'BusinessServices' => 'Business Services',
          'EnergyandUtilities' => 'Energy & Utilities',
          'FinancialServices' => 'Financial Services',
          'FoodandBeverage' => 'Food & Beverage',
          'Manufacturing' => 'Manufacturing',
          'TravelRecreationandLeisure' => 'Travel, Recreation & Leisure',
          'MediaandEntertainment' => 'Media & Entertainment',
          'HealthcarePharmaceuticalsandBiotech' => 'Healthcare, Pharmaceuticals & Biotech',
          'RealEstateandConstruction' => 'Real Estate & Construction',
          'Education' => 'Education',
          'TransportationandStorage' => 'Transportation & Storage',
          'Other' => 'Other',
          'NetworkingEquipmentandSystems' => 'Networking Equipment & Systems',
          'WholesaleandDistribution' => 'Wholesale & Distribution',
        );


        $response = [];

        // build sort column
        $sortcolumn = (!isset($filters['sortcolumn']) || $filters['sortcolumn'] == '') ? 'accounts.name' : $filters['sortcolumn'];

        // build sort direction
        $sortorder = (!isset($filters['sortdirection']) || $filters['sortdirection'] == '') ? 'ASC' : $filters['sortdirection'];

        $sortcolumn_text = ($sortcolumn == 'accounts.annual_revenue' || $sortcolumn == 'accounts.employees') ? "cast(replace(".$sortcolumn.",',','') as UNSIGNED)" : $sortcolumn;

        $sorticon = $sortorder == 'ASC' ? "&#9206;" : "&#9207;";

        $fts = $filters['fts'] == 1 ? '%' : '';

        $query = "SELECT id, name,description, phone_office,name, employees,annual_revenue, billing_address_city, billing_address_state, billing_address_postalcode, industry,sub_industry_1_c,contact_count_c, website";

        $total_query = "SELECT COUNT(accounts.id) as count";

        $condition = " FROM accounts
                    		INNER JOIN accounts_cstm on accounts.id = accounts_cstm.id_c
                    		WHERE accounts.deleted = 0";

        if (isset($filters['company_name']) && $filters['company_name'] != ''){

            $condition .=" AND name LIKE '".$fts.$filters['company_name']."%' ";
        }

        if (isset($filters['city']) && $filters['city'] != ''){
            $condition .=" AND billing_address_city LIKE '%".$filters['city']."%'";
        }

        if (isset($filters['state']) && $filters['state'] != ''){
            $condition .=" AND billing_address_state IN ('".implode("','",$filters['state'])."')";
        }

        if (isset($filters['zipcode']) && $filters['zipcode'] != ''){
            $condition .=" AND billing_address_postalcode LIKE '%".$filters['zipcode']."%'";
        }

        if (!empty($filters['industry']) && $filters['industry'] != '' && !empty($filters['industry'])){
            $condition .=" AND industry IN ('".implode("','",$filters['industry'])."')";
        }

        if (isset($filters['sub_industry']) && $filters['sub_industry'] != '' && !empty($filters['sub_industry'])){
            $condition .=" AND sub_industry_1_c IN ('".implode("','",$filters['sub_industry'])."')";
        }

        if (isset($filters['area_code']) && $filters['area_code'] != ''){
            $condition .=" AND phone_office LIKE '".$filters['area_code']."%'";
        }

        if (isset($filters['employees_min']) && $filters['employees_min'] != 0 && $filters['employees_min'] != ''){
            $condition.=" AND employees > ".$filters['employees_min'];
        }

        if (isset($filters['employees_max']) && $filters['employees_max'] != 0 && $filters['employees_max'] != ''){
            $condition.=" AND employees < ".$filters['employees_max'];
        }

        if (isset($filters['revenue_min']) && $filters['revenue_min'] != 0 && $filters['revenue_min'] != ''){
            $condition.=" AND annual_revenue >= ".$filters['revenue_min'];
        }

        if (isset($filters['revenue_max']) && $filters['revenue_max'] != 0 && $filters['revenue_max'] != ''){
            $condition.=" AND annual_revenue != 0 AND annual_revenue <= ".$filters['revenue_max'];
        }


        $condition.="
			AND accounts.id NOT IN (SELECT accounts.id
            FROM accounts
            INNER JOIN accounts_cstm on accounts.id = accounts_cstm.id_c
	    INNER JOIN prospect_lists_prospects on prospect_lists_prospects.related_id = accounts.id AND prospect_lists_prospects.deleted = 0
	    INNER JOIN prospect_lists on prospect_lists.id = prospect_lists_prospects.prospect_list_id and prospect_lists.deleted =0
            INNER JOIN atc_isscampaigns_prospectlists_1_c on atc_isscampaigns_prospectlists_1prospectlists_idb = prospect_lists.id AND atc_isscampaigns_prospectlists_1_c.deleted =0
            WHERE atc_isscampaigns_prospectlists_1atc_isscampaigns_ida = '{$filters['campaign']}')";

        $sort = " ORDER BY {$sortcolumn_text} {$sortorder} ";

        $query = $query.$condition.$sort;

        $total_query = $total_query.$condition;

        //echo $query;
        $all_query = "SELECT accounts.id as account_id ".$condition;

        // fetch results
        $results = DB::select($query, [1]);

        // go through each result
        foreach($results as $result){

            // make sure that we have office phone
            if($result->phone_office != ''){

                // fetch area code
                $area_code = trim(preg_replace("/[^0-9]/", "", $result->phone_office));

                // make sure that areacode starts with 1
                if (!empty($area_code) && $area_code[0] == '1'){

                    // set area code
                    $area = substr($area_code, 1);
                }
                else
                    // set area code
                    $area = substr($area_code, 0, 3);
            }
            else
                // do not have area code
                $area = "";
		//use labels instead of key values, where applicable.
	    if (array_key_exists($result->industry,$industry_list)){
                $ind = $industry_list[$result->industry];
            }
            else{
                $ind = $result->industry;
            }
            // build response
            $response[] = [
                "",
                $result->name,
                $area,
                $result->employees,
                $result->annual_revenue,
                $result->billing_address_city,
                $result->billing_address_state,
                $result->billing_address_postalcode,
                $ind,
                $result->sub_industry_1_c,
                $result->website,
                "<button id='$result->id' type='button' class='btn btn-info' data-toggle='tooltip' data-placement='left' title='{$result->description}'>ⓘ</button>"
            ];
        }

        //return response
        return $response;
    }

    /**
     * Creates new Target list record and relates it to campaign
     *
     * @param $new_list_data
     * @return array|bool|float|\Guzzle\Http\Message\RequestInterface|int|string
     */
    public static function createNewList($new_list_data){

        // init new rest
        $connector = new Rest();

        // log in to sugar
        $connector->setUrl(env('ISS_URL'))
            ->setUsername(env('ISS_API_USERNAME'))
            ->setPassword(env('ISS_API_PASSWORD'))
            ->setPlatform(env('ISS_API_PLATFORM'))
            ->connect();

        $prospect_list = $connector->postEndpoint('ProspectLists', [
            'name' => $new_list_data['listname'],
            'atc_isscampaigns_prospectlists_1atc_isscampaigns_ida' => $new_list_data['campaign_id'],
            'assigned_user_id' => $new_list_data['campaigndirector'],
            'atc_clients_id_c' => $new_list_data['client_id']
        ]);

        return $prospect_list;
    }

    /**
     * Locks target list to prevent any further changes
     *
     * @param $target_list_id
     * @return array|bool|float|\Guzzle\Http\Message\RequestInterface|int|string
     */
    public static function lockList($target_list_id){

        // init new rest
        $connector = new Rest();

//        try{

        // log in to sugar
        $connector->setUrl(env('ISS_URL'))
            ->setUsername(env('ISS_API_USERNAME'))
            ->setPassword(env('ISS_API_PASSWORD'))
            ->setPlatform(env('ISS_API_PLATFORM'))
            ->connect();

        $prospect_list = $connector->putEndpoint("ProspectLists/{$target_list_id}", [
            'client_edit_disabled_c' => 1
        ]);
//        }catch (\Exception $e){
//            return [];
//        }

        return $prospect_list;
    }

    /**
     * Adds selected accounts to selected target list
     *
     * @param $accounts_ids
     * @param $target_list_id
     * @return array
     */
    public static function addToList($accounts_ids, $target_list_id){

        // init vars
        $record_array = [];
        $selection_contactcount = 0;
        $max_records = 50000;
        $total_accounts = 0;

        // make sure that we have accounts
        if(!empty($accounts_ids)){
            // Loop to store and display values of individual checked checkbox.
            foreach($accounts_ids as $account_id){

                // build query
                $query = "SELECT count(contacts.id) as contact_count_c 
			              FROM accounts_contacts 
			              INNER JOIN accounts on accounts.id = accounts_contacts.account_id  AND accounts.deleted = 0  AND accounts_contacts.deleted = 0
			              INNER JOIN contacts on accounts_contacts.contact_id = contacts.id AND contacts.deleted = 0  
			              WHERE accounts.id='{$account_id}' 
			              LIMIT 1";

                // fetch results
                $results = DB::select($query, [1]);

                foreach($results as $result){
                    $record_array[$account_id] = $result->contact_count_c;
                    $selection_contactcount += intval($result->contact_count_c);
                }
            }
        }

        // build query for account count
        $existingaccountsq = "SELECT COUNT(*) as accountcount 
                              FROM prospect_lists_prospects 
                              WHERE prospect_list_id = '{$target_list_id}' 
                              AND deleted = 0 
                              AND related_type = 'Accounts'";

        // fetch results
        $ea_results = DB::select($existingaccountsq, [1]);

        // go through each record (we should allways get one result here)
        foreach($ea_results as $ea_result){
            $existing_accountcount = intval($ea_result->accountcount);
            $total_accounts = $existing_accountcount + count($accounts_ids);
        }

        if($selection_contactcount < $max_records){

            // go through each account
            foreach($record_array as $account => $count){

                // build query for fetchin uuid
                $generate_uuid_query = "SELECT UUID() as id";

                // fetch results
                $generate_uuid_results = DB::select($generate_uuid_query, [1]);

                // fetch uuid
                $id = $generate_uuid_results[0]->id;

                // build and execute querty to insert the Account.
                $account_insert_query = "REPLACE INTO prospect_lists_prospects VALUES( '{$id}','{$target_list_id}','{$account}','Accounts','".gmdate('Y-m-d H:i:s')."',0);";

                // fetch results
                $account_insert_results = DB::select($account_insert_query, [1]);

                //get list of contacts related to the account we just added.
                $contactquery = "SELECT contact_id 
                                 FROM accounts_contacts 
				                 INNER JOIN contacts on contacts.id = contact_id AND contacts.deleted = 0 
				                 WHERE account_id = '{$account}' 
				                 AND accounts_contacts.deleted = 0";

                // run query
                $contact_results = DB::select($contactquery, [1]);

                // go through each contact id
                foreach($contact_results as $contact_result){

                    // fetch results (generate new uuid)
                    $generate_uuid_results = DB::select($generate_uuid_query, [1]);

                    // set uuid
                    $unid = $generate_uuid_results[0]->id;

                    // build relationship query
                    $contact_insert_query = "REPLACE INTO prospect_lists_prospects VALUES('{$unid}','{$target_list_id}','{$contact_result->contact_id}','Contacts','".gmdate('Y-m-d H:i:s')."',0)";

                    // run relationship query
                    $contact_insert_results = DB::select($contact_insert_query, [1]);
                }
            }

            // number of selected accounts
            $accountcount = count($accounts_ids);

            // return response
            return ['selected_accounts' => $accountcount,
                    'total_accounts' => $total_accounts];
        }
        else{

            // return error msg
            return ['msg' => "Total number of allowable Contacts exceeded."];
        }
    }

    public static function deleteList($list_id){
        // init new rest
        $connector = new Rest();

        // log in to sugar
        $connector->setUrl(env('ISS_URL'))
            ->setUsername(env('ISS_API_USERNAME'))
            ->setPassword(env('ISS_API_PASSWORD'))
            ->setPlatform(env('ISS_API_PLATFORM'))
            ->connect();

        $prospect_list = $connector->delete('ProspectLists', $list_id);

        return true;
    }
}
