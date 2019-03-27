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
   font-size: 14px;
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
.pagination{
   display: flex;
   justify-content: flex-end;
   align-items: center;
}
.pagination li{
   display: inline;
}
.pagination li a{
    padding: 2px 8px !important;
    position: relative;
    float: left;
    margin-left: -1px;
    line-height: 1.42857;
    color: rgb(51, 122, 183);
    text-decoration: none;
    background-color: rgb(255, 255, 255);
    padding: 6px 12px;
    border-width: 1px;
    border-style: solid;
    border-color: rgb(221, 221, 221);
    border-image: initial;
}
.active{
    color: rgb(255, 255, 255)!important;
    background-color: rgb(0, 119, 221)!important;
    cursor: pointer;
    margin: 0px 5px 0px 5px!important;
    border-radius: 4px;
}
</style>
</head>
<body>
<div class="container-r">
 <div class="sel">
    <text>查询订单号:</text><input style="width:200px;" type="text" name="orderno" value="<?php echo $orderno; ?>" placeholder="请输入要查询的订单号"/>
    <text style="margin-left:20px;">查询锁号:</text><input style="width:200px;" type="text" name="lockcode" placeholder="请输入要查询的锁号" value="<?php echo $lockcode; ?>"/>
    <text style="margin-left:20px;">查询医院:</text><input style="width:300px;" type="text" name="branch" value="<?php echo $branch; ?>" placeholder="请输入要查询的医院名称或编号"/>
    <button onclick="sel()" style="margin-left:20px;">查询</button>
  </div>
  <table class="table">
    <tr>
     <td>订单号</td>
     <td>锁编号</td>
     <td>rfid编号</td>
     <td>开锁时间</td>
     <td>关锁时间</td>
     <td>状态</td>
     <td>医院名称</td>
    </tr>
    <?php foreach ($query as $item): ?>
    <tr <?php if($item['status']=='1'){echo "style='color:red'";}?>>
     <td><?php echo $item['ext_order_no']?></td>
     <td><?php echo $item['get_lock_id']?></td>
     <td><?php echo $item['rfid']?></td>
     <td><?php echo $item['start_time'];?></td>
     <td><?php echo $item['end_time']; ?></td>
     <td><?php if($item['status']=='1') {echo '借用中';}elseif ($item['status']=='2'){echo "已归还";}else{echo "开锁失败";}?></td>
     <td><?php echo $item['borrow_shop_name']?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <div style="text-align: right;margin-right:30px;margin-top:20px;"><?php echo $page ?></div>
<div>
<script>
//For Demo only
function sel(){
	var orderno = $("input[name='orderno']").val();
	var lockcode = $("input[name='lockcode']").val();
	var branch = $("input[name='branch']").val();
	if(orderno.length == 0){
        orderno = 'no';
	}
	if(lockcode.length == 0){
        lockcode = 'no';
	}
	if(branch.length == 0){
		branch = 'no';
	}
	window.location.href='http://xyj.yiyao365.cn/AliRentRomens/index.php/Cloud/Cloud/locklog/'+orderno+'/'+lockcode+'/'+branch;
}
function index(){
	window.location.href='http://xyj.yiyao365.cn/AliRentRomens/index.php/Cloud/Cloud/index/'+orderno+'/'+lockcode+'/'+branch;	
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

