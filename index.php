<?php
/**
 * wechat php test
 */
include("curl.class.php");
include("api.class.php");
include("wechat.class.php");
$wechat = new wechat;

$echoStr = $_GET["echostr"];
if($echoStr) {
    define("TOKEN", "linling");
    $wechat->valid();
    exit;
}

if(isset($_GET["signature"])){
    $wechat->responseMsg();
}else{
    $wechat->appID = "wx9fc3d060c8031bb7";
    $wechat->appsecret = "91865bc923e8bd4efe597d936eb70de6";
    if(isset($_GET["menu"]) && $_GET["menu"]==1){
        $data = array(
            array("type"=>"click",
                "name"=>"今日推荐",
                "key"=>"V1001_TODAY_MUSIC"),
            array("type"=>"click",
                "name"=>"笑话",
                "key"=>"XIAOHUA"),
            array("name"=>"菜单",
                "sub_button"=>array(
                    array("type"=>"view",
                        "name"=>"搜索",
                        "url"=>"http://www.soso.com/"),
                    array("type"=>"miniprogram",
                        "name"=>"wxa",
                        "url"=>"http://mp.weixin.qq.com",
                        "appid"=>"wx286b93c14bbf93aa",
                        "pagepath"=>"pages/lunar/index"),
                    array("type"=>"click",
                        "name"=>"赞一下我们",
                        "key"=>"V1001_GOOD"),
                )),
        );
        echo $wechat->newmenu($data);
    }elseif(isset($_GET["remenu"]) && $_GET["remenu"]==1){
        echo $wechat->remenu();
    }elseif(isset($_GET["code"]) && $_GET["code"]==1){
        echo $wechat->newcode();
    }elseif(isset($_GET["ticket"]) && $_GET["ticket"]==1){
        header("content-type:jpeg");
        $TICKET = "gQFY8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyMmlaREJMWnRkZWsxMmhvM3h0Y0UAAgSRi9tcAwQAjScA";
        echo $wechat->getticket($TICKET);
    }else{
        $wechat->responseMsg();
    }
}

?>