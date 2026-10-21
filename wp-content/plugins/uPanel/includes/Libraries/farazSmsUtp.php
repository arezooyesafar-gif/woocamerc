<?php

class farazSmsUtp {

    public const TOKEN=null;
    public const API_ADDRESS=null;
    public const SEND_SMS=null;
    public const SENDER=null;
    public const USERNAME=null;
    public const PASSWORD=null;


    public function __construct($username,$password,$token,$number,$forgot_pattern)
    {
        $this->FORGOT_PATTERN=$forgot_pattern;
        $this->TOKEN=$token;
        $this->SENDER=$number;
        $this->PASSWORD=$username;
        $this->USERNAME=$password;
        $this->API_ADDRESS="https://api.kavenegar.com/v1/".$this->TOKEN;
        $this->SEND_SMS=$this->API_ADDRESS."/sms/send.json";
    }

    public function sendSMS($receptor,$message,$code=null,$pattern_type)
    {
        $input_data = array('code' => $code);
        $from = $this->SENDER;
        if($pattern_type == 'login')
         $result = 'https://ippanel.com/patterns/pattern?username='.urlencode($this->PASSWORD).'&password='.urlencode($this->USERNAME).'&from='.urlencode($from).'&to='.urlencode(json_encode($receptor)).'&input_data='.urlencode(json_encode($input_data)).'&pattern_code='.urlencode($this->TOKEN);
		else if($pattern_type == 'password')
        $result = 'https://ippanel.com/patterns/pattern?username='.urlencode($this->PASSWORD).'&password='.urlencode($this->USERNAME).'&from='.urlencode($from).'&to='.urlencode(json_encode($receptor)).'&input_data='.urlencode(json_encode($input_data)).'&pattern_code='.urlencode($this->FORGOT_PATTERN);
$ch = curl_init($result); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
$data = curl_exec($ch);
curl_close($ch);
		
// https://ippanel.com/patterns/pattern?username=09121887662&password=Faraz@1361539186&from=3000505&to=09362735882&input_data={'code':5858}&pattern_code=2ketzfglh2xzy5k

//     print_r('https://ippanel.com/patterns/pattern?username='.urlencode($this->PASSWORD).'&password='.urlencode($this->USERNAME).'&from='.urlencode($from).'&to='.urlencode(json_encode($receptor)).'&input_data='.urlencode(json_encode($input_data)).'&pattern_code='.urlencode($this->TOKEN));
//     die();

        $returnValue=["status"=>"error","data"=>null,"message"=>null];
        // $return=json_decode($result);
        if((int)$data > 100000){
            hd_upanel_insert_sms_message(static::class,'0',$message,$code,$receptor);
               $returnValue["message"]="پیام با موفقیت ارسال شد";
               $returnValue["status"]="success";
           }
           else{
               $returnValue["message"]=$data;
           }


    
        return $returnValue;
    }
}