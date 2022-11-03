<?php
/**
 * @author Eontek DOO <office@eontek.co>
 * @copyright (c) Eontek DOO. All rights reserved.
 */
?>

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

@yield('title')


<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

</head>

<body>

<div class="container" style="padding-top: 30px;">

    {{--make sure that feedback is not already submitted--}}
    @if($feedback_exists)

        <div class="row">

            <div class="col-md-12">
                <div class="alert alert-info" role="alert">
                    <h4>Feedback has already been submitted for this appointment. Thank
                        you.</h4>
                </div>
            </div>

            <hr style="margin: 20px 0;">

            <div class="col-md-12">
                <h5>Thank you!</h5>
                <h5>Inside Sales Solutions</h5>
                <h5>Main Office: 347-363-6375</h5>
                <br>
            </div>

        </div>

    @else

        <div class="row">

            <div class="col-12">
                <h3>Meeting Feedback Request</h3>
                <h5>We appreciate your feedback!</h5>
                <hr style="margin: 20px 0;">
            </div>

        </div>

        @if($submitted)

            <div class="row">

                <div id="after_submit" class="col-12">
                    <div class="alert alert-success" role="alert">
                        <h4>Your Feedback Has Been Recorded</h4>
                    </div>
                </div>

            </div>


        @elseif($success)


            <div class="row">

                <div id="submitting_message" style="display: none" class="col-md-12">
                    <div class="alert alert-info" role="alert">
                        <h4>Your Feedback Is Being Submitted, Please Wait</h4>
                    </div>
                </div>

            </div>

            <div class="row" id="form_container">

                <div class="col-md-12">

                    <div class="row">

                        <div class="col-12">

                            <h5>We anticipate your meeting went well and look forward to
                                your
                                feedback.
                                Please complete the info below, it helps us improve what we're doing for you.</h5>

                            <hr style="margin: 20px 0;">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-12">

                            <form method="post" action="">

                                {{--pass the token--}}
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                {{--pass the app's id--}}
                                <input type="hidden" name="hidden_appointment_id" value='{{$appointment_id}}'>

                                <div class="form-group">
                                    <label>Was your meeting with someone that can make or influence future
                                        purchasing decisions?</label>
                                    <div class="radio">
                                        <label><input type="radio" value="Yes" name="influence"> Yes</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" value="No" name="influence"> No</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Did this appointment lead to a 2nd appointment or meeting?</label>
                                    <div class="radio">
                                        <label><input type="radio" value="Yes" name="appointment_lead"> Yes</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" value="No" name="appointment_lead"> No</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">How would you qualify the appointment?</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="qualify"
                                                   value="A_Good_introductory_meeting_that_may_offer_future_possibilities">
                                            A) Good introductory meeting that may offer future possibilities.
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="qualify"
                                                   value="B_Positive_step_in_terms_of_getting_your_foot_in_the_door">
                                            B) Positive step in terms of getting your foot in the door.
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="qualify"
                                                   value="C._Positive_because_the_contact_directed_us_to_another_decision_maker.">
                                            C) Positive because contact directed us to another decision maker.
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="qualify"
                                                   value="D_Negative_Not_worth_my_time.">
                                            D) Negative, not worth my time.
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        Did you identify an opportunity to pursue (Add details below as well
                                        please.)
                                    </label>
                                    <div class="radio">
                                        <label><input type="radio" value="Yes" name="pursue_opp"> Yes</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" value="No" name="pursue_opp"> No</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Potential Timeline:</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="timeline" value="SixMonths">
                                            Potential opportunity within 6 mos.
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="timeline" value="TwelveMonths">
                                            Potential opportunity within 12 mos.
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="timeline" value="Longer">
                                            Potential opportunity > 1 yr.
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Potential Opportunity Value:</label>
                                    <div class="radio">
                                        <label><input type="radio" name="opp_value" value="35"> Under $35K.</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="opp_value" value="75"> $35-75K</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="opp_value" value="150"> $75-$150K</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="opp_value" value="150_Plus"> $150K
                                            +</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="opp_value" value="400"> $400K +</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="opp_value" value="1m"> $1M +</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="opp_details">Opportunity Details (Please complete).</label>
                                    <textarea class="form-control" rows="5" name="opp_details"></textarea>
                                </div>

                                <!--<div class="form-group">
                                   <label for="">(Additional Details:) How it went and any next steps, challenges, current infrastructure, incumbents, projects, etc.</label>
                                   <textarea class="form-control" rows="5" id="additional_details" name="additional_details"></textarea>
                               </div>-->

                                <button id="submit_button" name="submit" type="submit" class="btn btn-primary">
                                    Submit
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        @else

            <div class="row">

                <div class="col-12">
                    <div class="alert alert-warning" role="alert">
                        <h4>{{$message}}</h4>
                    </div>
                </div>

            </div>

        @endif

        <div class="row">

            <div class="col-12">

                <hr style="margin: 20px 0;">

                <h5>Thank you!</h5>
                <h5>Inside Sales Solutions</h5>
                <h5>Main Office: 347-918-4747</h5>

            </div>

            <br><br>

        </div>

    @endif

</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>

<script>

  // attach listener on submit button
  $('#submit_button').click(function () {

    // hide the form
    $('#form_container').hide()

    // show the message
    $('#submitting_message').show()

  })

</script>

</body>

</html>
