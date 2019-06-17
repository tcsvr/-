<?php
/**
 * wechat php test
 */




define("TOKEN", "linling");
$wechatObj = new wechatCallbackapiTest();
// $wechatObj->valid();

$wechatObj->responseMsg();

$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
file_put_contents("data.txt",$postStr);

class wechatCallbackapiTest{

    /**
     * 接入验证
     */
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 接收并回复消息
     */
    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        file_put_contents("data.txt",$postStr);
//        $postStr = file_get_contents("php://input");//php7获取post原生
//        file_put_contents("data.txt",$postStr);

        //extract post data
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $msgtype = $postObj->MsgType;
            $time = time();

            if ($msgtype == "text") {

                $keyword = strtolower(trim($postObj->Content));

                if($keyword=="lucky"){

                    $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[music]]></MsgType>
                        <Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                        </Music>
                        </xml>";

                    $Title = "小幸运";
                    $Description = "桃子鱼仔的Ukulele教室";
                    $MusicUrl = "http://1.2016.applinzi.com/wx/music/lucky.mp3";
                    $HQMusicUrl = "http://1.2016.applinzi.com/wx/music/lucky.mp3";
                    $ThumbMediaId = "JcX0UIGCrSnFHpsbzk4OeeYqIqTNvRnFZTZnBOlGNQY";

                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $Title, $Description, $MusicUrl, $HQMusicUrl, $ThumbMediaId);

                    echo $resultStr;

                }

                $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";

                $msgType = "text";
                $contentStr = "Welcome to Maczen!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                echo $resultStr;

            }else if($msgtype=="image") {

                $MediaId = $postObj->MediaId;

                $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[image]]></MsgType>
                                <Image>
                                <MediaId><![CDATA[%s]]></MediaId>
                                </Image>
                                </xml>";

                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $MediaId);

                echo $resultStr;

            }else if($msgtype=="voice"){

                $MediaId = $postObj->MediaId;

                $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    <Voice>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>
                    </xml>";

                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $MediaId);

                echo $resultStr;

            }else if($msgtype=="location"){

                $x = $postObj->Location_X;
                $y = $postObj->Location_Y;
                $label = $postObj->Label;

                $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";

                $msgType = "text";
                $contentStr = "$x - $y - $label";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                echo $resultStr;
            }else if($msgtype=="event"){

                $event = $postObj->Event;
                $eventkey = $postObj->EventKey;

                if($event=="subscribe"){
                    $contentStr = "$fromUsername 已关注";
                }else if($event=="CLICK"){
                    $contentStr = "自定义菜单事件";
                }

                $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";

                $msgType = "text";

                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                echo $resultStr;
            }else{
                echo "Input something...";
            }

        }else {
            echo "";
            exit;
        }
    }
}



?>