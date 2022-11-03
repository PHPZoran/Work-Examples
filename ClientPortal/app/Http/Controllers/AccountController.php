<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AccountController
 *
 * @package App\Http\Controllers
 */
class AccountController extends Controller {

    /**
     * Generates default/landing page of the application
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|JsonResponse|\Illuminate\View\View
     */
    public function index(){

        // fetch campaigns
        $campaigns = Account::fetchCampaigns();

        // build data for the view
        $data = [
            'campaigns' => $campaigns,
        ];

        // send data to the report view
        return view('account.account', ['data' => $data]);
    }

    /**
     * Generates data for campaign director and actions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchLists(Request $request){

        // get frontend data
        $data = $request->all();

        $target_list = Account::fetchLists($data['campaign_id']);

        // fetch director info
        $contact_info = Account::fetchContactInfo($data['campaign_id']);

        $data = [
            'target_list' => $target_list,
            'contact_info' => $contact_info,
        ];

        // return response
        return response()->json($data);
    }

    /**
     * Fetches formatted list data for DataTables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request){

        // get frontend data
        $data = $request->all();

        // make sure that we have filters set
        if(empty($data['filters']))
            $data['filters'] = [];

        // get list data for datatables
        $target_list_data = Account::getList($data['target_list_id'], $data['filters']);

        // return data
        return response()->json($target_list_data);
    }

    /**
     * Removes relationship between tagrget list and contacts/accounts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function removeAccounts(Request $request){

        // get frontend data
        $data = $request->all();

        // make sure that we have target list and accounts
        if(empty($data['target_list_id']) || empty($data['accounts']))

            // return data
            return response()->json([], '400');

        Account::removeAccounts($data['target_list_id'], $data['accounts']);

        // return data
        return response()->json();
    }

    /**
     * Fetches formatted list data for DataTables
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAccountList(Request $request){

        // get frontend data
        $data = $request->all();

        // make sure that we have filters set
        if(empty($data['filters']))
            $data['filters'] = [];

        // get list data for datatables
        $account_data = Account::getAccountList($data['filters']);

        // return data
        return response()->json($account_data);
    }

    /**
     * Creates New Target List in SugarCRM via API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createNewList(Request $request){

        // get frontend data
        $data = $request->all();

        // get list data for datatables
        $account_data = Account::createNewList($data);

        // return data
        return response()->json($account_data);
    }

    /**
     * Updates existing target list in SugarCRM via API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function lockList(Request $request){

        // get frontend data
        $data = $request->all();

        // get list data for datatables
        $account_data = Account::lockList($data['target_list_id']);

        // return data
        return response()->json($account_data);
    }

    /**
     * Adds selected accounts to selected target list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addToList(Request $request){

        // get frontend data
        $data = $request->all();

        // get list data for datatables
        $account_data = Account::addToList($data['accounts'], $data['target_list_id']);

        // return data
        return response()->json($account_data);
    }

    public function deleteList(Request $request){

        // get frontend data
        $data = $request->all();

        $response = Account::deleteList($data['listid']);

        return response()->json($response);
    }
}