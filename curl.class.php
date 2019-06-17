<?php
/**
* php模拟提交
 */
class curl{
    public $url;
    public $data='';
    public $headerArray = array();

    public function get(){
        $ch = $this->curl_ch();
        if($this->data){
            curl_setopt($ch, CURLOPT_HTTPGET, true);//get
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        }
        $output = curl_exec($ch);//执行
        curl_close($ch);//关闭
        return $output;
    }

    public function post(){
        $ch = $this->curl_ch();
        if($this->data){
            curl_setopt($ch, CURLOPT_POST, 1);//post
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        }
        $output = curl_exec($ch);//执行
        curl_close($ch);//关闭
        return $output;
    }

    private function curl_ch(){
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($this->headerArray){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$this->headerArray);
        }
        return $ch;
    }
}

?>