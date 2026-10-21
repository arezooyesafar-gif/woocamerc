<?php

class kavenegarRest {

    public const TOKEN=null;
    public const API_ADDRESS=null;
    public const SEND_SMS=null;
    public const SENDER=null;


    public function __construct($username,$password,$token,$number)
    {

        $this->TOKEN=$token;
        $this->SENDER=$number;
        $this->API_ADDRESS="https://api.kavenegar.com/v1/".$this->TOKEN;
        $this->SEND_SMS=$this->API_ADDRESS."/sms/send.json";
    }

    public function sendSMS($receptor,$message,$code=null,$pattern)
    {
        $args = array(
          /*  'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( YOUR_USERNAME . ':' . YOUR_PASSWORD )
            )*/
            'body'        => [
                "receptor"=>$receptor,
                "sender"=>$this->SENDER,
                "message"=>($code==null?$message:str_replace("X",$code,$message))
            ],
        );
        $returnValue=["status"=>"error","data"=>null,"message"=>null];
       $bp= wp_remote_get( $this->SEND_SMS, $args );
       if(isset($bp["body"])){
           $return=json_decode($bp["body"]);
           if($return->return->status==200){
            hd_upanel_insert_sms_message(static::class,'0',$message,$code,$receptor);
               $returnValue["message"]="پیام با موفقیت ارسال شد";
               $returnValue["status"]="success";
           }
           else{
               $returnValue["message"]=$return->return->message;
           }
       }else{
           $returnValue["message"]="خطا در ارسال پیامک";
       }
        return $returnValue;
    }
}