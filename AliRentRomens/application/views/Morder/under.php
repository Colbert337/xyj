<!DOCTYPE html>  
<meta name="viewport"
	content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="http://xyj.yiyao365.cn/AliRentRomens/application/views/css/style.css?v=2" type="text/css" />
<script type="text/javascript" src="http://xyj.yiyao365.cn/AliRentRomens/application/views/js/jquery.js"></script>
<html>  
<head>  
  <meta charset="UTF-8">  
  <title>蹲位下架</title>  
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
       display: -webkit-flex;
    display: -webkit-box;
    display: flex;
    -webkit-box-orient: horizontal;
    -webkit-box-direction: normal;
    -webkit-flex-direction: row;
    flex-direction: row;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    justify-content: space-between;
    margin: 0 auto;
    border-radius: 8px ;
    }
</style>
</head>  
<body>  
 <div class="main" style="width:80%;background-color: white;text-align: center;margin-top:200px;">
 <img id ="query" style="vertical-align: middle;height:50px; margin-left: 18%;" src="http://romens-10034140.image.myqcloud.com/scan.png?imageView2/100/w/640/h/0/format/png/q/85">
 <input style="width:100%;padding: 5px;font-size: 22px;" class="z_znumber" type="text" id="qrcode" placeholder="输入锁号"/>
 </div>
 <div style="text-align: center;margin-top:20px;">
 <input style="width:20%;!important" class="selbutton" onclick="sel()" type="button" value='下架'>
 </div>
</body>  
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>  
<script>  
function sel(){
	var qrcode= $("#qrcode").val();
		$.ajax({
            async : true,
            url:"http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/underlock",
            type:"POST",
            data:"qrcode="+qrcode,
            success: function(datas) {
			var datas=JSON.parse(datas);
			   console.log(datas);
			   alert(datas.msg);
            },
            error: function(datas) {
                alert("系统繁忙！请稍候再试");
            }
        });
}
wx.config({//配置wx.config
     //debug: true,//调试输出需要的话就拿出来
     appId: '<?php echo $signPackage["appId"];?>',
     timestamp: <?php echo $signPackage["timestamp"];?>,
     nonceStr: '<?php echo $signPackage["nonceStr"];?>',
     signature: '<?php echo $signPackage["signature"];?>',
     jsApiList: [
     //要调用的接口全部写在这
       'scanQRCode'
     ]

 });
wx.ready(function () {
   document.querySelector('#query').onclick = function () {//调用扫码事件返回扫码值   
   wx.scanQRCode({
     needResult: 1,
     scanType: ["qrCode","barCode"],
     success: function (res) {
     var code = res.resultStr; 
    	 $("#qrcode").val(code.substring(44));       
     }
   });
 };   
});
</script>
</html> 