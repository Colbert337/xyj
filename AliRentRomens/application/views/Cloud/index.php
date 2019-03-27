<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>蹲位管理 - 开锁记录</title>
<script type="text/javascript" src="http://xyj.yiyao365.cn/AliRentRomens/application/views/js/jquery.js"></script>
<style>
html, body{
   padding:0px;
   margin:0px;
   font-family: 'Raleway', sans-serif;
   height:100%;
}
.container{
   width:200px;
   background:rgba(0, 0, 0, 1);
   padding:10px 0px 20px 0px;
   border:1px solid #111;
   border-radius:4px;
   box-shadow:0px 4px 5px rgba(0, 0, 0, 0.75);
   height:100%;
   display: inline-block;
}
.container-r{
   display: inline-block;
   width:100%;
   position: absolute;
   height:100%;
   font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
}
.link{
   font-size:16px;
   font-weight:300;
   text-align:center;
   position:relative;
   height:40px;
   line-height:40px;
   margin-top:10px;
   overflow:hidden;
   width:90%;
   margin-left:5%;
   cursor:pointer;
}
.link:after{
   content: '';
   position:absolute;
   width:80%;
   border-bottom:1px solid rgba(255, 255, 255, 0.5);
   bottom:50%;
   left:-100%;
   transition-delay: all 0.5s;
   transition: all 0.5s;
}
.link:hover:after,
.link.hover:after{
   left:100%;
}
.link .text{
   text-shadow:0px -40px 0px rgba(255, 255, 255, 1);
   transition:all 0.75s;
   transform:translateY(100%) translateZ(0);
   transition-delay:all 0.25s;
}
.link:hover .text,
.link.hover .text{
   text-shadow:0px -40px 0px rgba(255, 255, 255, 0);
   transform:translateY(0%) translateZ(0) scale(1.1);
   font-weight:600;
   color: white;
}
a {
   text-decoration:none;
}
.table{
   margin-top: 20px;
   margin-left:50px;
   width:90%;
   text-align:center;
}
table td{
   padding:10px;
    border-bottom: 1px solid #ddd;
}
input{
   vertical-align: middle;
   border-radius: 10px;
   padding:6px 12px;
   outline:none;
   margin-left:10px;
}
.sel{
   margin-top: 50px;
   margin-left:50px;
   width: 90%;
}
button{
   color: #fff;
   background-color: #337ab7;
   border: none;
   padding: 6px 12px;
   text-algin:center;
   text-align: center;
   border-radius: 4px;
}
</style>
</head>
<body>
<div class="container-r">
  <div class="sel">
    <text>查询锁号:</text><input style="width:200px;" type="text" value="<?php echo $lockcode; ?>" name="lockcode" placeholder="请输入要查询的锁号"/>
<!--    <text style="margin-left:20px;">查询医院:</text><input style="width:300px;" type="text" name="branch" placeholder="请输入要查询的医院名称或编号"/>-->
    <button onclick="sel()" style="margin-left:20px;">查询</button>
  </div>
  <table class="table">
    <tr>
     <td>墩位编号</td>
     <td>车辆编号</td>
     <td>状态</td>
     <td>操作</td>
    </tr>
    <?php if($res['code'] == '0'){
    	     echo '<tr><td>'.$lockcode.'</td>';
             echo '<td>'.$res['rfid'].'</td>';
             if($res['lockStatus'] == '1'){
      	        echo '<td>在线</td>';
             }else{
      	        echo '<td>脱网</td>';
             }
             echo  '<td><button>下架</button></td>';
    } 
    ?>
  </table>
<div>
<script>
//For Demo only
function sel(){
	var lockcode = $("input[name='lockcode']").val();
	window.location.href='http://xyj.yiyao365.cn/AliRentRomens/index.php/Cloud/Cloud/index/'+lockcode;
}
function locklog(){
	window.location.href='http://xyj.yiyao365.cn/AliRentRomens/index.php/Cloud/Cloud/locklog';	
}
function addClass(id){
   setTimeout(function(){
      if(id > 0) links[id-1].classList.remove('hover')
      links[id].classList.add('hover')
   }, id*750) 
}
</script>
</body>
</html>

