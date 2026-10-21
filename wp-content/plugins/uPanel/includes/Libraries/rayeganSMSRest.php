<?php
class rayeganSMSRest {

    const SEND_REST="https://raygansms.com/SendMessageWithUrl.ashx?";
    public const USERNAME=null;
    public const PASSWORD=null;
    public const SENDER=null;

    public function __construct($username,$password,$token,$number)
    {
        $this->PASSWORD=$username;
        $this->USERNAME=$password;
        $this->SENDER=$number;
    }
    public function sendSMS($receptor,$message,$code=null,$pattern)
    {
        $param = array
        (
            'Username'=>$this->PASSWORD,
            'Password'=>$this->USERNAME,
            'PhoneNumber'=>$this->SENDER,
            'MessageBody'=>($code==null?$message:str_replace("X",$code,$message)),
            'RecNumber'=>$receptor,
            'Smsclass'=>1
        );
        $bp=file_get_contents(self::SEND_REST.http_build_query($param));
        $returnValue=["status"=>"error","data"=>null,"message"=>null];
        if(isset($bp) && is_numeric($bp)){
            hd_upanel_insert_sms_message(static::class,'0',$message,$code,$receptor);
            $returnValue["message"]="پیام با موفقیت ارسال شد";
            $returnValue["status"]="success";

        }else{
            $returnValue["message"]="خطا در ارسال پیامک";
        }
        return $returnValue;
    }
}