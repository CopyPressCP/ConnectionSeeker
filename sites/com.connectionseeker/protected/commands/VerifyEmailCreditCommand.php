<?php
/*
D:\WEBIDE\Language\php>php -q K:\NewHtdocs\yii\yii1.1.8.dev\sites\com.connectionseeker\cron.php VerifyEmailCredit
*/
Yii::import('application.vendors.*');

class VerifyEmailCreditCommand extends CConsoleCommand {
    public function run($args) {
        /*
        $username	= 'kzipp-cp';
        $password	= '!Steel99';
        $email		= 'kylezipp@gmail.com';
        $api_url	= 'http://api.verify-email.org/api.php?';
        $url		= $api_url . 'usr=' . $username . '&pwd=' . $password . '&check=' . $email;
        */
        $url = "http://api.verify-email.org/api.php?usr=kzipp-cp&pwd=!Steel99&check=kylezipp@gmail.com";

        /*
        the response is received in JSON format; 
        We use the function remote_get_contents($url) to detect in witch way to get the remote content
        */
        $object		= json_decode(remote_get_contents($url));

        /*
        echo 'The email address ' . $email . ' is ' . ($object->verify_status?'GOOD':'BAD or cannot be verified') . '  '; 
        echo 'authentication_status - ' . $object->authentication_status . ' (your authentication status: 1 - success; 0 - invalid user)'; 
        echo 'limit_status - ' . $object->limit_status . ' (1 - verification is not allowed, see limit_desc; 0 - not limited)'; 
        echo 'limit_desc - ' . $object->limit_desc . ' '; 
        echo 'verify_status - ' . $object->verify_status . ' (entered email is: 1 - OK; 0 - BAD)'; 
        echo 'verify_status_desc - ' . $object->verify_status_desc . ' ';
        */

        if ($object->authentication_status == 0 || $object->limit_status == 1) {
            //Send one notice email to kyle;
            echo $content =  "Verification is not allowed Or invalid user Or these is no more credits, Please contact the administrator of Verify-Email.";

            Utils::notice(array('content'=>$content, 'tos'=>'kzipp@copypress.com', 'cc'=>false,
                                'subject'=>'Verify-Email: Verification is not allowed OR invalid user'));

        } else {
            //do nothing for now;
            echo "Verify-Email working correctly.";
        }
    }
}


function remote_get_contents($url) {
    if (function_exists('curl_get_contents') AND function_exists('curl_init')) {
        return curl_get_contents($url);
    } else {
        // A litte slower, but (usually) gets the job done
        return file_get_contents($url);
    }
}

function curl_get_contents($url){
    // Initiate the curl session
    $ch = curl_init();

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, $url);

    // Removes the headers from the output
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // Return the output instead of displaying it directly
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute the curl session
    $output = curl_exec($ch);

    // Close the curl session
    curl_close($ch);

    // Return the output as a variable
    return $output;
}

?>