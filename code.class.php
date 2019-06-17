<?php
class code extends api{
    /**
    * 生成二维码
     */
    public function create_code($data){
        $access_token = $this->get_access_token();
        $this->url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$access_token";
        $this->data = $data;
        return $this->post();
    }

    /**
    * ticket获取二维码
     */
    public function ticket_get_code($TICKET){
        $this->url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$TICKET";
        return $this->get();
    }

    /**
    * 获取二维码并显示
     * $str = 参数
     * $type 默认临时，有效期30天   QR_LIMIT_SCENE == 永久
     */
    public function get_code($TICKET,$str="1",$type="QR_STR_SCENE"){
        if(!$TICKET){
            $TICKET = $this->create_code('{"expire_seconds": 2592000,"action_name": "'.$type.'", "action_info": {"scene": {"scene_str": "'.$str.'"}}}');
            $TICKET = json_decode($TICKET,true);
            $TICKET = $TICKET["ticket"];
        }
        header("content-type:jpeg");
        echo $this->ticket_get_code($TICKET);
    }
}