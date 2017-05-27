<?php

namespace App\Helpers;

use App\Models\PushNotificationsToken;
use App;

class SendPushNotification
{

    //------------CHANGED-------------------
    // (Android)API access key from Google API's Console.
    private static $API_ACCESS_KEY = 'AIzaSyCl9lkXrA__C7fz9cIui-odn4TPkVsHqC0';
    // (iOS) Private key's passphrase.
    private static $passphrase = '0000';
    // (Windows Phone 8) The name of our push channel.
    //private static $channelName = "joashp";
    //------------CHANGED-------------------

    /**
     * Sends Push notification for Android users
     * @param $data
     * @param $reg_id - array of all tokens for notifications
     * @return mixed
     */
    public function android($data, $reg_id)
    {
        $url = 'https://android.googleapis.com/gcm/send';
        $message = array(
            'title' => $data['title'],
            'message' => $data['body'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1,
        );

        $headers = array(
            'Authorization: key=' . self::$API_ACCESS_KEY,
            'Content-Type: application/json',
        );

        $fields = array(
            'registration_ids' => $reg_id,
            'data' => $message,
        );

        return self::useCurl($url, $headers, json_encode($fields));
    }

    /**
     * Sends Push notification for iOS users
     * http://stackoverflow.com/questions/20763514/sending-5000-push-notifications-at-same-time-keep-the-connection-to-apple-open
     * @param $data
     * @param $deviceTokens - array of tokens so we send it all at once without opening and closing connections
     * @return bool
     */
    public static function iOS($data, $deviceTokens)
    {
        //------------CHANGED-------------------
        if (App::isLocal()) {
            $applePushGateway = "ssl://gateway.sandbox.push.apple.com:2195";
            $ckpem = "ios_sandbox.pem";
        } else {
            $applePushGateway = "ssl://gateway.push.apple.com:2195";
            $ckpem = "ios_production.pem";
        }
        //------------CHANGED-------------------

        $ctx = stream_context_create();
        // ck.pem is your certificate file
        stream_context_set_option($ctx, 'ssl', 'local_cert', $ckpem);
        stream_context_set_option($ctx, 'ssl', 'passphrase', self::$passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            $applePushGateway, $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp) {
            return false;
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        }

        //------------CHANGED-------------------
        // Create the payload body
        $body['aps'] =
            [
                'alert' =>
                    [
                        'title' => $data['title'],
                        'body' => $data['body'],
                    ],
                'sound' => $data['sound'], //"message.wav"
            ];

        //only if badge is set, send it
        if (isset($data['badge'])) {
            $body['aps']['badge'] = (int)$data['badge'];
        }

        //this is used so you know which pin to open
        if (isset($data["pin_id"])) {
            $body["pin_id"] = (int)$data["pin_id"];
        }

        //------------CHANGED-------------------

        // Encode the payload as JSON
        $payload = json_encode($body);

        //if you have multiple tokens for one user or if you want to send notifications to more users take all the tokens you need and put in array and use foreach to send notification, this way is faster because connection to apple server is  opened during sending
        foreach ($deviceTokens as $deviceToken) {
            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
        }

        // Close the connection to the server
        fclose($fp);

        if (!$result) {
            return false;
        } else {
            return true;
        }

    }

    // Sends Push's toast notification for Windows Phone 8 users
    /*public static function WP($data, $uri)
    {
        $delay = 2;
        $msg   = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
        "<wp:Notification xmlns:wp=\"WPNotification\">" .
        "<wp:Toast>" .
        "<wp:Text1>" . htmlspecialchars($data['mtitle']) . "</wp:Text1>" .
        "<wp:Text2>" . htmlspecialchars($data['mdesc']) . "</wp:Text2>" .
            "</wp:Toast>" .
            "</wp:Notification>";

        $sendedheaders = array(
            'Content-Type: text/xml',
            'Accept: application/*',
            'X-WindowsPhone-Target: toast',
            "X-NotificationClass: $delay",
        );

        $response = self::useCurl($uri, $sendedheaders, $msg);

        $result = array();
        foreach (explode("\n", $response) as $line) {
            $tab = explode(":", $line, 2);
            if (count($tab) == 2) {
                $result[$tab[0]] = trim($tab[1]);
            }

        }

        return $result;
    }*/

    /**
     * CURL to other servers (Apple or Google)
     * @param $url
     * @param $headers
     * @param null $fields
     * @return mixed
     */
    private static function useCurl($url, $headers, $fields = null)
    {
        // Open connection
        $ch = curl_init();
        if ($url) {
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if ($fields) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }

            // Execute post
            $result = curl_exec($ch);
            if ($result === false) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);

            return $result;
        }
    }

    /**
     * send notification to a user
     * @param $user_id - id of a user from users
     * @param $data - notification payload
     * @return bool
     */
    public static function sendNotification($user_id, $data)
    {
        $tokens = PushNotificationsToken::getNotificationTokens($user_id);
        $return = true;
        $iOStokens = $Androidtokens = [];
        foreach ($tokens as $token) {
            /*if (!empty($token->token) && $token->device == "android") {
            $Androidtokens[] = $token->token;
            }*/

            if (!empty($token->token) && $token->device == "ios") {
                $iOStokens[] = $token->token;
            }

        }

        //only send notifications if there are tokens
        if (!empty($iOStokens)) {
            SendPushNotification::iOS($data, $iOStokens);
        }

        //send android notifications
        /*if (!empty($Androidtokens)) {
        $return = SendPushNotification::android($data, $Androidtokens);
        }*/

    }


}
