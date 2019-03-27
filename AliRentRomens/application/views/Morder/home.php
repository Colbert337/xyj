<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport"
	content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta charset="utf-8">
<link rel="stylesheet" href="http://xyj.yiyao365.cn/AliRentRomens/application/views/css/style.css?v=2" type="text/css" />
<script type="text/javascript" src="http://xyj.yiyao365.cn/AliRentRomens/application/views/js/jquery.js"></script>
<title>我的主页</title>

<style type="text/css">
body:before {
	content: ' ';
	position: fixed;
	z-index: -1;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	background:
		url(http://romens-10034140.image.myqcloud.com/sda.jpg?imageView2/100/w/640/h/0/format/png/q/85)
		no-repeat 0px 0px;
	background-size: cover;
}
.main{
    margin:20px auto;
    text-align: center;
    background-color: white;
    padding: 10px;
    border-radius: 8px;
    font-size:18px;
    font-weight:bold;
}
</style>
<script type="text/javascript">
 function nav(where){
	 window.location.href='http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/'+where;
 }
</script>
</head>
<body>
<div onclick="nav('index')" class="main">订单管理</div>
<div onclick="nav('under')" class="main">蹲位管理</div>

</body>
</html>
