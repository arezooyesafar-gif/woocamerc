<?php
class melipayamakRest {

    public const USERNAME=null;
    public const PASSWORD=null;
    public const SENDER=null;
    const SEND_REST="https://ippanel.com/services.jspd";

    public function __construct($username,$password,$token,$number)
    {
        $this->PASSWORD=$username;
        $this->USERNAME=$password;
        $this->SENDER=$number;
    }

    public function sendSMS($receptor,$message,$code=null,$pattern)
    {
        

        $rcpt_nm = array($receptor);
        $result = file_get_contents('https://api.payamak-panel.com/post/Send.asmx/SendSimpleSMS2?username='.urlencode($this->PASSWORD).'&password='.$this->USERNAME.'&to='.$receptor.'&from='.$this->SENDER.'&text='.urlencode($code==null?$message:str_replace("X",$code,$message)).'&isflash=false');
   
        // $result = trim($result);

        $returnValue=["status"=>"error","data"=>null,"message"=>null];
        $errorarray =array(0,1,2,3,4,5,6,7,8,9,10,11,12,35);
       
       $result = simplexml_load_string($result);
$result = json_encode($result);
$result = json_decode($result,TRUE)[0];


        if(!in_array($result, $errorarray)){
     
                hd_upanel_insert_sms_message(static::class,'0',$message,$code,$receptor);
                $returnValue["message"]="پیام با موفقیت ارسال شد";
                $returnValue["status"]="success";
                $returnValue["data"]=$result;
         
        }else{
            $returnValue["message"]="خطا در ارسال پیامک";
            $returnValue["data"]=$result;
        }
        return $returnValue;
    }
}