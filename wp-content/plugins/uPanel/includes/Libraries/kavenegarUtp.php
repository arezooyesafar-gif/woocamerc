<?php

class kavenegarUtp {

    public const TOKEN=null;
    public const API_ADDRESS=null;
    public const SEND_SMS=null;
    public const SENDER=null;


    public function __construct($username,$password,$token,$number,$forgot_pattern)
    {
        
$this->FORGOT_PATTERN=$forgot_pattern;
  
        $this->TOKEN=$token;
        $this->SENDER=$number;
        $this->API_ADDRESS="https://api.kavenegar.com/v1/".$this->TOKEN;
        $this->SEND_SMS=$this->API_ADDRESS."/sms/send.json";
    }

    public function sendSMS($receptor,$message,$code=null,$pattern_type)
    {



        if($pattern_type == 'login')
        $result = file_get_contents('https://api.kavenegar.com/v1/'.$this->TOKEN.'/verify/lookup.json?receptor='.$receptor.'&token='.$code.'&template='.$this->SENDER);
		else if($pattern_type == 'password')
        $result = file_get_contents('https://api.kavenegar.com/v1/'.$this->FORGOT_PATTERN.'/verify/lookup.json?receptor='.$receptor.'&token='.$code.'&template='.$this->SENDER);

    
        $returnValue=["status"=>"error","data"=>null,"message"=>null];
        $return=json_decode($result);
        if($return->return->status==200){
            hd_upanel_insert_sms_message(static::class,'0',$message,$code,$receptor);
               $returnValue["message"]="پیام با موفقیت ارسال شد";
               $returnValue["status"]="success";
           }
           else{
               $returnValue["message"]=$return->return->message;
           }


    
        return $returnValue;
    }
}