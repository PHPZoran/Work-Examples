<?php
$main_ctr              = "display:block";
$already_submitted_ctr = "display:none";
$username     = 'chris@mastersolve.com';
$password     = '888888cc';
$instance_url = 'https://crm.isaless.com/rest/v10';
//Login - POST /oauth2/token
$auth_url     = $instance_url . "/oauth2/token";
$oauth2_token_arguments = array(
    "grant_type" => "password",
    "client_id" => "sugar",
    "client_secret" => "",
    "username" => $username,
    "password" => $password,
    "platform" => "api"
);
$auth_request = curl_init($auth_url);
curl_setopt($auth_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
curl_setopt($auth_request, CURLOPT_HEADER, false);
curl_setopt($auth_request, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($auth_request, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($auth_request, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($auth_request, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json"
));
//convert arguments to json
$json_arguments = json_encode($oauth2_token_arguments);
curl_setopt($auth_request, CURLOPT_POSTFIELDS, $json_arguments);
//execute request
$oauth2_token_response = curl_exec($auth_request);
//decode oauth2 response to get token
$oauth2_token_response_obj = json_decode($oauth2_token_response);
$oauth_token               = $oauth2_token_response_obj->access_token;
$id = $_GET['appointment_id'];
//Get Record - GET //:record
$url = $instance_url . "/ATC_Appointments/$id";
//Setup request to only return some fields on module
$data = array(
    'fields' => 'feedback_status_c'
);
//Add data to the URL
$url = $url . "?" . http_build_query($data);
$curl_request = curl_init($url);
curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
curl_setopt($curl_request, CURLOPT_HEADER, false);
curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($curl_request, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "oauth-token: {$oauth_token}"
));
//execute request
$curl_response = curl_exec($curl_request);
//decode json
$record        = json_decode($curl_response);
//display the created record
if ($record->feedback_status_c == "received") {
    $already_submitted_ctr = "display:block";
    $main_ctr              = "display:none";
}
curl_close($curl_request);
?>

<html>
    <head>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
        <title>Feedback Form</title>
    </head>
    <body>
        <br>

        <div style="<?php
echo $already_submitted_ctr;
?>" class="container">

             <div class="col-md-12">
                  <div class="alert alert-success" role="alert">
                      <h4>Feedback has already been submitted for this appointment.Thank you.</h4>
                  </div>
              </div>

              <hr>
            
             <div class="col-md-12">
                <h5>Thank you!</h5>
                <h5>Inside Sales Solutions</h5>
                <h5>Main Office: 347-363-6375</h5>
                <br>
             </div>

        </div>

        <div style="<?php
echo $main_ctr;
?>" class="container">
            <div class="col-md-12">
                <h3>Meeting Feedback Request</h3>
                <h5>We appreciate your feedback!</h5>
                <hr>
            </div>
            
            <?php
if (isset($_POST['submit'])) {
    echo '<div class="col-md-12">
                                 <div class="alert alert-success" role="alert">
                                     <h4>Your Feedback Has Been Recorded</h4>
                                  </div>
                           </div>';
} else {
    
?>



            <div  class="col-md-12">
                <h5>We anticipate your meeting went well and look forward to your feedback.
                 Please complete the info below, it helps us improve what we're doing for you.</h5>
			</div>
			<div class="col-md-12">
				 <h5>Please inform Inside Sales Solutions if an attended appointment was not qualified, within 72 hours of the appointment time. 
				 Inside Sales Solutions will then generate a replacement appointment.<h5>
                <hr>
            </div>

					<h4>
            <div id="form_container" class="col-md-12">
                <form method="post" >
                      <div class="form-group">
                         <label for="">Was your meeting with a decision maker or key influencer for your offering?</label>
                         <div class="radio">
                             <label><input type="radio" value="Yes" name="influence">Yes</label>
                         </div>
                        <div class="radio">
                             <label><input type="radio" value="No" name="influence">No</label>
                        </div> 
                      </div>  

                       <div class="form-group">
                         <label for="">Did this appointment lead to a 2nd appointment or meeting?</label>
                         <div class="radio">
                             <label><input type="radio" value="Yes" name="appointment_lead">Yes</label>
                         </div>
                        <div class="radio">
                             <label><input type="radio" value="No" name="appointment_lead">No</label>
                        </div>
                      </div>

                      <div class="form-group">
                          <label for="">How would you qualify the appointment?</label>
                           <div class="radio">
                             <label><input type="radio" name="qualify" value="A_Good_introductory_meeting_that_may_offer_future_possibilities">A) Good introductory meeting that may offer future possibilities.</label>
                           </div>
                           <div class="radio">
                             <label><input type="radio"  name="qualify" value="B_Positive_step_in_terms_of_getting_your_foot_in_the_door">B) Positive step in terms of getting your foot in the door.</label>
                           </div>
                           <div class="radio">
                             <label><input type="radio"  name="qualify" value="C._Positive_because_the_contact_directed_us_to_another_decision_maker.">C) Positive because contact directed us to another decision maker.</label>
                           </div>
                           <div class="radio">
                             <label><input type="radio"  name="qualify" value="D_Negative_Not_worth_my_time.">D) Negative, not worth my time.</label>
                           </div>
                      </div>

                      <div class="form-group">
                         <label for="">Did you identify an opportunity to pursue (Add details below as well please.)</label>
                         <div class="radio">
                             <label><input type="radio" value="Yes" name="pursue_opp">Yes</label>
                         </div>
                        <div class="radio">
                             <label><input type="radio" value="No" name="pursue_opp">No</label>
                        </div>
                      </div>

                      
                      <div class="form-group">
                          <label for="">Potential Timeline:</label>
                           <div class="radio">
                             <label><input type="radio" name="timeline" value="SixMonths">Potential opportunity within 6 mos.</label>
                           </div>
                           <div class="radio">
                             <label><input type="radio" name="timeline" value="TwelveMonths">Potential opportunity within 12 mos.</label>
                           </div>
                           <div class="radio">
                             <label><input type="radio" name="timeline" value="Longer">Potential opportunity > 1 yr.</label>
                           </div>
                      </div>

                       <div class="form-group">
                          <label for="">Potential Opportunity Value:</label>
                           <div class="radio">
                             <label><input type="radio" name="opp_value" value="35">Under $35K.</label>
                           </div>
                           <div class="radio">
                             <label><input type="radio" name="opp_value" value="75">$35-75K</label>
                           </div>
                           <div class="radio">
                             <label><input type="radio"  name="opp_value"  value="150">$75-$150K</label>
                           </div>
                            <div class="radio">
                             <label><input type="radio"  name="opp_value" value="150_Plus">$150K +</label>
                           </div>
                            <div class="radio">
                             <label><input type="radio"  name="opp_value" value="400">$400K +</label>
                           </div>
                           <div class="radio">
                             <label><input type="radio"  name="opp_value" value="1m">$1M +</label>
                           </div>
                      </div>

                      <div class="form-group">
                          <label for="">Opportunity Details (Please complete).</label>
                          <textarea class="form-control" rows="5" name="opp_details"></textarea>
                      </div>

                       <!--<div class="form-group">
                          <label for="">(Additional Details:) How it went and any next steps, challenges, current infrastructure, incumbents, projects, etc.</label>
                          <textarea class="form-control" rows="5" id="additional_details" name="additional_details"></textarea>
                      </div>-->
                      
                      <button id="submit_button" name="submit" type="submit" class="btn btn-primary">Submit</button> 
                </form>

                <?php
}
?>

                <hr>

                <h5>Thank you!</h5>
                <h5>Inside Sales Solutions</h5>
                <h5>Main Office: 347-918-4747</h5>
                <br>

            </div>

            <?php 
               if(isset($_POST['submit'])){
                   
                  $form_response="";
                  if(isset($_POST['influence'])){
                     $form_response.="Was your meeting with someone that can make or influence future purchasing decisions?";
                      if($_POST['influence']=="Yes"){
                        $form_response.="\nYes";
                      }else{
                         $form_response.="\nNo";
                      }
                  }
                  if(isset($_POST['appointment_lead'])){
                     $form_response.="\nDid this appointment lead to a 2nd appointment or meeting?";
                     if($_POST['appointment_lead']=="Yes"){
                        $form_response.="\nYes";
                     }else{
                        $form_response.="\nNo";
                     }
                  }
                 
                  if(isset($_POST['qualify'])){
                     $value_map=array(
                         "A_Good_introductory_meeting_that_may_offer_future_possibilities"=>"A) Good introductory meeting that may offer future possibilities.",
                         "B_Positive_step_in_terms_of_getting_your_foot_in_the_door"=>"B) Positive step in terms of getting your foot in the door.",
                         "C._Positive_because_the_contact_directed_us_to_another_decision_maker."=>"C) Positive because contact directed us to another decision maker.",
                         "D_Negative_Not_worth_my_time."=>"D) Negative, not worth my time."
                     );
                    
                     $form_response.="\nHow would you qualify the appointment?\n";
                     $qualify = $value_map[$_POST['qualify']];
                     $form_response.=$qualify; 
                  }
                  if(isset($_POST['pursue_opp'])){
                     $form_response.="\nDid you identify an opportunity to pursue (Add details below as well please.)";
                     if($_POST['pursue_opp']=="Yes"){
                       $form_response.="\nYes";
                     }else{
                       $form_response.="\nNo";
                     }
                  }
                  if(isset($_POST['timeline'])){
                     $form_response.="\nPotential Timeline:\n";
                     $timeline = $_POST['timeline'];
                     $form_response.=$timeline;
                  }
                  if(isset($_POST['opp_value'])){
                    
                    $value_map=array(
                       "35"=>"Under 35k",
                       "75"=>"35k-75k",
                       "150"=>"75k-150k",
                       "150_Plus"=>"150k +",
                       "400"=>"400k +",
                       "1m"=>"1mplus"
                       
                    );
               
                    $form_response.="\nPotential Opportunity Value:\n";
                    $opp_value = $value_map[$_POST['opp_value']];
                    $form_response.=$opp_value;
                  }
                  if(isset($_POST['opp_details'])){
                     $form_response.="\nOpportunity Details (Please complete).";
                     $form_response.="\n".$_POST['opp_details'];
                  }
                 
                 /* if(isset($_POST['additional_details'])){
                     $form_response.="\n(Additional Details:) How it went and any next steps, challenges, current infrastructure, incumbents, projects, etc.";
                     $form_response.="\n".$_POST['additional_details'];
                 }*/
                  if($form_response!=""){
                     
                     $username = 'chris@mastersolve.com';
                     $password = '888888cc';
                     $instance_url = 'https://crm.isaless.com/rest/v10';
			//Login - POST /oauth2/token
			$auth_url = $instance_url . "/oauth2/token";
			$oauth2_token_arguments = array(
				"grant_type" => "password",
				//client id - default is sugar. 
				//It is recommended to create your own in Admin > OAuth Keys
				"client_id" => "sugar", 
				"client_secret" => "",
				"username" => $username,
				"password" => $password,
				//platform type - default is base.
				//It is recommend to change the platform to a custom name such as "custom_api" to avoid authentication conflicts.
				"platform" => "api" 
			);
			$auth_request = curl_init($auth_url);
			curl_setopt($auth_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($auth_request, CURLOPT_HEADER, false);
			curl_setopt($auth_request, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($auth_request, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($auth_request, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($auth_request, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json"
			));
			//convert arguments to json
			$json_arguments = json_encode($oauth2_token_arguments);
			curl_setopt($auth_request, CURLOPT_POSTFIELDS, $json_arguments);
			//execute request
			$oauth2_token_response = curl_exec($auth_request);
			//decode oauth2 response to get token
			$oauth2_token_response_obj = json_decode($oauth2_token_response);
			$oauth_token = $oauth2_token_response_obj->access_token;
                        
                        $id = $_GET['appointment_id'];
		
                 	//Update Record - PUT /<module>/:record
			$url = $instance_url . "/ATC_Appointments/$id";
                 	//Set up the Record details
			if($_POST['influence'] == 'Yes'){
				$record->dm_qualified_c = 'Yes';
			}
			elseif($_POST['influence'] == 'No'){
				$record->dm_qualified_c = 'No';
			}
			else{
				$record->dm_qualified_c = '';
			}
			$record->appointment_feedback = $form_response;
                        $record->feedback_status_c = "received";
                        $record->second_appointment_c=$_POST['appointment_lead'];
                        $record->positive_appointment_c=$_POST['qualify'];
                        $record->opportunity_amount=$_POST['opp_value'];
                        $record->appointment_result_c=$_POST['timeline'];
                     
			$curl_request = curl_init($url);
			curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($curl_request, CURLOPT_HEADER, false);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"oauth-token: {$oauth_token}"
			));
			//convert arguments to json
			$json_arguments = json_encode($record);
			curl_setopt($curl_request, CURLOPT_POSTFIELDS, $json_arguments);
			//execute request
			$curl_response = curl_exec($curl_request);
			//decode json
			$updatedRecord = json_decode($curl_response);
			//display the created 
			curl_close($curl_request);         
                       
 
                  }
               }
               
            ?>
        </div>
        

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    </body>
</html>
