<?php

class melipayamakUtp {


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
        
    

        if($pattern_type == 'login'){

            $result = file_get_contents('https://api.payamak-panel.com/post/Send.asmx/SendByBaseNumber2?username='.$this->PASSWORD.'&password='.urlencode($this->USERNAME).'&text='.$code.'&to='.$receptor.'&bodyId='.$this->SENDER);
            if($result === false){
                $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_URL, 'https://api.payamak-panel.com/post/Send.asmx/SendByBaseNumber2?username='.$this->PASSWORD.'&password='.urlencode($this->USERNAME).'&text='.$code.'&to='.$receptor.'&bodyId='.$this->SENDER);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($cURLConnection);
            curl_close($cURLConnection);
            }
        }
        else if($pattern_type == 'password'){

            $result = file_get_contents('https://api.payamak-panel.com/post/Send.asmx/SendByBaseNumber2?username='.$this->PASSWORD.'&password='.urlencode($this->USERNAME).'&text='.$code.'&to='.$receptor.'&bodyId='.$this->FORGOT_PATTERN);
if($result === false){
	$cURLConnection = curl_init();
curl_setopt($cURLConnection, CURLOPT_URL, 'https://api.payamak-panel.com/post/Send.asmx/SendByBaseNumber2?username='.$this->PASSWORD.'&password='.urlencode($this->USERNAME).'&text='.$code.'&to='.$receptor.'&bodyId='.$this->FORGOT_PATTERN);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($cURLConnection);
curl_close($cURLConnection);
}
        }




    
        $returnValue=["status"=>"error","data"=>null,"message"=>null];
    
        if(strlen($result) > 10 ){
            hd_upanel_insert_sms_message(static::class,'0',$message,$code,$receptor);
               $returnValue["message"]="پیام با موفقیت ارسال شد";
               $returnValue["status"]="success";
           }
           else{
               $returnValue["message"]=$result;
           }


    
        return $returnValue;
    }
}