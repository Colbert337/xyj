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
button{
  height: 30px;
  font-size: 14px;
  width:100%;
}
</style>
<script type="text/javascript">
function selinfo(id,admin,name,status){
	var css= $("#"+id).attr("class");
	if(css == 'orderinfo'){
	document.getElementById( id ).className = "orderinfo-none";
	}else{
		$.ajax({
            async : true,
            url:"http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/sel_info",
            type:"POST",
            data:"id="+id,
            success: function(datas) {
			console.log(datas);
			var datas=JSON.parse(datas);
			var html ="<table style='width:100%;border-collapse: collapse'>"
	          +"<tr>"     
	             +"<td>租金金额："+datas.rent+"</td>"
	             +"<td>设备押金：￥"+datas.drent+"</td>"
	          +"</tr>"
	          +"<tr>"
	             +"<td>订单渠道："+datas.orderway+"</td>"
	             +"<td>渠道状态："+datas.wayinfo+"</td>"
	          +"</tr>"      
	          +"<tr>"
	             +"<td colspan='2'>设备锁号："+datas.lockcode+"</td>"
	          +"</tr>" 
	          +"<tr>"
	            +"<td colspan='2'>归还时间："+datas.returntime+"</td>"
	          +"</tr>"
	          +"<tr>"
	             +"<td colspan='2'>门店名称："+datas.shopname+"</td>"
	          +"</tr>";
	        if(admin == 'h' && status != '3'){
	           var  htmls = "<tr><td><button onclick=cancel('"+id+"','"+name+"')>撤销</button</td><td><button onclick=complete('"+id+"','"+name+"')>归还</button></td></tr>";
               html = html + htmls;
		    }else if (admin == 'h' && status == '3'){
		       var  htmls = "<tr><td colspan='2'><button onclick=update_money('"+id+"','"+name+"')>修改金额</button</td></tr>";
	           html = html + htmls;
		    }
	        html = html + "</table>";
	       
                $("#"+id).html(html);
            },
            error: function(datas) {
                alert("系统繁忙！请稍候再试");
            }
        });
		document.getElementById( id ).className = "orderinfo";
	}
}
function complete(id,name){
	var money = prompt("请输入扣款金额", ""); //将输入的内容赋给变量 money ，  
	  
    //这里需要注意的是，prompt有两个参数，前面是提示的话，后面是当对话框出来后，在对话框里的默认值  
    if (money)//如果返回的有内容  
    {  
    	$.ajax({
	        async : true,
	        url:"http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/complete",
	        type:"POST",
	        data:"id="+id+"&money="+money+"&name="+name,
	        success: function(datas) {
		    	var datas=JSON.parse(datas);
		    	console.log(datas);
		    	if(datas.state == '1' || datas=='1'){
	                 alert('归还成功，点击提交按钮刷新订单')
			    }else{
	                 alert(datas.msg);
				}
		    },
		    error: function(datas) {
	            alert("系统繁忙！请稍候再试");
	        }
		})
    }  
}
function cancel(id,name){
	if(confirm('确认撤销吗 兄dei？')){
		$.ajax({
	        async : true,
	        url:"http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/cancel",
	        type:"POST",
	        data:"id="+id+"&name="+name,
	        success: function(datas) {
		    	var datas=JSON.parse(datas);
		    	console.log(datas);
		    	if(datas.state == '1'){
	                 alert('撤销成功，点击提交按钮刷新订单')
			    }else{
	                 alert(datas.msg);
				}
		    },
		    error: function(datas) {
	            alert("系统繁忙！请稍候再试");
	        }
		})
	}
	
}
function update_money(id,name){
	var money = prompt("请输入修改金额", ""); //将输入的内容赋给变量 money ，  
    //这里需要注意的是，prompt有两个参数，前面是提示的话，后面是当对话框出来后，在对话框里的默认值  
    if (money)//如果返回的有内容  
    {  
    	$.ajax({
	        async : true,
	        url:"http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/update_money",
	        type:"POST",
	        data:"id="+id+"&money="+money+"&name="+name,
	        success: function(datas) {
		    	var datas=JSON.parse(datas);
		    	console.log(datas);
		    	if(datas.state == '1'){
	                 alert('修改成功，点击提交按钮刷新订单')
			    }else{
	                 alert(datas.msg);
				}
		    },
		    error: function(datas) {
	            alert("系统繁忙！请稍候再试");
	        }
		})
    }  
}
</script>
</head>
<body>
<div class="containers">
<h2 style="text-align: center;">轮椅管理</h1>
   <div style="margin-top: 10px; padding: 20px; background: #fff; border-radius: 5px;">
   <form action=""  method="post">
       <div class="section">
          <div class="section-title">订单号：</div>
          <div class="picker" style="display: flex; justify-content: flex-end;">
                 <input name="out_order_no" class="sel_input" value="<?php echo $out_order_no; ?>" type="text" placeholder="请输入订单号" />
          </div>
       </div>
       <div class="section">
          <div class="section-title">设备名称：</div>
          <div class="picker" style="display: flex; justify-content: flex-end;">
                 <input name="goodsname" class="sel_input" value="<?php echo $goodsname; ?>" type="text" placeholder="请输入设备名称" />
          </div>
       </div>
       <div class="section">
          <div class="section-title">用户姓名：</div>
          <div class="picker" style="display: flex; justify-content: flex-end;">
                 <input  name="username" onInput="searchInputChange1" value="<?php echo $username; ?>" class="sel_input" type="text" placeholder="请输入用户姓名" />
          </div>
       </div>
       <div class="section">
           <div class="section-title">联系电话：</div>
           <div class="picker" style="display: flex; justify-content: flex-end;">
                 <input name="phone" onInput="searchInputChange2" class="sel_input" value="<?php echo $phone; ?>" type="text" placeholder="请输入联系电话" />
           </div>
        </div>
        <div class="section">
           <input class="selbutton" type="submit" value='提交'>
        </div>
        
    </form>
    </div>
    <!-- 订单部分 -->
    <?php foreach ($info as $item): ?>
    <div onclick="selinfo('<?php echo $item['ID'] ?>','<?php echo $admin ?>','<?php echo $name ?>','<?php echo $item['STATUS'] ?>')" style="margin-top:10px;" class="order-list">
      <div  class="order-img">
        <div>
             <img src="<?php echo $item['LEASEPIC'] ?>" />
        </div >
      </div >
      <div class="order-detail">
         <div><?php echo $item['GOODS_NAME'] ?></div >
         <div>订单号:<?php echo $item['OUT_ORDER_NO'] ?></div >
         <div style="width:60%;"><?php echo $item['USERNAME'] ?></div >
        <div>
         <img style="width: 0.6rem;" class="telphone" src="http://romens-10034140.image.myqcloud.com/conew_2_dianhua.png?imageView2/100/w/640/h/0/format/png/q/85" />
         <?php echo $item['PHONE'] ?>
         </div >
         <div>下单时间: <?php echo $item['CREATETIME'] ?></div >
      </div >
      <div class="order-i">
         <img style="margin-top:15px;width:60px;height:60px;" src="http://romens-10034140.image.myqcloud.com/<?php if($item['STATUS'] == '0'){
         	     echo 'conew_2_notake.png';
               }else if ($item['STATUS'] == '1' || $item['STATUS'] == '6' || $item['STATUS'] == '8'){
               	 echo 'conew_2_noreturn.png';
               }else if ($item['STATUS'] == '2'){
               	 echo 'conew_2_revoke.png';
               }else if ($item['STATUS'] == '9'){
               	 echo 'conew_2_nopay.png';
               }else if ($item['STATUS'] == '3'){
               	 echo 'conew_2_yesreturn.png';
               }
         ?>?imageView2/100/w/640/h/0/format/png/q/85"/>
      </div >
     </div >
     <div id="<?php echo $item['ID'] ?>" class="orderinfo-none">
       
     </div>
    </div>
    <?php endforeach; ?>
</div>

</body>
</html>
