
<?php



class smsirUtp {

    public const TOKEN=null;
    public const SENDER=null;
    const SEND_REST="https://api.sms.ir/v1/send/verify";

    public function __construct($username,$password,$token,$number,$forgot_pattern)
    {
        
 $this->FORGOT_PATTERN=$forgot_pattern;

        $this->TOKEN=$token;
        $this->USERNAME=$username; 
        $this->SENDER=$number;// in template hastesh
    }

    public function sendSMS($receptor,$message,$code=null,$pattern_type)
    {

   
$curl = curl_init();
curl_setopt_array($curl, array(
CURLOPT_URL => 'https://api.sms.ir/v1/send/verify',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_POSTFIELDS =>'{
"mobile": "'.$receptor.'",
"templateId": '.(($pattern_type == 'login')?$this->SENDER:$this->FORGOT_PATTERN).',
"parameters": [{"name": "CODE","value": "'.$code.'"}]}',
CURLOPT_HTTPHEADER => array(
'Content-Type: application/json',
'Accept: text/plain',
'x-api-key: '.$this->TOKEN
),
));
$response = curl_exec($curl);
$response = json_decode($response);
curl_close($curl);

        if($response->status == 1){

                hd_upanel_insert_sms_message(static::class,'0',$message,$code,$receptor);
                $returnValue["message"]="پیام با موفقیت ارسال شد";
                $returnValue["status"]="success";
       
         
        }else{
           
            $returnValue["message"]="خطا در ارسال پیامک";

        }
        return $returnValue;
    }
}