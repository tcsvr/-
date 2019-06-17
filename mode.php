<?php
	include("curl.class.php");
	include("api.class.php");
	$api = new api;
	$api->appID = "wx9fc3d060c8031bb7";
	$api->url = "http://pintao.iask.in/weixin/login.php";
	$href = $api->get_login_url(true);

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="renderer" content="webkit">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<style>
		a{
			display: inline-block;
			margin-top: 30px;
			width: 80%;
			border: none;
			border-radius: 5px;
			background-color: #0088ff;
			color: white;
			margin-left: 10%;
			height: 40px;
			line-height: 40px;
			text-align: center;
			text-decoration: none;
		}
		h2{
			text-align: center;
			margin-bottom: 0;
		}
		img{
			display: block;
			width: 138px;
			margin: auto;
		}
	</style>
</head>
<body>
	<a href="<?php echo $href; ?>">微信登录</a>
	
	<h2>长按扫描二维码关注公众号</h2>
	<img src="images/1.png" alt="">
</body>
</html>