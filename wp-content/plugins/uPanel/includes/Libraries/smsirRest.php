<?php

class smsirRest {

    public const TOKEN=null;
    public const SENDER=null;
    const SEND_REST="https://api.sms.ir/v1/send/bulk";

    public function __construct($username,$password,$token,$number)
    {
        $this->TOKEN=$token;
        $this->SENDER=$number;
    }

    public function sendSMS($receptor,$message,$code=null,$pattern)
    {

        $args = array(
              'headers' => array(
                  'Content-Type' => "application/json",
                  'x-api-key' => $this->TOKEN,
              ),
            'body'=> json_encode([
                "lineNumber"=>$this->SENDER,
                "Mobiles"=>[$receptor],
                "MessageText"=>($code==null?$message:str_replace("X",$code,$message)),
            ]),
        );
        $returnValue=["status"=>"error","data"=>null,"message"=>null];
        $bp= wp_remote_post( self::SEND_REST, $args );

        if(isset($bp["body"])){
        
            $return=json_decode($bp["body"]);
      
            if($return->status=="1"){
                hd_upanel_insert_sms_message(static::class,'0',$message,$code,$receptor);
                $returnValue["message"]="پیام با موفقیت ارسال شد";
                $returnValue["status"]="success";
            }
            else{
                       $returnValue["message"]=$return->message;
                       $returnValue["data"]=$return->status;  
            }
         
        }else{
           
            $returnValue["message"]="خطا در ارسال پیامک";
           $returnValue["message"]=$return->message;
        }
        return $returnValue;
    }
}