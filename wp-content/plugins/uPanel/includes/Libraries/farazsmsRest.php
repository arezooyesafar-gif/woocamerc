<?php
class farazSmsRest {

    public const USERNAME=null;
    public const PASSWORD=null;
    public const SENDER=null;
    const SEND_REST="https://api.iranpayamak.com/ws/v1/sms/simple";

    public function __construct($username,$password,$token,$number,$forgot_pattern)
    {
        $this->FORGOT_PATTERN=$forgot_pattern;
        $this->PASSWORD=$password; // Not used in new API
        $this->USERNAME=$username; // Not used in new API
        $this->SENDER=$number; // This will be the line_number
        $this->TOKEN=$token; // This will be the API Key
    }

    public function sendSMS($receptor,$message,$code=null,$pattern)
    {
        // Prepare the message text
        $text = ($code == null) ? $message : str_replace("X", $code, $message);
        
        // Prepare recipients array
        $recipients = array($receptor);
        
        // Prepare the request data
        $postData = array(
            'text' => $text,
            'line_number' => $this->SENDER,
            'recipients' => $recipients,
            'number_format' => 'english'
        );

        // Initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => self::SEND_REST,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Api-Key: ' . $this->TOKEN,
                'Content-Type: application/json'
            ),
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($result, true);
        $returnValue = ["status" => "error", "data" => null, "message" => null];

        // Check if the request was successful
        // Adjust this condition based on the actual success response from the new API
        if (isset($return) && !isset($return['error'])) {
            hd_upanel_insert_sms_message(static::class, '0', $message, $code, $receptor);
            $returnValue["message"] = "پیام با موفقیت ارسال شد";
            $returnValue["status"] = "success";
            $returnValue["data"] = $return;
        } else {
            $returnValue["message"] = "خطا در ارسال پیامک";
            $returnValue["data"] = $result;
        }
        
        return $returnValue;
    }
}