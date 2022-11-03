<?php

/*Contains 2 functions for Discord webhook calls. One with RichEmbed and one without.
  Include once to use on any page.
*/
//error_reporting(0);
//DM Channel
$dmURL = "https://discordapp.com/api/webhooks/452286592569180172/q1I-Xb0M_Qfne9bTW4knKX22wrFhlGf4yPyiseDGoYF-zX76toG1Cp74z2FGcHLj4gwz";

//Mord Mystery Channel
$adminURL = "https://discordapp.com/api/webhooks/676852516025139232/ZhskCKtCYDazi83j3rDamsGlmkQjeT4xEoKHML0T0HqUHgtUgLHYq_g4pCYlbEN3pgqv";

$testURL = "https://discordapp.com/api/webhooks/677402664060846132/mZiVXv9PMoK4DF8GMskIDoLUIg_KmO2nxFP6ccgcNd8Zg7QXDm-3m41sHn81JDbC3yHO";

//if(parse_ini_file('/web_info/web.ini')){
//    $ini = parse_ini_file('/web_info/web.ini');
//}else{
//    $ini = parse_ini_file('web.ini');
//}

function DiscordPageQuery($username, $ip, $url, $inputs="N/A", $color="55FF00"){

  $hookObject = json_encode([
      /*
       * An array of Embeds
       */
      "embeds" => [
          /*
           * Our first embed
           */
          [
              // Set the title for your embed
              "title" => "Page Query",

              // A description for your embed
              "description" => "`User:` " . $username . "\r\n`IP:` " . $ip . "\r\n`Inputs:` " . $inputs . "\r\n[".basename($_SERVER["PHP_SELF"])."](http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI])",

              // The integer color to be used on the left side of the embed
              "color" => hexdec( $color ),

          ]
      ]

  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

  $ch = curl_init();

  curl_setopt_array( $ch, [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $hookObject,
      CURLOPT_HTTPHEADER => [
      "Content-Type: application/json"
    ]
  ]);

  $response = curl_exec( $ch );
  curl_close( $ch );

}

function DiscordBanNotif($username, $url, $isBan, $inputs, $color="FF0000"){
    if($isBan){
        $banString = " has banned ";
    }else{
        $banString = " has unbanned ";
    }
    $hookObject = json_encode([
        /*
         * An array of Embeds
         */
        "embeds" => [
            /*
             * Our first embed
             */
            [
                // Set the title for your embed
                "title" => "Ban Notification",

                // A description for your embed
                "description" => $username . $banString  .  $inputs . "\r\n[".basename($_SERVER["PHP_SELF"])."](http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI])",

                // The integer color to be used on the left side of the embed
                "color" => hexdec( $color ),

            ]
        ]

    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

    $ch = curl_init();

    curl_setopt_array( $ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $hookObject,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ]
    ]);

    $response = curl_exec( $ch );
    curl_close( $ch );

}

function DiscordWarning($username, $ip, $warning, $url, $color="FF0000", $pingee="\@everyone"){
  $hookObject = json_encode([

      "content" => "**WARNING** $pingee",
      /*
       * An array of Embeds
       */
      "embeds" => [
          /*
           * Our first embed
           */
          [
              // Set the title for your embed
              "title" => $warning,

              // A description for your embed
              "description" => "`User:` " . $username . "\r\n`IP:` " . $ip . "\r\n[".basename($_SERVER["PHP_SELF"])."](http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI])\r\n".$_SERVER["SERVER_NAME"],

              // The integer color to be used on the left side of the embed
              "color" => hexdec( $color ),

          ]
      ]

  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

  $ch = curl_init();

  curl_setopt_array( $ch, [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $hookObject,
      CURLOPT_HTTPHEADER => [
      "Content-Type: application/json"
    ]
  ]);

  $response = curl_exec( $ch );
  curl_close( $ch );
}

function SendRichEmbedMessageToDiscord($content, $title, $description, $color="FFFFFF", $url="https://discordapp.com/api/webhooks/676852516025139232/ZhskCKtCYDazi83j3rDamsGlmkQjeT4xEoKHML0T0HqUHgtUgLHYq_g4pCYlbEN3pgqv"){

  $hookObject = json_encode([
      /*
       * The general "message" shown above your embeds
       */
      "content" => $content,

      /*
       * An array of Embeds
       */
      "embeds" => [
          /*
           * Our first embed
           */
          [
              // Set the title for your embed
              "title" => $title,

              // A description for your embed
              "description" => $description,

              // The integer color to be used on the left side of the embed
              "color" => hexdec( $color ),

          ]
      ]

  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

  $ch = curl_init();

  curl_setopt_array( $ch, [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $hookObject,
      CURLOPT_HTTPHEADER => [
      "Content-Type: application/json"
    ]
  ]);

  $response = curl_exec( $ch );
  curl_close( $ch );

}

function SendMessageToDiscord($content, $url="https://discordapp.com/api/webhooks/676852516025139232/ZhskCKtCYDazi83j3rDamsGlmkQjeT4xEoKHML0T0HqUHgtUgLHYq_g4pCYlbEN3pgqv"){

  $hookObject = json_encode([
      /*
       * The general "message" shown above your embeds
       */
      "content" => $content,

  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

  $ch = curl_init();

  curl_setopt_array( $ch, [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $hookObject,
      CURLOPT_HTTPHEADER => [
      "Content-Type: application/json"
    ]
  ]);

  $response = curl_exec( $ch );
  curl_close( $ch );

}

$ClientIPaddress = '';
    if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
        $ClientIPaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
        $ClientIPaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (array_key_exists('HTTP_X_FORWARDED' , $_SERVER))
        $ClientIPaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (array_key_exists('HTTP_FORWARDED_FOR', $_SERVER))
        $ClientIPaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (array_key_exists('HTTP_FORWARDED', $_SERVER))
        $ClientIPaddress = $_SERVER['HTTP_FORWARDED'];
    else if (array_key_exists('REMOTE_ADDR', $_SERVER))
        $ClientIPaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ClientIPaddress = 'UNKNOWN';


function ArLog($message){

  $pathToLogs = "/web_info/DMLogs/" . date("Y-m-d") . ".txt";

  if(!is_dir("/web_info/DMLogs/"))
  mkdir("/web_info/DMLogs/", 0700, true);

  $logFile = fopen($pathToLogs, "a+");

  fwrite($logFile, $message . "\r\n");
  fclose($logFile);

}

function ArLogTimeStamped($message){

  $timeStampedMessage = "[" . date("Y-m-d h:i:sa") ."] " . $message;

  ArLog($timeStampedMessage);

}


function ArLogStandard($user, $ip, $inputs=""){

  if(!$inputs)
  $inputs="N/A";

  $standardMess = "User: " . $user . " IP: " . $ip . " Page: " . basename($_SERVER["PHP_SELF"]) . " Inputs: " . $inputs;

  ArLogTimeStamped($standardMess);
}


?>
