<?php
class jssdk extends api{
    public function get_noncestr($max){
        $noncestr = "cbsakcsack2r3289fnfu02n210nqwn129021c210c1bwq";
        $str = "";
        for($i=0;$i<$max;$i++){
            $str .= $noncestr[rand(0,strlen($noncestr)-1)];
        }
        return $str;
    }

    public function get_url(){
        return ($_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]);
    }

    public function signature(){
        $noncestr = $this->get_noncestr(4);
        $time = time();
        $url = $this->get_url();
        $ticket = $this->get_jsapi_ticket();
        $tmpArr = array(
            "noncestr=".$noncestr,
            "timestamp=".$time,
            "url=".$url,
            "jsapi_ticket=".$ticket
        );

        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr,"&" );
        $sign = sha1( $tmpStr );
        return array(
            "appId"=>"'".$this->appID."'",
            "timestamp"=>$time,
           "nonceStr"=> "'".$noncestr."'",
            "signature"=>"'".$sign."'"
        );
    }
}