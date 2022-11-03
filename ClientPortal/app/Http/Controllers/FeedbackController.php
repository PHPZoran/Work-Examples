<?php
/**
 * @author Eontek DOO <office@eontek.co>
 * @copyright (c) Eontek DOO. All rights reserved.
 */

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spinegar\Sugar7Wrapper\Rest;

/**
 * Class FeedbackController
 *
 * @package App\Http\Controllers
 */
class FeedbackController extends Controller
{

    // introduce the error message
    // (end-users will see this message if anything goes sideways on feedback page)
    var $error_message = "Please click on the link you received in the email without changing it";

    /**
     * Generates default/landing page of the application
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // introduce the passed appointment id
        $appointment_id = $request->input('appointment_id');

        // make sure that id was actually passed
        if (empty($appointment_id)) {

            return view('feedback.feedback', [
                'success' => false,
                'message' => $this->error_message,
                'feedback_exists' => false,
                'submitted' => false
            ]);
        }

        // retrieve appointment's data
        $appointment_data = $this->getAppointment($appointment_id);

        $appointment_data['submitted'] = false;

        // send data to the report view
        return view('feedback.feedback', $appointment_data);
    }

    /**
     * Retrieves appointment data
     *
     * @param $appointment_id
     * @return array|bool
     */
    private function getAppointment($appointment_id)
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
            return ['success' => false, 'message' => $this->error_message, 'feedback_exists' => false];
        }

        // try to retrieve appointment
        try {

            // introduce the sugar endpoint (which will retrieve appointment)
            $endpoint = "ATC_Appointments/{$appointment_id}";

            // introduce data that'll be passed to the endpoint
            $data = array(

                // retrieve only this one field
                'fields' => 'feedback_status_c'
            );

            // try to retrieve app.
            $appointment_data = $connector->getEndpoint($endpoint, $data);

        } catch (\Exception $exception) {

            // return error message
            return ['success' => false, 'message' => $this->error_message, 'feedback_exists' => false];
        }

        // determine if feedback for this appointment already exists
        $feedback_exists = $appointment_data['feedback_status_c'] == "received";

        // return response
        return ['success' => true, 'feedback_exists' => $feedback_exists, 'appointment_id' => $appointment_id];
    }

    /**
     * Handles submitting the feedback form
     *
     * @param Request $request
     * @return array
     */
    public function submitFeedback(Request $request)
    {
        // introduce the form data
        $form_data = $request->all();

        // make sure that app. id is set
        if (empty($form_data['hidden_appointment_id'])) {
            return view('feedback.feedback', [
                'success' => false,
                'feedback_exists' => false,
                'submitted' => false,
                'message' => $this->error_message
            ]);
        }

        // introduce the appointment's id
        // (this var is a POST var that was hidden on the form;
        // we also have appointment_id var and it's passed in url - so it's not safe to use that)
        $appointment_id = $form_data['hidden_appointment_id'];

        // introduce the appointment's data
        $appointment_data = [
            'feedback_status_c' => "received",
        ];

        if (isset($form_data['influence'])) {
            $appointment_data['dm_qualified_c'] = $form_data['influence'];
        }

        if (isset($form_data['appointment_lead'])) {
            $appointment_data['second_appointment_c'] = $form_data['appointment_lead'];
        }

        if (isset($form_data['qualify'])) {
            $appointment_data['positive_appointment_c'] = $form_data['qualify'];
        }

        if (isset($form_data['timeline'])) {
            $appointment_data['appointment_result_c'] = $form_data['timeline'];
        }

        if (isset($form_data['opp_value'])) {
            $appointment_data['opportunity_amount'] = $form_data['opp_value'];
        }

        if (isset($form_data['opp_details'])) {
            $appointment_data['appointment_feedback'] = $form_data['opp_details'];
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
            return view('feedback.feedback', [
                'success' => false,
                'feedback_exists' => false,
                'submitted' => false,
                'message' => $this->error_message
            ]);
        }

        // try to update appointment
        try {

            // introduce the sugar endpoint (which will update appointment)
            $endpoint = "ATC_Appointments/{$appointment_id}";

            // try to update app.
            $connector->putEndpoint($endpoint, $appointment_data);

        } catch (\Exception $exception) {

            // return error message
            return view('feedback.feedback', [
                'success' => false,
                'feedback_exists' => false,
                'submitted' => false,
                'message' => $this->error_message
            ]);
        }

        // return response
        return view('feedback.feedback', ['success' => true, 'feedback_exists' => false, 'submitted' => true]);
    }

}