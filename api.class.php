<?php
class api extends curl{
    public $appID;
    public $appsecret;
    public function get_access_token(){
        if(!file_exists("catch")){
            mkdir("catch",0777,true);
        }
        $filename = "./catch/".md5($this->appID.$this->appsecret)."_access_token.txt";
        if(file_exists($filename) && is_file($filename) && (time()-7000)<fileatime($filename)){
            return file_get_contents($filename);
        }else{
            $this->url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appID&secret=$this->appsecret";
            $contentArr = json_decode($this->get(),true);
            $content = $contentArr["access_token"];
            file_put_contents($filename,$content);
            return $content;
        }
    }

    public function get_jsapi_ticket(){
        if(!file_exists("catch")){
            mkdir("catch",0777,true);
        }
        $filename = "./catch/".md5($this->appID.$this->appsecret)."_jsapi_ticket.txt";
        if(file_exists($filename) && is_file($filename) && (time()-7000)<fileatime($filename)){
            return file_get_contents($filename);
        }else{
            $this->url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$this->get_access_token()."&type=jsapi";
            $contentArr = json_decode($this->get(),true);
            $content = $contentArr["ticket"];
            if($contentArr["errcode"]==0) {
                file_put_contents($filename,$content );
            }
            return $content;
        }
    }

    public function get_login_url($encode=true,$scope = "snsapi_userinfo",$state=''){
        //应用授权作用域， snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid）， snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且， 即使在未关注的情况下，只要用户授权，也能获取其信息 ）
        if($encode){
            $arr = array(
                "appid"=>$this->appID,
                "redirect_uri"=>$this->url,
                "response_type"=>"code",
                "scope"=>$scope,
                "state"=>$state
                );
            $get = http_build_query($arr);
            return "https://open.weixin.qq.com/connect/oauth2/authorize?{$get}#wechat_redirect";
        }
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appID}&redirect_uri={$this->url}&response_type=code&scope={$scope}&state=$state#wechat_redirect";
    }
}
?>