<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * Class Auth
 *
 * @package App\Models
 */
class Auth extends Model{

    public static $users = [];

    public static $logged_in = false;

    /**
     * Fetches user(s) from CRM DB
     *
     * @param array $credentials / Ex. ['username' => '<user name>', 'password' => '<user password>']
     * @return bool
     */
    public static function authenticate($credentials){
        // make sure that credentials are passed
        if(empty($credentials['username']) || empty($credentials['password']))
            return false;

        // build query
        $query = "SELECT username, password, users.id as user_id
			      FROM cp_client_users users
			      WHERE username = '".strtolower($credentials['username'])."'
			      AND password = '{$credentials['password']}'
			      AND deleted=0";

        // fetch results
        $results = DB::select($query, [1]);

        // go through each result
        foreach($results as $user_data){

            // append user id
            self::$users[] = $user_data->user_id;
        }

        // make sure that we have results
        if(count($results) > 0){
            // set log in flag
            self::$logged_in = true;

            // save users ids to session
            session(['users' => self::$users]);

            // save session logged in flag
            session(['logged_in' => self::$logged_in]);
        }

        return self::$logged_in;
    }

    /**
     * Logs out from system
     */
    public static function logOut(){
        session(['logged_in'=> false]);

        return redirect('login');
    }

    /**
     * Fetches ATC_IssCampaigns for logged in user
     *
     * @return mixed
     */
    public static function fetchCampaigns(){
        $campaigns = [];

        // build query
        $query = "SELECT distinct atc_isscampaigns.id, name
                  FROM atc_isscampaigns
                  INNER JOIN atc_isscampaigns_cstm ISC ON ISC.id_c = atc_isscampaigns.id and atc_isscampaigns.deleted = 0
                  INNER JOIN atc_isscampaigns_cp_client_users_1_c cc on atc_isscampaigns_cp_client_users_1atc_isscampaigns_ida = atc_isscampaigns.id AND cc.deleted = 0
                  WHERE cc.atc_isscampaigns_cp_client_users_1cp_client_users_idb IN ('".implode("','",session('users'))."')
                  AND campaign_start_date_c IS NOT NULL
                  AND campaign_start_date_c != ''
                  AND (campaign_finish_date_c > date_sub(now(), INTERVAL 9 MONTH) OR campaign_finish_date_c = '' OR campaign_finish_date_c is null)  
                  ORDER BY name";

        // fetch results
        $results = DB::select($query, [1]);

        // go through each result
        foreach($results as $campaign_data){
            $campaigns[] = $campaign_data->id;
        }

        // add campaigns to sesion
        session(['campaigns' => $campaigns]);

        // return results
        return $results;
    }

    /**
     * Fetches ATC_ClientSalesReps for logged in user
     *
     * @param $campaigns
     * @return mixed
     */
    public static function fetchSalesReps($campaigns = []){
        $camp_where = '';

        if(!empty($campaigns)){
            foreach($campaigns as $campaign){

                if(is_object($campaign)){
                    $campaigns_arr[] = $campaign->id;
                }
                else{
                    $campaigns_arr[] = $campaign;
                }

            }
            $camp_where = "AND atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('".implode("','",$campaigns_arr)."')";
        }

        $salesreps = [];

        // build query
        $query = "SELECT DISTINCT atc_clientsalesreps.id, first_name, last_name 
                  FROM atc_clientsalesreps 
                  INNER JOIN atc_clientsalesreps_atc_appointments_c ON atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida = atc_clientsalesreps.id AND atc_clientsalesreps_atc_appointments_c.deleted = 0 
                  INNER JOIN atc_isscampaigns_atc_appointments_c ON atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_clientsalesreps_atc_appointmentsatc_appointments_idb AND atc_isscampaigns_atc_appointments_c.deleted = 0 
                  {$camp_where}
                  ORDER BY last_name";

        // fetch results
        $results = DB::select($query, [1]);

        // go through each result
        foreach($results as $salesreps_data){
            $salesreps[] = $salesreps_data->id;
        }

        // add campaigns to sesion
        session(['salesreps' => $salesreps]);

        // return results
        return $results;
    }

    /**
     * Fetches activity data for report view
     *
     * @param array $selected_campaigns
     * @param array $selected_salesreps
     * @return mixed
     */
    public static function fetchActivities($selected_campaigns, $selected_salesreps){

        // build query
        $query = "SELECT SUM(CASE WHEN calls_cstm.call_outcome_c = 'Call' THEN 1 ELSE 0 END) As num_call
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Not Interested' THEN 1 ELSE 0 END) As num_not_interested
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Left company' THEN 1 ELSE 0 END) As num_left
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Admin Blocking' THEN 1 ELSE 0 END) As num_admin
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Lead Generated' THEN 1 ELSE 0 END) As num_lead
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Long term call back' THEN 1 ELSE 0 END) As num_long_call
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Hot call back' THEN 1 ELSE 0 END) As num_hot
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Contact_Sales_Rep' THEN 1 ELSE 0 END) As Contact_Sales_Rep
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Referral To' THEN 1 ELSE 0 END) As num_ref_to
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Reschedule' THEN 1 ELSE 0 END) As num_reschedule
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Cancelled' THEN 1 ELSE 0 END) As num_cancelled
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'WrongContact' THEN 1 ELSE 0 END) As num_wrong
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'WrongNumber' THEN 1 ELSE 0 END) As num_number
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Booked Colleague' THEN 1 ELSE 0 END) As num_colleague
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Invite_Sent' THEN 1 ELSE 0 END) As invite_sent
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'suggestedcolleague' THEN 1 ELSE 0 END) As num_suggested
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Existing_Customer' THEN 1 ELSE 0 END) As num_existing
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Does_Not_Qualify' THEN 1 ELSE 0 END) As num_dnq
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Outsourced' THEN 1 ELSE 0 END) As num_outsourced
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'EmailandVM1' THEN 1 ELSE 0 END) As email_vm1
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'EmailandVM2' THEN 1 ELSE 0 END) As email_vm2
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'EmailandVM3' THEN 1 ELSE 0 END) As email_vm3
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'EmailandVM4' THEN 1 ELSE 0 END) As email_vm4
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'New_Contact' THEN 1 ELSE 0 END) As new_contact
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Short_Term_Call_Back' THEN 1 ELSE 0 END) As short_term_call_back
                  , SUM(CASE WHEN calls_cstm.call_outcome_c <> '' THEN 1 ELSE 0 END) As total_calls
                  , SUM(CASE WHEN Month(calls.date_entered) = Month(curdate()) AND Year(calls.date_entered) = Year(curdate()) THEN 1 ELSE 0 END) As num_month
                  , SUM(CASE WHEN Month(calls.date_entered) = Month(Date_Sub(curdate(),interval 1 month)) AND Year(calls.date_entered) = Year(Date_Sub(curdate(),interval 1 month)) THEN 1 ELSE 0 END) As num_last
                  , SUM(CASE WHEN calls_cstm.call_outcome_c <> '' THEN 1 ELSE 0 END)/(PERIOD_DIFF(Date_Format(Max(calls.date_entered),'%Y%m'),Date_Format(Min(calls.date_entered),'%Y%m'))+1) As AvgCalls
                  FROM calls INNER JOIN calls_cstm ON calls.id = calls_cstm.id_c
                  INNER JOIN atc_isscampaigns_calls_1_c cc
                  ON cc.atc_isscampaigns_calls_1calls_idb = calls.id
                  WHERE cc.atc_isscampaigns_calls_1atc_isscampaigns_ida IN('".implode("','", $selected_campaigns)."')
                  AND (calls.date_modified > date('') or '' = '') 
                  AND (calls.date_modified <= date('') or '' = '') 
                  AND calls.date_entered <> ''
                  ORDER BY calls.date_entered ASC";

        // fetch results
        $results = DB::select($query, [1]);

        // return
        $response = self::translatePieChart(reset($results));

        $response['as_ta'] = self::fetchSnapshot($selected_campaigns, $selected_salesreps);

        $response['campaign_results'] = self::fetchCampaignResults($selected_campaigns);

        return $response;
    }

    /**
     * Helper method for formating data for Highcharts Pie chart
     *
     * @param $pie_data
     * @return array
     */
    private static function translatePieChart($pie_data){

        // pie chart labels
        $labels = [
            'num_call' => 'Call',
            'num_not_interested' => 'Not Interested',
            'num_left' => 'Left company',
            'num_admin' => 'Admin Blocking',
            'num_lead' => 'Lead Generated',
            'num_long_call' => 'Long term call back',
            'num_hot' => 'Hot call back',
            'Contact_Sales_Rep' => 'Contact Sales Rep',
            'num_ref_to' => 'Referral To',
            'num_reschedule' => 'Reschedule',
            'num_cancelled' => 'Cancelled',
            'num_wrong' => 'Wrong Contact',
            'num_number' => 'Wrong Number',
            'num_colleague' => 'Booked Colleague',
            'invite_sent' => 'Email&Callback',
            'num_suggested' => 'Suggested Colleague',
            'num_existing' => 'Existing Customer',
            'num_dnq' => 'Does Not Qualify',
            'num_outsourced' => 'Outsourced',
            'email_vm1' => 'Email and VM1',
            'email_vm2' => 'Email and VM2',
            'email_vm3' => 'Email and VM3',
            'email_vm4' => 'Email and VM4',
            'new_contact' => 'New Contact',
            'short_term_call_back' => 'Short Term Call Back'
        ];

        $response = [];

        // go through each pie chart data
        foreach($pie_data as $key => $value){

            // make sure that value is > 0
            if(intval($value) > 0 && array_key_exists($key, $labels)){

                // build pie data
                $response['pie_data'][] = ['name' => $labels[$key], 'y' => intval($value)];
            }
            else {
                // build table data
                $response['table_data'][$key] = floatval($value);
            }
        }

        // return
        return $response;
    }

    /**
     * Fetches snapshoot data
     *
     * @param $selected_campaigns
     * @param $selected_salesreps
     * @return array
     */
    public static function fetchSnapshot($selected_campaigns, $selected_salesreps){
        $sl_query = empty($selected_salesreps) ? '' : "AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN ('".implode("','", $selected_salesreps)."')";

        $query = "SELECT SUM(CASE WHEN appointment_status = 'Cancelled' THEN 1 ELSE 0 END) AS num_cancelled
                 , SUM(CASE WHEN appointment_status = 'Attended' OR appointment_status = 'Attended_Policy' THEN 1 ELSE 0 END) AS num_attended
                 , SUM(CASE WHEN appointment_status = 'Accepted' THEN 1 ELSE 0 END) AS num_accepted
                 , SUM(CASE WHEN appointment_status = 'Reschedule' THEN 1 ELSE 0 END) AS num_rescheduled
                 , SUM(CASE WHEN atc_appointments.date_entered >= '2017-03-26' THEN 1 ELSE 0 END) AS appointments_last_week
                 , SUM(CASE WHEN appointment_date >= '2017-03-26' and (appointment_status='Attended' OR appointment_status = 'Attended_Policy') THEN 1 ELSE 0 END) AS attended_last_week
                 , SUM(CASE WHEN atc_appointments.deleted='0' THEN 1 ELSE 0 END) AS total_generated
                 , SUM(CASE WHEN (appointment_status='Attended' OR appointment_status = 'Attended_Policy') and atc_appointments.deleted='0'  THEN 1 ELSE 0 END) AS total_attended
                 , SUM(CASE WHEN appointment_status = 'Confirmed' THEN 1 ELSE 0 END) AS num_confirmed
                 , SUM(CASE WHEN appointment_status IN ('Attended','Attended_Policy','Cancelled','Accepted','Reschedule','Confirmed') THEN 1 ELSE 0 END) AS num_appointments
                 , SUM(CASE WHEN Month(atc_appointments.appointment_date) = Month(curdate()) AND Year(atc_appointments.appointment_date) = Year(curdate()) THEN 1 ELSE 0 END) As num_month
                 , SUM(CASE WHEN Month(atc_appointments.appointment_date) = Month(Date_Sub(curdate(),interval 1 month)) AND Year(atc_appointments.appointment_date) = Year(Date_Sub(curdate(),interval 1 month)) THEN 1 ELSE 0 END) As num_last
                 , Date_Format(Max(atc_appointments.appointment_date),'%Y%m') AS MaxDate
                 , Date_Format(Min(atc_appointments.appointment_date),'%Y%m') AS MinDate
                 , camp.name AS CampName
                 FROM atc_appointments INNER JOIN atc_isscampaigns_atc_appointments_c ca
                 ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                 INNER JOIN atc_isscampaigns camp
                 ON camp.id = ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida
                 LEFT JOIN prospectlists_atc_appointments_1_c ta
                 ON ta.prospectlists_atc_appointments_1atc_appointments_idb = atc_appointments.id
                 LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                 WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('".implode("','", $selected_campaigns)."')
                 and atc_appointments.deleted = 0
                 and ca.deleted = 0
                 and camp.deleted = 0
                 and ta.deleted = 0
                 and sa.deleted = 0
                 {$sl_query}
                 GROUP BY camp.name";

        // fetch results
        $results = DB::select($query, [1]);

        $response = [
            'app_agg_array' => $results,
            'num_appointments' => 0,
            'num_app_month' => 0,
            'num_app_last' => 0,
            'num_app_max' => 0,
            'num_app_min' => 999999,
            'accepted_total' => 0,
            'rescheduled_total' => 0,
            'attended_total' => 0,
            'cancelled_total' => 0,
            'confirmed_total' => 0,
            'new_appointments_last_week' => 0,
            'attended_last_week' => 0,
            'total_generated' => 0,
            'total_attended' => 0,
            'barchart' => []
        ];

        // go through each row
        foreach($results as $result){
            $response['num_appointments'] += intval($result->num_appointments);
            $response['num_app_month'] += intval($result->num_month);
            $response['num_app_last'] += intval($result->num_last);
            $response['num_app_max'] = ($result->MaxDate > $response['num_app_max'] ? $result->MaxDate : $response['num_app_max']);
            $response['num_app_min'] = ($result->MinDate < $response['num_app_min'] ? $result->MaxDate : $response['num_app_min']);
            $response['accepted_total'] += intval($result->num_accepted);
            $response['rescheduled_total'] += intval($result->num_rescheduled);
            $response['attended_total'] += intval($result->num_attended);
            $response['cancelled_total'] += intval($result->num_cancelled);
            $response['confirmed_total'] += intval($result->num_confirmed);
            $response['new_appointments_last_week'] += intval($result->appointments_last_week);
            $response['attended_last_week'] += intval($result->attended_last_week);
            $response['total_generated'] += intval($result->total_generated);
            $response['total_attended'] += intval($result->total_attended);
            $response['barchar'][] = ['name' => $result->CampName, 'data' => [
                intval($result->num_accepted),
                intval($result->num_rescheduled),
                intval($result->num_attended),
                intval($result->num_cancelled),
                intval($result->num_confirmed)
            ]];
        }

        if(count($selected_campaigns) == 1){
            $campaign_target_query = "SELECT appointment_target_campaign_c 
                                  FROM atc_isscampaigns_cstm 
                                  WHERE id_c = '{$selected_campaigns[0]}' 
                                  LIMIT 1";

            // fetch results
            $ct_results = DB::select($campaign_target_query, [1]);

            $response['campaign_target'] = empty($ct_results[0]->appointment_target_campaign_c) ? 0 : $ct_results[0]->appointment_target_campaign_c;
        }
        else{
            $response['campaign_target'] = "Select a single list to view Target.";
        }

        $response['num_appointments'] = $response['num_appointments'] - $response['cancelled_total'];

        $response['total_generated'] = $response['total_generated'] - $response['cancelled_total'];

        $response['num_avg_apps'] = $response['num_appointments']/($response['num_app_max']-$response['num_app_min']+1);

        // return
        return $response;
    }

    public static function fetchCampaignResults($selected_campaigns){
        $query = "SELECT cstm.appointment_result_c as apr,
                  SUM(CASE WHEN atc_appointments.opportunity_amount = 'No_Result' THEN 1 ELSE 0 END) AS nr,
                  SUM(CASE WHEN atc_appointments.opportunity_amount = '35' THEN 1 ELSE 0 END) AS n35,
                  SUM(CASE WHEN atc_appointments.opportunity_amount = '75' THEN 1 ELSE 0 END) AS n75,
                  SUM(CASE WHEN atc_appointments.opportunity_amount = '150' THEN 1 ELSE 0 END) AS n150,
                  SUM(CASE WHEN atc_appointments.opportunity_amount = '150_Plus' THEN 1 ELSE 0 END) AS n150_Plus,
                  SUM(CASE WHEN atc_appointments.opportunity_amount = '400' THEN 1 ELSE 0 END) AS n400,
                  SUM(CASE WHEN atc_appointments.opportunity_amount = '1m' THEN 1 ELSE 0 END) AS n1m
                  FROM atc_appointments INNER JOIN atc_isscampaigns_atc_appointments_c ca
                  ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  INNER JOIN atc_clientsalesreps_atc_appointments_c sa
                  ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_appointments_cstm cstm
                  ON cstm.id_c = atc_appointments.id
                  WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('".implode("','", $selected_campaigns)."')
                  AND atc_appointments.deleted = 0
                  and ca.deleted = 0
                  and sa.deleted = 0
                  AND (appointment_status = 'Attended' OR appointment_status ='Attended_Policy' OR appointment_status = 'Confirmed')
                  AND cstm.appointment_result_c IN('NoResult','SixMonths','TwelveMonths','Longer')   GROUP BY cstm.appointment_result_c
                  ORDER BY cstm.appointment_result_c;";

        // fetch results
        $results = DB::select($query, [1]);

        $labels = [
            'NoResult' => 'Awaiting Timeline / Response Submitted',
            'SixMonths' => 'Within 6 Months',
            'TwelveMonths' => 'Within 6 to 12 Months',
            'Longer' => 'Greater than 12 Months'
        ];

        // go through each result
        foreach($results as $res){
            $response[$res->apr] = [
                'nr' => intval($res->nr),
                '35' => intval($res->n35),
                '75' => intval($res->n75),
                '150' => intval($res->n150),
                '150_Plus' => intval($res->n150_Plus),
                '400' => intval($res->n400),
                '1m' => intval($res->n1m)
            ];
        }

        $total_count = 0;

        // go through each response
        foreach($response as $key => $data){

            // special rule for this for some reason TODO: ask mastersolve!
            if($key == 'TwelveMonths' || $key == 'Longer')
                $total_count += array_sum($data);

            $final_data[$labels[$key]] = [
                    'Qty' => array_sum($data),
                    'Cumulative Value' => $data['35']*17500 + $data['75']*55000 + $data['150']*112500 + $data['150_Plus']*275000 + $data['400']*700000 + $data['1m']*1000000,
                    'Total Count' => ($key == 'TwelveMonths' || $key == 'Longer') ? $total_count : array_sum($data),
                ];
        }

        foreach($labels as $value){
            if(!isset($final_data[$value])){
                $final_data[$value] = [
                    'Qty' => 0,
                    'Cumulative Value' => 0,
                    'Total Count' => 0
                ];
            }
        }

        if(!empty($final_data)){
            $final_data['chart_data'] = [
                intval($final_data['Awaiting Timeline / Response Submitted']['Cumulative Value']),
                (intval($final_data['Awaiting Timeline / Response Submitted']['Cumulative Value']) + intval($final_data['Within 6 Months']['Cumulative Value'])),
                (intval($final_data['Awaiting Timeline / Response Submitted']['Cumulative Value']) + intval($final_data['Within 6 Months']['Cumulative Value']) + intval($final_data['Within 6 to 12 Months']['Cumulative Value'])),
                (intval($final_data['Awaiting Timeline / Response Submitted']['Cumulative Value']) + intval($final_data['Within 6 Months']['Cumulative Value']) + intval($final_data['Within 6 to 12 Months']['Cumulative Value']) + intval($final_data['Greater than 12 Months']['Cumulative Value']))
            ];
        }

        return $final_data;
    }
}
