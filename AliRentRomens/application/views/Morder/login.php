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
<title>轮椅管理</title>

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
</style>
<script type="text/javascript">

$(document).ready(function(){ 
	　　if(localStorage.name !=undefined){
		  $("#username").val(localStorage.name);
		  $("#pwd").val(localStorage.pwd);
	   }
	}); 
function login(){
	var username= $("#username").val();
	var pwd = $("#pwd").val();
		$.ajax({
            async : true,
            url:"http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/check",
            type:"POST",
            data:"username="+username+'&pwd='+pwd,
            success: function(datas) {
			var datas=JSON.parse(datas);
			console.log(datas);
			  if(datas.state == '1'){
				  window.location.href='http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/home';
				  if(localStorage.name !=username){
					  localStorage.name=username;
				  }	
				  if(localStorage.pwd !=pwd){
					  localStorage.pwd=pwd;
				  }	  
			  }else{
                  alert(datas.msg);
			  }
            },
            error: function(datas) {
                alert("系统繁忙！请稍候再试");
            }
        });

}
</script>
</head>
<body>
<div class="containers">
<h2 style="text-align: center;">轮椅管理</h1>
   <div id="con_txt">
  
    <div class="z_style">
     <div style="width:40%;font-size:16px;">
     <text>登陆账号：</text>
     </div>
     <div style="width:60%;"> 
     <input style="width:100%;font-size:16px;" value="" id="username" type="text" placeholder="输入登录名" class="z_znumber"/>
     </div>
     </div>
     <div class="z_style">
     <div style="width:40%;font-size:16px;">
     <text>密码：</text>
     </div>
     <div style="width:60%;"> 
     <input style="width:100%;font-size:16px;" value=""  id="pwd" type="password" placeholder="输入密码" class="z_znumber"/>
     </div>
    </div>
    <button  onclick="login()" class="selbutton" id ="btn">提交</button>
    <a style="float:right;" href="http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/update_pwd">修改密码</a>
   </div>
   
</div>

</body>
</html>
