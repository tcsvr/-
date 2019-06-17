<?php
class wechat extends api{
    public $postObj;
    public $fromUsername;//用户的账号
    public $toUsername;//开发者账号
    public $msgtype;
    public $time;
    // 检查签名
    public function checkSignature(){
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array(TOKEN, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        return ( $tmpStr == $signature );
    }

    //    验证消息
    public function valid(){
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
        }
    }

//==========================事件处理 开始======================================
    //  事件
    public function receiveEvent(){
        $event = $this->postObj->Event;//事件类型
        switch($event){
            case "subscribe";//关注
                //扫描带参数二维码事件
                $EventKey = $this->postObj->EventKey;//事件KEY值，与自定义菜单接口中KEY值对应
                if(strpos($EventKey,"qrscene_")!==false){
                    $Ticket = $this->postObj->Ticket;//二维码的ticket，可用来换取二维码图片
                    file_put_contents("code.txt","EventKey==".$EventKey."\nTicket==".$Ticket);
                }
                printf($this->replayText(),$this->fromUsername,$this->toUsername,"欢迎关注公众号平台，请登录账号领取奖励\n<a href=\"http://pintao.iask.in/weixin/mode.php\">立即登录</a>");
                break;
            case "CLICK"://菜单事件
                $EventKey = $this->postObj->EventKey;//事件KEY值，与自定义菜单接口中KEY值对应
                if($EventKey=="V1001_TODAY_MUSIC") {//今日推荐
                    $arr = array(
                        array("title" => "图文消息标题1", "description" => "图文消息描述1", "picurl" => "http://118.89.48.22/weixin/images/img1.jpg", "url" => "http://www.ytmp3.cn/down/53719.mp3"),
                        array("title" => "图文消息标题2", "description" => "图文消息描述2", "picurl" => "http://118.89.48.22/weixin/images/img2.jpg", "url" => "http://www.ytmp3.cn/down/53719.mp3"),
                        array("title" => "图文消息标题3", "description" => "图文消息描述3", "picurl" => "http://118.89.48.22/weixin/images/img3.jpg", "url" => "http://www.ytmp3.cn/down/53719.mp3"),
                    );
                    printf($this->replayArticle($arr), $this->fromUsername, $this->toUsername);
                }else if($EventKey== "V1001_GOOD"){
                    printf($this->replayText(),$this->fromUsername,$this->toUsername,"点赞成功");
                }elseif($EventKey== "XIAOHUA"){
                    $content = $this->randomtext();
                    printf($this->replayText(),$this->fromUsername,$this->toUsername,$content);
                }
            break;
            case "SCAN";//用户已关注时的事件推送
                $EventKey = $this->postObj->EventKey;//事件KEY值，与自定义菜单接口中KEY值对应
                $Ticket = $this->postObj->Ticket;//二维码的ticket，可用来换取二维码图片
                file_put_contents("code.txt","EventKey==".$EventKey."\nTicket==".$Ticket);
                break;
        }

    }

    //    临时二维码
    public function newcode(){
        $access_token = $this->get_access_token();
        $this->url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$access_token";
        $this->data = '{"expire_seconds": 2592000,"action_name": "QR_STR_SCENE", "action_info": {"scene": {"scene_str": "test"}}}';
        return $this->post();
    }

    //    ticket获取二维码
    public function getticket($TICKET){
        $this->url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$TICKET";
        return $this->get();
    }

    //    自定义菜单创建
    public function newmenu($data){
        $access_token = $this->get_access_token();
        $this->url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $this->data = json_encode(array("button"=>$data),JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $this->post();
    }

    //    删除菜单
    public function remenu(){
        $access_token = $this->get_access_token();
        $this->url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=$access_token";
        return $this->get();
    }

//==========================事件处理 结束======================================

//************3->随机笑话************;
    public function randomtext(){
        $this->url = "http://v.juhe.cn/joke/randJoke.php";
        $params = array("key" => "92f9b7b0bf2296abe45f3384e47b17ba",//您申请的key;
        );
        $this->data = http_build_query($params);
        $result = $this->post();
        $result = json_decode($result,true);
        $rand = rand(0,count($result["result"])-1);
        $content = $result["result"][$rand]["content"];
        return $content;
    }

    // 响应消息
    public function responseMsg(){
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if(empty($postStr)){
            $postStr = file_get_contents("php://input");
            if(empty($postStr)){
                echo "error";
                exit;
            }
        }

        libxml_disable_entity_loader(true);
        $this->postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->fromUsername = $this->postObj->FromUserName;//用户的账号
        $this->toUsername = $this->postObj->ToUserName;//开发者账号
        $this->msgtype = $this->postObj->MsgType;
        $this->time = time();
        switch($this->msgtype){
            case "text"://文本
                $this->receiveText();
            break;
            case "image"://图片
                $this->receiveImage();
            break;
            case "voice"://语音
                $this->receiveVoice();
            break;
            case "video"://视频
                ;
            break;
            case "shortvideo"://小视频
                ;
            break;
            case "location"://地理位置消息
                ;
            break;
            case "link"://链接消息
                ;
            break;
            case "event"://事件
                $this->receiveEvent();
            break;
        }
    }
//===================消息处理 开始==========================
    //接受文本消息
    public function receiveText(){
        $Content = $this->postObj->Content;

        if($Content == "时间"){
            $Content = "现在是北京时间：\n".date("Y-m-d H:i:s",time());
        }elseif($Content == "音乐"){
            printf($this->replayMusic(),$this->fromUsername,$this->toUsername,"音乐标题","音乐描述","http://www.ytmp3.cn/down/53719.mp3","http://www.ytmp3.cn/down/53719.mp3");
            exit;
        }elseif($Content == "新闻"){
            $arr = array(
                array("title"=>"图文消息标题1","description"=>"图文消息描述1","picurl"=>"http://118.89.48.22/weixin/images/img1.jpg","url"=>"http://www.ytmp3.cn/down/53719.mp3"),
                array("title"=>"图文消息标题2","description"=>"图文消息描述2","picurl"=>"http://118.89.48.22/weixin/images/img2.jpg","url"=>"http://www.ytmp3.cn/down/53719.mp3"),
                array("title"=>"图文消息标题3","description"=>"图文消息描述3","picurl"=>"http://118.89.48.22/weixin/images/img3.jpg","url"=>"http://www.ytmp3.cn/down/53719.mp3"),
            );
            printf($this->replayArticle($arr),$this->fromUsername,$this->toUsername);
            exit;
        }elseif($Content=="图片"){
            $media_id="AFTNvhjS3jf26a2Xmj24betK5k_zEzhV7tMX3iHREosJYnficDYvnvIOL_-_153-";
            printf($this->replayImage(),$this->fromUsername,$this->toUsername,$media_id);
            exit;
        }elseif($Content=="视频"){
            $media_id="i3WY87umif6h-vrWWPrV9iVOuJtMuNjcCCBh_doYR40LzEm8zQ6DCHEywcaiTsYi";
            printf($this->replayVideo(),$this->fromUsername,$this->toUsername,$media_id);
            exit;
        }
        printf($this->replayText(),$this->fromUsername,$this->toUsername,$Content);
    }

    //接受图片消息
    public function receiveImage(){
        $media_id = $this->postObj->MediaId;//开发者账号
        ;
        printf($this->replayImage(),$this->fromUsername,$this->toUsername,$media_id);
    }

    //接受语音消息
    public function receiveVoice(){
        $media_id = $this->postObj->MediaId;//开发者账号
        ;
        printf($this->replayVoice(),$this->fromUsername,$this->toUsername,$media_id);
    }
//===================消息处理 结束==========================

//===================回复消息 开始==========================
    //    回复文本
    public function replayText(){
        $xml ="<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>$this->time</CreateTime>
              <MsgType><![CDATA[text]]></MsgType>
              <Content><![CDATA[%s]]></Content>
            </xml>";
        return $xml;
    }

    //    回复图片
    public function replayImage(){
        $xml ="<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>$this->time</CreateTime>
              <MsgType><![CDATA[image]]></MsgType>
              <Image>
                <MediaId><![CDATA[%s]]></MediaId>
              </Image>
        </xml>";
        return $xml;
    }

    //    回复语音
    public function replayVoice(){
        $xml ="<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>$this->time</CreateTime>
              <MsgType><![CDATA[voice]]></MsgType>
              <Voice>
                <MediaId><![CDATA[%s]]></MediaId>
              </Voice>
        </xml>";
        return $xml;
    }

    //    回复视频
    public function replayVideo(){
        $xml ="<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>$this->time</CreateTime>
              <MsgType><![CDATA[video]]></MsgType>
              <Video>
                <MediaId><![CDATA[%s]]></MediaId>
              </Video>
        </xml>";
        return $xml;
    }

    //音乐
    function replayMusic(){
        $xml = "<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>$this->time</CreateTime>
              <MsgType><![CDATA[music]]></MsgType>
              <Music>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
              </Music>
        </xml>";
        return $xml;
    }

    //    图文消息(文章)
    public function replayArticle($arr){
        $xml ="<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>$this->time</CreateTime>
              <MsgType><![CDATA[news]]></MsgType>
              <ArticleCount>".count($arr)."</ArticleCount>
              <Articles>";
        foreach($arr as $v) {
            $xml .="<item>
                  <Title><![CDATA[".$v["title"]."]]></Title>
                  <Description><![CDATA[".$v["description"]."]]></Description>
                  <PicUrl><![CDATA[".$v["picurl"]."]]></PicUrl>
                  <Url><![CDATA[".$v["url"]."]]></Url>
                </item>";
        }
        $xml .="      </Articles>
        </xml>";
        return $xml;
    }
//===================回复消息 结束==========================

}
?>