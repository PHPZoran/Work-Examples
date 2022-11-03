<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPExcel;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_Alignment;
use PHPExcel_IOFactory;
use PHPExcel_Style_Font;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use DateTime;
use DateTimeZone;


/**
 * Class ReportController
 *
 * @package App\Http\Controllers
 */
class ReportController extends Controller
{

    var $users;

    // introduce the current user's id
    var $current_user_id = null;

    /**
     * Generates default/landing page of the application
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $get_data = $request->all();

        // fetch campaigns
        $campaigns = Report::fetchCampaigns(empty($get_data['type']) ? '' : $get_data['type']);

        // fetch salesreps
        $salesreps = Report::fetchSalesReps($campaigns);

        // build data for the view
        $data = [
            'campaigns' => $campaigns,
            'salesreps' => $salesreps
        ];

        if (!empty($get_data['type'])) {
            return response()->json($data);
        }

        // send data to the report view
        return view('report.report', ['data' => $data]);
    }

    /**
     * Fetches SaleReps based on selected campaigns
     *
     * @param Request $request
     * @return mixed
     */
    public function salesreps(Request $request)
    {

        // get frontend data
        $data = $request->all();

        // return data to the view
        return Report::fetchSalesReps($data['campaign_ids']);
    }

    /**
     * Fetches Activities data based on selected camaigns and salesreps
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function activities(Request $request)
    {

        // fetch frontend data (camapaigns and salesreps)
        $post_data = $request->all();

        // make sure that we have selected campaigns and salesreps
        if (!isset($post_data['salesreps_ids'])) {
            $post_data['salesreps_ids'] = [];
        }
        if (!isset($post_data['campaign_ids'])) {
            $post_data['campaign_ids'] = [];
        }
        if (!isset($post_data['date_created'])) {
            $post_data['date_created'] = '';
        }
        if (!isset($post_data['appointment_date'])) {
            $post_data['appointment_date'] = '';
        }

        // fetch activities
        $activities = Report::fetchActivities($post_data['campaign_ids'], $post_data['salesreps_ids']);

        // fetch appointments data
        $activities['appointments_results'] = Report::fetchAppointments($post_data['campaign_ids'],
            $post_data['salesreps_ids'], $post_data['appointment_date'], $post_data['date_created']);

        // pass dropdowns (that are used on appointments details)
        $activities['dropdowns'] = Report::getDropDowns();

        // return response
        return response()->json($activities);
    }

    /**
     * Retrieves details for requested chart (Pie Chart Drill Down)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function chartDrillDown(Request $request)
    {
        // make sure that action is set
        if (empty($action = $request->input('action'))) {

            return response()->json([
                'success' => false,
                'message' => 'Action was not set. Please refresh the page and try again.'
            ]);
        }

        // take the action based on the request
        switch ($action) {

            case 'prospecting_activity':
                {
                    // retrieve 'prospecting activity' chart details
                    return $this->prospectingActivityDrillDown($request);

                    break;
                }

            case 'positive_appointment':
                {
                    // retrieve 'positive appointment' chart details
                    return $this->positiveAppointmentDrillDown($request);

                    break;
                }

            case 'second_appointment':
                {
                    // retrieve 'second appointment' chart details
                    return $this->secondAppointmentDrillDown($request);

                    break;
                }

            case 'held_appointments':
                {
                    // retrieve 'held appointments' chart details
                    return $this->heldAppointmentsDrillDown($request);

                    break;
                }

            case 'max_value':
                {
                    // retrieve 'max value' chart details
                    return $this->maxValueDrillDown($request);

                    break;
                }

            case 'total_appointments':
                {
                    // retrieve 'total appointments' chart details
                    return $this->totalAppointmentsDrillDown($request);

                    break;
                }

            default:
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unknown action. Please refresh the page and try again.'
                    ]);
                }
        }

    }

    /**
     * Retrieves 'total appointments' chart details (chart drill down)
     *
     * @param $request
     * @return JsonResponse
     */
    private function totalAppointmentsDrillDown($request)
    {
        // introduce the request data
        $campaign_ids = $request->input('campaign_ids');
        $salesreps_ids = $request->input('salesreps_ids');
        $status = $request->input('status');

        // make sure that all needed parts have been passed
        if (empty($campaign_ids) or empty($salesreps_ids) or empty($status)) {

            return response()->json([
                'success' => false,
                'message' => 'Post data is missing. Please refresh the page and try again.'
            ]);
        }

        // fetch appointments
        $total_appointments_details = Report::totalAppointmentsDetails($campaign_ids, $salesreps_ids, $status);

        // introduce the response array
        $response = [];

        // introduce UTC datetime object
        $UTC = new DateTimeZone("UTC");

        // introduce EST datetime object
        $EST = new DateTimeZone("America/New_York");

        // iterate trough retrieved data
        foreach ($total_appointments_details as $appointment_data) {

            // introduce the appointment date in DB format
            $appointment_date_db = $appointment_data->appointment_date;

            // introduce the appointment date object
            $appointment_date_object = new DateTime($appointment_date_db, $UTC);

            // change the TZ to EST
            $appointment_date_object->setTimezone($EST);

            // format the date to user format
            $appointment_date_user = $appointment_date_object->format('Y-m-d h:i A');

            // format the data it the way front-end expects it
            $response[] = [
"<a id='{$appointment_data->appid}' href='' class='detail-view btn-link show-overlay'>{$appointment_data->appointment_number}</a>",                
//'<a id="'.$appointment_data->appid.'" href ="" class="detail-view btn-link show-overlay">'.$appointment_data->appointment_number.'</a>',
                $appointment_date_user,
                $appointment_data->con_first . ' ' . $appointment_data->con_last,
                $appointment_data->account_name,
                $appointment_data->sales_first . ' ' . $appointment_data->sales_last,
            ];
        }

        // return data
        return response()->json(['success' => true, 'data' => $response]);
    }

    /**
     * Retrieves 'max value' chart details (chart drill down)
     *
     * @param $request
     * @return JsonResponse
     */
    private function maxValueDrillDown($request)
    {

        // introduce the request data
        $campaign_ids = $request->input('campaign_ids');
        $salesreps_ids = $request->input('salesreps_ids');
        $value_range = $request->input('value_range');

        // make sure that all needed parts have been passed
        if (empty($campaign_ids) or empty($salesreps_ids) or empty($value_range)) {

            return response()->json([
                'success' => false,
                'message' => 'Post data is missing. Please refresh the page and try again.'
            ]);
        }

        // fetch activities
        $max_value_details = Report::maxValueDetails($campaign_ids, $salesreps_ids, $value_range);

        // introduce the response array
        $response = [];

        // iterate through retrieved data
        foreach ($max_value_details as $opp_value_data) {

            // format the data it the way front-end expects it
            $response[] = [
		$opp_value_data->appointment_number,
                $opp_value_data->first_name . ' ' . $opp_value_data->last_name,
                $opp_value_data->title,
                $opp_value_data->name,
		$opp_value_data->billing_address_state,

            ];
        }

        // return data
        return response()->json(['success' => true, 'data' => $response]);
    }

    /**
     * Retrieves 'prospecting activity' chart details (chart drill down)
     *
     * @param $request
     * @return JsonResponse
     */
    private function prospectingActivityDrillDown($request)
    {
        // introduce the request data
        $campaign_ids = $request->input('campaign_ids');
        $salesreps_ids = $request->input('salesreps_ids');
        $call_outcome = $request->input('call_outcome');

        // make sure that all needed parts have been passed
        if (empty($campaign_ids) or empty($salesreps_ids) or empty($call_outcome)) {

            return response()->json([
                'success' => false,
                'message' => 'Post data is missing. Please refresh the page and try again.'
            ]);
        }

        // fetch activities
        $activity_details = Report::activityDetails($campaign_ids, $salesreps_ids, $call_outcome);

        // introduce the response array
        $response = [];

        // iterate through retrieved data
        foreach ($activity_details as $activity) {

            // format the data it the way front-end expects it
            $response[] = [
                $activity->title,
                $activity->name,
                rtrim($activity->billing_address_city, ','),
                $activity->billing_address_state,
            ];
        }

        // return data
        return response()->json(['success' => true, 'data' => $response]);
    }

    /**
     * Retrieves 'positive appointment' chart details (chart drill down)
     *
     * @param $request
     * @return JsonResponse
     */
    private function heldAppointmentsDrillDown($request)
    {
        // introduce the request data
        $campaign_ids = $request->input('campaign_ids');
        $salesreps_ids = $request->input('salesreps_ids');
        $appointment_outcome = $request->input('appointment_outcome');

        // make sure that all needed parts have been passed
        if (empty($campaign_ids) or empty($salesreps_ids) or empty($appointment_outcome)) {

            return response()->json([
                'success' => false,
                'message' => 'Post data is missing. Please refresh the page and try again.'
            ]);
        }

        // fetch held appointments' details
        $held_appointments = Report::heldAppointmentDetails($campaign_ids, $salesreps_ids, $appointment_outcome);

        // introduce the response array
        $response = [];

        // iterate through retrieved data
        foreach ($held_appointments as $appointment) {

            // format the data it the way front-end expects it
            $response[] = [
		$appointment->appointment_number,
                $appointment->first_name . ' ' . $appointment->last_name,
                $appointment->title,
                $appointment->name,
		$appointment->billing_address_state,
            ];
        }

        // return data
        return response()->json(['success' => true, 'data' => $response]);
    }

    /**
     * Retrieves 'positive appointment' chart details (chart drill down)
     *
     * @param $request
     * @return JsonResponse
     */
    private function positiveAppointmentDrillDown($request)
    {
        // introduce the request data
        $campaign_ids = $request->input('campaign_ids');
        $salesreps_ids = $request->input('salesreps_ids');
        $appointment_outcome = $request->input('appointment_outcome');

        // make sure that all needed parts have been passed
        if (empty($campaign_ids) or empty($salesreps_ids) or empty($appointment_outcome)) {

            return response()->json([
                'success' => false,
                'message' => 'Post data is missing. Please refresh the page and try again.'
            ]);
        }

        // fetch positive appointment details
        $positive_appointment = Report::positiveAppointmentDetails($campaign_ids, $salesreps_ids, $appointment_outcome);

        // introduce the response array
        $response = [];

        // iterate through retrieved data
        foreach ($positive_appointment as $appointment) {

            // format the data it the way front-end expects it
            $response[] = [
                $appointment->appointment_number,	
		$appointment->first_name . ' ' . $appointment->last_name,
                $appointment->title,
                $appointment->name,
		$appointment->billing_address_state,
            ];
        }

        // return data
        return response()->json(['success' => true, 'data' => $response]);
    }

    /**
     * Retrieves 'second appointment' chart details (chart drill down)
     *
     * @param $request
     * @return JsonResponse
     */
    private function secondAppointmentDrillDown($request)
    {
        // introduce the request data
        $campaign_ids = $request->input('campaign_ids');
        $salesreps_ids = $request->input('salesreps_ids');
        $appointment_outcome = $request->input('appointment_outcome');

        // make sure that all needed parts have been passed
        if (empty($campaign_ids) or empty($salesreps_ids) or empty($appointment_outcome)) {

            return response()->json([
                'success' => false,
                'message' => 'Post data is missing. Please refresh the page and try again.'
            ]);
        }

        // fetch second appointment details
        $second_appointment = Report::secondAppointmentDetails($campaign_ids, $salesreps_ids, $appointment_outcome);

        // introduce the response array
        $response = [];

        // iterate through retrieved data
        foreach ($second_appointment as $appointment) {

            // format the data it the way front-end expects it
            $response[] = [
		$appointment->appointment_number,
                $appointment->first_name . ' ' . $appointment->last_name,
                $appointment->title,
                $appointment->name,
		$appointment->billing_address_state,
            ];
        }

        // return data
        return response()->json(['success' => true, 'data' => $response]);
    }

    /**
     * Fetches positive appointments data for charts and table
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function positiveAppointments(Request $request)
    {

        // fetch frontend data (campaigns)
        $post_data = $request->all();

        // make sure that we have
        if (!isset($post_data['campaign_ids'])) {
            $post_data['campaign_ids'] = [];
        }

        // fetch appointments
        $appointments = Report::fetchPositiveAppointments($post_data['campaign_ids'], $post_data['salesrep_ids']);

        // return response
        return response()->json($appointments);
    }

    /**
     * Generates and downloads appointments list excel file
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function downloadExcel(Request $request)
    {

        // fetch frontend data (camapaigns and salesreps)
        $post_data = $request->all();

        // make sure that we have selected campaigns and salesreps
        if (!isset($post_data['salesreps_ids'])) {
            $post_data['salesreps_ids'] = [];
        }
        if (!isset($post_data['campaign_ids'])) {
            $post_data['campaign_ids'] = [];
        }
        if (!isset($post_data['date_created'])) {
            $post_data['date_created'] = '';
        }
        if (!isset($post_data['appointment_date'])) {
            $post_data['appointment_date'] = '';
        }

        // fetch data from model
        $data = Report::exportAppointments($post_data['campaign_ids'], $post_data['salesreps_ids'],
            $post_data['appointment_date'], $post_data['date_created']);

        // init vars
        $UTC = new DateTimeZone("UTC");
        $newTZ = new DateTimeZone("America/New_York");

        define('COLUMN_WIDTH_A', 23);
        define('COLUMN_WIDTH_B', 11);
        define('COLUMN_WIDTH_SHORT', 20);
        define('COLUMN_WIDTH_LONG', 40);

        define('WORKSHEET_APPOINTMENTS', 0);
        define('WORKSHEET_CANCELLED', 1);

        // init labels
        $appointment_status_labels = array(
            '' => '',
            'Attended' => 'Attended',
            'Attended_Policy' => 'Attended',
            'Cancelled' => 'Cancelled',
            'Cancelled_ISS' => 'Cancelled',
            'Accepted' => 'Upcoming',
            'Confirmed' => 'Upcoming',
            'Reschedule' => 'Reschedule',
            'Days_Calling' => 'Days Calling',
            'DC_Appt_Accepted' => 'DC Appt Accepted',
            'DC_Appt_Attended' => 'DC Appt Attended',
            'Event Registration' => 'Event Registration'
        );

        $opportunity_amount_labels = array(
            '' => '',
            'No_Result' => '',
            '35' => '0 - 35',
            '75' => '35-75',
            '150' => '75-150',
            '150_Plus' => '150-400',
            '400' => '400 - 1 Mil',
            '1m' => '1 Mil +'
        );

        $opportunity_timeline_labels = array(
            '' => '',
            'NoResult' => 'Awaiting Feedback',
            'SixMonths' => 'Within 6 Months',
            'TwelveMonths' => 'Within 12 Months',
            'Longer' => 'Greater than 12 Months',
        );

        // init PHPExcel
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->createSheet(WORKSHEET_CANCELLED);

        $objPHPExcel->getProperties()->setCreator("Inside Sales")
            ->setLastModifiedBy("Inside Sales")
            ->setTitle("Appointments Output")
            ->setSubject("Appointments Output")
            ->setDescription("Inside Sales Appointments Output Document.")
            ->setKeywords("")
            ->setCategory("Appointments");

        $objPHPExcel->getDefaultStyle()->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $objPHPExcel->getDefaultStyle()->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize('11');

        /** Row 2 */

        // image
        // This adds the logo to the appointment sheet
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath('images/iss_logo.jpg');
        $objDrawing->setHeight(80);
        $objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(WORKSHEET_APPOINTMENTS));
        $objDrawing->setOffsetX(40);    // setOffsetX works properly
        $objDrawing->setOffsetY(20);

        // This adds the logo to the cancel sheet
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath('images/iss_logo.jpg');
        $objDrawing->setHeight(80);
        $objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(WORKSHEET_CANCELLED));
        $objDrawing->setOffsetX(40);    // setOffsetX works properly
        $objDrawing->setOffsetY(20);

        // call the function to set the formatting that
        // does not require the cells to be filled in,
        // so happens before the cells are filled by the
        // record set. First for appointmnets and then for
        // the cancelled worksheets
        $this->setFormatBeforeFill($objPHPExcel->setActiveSheetIndex(WORKSHEET_APPOINTMENTS), 'Appointments');
        $this->setFormatBeforeFill($objPHPExcel->setActiveSheetIndex(WORKSHEET_CANCELLED), 'Cancelled');

        /**
         * 6th Row on wards: data from the database
         */

        // declare the counters for the
        // appointment and cancelled
        // worksheet
        // I start then at 5 because they
        // increment once in the first iteration
        $currentRowSheetAppointment = 5;
        $currentRowSheetCancelled = 5;

        $active_sheet = $objPHPExcel->setActiveSheetIndex(WORKSHEET_APPOINTMENTS);
        $active_sheet->setCellValue('A300', $data['query']);
        $active_sheet->setCellValue('A302', implode("','", $post_data['campaign_ids']));

        foreach ($data['results'] as $excel_data) {
            // Format the date data
            $date = new DateTime($excel_data->date, $UTC);
            $date->setTimezone($newTZ);
            $createddate = new DateTime($excel_data->date_created, $UTC);
            $createddate->setTimezone($newTZ);
            $modifieddate = new DateTime($excel_data->date_modified, $UTC);
            $modifieddate->setTimezone($newTZ);

            // ($row['status'] = Attended, Cancelled, Reschedule
            //
            if ($excel_data->status == 'Cancelled' or $excel_data->status == 'Cancelled_ISS') {

                // set the active sheet to the cancelled sheet
                $active_sheet = $objPHPExcel->setActiveSheetIndex(WORKSHEET_CANCELLED);

                // increment the row column
                $currentRowSheetCancelled++;

                // set the current row
                $i = $currentRowSheetCancelled;
            } else {

                // set the active sheet to the appointments sheet
                $active_sheet = $objPHPExcel->setActiveSheetIndex(WORKSHEET_APPOINTMENTS);

                // increment the row column
                $currentRowSheetAppointment++;

                // set the current row
                $i = $currentRowSheetAppointment;

                if ($excel_data->status == 'Reschedule') {
                    $active_sheet
                        ->getStyle('A' . $i . ':' . 'B' . $i)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FF7F50');
                }

                if (strtotime($excel_data->date_created) > strtotime('-7 day')) {


                    $styleArray = array(
                        'font' => array(
                            'bold' => true,
                            'color' => array('rgb' => 'FF0000'),
                        )
                    );

                    $active_sheet
                        ->getStyle('A' . $i . ':' . 'Z' . $i)->applyFromArray($styleArray);
                }

            }

            // introduce the opp's timeline
            $timeline = $opportunity_timeline_labels[$excel_data->timeline];

            // introduce the appointment's status
            $appointment_status = $appointment_status_labels[$excel_data->status];

            // check if appointment's status is 'Upcoming'
            if ($appointment_status == 'Upcoming') {

                // don't show the timeline for upcoming appointments
                $timeline = '';
            }

            //$objPHPExcel->getActiveSheet()
            $active_sheet
                ->setCellValue('A' . $i, $excel_data->campaign_name)
                ->setCellValue('B' . $i, $appointment_status)
                ->setCellValue('C' . $i, $date->format('Y-m-d H:iA'))
                ->setCellValue('D' . $i, htmlentities($excel_data->notes, ENT_SUBSTITUTE))
                ->setCellValue('E' . $i, $timeline)
                ->setCellValue('F' . $i, $opportunity_amount_labels[$excel_data->opportunity_amount])
                ->setCellValue('G' . $i, $excel_data->sales_rep)
                ->setCellValue('H' . $i, htmlentities($excel_data->sales_feedback, ENT_SUBSTITUTE))
                ->setCellValue('I' . $i, $excel_data->account_name)
                ->setCellValue('J' . $i, $excel_data->contact)
                ->setCellValue('K' . $i, $excel_data->format)
                ->setCellValue('L' . $i, $excel_data->title)
                ->setCellValue('M' . $i, $excel_data->direct_phone)
                ->setCellValue('N' . $i, $excel_data->office_phone)
                ->setCellValue('O' . $i, $excel_data->email)
                ->setCellValue('P' . $i, $excel_data->street)
                ->setCellValue('Q' . $i, $excel_data->city)
                ->setCellValue('R' . $i, $excel_data->state)
                ->setCellValue('S' . $i, $excel_data->postal_code)
                ->setCellValue('T' . $i, $excel_data->country)
                ->setCellValue('U' . $i, $excel_data->ise)
                ->setCellValue('V' . $i, $excel_data->account_manager)
                ->setCellValue('W' . $i, $excel_data->account_director)
                ->setCellValue('X' . $i, $excel_data->distrubutor)
                ->setCellValue('Y' . $i, $createddate->format('Y-m-d H:iA'))
                ->setCellValue('Z' . $i, $modifieddate->format('Y-m-d H:iA'));
        }

        // call the function to format after the cells have been filled
        $this->setFormatAfterFill($objPHPExcel->setActiveSheetIndex(WORKSHEET_CANCELLED));
        $this->setFormatAfterFill($objPHPExcel->setActiveSheetIndex(WORKSHEET_APPOINTMENTS));

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->setPreCalculateFormulas(true);

        ob_end_clean();

        // generate files in public/export
        $objWriter->save("export/iss_appointments.xlsx");

        // return response
        return response()->json("export/iss_appointments.xlsx");
    }

    /*
    Created by :    A.P. Massardo
    Date:           09-April-2016
    Description:    Formats cells and creates headers for the spread sheet,
                    for cells that are not data dependent
    */
    private function setFormatBeforeFill($active_sheet, $title)
    {

        $active_sheet->setTitle($title);

        $active_sheet->getRowDimension('2')->setRowHeight(60);
        $active_sheet->setCellValue('C2', 'Inside Sales Solutions Appointment Report')
            ->getStyle('C2')
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => '6495ed'
                    ],
                    'size' => 18,
                ],
            ]);

        // portal login link
        $active_sheet->getStyle('H2')
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => '0000cd'
                    ],
                    'size' => 16,
                    'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE
                ],
            ]);
        $active_sheet->getStyle('H2')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('8470ff');
        $active_sheet->getStyle('H2')->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $active_sheet->getStyle('H2')->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('H2', 'Client Portal Login');
        $active_sheet->getCell('H2')
            ->getHyperlink()
            ->setUrl('https://portal.isaless.com/');

        /**
         * Row #4
         * Appointment information title
         */
        $active_sheet->getRowDimension('4')->setRowHeight(30);
        $active_sheet->setCellValue('D4', 'Appointment Information')
            ->getStyle('D4')
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => '6495ed'
                    ],
                    'size' => 16,
                ],
            ]);
        $active_sheet->getStyle('A4:H4')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('f1dcdb');

        $active_sheet->setCellValue('J4', 'Prospect Information')
            ->getStyle('J4')
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => '6495ed'
                    ],
                    'size' => 16,
                ],
            ]);
        $active_sheet->getStyle('I4:Z4')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('e4dfec');

        // 5th Row: Headings
        // first row appointment headings
        $appointment_headings = [
            'Campaign',
            'Status',
            'Date (EST)',
            'Notes',
            'Opportunity Timeline',
            'Opportunity Value',
            'Sales Rep',
            'Appointment Feedback'
        ];
        $heading_row = 5;
        $column = 'A';
        foreach ($appointment_headings as $heading) {
            $active_sheet->setCellValue($column . $heading_row, $heading);
            $column++;
        }

        // first row prospect information headings
        $prospect_information_headings = [
            "Account Name",
            "Contact",
            "Format",
            "Title",
            "Direct Phone",
            "Office Phone",
            "Email",
            "Street",
            "City",
            "State",
            "Postal Code",
            "Country",
            "SDR",
            "Campaign Results Admin",
            "Campaign Results Director",
            "Distrubutor",
            "Date Created",
            "Date Modified",
        ];
        $heading_row = 5;
        $column = 'I';
        foreach ($prospect_information_headings as $heading) {
            $active_sheet->setCellValue($column . $heading_row, $heading);
            $column++;
        }
        $active_sheet->getStyle('A5:Z5')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('568ed3');
        $active_sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $active_sheet->getRowDimension('5')->setRowHeight(20);

// HERE

        $BStyle = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $active_sheet->getStyle('A1:Z3')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFFFF');

        $active_sheet->getStyle('A4:H4')->applyFromArray($BStyle);
        $active_sheet->getStyle('I4:Z4')->applyFromArray($BStyle);
        $active_sheet->getStyle('A5:H5')->applyFromArray($BStyle);
        $active_sheet->getStyle('I5:Z5')->applyFromArray($BStyle);
        $active_sheet->getStyle('A1:Z3')->applyFromArray($BStyle);
//for testing!!!!

    }

    /*
        Created by :    A.P. Massardo
        Date:           09-April-2016
        Description:    Formats cells and creates headers for the spread sheet,
                        for cells that are data dependent
    */
    private function setFormatAfterFill($active_sheet)
    {

        $RStyle = array(
            'borders' => array(
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $active_sheet->getStyle('H7:H' . $active_sheet->getHighestRow())->applyFromArray($RStyle);

        $active_sheet
            ->getStyle('C1:C' . $active_sheet->getHighestRow())
            ->getAlignment()
            ->setWrapText(false);

        $active_sheet
            ->getStyle('G1:G' . $active_sheet->getHighestRow())
            ->getAlignment()
            ->setWrapText(false);


        // Key / Legend
        $active_sheet->setCellValue('A' . ($active_sheet->getHighestRow() + 5), 'Key')
            ->getStyle('A' . ($active_sheet->getHighestRow()))
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);
        $active_sheet->setCellValue('A' . ($active_sheet->getHighestRow() + 1), 'Reschedule')
            ->getStyle('A' . ($active_sheet->getHighestRow()))
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF7F50');


        $active_sheet
            ->setCellValue('A' . ($active_sheet->getHighestRow() + 1), 'New Appointment')
            ->getStyle('A' . ($active_sheet->getHighestRow()))
            ->applyFromArray([
                'font' => [
                    'color' => [
                        'rgb' => 'FF0000'
                    ],
                ],
            ]);

        // border
        $active_sheet
            ->getStyle('A' . ($active_sheet->getHighestRow() - 2) . ':' . 'A' . $active_sheet->getHighestRow())
            ->applyFromArray([
                'borders' => [
                    'outline' => [
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    ]
                ]
            ]);


        foreach (range('A', $active_sheet->getHighestDataColumn()) as $columnID) {

            $active_sheet->getColumnDimension($columnID)->setAutoSize(false);

            if (($columnID == 'A')) {
                //$active_sheet->getColumnDimension($columnID)->setAutoSize(true);
                $active_sheet->getColumnDimension($columnID)->setWidth(COLUMN_WIDTH_A);
            } elseif (($columnID == 'B')) {
                //$active_sheet->getColumnDimension($columnID)->setAutoSize(true);
                $active_sheet->getColumnDimension($columnID)->setWidth(COLUMN_WIDTH_B);
            } elseif (($columnID == 'D') || ($columnID == 'H')) {
                //$active_sheet->getColumnDimension($columnID)->setAutoSize(false);
                $active_sheet->getColumnDimension($columnID)->setWidth(COLUMN_WIDTH_LONG);
            } else {
                //$active_sheet->getColumnDimension($columnID)->setAutoSize(false);
                $active_sheet->getColumnDimension($columnID)->setWidth(COLUMN_WIDTH_SHORT);
            }
        }

        // some settings
        $active_sheet->freezePane('A6'); // Freeze panes
        $active_sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1); // Rows to repeat at top

    }


    /**
     * Handles delivery of snapshot report
     * (delegates saving and reading configuration, as well as sending report)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reportDelivery(Request $request)
    {
        // retrieve the current user
        $current_user = session('users');

        // make sure that current user is unique
        if (count($current_user) != 1) {

            return response()->json([
                'success' => false,
                'message' => 'Multiple users found. Please contact your administrator.'
            ]);
        }

        // introduce the current user's id
        $this->current_user_id = reset($current_user);

        // check if loading configuration was requested
        if (!empty($request->input('load_configuration'))) {

            // retrieve current configuration
            return $this->getConfiguration();
        }

        // check if saving configuration was requested
        if (!empty($request->input('save_configuration'))) {

            // save configuration
            return $this->saveConfiguration($request->input('data'));
        }

        // check if sending report was requested
        if (!empty($request->input('send_report'))) {

            // save configuration
            return $this->sendReport();
        }

        // this line shouldn't ever be reached
        return response()->json([
            'success' => false,
            'message' => 'Action was not specified. Please contact your administrator.'
        ]);
    }

    /**
     * Retrieves report delivery configuration
     */
    private function getConfiguration()
    {
        // retrieve delivery report configuration (for current user)
        return Report::getDeliveryReportConfiguration($this->current_user_id);
    }

    /**
     * Saves delivery report configuration
     *
     * @param $data
     * @return array|bool|float|\Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface|JsonResponse|int|string
     */
    private function saveConfiguration($data)
    {
        // set portal user's ud
        $data['portal_user_id'] = $this->current_user_id;

        // save (edit) delivery report configuration
        return Report::saveDeliveryReportConfiguration($data);
    }

    /**
     * Triggers sending report
     */
    private function sendReport()
    {
        // triggers sending report
        return Report::sendReport($this->current_user_id);
    }

    /**
     * Delegates editing appointment
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function editAppointment(Request $request)
    {
        // introduce retrieved data
        $data = $request->input('data');

        $appointment_id = $request->input('appointment_id');

        // introduce post data
        $post_data = [];

        // iterate through retrieved data
        foreach ($data as $field => $value) {

            // check if set value is NULL
            // (value "" from dropdown gets interpreted as null when passed from ajax)
            if ($value == null) {

                // set proper value (valid dropdown option in sugar)
                $value = "";
            }

            // set post data
            $post_data[$field] = $value;
        }

        // try to edit appointment
        return Report::editAppointment($appointment_id, $post_data);
    }


}
