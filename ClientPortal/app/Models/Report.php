<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Spinegar\Sugar7Wrapper\Rest;
use Carbon\Carbon;
use DateTimeZone;
use DateTime;

/**
 * Class Report
 *
 * @package App\Models
 */
class Report extends Model
{

    /**
     * Fetches ATC_IssCampaigns for logged in user
     *
     * @param string $campaign_status
     * @return mixed
     */
    public static function fetchCampaigns($campaign_status = '')
    {
        $campaigns = [];

        if ($campaign_status == 'active') {

            $camp_status_query = " AND (campaign_finish_date_c IS null OR campaign_finish_date_c = '') ";

        } elseif ($campaign_status == 'completed') {

            $camp_status_query = "AND campaign_finish_date_c IS NOT NULL AND campaign_finish_date_c != '' ";

        } elseif ($campaign_status == 'event') {

            $camp_status_query = "AND campaign_type_c = 'event campaign'";

        } else {

            $camp_status_query = "";
        }

        // build query
        $query = "SELECT distinct atc_isscampaigns.id, name
                  FROM atc_isscampaigns
                  INNER JOIN atc_isscampaigns_cstm ISC ON ISC.id_c = atc_isscampaigns.id and atc_isscampaigns.deleted = 0
                  INNER JOIN atc_isscampaigns_cp_client_users_1_c cc on atc_isscampaigns_cp_client_users_1atc_isscampaigns_ida = atc_isscampaigns.id AND cc.deleted = 0
                  WHERE cc.atc_isscampaigns_cp_client_users_1cp_client_users_idb IN ('" . implode("','",
                session('users')) . "')
                  AND campaign_start_date_c IS NOT NULL
                  AND campaign_start_date_c != ''
                  AND (campaign_finish_date_c > date_sub(now(), INTERVAL 9 MONTH) OR campaign_finish_date_c = '' OR campaign_finish_date_c is null)
                  {$camp_status_query}
                  ORDER BY name";

        // fetch results
        $results = DB::select($query, [1]);

        // go through each result
        foreach ($results as $campaign_data) {
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
    public static function fetchSalesReps($campaigns = [])
    {

        // init custom where statement
        $camp_where = '';

        // make sure that we have passed campaigns
        if (!empty($campaigns)) {

            // go through each campaign
            foreach ($campaigns as $campaign) {

                // make sure that campaign is an object
                if (is_object($campaign)) {

                    // append campaign id to campaigns array
                    $campaigns_arr[] = $campaign->id;
                } else {

                    // append campaign id to campaigns array
                    $campaigns_arr[] = $campaign;
                }

            }

            // build custom where
            $camp_where = "AND atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','",
                    $campaigns_arr) . "')";
        } else {
            $camp_where = "WHERE 1=0 ";
        }

        // init salesreps container
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
        foreach ($results as $salesreps_data) {

            // append salesreps id to the container
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
    public static function fetchActivities($selected_campaigns, $selected_salesreps)
    {

        // build query
        $query = "SELECT SUM(CASE WHEN calls_cstm.call_outcome_c = 'Call' THEN 1 ELSE 0 END) As num_call
                  , SUM(CASE WHEN calls_cstm.call_outcome_c IN ('Convo_Not_Interested_Genuinely','Convo_Not_Interested_Did_Not_Listen') THEN 1 ELSE 0 END) As num_not_interested
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Left company' THEN 1 ELSE 0 END) As num_left
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Admin Blocking' THEN 1 ELSE 0 END) As num_admin
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Convo_Appointment_Generated' THEN 1 ELSE 0 END) As num_lead
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Long term call back' THEN 1 ELSE 0 END) As num_long_call
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Hot call back' THEN 1 ELSE 0 END) As num_hot
				  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Event Registration' THEN 1 ELSE 0 END) As num_event
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Referral To' THEN 1 ELSE 0 END) As num_ref_to
 
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Cancelled' THEN 1 ELSE 0 END) As num_cancelled
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'WrongContact' THEN 1 ELSE 0 END) As num_wrong


     
                  , SUM(CASE WHEN calls_cstm.call_outcome_c IN ('Suggested Colleague','Admin_Suggested_Colleague') THEN 1 ELSE 0 END) As num_suggested
                  , SUM(CASE WHEN calls_cstm.call_outcome_c IN ('Existing_Customer','Does_Not_Qualify') THEN 1 ELSE 0 END) As num_dnq
               
             
                  , SUM(CASE WHEN calls_cstm.call_outcome_c IN ('Email and VM','Convo_Sent_Email_Follow_Up') THEN 1 ELSE 0 END) As email_vm1

          
                  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Short_Term_Call_Back' THEN 1 ELSE 0 END) As short_term_call_back
                  , SUM(CASE WHEN calls_cstm.call_outcome_c <> '' THEN 1 ELSE 0 END) As total_calls
                  , SUM(CASE WHEN Month(calls.date_entered) = Month(curdate()) AND Year(calls.date_entered) = Year(curdate()) THEN 1 ELSE 0 END) As num_month
                  , SUM(CASE WHEN Month(calls.date_entered) = Month(Date_Sub(curdate(),interval 1 month)) AND Year(calls.date_entered) = Year(Date_Sub(curdate(),interval 1 month)) THEN 1 ELSE 0 END) As num_last
                  , SUM(CASE WHEN calls_cstm.call_outcome_c <> '' THEN 1 ELSE 0 END)/(PERIOD_DIFF(Date_Format(Max(calls.date_entered),'%Y%m'),Date_Format(Min(calls.date_entered),'%Y%m'))+1) As AvgCalls
                  FROM calls INNER JOIN calls_cstm ON calls.id = calls_cstm.id_c
                  INNER JOIN atc_isscampaigns_calls_1_c cc
                  ON cc.atc_isscampaigns_calls_1calls_idb = calls.id
                  WHERE cc.atc_isscampaigns_calls_1atc_isscampaigns_ida IN('" . implode("','", $selected_campaigns) . "')
                  AND (calls.date_modified > date('') or '' = '') AND (calls.date_modified <= date('') or '' = '')
                  AND calls.date_entered <> ''
                  ORDER BY calls.date_entered ASC";
				  // Used to be in query, leaving it here in case we need it in the future.
//  , SUM(CASE WHEN calls_cstm.call_outcome_c = 'Reschedule' THEN 1 ELSE 0 END) As num_reschedule
        // fetch results
        $results = DB::select($query, [1]);

        // fetch piechart data
        $response = self::translatePieChart(reset($results));

        // fetch snapshot data
        $response['as_ta'] = self::fetchSnapshot($selected_campaigns, $selected_salesreps);

        // fetch campaigns data
        $response['campaign_results'] = self::fetchCampaignResults($selected_campaigns, $selected_salesreps);

        // return response
        return $response;
    }

    /**
     * Retrieves activity details
     * (additional info about an activity graph slice)
     *
     * @param $campaign_ids
     * @param $salesreps_ids
     * @param $call_outcome
     * @return array
     */
    public static function activityDetails($campaign_ids, $salesreps_ids, $call_outcome)
    {

        $label_arr = Array(
            'Call' => 'Call',
            'Referral To' => 'Referral To',
            'suggestedcolleague' => 'Suggested Colleague',
            'Hot call back' => 'Hot call back',
			'Event Registration' => 'Event Registration',
            'EmailandVM1' => 'Emailed, Following Up',
          //  'EmailandVM2' => 'Email & VM2',
       //     'EmailandVM3' => 'Email & VM3',
       //     'EmailandVM4' => 'Email & Call Back',
        //    'New_Contact' => 'New Contact',
            'Long term call back' => 'Long term call back',
            'Short_Term_Call_Back' => 'Short Term Call Back',
      //      'Invite_Sent' => 'Invite Sent',
            'Convo_Appointment_Generated' => 'Appointment Generated',
       //     'Reschedule' => 'Reschedule',
            'Cancelled' => 'Cancelled',
            'Not Interested' => 'Not Interested',
            'WrongContact' => 'Wrong Contact',
            'Admin Blocking' => 'Admin Blocking',
         //   'Bookedcolleague' => 'Booked Colleague',
            'Left company' => 'Left company',
       //     'WrongNumber' => 'Wrong number',
          //  'Existing_Customer' => 'Does Not Qualify',
            'Does_Not_Qualify' => 'Does Not Qualify',
      //      'Outsourced' => 'Does Not Qualify',
          //  'Contact_Sales_Rep' => 'Contact Sales Rep',
     //       'Remove_from_TL' => 'Remove Co. from TL'
        );

        $label_arr = array_flip($label_arr);
        $tcall_outcome = $label_arr[$call_outcome];

        // introduce the SQL query
        // (try to retrieve contacts' and accounts' details for all calls with certain status)
        $query = "
            SELECT contacts.title, accounts.name, accounts.billing_address_city, accounts.billing_address_state
            FROM contacts
                INNER JOIN calls_contacts on contacts.id = calls_contacts.contact_id
                INNER JOIN calls_cstm on calls_cstm.id_c = calls_contacts.call_id
                INNER JOIN accounts_contacts on accounts_contacts.contact_id = contacts.id
                INNER JOIN accounts on accounts_contacts.account_id = accounts.id
                INNER JOIN atc_isscampaigns_calls_1_c cc ON cc.atc_isscampaigns_calls_1calls_idb = calls_cstm.id_c

            WHERE calls_cstm.call_outcome_c = '$tcall_outcome'
                AND cc.atc_isscampaigns_calls_1atc_isscampaigns_ida IN ('" . implode("','", $campaign_ids) . "')
                AND contacts.deleted = 0
                AND calls_contacts.deleted = 0
                AND accounts_contacts.deleted = 0
                AND accounts.deleted = 0
                AND cc.deleted = 0

            GROUP BY contacts.title
        ";

        // fetch results
        $results = DB::select($query, [1]);

        // return response
        return $results;
    }

    /**
     * Retrieves positive appointment details
     * (similar to fetchPositiveAppointments, but with related contact's details)
     *
     * @param $campaign_ids
     * @param $salesreps_ids
     * @param $appointment_outcome
     * @return array|mixed
     */
    public static function heldAppointmentDetails($campaign_ids, $salesreps_ids, $appointment_outcome)
    {
        // introduce the possible appointment outcomes
        $labels = array(
            'Awaiting Timeline' => 'NoResult',
            'Within 6 Months' => 'SixMonths',
            '6-12 Months' => 'TwelveMonths',
            'Greater than 12 Months' => 'Longer'
        );

        // introduce the appointment outcome label
        $appointment_outcome_label = $labels[$appointment_outcome];

        // make sure that passed $appointment_outcome is valid
        if (!$appointment_outcome_label) {
            return response()->json([
                'success' => false,
                'message' => 'Passed appointment outcome is not valid. Please refresh the page and try again.'
            ]);
        }

        $salesrepfilter = empty($salesreps_ids) ? " " : " AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                $salesreps_ids) . "') ";

        // build query
        $query = "
            SELECT atc_appointments.name 'appointment_number', con.first_name, con.last_name, con.title, acc.name, acc.billing_address_state
            FROM atc_appointments
                  INNER JOIN atc_isscampaigns_atc_appointments_c ca ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_appointments_cstm cstm ON cstm.id_c = atc_appointments.id
                  LEFT JOIN atc_appointments_contacts_c ac ON ac.atc_appointments_contactsatc_appointments_idb = atc_appointments.id and ac.deleted = 0
                  LEFT JOIN contacts con ON ac.atc_appointments_contactscontacts_ida = con.id and con.deleted = 0
                  LEFT JOIN accounts_contacts accon ON accon.contact_id = con.id and accon.deleted = 0 and accon.primary_account = 1   
                  LEFT JOIN accounts acc ON accon.account_id = acc.id and acc.deleted = 0
            WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','", $campaign_ids) . "')
                  {$salesrepfilter}
                  AND cstm.appointment_result_c = '{$appointment_outcome_label}'
                  AND atc_appointments.deleted = 0
                  AND atc_appointments.appointment_status != 'Cancelled_ISS'
                  AND ca.deleted = 0
                  AND sa.deleted = 0
                  AND (appointment_status = 'Attended' OR appointment_status ='Attended_Policy' OR appointment_status = 'DC_Appt_Attended' OR appointment_status = 'Confirmed')
                ";

        // fetch results
        $results = DB::select($query, [1]);

        // return response
        return $results;
    }

    /**
     * Retrieves positive appointment details
     * (similar to fetchPositiveAppointments, but with related contact's details)
     *
     * @param $campaign_ids
     * @param $salesreps_ids
     * @param $appointment_outcome
     * @return array|mixed
     */
    public static function positiveAppointmentDetails($campaign_ids, $salesreps_ids, $appointment_outcome)
    {
        // introduce the possible appointment outcomes
        $labels = array(
            'Awaiting Feedback' => 'No_Response',
            'Good Future Prospect' => 'A_Good_introductory_meeting_that_may_offer_future_possibilities',
            'Foot in the Door' => 'B_Positive_step_in_terms_of_getting_your_foot_in_the_door',
            'Redirected to Decision Maker' => 'C_Positive_because_the_contact_directed_us_to_another_decision_maker',
            'Not Worth While' => 'D_Negative_Not_worth_my_time',
        );

        // introduce the appointment outcome label
        $appointment_outcome_label = $labels[$appointment_outcome];

        // make sure that passed $appointment_outcome is valid
        if (!$appointment_outcome_label) {
            return response()->json([
                'success' => false,
                'message' => 'Passed appointment outcome is not valid. Please refresh the page and try again.'
            ]);
        }

        $salesrepfilter = empty($salesreps_ids) ? " " : " AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                $salesreps_ids) . "') ";

        // build query
        $query = "
            SELECT atc_appointments.name as 'appointment_number', con.first_name, con.last_name, con.title, acc.name, acc.billing_address_state
            FROM atc_appointments
                  INNER JOIN atc_isscampaigns_atc_appointments_c ca ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_appointments_cstm cstm ON cstm.id_c = atc_appointments.id
                  LEFT JOIN atc_appointments_contacts_c ac ON ac.atc_appointments_contactsatc_appointments_idb = atc_appointments.id and ac.deleted = 0
                  LEFT JOIN contacts con ON ac.atc_appointments_contactscontacts_ida = con.id and con.deleted = 0
                  LEFT JOIN accounts_contacts accon ON accon.contact_id = con.id and accon.deleted = 0 and accon.primary_account = 1   
                  LEFT JOIN accounts acc ON accon.account_id = acc.id and acc.deleted = 0
	          WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','", $campaign_ids) . "')
                  {$salesrepfilter}
                  AND cstm.positive_appointment_c = '{$appointment_outcome_label}'
                  AND atc_appointments.deleted = 0
                  AND atc_appointments.appointment_status != 'Cancelled_ISS'
                  AND ca.deleted = 0
                  AND sa.deleted = 0
                  AND (appointment_status = 'Attended' OR appointment_status ='Attended_Policy' OR appointment_status = 'DC_Appt_Attended' OR appointment_status = 'Confirmed')
                ";

        // fetch results
        $results = DB::select($query, [1]);

        // return response
        return $results;
    }

    /**
     * Retrieves positive appointment details
     * (similar to fetchPositiveAppointments, but with related contact's details)
     *
     * @param $campaign_ids
     * @param $salesreps_ids
     * @param $appointment_outcome
     * @return array|mixed
     */
    public static function secondAppointmentDetails($campaign_ids, $salesreps_ids, $appointment_outcome)
    {
        // introduce the possible appointment outcomes
        $labels = array('Awaiting Feedback', 'Yes', 'No');

        // make sure that passed $appointment_outcome is valid
        if (!in_array($appointment_outcome, $labels)) {

            return response()->json([
                'success' => false,
                'message' => 'Passed appointment outcome is not valid. Please refresh the page and try again.'
            ]);
        }

        $salesrepfilter = empty($salesreps_ids) ? " " : " AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                $salesreps_ids) . "') ";

        //
        $second_appointment_rule = $appointment_outcome == 'Awaiting Feedback' ? " 'No_Result' or cstm.second_appointment_c = '' OR cstm.second_appointment_c IS NULL" : "'{$appointment_outcome}'";

        // build query
        $query = "
            SELECT atc_appointments.name as 'appointment_number', con.first_name, con.last_name, con.title, acc.name, acc.billing_address_state
            FROM atc_appointments
                  INNER JOIN atc_isscampaigns_atc_appointments_c ca ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_appointments_cstm cstm ON cstm.id_c = atc_appointments.id
                  LEFT JOIN atc_appointments_contacts_c ac ON ac.atc_appointments_contactsatc_appointments_idb = atc_appointments.id and ac.deleted = 0
                  LEFT JOIN contacts con ON ac.atc_appointments_contactscontacts_ida = con.id and con.deleted = 0
                  LEFT JOIN accounts_contacts accon ON accon.contact_id = con.id and accon.deleted = 0 and accon.primary_account = 1   
                  LEFT JOIN accounts acc ON accon.account_id = acc.id and acc.deleted = 0
                  WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','", $campaign_ids) . "')
                  {$salesrepfilter}
                  AND (cstm.second_appointment_c = {$second_appointment_rule})
                  AND atc_appointments.deleted = 0
                  AND atc_appointments.appointment_status != 'Cancelled_ISS'
                  AND ca.deleted = 0
                  AND sa.deleted = 0
                  AND (appointment_status = 'Attended' OR appointment_status ='Attended_Policy' OR appointment_status = 'DC_Appt_Attended' OR appointment_status = 'Confirmed')
                ";

        // fetch results
        $results = DB::select($query, [1]);

        // return response
        return $results;
    }

    /**
     * Retrieves positive appointment details
     * (similar to fetchPositiveAppointments, but with related contact's details)
     *
     * @param $campaign_ids
     * @param $salesreps_ids
     * @param $value_range
     * @return array|mixed
     */
    public static function maxValueDetails($campaign_ids, $salesreps_ids, $value_range)
    {
        // introduce the possible appointment outcomes
        $labels = array(
            '$0 - $35k' => '35',
            '$35k - $75k' => '75',
            '$75k - $150k' => '150',
            '$150k - $400k' => '150_Plus',
            '$400k - $1M' => '400',
            '$1M +' => '1m',
        );

        // introduce the appointment outcome label
        $opportunity_amount_label = $labels[$value_range];

        // make sure that passed $appointment_outcome is valid
        if (!$opportunity_amount_label) {

            return response()->json([
                'success' => false,
                'message' => 'Passed appointment outcome is not valid. Please refresh the page and try again.'
            ]);
        }

        $salesrepfilter = empty($salesreps_ids) ? " " : " AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                $salesreps_ids) . "') ";

        // build query
        $query = "
            SELECT atc_appointments.name as 'appointment_number', con.first_name, con.last_name, con.title, acc.name, acc.billing_address_state
            FROM atc_appointments
                  INNER JOIN atc_isscampaigns_atc_appointments_c ca ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_appointments_cstm cstm ON cstm.id_c = atc_appointments.id
                  LEFT JOIN atc_appointments_contacts_c ac ON ac.atc_appointments_contactsatc_appointments_idb = atc_appointments.id and ac.deleted = 0
                  LEFT JOIN contacts con ON ac.atc_appointments_contactscontacts_ida = con.id and con.deleted = 0
                  LEFT JOIN accounts_contacts accon ON accon.contact_id = con.id and accon.deleted = 0 and accon.primary_account = 1   
                  LEFT JOIN accounts acc ON accon.account_id = acc.id and acc.deleted = 0 
            WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','", $campaign_ids) . "')
                  {$salesrepfilter}
                  AND atc_appointments.opportunity_amount = '{$opportunity_amount_label}'
                  AND atc_appointments.deleted = 0
                  AND atc_appointments.appointment_status != 'Cancelled_ISS'
                  AND ca.deleted = 0
                  AND sa.deleted = 0
                  AND (appointment_status = 'Attended' OR appointment_status ='Attended_Policy' OR appointment_status = 'DC_Appt_Attended' OR appointment_status = 'Confirmed')
                ";

        // fetch results
        $results = DB::select($query, [1]);

        // return response
        return $results;
    }


    /**
     * Retrieves total appointment details
     *
     * @param $campaign_ids
     * @param $salesreps_ids
     * @param $status (appointment status)
     * @return array|mixed
     */
    public static function totalAppointmentsDetails($campaign_ids, $salesreps_ids, $status)
    {

        $statuses_labels = array(
            'Upcoming' => "('Accepted','DC_Appt_Accepted')",
            'Reschedule' => "('Reschedule')",
            'Attended' => "('Attended','Attended_Policy','DC_Appt_Attended')",
            'Cancelled' => "('Cancelled')",
            'Confirmed' => "('Confirmed')"
        );

        // introduce the status(es)
        $statuses = $statuses_labels[$status];

        // make sure that passed $appointment_outcome is valid
        if (!$statuses) {

            return response()->json([
                'success' => false,
                'message' => 'Passed appointment status is not valid. Please refresh the page and try again.'
            ]);
        }

        $salesrepfilter = empty($salesreps_ids) ? " " : " AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                $salesreps_ids) . "') ";

        // build query
        $query = "
            SELECT atc_appointments.id as appid, atc_appointments.appointment_number, atc_appointments.appointment_date, con.first_name as con_first, con.last_name as con_last, atc_clientsalesreps.first_name as sales_first, atc_clientsalesreps.last_name as sales_last, acc.name as account_name
            FROM atc_appointments
                  INNER JOIN atc_isscampaigns_atc_appointments_c ca ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_clientsalesreps ON sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida = atc_clientsalesreps.id
                  LEFT JOIN atc_appointments_cstm cstm ON cstm.id_c = atc_appointments.id
                  LEFT JOIN atc_appointments_contacts_c ac ON ac.atc_appointments_contactsatc_appointments_idb = atc_appointments.id and ac.deleted = 0
                  LEFT JOIN contacts con ON ac.atc_appointments_contactscontacts_ida = con.id and con.deleted = 0
                  LEFT JOIN accounts_contacts accon ON accon.contact_id = con.id and accon.deleted = 0 and accon.primary_account = 1   
                  LEFT JOIN accounts acc ON accon.account_id = acc.id and acc.deleted = 0 
            WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','", $campaign_ids) . "')
                  {$salesrepfilter}
                  AND atc_appointments.appointment_status IN {$statuses}
                  AND atc_appointments.deleted = 0
                  AND ca.deleted = 0
                  AND sa.deleted = 0
                ";

        // fetch results
        $results = DB::select($query, [1]);

        // return response
        return $results;
    }

    /**
     * Helper method for formating data for Highcharts Pie chart
     *
     * @param $pie_data
     * @return array
     */
    private static function translatePieChart($pie_data)
    {

        // pie chart labels
        $labels = [
            'num_call' => 'Call',
            'num_not_interested' => 'Not Interested',
            'num_left' => 'Left company',
            'num_admin' => 'Admin Blocking',
            'num_lead' => 'Appointment Generated',
            'num_long_call' => 'Long term call back',
            'num_hot' => 'Hot call back',
			'num_event' => "Event Registration",
      //      'Contact_Sales_Rep' => 'Contact Sales Rep',
            'num_ref_to' => 'Referral To',
          //  'num_reschedule' => 'Reschedule',
            'num_cancelled' => 'Cancelled',
            'num_wrong' => 'Wrong Contact',
       //     'num_number' => 'Wrong Number',
         //   'num_colleague' => 'Booked Colleague',
          //  'invite_sent' => 'Email&Callback',
            'num_suggested' => 'Suggested Colleague',
         //   'num_existing' => 'Does Not Qualify',
            'num_dnq' => 'Does Not Qualify',
        //    'num_outsourced' => 'Does Not Qualify',
            'email_vm1' => 'Emailed, Following Up',

      //      'new_contact' => 'New Contact',
            'short_term_call_back' => 'Short Term Call Back'
        ];

        $response = [];

        // go through each pie chart data
        foreach ($pie_data as $key => $value) {

            // make sure that value is > 0
            if (intval($value) > 0 && array_key_exists($key, $labels)) {

                // build pie data
                $response['pie_data'][] = ['name' => $labels[$key], 'y' => intval($value)];
            } else {
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
    public static function fetchSnapshot($selected_campaigns, $selected_salesreps)
    {
        // build custom where
        $sl_query = empty($selected_salesreps) ? '' : "AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN ('" . implode("','",
                $selected_salesreps) . "')";

        $last_7 = date('Y-m-d', strtotime('-7 days'));

        // build query
        $query = "SELECT SUM(CASE WHEN appointment_status = 'Cancelled' THEN 1 ELSE 0 END) AS num_cancelled
                 , SUM(CASE WHEN appointment_status IN ('Attended','Attended_Policy','DC_Appt_Attended') THEN 1 ELSE 0 END) AS num_attended
                 , SUM(CASE WHEN appointment_status IN ('Accepted','DC_Appt_Accepted') THEN 1 ELSE 0 END) AS num_accepted
                 , SUM(CASE WHEN appointment_status = 'Reschedule' THEN 1 ELSE 0 END) AS num_rescheduled
                 , SUM(CASE WHEN atc_appointments.date_entered >= '" . $last_7 . "' THEN 1 ELSE 0 END) AS appointments_last_week
                 , SUM(CASE WHEN appointment_date >= '" . $last_7 . "' and appointment_status IN ('Attended','Attended_Policy','DC_Appt_Attended') THEN 1 ELSE 0 END) AS attended_last_week
                 , SUM(CASE WHEN atc_appointments.deleted='0' THEN 1 ELSE 0 END) AS total_generated
                 , SUM(CASE WHEN appointment_status IN ('Attended','Attended_Policy','DC_Appt_Attended') and atc_appointments.deleted='0' THEN 1 ELSE 0 END) AS total_attended
                 , SUM(CASE WHEN appointment_status = 'Confirmed' THEN 1 ELSE 0 END) AS num_confirmed
                 , SUM(CASE WHEN appointment_status IN ('Attended','Attended_Policy','DC_Appt_Attended','Cancelled','Accepted','DC_Appt_Accepted','Reschedule','Confirmed') THEN 1 ELSE 0 END) AS num_appointments
                 , SUM(CASE WHEN Month(atc_appointments.appointment_date) = Month(curdate()) AND Year(atc_appointments.appointment_date) = Year(curdate()) THEN 1 ELSE 0 END) As num_month
                 , SUM(CASE WHEN Month(atc_appointments.appointment_date) = Month(Date_Sub(curdate(),interval 1 month)) AND Year(atc_appointments.appointment_date) = Year(Date_Sub(curdate(),interval 1 month)) THEN 1 ELSE 0 END) As num_last
                 , Date_Format(Max(atc_appointments.appointment_date),'%Y%m') AS MaxDate
                 , Date_Format(Min(atc_appointments.appointment_date),'%Y%m') AS MinDate
                 , camp.name AS CampName
                 FROM atc_appointments INNER JOIN atc_isscampaigns_atc_appointments_c ca
                 ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                 INNER JOIN atc_isscampaigns camp
                 ON camp.id = ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida
                 LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                 WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','",
                $selected_campaigns) . "')
                 and atc_appointments.deleted = 0
                 and atc_appointments.appointment_status != 'Cancelled_ISS'
                 and ca.deleted = 0
                 and camp.deleted = 0
                 and sa.deleted = 0
                 {$sl_query}
                 GROUP BY camp.name
        ";

        // fetch anointment results
        $results = DB::select($query, [1]);

        // build response
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

        // go through each anointment
        foreach ($results as $result) {
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
            $response['barchar'][] = [
                'name' => $result->CampName,
                'data' => [
                    intval($result->num_accepted),
                    intval($result->num_rescheduled),
                    intval($result->num_attended),
                    intval($result->num_cancelled),
                    intval($result->num_confirmed)
                ]
            ];
        }

        // make sure that only one campaign was selected
        if (count($selected_campaigns) == 1) {

            // build query
            $campaign_target_query = "SELECT appointment_target_campaign_c
                                  FROM atc_isscampaigns_cstm
                                  WHERE id_c = '{$selected_campaigns[0]}'
                                  LIMIT 1";

            // fetch results
            $ct_results = DB::select($campaign_target_query, [1]);

            // add data to response
            $response['campaign_target'] = empty($ct_results[0]->appointment_target_campaign_c) ? 0 : $ct_results[0]->appointment_target_campaign_c;
        } else {
            // display info msg
            $response['campaign_target'] = "Select a single list to view Target.";
        }

        // recalculate number of appointments
        $response['num_appointments'] = $response['num_appointments'] - $response['cancelled_total'];

        // recalculate total generated
        $response['total_generated'] = $response['total_generated'] - $response['cancelled_total'];

        // recalculate average number
        $response['num_avg_apps'] = $response['num_appointments'] / ($response['num_app_max'] - $response['num_app_min'] + 1);

        // return response
        return $response;
    }

    /**
     * Builds data for PieChart
     *
     * @param $selected_campaigns
     * @param $selected_salesreps
     * @return mixed
     */
    public static function fetchCampaignResults($selected_campaigns, $selected_salesreps)
    {

        $app_filter = '';
        $month_three = date("Y-m-", strtotime("-3 months")) . '01';
        $week_start = date("Y-m-d H:i:s", strtotime('monday this week'));

        $final_data = [];

        if (empty($selected_salesreps)) {
            $salesrepfilter = "";
        } else {
            $salesrepfilter = " AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                    $selected_salesreps) . "') ";
        }
        //cumulative query:
        // build query
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
                  WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','",
                $selected_campaigns) . "')
                  AND atc_appointments.deleted = 0
                  AND atc_appointments.appointment_status != 'Cancelled_ISS'
                  AND ca.deleted = 0
                  AND sa.deleted = 0
                  AND (appointment_status = 'Attended' OR appointment_status ='Attended_Policy' OR appointment_status = 'DC_Appt_Attended' OR appointment_status = 'Confirmed')
                  AND cstm.appointment_result_c IN('NoResult','SixMonths','TwelveMonths','Longer')
                  {$salesrepfilter}
                  {$app_filter}
                  GROUP BY cstm.appointment_result_c
                  ORDER BY cstm.appointment_result_c;";

        // fetch results
        $results = DB::select($query, [1]);

        // init labels for chart
        $labels = [
            'NoResult' => 'Awaiting Timeline / Response Submitted',
            'SixMonths' => 'Within 6 Months',
            'TwelveMonths' => 'Within 6 to 12 Months',
            'Longer' => 'Greater than 12 Months'
        ];

        // go through each result
        foreach ($results as $res) {

            // generate chart response
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

        // init counter
        $total_count = 0;

        // make sure that we have response
        if (isset($response)) {

            // go through each response
            foreach ($response as $key => $data) {

                // special rule for this for some reason
                if ($key == 'TwelveMonths' || $key == 'Longer') {
                    $total_count += array_sum($data);
                }

                $final_data[$labels[$key]] = [
                    'Qty' => array_sum($data),
                    'Cumulative Value' => $data['35'] * 17500 + $data['75'] * 55000 + $data['150'] * 112500 + $data['150_Plus'] * 275000 + $data['400'] * 700000 + $data['1m'] * 1000000,
                    'Total Count' => ($key == 'TwelveMonths' || $key == 'Longer') ? $total_count : array_sum($data),
                ];
            }
        }
        if (empty($final_data['Within 6 Months']['Cumulative Value'])) {
            $final_data['Within 6 Months']['Cumulative Value'] = 0;
        }
        if (empty($final_data['Within 6 to 12 Months']['Cumulative Value'])) {
            $final_data['Within 6 to 12 Months']['Cumulative Value'] = 0;
        }
        if (empty($final_data['Greater than 12 Months']['Cumulative Value'])) {
            $final_data['Greater than 12 Months']['Cumulative Value'] = 0;
        }
        if (empty($final_data['Greater than 12 Months']['Total Count'])) {
            $final_data['Greater than 12 Months']['Total Count'] = 0;
        }
        if (empty($final_data['Greater than 12 Months']['Qty'])) {
            $final_data['Greater than 12 Months']['Qty'] = 0;
        }
        if (empty($final_data['Within 6 to 12 Months']['Qty'])) {
            $final_data['Within 6 to 12 Months']['Qty'] = 0;
        }
        if (empty($final_data['Within 6 Months']['Qty'])) {
            $final_data['Within 6 Months']['Qty'] = 0;
        }
        if (empty($final_data['Awaiting Timeline / Response Submitted']['Cumulative Value'])) {
            $final_data['Awaiting Timeline / Response Submitted']['Cumulative Value'] = 0;
        }

        //inal_data['Within 6 Months']['Cumulative Value'] += $final_data['Awaiting Timeline / Response Submitted']['Cumulative Value'];
        $final_data['Within 6 to 12 Months']['Cumulative Value'] += $final_data['Within 6 Months']['Cumulative Value'];
        $final_data['Greater than 12 Months']['Cumulative Value'] += $final_data['Within 6 to 12 Months']['Cumulative Value'];
        $final_data['Within 6 to 12 Months']['Total Count'] = $final_data['Within 6 Months']['Qty'] + $final_data['Within 6 to 12 Months']['Qty'];
        $final_data['Greater than 12 Months']['Total Count'] = $final_data['Greater than 12 Months']['Qty'] + $final_data['Within 6 to 12 Months']['Qty'] + $final_data['Within 6 Months']['Qty'];

        // go through each label
        foreach ($labels as $value) {

            // make sure that final data for current label is not set
            if (!isset($final_data[$value])) {

                // set final data for current label
                $final_data[$value] = [
                    'Qty' => 0,
                    'Cumulative Value' => 0,
                    'Total Count' => 0
                ];
            }
        }

        // make sure that final data is not empty
        if (!empty($final_data)) {

            // add chart data

            $final_data['chart_data'] = [
                //intval($final_data['Awaiting Timeline / Response Submitted']['Cumulative Value']),
                intval($final_data['Within 6 Months']['Cumulative Value']),
                intval($final_data['Within 6 to 12 Months']['Cumulative Value']),
                intval($final_data['Greater than 12 Months']['Cumulative Value'])
            ];

        }

        $mappings = [

            '35' => '0-35k',
            '75' => '35k - 75k',
            '150' => '75k-150k',
            '150_Plus' => '150k - 400k',
            '400' => '400k - 1m',
            '1m' => '1m +',
        ];

        // make sure that we have response
        if (!empty($response)) {

            // go through mapping
            foreach ($mappings as $label_key => $label_value) {

                $sec_val = 0;

                foreach ($response as $key => $values) {

                    $sec_val += $values[$label_key];
                }

                $final_data['max_value_pie_chart_data'][] = [
                    $label_value,
                    $sec_val
                ];

                $final_data['max_value_bar_chart_data'][] = [
                    $sec_val
                ];
            }
        }

        // return response
        return $final_data;
    }

    /**
     * Fetches data for campaign results charts
     *
     * @param $campaigns
     * @param $salesreps
     * @return array
     */
    public static function fetchPositiveAppointments($campaigns, $salesreps)
    {

        // init response
        $response = [];

        if (empty($salesreps)) {
            $salesrepfilter = " ";
        } else {
            $salesrepfilter = " AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                    $salesreps) . "') ";
        }

        // build query
        $query = "SELECT
                  SUM(CASE WHEN appointment_status = 'Attended' OR appointment_status = 'Attended_Policy' OR  appointment_status = 'DC_Appt_Attended' OR appointment_status = 'Confirmed' THEN 1 ELSE 0 END) AS num_held,
                  SUM(CASE WHEN cstm.appointment_result_c = 'NoResult' or cstm.appointment_result_c = '' THEN 1 ELSE 0 END) AS NumNo,
                  SUM(CASE WHEN cstm.appointment_result_c = 'SixMonths' THEN 1 ELSE 0 END) AS NumSix,
                  SUM(CASE WHEN cstm.appointment_result_c = 'TwelveMonths' THEN 1 ELSE 0 END) AS NumTwelve,
                  SUM(CASE WHEN cstm.appointment_result_c = 'Longer' THEN 1 ELSE 0 END) AS NumLonger,
                  SUM(CASE WHEN cstm.positive_appointment_c = 'No_Result' OR cstm.positive_appointment_c = '' OR cstm.positive_appointment_c IS NULL THEN 1 ELSE 0 END) AS no_result_count_positive,
                  SUM(CASE WHEN cstm.positive_appointment_c = 'A_Good_introductory_meeting_that_may_offer_future_possibilities' THEN 1 ELSE 0 END) a_good_future_count_positive,
		          SUM(CASE WHEN cstm.positive_appointment_c = 'B_Positive_step_in_terms_of_getting_your_foot_in_the_door' THEN 1 ELSE 0 END) b_positive_foot_count_positive,
    	          SUM(CASE WHEN cstm.positive_appointment_c = 'C_Positive_because_the_contact_directed_us_to_another_decision_maker' THEN 1 ELSE 0 END) c_positive_decision_count_positive,
    	          SUM(CASE WHEN cstm.positive_appointment_c = 'D_Negative_Not_worth_my_time' THEN 1 ELSE 0 END) d_negative_time_count_positive,
                  SUM(CASE WHEN cstm.second_appointment_c = 'No_Result' or cstm.second_appointment_c = '' OR cstm.second_appointment_c IS NULL THEN 1 ELSE 0 END) AS no_result_count_second,
                  SUM(CASE WHEN cstm.second_appointment_c = 'Yes' THEN 1 ELSE 0 END) AS yes_count_second,
                  SUM(CASE WHEN cstm.second_appointment_c = 'No' THEN 1 ELSE 0 END) no_count_second,
                  SUM(CASE WHEN Month(atc_appointments.appointment_date) = Month(curdate()) AND Year(atc_appointments.appointment_date) = Year(curdate()) AND (appointment_status = 'Attended' OR appointment_status = 'Attended_Policy' OR appointment_status = 'DC_Appt_Attended')  THEN 1 ELSE 0 END) As num_month,
                  SUM(CASE WHEN Month(atc_appointments.appointment_date) = Month(Date_Sub(curdate(),interval 1 month)) AND Year(atc_appointments.appointment_date) = Year(Date_Sub(curdate(),interval 1 month)) AND (appointment_status = 'Attended' OR appointment_status = 'Attended_Policy' OR appointment_status = 'DC_Appt_Attended')  THEN 1 ELSE 0 END) As num_last,
                  SUM(CASE WHEN appointment_status = 'Attended' OR appointment_status = 'Attended_Policy' OR appointment_status = 'DC_Appt_Attended' OR appointment_status = 'Confirmed'   THEN 1 ELSE 0 END)/(PERIOD_DIFF(Date_Format(Max(atc_appointments.appointment_date),'%Y%m'),Date_Format(Min(atc_appointments.appointment_date),'%Y%m'))+1) As AvgHeld
                  FROM atc_appointments
                  INNER JOIN atc_isscampaigns_atc_appointments_c ca ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id
                  LEFT JOIN atc_appointments_cstm cstm ON cstm.id_c = atc_appointments.id
                  WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN ('" . implode("','", $campaigns) . "')
                  {$salesrepfilter}
                  AND atc_appointments.deleted = 0
                  AND atc_appointments.appointment_status != 'Cancelled_ISS'
                  AND ca.deleted = 0
                  AND sa.deleted = 0
                  AND (appointment_status = 'Attended' OR appointment_status ='Attended_Policy' OR appointment_status = 'DC_Appt_Attended' OR appointment_status = 'Confirmed')";

        // fetch results
        $results = DB::select($query, [1]);

        $results = reset($results);

        // data for first piechart
        $app_campaign_positive_array_results = [
            intval($results->no_result_count_positive),
            intval($results->a_good_future_count_positive),
            intval($results->b_positive_foot_count_positive),
            intval($results->c_positive_decision_count_positive),
            intval($results->d_negative_time_count_positive)
        ];

        // data for positive appointment pie chart
        if ($app_campaign_positive_array_results[0] > 0) {
            $response['positive_pie_chart_data'][] = ['Awaiting Feedback', $app_campaign_positive_array_results[0]];
        }
        if ($app_campaign_positive_array_results[1] > 0) {
            $response['positive_pie_chart_data'][] = ['Good Future Prospect', $app_campaign_positive_array_results[1]];
        }
        if ($app_campaign_positive_array_results[2] > 0) {
            $response['positive_pie_chart_data'][] = ['Foot in the Door', $app_campaign_positive_array_results[2]];
        }
        if ($app_campaign_positive_array_results[3] > 0) {
            $response['positive_pie_chart_data'][] = [
                'Redirected to Decision Maker',
                $app_campaign_positive_array_results[3]
            ];
        }
        if ($app_campaign_positive_array_results[4] > 0) {
            $response['positive_pie_chart_data'][] = ['Not Worth While', $app_campaign_positive_array_results[4]];
        }

        // data for positive appointment bar chart
        $response['positive_bar_chart_data'][] = ['Awaiting Feedback', $app_campaign_positive_array_results[0]];
        $response['positive_bar_chart_data'][] = ['Good Future Prospect', $app_campaign_positive_array_results[1]];
        $response['positive_bar_chart_data'][] = ['Foot in the Door', $app_campaign_positive_array_results[2]];
        $response['positive_bar_chart_data'][] = [
            'Redirected to Decision Maker',
            $app_campaign_positive_array_results[3]
        ];
        $response['positive_bar_chart_data'][] = ['Not Worth While', $app_campaign_positive_array_results[4]];

        // data for second appointment bar chart
        $app_campaign_second_array_results = [
            intval($results->no_result_count_second),
            intval($results->yes_count_second),
            intval($results->no_count_second)
        ];

        // second positive pie chart data
        if ($app_campaign_second_array_results[0] > 0) {
            $response['second_positive_pie_chart_data'][] = [
                'Awaiting Feedback',
                $app_campaign_second_array_results[0]
            ];
        }
        if ($app_campaign_second_array_results[1] > 0) {
            $response['second_positive_pie_chart_data'][] = ['Yes', $app_campaign_second_array_results[1]];
        }
        if ($app_campaign_second_array_results[2] > 0) {
            $response['second_positive_pie_chart_data'][] = ['No', $app_campaign_second_array_results[2]];
        }

        // second appointment bar chart data
        $response['second_positive_bar_chart_data'] = [
            $app_campaign_second_array_results[0],
            $app_campaign_second_array_results[1],
            $app_campaign_second_array_results[2]
        ];

        // table data for positive and second appointment table
        $response['table_data']['num_held'] = empty($results->num_held) ? 0 : round($results->num_held, 1);
        $response['table_data']['num_held_month'] = empty($results->num_month) ? 0 : round($results->num_month, 1);
        $response['table_data']['num_held_last'] = empty($results->num_last) ? 0 : round($results->num_last, 1);
        $response['table_data']['num_held_avg'] = empty($results->AvgHeld) ? 0 : round($results->AvgHeld, 1);

        // data for appointments held charts
        $app_cht_results = [
            intval($results->NumNo),
            intval($results->NumSix),
            intval($results->NumTwelve),
            intval($results->NumLonger)
        ];

        // appointments held pie chart data
        if ($app_cht_results[0] > 0) {
            $response['appointments_held_pie_chart_data'][] = ['Awaiting Timeline', $app_cht_results[0]];
        }
        if ($app_cht_results[1] > 0) {
            $response['appointments_held_pie_chart_data'][] = ['Within 6 Months', $app_cht_results[1]];
        }
        if ($app_cht_results[2] > 0) {
            $response['appointments_held_pie_chart_data'][] = ['6-12 Months', $app_cht_results[2]];
        }
        if ($app_cht_results[3] > 0) {
            $response['appointments_held_pie_chart_data'][] = ['Greater than 12 Months', $app_cht_results[3]];
        }

        // appointments held bar chart data
        $response['appointments_held_bar_chart_data'] = [
            $app_cht_results[0],
            $app_cht_results[1],
            $app_cht_results[2],
            $app_cht_results[3]
        ];

        // return data
        return $response;
    }

    /**
     * Fetches data for Appointments list view
     *
     * @param $selected_campaigns
     * @param $selected_salesreps
     * @param string $appointment_date_filter
     * @param string $date_created_filter
     * @return array
     */
    public static function fetchAppointments(
        $selected_campaigns,
        $selected_salesreps,
        $appointment_date_filter = '',
        $date_created_filter = ''
    ) {

        $res = [];
        $res['table_data'] = [];
        $total_attended = 0;
        $total_appointments = 0;

        // build salesreps filter
        if (empty($selected_salesreps)) {
            $salesrepfilter = "";
        } else {
            $salesrepfilter = "AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                    $selected_salesreps) . "') ";
        }

        // retrieve all times needed for filtering
        $time = self::getTimes();

        /**
         * Build 'Condition filter'
         */
		 // Change back to Last 7, currently using field for testing. 
        if ($appointment_date_filter == "today") {
            $condition = "AND atc_appointments.appointment_date >= '{$time['today']}' and atc_appointments.appointment_date <= '{$time['end_of_today']}'";
		} elseif ($appointment_date_filter == "tomorrow") {
            $condition = "AND atc_appointments.appointment_date > '{$time['tomorrow']}' and atc_appointments.appointment_date <= '{$time['end_of_tomorrow']}'";
		} elseif ($appointment_date_filter == "yesterday") {
            $condition = "AND atc_appointments.appointment_date > '{$time['yesterday']}' and atc_appointments.appointment_date <= '{$time['end_of_yesterday']}'";				
		} elseif ($appointment_date_filter == "last_7_days") {
            $condition = "AND atc_appointments.appointment_date > '{$time['last_7']}' and atc_appointments.appointment_date <= '{$time['now_time']}'";	
        } elseif ($appointment_date_filter == "last_30_days") {
            $condition = "AND atc_appointments.appointment_date > '{$time['last_30']}' and atc_appointments.appointment_date <= '{$time['now_time']}'";
        } elseif ($appointment_date_filter == "this_month") {
            $condition = "AND atc_appointments.appointment_date > '{$time['this_month_start']}' and atc_appointments.appointment_date <= '{$time['this_month_end']}'";
        } elseif ($appointment_date_filter == "last_month") {
            $condition = "AND atc_appointments.appointment_date > '{$time['last_month_start']}' and atc_appointments.appointment_date <= '{$time['last_month_end']}'";
        } elseif ($appointment_date_filter == "future") {
            $condition = "AND atc_appointments.appointment_date > '{$time['now_time']}' AND atc_appointments.appointment_status not in ('Attended', 'DC_Appt_Attended','Attended_Policy') ";
        } else {
            $condition = '';
        }

        // build date filter query
		// Steven Heath 7/29/2019: Added today and yesterday to the create_date (Tomorrow excluded). 
		if ($date_created_filter == "today") {
            $created_condition = "AND atc_appointments.date_entered >= '{$time['today']}' and atc_appointments.date_entered <= '{$time['end_of_today']}'";
		}elseif ($date_created_filter == "yesterday") {
			$created_condition = "AND atc_appointments.date_entered >= '{$time['yesterday']}' and atc_appointments.date_entered <= '{$time['end_of_yesterday']}'";		
		}elseif ($date_created_filter == "last_7_days") {
            $created_condition = "AND atc_appointments.date_entered  > '{$time['last_7']}' and atc_appointments.date_entered <= '{$time['now_time']}'";
        } elseif ($date_created_filter == "last_30_days") {
            $created_condition = "AND atc_appointments.date_entered  > '{$time['last_30']}' and atc_appointments.date_entered <= '{$time['now_time']}'";
        } elseif ($date_created_filter == "this_month") {
            $created_condition = "AND atc_appointments.date_entered  > '{$time['this_month_start_time']}' and atc_appointments.date_entered <= '{$time['this_month_end_time']}'";
        } elseif ($date_created_filter == "last_month") {
            $created_condition = "AND atc_appointments.date_entered  > '{$time['last_month_start_time']}' and atc_appointments.date_entered <= '{$time['last_month_end_time']}'";
        } else {
            $created_condition = '';
        }

        // build query
		// Steven 10/28/2019 Adding second_appointment_C to table 
        $app_query = "SELECT distinct appointment_number, appointment_status, atc_appointments.id, appointment_date, appointment_place,atc_appointments.description, atc_appointments.date_entered,
                      atc_appointments.opportunity_amount, second_appointment_c, rep.first_name as rep_first, rep.last_name as rep_last, accounts.name as acc_name, con.first_name as con_first,
                      con.last_name as con_last, con.title, con.phone_other, con.phone_work, contact_email, con.primary_address_street, con.primary_address_city, atc_appointments_cstm.appointment_result_c,
                      con.primary_address_state, con.primary_address_postalcode, con.primary_address_country, concat(ise.first_name, ' ',ise.last_name) As ISE, concat(accman.first_name, ' ', accman.last_name) As Acc_Man, atc_isscampaigns.name as 'campaign_name',
                      second_appointment_c, positive_appointment_c, dm_qualified_c, feedback_timestamp
                      FROM atc_appointments
                      INNER JOIN atc_isscampaigns_atc_appointments_c ca ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id and ca.deleted = 0


                      LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id and sa.deleted = 0
                      LEFT JOIN atc_clientsalesreps rep ON rep.id = sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida and rep.deleted = 0
                      INNER JOIN accounts_atc_appointments_1_c acc ON acc.accounts_atc_appointments_1atc_appointments_idb = atc_appointments.id and acc.deleted = 0
                      LEFT JOIN accounts ON accounts.id = acc.accounts_atc_appointments_1accounts_ida and acc.deleted = 0
                      LEFT JOIN atc_appointments_contacts_c ac ON ac.atc_appointments_contactsatc_appointments_idb = atc_appointments.id and ac.deleted = 0
                      LEFT JOIN contacts con ON ac.atc_appointments_contactscontacts_ida = con.id and con.deleted = 0
                      LEFT JOIN atc_appointments_cstm ON atc_appointments_cstm.id_c = atc_appointments.id
                      LEFT JOIN users ise ON ise.id = atc_appointments.assigned_user_id
                      LEFT JOIN atc_isscampaigns on atc_isscampaigns.id = ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida and atc_isscampaigns.deleted =0
                      LEFT JOIN users accman ON atc_isscampaigns.assigned_user_id = accman.id and accman.deleted = 0
                      WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN('" . implode("','",
                $selected_campaigns) . "')
                      {$salesrepfilter}
                      {$condition}
                      {$created_condition}
                      and atc_appointments.appointment_status != 'Cancelled_ISS'
                      ORDER BY appointment_date DESC";

        // fetch results
        $results = DB::select($app_query, [1]);

        // go troufgh each result from db
        foreach ($results as $key => $record_data) {

            $app_date = new DateTime($record_data->appointment_date, new DateTimeZone('UTC'));
            $app_date->setTimezone(new DateTimeZone("America/New_York"));
            $record_data->appointment_date = $app_date->format('m-d-Y h:i A');

            $createdate = new DateTime($record_data->date_entered, new DateTimeZone('UTC'));
            $createdate->setTimezone(new DateTimeZone("America/New_York"));
            $record_data->date_entered = $createdate->format('m-d-Y');
            $record_data->description = nl2br($record_data->description);
            

            // make sure that we have appropriate counter
            if ($record_data->appointment_status == "Attended" || $record_data->appointment_status == "Attended_Policy" || $record_data->appointment_status == "DC_Appt_Attended") // up the counter
            {
                $total_attended++;
            }

            // make sure that status is not set to canceled
            if ($record_data->appointment_status != 'Cancelled') {

                // up the counter
                $total_appointments++;

                // introduce opp timeline and amount
                $opp_timeline = '';
                $opp_amount = '';

                // make sure that appointment was attended
                // (only attended apps can have timeline and amount)
                if ($record_data->appointment_status == "Attended") {

                    // make sure that app have feedback
                    $opp_timeline = empty($record_data->appointment_feedback) ? 'Awaiting Feedback' : $record_data->appointment_result_c;
                    $opp_amount = empty($record_data->appointment_feedback) ? 'Awaiting Feedback' : $record_data->opportunity_amount;
					$second_app = empty($record_data->appointment_feedback) ? 'Awaiting Feedback' : $record_data->second_appointment_c;
                }

                // display 'Accepted' and 'DC_Appt_Accepted' as 'Upcoming' on portal
                if (in_array($record_data->appointment_status, ['Accepted', 'DC_Appt_Accepted'])) {
                    $record_data->appointment_status = 'Upcoming';
                }

                // display 'DC_Appt_Attended' as 'Attended' on portal
                if ($record_data->appointment_status == 'DC_Appt_Attended' || $record_data->appointment_status == 'Attended_Policy') {
                    $record_data->appointment_status = 'Attended';
                }
                $appointment_result_c = Array(
                    "" => "",
                    "NoResult" => "Awaiting Timeline",
                    "SixMonths" => "< 6 Months",
                    "TwelveMonths" => "6-12 Months",
                    "Longer" => "> 12 Months"
                );
				
                $opportunity_amount = Array(
                    "" => "",
                    "No_Result" => "",
                    "35" => "0-35k",
                    "75" => "35-75k",
                    "150" => "75-150k",
                    "150_Plus" => "150k-400k",
                    "400" => "400k-1M",
                    "1m" => "1M +"
                );
				$second_appointment = Array(
				""=>"",
				"Yes"=>"Yes",
				"No"=>"No",
				"No_Result"=>"No Response"
				);


                // make sure that appointment was attended
                // (only them can have timeline and amount)
                $opp_timeline = $record_data->appointment_status == "Attended" ? $record_data->appointment_result_c : '';
                $opp_amount = $record_data->appointment_status == "Attended" ? $record_data->opportunity_amount : '';
				$second_app = $record_data->appointment_status == "Attended" ?  $record_data->second_appointment_c : '';

                // build response
                $res['table_data'][] = [
                    "<a id='{$record_data->id}' href='' class='detail-view btn-link show-overlay'>{$record_data->appointment_number}</a>",
                    $record_data->campaign_name,
                    $record_data->appointment_status,
                    $record_data->appointment_place,
                    $record_data->appointment_date,
                    $record_data->acc_name,
                    $record_data->title,
                    "{$record_data->con_first} {$record_data->con_last}",
                    "{$record_data->rep_first} {$record_data->rep_last}",
                    $appointment_result_c[$opp_timeline],
                    $opportunity_amount[$opp_amount],
                    $second_appointment[$second_app]
                ];

                // detail view data
                $res['detail_data'][$record_data->id] = $record_data;
            }
        }

        // add total counters
        $res['totals']['appointments_total_attended'] = $total_attended;
        $res['totals']['appointments_total_appointments'] = $total_appointments;

        // return response
        return $res;
    }

    /**
     * Retrieves all dates and times needed for filtering appointments
     *
     * @return array
     */
    private static function getTimes()
    {

        $now_time = new Carbon('now');

        // time retrieved is UTC, same as DB
        $now_time = $now_time->format('Y-m-d H:i:s');

        $this_month_start_time = new Carbon('first day of this month');

        $this_month_end_time = new Carbon('last day of this month');

        $last_month_start_time = new Carbon('first day of last month');

        $last_month_end_time = new Carbon('last day of last month');
		// Steven 7/29 Added for Today functions. 
		$startDay = Carbon::today()->startOfDay(); // Purely for helping define end of day 
		$endDay   = $startDay->copy()->endOfDay();
		$startTomorrowDay = Carbon::tomorrow()->startofDay(); // Purely for heling define end of tomorrow. 
		$endTomorrowDay = $startTomorrowDay->copy()->endofDay();
		$startYesterday = Carbon::yesterday()->startofDay(); // Purely for helping define end of yesterday. 
		$endYesterday = $startYesterday->copy()->endofDay();
		

        return array(
            'today' => date("Y-m-d"),
			// Steven 7/29: Added in tomorrow and yesterday in the array. 
			'tomorrow' => date('Y-m-d', strtotime('+1 days')),
			'yesterday'=> date('Y-m-d', strtotime('-1 days')),
            'last_7' => date('Y-m-d', strtotime('-7 days')),
            'last_30' => date('Y-m-d', strtotime('-30 days')),
            'last_month_start' => date('Y-m-d', strtotime('first day of last month')),
            'last_month_end' => date('Y-m-d', strtotime('last day of last month')),
            'this_month_start' => date('Y-m-d', strtotime('first day of this month')),
            'this_month_end' => date('Y-m-d', strtotime('last day of this month')),
            'now_time' => $now_time,
			// Steven 7/29: Added end_of_today, end_of_tomorrow, and end_of_yesterday.
			'end_of_today' => $endDay->endofDay()->format('Y-m-d H:i:s'),
			'end_of_tomorrow' => $endTomorrowDay->endofDay()->format('Y-m-d H:i:s'),
			'end_of_yesterday'=> $endYesterday->endofDay()->format('Y-m-d H:i:s'),
            'this_month_start_time' => $this_month_start_time->startOfDay()->format('Y-m-d H:i:s'),
            'this_month_end_time' => $this_month_end_time->endOfDay()->format('Y-m-d H:i:s'),
            'last_month_start_time' => $last_month_start_time->startOfDay()->format('Y-m-d H:i:s'),
            'last_month_end_time' => $last_month_end_time->endOfDay()->format('Y-m-d H:i:s')
        );
    }

    /**
     * Retrieves dropdown key/value pairs
     *
     * @return array
     */
    public static function getDropDowns()
    {
        // introduce labels for $second_appointment_c
        $second_appointment_c = [
            ['Yes' => 'Yes'],
            ['No' => 'No'],
            ['No_Result' => 'No Response'],
        ];

        // introduce labels for $positive_appointment_c
        $positive_appointment_c = array(
            ['No_Result' => 'No Respnse'],
            ['A_Good_introductory_meeting_that_may_offer_future_possibilities' => 'A. Good introductory meeting that may offer future possibilities'],
            ['B_Positive_step_in_terms_of_getting_your_foot_in_the_door' => 'B. Positive step in terms of getting your foot in the door'],
            ['C._Positive_because_the_contact_directed_us_to_another_decision_maker.' => 'C. Positive because the contact directed us to another decision maker.'],
            ['D_Negative_Not_worth_my_time.' => 'D. Negative, Not worth my time.'],
        );

        // introduce labels for $dm_qualified_c
        $dm_qualified_c = array(
            ['' => ''],
            ['Yes' => 'Yes'],
            ['No' => 'No']
        );

        // introduce labels for $dm_qualified_c
        // (hint: there is completely different set of labels in sugar.
        // these ones are requested for portal)
        $appointment_result_c = array(
            ["NoResult" => "Awaiting Timeline"],
            ["SixMonths" => "< 6 Months"],
            ["TwelveMonths" => "6-12 Months"],
            ["Longer" => "> 12 Months"]
        );

        // introduce labels for $opportunity_amount
        // (hint: same as $appointment_result_c, these options
        // are different comparing to sugar's)
        $opportunity_amount = [
            ["" => ""],
            ["No_Result" => ""],
            ["35" => "0-35k"],
            ["75" => "35-75k"],
            ["150" => "75-150k"],
            ["150_Plus" => "150k-400k"],
            ["400" => "400k-1M"],
            ["1m" => "1M +"]
        ];

        return [
            'second_appointment_c' => $second_appointment_c,
            'positive_appointment_c' => $positive_appointment_c,
            'dm_qualified_c' => $dm_qualified_c,
            'appointment_result_c' => $appointment_result_c,
            'opportunity_amount' => $opportunity_amount
        ];
    }

    /**
     * Exports Appointment data for Appointments list view in Excel file
     *
     * @param $selected_campaigns
     * @param $selected_salesreps
     * @param string $appointment_date_filter
     * @param string $date_created_filter
     * @return array
     */
    public static function exportAppointments(
        $selected_campaigns,
        $selected_salesreps,
        $appointment_date_filter = '',
        $date_created_filter = ''
    ) {

        // build salesreps filter
        if (empty($selected_salesreps)) {
            $salesrepfilter = "";
        } else {
            $salesrepfilter = "AND sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida IN('" . implode("','",
                    $selected_salesreps) . "') ";
        }

        // retrieve all times needed for filtering
        $time = self::getTimes();

        /**
         * Build 'Condition filter'
         */
		 // Steven 7/26/2019: Added Today Filter, and on 7/29/2019, added yesterday and tomorrow filters. 
        if ($appointment_date_filter == "today") {
            $condition = "AND atc_appointments.appointment_date >= '{$time['today']}' and atc_appointments.appointment_date <= '{$time['end_of_today']}'";
        } elseif ($appointment_date_filter == "tomorrow") {
            $condition = "AND atc_appointments.appointment_date > '{$time['tomorrow']}' and atc_appointments.appointment_date <= '{$time['end_of_tomorrow']}'";
        } elseif ($appointment_date_filter == "yesterday") {
            $condition = "AND atc_appointments.appointment_date > '{$time['yesterday']}' and atc_appointments.appointment_date <= '{$time['end_of_yesterday']}'";				
        } elseif ($appointment_date_filter == "last_7_days") {
            $condition = "AND atc_appointments.appointment_date > '{$time['last_7']}' and atc_appointments.appointment_date <= '{$time['now_time']}'";			
        } elseif ($appointment_date_filter == "last_30_days") {
            $condition = "AND atc_appointments.appointment_date > '{$time['last_30']}' and atc_appointments.appointment_date <= '{$time['now_time']}'";
        } elseif ($appointment_date_filter == "this_month") {
            $condition = "AND atc_appointments.appointment_date > '{$time['this_month_start']}' and atc_appointments.appointment_date <= '{$time['this_month_end']}'";
        } elseif ($appointment_date_filter == "last_month") {
            $condition = "AND atc_appointments.appointment_date > '{$time['last_month_start']}' and atc_appointments.appointment_date <= '{$time['last_month_end']}'";
        } elseif ($appointment_date_filter == "future") {
            $condition = "AND atc_appointments.appointment_date >= '{$time['now_time']}'";
		}  else {
			$condition = '';
			  
        }

        // build date filter query
		// Steven 7/29/2019: Added Today and yesterday filters. (You can't create something in the future, so tomorrow is not necessary).
		if ($date_created_filter == "today") {
			$created_condition = "AND atc_appointments.date_entered >= '{$time['today']}' and atc_appointments.date_entered <= '{$time['end_of_today']}'";
		}elseif ($date_created_filter == "yesterday") {
			$created_condition = "AND atc_appointments.date_entered >= '{$time['yesterday']}' and atc_appointments.date_entered <= '{$time['end_of_yesterday']}'";				
        }elseif ($date_created_filter == "last_7_days") {
            $created_condition = "AND atc_appointments.date_entered  > '{$time['last_7']}' and atc_appointments.date_entered <= '{$time['now_time']}'";
        } elseif ($date_created_filter == "last_30_days") {
            $created_condition = "AND atc_appointments.date_entered  > '{$time['last_30']}' and atc_appointments.date_entered <= '{$time['now_time']}'";
        } elseif ($date_created_filter == "this_month") {
            $created_condition = "AND atc_appointments.date_entered  > '{$time['this_month_start_time']}' and atc_appointments.date_entered <= '{$time['this_month_end_time']}'";
        } elseif ($date_created_filter == "last_month") {
            $created_condition = "AND atc_appointments.date_entered  > '{$time['last_month_start_time']}' and atc_appointments.date_entered <= '{$time['last_month_end_time']}'";
        } else {
            $created_condition = '';
        }

        // build query
        $app_query = "SELECT DISTINCT appointment_number AS 'appointment_number', appointment_status AS 'status', appointment_date AS 'date', appointment_place AS 'format', atc_appointments.description AS 'notes',
                      appointment_feedback AS 'sales_feedback', concat(rep.first_name,' ',rep.last_name) as 'sales_rep', accounts.name as 'account_name', concat(con.first_name,' ',con.last_name) as 'contact', con.title AS 'title',
                      con.phone_other AS 'direct_phone', con.phone_work AS 'office_phone', contact_email As 'email', con.primary_address_street AS 'street', con.primary_address_city AS 'city', atc_appointments_cstm.appointment_result_c AS 'timeline',
                      atc_appointments.opportunity_amount as 'opportunity_amount', con.primary_address_state AS 'state', con.primary_address_postalcode AS 'postal_code', con.primary_address_country AS 'country', concat(ise.first_name, ' ',ise.last_name) As 'ise',
                      concat(accman.first_name, ' ', accman.last_name) As 'account_manager', concat(adu.first_name, ' ',adu.last_name) As 'account_director', atc_appointments.date_entered as 'date_created', atc_appointments.date_modified as 'date_modified',
                      cac.campaign_email_and_password_c as 'distrubutor', cam.name as campaign_name
                      FROM atc_appointments
                      INNER JOIN atc_isscampaigns_atc_appointments_c ca ON ca.atc_isscampaigns_atc_appointmentsatc_appointments_idb = atc_appointments.id AND ca.deleted = 0
                      INNER JOIN atc_isscampaigns cam ON cam.id = ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida
                      INNER JOIN atc_isscampaigns_cstm cac ON ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida = cac.id_c
                      LEFT JOIN atc_clients_atc_isscampaigns_c cc ON cc.atc_clients_atc_isscampaignsatc_isscampaigns_idb = ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida and cc.deleted = 0
		              LEFT JOIN atc_clients c ON c.id = cc.atc_clients_atc_isscampaignsatc_clients_ida


		              LEFT JOIN users accman ON cam.assigned_user_id = accman.id
                      LEFT JOIN atc_clientsalesreps_atc_appointments_c sa ON sa.atc_clientsalesreps_atc_appointmentsatc_appointments_idb = atc_appointments.id and sa.deleted = 0
                      LEFT JOIN atc_clientsalesreps rep ON rep.id = sa.atc_clientsalesreps_atc_appointmentsatc_clientsalesreps_ida
                      INNER JOIN accounts_atc_appointments_1_c acc ON acc.accounts_atc_appointments_1atc_appointments_idb = atc_appointments.id and acc.deleted = 0
                      INNER JOIN accounts ON accounts.id = acc.accounts_atc_appointments_1accounts_ida
                      INNER JOIN atc_appointments_contacts_c ac ON ac.atc_appointments_contactsatc_appointments_idb = atc_appointments.id and ac.deleted = 0
                      INNER JOIN contacts con ON ac.atc_appointments_contactscontacts_ida = con.id
                      LEFT JOIN atc_appointments_cstm ON atc_appointments_cstm.id_c = atc_appointments.id
	                  LEFT JOIN users ise ON ise.id = atc_appointments.assigned_user_id
                      LEFT JOIN users_atc_isscampaigns_1_c ad on ad.users_atc_isscampaigns_1atc_isscampaigns_idb = cac.id_c and ad.deleted = 0
                      LEFT JOIN users adu on adu.id = ad.users_atc_isscampaigns_1users_ida
                      WHERE ca.atc_isscampaigns_atc_appointmentsatc_isscampaigns_ida IN('" . implode("','",
                $selected_campaigns) . "')
                      {$salesrepfilter}
                      {$condition}
                      {$created_condition}
                      ORDER BY appointment_date";

        // fetch results
        $results = DB::select($app_query, [1]);

        // return response
        return ['results' => $results, 'query' => $app_query];
    }

    /**
     * Saves new (or edits existing) report delivery configuration
     *
     * @param $data
     * @return array|bool|float|\Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface|int|string
     */
    public static function saveDeliveryReportConfiguration($data)
    {
        // introduce new API wrapper object
        $connector = new Rest();

        // try to log in to sugar
        try {

            $connector->setUrl(env('ISS_URL'))
                ->setUsername(env('ISS_API_USERNAME'))
                ->setPassword(env('ISS_API_PASSWORD'))
                ->setPlatform(env('ISS_API_PLATFORM'))
                ->connect();

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        // introduce the configuration record data
        $post_data = [
            'name' => 'configuration ' . $data['portal_user_id'],
            'ms_reportd2fd0t_users_idb' => $data['portal_user_id'],
            'description' => json_encode($data)
        ];

        try {

            // check if this is configuration edit (or save)
            if ($configuration_id = $data['id']) {

                // edit configuration
                $result = $connector->putEndpoint(
                    "MS_ReportDeliveryConfiguration/$configuration_id",
                    $post_data);

            } else {

                // save configuration
                $result = $connector->postEndpoint(
                    'MS_ReportDeliveryConfiguration',
                    $post_data);
            }

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        // make sure that configuration id exists
        if (!$configuration_id = $result['id']) {

            // return error message
            return response()->json([
                'success' => false,
                'message' => 'Configuration id was not retrieved, please refresh the page and try again'
            ]);
        }

        // return response
        return response()->json([
            'success' => true,
            'configuration_id' => $result['id']
        ]);
    }

    /**
     * Retrieves delivery report configuration (for passed user)
     *
     * @param $cp_user_id [portal user's id]
     * @return array
     */
    public static function getDeliveryReportConfiguration($cp_user_id)
    {
        // introduce new API wrapper object
        $connector = new Rest();

        // try to log in to sugar
        try {

            $connector->setUrl(env('ISS_URL'))
                ->setUsername(env('ISS_API_USERNAME'))
                ->setPassword(env('ISS_API_PASSWORD'))
                ->setPlatform(env('ISS_API_PLATFORM'))
                ->connect();

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        // try to retrieve portal user's bean
        try {

            $cp_user_bean = $connector->getEndpoint("CP_Client_Users/$cp_user_id");

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        // introduce related configuration's data
        $configuration_link = $cp_user_bean['ms_reportdeliveryconfiguration_cp_client_users_1'];

        // make sure that user has configuration set
        if (!$configuration_link['id']) {

            return response()->json(['success' => false, 'message' => 'no-configuration']);
        }

        // try to retrieve configuration bean
        try {

            $configuration_bean = $connector->getEndpoint("MS_ReportDeliveryConfiguration/{$configuration_link['id']}");

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        // introduce the configuration
        $configuration_array = json_decode($configuration_bean['description'], true);

        // make sure that configuration is valid
        if (!$configuration_array) {

            // return error message
            return response()->json([
                'success' => false,
                'message' => 'Configuration invalid. Please contact your administrator.'
            ]);
        }

        // return retrieved configuration data
        return response()->json(['success' => true, 'data' => $configuration_array]);
    }

    /**
     * Triggers sending shapshort report from sugar
     *
     * @param $cp_user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendReport($cp_user_id)
    {
        // make sure that current user's id has been passed
        if (empty($cp_user_id)) {

            // return error message
            return response()->json(['success' => false, 'message' => "User's ID has not been passed"]);
        }

        // introduce new API wrapper object
        $connector = new Rest();

        // try to log in to sugar
        try {

            $connector->setUrl(env('ISS_URL'))
                ->setUsername(env('ISS_API_USERNAME'))
                ->setPassword(env('ISS_API_PASSWORD'))
                ->setPlatform(env('ISS_API_PLATFORM'))
                ->connect();

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        // try to trigger sending report
        try {

            // introduce the sugar endpoint (which will generate and send the report)
            $endpoint = 'ms-portal-reports/snapshot-report';

            // try to send the report
            $sending_report_status = $connector->postEndpoint($endpoint, ['cp_user_id' => $cp_user_id]);

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        // return retrieved configuration data
        return response()->json($sending_report_status);
    }

    /**
     * Triggers editing appointment record
     *
     * @param $appointment_id
     * @param $post_data
     * @return array|\Illuminate\Http\JsonResponse
     */
    public static function editAppointment($appointment_id, $post_data)
    {
        // make sure that data has been passed
        if (empty($post_data)) {

            // return error message
            return response()->json(['success' => false, 'message' => "Data has not been passed"]);
        }

        // introduce new API wrapper object
        $connector = new Rest();

        // try to log in to sugar
        try {

            $connector->setUrl(env('ISS_URL'))
                ->setUsername(env('ISS_API_USERNAME'))
                ->setPassword(env('ISS_API_PASSWORD'))
                ->setPlatform(env('ISS_API_PLATFORM'))
                ->connect();

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        // try to edit appointment
        try {

            // introduce the endpoint (editing appointment)
            $endpoint = 'ATC_Appointments/' . $appointment_id;

            // make the call
            $appointment = $connector->putEndpoint($endpoint, $post_data);

        } catch (\Exception $exception) {

            // return error message
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }

        return ['success' => true, 'appointment' => $appointment];
    }


}
