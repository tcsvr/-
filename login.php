<?php
	$get = $_GET;
	$url = "";
	$info = array();
	$con =  "";
	$josn = array();

	if(isset($get["code"])){
		include("curl.class.php");
		include("api.class.php");
		$api = new api;
		$APPID = "wx9fc3d060c8031bb7";
		$appsecret = "91865bc923e8bd4efe597d936eb70de6";
		$api->appID = $APPID;
		$api->appsecret = $appsecret;


		$CODE = $_GET["code"];

		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$APPID}&secret={$appsecret}&code={$CODE}&grant_type=authorization_code";

		$api->url = $url;
		$con = $api->get();
		$josn = json_decode($con,1);
		if(isset($josn["openid"])){
			$openid = $josn["openid"];//如果未保存，需要保存
			
			$access_token = $api->get_access_token();
			$api->url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
			$con = $api->get();
			$info = json_decode($con,1);
		}
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="renderer" content="webkit">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
</head>
<body>
	<h2>您好， <?php if(isset($info["nickname"]))echo $info["nickname"]; ?></h2>
</body>
</html>