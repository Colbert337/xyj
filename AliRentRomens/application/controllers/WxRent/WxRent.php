<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
require_once dirname(__FILE__).'/../../wxdecode/wxBizDataCrypt.php';
class WxRent extends CI_Controller {
	public $appid = 'wxa5d85fdc620e20bb';
	public $secret= '14691da2b1c3ba690ec2e644e4592b71';
	public function __construct()//1111
	{
		parent::__construct();
		$this->load->library('functions');
	}
	//发送模板消息
	public function tempsend($openid,$prepay_id,$orderno,$create,$return,$money){
	    $access_token = json_decode($this->functions->getRedisByKey('access_token')) ;
	    if($access_token){
	    	$access_token = $access_token->access_token;
	    }else{
	    	$access_token = json_decode($this->get_access_token());
	    	$access_token = $access_token->access_token;
	    }
	    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
	    $data = array(
	      'touser'=>$openid,
	      'template_id'=>'-AzMDnFZtCdFK3BCWW_00JI1IuPUF5azdAbzTsbf-z4',
	      'page'=>'pages/index/orderList',
	      'form_id'=>$prepay_id,
	      'data'=>array(
	         'keyword1'=>array(
	           'value'=>$orderno
	         ),
	         'keyword2'=>array(
	           'value'=>$create
	         ),
	         'keyword3'=>array(
	           'value'=>$return
	         ),
	         'keyword4'=>array(
	           'value'=>$money
	         ),
	         'keyword5'=>array(
	           'value'=>'感谢您使用雨诺器械租,祝您生活愉快'
	         ),
	      )
	    );
	    $res=$this->curlpost($url, json_encode($data));
        return $res;
	}
	public function testt(){
			$url = "http://xyj.yiyao365.cn/AliRentRomens/index.php/WxRent/WxApi/tempsend_api";
//			$data = array(
//			  'type'=>'mp',
//			  'appid'=>'wxa5d85fdc620e20bb',
//			  'secret'=>'14691da2b1c3ba690ec2e644e4592b71',
//			  'openid'=>'oFRnT5I9tpC0lfqj1ZwL0vErsk6E',
//			  'tempid'=>'NP3g7GXH_mBOaEKe596Yp-6TjVRoXumdjW5_WCrveYM',
//			  //'url'=>'http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/login',
//			  'mp_appid'=>'wxc6283d4008bb8ea0',
//			  'page'=>'pages/index/orderList',
//			  'first'=>'1',
//			  'keyword1'=>'2',
//			  'keyword2'=>'3',
//			  'keyword3'=>'4',
//			  'keyword4'=>'5',
//			  'remark'=>'备注',
//			);
			$data = array(
			  'type'=>'Wp',
			  'appid'=>'wxa5d85fdc620e20bb',
			  'secret'=>'14691da2b1c3ba690ec2e644e4592b71',
			  'openid'=>'oFRnT5I9tpC0lfqj1ZwL0vErsk6E',
			  'tempid'=>'-AzMDnFZtCdFK3BCWW_00JI1IuPUF5azdAbzTsbf-z4',
			  'page'=>'pages/index/orderList',
			  'keyword1'=>'2',
			  'keyword2'=>'3',
			  'keyword3'=>'4',
			  'keyword4'=>'5',
			  'keyword5'=>'这是备注',
			  'form_id'=>'wx0417214843213062ef3519352700227078',
			  'emp_word'=>'keyword1'
			);
			$a=$this->functions->curlPostArray($url,$data);
			var_dump($a);
					             
	}
	public function tempsends(){
		$access_token = json_decode($this->functions->getRedisByKey('access_token')) ;
	    if($access_token){
	    	$access_token = $access_token->access_token;
	    }else{
	    	$access_token = json_decode($this->get_access_token());
	    	$access_token = $access_token->access_token;
	    }
	    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token='.$access_token;
	    $data = array(
	      'touser'=>'oFRnT5I9tpC0lfqj1ZwL0vErsk6E',
	      'mp_template_msg'=>array(
	         'appid'=>'wxc6283d4008bb8ea0',
	         'template_id'=>'NP3g7GXH_mBOaEKe596Yp-6TjVRoXumdjW5_WCrveYM',
	         'url'=>'http://xyj.yiyao365.cn/AliRentRomens/index.php/Morder/Order/index',
	         'miniprogram'=>array(
	            'appid'=>$this->appid,
	            'pagepath'=>'pages/index/diyindex'
	         ),
	         'data'=>array(
	            'first'=>array(
	               'value'=>'归还成功'
	            ),
	            'keyword1'=>array(
	               'value'=>'青岛雨诺'
	            ),
	            'keyword2'=>array(
	               'value'=>'2016年3月4日 14'
	            ),
	            'keyword3'=>array(
	               'value'=>'1天'
	            ),
	            'keyword4'=>array(
	               'value'=>'201811111'
	            ),
	            'remark'=>array(
	               'value'=>'感谢使用'
	            )
	         )
	      )
	    );
	    $a=$this->curlpost($url, json_encode($data));
	    var_dump($a);
	}
	//获取accesstoken
	public function get_access_token(){
		$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
	    $res=$this->object_to_array(json_decode(file_get_contents($url)));
	    return json_encode($res,JSON_UNESCAPED_UNICODE);
	    $this->functions->setRedisKeyVal('access_token',json_encode($res,JSON_UNESCAPED_UNICODE),7200);
	}
	//商家版登陆校验
	public function login(){
		$code = $_POST['code'];
		$this->functions->debuglog($code);
		$url="https://api.weixin.qq.com/sns/jscode2session?appid=$this->appid&secret=$this->secret&js_code=$code&grant_type=authorization_code";
		$result = file_get_contents( $url, false, $context );
		echo $result;
	}
	//小程序二维码生成
	public function get_qrcode(){
		$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret";
		$res=$this->object_to_array(json_decode(file_get_contents($url)));
		$access_token=$res['access_token'];
		$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$access_token";
		$data = array(
	      'scene'=>'100000004386',
	      'page'=>'pages/index/Info',
		);
		$res=$this->send_post($url, json_encode($data));
		$result=$this->data_uri($res,'image/png');
		var_dump(iconv("UTF-8", "GBK",$result));
		echo '<image src='.$result.'></image>';
	}
	protected function send_post( $url, $post_data ) {
		$options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type:application/json',
		//header 需要设置为 JSON
                'content' => $post_data,
                'timeout' => 60
		//超时时间
		)
		);
		$context = stream_context_create( $options );
		$result = file_get_contents( $url, false, $context );
		return $result;
	}
	public function index(){
		$this->load->view('AliRent/welcome_message');
	}
	//二进制转图片image/png
	public function data_uri($contents, $mime)
	{
		$base64   = base64_encode($contents);
		return ('data:' . $mime . ';base64,' . $base64);
	}
	//判断是否在归还日期内
	public function isreturndata(){
		$thisdata=strtotime(date("Y-m-d H:i:s"));
		$endtime=strtotime($_POST['endtime']);
		$retime=$_POST['endtime'];
		$returntime = strtotime("$retime - 1 day");
		if($thisdata>$returntime && $thisdata<=$endtime){
			echo "1";
		}else{
			echo "2";
		}
	}
	//获取优惠券
	public function coupon(){
		$post_arr=array(
					             'QueryType'=>'get_goodsinfo',
					             'Params'=>'{"id":"'.$_POST['id'].'","branchid":"'.$_POST['branchid'].'","userid":"'.$_POST['userid'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $res=$this->object_to_array($res);
					             $result=array();
					             $array0=array(
					                      'NAME'=>'请选择优惠券',
					             );
					             $result[0] = $array0;
					             $i=1;
					             foreach ($res['coupon'] as $key=>$val){
					             	if($key != 'CREATETIME'){
					             		$result[$i] = $val;
					             		$i++;
					             	}
					             }
					             echo json_encode($result);
	}
	//天租日期选择
	public function date_chose(){
		if($_POST['create_time']){
			$t=strtotime($_POST['create_time']);
			$create_time=date("Y-m-d",$t);
		}else{
			$create_time=date("Y-m-d");
		};
		$datechose=array();
		for($i=1;$i<31;$i++){
			$datechose[] = $i.'天';
		}
		echo json_encode($datechose);
	}
	//借用时间秒数
	public function renttime(){
		$second=floor((strtotime(date("Y-m-d H:i:s"))-strtotime($_POST['createtime'])));
		echo $second;
	}
	//城市转化坐标
	public function get_location(){
		$url='http://api.map.baidu.com/geocoder/v2/?output=json&ak=hZ4eRI4DgupkNCyK5g9qj8lCjSKGMrfc&address='.$_POST['cityname'];
		$res=file_get_contents($url);
		$result=json_decode($res,true);
		$gol=$this->bd_decrypt($result['result']['location']['lng'],$result['result']['location']['lat']);
		$data=array(
	       'lng'=>$gol['gg_lon'],
	       'lat'=>$gol['gg_lat']
		);
		echo json_encode($data);
	}
	//感觉所在位置得出地址
	public function get_local(){
		$userid=$_POST['userid'];
		if($this->functions->getRedisByKey('xcx_address'.$userid)){
			echo $this->functions->getRedisByKey('xcx_address'.$userid);
		}else{
			$gol=$this->bd_encrypt($_POST['longitude'], $_POST['latitude']);
			$url='http://api.map.baidu.com/geocoder/v2/?output=json&ak=hZ4eRI4DgupkNCyK5g9qj8lCjSKGMrfc&location='.(String)$gol['bd_lat'].','.(String)$gol['bd_lon'];
			//hZ4eRI4DgupkNCyK5g9qj8lCjSKGMrfc
			$res=file_get_contents($url);
			$address=json_decode($res,true);
			$data = array(
			     'city'=>$address['result']['addressComponent']['city'],
			     'district'=>$address['result']['addressComponent']['district'],
			     'street'=>$address['result']['addressComponent']['street'],
			     'street_number'=>$address['result']['addressComponent']['street_number']
			);
			$this->functions->setRedisKeyVal('xcx_address'.$userid,json_encode($data,JSON_UNESCAPED_UNICODE),60);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
		}
	}
	//切换城市
	public function changecity(){
		$orientationList=array(
		0=>array(
		     'id'=>'02',
		     'region'=>'B'
		     ),
		     1=>array(
		     'id'=>'09',
		     'region'=>'J'
		     ),
		     2=>array(
		     'id'=>'15',
		     'region'=>'Q'
		     ),
		     3=>array(
		     'id'=>'17',
		     'region'=>'S'
		     ),
		     4=>array(
		     'id'=>'22',
		     'region'=>'X'
		     )
		     );
		     $act_addList=array(
		     0=>array(
		     'id'=>'02',
		     'region'=>'B',
		     'city'=>array(
		     0=>array(
		        'id'=>'110100',
		        'name'=>'北京市'
		        )
		        )
		        ),
		      1=>array(
		             'id'=>'09',
		             'region'=>'J',
		             'city'=>array(
		                 0=>array(
		                     'id'=>'130301',
		                     'name'=>'秦皇岛市'
		                 )
		             )
		             
		         ),
		        //		        1=>array(
		        //		     'id'=>'09',
		        //		     'region'=>'J',
		        //		     'city'=>array(
		        //		        0=>array(
		        //		              'id'=>'330400',
		        //		              'name'=>'嘉兴市'
		        //		              )
		        //		              )
		        //		              ),
		        2=>array(
		     'id'=>'15',
		     'region'=>'Q',
		     'city'=>array(
		        0=>array(
		        'id'=>'370200',
		        'name'=>'青岛市'
		        )
		        )
		        ),
		        3=>array(
		     'id'=>'17',
		     'region'=>'S',
		     'city'=>array(
		        0=>array(
		        'id'=>'310100',
		        'name'=>'上海市'
		        ) 
		        )
		            
		        ),
		         4=>array(
		             'id'=>'22',
		             'region'=>'S',
		             'city'=>array(
		                 0=>array(
		                     'id'=>'610100',
		                     'name'=>'西安市'
		                 )
		             )
		             
		         )
		        );
		        $data=array(
		   'orientationList'=>$orientationList,
		   'act_addList'=>$act_addList,
		   'menu'=>'no'
		   );
		   echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
	//高德所在位置得出地址
	public function gd_get_address(){
		$url="http://restapi.amap.com/v3/geocode/ regeo?location=120.413712,36.076259&key=f97955d5f79b2768313e560acb40ffcd";
		$arr=file_get_contents($url);
		$newarr=json_decode($arr,true);
		print_R($newarr);
	}
	//导航去往门店
	public function goto_store(){
	    $post_arr=array(
	        'QueryType'=>'getBranch',
	        'Params'=>'{"guid":"'.$_POST['markerid'].'"}',
	        'UserGuid'=>'ODh8QHJvbWVucw--'
	    );
	    $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	    $res=$this->functions->curlPostArray($url, $post_arr);
	    $res=$this->object_to_array($res);
	    $gol=$this->bd_decrypt($res[0]['lng'], $res[0]['lat']);
	    $data=array(
	        'lat'=>$gol['gg_lat'],
	        'lng'=>$gol['gg_lon'],
	        'shopname'=>$res[0]['NAME'],
	        'address'=>$res[0]['ADDRESS']
	    );
	    echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
	//地图附近门店
	public function nearstore(){
		$post_arr=array(
					             'QueryType'=>'getBranch',
					             'Params'=>'{"lat":"'.$_POST['lat'].'","lng":"'.$_POST['lng'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $res=$this->object_to_array($res);
					             for($i=0;$i<10;$i++){
					             	$gol=$this->bd_decrypt($res[$i]['lng'], $res[$i]['lat']);
					             	$lat=(String)$gol['gg_lat'];
					             	$lng=(String)$gol['gg_lon'];
					             	$result[$i]=array(
					                'iconPath'=>'http://romens-10034140.image.myqcloud.com/conew_2_dingwei.png?imageView2/100/w/640/h/0/format/png/q/85',
					                'id'=>$res[$i]['GUID'],
					                'latitude'=>$lat,
					                'longitude'=>$lng,
					                'width'=>25,
					                'height'=>25,
					                'title'=>$res[$i]['NAME'],
					             	'callout'=>array(
					             	    'content'=>$res[$i]['ADDRESS'],
					             	    'fontSize'=>14,
					             	    'padding'=>10,
					             	    'textAlign'=>'center',
					             	    'borderRadius'=>8
					             	 ),
					             	 'label'=>array(
					             	     'content'=>$res[$i]['NAME'],
					             	 )
					             	);
					             }
					             echo json_encode($result);

					             //echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//附近门店距离计算
	public function near_store(){
		$gol=$this->bd_encrypt($_POST['longitude'], $_POST['latitude']);
		$lat=(String)$gol['bd_lat'];
		$lng=(String)$gol['bd_lon'];
		//echo $_POST['latitude'];die;
		$post_arr=array(
					             'QueryType'=>'getBranchInfo',
					             'Params'=>'{"businessesId":"'.$_POST['businessesId'].'","goodsid":"'.$_POST['goodsid'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $res=$this->object_to_array($res);
					             //计算距离
					             for($i=0;$i<count($res);$i++){
					             	$dis=$this->getDistance($lat,$lng,$res[$i]['lat'],$res[$i]['lng']);
					             	$res[$i]['dis']=(float)($dis/1000);
					             }
					             //按距离排序
					             $len=count($res);
					             for($k=0;$k<=$len;$k++)
					             {
					             	for($j=$len-1;$j>$k;$j--){
					             		if($res[$j]['dis']<$res[$j-1]['dis']){
					             			$temp = $res[$j];
					             			$res[$j] = $res[$j-1];
					             			$res[$j-1] = $temp;
					             		}
					             	}
					             }
					             //KM和M的显示
					             for($i=0;$i<count($res);$i++){
					             	if($res[$i]['dis']<1){
					             		$res[$i]['dis']=(int)($res[$i]['dis']*1000).'m';
					             	}else{
					             		$res[$i]['dis']=(int)($res[$i]['dis']).'km';
					             	}
					             }
					             //debuglog('门店信息：'.json_encode($res[0],JSON_UNESCAPED_UNICODE));
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//获取user_id
	public function get_user_id(){
		$auth_code = $_POST['auth_code'];
		if($_POST['check']){
			$this->aop->appId='2017092608942796';
		}
		$request = new AlipaySystemOauthTokenRequest();
		$request->setCode($_POST['auth_code']);
		$request->setGrantType('authorization_code');
		//$signData = $request->getApiParas();
		//debuglog('sign:'.json_encode($signData));
		//$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		//debuglog('sign data:'.$sign);
		/*$result = $this->aop->execute($request);
		 $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		 $resultCode = $result->$responseNode->code;
		 debuglog('result data1:'.$resultCode);
		 if(!empty($resultCode)&&$resultCode == 10000){
		 echo "成功".$result->$responseNode->user_id;
		 } else {
		 echo "失败".$result->$responseNode->code;
		 }*/
		$result = $this->aop->execute($request,null,null,1);
		//debuglog('userid:'.json_encode($result));
		$res=json_decode($result,true);
		exit($res['user_id']);
	}

	//获取user_tel
	public function get_user_tel(){
		$pc = new WXBizDataCrypt($this->appid, $_POST['sessionKey']);
		$errCode = $pc->decryptData($_POST['encryptedData'], $_POST['iv'], $data );
		if ($errCode == 0) {
			echo $data;
		} else {
			echo "失败";
		}
	}
	//获取用户所有信息
	public function get_user_info(){
		$auth_code = $_POST['auth_code'];
		$request = new AlipaySystemOauthTokenRequest();
		$request->setCode($_POST['auth_code']);
		$request->setGrantType('authorization_code');
		$result = $this->aop->execute($request,null,null,1);
		$res=json_decode($result,true);
		$access_token = $res['access_token'];
		$request = new AlipayUserInfoShareRequest ();
		$signData = $request->getApiParas();
		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request,$access_token);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		$tel = $result->$responseNode->mobile;
		$userid = $result->$responseNode->user_id;
		$username = $result->$responseNode->user_name;
		$user_name=iconv('GB2312', 'UTF-8',$username);
		$data = array(
		  'userid'=>$userid,
		  'username'=>$username,
		  'tel'=>$tel
		);
		//S($userid.$data);
		if(!empty($resultCode)&&$resultCode == 10000){
			//创建会员卡
			$post_arr=array(
					             'QueryType'=>'addUser',
					             'Params'=>'{"orgguid":"88","id":"'.$userid.'","name":"'.$username.'","phone":"'.$tel.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $urls='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $ress=$this->functions->curlPostArray($urls, $post_arr);
					             //debuglog('创建会员结果:'.json_encode($ress));
					             echo json_encode($data,JSON_UNESCAPED_UNICODE);
		}else{
			echo "失败";
		}
	}
	//二维码生成列表页
	public function order_list(){
		$post_arr=array(
					             'QueryType'=>'get_goodsinfo',
					             'Params'=>'{"orgguid":"88"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $this->assign('info',$res);
					             $this->display();
	}
	//生成跳转小程序链接(二维码)
	public function goto_order(){
		$info="code=".$_GET['code'];
		$info=urlencode($info);
		$html='alipays://platformapi/startapp?appId=2017051807276496&query='.$info;
		echo $html;
		//header("Location:$html");
	}
	//发放物品
	public function updata_status(){
		$post_arr=array(
					'QueryType'=>'update_order',
					'Params'=>'{"status":"1","order_no":"'.$_GET['order_no'].'"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
					if($res->state == '1'){
						$this->success('已发放',U('index',array('token'=>$_GET['token'])));
					}
	}
	//关注生活号
	public function isfocuson(){
		$data =array(
		   'EventType'=>'follow',
		   'ActionParam'=>array(
		     'scene'=>array(
		         'sceneId'=>'tinyApp'
		         ),
		         ),
		   'FromAlipayUserId'=>'2088902145150773'
		   );
		   if($data['EventType'] == 'follow'){
		   	if($data['ActionParam']['scene']['sceneId'] == 'tinyApp'){
		   		$post_arr=array(
					             'QueryType'=>'updatefocus',
					             'Params'=>'{"userid":"'.$data['FromAlipayUserId'].'","isfocuson":"1"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
		   	}
		   }else{
		   	$post_arr=array(
					             'QueryType'=>'updatefocus',
					             'Params'=>'{"userid":"'.$data['FromAlipayUserId'].'","isfocuson":"2"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
		   }
	}
	//查询订单
	public function alirent_query(){
		$request = new ZhimaMerchantOrderRentQueryRequest ();
		$bizcontentarray=array(
		    'out_order_no'=>$_GET['out_order_no'],
	        'product_code'=>'w1010100000000002858'
	        );
	        $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
	        //echo '0^'.$bizcontent0;die;
	        $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
	        $request->setBizContent($bizcontent);
	        //$signData = $request->getApiParas();
	        //$sign = $this->aop->rsaSign($signData, $this->aop->signType);
	        $result = $this->aop->execute($request);
	        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
	        $resultCode = $result->$responseNode->code;
	        if(!empty($resultCode)&&$resultCode == 10000){
	        	echo "订单号:".$result->$responseNode->order_no."<br/>名称:".iconv("GBK", "UTF-8",$result->$responseNode->goods_name);
	        } else {
	        	echo "失败".$result->$responseNode;
	        }
	}
	//修改订单状态
	public function test_cx($order_no){
		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//撤销接口
	public function ali_cancel(){
		$request = new ZhimaMerchantOrderRentCancelRequest ();
		$bizcontentarray=array(
				             'order_no'=>$_POST['order_no'],
				             'product_code'=>$_POST['product_code']//'w1010100000000002858'
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		//echo '0^'.$bizcontent0;die;
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$request->setBizContent($bizcontent);
		$signData = $request->getApiParas();
		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			$data=array(
				             	    'state'=>1,
				             	    'msg'=>'成功'
				             	    );
				             	    echo json_encode($data,JSON_UNESCAPED_UNICODE);
		}else{
			$data=array(
				             	    'state'=>2,
				             	    'msg'=>'失败'
				             	    );
				             	    echo json_encode($data,JSON_UNESCAPED_UNICODE);
		}
	}
	public function curlpost($url,$data){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, FALSE);
		curl_setopt($ch, CURLOPT_URL, $url);//设置链接
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
		curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据
		$response = curl_exec($ch);//接收返回信息
		$res=$response;
		if(curl_errno($ch)){//出错则显示错误信息
			echo"错误：";
			print curl_error($ch);
		}
		curl_close($ch); //关闭curl链接
		return $res;
	}
	private function object_array($array) {
		if(is_object($array)) {
			$array = (array)$array;
		} if(is_array($array)) {
			foreach($array as $key=>$value) {
				$array[$key] = $this->object_array($value);
			}
		}
		return $array;
	}
	function object_to_array($obj){
		$_arr = is_object($obj)? get_object_vars($obj) :$obj;
		foreach ($_arr as $key => $val){
			$val=(is_array($val)) || is_object($val) ? $this->object_to_array($val) :$val;
			$arr[$key] = $val;
		}
		return $arr;
	}
	//计算两点间经纬度的距离
	function getDistance($lat1, $lng1, $lat2, $lng2)
	{
		$earthRadius = 6367000; //approximate radius of earth in meters

		/*
		 Convert these degrees to radians
		 to work with the formula
		 */

		$lat1 = ($lat1 * pi() ) / 180;
		$lng1 = ($lng1 * pi() ) / 180;

		$lat2 = ($lat2 * pi() ) / 180;
		$lng2 = ($lng2 * pi() ) / 180;

		/*
		 Using the
		 Haversine formula

		 http://en.wikipedia.org/wiki/Haversine_formula

		 calculate the distance
		 */

		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
		$stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		$calculatedDistance = $earthRadius * $stepTwo;
		return round($calculatedDistance);
	}
	//高德坐标转百度
	public function bd_encrypt($gg_lon,$gg_lat)

	{

		$x_pi = 3.14159265358979324 * 3000.0 / 180.0;

		$x = $gg_lon;

		$y = $gg_lat;

		$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);

		$theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);

		$data['bd_lon'] = $z * cos($theta) + 0.0065;

		$data['bd_lat'] = $z * sin($theta) + 0.006;

		return $data;

	}
	//百度左边转高德坐标
	function bd_decrypt($bd_lon,$bd_lat)
	{
		$x_pi = 3.14159265358979324 * 3000.0 / 180.0;
		$x = $bd_lon - 0.0065;
		$y = $bd_lat - 0.006;
		$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
		$theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
		$data['gg_lon'] = $z * cos($theta);
		$data['gg_lat'] = $z * sin($theta);
		return $data;
	}
}
?>