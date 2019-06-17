<?php
    include("curl.class.php");
    include("api.class.php");
    include("jjsdk.class.php");
    $jssdk = new jssdk;
    $jssdk->appID = "wx9fc3d060c8031bb7";
    $jssdk->appsecret = "91865bc923e8bd4efe597d936eb70de6";

    $config = $jssdk->signature();
    $name = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"];

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>jssdk DEMO</title>
    <style>
        input{
            width: 100%;height: 60px;padding: 10px;background-color: #0088ff;color: white;margin-bottom: 1em;;
        }
    </style>
    <script src="js/jweixin-1.4.0.js"></script>
    <script src="js/jquery-3.3.1.js"></script>
    <script>
        wx.config({
            debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            <?php
            foreach($config as $k=>$v){
                echo $k.":".$v.",\n";
            }
            ?>
            jsApiList: ['updateAppMessageShareData','chooseImage'] // 必填，需要使用的JS接口列表
        });
    </script>
</head>
<body>
    <h1>测试 jssdk 调用手机硬件，分享接口</h1>
    <input type="button" id="input1" value="打开"/>
    <input type="button" id="input2" value="打开"/>
    <script>
        $("h1").css("color","red");

        wx.ready(function () {   //需在用户可能点击分享按钮前就先调用
            $("#input1").click(function(){
                wx.chooseImage({
                    count: 1, // 默认9
                    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                    success: function (res) {
                        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    }
                });
            });
            $("#input2").click(function(){
                wx.updateAppMessageShareData({
                    title: '分享标题', // 分享标题
                    desc: '分享描述', // 分享描述
                    link: '<?php echo $jssdk->get_url(); ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                    imgUrl: '<?php echo $name; ?>/weixin/images/img1.jpg', // 分享图标
                    success: function () {
                        // 设置成功
                        alert("分享成功");
                    }
                });
            });
        });
    </script>
</body>
</html>