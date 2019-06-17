<?php
header("content-type:jpeg");
include("curl.class.php");
include("api.class.php");

class material extends api{
    /**
    * 新增临时素材
     */
    public function add_ls_material(){
        $access_token = $this->get_access_token();
        $this->url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=video";
        $img = "./video/bootstrap.mp4";
        $img = realpath($img);//获取绝对路径
//        $this->data = array("media"=> new CURLFile($img));
        $this->data = array("media"=> curl_file_create($img));
        return $this->post();
    }

    /**
    * 获取临时素材
     */
    public function get_ls_material(){
        $access_token = $this->get_access_token();
        $media_id="AFTNvhjS3jf26a2Xmj24betK5k_zEzhV7tMX3iHREosJYnficDYvnvIOL_-_153-";
        $this->url="https://api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$media_id";
        return $this->get();
    }
}
$mate = new material;
$mate->appID = "wx9fc3d060c8031bb7";
$mate->appsecret = "91865bc923e8bd4efe597d936eb70de6";
echo $mate->get_ls_material();
