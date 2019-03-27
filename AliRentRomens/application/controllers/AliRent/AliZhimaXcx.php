<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
defined('BASEPATH') OR exit('No direct script access allowed');
require_once '/var/www/AliRentRomens/application/aop/AopClient.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardTemplateCreateRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardTemplateModifyRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayOfflineMaterialImageUploadRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardOpenRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayOpenPublicShortlinkCreateRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayOpenPublicQrcodeCreateRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardActivateurlApplyRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayMarketingCardActivateformQueryRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/AlipayOpenPublicMessageSingleSendRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentCreateRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentCancelRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentCompleteRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentQueryRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantBorrowEntityUploadRequest.php';
require_once '/var/www/AliRentRomens/application/aop/request/ZhimaMerchantOrderRentModifyRequest.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayTradeAppPayRequest.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayUserElectronicidUserQueryRequest.php';
require_once dirname(__FILE__).'/../../aop2/request/AlipayFundTransToaccountTransferRequest.php';
require_once dirname(__FILE__).'/../WxRent/WxPay.php';
require_once dirname(__FILE__).'/../WxRent/WxApi.php';
class AliZhimaXcx extends CI_Controller {
	public $aliappid;
	public $rsaPrivateKey;
	public $rsaPrivateKeyFilePath;
	public $alipayrsaPublicKey;
	public $token;
	public $aop;
	public $wxuser;
	public function __construct()
	{
		parent::__construct();
		$this->aop = new AopClient ();
		$this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$this->aop->appId = '2016121004107537';
		//$this->aop->rsaPrivateKey = $this->rsaPrivateKey;
		//merchant_rsa_private_key.pem路径111
		$this->rsaPrivateKeyFilePath ='/var/www/AliRentRomens/application/aop/key/jzkey/merchant_rsa_private_key.pem';
		//merchant_rsa_private_key.pem内容
		//$this->rsaPrivateKey ='MIICWwIBAAKBgQCoHBQthaQWaV5pzZ0B8qQwDf8a+GbRxZDqvwOfM3rJckBDuJ2qXJvYCQyQIq+ZdfPw1dDd4pJgK4t3U83qXhakM/nCRgi4gVHdobP7KUpLJURKfsadTcQX7aaydwr176yT++0Ke51/G5/HsqZ2jHPY3ooUeiQYhUskCi/SCGLkYQIDAQABAoGARJSv5qJOfpYd3ivzkYfbU39iQy5zQ8DFjf6/C4OE5AmoDfiS2Z1ONqP6bBK6cHCeQ/H2c46rCHC7RML7jlE0Cr9zWPis/G2x1jYPiVb3wUzmR/21lHIbB7qIzQVGKPx7Ugd2Z71HUyrtH4z3aZ9sTr2JE6p2Y4tuN7LibV8A9cECQQDZu34bYZjATyxrdRdz3dufO+IcYksKTHQZvC2XAj6d1DRryB8puQFa9gVDD6Q4NdbJkvBfHxvwugm3iQik9eTfAkEAxaflEQLXzNLbHRDIEfJoboU4ZN3KJZkY0hKV5iG0YjJRIq1t9zTDvoP4udNF7k5rFYDbm3Wb5IVt9d4XDEUevwJAZYaOq/fbQTjpzoV/1RBLWzmSGoge04OI04MyguqSBggwFV3wYgUZQ6/aDkYZ3fgE2mNA4CniXmJxK3qjZEAgYwJAG4trO7SmuC+GQ4WsK/wZG5XLJxtVaWntcJEQfLKjva9/aRK8KWAcCze++L59l1ksSSHc+Mwp/m2txj69/YLAZwJADKgq0MAXm4txbIAyWAdmvKnTUAC7RFoFIB9GKyefwc53HSgJv96GQ9eVLfaqtAhR0dhF0C/4DoBwONh1jk1zkg==';
		//alipay_rsa_public_key.pem内容
		$this->alipayrsaPublicKey ='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB';
		//$this->aop->rsaPrivateKey = $this->rsaPrivateKey;
		$this->aop->rsaPrivateKeyFilePath = $this->rsaPrivateKeyFilePath;
		$this->aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
		$this->aop->apiVersion = '1.0';
		$this->aop->signType = 'RSA';
		$this->aop->postCharset = 'GBK';
		$this->aop->format = 'json';
		$this->load->library('functions');
	}
	public  function alibugreturn($out_order_no){
		$aliorder = $this->zhima_sel($out_order_no);
		if(!empty($aliorder['code'])&&$aliorder['code'] == 10000){
			//echo $result->$responseNode->order_no."<br/>状态:".iconv("GBK", "UTF-8",$result->$responseNode->use_state)."支付状态：".iconv("GBK", "UTF-8",$result->$responseNode->pay_status)."<br/>借出时间:".$result->$responseNode->borrow_time."归还时间:".$result->$responseNode->restore_time;
			$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"orderno":"'.$aliorderp['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             var_dump($res[0]);
					             $this->transpay2($out_order_no, $res[0]['USER_ID'], $res[0]['RENT_AMOUNT']);
					             if($res == null){
					             	echo "no order";
					             }
		} else {
			echo "失败".$aliorder;
		}

	}
	public function transpay2($order,$userid,$money){
		$request = new AlipayFundTransToaccountTransferRequest ();
		$bizcontentarray=array(
		    'out_biz_no'=>$order,
	        'payee_type'=>'ALIPAY_LOGONID',
		    'payee_account'=>'xiqiang@jianzubao.net',
		    'amount'=>$money,
		    'payer_show_name'=>'青岛雨诺',
		    'remark'=>'健租宝7月设备回款'
		    );
		    $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		    //echo '0^'.$bizcontent0;die;
		    $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		    $request->setBizContent($bizcontent);
		    //$signData = $request->getApiParas();
		    //$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		    $result = $this->aop->execute($request);
		    var_dump($request);
		    $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		    $resultCode = $result->$responseNode->code;
		    if(!empty($resultCode)&&$resultCode == 10000){
		    	echo $order."成功";
		    } else {
		    	echo $order."失败".$result->$responseNode;
		    }
	}
	//生活号推送模板消息接口
	public function send_temp($userid,$msg){
		$url = 'http://zfb.yiyao365.cn/index.php?g=User&m=AliMemberCard&a=SENDTEMP&token=fdpbbq1480645793&userid='.$userid;
		$a=$this->functions->curlPostArray($url,$msg);
	}
	public function tempmsg(){
		$request = new AlipayFundTransToaccountTransferRequest ();
		$bizcontentarray=array(
		    'to_user_id'=>'2088902145150773',
	        'payee_type'=>'ALIPAY_USERID',
		    'payee_account'=>$user_id,
		    'amount'=>$amount,
		    'payer_show_name'=>'押金返还',
		    'remark'=>$remark
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$request->setBizContent($bizcontent);
		//$signData = $request->getApiParas();
		//$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			$data = array(
			  'state'=>'2',
			  'msg'=>'下单失败，退款成功'
			  );
		} else {
			$data = array(
			  'state'=>'2',
			  'msg'=>'下单失败，请等待退款'
			  );
			  $this->functions->debuglog($out_order_no.'订单退款失败');
		}
	}
	public function transpay($out_order_no,$user_id,$amount,$remark){
		$request = new AlipayFundTransToaccountTransferRequest ();
		$bizcontentarray=array(
		    'out_biz_no'=>$out_order_no,
	        'payee_type'=>'ALIPAY_USERID',
		    'payee_account'=>$user_id,
		    'amount'=>$amount,
		    'payer_show_name'=>'押金返还',
		    'remark'=>$remark
		);
		$this->functions->debuglog('退款订单信息:'.json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE));
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$request->setBizContent($bizcontent);
		//$signData = $request->getApiParas();
		//$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			$data = array(
			  'state'=>'2',
			  'msg'=>'下单失败，退款成功'
			  );
		} else {
			$data = array(
			  'state'=>'2',
			  'msg'=>'下单失败，请等待退款'
			  );
			  $this->functions->debuglog($out_order_no.'订单退款失败');
		}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
	//奇奇开锁
	public function qiqi_unlock($orderno,$lockCode,$createtime,$userid,$out_order_no){
		$this->functions->debuglog($out_order_no.'发起开锁');
		$url="http://java.xingoxing.com/api/rent";//"http://server.571cn.com:12580/sg-rest-api/api/rent";
		//$url="http://server.571cn.com:12580/api/rent";
		$data=array(
		  'extOrderNo'=>$orderno,
		  'lockCode'=>$lockCode,//'100000004492',
		  'createTime'=>$createtime
		);
		foreach ($data as $key=>$value){
			$arr[$key] = $key;
		}
		sort($arr); //字典排序的作用就是防止因为参数顺序不一致而导致下面拼接加密不同
		// 2. 将Key和Value拼接
		$str = "5ADC9AD1224C38886394C3FB45BD77FC";
		foreach ($arr as $k => $v) {
			$str = $str.$arr[$k].$data[$v];
		}
		$data['sign']=md5($str);
		$data['channel']='5ADC9AD1224C38886394C3FB45BD77FC';
		$res = $this->object_to_array(json_decode(json_decode($this->curlpost($url, $data))));
		//$this->functions->debuglog($lockCode.'开锁结果：'.json_encode($res,JSON_UNESCAPED_UNICODE));
		if($res['code'] == '000000'){
			$data=array(
			'state'=>'1',
			'msg'=>'开锁成功'
			);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
			$this->functions->debuglog($out_order_no.'开锁成功等待反馈');
			//			M('rm_lockorder')->where($where)->save(array('status'=>'1'));
		}else{
			$msg = $res['msg'];
			//			M('rm_lockorder')->where($where)->save(array('status'=>'2'));
			$data=array(
			'state'=>'2',
			'msg'=>'开锁失败,请重试'
			);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
			$this->functions->debuglog($out_order_no.'开锁失败'.$msg);
			$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$orderno.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             //传入订单号时查询
					             if(!$res['ID']){
					             	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$orderno.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             }
					             $order_no=$res['ORDER_NO'];
					             $out_order_no = $res['OUT_ORDER_NO'];
					             if($res['ISDEPOSIT'] == '1'){
					             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             }else{
					             	$request = new ZhimaMerchantOrderRentCancelRequest ();
					             	$bizcontentarray=array(
				             'order_no'=>$order_no,
				             'product_code'=>'w1010100000000002858'
				             );
				             $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				             //echo '0^'.$bizcontent0;die;
				             $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				             $request->setBizContent($bizcontent);
				             //				             $signData = $request->getApiParas();
				             //				             $sign = $this->aop->rsaSign($signData, $this->aop->signType);
				             $result = $this->aop->execute($request);
				             $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				             $resultCode = $result->$responseNode->code;
				             if(!empty($resultCode)&&$resultCode == 10000){
				             	$this->functions->debuglog($out_order_no.'撤销成功');
				             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					              
				             }else{
				             	$data=array(
				             	    'state'=>'3',
				             	    'msg'=>'开锁失败，请联系工作人员'
				             	    );
				             	    $this->functions->debuglog($out_order_no.'开锁撤销失败');
				             }
					             }

		}

	}
	//反馈奇奇开锁
	public function qiqi_unlocknotice(){
		//$this->functions->debuglog($_POST['bikeCode']);
		$msg=array(
			  'code'=>'000000',
			  'msg'=>'ok'
			  );
	    $fmsg=array(
	          'code'=>'100001',
	          'msg'=>'fail'
	          );
	          //        $data=array(
	          //		  'extOrderNo'=>$_POST['extOrderNo'],
	          //		  'lockCode'=>$_POST['lockCode'],//'100000004492',
	          //		  'createTime'=>$_POST['createTime']
	          //		);
	          //		foreach ($data as $key=>$value){
	          //			$arr[$key] = $key;
	          //		}
	          //		sort($arr); //字典排序的作用就是防止因为参数顺序不一致而导致下面拼接加密不同
	          //		// 2. 将Key和Value拼接
	          //		$str = "5ADC9AD1224C38886394C3FB45BD77FC";
	          //		foreach ($arr as $k => $v) {
	          //			$str = $str.$arr[$k].$data[$v];
	          //		}
	          //		$sign=md5($str);
	          //		if($sign==$_POST['sign']){
	          $post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$_POST['extOrderNo'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             if(!$res['ID']){
					             	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$_POST['extOrderNo'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             }
					             $order_no=$res['ORDER_NO'];
					             $out_order_no=$res['OUT_ORDER_NO'];
					             $taketime = date("Y-m-d H:i:s");
					             $userid = $res['USER_ID'];
					             $dmoney = $res['DEPOSIT_AMOUNT'];
					             $branchid = $res['LEASEBRANCHGUID'];
					             $goodsname = $res['GOODS_NAME'];
					             if($_POST['result']== '0'){
					             	$this->functions->debuglog($out_order_no.'开锁信息反馈开锁成功');
					             	//如果已经关锁 不改变状态
					             	if($res['STATUS'] != '3'){
					             		$status = '1';
					             	}else{
					             		$status = '3';
					             	}
					             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"numberno":"'.$_POST['bikeCode'].'","status":"'.$status.'","order_no":"'.$res['ORDER_NO'].'","takegoodstime":"'.$taketime.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $result=$this->functions->curlPostArray($url, $post_arr);
					             //判断用户使用情况
					             $isrent = $this->functions->getRedisByKey('AliRent'.$res['USER_ID']);
					             if(!$isrent){
					             	$timing=array(
		 		                      'check'=>'hasrent',
		 		                      'time'=>strtotime($taketime),
		 		                      'usetime'=>0,
		 		                      'money'=>0
					             	);
					             	$timing=json_encode($timing);
					             	$this->functions->setRedisKeyVal('AliRent'.$res['USER_ID'],$timing,86400);
					             	$isrent = $this->functions->getRedisByKey('AliRent'.$res['USER_ID']);
					             	$this->functions->debuglog($res['USER_ID'].'用户已使用共享轮椅,记录为'.$isrent);
					             }
					             if($result->state != '1'){
					             	$this->functions->debuglog(json_encode($post_arr,JSON_UNESCAPED_UNICODE));
					             	$this->functions->debuglog(json_encode($result,JSON_UNESCAPED_UNICODE));
					             }
					             echo json_encode($msg);	
					             $returnaddress = $this->rentaddress($res['LEASEBRANCHGUID']);
					             if($returnaddress == 'NO'){
					                 $returnaddress = '感谢您使用雨诺器械租,祝您生活愉快';
					             }else{
					                 $returnaddress = '可归还地点'.$returnaddress;
					             }
					             $temp = array(
		                            'tempid'=>'ac18f53a63064ab1a9e88962638099a5',
		                            'first'=>'租借成功，订单号:'.$res['OUT_ORDER_NO'],
		                            'keyword1'=>$res['TAKEGOODSTIME'],
		                            'keyword2'=>$taketime,
		                            'keyword3'=>'以实际归还时间为准',
		                            'keyword4'=>$res['GOODS_NAME'],
		                            'keyword5'=>$res['OUT_ORDER_NO'],
		                            'keyword6'=>'400-686-0176',
					                 'remark'=>$returnaddress
		                            );
		                            if($res['ISDEPOSIT'] != '1'){
		                            	$this->send_temp($res['USER_ID'],$temp);
		                            }else{
		                                $prepayid = $this->functions->getRedisByKey('prepayid'.$out_order_no);
		                                if($prepayid){
		                                    $wxtemp = new WxApi();
		                                    $access_token = $this->functions->getRedisByKey('access_token');
		                                    if(!$access_token){
		                                        $access_token = $wxtemp->get_access_token();
		                                        $this->functions->setRedisKeyVal('access_token',$access_token,7200);
		                                    }
		                                    $tem=$wxtemp->rent_tempsend($access_token, $userid, $prepayid, $out_order_no, $goodsname, $dmoney, $branchid);
		                                }
		                            }
					             }else{
					             	//					             	$msg=array(
					             	//			  'status'=>'2',
					             	//			  'msg'=>'开锁失败'
					             	//			  );
					             	$this->functions->debuglog($out_order_no.'开锁信息反馈开锁失败！');
					             	$dmoney = $res['DEPOSIT_AMOUNT'];
					             	$this->functions->delRedisByKey('AliRent'.$res['USER_ID']);
					             	if($res['ISDEPOSIT'] == '1'){
					             		//$this->transpay($order_no,$res['USER_ID'],$res['DEPOSIT_AMOUNT'],'开锁失败押金退换');
					             		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $this->functions->debuglog('订单:'.$order_no.'发起退款');
					             $wxpay = new WxPay();
					             $wxpay_res=$wxpay->pay_refund($order_no,$dmoney,$dmoney);
					             //退款失败 重推5次
					             if($wxpay_res['result_code'] != 'SUCCESS'){
					                 for($i=0;$i<5;$i++){
					                     $wxpay_res=$wxpay->pay_refund($order_no,$dmoney,$dmoney);
					                     if($wxpay_res['result_code'] == 'SUCCESS'){
					                         break;
					                     }
					                 }
					             }
					             echo json_encode($msg);					             
					             	}else{
					             		$request = new ZhimaMerchantOrderRentCancelRequest ();
					             		$bizcontentarray=array(
				             'order_no'=>$order_no,
				             'product_code'=>'w1010100000000002858'
				             );
				             $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				             //echo '0^'.$bizcontent0;die;
				             //$this->functions->debuglog('撤销数据'.$bizcontent0);
				             $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				             $request->setBizContent($bizcontent);
				             //				             $signData = $request->getApiParas();
				             //				             $sign = $this->aop->rsaSign($signData, $this->aop->signType);
				             $result = $this->aop->execute($request);
				             $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				             $resultCode = $result->$responseNode->code;
				             if(!empty($resultCode)&&$resultCode == 10000){
				             	$this->functions->debuglog($out_order_no.'撤销成功');
				             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             //echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             echo json_encode($msg);
				             }else{
				             	$this->functions->debuglog($out_order_no.'撤销失败'.json_encode($result,JSON_UNESCAPED_UNICODE));
				             	if($result->$responseNode->sub_code == 'ORDER_IS_NOT_BORROW' || $result->$responseNode->sub_code == 'ORDER_IS_CANCEL'){
				             		echo json_encode($msg);
				             	}else{
				             		echo json_encode($fmsg);
				             	}
				             }
					             	}

					             }
					             //$this->functions->debuglog('qiqi开锁信息反馈:'.json_encode($msg,JSON_UNESCAPED_UNICODE));
	}
	public function tttt($orderno){
	        $post_arr=array(
	            'QueryType'=>'get_orderinfo',
	            'Params'=>'{"out_order_no":"'.$orderno.'"}',
	            'UserGuid'=>'ODh8QHJvbWVucw--'
	        );
	        $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	        $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
	        $res = $res[0];
	    $userid = $res['USER_ID'];
	    $order_no = $res['ORDER_NO'];
	    $out_order_no = $res['OUT_ORDER_NO'];
	    $dmoney = $res['DEPOSIT_AMOUNT'];
	    $branchid = $res['LEASEBRANCHGUID'];
	    $taketime = $res['TAKEGOODSTIME'];
	    $goodsname = $res['GOODS_NAME'];
	    $prepayid = $this->functions->getRedisByKey('prepayid'.$out_order_no);
	    var_dump($prepayid);
	    if($prepayid){
	        $wxtemp = new WxApi();
	        $access_token = $this->functions->getRedisByKey('access_token');
	        if(!$access_token){
	            $access_token = $wxtemp->get_access_token();
	            $this->functions->setRedisKeyVal('access_token',$access_token,7200);
	        }
	        $tem=$wxtemp->rent_tempsend($access_token, $userid, $prepayid, $out_order_no, $goodsname, $dmoney, $branchid);
	    }
	}
	//奇奇关锁通知
	public function qiqi_locknotice(){
		$msg=array(
			  'code'=>'000000',
			  'msg'=>'ok'
			  );
	    $fmsg=array(
	          'code'=>'100001',
	          'msg'=>'fail'
	          );
	          $where=array(
		        'extOrderNo'=>$_POST['extOrderNo']
	          );
	          $post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$_POST['extOrderNo'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             if(!$res['ID']){
					             	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$_POST['extOrderNo'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             }
					             //如果已经完成或者撤销则退出
					             if($res['STATUS'] == '3' || $res['STATUS'] == '2'){
					                 echo json_encode($msg);
					                 die;
					             }
					             $userid = $res['USER_ID'];
					             $order_no = $res['ORDER_NO'];
					             $out_order_no = $res['OUT_ORDER_NO'];
					             $dmoney = $res['DEPOSIT_AMOUNT'];
					             $branchid = $res['LEASEBRANCHGUID'];
					             $taketime = $res['TAKEGOODSTIME'];
					             $this->functions->debuglog($out_order_no.'发起关锁');
					             $lockinfo=$this->object_to_array(json_decode($res['LOCKINFO']));
					             //查看是否组用过
					             $firstorder = '';
					             $rentinfo = $this->object_to_array(json_decode($this->functions->getRedisByKey('AliRent'.$userid)));
					             if($rentinfo){
					             	$hastime = ($rentinfo['usetime']);
					             	$this->functions->debuglog('该用户已经使用：'.$hastime);
					             }else{
					             	$hastime = 0;
					             }
					             //$this->functions->debuglog('该用户已经使用：'.$hastime);
					             if($_POST['createTime']){
					             	$rtime = $_POST['createTime'];
					             }else{
					             	$rtime=date("Y-m-d H:i:s");
					             }
					             if(strtotime($res['TAKEGOODSTIME'])<=strtotime($rtime)){
					             	$res['TAKEGOODSTIME']=$res['CREATETIME'];
					             }
					             $renttime =(strtotime($rtime)-strtotime($res['TAKEGOODSTIME']))+$hastime;
					             if($renttime > 3600 && $hastime <= 3600 && $renttime<=23400){
					             	$firstorder = '1';
					             }
					             $rentdate=$renttime/3600;
					             $rentday=ceil($renttime/86400);
					             $rent = 0;
					             $rentcoupon = 0;
					             $hassale = 'no';
					             if(count($lockinfo)>0){
					             	for($i=0;$i<count($lockinfo);$i++){
					             		$rentcoupon = $lockinfo[$i]['USERPRICE'];
					             		$rent = $rentcoupon;
					             		if($rentdate<(float)$lockinfo[$i]['SPEC2VALUE'] && $rentdate>=(float)$lockinfo[$i]['SPEC1VALUE']){
					             			$hassale = 'yes';
					             			break;
					             		}
					             	}
					             }
					             if($hassale == 'no'){
					             	//$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$res['RENT_AMOUNT']*0.5;
					             	$post_arr=array(
					             'QueryType'=>'get_goodsinfo',
					             'Params'=>'{"goodsid":"'.$res['RENT_INFO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $ress=$this->functions->curlPostArray($url,$post_arr);
					             $result = $this->object_to_array($ress[0]);
					             if($renttime/86400>1){
					             	$a = fmod($rentdate,24);
					             	//处理单天不封顶
					             	if($result['ONEDAYPRICE'] == $dmoney){					             		
					             		$rent = (ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2))*$result['HOURPRICE']*0.5;				
					             	}else{
					             		if($a==0){
					             			$rent = ((float)$rentday*$result['ONEDAYPRICE']);
					             		}else{
					             			//特殊处理青医附院的计费
					             			if(substr($branchid,0,4)=='QYFY' && ceil($a*2)%2 != 0){
					             				$rent =(ceil($a*2)*(float)$result['HOURPRICE']*0.5-0.5)+(((float)$rentday-1)*$result['ONEDAYPRICE']);
					             			}else{
					             				$rent =ceil($a*2)*(float)$result['HOURPRICE']*0.5+(((float)$rentday-1)*$result['ONEDAYPRICE']);
					             			}
					             		}
					             	}
					             	 
					             }else{
					             	//$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$result['HOURPRICE']*0.5;
					             	//特殊处理青医附院的计费
					             	if(substr($branchid,0,4)=='QYFY' && ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)%2 != 0){
					             		$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$result['HOURPRICE']*0.5-0.5;
					             	}else{
					             		$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$result['HOURPRICE']*0.5;
					             	}
					             }
					             $rent = $rent + (float)$rentcoupon;
					             if($rent>(float)$result['ONEDAYPRICE']*(float)$rentday){
					             	$rent = (float)$result['ONEDAYPRICE']*(float)$rentday;
					             }
					             }
					             if($rentinfo){
					             	$rentinfo['usetime'] = $renttime;
					             	$sholdpay = $rent  ;
					             	$rent = $sholdpay - $rentinfo['money'];
					             	$rentinfo['money'] = $sholdpay;
					             	//$this->functions->debuglog('此次应付:'.$rent);
					             	$newtime = time()-$rentinfo['time'];
					             	if($newtime<86400){
					             		$newtime = 86400 - $newtime;
					             	}
					             	$this->functions->setRedisKeyVal('AliRent'.$userid,json_encode($rentinfo),$newtime);
					             }
					             if($rent>(float)$dmoney){
					             	$rent = $dmoney;
					             }
					             $rent = (String)$rent;
					             //完成订单
					             if($res['ISDEPOSIT'] == '1'){
					             	$renturn_rent = (float)$dmoney - (float)$rent;
					             	$renturn_rent = (String)$renturn_rent;
					             	//$this->transpay($order_no,$userid,$renturn_rent,'还车押金退换');
					             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"firstorder":"'.$firstorder.'","goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$rent.'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $drent = (float)$dmoney - (float)$rent;
					             $this->functions->debuglog('订单:'.$order_no.'发起退还押金');
					             $wxpay = new WxPay();
					             $wxpay_res=$wxpay->pay_refund($order_no,$dmoney,(String)$drent);
					             //如果退款失败，重复执行5次退款接口
					             if($wxpay_res['result_code'] != 'SUCCESS'){
					                 for($i=0;$i<5;$i++){
					                     $wxpay_res=$wxpay->pay_refund($order_no,$dmoney,(String)$drent);
					                     if($wxpay_res['result_code'] == 'SUCCESS'){
					                         break;
					                     }
					                 }
					             }
					             //发送模板消息
					             $prepayid = $this->functions->getRedisByKey('prepayid'.$out_order_no);
					             $this->functions->debuglog('prepay'.$prepayid);
					             if($prepayid){
					             	$wxtemp = new WxApi();
					             	$access_token = $this->functions->getRedisByKey('access_token');
					             	if(!$access_token){
					             		$access_token = $wxtemp->get_access_token();
					             		$this->functions->setRedisKeyVal('access_token',$access_token,7200);
					             	}
					             	$tem=$wxtemp->tempsend($access_token,$userid, $prepayid, $out_order_no,$taketime,$rtime, $rent);
					             }
					             //发送模板消息结束
					             echo json_encode($msg);	             
					             }else{
					             	$request = new ZhimaMerchantOrderRentCompleteRequest ();
					             	$bizcontentarray=array(
				                     'order_no'=>$res['ORDER_NO'],
				                     'product_code'=>$res['PRODUCT_CODE'],
				                     'restore_time'=>$rtime,
				                     'pay_amount_type'=>'RENT',
				                     'pay_amount'=>$rent
					             	);
					             	$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
					             	//$this->functions->debuglog('转换前数据:'.$bizcontent0);
					             	//echo '0^'.$bizcontent0;die;
					             	$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
					             	//$this->functions->debuglog('转换后数据：'.$bizcontent);
					             	$request->setBizContent($bizcontent);
					             	//					             $signData = $request->getApiParas();
					             	//					             $sign = $this->aop->rsaSign($signData, $this->aop->signType);
					             	$result = $this->aop->execute($request);
					             	$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
					             	$resultCode = $result->$responseNode->code;
					             	if(!empty($resultCode)&&$resultCode == 10000){
					             		//$this->functions->debuglog('归还成功'.$res['OUT_ORDER_NO']);
					             		//$this->success('归还成功！',U('alirent_info',array('token'=>$this->token)));
					             		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"firstorder":"'.$firstorder.'","goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$rent.'","alipricetime":"'.$rtime.'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($msg);
					             //$this->functions->debuglog(json_encode($post_arr));
					             $this->functions->debuglog($out_order_no.'还车成功');
					             //$this->functions->debuglog('一天内使用情况:'.json_encode($rentinfo));
					             //$this->functions->debuglog('归还结果:'.json_encode($post_arr));
					             //echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             	} else {
					             		$this->functions->debuglog($res['OUT_ORDER_NO'].'归还失败'.json_encode($result,JSON_UNESCAPED_UNICODE));
					             		$this->functions->debuglog(json_encode($bizcontent,JSON_UNESCAPED_UNICODE));
					             		//判断订单是否已撤销
					             		if($result->$responseNode->sub_code == 'ORDER_IS_CANCEL'){
					             			$post_arr=array(
					                     'QueryType'=>'update_order',
					                     'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$res['ORDER_NO'].'"}',
					                     'UserGuid'=>'ODh8QHJvbWVucw--'
					                     );
					                     $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					                     $res=$this->functions->curlPostArray($url, $post_arr);
					                     if($res->state == '1'){
					                     	echo json_encode($msg);
					                     	$this->functions->debuglog($out_order_no.'撤销芝麻已撤销的订单成功');
					                     }else{
					                     	echo json_encode($fmsg);
					                     	$this->functions->debuglog($out_order_no.'撤销芝麻已撤销的订单失败！！！');
					                     }
					             		}else if($result->$responseNode->sub_code == 'UNITRADE_WITHHOLDING_PAY_FAILED'){
					             			$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$rent.'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","status":"9","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $this->functions->debuglog(json_encode($post_arr));
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($msg);
					             $this->functions->debuglog($out_order_no.'还车成功,付款失败');
					             $temp = array(
		                            'tempid'=>'a4baf1fa56444a1b8c8e86cc3ca63c9f',
		                            'first'=>'您有一笔租金需要支付,请保证账户余额充足,以免影响信用',
		                            'keyword1'=>$rtime,
		                            'keyword2'=>$res['GOODS_NAME'],
		                            'keyword3'=>$rent,
		                            'keyword4'=>'400-686-0176',
		                            'remark'=>'感谢您使用雨诺信用借,祝您生活愉快'
		                            );
		                            //$this->functions->debuglog(json_encode($temp));
		                            if($res['ISDEPOSIT'] != '1'){
		                            	$this->send_temp($res['USER_ID'],$temp);
		                            }
		                            //判断订单是否已归还
					             		}else if($result->$responseNode->sub_code == 'ORDER_GOODS_IS_RESTORED'){
					             			echo json_encode($msg);
					             			//					             			$aliorder = $this->zhima_sel($out_order_no);
					             			//					             			$out_zfb_no = $aliorder['alipay_fund_order_no'];
					             			//					             			$paytime = $aliorder['pay_time'];
					             			//					             			if(!empty($aliorder['code'])&&$aliorder['code'] == 10000){
					             			//					             				$post_arr=array(
					             			//					                         'QueryType'=>'update_order',
					             			//					                         'Params'=>'{"firstorder":"'.$firstorder.'","out_zfb_no":"'.$out_zfb_no.'","goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$rent.'","alipricetime":"'.$paytime.'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$res['ORDER_NO'].'"}',
					             			//					                         'UserGuid'=>'ODh8QHJvbWVucw--'
					             			//					                         );
					             			//					                         $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             			//					                         $res=$this->functions->curlPostArray($url, $post_arr);
					             			//					                         if($res->state == '1'){
					             			//					                         	echo json_encode($msg);
					             			//					                         	$this->functions->debuglog($out_order_no.'还车成功');
					             			//					                         }else{
					             			//					                         	echo json_encode($fmsg);
					             			//					                         	$this->functions->debuglog($out_order_no.'还车失败');
					             			//					                         }
					             			//					             			} else {
					             			//					             				echo json_encode($fmsg);
					             			//					             				$this->functions->debuglog($out_order_no.'已归还，查询金额失败');
					             			//					             			}

					             		}
					             	}
					             }

	}
	//设备异常警告
	public function lock_warning(){
		$this->functions->debuglog('设备警告:'.json_encode($_POST,JSON_UNESCAPED_UNICODE));
	}
	public function yuntest($lockcode){
        $lockurl = 'http://140.143.129.247:8081/api/openLock?extOrderNo='.$orderno.'&lockCode='.$lockcode;
		$res=$this->curlpost($lockurl, '');
		$c =  $this->object_to_array(json_decode(json_decode($res)));
		var_dump($c);
	}
	//云锁开锁
	public function new_unlock($orderno,$lockCode,$createtime,$userid,$out_order_no){
		$this->functions->debuglog($out_order_no.'发起开锁');
		$lockurl = 'http://140.143.129.247:8081/api/rent?extOrderNo='.$orderno.'&lockCode='.$lockCode;
		$res=$this->object_to_array(json_decode(json_decode($this->curlpost($lockurl, ''))));
		if($res['code'] == '000000'){
			$data=array(
			'state'=>'1',
			'msg'=>'开锁成功'
			);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
			$this->functions->debuglog($out_order_no.'开锁成功等待反馈');
			//			M('rm_lockorder')->where($where)->save(array('status'=>'1'));
		}else{
			$this->functions->debuglog('云锁开锁结果:'.json_encode($res,JSON_UNESCAPED_UNICODE));
			$msg = $res['msg'];
			//			M('rm_lockorder')->where($where)->save(array('status'=>'2'));
			$data=array(
			'state'=>'2',
			'msg'=>'开锁失败,请重试'
			);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
			$this->functions->debuglog($out_order_no.'开锁失败'.$msg);
			$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$orderno.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             $order_no=$res['ORDER_NO'];
					             $out_order_no = $res['OUT_ORDER_NO'];
					             if($res['ISDEPOSIT'] == '1'){
					             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             }else{
					             	$request = new ZhimaMerchantOrderRentCancelRequest ();
					             	$bizcontentarray=array(
				             'order_no'=>$order_no,
				             'product_code'=>'w1010100000000002858'
				             );
				             $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				             //echo '0^'.$bizcontent0;die;
				             $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				             $request->setBizContent($bizcontent);
				             //				             $signData = $request->getApiParas();
				             //				             $sign = $this->aop->rsaSign($signData, $this->aop->signType);
				             $result = $this->aop->execute($request);
				             $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				             $resultCode = $result->$responseNode->code;
				             if(!empty($resultCode)&&$resultCode == 10000){
				             	$this->functions->debuglog($out_order_no.'撤销成功');
				             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					              
				             }else{
				             	$data=array(
				             	    'state'=>'3',
				             	    'msg'=>'开锁失败，请联系工作人员'
				             	    );
				             	    $this->functions->debuglog($out_order_no.'开锁撤销失败');
				             }
					             }

		}

	}
	//反馈新锁开锁
	public function new_unlocknotice(){
		//$this->functions->debuglog($_POST['bikeCode']);
		$msg=array(
			  'code'=>'000000',
			  'msg'=>'ok'
			  );
			  //$this->functions->debuglog(json_encode($_POST));
			  //echo json_encode($msg);die;
	    $fmsg=array(
	          'code'=>'100001',
	          'msg'=>'fail'
	          );
	          //        $data=array(
	          //		  'extOrderNo'=>$_POST['extOrderNo'],
	          //		  'lockCode'=>$_POST['lockCode'],//'100000004492',
	          //		  'createTime'=>$_POST['createTime']
	          //		);
	          //		foreach ($data as $key=>$value){
	          //			$arr[$key] = $key;
	          //		}
	          //		sort($arr); //字典排序的作用就是防止因为参数顺序不一致而导致下面拼接加密不同
	          //		// 2. 将Key和Value拼接
	          //		$str = "5ADC9AD1224C38886394C3FB45BD77FC";
	          //		foreach ($arr as $k => $v) {
	          //			$str = $str.$arr[$k].$data[$v];
	          //		}
	          //		$sign=md5($str);
	          //		if($sign==$_POST['sign']){
	          //	          $post_arr=array(
	          //					             'QueryType'=>'get_orderinfo',
	          //					             'Params'=>'{"id":"'.$_POST['extOrderNo'].'"}',
	          //					             'UserGuid'=>'ODh8QHJvbWVucw--'
	          //					             );
	          //					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	          //					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
	          $post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$_POST['extOrderNo'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             $order_no=$res['ORDER_NO'];
					             $out_order_no=$res['OUT_ORDER_NO'];
					             $taketime = date("Y-m-d H:i:s");
					             //订单医院记录
					             $this->load->database();
					             $data = array(
                                     'id'=>$res['ID'],
                                     'out_order_no'=>$res['OUT_ORDER_NO'],
                                     'order_no'=>$res['ORDER_NO'],
                                     'shopname'=>$res['BORROW_SHOP_NAME']
					             );
					             $this->db->insert('orders',$data);
					             if($_POST['result']== '0'){
					             	$this->functions->debuglog($out_order_no.'开锁信息反馈开锁成功');
					             	if($res['STATUS'] != '3'){
					             		$status = '1';
					             	}else{
					             		$status = '3';
					             	}
					             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"numberno":"'.$_POST['rfid'].'","status":"'.$status.'","order_no":"'.$res['ORDER_NO'].'","takegoodstime":"'.$taketime.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $result=$this->functions->curlPostArray($url, $post_arr);
					             if($result->state != '1'){
					             	$this->functions->debuglog(json_encode($post_arr,JSON_UNESCAPED_UNICODE));
					             	$this->functions->debuglog(json_encode($result,JSON_UNESCAPED_UNICODE));
					             }
					             echo json_encode($msg);
					             $returnaddress = $this->rentaddress($res['LEASEBRANCHGUID']);
					             if($returnaddress == 'NO'){
					                 $returnaddress = '感谢您使用雨诺器械租,祝您生活愉快';
					             }else{
					                 $returnaddress = '可归还地点'.$returnaddress;
					             }
					             $temp = array(
		                            'tempid'=>'ac18f53a63064ab1a9e88962638099a5',
		                            'first'=>'租借成功，订单号:'.$res['OUT_ORDER_NO'],
		                            'keyword1'=>$res['TAKEGOODSTIME'],
		                            'keyword2'=>$taketime,
		                            'keyword3'=>'以实际归还时间为准',
		                            'keyword4'=>$res['GOODS_NAME'],
		                            'keyword5'=>$res['OUT_ORDER_NO'],
		                            'keyword6'=>'400-686-0176',
		                            'remark'=>$returnaddress
		                            );
		                            //$this->functions->debuglog(json_encode($temp));
		                            if($res['ISDEPOSIT'] != '1'){
		                            	$this->send_temp($res['USER_ID'],$temp);
		                            }
		                            fastcgi_finish_request();
					             }else{
					             	//					             	$msg=array(
					             	//			  'status'=>'2',
					             	//			  'msg'=>'开锁失败'
					             	//			  );
					             	$this->functions->debuglog($out_order_no.'开锁信息反馈开锁失败！');
					             	$dmoney = $res['DEPOSIT_AMOUNT'];
					             	$this->functions->delRedisByKey('AliRent'.$res['USER_ID']);
					             	if($res['ISDEPOSIT'] == '1'){
					             		//$this->transpay($order_no,$res['USER_ID'],$res['DEPOSIT_AMOUNT'],'开锁失败押金退换');
					             		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $this->functions->debuglog('订单:'.$order_no.'发起退款');
					             $wxpay = new WxPay();
					             $wxpay_res=$wxpay->pay_refund($order_no,$dmoney,$dmoney);
					             //退款失败 重推5次
					             if($wxpay_res['result_code'] != 'SUCCESS'){
					                 for($i=0;$i<5;$i++){
					                     $wxpay_res=$wxpay->pay_refund($order_no,$dmoney,$dmoney);
					                     if($wxpay_res['result_code'] == 'SUCCESS'){
					                         break;
					                     }
					                 }
					             }
					             echo json_encode($msg);
					             fastcgi_finish_request();
					             	}else{
					             		$request = new ZhimaMerchantOrderRentCancelRequest ();
					             		$bizcontentarray=array(
				             'order_no'=>$order_no,
				             'product_code'=>'w1010100000000002858'
				             );
				             $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				             //echo '0^'.$bizcontent0;die;
				             //$this->functions->debuglog('撤销数据'.$bizcontent0);
				             $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				             $request->setBizContent($bizcontent);
				             //				             $signData = $request->getApiParas();
				             //				             $sign = $this->aop->rsaSign($signData, $this->aop->signType);
				             $result = $this->aop->execute($request);
				             $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				             $resultCode = $result->$responseNode->code;
				             if(!empty($resultCode)&&$resultCode == 10000){
				             	$this->functions->debuglog($out_order_no.'撤销成功');
				             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             //echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             echo json_encode($msg);
					             fastcgi_finish_request();
				             }else{
				             	$this->functions->debuglog($out_order_no.'撤销失败'.json_encode($result,JSON_UNESCAPED_UNICODE));
				             	if($result->$responseNode->sub_code == 'ORDER_IS_NOT_BORROW' || $result->$responseNode->sub_code == 'ORDER_IS_CANCEL'){
				             		echo json_encode($msg);
				             	}else{
				             		echo json_encode($fmsg);
				             	}
				             	fastcgi_finish_request();
				             }
					             	}

					             }
					             //$this->functions->debuglog('qiqi开锁信息反馈:'.json_encode($msg,JSON_UNESCAPED_UNICODE));
	}
	//新锁关锁通知
	public function new_locknotice(){
		$msg=array(
			  'code'=>'000000',
			  'msg'=>'ok'
			  );
			  //$this->functions->debuglog(json_encode($_POST));
			  $_POST['createTime']=date("Y-m-d H:i:s",($_POST['createTime']/1000));
	    $fmsg=array(
	          'code'=>'100001',
	          'msg'=>'fail'
	          );
	          $where=array(
		   'extOrderNo'=>$_POST['extOrderNo']
	          );
	          //	          $post_arr=array(
	          //					             'QueryType'=>'get_orderinfo',
	          //					             'Params'=>'{"id":"'.$_POST['extOrderNo'].'"}',
	          //					             'UserGuid'=>'ODh8QHJvbWVucw--'
	          //					             );
	          //					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	          //					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
	          $post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$_POST['extOrderNo'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             //如果已经完成或者撤销则退出
					             if($res['STATUS'] == '3' || $res['STATUS'] == '2'){
					                 $this->functions->debuglog($_POST['extOrderNo'].'已经操作过，无需重复操作');
					                 echo json_encode($msg);
					                 die;
					                 $this->functions->debuglog('没die掉呢');
					             }
					             $userid = $res['USER_ID'];
					             $order_no = $res['ORDER_NO'];
					             $out_order_no = $res['OUT_ORDER_NO'];
					             $dmoney = $res['DEPOSIT_AMOUNT'];
					             $branchid = $res['LEASEBRANCHGUID'];
					             if($res['TAKEGOODSTIME']=='1970-01-01 08:00:00'){
					             	$res['TAKEGOODSTIME']=$res['CREATETIME'];
					             }
					             $this->functions->debuglog($out_order_no.'发起关锁');
					             $lockinfo=$this->object_to_array(json_decode($res['LOCKINFO']));
					             //查看是否组用过
					             $rentinfo = $this->object_to_array(json_decode($this->functions->getRedisByKey('AliRent'.$userid)));
					             if($rentinfo){
					             	$hastime = ($rentinfo['usetime']);
					             	//$this->functions->debuglog('该用户已经使用：'.$hastime);
					             }else{
					             	$hastime = 0;
					             }
					             //$this->functions->debuglog('该用户已经使用：'.$hastime);
					             if($_POST['createTime']){
					             	$rtime = $_POST['createTime'];
					             }else{
					             	$rtime=date("Y-m-d H:i:s");
					             }
					             $renttime =(strtotime($rtime)-strtotime($res['TAKEGOODSTIME']))+$hastime;
					             $rentdate=$renttime/3600;
					             $rentday=ceil($renttime/86400);
					             $rent = 0;
					             $rentcoupon = 0;
					             $hassale = 'no';
					             if(count($lockinfo)>0){
					             	for($i=0;$i<count($lockinfo);$i++){
					             		$rentcoupon = $lockinfo[$i]['USERPRICE'];
					             		if($rentdate<(float)$lockinfo[$i]['SPEC2VALUE'] && $rentdate>=(float)$lockinfo[$i]['SPEC1VALUE']){
					             			$hassale = 'yes';
					             			break;
					             		}
					             	}
					             }
					             if($hassale == 'no'){
					             	//$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$res['RENT_AMOUNT']*0.5;
					             	$post_arr=array(
					             'QueryType'=>'get_goodsinfo',
					             'Params'=>'{"goodsid":"'.$res['RENT_INFO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $ress=$this->functions->curlPostArray($url,$post_arr);
					             $result = $this->object_to_array($ress[0]);
					             if($renttime/86400>1){
					             	$a = fmod($rentdate,24);
					             	//					             	if($a==0){
					             	//					             		$rent = ((float)$rentday*$result['ONEDAYPRICE']);
					             	//					             	}else{
					             	//					             		$rent =ceil($a*2)*(float)$result['HOURPRICE']*0.5+(((float)$rentday-1)*$result['ONEDAYPRICE']);
					             	//					             	}
					             	if($a==0){
					             		$rent = ((float)$rentday*$result['ONEDAYPRICE']);
					             	}else{
					             		//特殊处理青医附院的计费
					             		if(substr($branchid,0,4)=='QYFY' && ceil($a*2)%2 != 0){
					             			$rent =(ceil($a*2)*(float)$result['HOURPRICE']*0.5-0.5)+(((float)$rentday-1)*$result['ONEDAYPRICE']);
					             		}else{
					             			$rent =ceil($a*2)*(float)$result['HOURPRICE']*0.5+(((float)$rentday-1)*$result['ONEDAYPRICE']);
					             		}
					             	}
					             }else{
					             	//$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$result['HOURPRICE']*0.5;
					             	//特殊处理青医附院的计费
					             	if(substr($branchid,0,4)=='QYFY' && ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)%2 != 0){
					             		$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$result['HOURPRICE']*0.5-0.5;
					             	}else{
					             		$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$result['HOURPRICE']*0.5;
					             	}
					             }
					             if($rent>(float)$result['ONEDAYPRICE']*(float)$rentday){
					             	$rent = (float)$result['ONEDAYPRICE']*(float)$rentday;
					             }
					             }
					             $rent = $rent + (float)$rentcoupon;
					             if($rentinfo){
					             	$rentinfo['usetime'] = $renttime;
					             	$sholdpay = $rent  ;
					             	$rent = $sholdpay - $rentinfo['money'];
					             	$rentinfo['money'] = $sholdpay;
					             	//$this->functions->debuglog('此次应付:'.$rent);
					             	$newtime = time()-$rentinfo['time'];
					             	if($newtime<86400){
					             		$newtime = 86400 - $newtime;
					             	}
					             	$this->functions->setRedisKeyVal('AliRent'.$userid,json_encode($rentinfo),$newtime);
					             }
					             if($rent>(float)$dmoney){
					             	$rent = $dmoney;
					             }
					             $rent = (String)$rent;
					             //完成订单
					             if($res['ISDEPOSIT'] == '1'){
					             	$renturn_rent = (float)$dmoney - (float)$rent;
					             	$renturn_rent = (String)$renturn_rent;
					             	//$this->transpay($order_no,$userid,$renturn_rent,'还车押金退换');
					             	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$rent.'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);		             
					             $drent = (float)$dmoney - (float)$rent;
					             $this->functions->debuglog('订单:'.$order_no.'发起退还押金');
					             $wxpay = new WxPay();
					             $wxpay_res=$wxpay->pay_refund($order_no,$dmoney,(String)$drent);
					             //如果退款失败，重复执行5次退款接口
					             if($wxpay_res['result_code'] != 'SUCCESS'){
					                 for($i=0;$i<5;$i++){
					                     $wxpay_res=$wxpay->pay_refund($order_no,$dmoney,(String)$drent);
					                     if($wxpay_res['result_code'] == 'SUCCESS'){
					                         break;
					                     }
					                 }
					             }
					             echo json_encode($msg);
					             $this->functions->debuglog($out_order_no.'还车成功');
					             fastcgi_finish_request();
					             }else{
					             	$request = new ZhimaMerchantOrderRentCompleteRequest ();
					             	$bizcontentarray=array(
				                     'order_no'=>$res['ORDER_NO'],
				                     'product_code'=>$res['PRODUCT_CODE'],
				                     'restore_time'=>$rtime,
				                     'pay_amount_type'=>'RENT',
				                     'pay_amount'=>$rent
					             	);
					             	$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
					             	//$this->functions->debuglog('转换前数据:'.$bizcontent0);
					             	//echo '0^'.$bizcontent0;die;
					             	$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
					             	//$this->functions->debuglog('转换后数据：'.$bizcontent);
					             	$request->setBizContent($bizcontent);
					             	//					             $signData = $request->getApiParas();
					             	//					             $sign = $this->aop->rsaSign($signData, $this->aop->signType);
					             	$result = $this->aop->execute($request);
					             	$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
					             	$resultCode = $result->$responseNode->code;
					             	if(!empty($resultCode)&&$resultCode == 10000){
					             		//$this->functions->debuglog('归还成功'.$res['OUT_ORDER_NO']);
					             		//$this->success('归还成功！',U('alirent_info',array('token'=>$this->token)));
					             		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$rent.'","alipricetime":"'.$rtime.'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $this->functions->debuglog(json_encode($post_arr,JSON_UNESCAPED_UNICODE));
					             $this->functions->debuglog(json_encode($res,JSON_UNESCAPED_UNICODE));
					             echo json_encode($msg);
					             //$this->functions->debuglog(json_encode($post_arr));
					             $this->functions->debuglog($out_order_no.'还车成功');
					             //$this->functions->debuglog('一天内使用情况:'.json_encode($rentinfo));
					             //$this->functions->debuglog('归还结果:'.json_encode($post_arr));
					             //echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             fastcgi_finish_request();
					             	} else {
					             		$this->functions->debuglog($res['OUT_ORDER_NO'].'归还失败'.json_encode($result,JSON_UNESCAPED_UNICODE));
					             		//判断订单是否已撤销
					             		if($result->$responseNode->sub_code == 'ORDER_IS_CANCEL'){
					             			$post_arr=array(
					                     'QueryType'=>'update_order',
					                     'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$res['LEASEBRANCHGUID'].'","goodsid":"'.$res['RENT_INFO'].'","orgguid":"88","status":"2","order_no":"'.$res['ORDER_NO'].'"}',
					                     'UserGuid'=>'ODh8QHJvbWVucw--'
					                     );
					                     $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					                     $res=$this->functions->curlPostArray($url, $post_arr);
					                     if($res->state == '1'){
					                     	echo json_encode($msg);
					                     	$this->functions->debuglog($out_order_no.'撤销芝麻已撤销的订单成功');
					                     	fastcgi_finish_request();
					                     }else{
					                     	echo json_encode($fmsg);
					                     	$this->functions->debuglog($out_order_no.'撤销芝麻已撤销的订单失败！！！');
					                     	fastcgi_finish_request();
					                     }
					             		}else if($result->$responseNode->sub_code == 'UNITRADE_WITHHOLDING_PAY_FAILED'){
					             			$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$rent.'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","status":"9","order_no":"'.$res['ORDER_NO'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($msg);
					             $this->functions->debuglog($out_order_no.'还车成功,付款失败');
					             $temp = array(
		                            'tempid'=>'a4baf1fa56444a1b8c8e86cc3ca63c9f',
		                            'first'=>'您有一笔租金需要支付,请保证账户余额充足,以免影响信用',
		                            'keyword1'=>$rtime,
		                            'keyword2'=>$res['GOODS_NAME'],
		                            'keyword3'=>$rent,
		                            'keyword4'=>'400-686-0176',
		                            'remark'=>'感谢您使用雨诺信用借,祝您生活愉快'
		                            );
		                            //$this->functions->debuglog(json_encode($temp));
		                            if($res['ISDEPOSIT'] != '1'){
		                            	$warning=$this->send_temp($res['USER_ID'],$temp);
		                            	$this->functions->debuglog($out_order_no.'订单付款失败，推送信息结果：'.$warning);
		                            }
		                            //判断订单是否已归还
					             		}else if($result->$responseNode->sub_code == 'ORDER_GOODS_IS_RESTORED'){
					             			//					             			$aliorder = $this->zhima_sel($out_order_no);
					             			//					             			if($aliorder['alipay_fund_order_no']){
					             			//					             				$out_zfb_no = $aliorder['alipay_fund_order_no'];
					             			//					             			}else{
					             			//					             				$out_zfb_no = '';
					             			//					             			}
					             			//					             			$paytime = $aliorder['pay_time'];
					             			//					             			if(!empty($aliorder['code'])&&$aliorder['code'] == 10000){
					             			//					             				$post_arr=array(
					             			//					                         'QueryType'=>'update_order',
					             			//					                         'Params'=>'{"out_zfb_no":"'.$out_zfb_no.'","goodsid":"'.$res['RENT_INFO'].'","branchguid":"'.$res['LEASEBRANCHGUID'].'","money":"'.$rent.'","alipricetime":"'.$paytime.'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$res['ORDER_NO'].'"}',
					             			//					                         'UserGuid'=>'ODh8QHJvbWVucw--'
					             			//					                         );
					             			//					                         $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             			//					                         $res=$this->functions->curlPostArray($url, $post_arr);
					             			//					                         $this->functions->debuglog('已归还订单修改订单状态数据'.json_encode($res,JSON_UNESCAPED_UNICODE));
					             			echo json_encode($msg);
					             			//					                         if($res->state == '1'){
					             			fastcgi_finish_request();
					             			//					                         	echo json_encode($msg);
					             			//					                         	$this->functions->debuglog($out_order_no.'还车成功');
					             			//					                         }else{
					             			//					                         	echo json_encode($fmsg);
					             			//					                         	$this->functions->debuglog($out_order_no.'还车失败');
					             			//					                         }
					             			//					             			} else {
					             			//					             				echo json_encode($fmsg);
					             			//					             				$this->functions->debuglog($out_order_no.'已归还，查询金额失败');
					             			//					             			}

					             		}
					             	}
					             }

	}
	//附近门店距离计算
	public function near_store(){
		$post_arr=array(
					             'QueryType'=>'getBranchInfo',
					             'Params'=>'{"businessesId":"'.$_POST['businessesId'].'","goodsid":"'.$_POST['goodsid'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//地图附近门店
	public function nearstore(){
		$_POST['lat']='36.072517005321';
		$_POST['lng']='120.37618412944';
		$post_arr=array(
					             'QueryType'=>'getBranch',
					             'Params'=>'{"lat":"'.$_POST['lat'].'","lng":"'.$_POST['lng'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=curlPostArray($url, $post_arr);
					             echo"<pre>";
					             var_dump($res);
					             //echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//主单预计归还时间
	public function firstreturn_time(){
		$create_time=date("Y-m-d H:i:s");
		$time=(int)$_POST['borrow_cycle'];
		$timestart=$create_time;
		if($time == 31){
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
		}else{
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$data=array(
		   'create_time' => $create_time,
		   'return_time' => $returntime
		);
		echo json_encode($data);
	}
	//计算预计归还时间  -2017-8-10-
	public function return_time(){
		if(!$_POST['create_time']){
			$create_time=date("Y-m-d H:i:s");
		}else{
			$create_time=$_POST['create_time'];
		}
		$time=(int)$_POST['borrow_cycle'];
		$timestart=$create_time;
		if($time == 31){
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
		}else{
			$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$_POST['parentguid'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $res=$this->object_to_array($res);
					             //					             if(strtotime($res['ESTIMATERETURNDATETIME'])<strtotime($returntime)){
					             //					             	$data = array(
					             //					             	    'data'=>1,
					             //					             	    'time'=>$res['ESTIMATERETURNDATETIME']
					             //					             	);
					             //					             	echo json_encode($data);
					             //					             }else{
					             $data = array(
					             	    'data'=>2,
					             	    'time'=>$returntime
					             );
					             //$this->functions->debuglog('returntime:'.json_encode($data));
					             echo json_encode($data);
					             //					             }

	}
	//用户提货
	public function pick_up(){
		$request = new ZhimaMerchantOrderRentModifyRequest ();
		$time=(int)$_POST['borrow_cycle'];
		$timestart=date("Y-m-d H:i:s");
		if($time == 31){
			$expiry_time = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}else{
			$expiry_time = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$bizcontentarray=array(
				'order_no'=>$_POST['order_no'],
				'product_code'=>$_POST['product_code'],
				'borrow_time'=>$timestart,
				'expiry_time'=>$expiry_time,
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		//$this->functions->debuglog('转换前数据:'.$bizcontent0);
		//echo '0^'.$bizcontent0;die;
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		//$this->functions->debuglog('转换后数据：'.$bizcontent);
		$request->setBizContent($bizcontent);
		//		$signData = $request->getApiParas();
		//		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		//$this->functions->debuglog('修改结果:'.json_encode($result));
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			$post_arr=array(
					'QueryType'=>'update_order',
					'Params'=>'{"branchguid":"'.$_POST['branchguid'].'","goodsid":"'.$_POST['goodsid'].'","takegoodstime":"'.$timestart.'","returntime":"'.$expiry_time.'","iswarning":"0","numberno":"'.$_POST['numberno'].'","status":"1","order_no":"'.$_POST['order_no'].'"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					//$this->functions->debuglog('修改订单的信息：'.json_encode($post_arr));
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
					echo json_encode($res);
		} else {
			//echo "失败".iconv("GBK", "UTF-8",$result->$responseNode->sub_msg);
			$msg=array(
            	   'msg'=>'修改失败',
            	   'state'=>'1003'
            	   );
            	   echo json_encode($msg);
		}
	}
	//续租
	public function rent_continue(){
		$aliorder = $this->zhima_sel($_POST['out_order_no']);
		if(!empty($aliorder['code'])&&$aliorder['code'] == 10000){
			if(!$_POST['create_time']){
				$create_time=date("Y-m-d H:i:s");
			}else{
				$create_time=$_POST['create_time'];
			}
			$time=(int)$_POST['borrow_cycle'];
			$timestart=$create_time;
			if($time == 31){
				$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
			}else{
				$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
			}
			$order_no = $aliorder['order_no'];
			$datas= array (
          'id'=>uniqid(),
		  'create_time'=>$create_time,
		  'return_time'=>$returntime,
          'token'=>$_info['token'],
          'out_order_no'=>$_POST['out_order_no'],
          'order_no'=>$aliorder['order_no'],
          'user_id'=>$aliorder['user_id'],
          'admit_state'=>$aliorder['admit_state'],
          'name'=>'BK',
          'product_code'=>$_POST['product_code'],
          'goods_name'=>$_POST['goods_name'],
          'rent_info'=>$_POST['rent_info'],
          'rent_unit'=>$_POST['rent_unit'],
          'rent_amount'=>$_POST['rent_amount'],
          'deposit_amount'=>$_POST['deposit_amount'],
          'deposit_state'=>$_POST['deposit_state'],
          'borrow_cycle'=>$_POST['borrow_cycle'],
          'borrow_cycle_unit'=>$_POST['borrow_cycle_unit'],
          'borrow_shop_name'=>$_POST['borrow_shop_name'],
		  'leaseprice'=>$_POST['leaseprice'],
          'leasebranchguid'=>$_POST['leasebranchguid'],
          'status'=>'1', 
          'orgguid'=>$_POST['orgguid'],
		  'has_sub'=>$_POST['has_sub'],
		  'parentguid'=>$_POST['parentguid'],
		  'phone'=>$_POST['phone'],
		  'assistantno'=>$_POST['assistantno'],
		  'assistantname'=>$_POST['assistantname'],
		  'assistantphone'=>$_POST['assistantphone'],
		  'takegoodstime'=>$create_time
			);
			if(!empty($_POST['alidepose'])){
			    $datas['alideposit'] = $_POST['alidepose'];
			}
			$post_arr=array(
         'QueryType'=>'sub_order',
		 'Params'=>json_encode($datas,true),
		 'UserGuid'=>'ODh8QHJvbWVucw--'
		 );
		 $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
		 $res=$this->functions->curlPostArray($url, $post_arr);
		 //$this->functions->debuglog('续租创建订单:'.json_encode($res,JSON_UNESCAPED_UNICODE));
		 //买断完成
		 if($_POST['buy'] == 'ye'){
		 	$request = new ZhimaMerchantOrderRentCompleteRequest ();
		 	$rtime = date("Y-m-d H:i:s");
		 	$bizcontentarray=array(
				'order_no'=>$aliorder['order_no'],
				'product_code'=>$_POST['product_code'],
				'restore_time'=>$rtime,
				'pay_amount_type'=>'RENT',
				'pay_amount'=>$_POST['rent_amount']
		 	);
		 	$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		 	//$this->functions->debuglog('转换前数据:'.$bizcontent0);
		 	//echo '0^'.$bizcontent0;die;
		 	$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		 	//$this->functions->debuglog('转换后数据：'.$bizcontent);
		 	$request->setBizContent($bizcontent);
		 	//		 	$signData = $request->getApiParas();
		 	//		 	$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		 	$result = $this->aop->execute($request);
		 	$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		 	$resultCode = $result->$responseNode->code;
		 	if(!empty($resultCode)&&$resultCode == 10000){
		 		//$this->success('归还成功！',U('alirent_info',array('token'=>$this->token)));
		 		$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"goodsid":"'.$_POST['rent_info'].'","branchguid":"'.$_POST['leasebranchguid'].'","money":"'.$_POST['rent_amount'].'","returntime":"'.$rtime.'","returngoodstime":"'.date("Y-m-d H:i:s").'","iswarning":"2","orgguid":"88","status":"7","order_no":"'.$order_no.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);

		 	} else {
		 		//$this->functions->debuglog('买断完成失败');
		 	}
		 }
		 if($_POST['l_parentguid']=='0'){
		 	/*修改订单关系*/
		  $post_arr=array(
		  'QueryType'=>'update_order',
		  'Params'=>'{"has_sub":"1","order_no":"'.$_POST['l_order_no'].'"}',
		  'UserGuid'=>'ODh8QHJvbWVucw--'
		  );
		  $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
		  $res=$this->functions->curlPostArray($url, $post_arr);
		  //echo json_encode($res,JSON_UNESCAPED_UNICODE);
		 }
		 //完成上一笔订单
		 $request = new ZhimaMerchantOrderRentCompleteRequest ();
		 $bizcontentarray=array(
				'order_no'=>$_POST['l_order_no'],
				'product_code'=>$_POST['l_product_code'],
				'restore_time'=>date("Y-m-d H:i:s"),
				'pay_amount_type'=>'RENT',
				'pay_amount'=>$_POST['l_pay_amount']
		 );
		 $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		 //$this->functions->debuglog('转换前数据:'.$bizcontent0);
		 //echo '0^'.$bizcontent0;die;
		 $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		 //$this->functions->debuglog('转换后数据：'.$bizcontent);
		 $request->setBizContent($bizcontent);
		 //		 $signData = $request->getApiParas();
		 //		 $sign = $this->aop->rsaSign($signData, $this->aop->signType);
		 $result = $this->aop->execute($request);
		 $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		 $resultCode = $result->$responseNode->code;
		 if(!empty($resultCode)&&$resultCode == 10000){
		 	//$this->success('归还成功！',U('alirent_info',array('token'=>$this->token)));
		 	$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"goodsid":"'.$_POST['rent_info'].'","branchguid":"'.$_POST['leasebranchguid'].'","money":"'.$_POST['l_pay_amount'].'","returngoodstime":"'.$rtime.'","returngoodstime":"'.date("Y-m-d H:i:s").'","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$_POST['l_order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
		 } else {
				$msg=array(
            	   'msg'=>'归还失败',
            	   'state'=>'1003'
            	   );
            	   echo json_encode($msg,JSON_UNESCAPED_UNICODE);
            	   //$this->functions->debuglog('归还失败:'.json_encode($result,JSON_UNESCAPED_UNICODE));
		 }

		} else {
			echo "没有查询到订单";
		}
	}
	//列表页
	public function alirent_info(){
		$info=M('rm_alirent_info')->where(array('token'=>$_GET['token']))->select();
		$this->assign('info',$info);
		$this->display();
	}
	//商家确认订单
	public function sure_rent(){
		$post_arr=array(
					'QueryType'=>'update_order',
					'Params'=>'{"clerkno":"'.$_POST['clerk'].'","isclerk":"1","status":"1","order_no":"'.$_POST['order_no'].'"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
					echo json_encode($res,JSON_UNESCAPED_UNICODE);
	}
	//选择租用时间
	public function alirent_howlong(){
		$this->assign('goods_name',$_POST['goods_name']);
		$this->assign('product_code',$_POST['product_code']);
		$this->assign('rent_info',$_POST['rent_info']);
		$this->assign('borrow_shop_name',$_POST['borrow_shop_name']);
		$this->assign('deposit_amount',$_POST['deposit_amount']);
		$this->assign('rent_amount',$_POST['rent_amount']);
		$this->assign('rent_unit',$_POST['rent_unit']);
		$this->display();
	}
	//直接续租查询
	public function toreturn(){
		$post_arr=array(
					'QueryType'=>'get_orderinfo',
					'Params'=>'{"parentguid":"'.$_POST['parentguid'].'","user_id":"'.$_POST['userid'].'"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
					echo json_encode($res,JSON_UNESCAPED_UNICODE);

	}
	//发放修改订单日期
	public function updata_time(){
		$_POST=json_encode($_POST);
		//$this->functions->debuglog('传入数据'.$_POST);
		$_POST=json_decode($_POST,true);
		if($_POST['sign']){
			if($_POST['sign']!=md5('ROMENS'.date('Ymd'))){
				$msg=array(
            	   'msg'=>'签名错误',
            	   'state'=>'1001'
            	   );
            	   echo json_encode($msg);exit;
                //exit('ERROR SIGN');
			}
		}else{
			$msg=array(
            	   'msg'=>'未传入签名',
            	   'state'=>'1002'
            	   );
            	   echo json_encode($msg);exit;
            	   //exit('NO SIGN');
		}

		if(!$_POST['order_no']){
			$msg=array(
            	   'msg'=>'未传入订单号',
            	   'state'=>'1004'
            	   );
            	   echo json_encode($msg);exit;
		}
		if(!$_POST['product_code']){
			$msg=array(
            	   'msg'=>'未传入产品号',
            	   'state'=>'1005'
            	   );
            	   echo json_encode($msg);exit;
		}
		if(!$_POST['borrow_time']){
			$msg=array(
            	   'msg'=>'未传入开始时间',
            	   'state'=>'1006'
            	   );
            	   echo json_encode($msg);exit;
		}
		if(!$_POST['borrow_cycle']){
			$msg=array(
            	   'msg'=>'未传入租借时间',
            	   'state'=>'1007'
            	   );
            	   echo json_encode($msg);exit;
		}
		$request = new ZhimaMerchantOrderRentModifyRequest ();
		$time=(int)$_POST['borrow_cycle'];
		$timestart=$_POST['borrow_time'];
		if($time == 31){
			$expiry_time = date("Y-m-d 23:59:59",strtotime("$timestart + $time day"));
		}else{
			$expiry_time = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
		}
		$bizcontentarray=array(
				'order_no'=>$_POST['order_no'],
				'product_code'=>$_POST['product_code'],
				'borrow_time'=>$_POST['borrow_time'],
				'expiry_time'=>$expiry_time,
		        'sign'=>$_POST['sign']
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		//$this->functions->debuglog('转换前数据:'.$bizcontent0);
		//echo '0^'.$bizcontent0;die;
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		//$this->functions->debuglog('转换后数据：'.$bizcontent);
		$request->setBizContent($bizcontent);
		//		$signData = $request->getApiParas();
		//		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			$msg=array(
            	   'msg'=>'修改成功',
            	   'state'=>'1',
			       'time'=>$expiry_time
			);
			echo json_encode($msg);
		} else {
			//echo "失败".iconv("GBK", "UTF-8",$result->$responseNode->sub_msg);
			$msg=array(
            	   'msg'=>'修改失败',
            	   'state'=>'1003'
            	   );
            	   echo json_encode($msg);
		}
		//$this->functions->debuglog('res:'.json_encode($result));
	}
	//创建订单
	public function alirent_create(){
		$time=date("YmdHis").rand(1000,9999);
		$request = new ZhimaMerchantOrderRentCreateRequest();
		$bizcontentarray=array(
				'invoke_type'=>'WINDOWS',
				'invoke_return_url'=>'http://weixin.yiyao365.cn/alizhima/return_url.php',
				'invoke_state'=>array(
						'product_code'=>$_POST['product_code'],
						'goods_name'=>$_POST['goods_name'],
						'rent_info'=>$_POST['rent_info'],
						'rent_unit'=>$_POST['rent_unit'],
						'rent_amount'=>$_POST['rent_amount'],
						'deposit_amount'=>$_POST['deposit_amount'],
						'deposit_state'=>'Y',
						'borrow_cycle'=>$_POST['borrow_cycle'],
						'borrow_cycle_unit'=>$_POST['borrow_cycle_unit'],
						'borrow_shop_name'=>$_POST['borrow_shop_name']
		),
				'out_order_no'=>$time,
				'product_code'=>$_POST['product_code'],
				'goods_name'=>$_POST['goods_name'],
				'rent_info'=>$_POST['rent_info'],
				'rent_unit'=>$_POST['rent_unit'],
				'rent_amount'=>$_POST['rent_amount'],
				'deposit_amount'=>$_POST['deposit_amount'],
				'deposit_state'=>'Y',
				'borrow_cycle'=>$_POST['borrow_cycle'],
				'borrow_cycle_unit'=>$_POST['borrow_cycle_unit'],
				'borrow_shop_name'=>$_POST['borrow_shop_name']
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$request->setBizContent($bizcontent);
		$result = $this->aop->pageExecute($request,"GET");
		header("Location:$result");
		//echo $result;
	}
	//完成订单
	public function myalirent_complete(){
		$request = new ZhimaMerchantOrderRentCompleteRequest ();
		$rtime=date("Y-m-d H:i:s");
		$bizcontentarray=array(
				'order_no'=>$_POST['order_no'],
				'product_code'=>$_POST['product_code'],
				'restore_time'=>$rtime,
				'pay_amount_type'=>$_POST['pay_amount_type'],
				'pay_amount'=>$_POST['pay_amount']
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		//$this->functions->debuglog('转换前数据:'.$bizcontent0);
		//echo '0^'.$bizcontent0;die;
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		//$this->functions->debuglog('转换后数据：'.$bizcontent);
		$request->setBizContent($bizcontent);
		//		$signData = $request->getApiParas();
		//		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			if($result->$responseNode->alipay_fund_order_no){
				$out_zfb_no = $result->$responseNode->alipay_fund_order_no;
			}else{
				$out_zfb_no = '';
			}
			//			//$this->success('归还成功！',U('alirent_info',array('token'=>$this->token)));
			$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"out_zfb_no":"'.$out_zfb_no.'","goodsid":"'.$_POST['goodsid'].'","branchguid":"'.$_POST['branchguid'].'","money":"'.$_POST['pay_amount'].'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$_POST['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             $this->functions->debuglog('归还结果:'.json_encode($result));
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
		} else {
			echo json_encode($result,JSON_UNESCAPED_UNICODE);
			//var_dump($result->$responseNode->sub_code);
		}
	}
	//押金模式完成订单
	public function myrent_complete(){
	    $post_arr=array(
	       'QueryType'=>'get_goodsinfo',
	       'Params'=>'{"goodsid":"'.$_POST['goodsid'].'"}',
	       'UserGuid'=>'ODh8QHJvbWVucw--'
	     );
	     $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	     $res=$this->functions->curlPostArray($url,$post_arr);
	     $result = $this->object_to_array($res[0]);
	     if($_POST['alidepose'] != $result['DEPOSIT_AMOUNT']){
	         if($_POST['dmoney']>=$_POST['pay_amount']){
	             $this->myalirent_complete();
	             $refund_data = array(
	                 'out_order_no'=>$_POST['out_order_no'],
	                 'money'=>$_POST['alidepose'],
	                 'out_request_no'=>'r'.$_POST['out_order_no']
	             );
	             $refund_url = "http://xyj.yiyao365.cn/AliRentRomens/index.php/AliRent/AliRent/ali_refund";
	             $refund_res = $this->functions->curlPostArray($refund_url, $refund_data);
	         }else{
	             //租金大于支付宝押金
	             $submoney = $_POST['pay_amount'] - $_POST['dmoney'];
	             $request = new ZhimaMerchantOrderRentCompleteRequest ();
	             $rtime=date("Y-m-d H:i:s");
	             $bizcontentarray=array(
	                 'order_no'=>$_POST['order_no'],
	                 'product_code'=>$_POST['product_code'],
	                 'restore_time'=>$rtime,
	                 'pay_amount_type'=>$_POST['pay_amount_type'],
	                 'pay_amount'=>$_POST['dmoney']
	             );
	             $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
	             //$this->functions->debuglog('转换前数据:'.$bizcontent0);
	             //echo '0^'.$bizcontent0;die;
	             $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
	             //$this->functions->debuglog('转换后数据：'.$bizcontent);
	             $request->setBizContent($bizcontent);
	             //		$signData = $request->getApiParas();
	             //		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
	             $result = $this->aop->execute($request);
	             $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
	             $resultCode = $result->$responseNode->code;
	             if(!empty($resultCode)&&$resultCode == 10000){
	                 if($result->$responseNode->alipay_fund_order_no){
	                     $out_zfb_no = $result->$responseNode->alipay_fund_order_no;
	                 }else{
	                     $out_zfb_no = '';
	                 }
	                 //			//$this->success('归还成功！',U('alirent_info',array('token'=>$this->token)));
	                 $post_arr=array(
	                     'QueryType'=>'update_order',
	                     'Params'=>'{"out_zfb_no":"'.$out_zfb_no.'","goodsid":"'.$_POST['goodsid'].'","branchguid":"'.$_POST['branchguid'].'","money":"'.$_POST['pay_amount'].'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$_POST['order_no'].'"}',
	                     'UserGuid'=>'ODh8QHJvbWVucw--'
	                 );
	                 $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	                 $res=$this->functions->curlPostArray($url, $post_arr);
	                 $this->functions->debuglog('归还结果:'.json_encode($result));
	                 echo json_encode($res,JSON_UNESCAPED_UNICODE);
	                 $refund_data = array(
	                     'out_order_no'=>$_POST['out_order_no'],
	                     'money'=>$_POST['alidepose'] - $submoney,
	                     'out_request_no'=>'r'.$_POST['out_order_no']
	                 );
	                 $refund_url = "http://xyj.yiyao365.cn/AliRentRomens/index.php/AliRent/AliRent/ali_refund";
	                 $refund_res = $this->functions->curlPostArray($refund_url, $refund_data);
	             } else {
	                 echo json_encode($result,JSON_UNESCAPED_UNICODE);
	                 //var_dump($result->$responseNode->sub_code);
	             }
	         }
	     }else{
	         $rtime=date("Y-m-d H:i:s");
	         //全押金模式归还
	         $post_arr=array(
	             'QueryType'=>'update_order',
	             'Params'=>'{"goodsid":"'.$_POST['goodsid'].'","branchguid":"'.$_POST['branchguid'].'","money":"'.$_POST['pay_amount'].'","returngoodstime":"'.$rtime.'","isclerk":"1","iswarning":"2","orgguid":"88","status":"3","order_no":"'.$_POST['order_no'].'"}',
	             'UserGuid'=>'ODh8QHJvbWVucw--'
	         );
	         $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	         $res=$this->functions->curlPostArray($url, $post_arr);
	         $this->functions->debuglog('归还结果:'.json_encode($result));
	         echo json_encode($res,JSON_UNESCAPED_UNICODE);
	         $refund_data = array(
	             'out_order_no'=>$_POST['out_order_no'],
	             'money'=>$_POST['alidepose'] - $_POST['pay_amount'],
	             'out_request_no'=>'r'.$_POST['out_order_no']
	         );
	         $refund_url = "http://xyj.yiyao365.cn/AliRentRomens/index.php/AliRent/AliRent/ali_refund";
	         $refund_res = $this->functions->curlPostArray($refund_url, $refund_data);
	     }
	}
	//完成订单
	public function alirent_complete(){
		$_POST=json_encode($_POST);
		$this->functions->debuglog('传入数据'.$_POST);
		$_POST=json_decode($_POST,true);
		if($_POST['sign']){
			if($_POST['sign']!=md5('ROMENS'.date('Ymd'))){
				$msg=array(
            	   'msg'=>'签名错误',
            	   'state'=>'1001'
            	   );
            	   echo json_encode($msg,JSON_UNESCAPED_UNICODE);exit;
                //exit('ERROR SIGN');
			}
		}else{
			$msg=array(
            	   'msg'=>'未传入签名',
            	   'state'=>'1002'
            	   );
            	   echo json_encode($msg,JSON_UNESCAPED_UNICODE);exit;
            	   //exit('NO SIGN');
		}

		if(!$_POST['order_no']){
			$msg=array(
            	   'msg'=>'未传入订单号',
            	   'state'=>'1004'
            	   );
            	   echo json_encode($msg,JSON_UNESCAPED_UNICODE);exit;
		}
		if(!$_POST['product_code']){
			$msg=array(
            	   'msg'=>'未传入产品号',
            	   'state'=>'1005'
            	   );
            	   echo json_encode($msg,JSON_UNESCAPED_UNICODE);exit;
		}
		if(!$_POST['pay_amount'] && $_POST['pay_amount']!=0){
			$msg=array(
            	   'msg'=>'未传入金额',
            	   'state'=>'1006'
            	   );
            	   echo json_encode($msg,JSON_UNESCAPED_UNICODE);exit;
		}
		if(!$_POST['pay_amount_type']){
			$msg=array(
            	   'msg'=>'未传入金额类型',
            	   'state'=>'1007'
            	   );
            	   echo json_encode($msg,JSON_UNESCAPED_UNICODE);exit;
		}
		$request = new ZhimaMerchantOrderRentCompleteRequest ();
		$bizcontentarray=array(
				'order_no'=>$_POST['order_no'],
				'product_code'=>$_POST['product_code'],
				'restore_time'=>date("Y-m-d H:i:s"),
				'pay_amount_type'=>$_POST['pay_amount_type'],
				'pay_amount'=>$_POST['pay_amount']
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		$this->functions->debuglog('归还数据:'.$bizcontent0);
		//echo '0^'.$bizcontent0;die;
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		//$this->functions->debuglog('转换后数据：'.$bizcontent);
		$request->setBizContent($bizcontent);
		//		$signData = $request->getApiParas();
		//		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$this->functions->debuglog('归还结果:'.$result->$responseNode->alipay_fund_order_no);
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			//$this->success('归还成功！',U('alirent_info',array('token'=>$this->token)));
			$out_zfb_no = $result->$responseNode->alipay_fund_order_no;
			$msg=array(
            	   'msg'=>'归还成功',
            	   'state'=>'1',
            	   'out_zfb_no'=>$out_zfb_no
			);
			echo json_encode($msg,JSON_UNESCAPED_UNICODE);
		} else {
			$this->functions->debuglog('归还失败:'.json_encode($result,JSON_UNESCAPED_UNICODE));
			if($result->$responseNode->sub_code == 'UNITRADE_WITHHOLDING_PAY_FAILED'){
				$msg=array(
            	   'msg'=>'余额不足',
            	   'state'=>'1008'
            	   );
            	   $post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"orderno":"'.$_POST['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             $temp = array(
		                            'tempid'=>'a4baf1fa56444a1b8c8e86cc3ca63c9f',
		                            'first'=>'您有一笔租金需要支付,请保证账户余额充足,以免影响信用,以免影响信用',
		                            'keyword1'=>$bizcontentarray['restore_time'],
		                            'keyword2'=>$res['GOODS_NAME'],
		                            'keyword3'=>$bizcontentarray['pay_amount'],
		                            'keyword4'=>'400-686-0176',
		                            'remark'=>'感谢您使用雨诺信用借,祝您生活愉快'
		                            );
		                            //$this->functions->debuglog(json_encode($temp));
		                            if($res['ISDEPOSIT'] != '1'){
		                            	$this->send_temp($res['USER_ID'],$temp);
		                            }
		                            echo json_encode($msg,JSON_UNESCAPED_UNICODE);die;
			}else{
				$aliorder = $this->zhima_sel($_POST['out_order_no']);
				if(!empty($aliorder['code'])&&$aliorder['code'] == 10000){
					//echo $result->$responseNode->order_no."<br/>状态:".iconv("GBK", "UTF-8",$result->$responseNode->use_state)."<br/>借出时间:".$result->$responseNode->borrow_time."归还时间:".$result->$responseNode->restore_time;
					if($aliorder['use_state'] == 'restore' && $aliorder['pay_amount'] == $_POST['pay_amount'] && $aliorder['pay_status'] == 'PAY_SUCCESS'){
						$out_zfb_no =$aliorder['alipay_fund_order_no'];
						$msg=array(
            	   'msg'=>'归还成功',
            	   'state'=>'1',
				   'out_zfb_no'=>$out_zfb_no 
						);
					}else{
						$msg=array(
            	   'msg'=>'归还失败',
            	   'state'=>'1003'
            	   );
					}
				} else {
					$msg=array(
            	   'msg'=>'归还失败',
            	   'state'=>'1003'
            	   );
				}

			}
			echo json_encode($msg,JSON_UNESCAPED_UNICODE);
		}
		//$this->functions->debuglog('res:'.json_encode($result));

	}
	//查询订单
	public function alirent_query($out_order_no){
		echo "<pre>";
		$res = $this->zhima_sel($out_order_no);
		var_dump($res);
		if(!empty($res['code'])&&$res['code'] == 10000){
			echo $res['order_no']."<br/>状态:".$res['use_state']."支付状态：".$res['pay_status']."<br/>借出时间:".$res['borrow_time']."归还时间:".$res['restore_time'];
		} else {
			echo "失败".$res;
		}
	}
	//云锁延时撤销
	public function text_tc(){
		$request = new ZhimaMerchantOrderRentCancelRequest ();
		$bizcontentarray=array(
				'order_no'=>$_POST['order_no'],
				'product_code'=>'w1010100000000002858'
				);
				$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				//echo '0^'.$bizcontent0;die;
				$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				$request->setBizContent($bizcontent);
				//				$signData = $request->getApiParas();
				//				$sign = $this->aop->rsaSign($signData, $this->aop->signType);
				$result = $this->aop->execute($request);
				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				$resultCode = $result->$responseNode->code;
				if(!empty($resultCode)&&$resultCode == 10000){
					echo "1";
				}else{
					echo "2";
				}
	}
	//测试撤销
	public function tc($order_no){
		$request = new ZhimaMerchantOrderRentCancelRequest ();
		$bizcontentarray=array(
				'order_no'=>$order_no,
				'product_code'=>'w1010100000000002858'
				);
				$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				//echo '0^'.$bizcontent0;die;
				$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				$request->setBizContent($bizcontent);
				//				$signData = $request->getApiParas();
				//				$sign = $this->aop->rsaSign($signData, $this->aop->signType);
				$result = $this->aop->execute($request);
				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				$resultCode = $result->$responseNode->code;
				if(!empty($resultCode)&&$resultCode == 10000){
					echo "成功";
				}else{
					echo "失败";
				}
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
		//		$signData = $request->getApiParas();
		//		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			$data=array(
				             	    'state'=>1,
				             	    'msg'=>'成功'
				             	    );
				             	    echo json_encode($data,JSON_UNESCAPED_UNICODE);
				             	    $this->functions->dubuglog($_POST['order_no'].'订单后台撤销');
		}else{
			$data=array(
				             	    'state'=>2,
				             	    'msg'=>'失败'
				             	    );
				             	    echo json_encode($data,JSON_UNESCAPED_UNICODE);
		}
	}
	//小程序创建订单
	public function xcx_create(){
		$this->functions->debuglog($_POST['phone'].'用户租借,订单号：'.$_POST['out_order_no']);
		if(!empty($_POST['usercouponid'])){
		    $this->load->database();
		    //$sql = "UPDATE alicoupon SET orderno = '$coupon_orderno' WHERE id = '$coupon_id';";
		    $data = array(		        
		        'orderno' => $_POST['out_order_no'],	
		        'status'=>'2'
		    );	    
		    $this->db->where('id', $_POST['usercouponid']);	    
		    $this->db->update('alicoupon', $data);
		}
		$cons=$this->object_to_array(json_decode($_POST['consumables']));
		$consumables=array();
		$consumablesamount=0;
		if(count($cons)== '1'){
			$consumables['consumablesguid']=$cons[0]['GUID'];
			$consumables['goodsprice']=$cons[0]['PRODUCT_PRICE'];
			$consumables['price']=$cons[0]['PRODUCT_PRICE'];
			$consumables['num']=$cons[0]['num'];
			$consumables['tamount']=(int)$cons[0]['PRODUCT_PRICE']*(int)$cons[0]['num'];
			$consumablesamount=$consumables['tamount'];
		}else{
			for($i=0;$i<count($cons);$i++){
				$consumables[$i]['consumablesguid']=$cons[$i]['GUID'];
				$consumables[$i]['goodsprice']=$cons[$i]['PRODUCT_PRICE'];
				$consumables[$i]['price']=$cons[$i]['PRODUCT_PRICE'];
				$consumables[$i]['num']=$cons[$i]['num'];
				$consumables[$i]['tamount']=(int)$cons[$i]['PRODUCT_PRICE']*(int)$cons[$i]['num'];
				$consumablesamount=$consumablesamount+$consumables[$i]['tamount'];
			}
		}
		$aliorder = $this->zhima_sel($_POST['out_order_no']);
		if(!empty($aliorder['code'])&&$aliorder['code'] == 10000){
			if(!$_POST['create_time']){
				$create_time=date("Y-m-d H:i:s");
			}else{
				$create_time=$_POST['create_time'];
			}
			$time=(int)$_POST['borrow_cycle'];
			$timestart=$create_time;
			if($time == 31){
				$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
			}else{
				$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
			}
			if($_POST['serialno'] == '' && $_POST['lockCode'] == ''){
				$status = '0';
			}else if($_POST['serialno'] != ''){
				$status = '1';
			}else{
				$status = '7';
			}
			$redischeck=strtotime($create_time);
			$datas= array (
          'id'=>uniqid(),
		  'create_time'=>$create_time,
		  'return_time'=>$returntime,
          'token'=>'',//$_info['token'],
          'out_order_no'=>$_POST['out_order_no'],
          'order_no'=>$aliorder['order_no'],
          'user_id'=>$aliorder['user_id'],
          'admit_state'=>$aliorder['admit_state'],
          'name'=>'BK',
          'product_code'=>$_POST['product_code'],
          'goods_name'=>$_POST['goods_name'],
          'rent_info'=>$_POST['rent_info'],
          'rent_unit'=>$_POST['rent_unit'],
          'rent_amount'=>$_POST['rent_amount'],
          'deposit_amount'=>$_POST['deposit_amount'],
          'deposit_state'=>$_POST['deposit_state'],
          'borrow_cycle'=>$_POST['borrow_cycle'],
          'borrow_cycle_unit'=>$_POST['borrow_cycle_unit'],
          'borrow_shop_name'=>$_POST['borrow_shop_name'],
		  'leaseprice'=>$_POST['leaseprice'],
          'leasebranchguid'=>$_POST['leasebranchguid'],
          'status'=>$status, 
          'orgguid'=>$_POST['orgguid'],
		  'phone'=>$_POST['phone'],
		  'user_name'=>'',//S('user_name'.$result->$responseNode->user_id),
		  'consumables'=>$consumables,
		  'consumablesamount'=>$consumablesamount,
		  'usercouponid'=>$_POST['usercouponid'],
		  'numberno'=>$_POST['serialno'],
		  'lockinfo'=>$_POST['lockinfo'],
		  'lockcode'=>$_POST['lockCode'],
		  'transportguid'=>$_POST['transportguid'],
		  'transportprice'=>$_POST['transportprice'],
		  'useraddress'=>$_POST['useraddress']
			);
			if(!empty($_POST['alidepose'])){
			    $datas['alideposit'] = $_POST['alidepose'];
			}
			//判断用户使用情况
			//			$isrent = $this->functions->getRedisByKey('AliRent'.$aliorder['user_id']);
			//			if(!$isrent){
			//				$timing=array(
			//		 		   'check'=>'hasrent',
			//		 		   'time'=>$redischeck,
			//		 		   'usetime'=>0,
			//		 		   'money'=>0
			//				);
			//				$datas['userfirstorder']='1';
			//				$timing=json_encode($timing);
			//				$this->functions->setRedisKeyVal('AliRent'.$aliorder['user_id'],$timing,86400);
			//				$isrent = $this->functions->getRedisByKey('AliRent'.$aliorder['user_id']);
			//				$this->functions->debuglog($result->$responseNode->user_id.'用户已使用共享轮椅,记录为'.$isrent);
			//			}
			$post_arr=array(
         'QueryType'=>'sub_order',
		 'Params'=>json_encode($datas,true),
		 'UserGuid'=>'ODh8QHJvbWVucw--'
		 );
		 $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
		 $res=$this->functions->curlPostArray($url, $post_arr);
		 $this->functions->debuglog($datas['out_order_no'].'入库结果'.json_encode($res, JSON_UNESCAPED_UNICODE));
		 if($res->state != '1'){
		 	$request = new ZhimaMerchantOrderRentCancelRequest ();
		 	$this->functions->debuglog($datas['out_order_no'].'订单创建失败');
		 	$this->functions->debuglog(json_encode($datas, JSON_UNESCAPED_UNICODE));
		 	$bizcontentarray=array(
				'order_no'=>$datas['order_no'],
				'product_code'=>'w1010100000000002858'
				);
				$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				//echo '0^'.$bizcontent0;die;
				$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				$request->setBizContent($bizcontent);
				//				$signData = $request->getApiParas();
				//				$sign = $this->aop->rsaSign($signData, $this->aop->signType);
				$result = $this->aop->execute($request);
				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				$resultCode = $result->$responseNode->code;
		 }else{
		 	//$add = json_encode($datas,JSON_UNESCAPED_UNICODE);
		 	//$this->load->database();
		 	//$this->db->insert('orders', json_decode($add));
		 	if($_POST['lockCode'] != ''){
		 		//$this->functions->debuglog(json_encode($datas,JSON_UNESCAPED_UNICODE));
		 		$this->qiqi_unlock($datas['out_order_no'],$_POST['lockCode'],$create_time,$aliorder['user_id'],$_POST['out_order_no']);
		 	}else{
		 		echo json_encode($res,JSON_UNESCAPED_UNICODE);
		 		$msg = array(
		       'tempid'=>'ac18f53a63064ab1a9e88962638099a5',
		       'first'=>'租借成功，订单号:'.$datas['out_order_no'],
		       'keyword1'=>$datas['create_time'],
		       'keyword2'=>$datas['create_time'],
		       'keyword3'=>'以实际归还时间为准',
		       'keyword4'=>$datas['goods_name'],
		       'keyword5'=>$datas['out_order_no'],
		       'keyword6'=>'400-686-0176',
		       'remark'=>'感谢您使用雨诺信用借,祝您生活愉快'
		       );
		       $this->send_temp($datas['user_id'], $msg);
		       $this->functions->debuglog($datas['out_order_no'].'非共享订单');
		       //		 	$this->functions->debuglog('1234'.json_encode($res));
		       //		 	if($res['state']  == '1'){
		       //		 		echo json_encode($res,JSON_UNESCAPED_UNICODE);
		       //		 	}else{
		       //		 		$request = new ZhimaMerchantOrderRentCancelRequest ();
		       //		 		$bizcontentarray=array(
		       //				'order_no'=>$result->$responseNode->order_no,
		       //				'product_code'=>'w1010100000000002858'
		       //				);
		       //				$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		       //				//echo '0^'.$bizcontent0;die;
		       //				$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		       //				$request->setBizContent($bizcontent);
		       //				$signData = $request->getApiParas();
		       //				$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		       //				$result = $this->aop->execute($request);
		       //				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		       //				$resultCode = $result->$responseNode->code;
		       //				if(!empty($resultCode)&&$resultCode == 10000){
		       //					$data=array(
		       //			'state'=>'2',
		       //			'msg'=>'系统繁忙请重试'
		       //			);
		       //			echo json_encode($data,JSON_UNESCAPED_UNICODE);
		       //				}else{
		       //					$data=array(
		       //			'state'=>'3',
		       //			'msg'=>'系统繁忙请重试'
		       //			);
		       //			echo json_encode($data,JSON_UNESCAPED_UNICODE);
		       //				}
		       //		 	}
		 	}
		 }

		} else {
			echo "没有查询到订单";
		}
	}
	//小程序创建订单
	public function xcx_create_new(){
		//		if($_POST['lockCode'] == '100000004386'){
		//			$this->xcx_create();die;
		//		}
		$this->functions->debuglog($_POST['phone'].'用户租借,订单号：'.$_POST['out_order_no']);
		$request = new ZhimaMerchantOrderRentQueryRequest ();
		$bizcontentarray=array(
				'out_order_no'=>$_POST['out_order_no'],
				'product_code'=>$_POST['product_code']
		);
		$cons=$this->object_to_array(json_decode($_POST['consumables']));
		$consumables=array();
		$consumablesamount=0;
		if(count($cons)== '1'){
			$consumables['consumablesguid']=$cons[0]['GUID'];
			$consumables['goodsprice']=$cons[0]['PRODUCT_PRICE'];
			$consumables['price']=$cons[0]['PRODUCT_PRICE'];
			$consumables['num']=$cons[0]['num'];
			$consumables['tamount']=(int)$cons[0]['PRODUCT_PRICE']*(int)$cons[0]['num'];
			$consumablesamount=$consumables['tamount'];
		}else{
			for($i=0;$i<count($cons);$i++){
				$consumables[$i]['consumablesguid']=$cons[$i]['GUID'];
				$consumables[$i]['goodsprice']=$cons[$i]['PRODUCT_PRICE'];
				$consumables[$i]['price']=$cons[$i]['PRODUCT_PRICE'];
				$consumables[$i]['num']=$cons[$i]['num'];
				$consumables[$i]['tamount']=(int)$cons[$i]['PRODUCT_PRICE']*(int)$cons[$i]['num'];
				$consumablesamount=$consumablesamount+$consumables[$i]['tamount'];
			}
		}
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$request->setBizContent($bizcontent);
		//$signData = $request->getApiParas();
		//$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			if(!$_POST['create_time']){
				$create_time=date("Y-m-d H:i:s");
			}else{
				$create_time=$_POST['create_time'];
			}
			$time=(int)$_POST['borrow_cycle'];
			$timestart=$create_time;
			if($time == 31){
				$returntime = date("Y-m-d H:i:s",strtotime("$timestart + 1 month"));
			}else{
				$returntime = date("Y-m-d H:i:s",strtotime("$timestart + $time day"));
			}
			if($_POST['serialno'] == '' && $_POST['lockCode'] == ''){
				$status = '0';
			}else if($_POST['serialno'] != ''){
				$status = '1';
			}else{
				$status = '7';
			}
			$redischeck=strtotime($create_time);
			$datas= array (
          'id'=>uniqid(),
		  'create_time'=>$create_time,
		  'return_time'=>$returntime,
          'token'=>'',//$_info['token'],
          'out_order_no'=>$_POST['out_order_no'],
          'order_no'=>$result->$responseNode->order_no,
          'user_id'=>$result->$responseNode->user_id,
          'admit_state'=>$result->$responseNode->admit_state,
          'name'=>'BK',
          'product_code'=>$_POST['product_code'],
          'goods_name'=>$_POST['goods_name'],
          'rent_info'=>$_POST['rent_info'],
          'rent_unit'=>$_POST['rent_unit'],
          'rent_amount'=>$_POST['rent_amount'],
          'deposit_amount'=>$_POST['deposit_amount'],
          'deposit_state'=>$_POST['deposit_state'],
          'borrow_cycle'=>$_POST['borrow_cycle'],
          'borrow_cycle_unit'=>$_POST['borrow_cycle_unit'],
          'borrow_shop_name'=>$_POST['borrow_shop_name'],
		  'leaseprice'=>$_POST['leaseprice'],
          'leasebranchguid'=>$_POST['leasebranchguid'],
          'status'=>$status, 
          'orgguid'=>$_POST['orgguid'],
		  'phone'=>$_POST['phone'],
		  'user_name'=>'',//S('user_name'.$result->$responseNode->user_id),
		  'consumables'=>$consumables,
		  'consumablesamount'=>$consumablesamount,
		  'usercouponid'=>$_POST['usercouponid'],
		  'numberno'=>$_POST['serialno'],
		  'lockinfo'=>$_POST['lockinfo'],
		  'lockcode'=>$_POST['lockCode'],
		  'transportguid'=>$_POST['transportguid'],
		  'transportprice'=>$_POST['transportprice'],
		  'useraddress'=>$_POST['useraddress']
			);
			//判断用户使用情况
			$isrent = $this->functions->getRedisByKey('AliRent'.$result->$responseNode->user_id);
			if(!$isrent){
				$timing=array(
		 		   'check'=>'hasrent',
		 		   'time'=>$redischeck,
		 		   'usetime'=>0,
		 		   'money'=>0
				);
				$datas['userfirstorder']='1';
				$timing=json_encode($timing);
				$this->functions->setRedisKeyVal('AliRent'.$result->$responseNode->user_id,$timing,86400);
				$isrent = $this->functions->getRedisByKey('AliRent'.$result->$responseNode->user_id);
				//$this->functions->debuglog($result->$responseNode->user_id.'用户已使用共享轮椅,记录为'.$isrent);
			}
			$post_arr=array(
         'QueryType'=>'sub_order',
		 'Params'=>json_encode($datas,true),
		 'UserGuid'=>'ODh8QHJvbWVucw--'
		 );
		 $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
		 $res=$this->functions->curlPostArray($url, $post_arr);
		 if($res->state != '1'){
		 	$request = new ZhimaMerchantOrderRentCancelRequest ();
		 	$this->functions->debuglog($datas['out_order_no'].'订单创建失败');
		 	$this->functions->debuglog(json_encode($datas, JSON_UNESCAPED_UNICODE));
		 	$bizcontentarray=array(
				'order_no'=>$datas['order_no'],
				'product_code'=>'w1010100000000002858'
				);
				$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
				//echo '0^'.$bizcontent0;die;
				$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
				$request->setBizContent($bizcontent);
				//				$signData = $request->getApiParas();
				//				$sign = $this->aop->rsaSign($signData, $this->aop->signType);
				$result = $this->aop->execute($request);
				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				$resultCode = $result->$responseNode->code;
		 }
		 if($_POST['lockCode'] != ''){
		 	$this->new_unlock($datas['out_order_no'],$_POST['lockCode'],$create_time,$result->$responseNode->user_id,$_POST['out_order_no']);
		 }else{
		 	echo json_encode($res,JSON_UNESCAPED_UNICODE);
		 	$msg = array(
		       'tempid'=>'ac18f53a63064ab1a9e88962638099a5',
		       'first'=>'租借成功，订单号:'.$datas['out_order_no'],
		       'keyword1'=>$datas['create_time'],
		       'keyword2'=>$datas['create_time'],
		       'keyword3'=>'以实际归还时间为准',
		       'keyword4'=>$datas['goods_name'],
		       'keyword5'=>$datas['out_order_no'],
		       'keyword6'=>'400-686-0176',
		       'remark'=>'感谢您使用雨诺信用借,祝您生活愉快'
		       );
		       $this->send_temp($datas['user_id'], $msg);
		       $this->functions->debuglog($datas['out_order_no'].'非共享订单');
		       //		 	$this->functions->debuglog('1234'.json_encode($res));
		       //		 	if($res['state']  == '1'){
		       //		 		echo json_encode($res,JSON_UNESCAPED_UNICODE);
		       //		 	}else{
		       //		 		$request = new ZhimaMerchantOrderRentCancelRequest ();
		       //		 		$bizcontentarray=array(
		       //				'order_no'=>$result->$responseNode->order_no,
		       //				'product_code'=>'w1010100000000002858'
		       //				);
		       //				$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		       //				//echo '0^'.$bizcontent0;die;
		       //				$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		       //				$request->setBizContent($bizcontent);
		       //				$signData = $request->getApiParas();
		       //				$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		       //				$result = $this->aop->execute($request);
		       //				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		       //				$resultCode = $result->$responseNode->code;
		       //				if(!empty($resultCode)&&$resultCode == 10000){
		       //					$data=array(
		       //			'state'=>'2',
		       //			'msg'=>'系统繁忙请重试'
		       //			);
		       //			echo json_encode($data,JSON_UNESCAPED_UNICODE);
		       //				}else{
		       //					$data=array(
		       //			'state'=>'3',
		       //			'msg'=>'系统繁忙请重试'
		       //			);
		       //			echo json_encode($data,JSON_UNESCAPED_UNICODE);
		       //				}
		       //		 	}
		 }
		 //$this->functions->debuglog(json_encode($datas,JSON_UNESCAPED_UNICODE));
		 //$add = json_encode($datas,JSON_UNESCAPED_UNICODE);
		 //$this->load->database();
		 //$this->db->insert('orders', json_decode($add));
		} else {
			echo "没有查询到订单".$result->$responseNode;
		}
	}
	//撤销订单
	public function alirent_cancel(){
		$post_arr=array(
					'QueryType'=>'get_orderinfo',
					'Params'=>'{"id":"'.$_POST['id'].'","orgguid":"88"}',
					'UserGuid'=>'ODh8QHJvbWVucw--'
					);
					$url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					$res=$this->functions->curlPostArray($url, $post_arr);
 					$alidepose = $res->ALIDEPOSE;
 					$out_order_no = $res->OUT_ORDER_NO;
					if ($res->STATUS == '0' || $res->STATUS == '6'){
						$request = new ZhimaMerchantOrderRentCancelRequest ();
						$bizcontentarray=array(
				            'order_no'=>$_POST['order_no'],
				             'product_code'=>$_POST['product_code']
						);
						$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
						//echo '0^'.$bizcontent0;die;
						$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
						$request->setBizContent($bizcontent);
						//						$signData = $request->getApiParas();
						//						$sign = $this->aop->rsaSign($signData, $this->aop->signType);
						$result = $this->aop->execute($request);
						$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
						$resultCode = $result->$responseNode->code;
						if(!empty($resultCode)&&$resultCode == 10000){
							$this->functions->debuglog($_POST['order_no'].'主动撤销');
							//echo "成功";
							$post_arr=array(
					             'QueryType'=>'update_order',
					             'Params'=>'{"money":"0","isclerk":"1","branchguid":"'.$_POST['branchguid'].'","goodsid":"'.$_POST['goodsid'].'","orgguid":"88","status":"2","order_no":"'.$_POST['order_no'].'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->functions->curlPostArray($url, $post_arr);
					             echo json_encode($res,JSON_UNESCAPED_UNICODE);
					             //退款支付宝部分押金
					             if(!empty($alidepose)){
					                 // 					    $alirefund = new AliRent();
					                 // 					    $refund_res=$alirefund->ali_refund($res->OUT_ORDER_NO,$alidepose);
					                 // 					    $this->functions->debuglog($res->OUT_ORDER_NO.$alidepose);
					                 $refund_data = array(
					                     'out_order_no'=>$out_order_no,
					                     'money'=>$alidepose
					                 );
					                 $refund_url = "http://xyj.yiyao365.cn/AliRentRomens/index.php/AliRent/AliRent/ali_refund";
					                 $refund_res = $this->functions->curlPostArray($refund_url, $refund_data);
					             }
						}else{
							$data=array('state'=>'2');
							echo json_encode($data);
						}
					}else{
						$data=array('state'=>'2');
						echo json_encode($data);
					}

	}

	//借用实体地图上传
	public function alirent_map(){
		$request = new ZhimaMerchantBorrowEntityUploadRequest ();
		$bizcontentarray=array(
		'product_code'=>'w1010100000000002858',
		'category_code'=>'test',
		'entity_code'=>'2016000100010011',
		'longitude'=>'120.41391',
		'latitude'=>'36.077506',
		'entity_name'=>'医用制氧机',
		'address_desc'=>'宁夏路市南软件园3号楼517',
		'office_hours_desc'=>'09:00—22:00',
		'contact_number'=>'0532-85886309',
		'collect_rent'=>'Y',
		'can_borrow'=>'Y',
		'can_borrow_cnt'=>'10',
		'total_borrow_cnt'=>'1',
		'upload_time'=>date("Y-m-d h:i:s")
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		//echo '0^'.$bizcontent0;die;
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$request->setBizContent($bizcontent);
		//		$signData = $request->getApiParas();
		//		$sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			echo "成功";
		} else {
			echo "失败";
		}
	}
	//查询可租借和归还的位置
	public function rentaddress($order){
	    $post_arr=array(
	        'QueryType'=>'query_main',
	        'Params'=>'{"branchguid":"'.$order.'"}',
	        'UserGuid'=>'ODh8QHJvbWVucw--'
	    );
	    $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	    $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
	    if(!$res['status']){
	        $address = str_replace("-",",",$res['msg']);
	        return  $address;
	    }else{
	        return  'NO';
	    }
	}
	//查询订单方法
	public function zhima_sel($out_order_no){
		$request = new ZhimaMerchantOrderRentQueryRequest ();
		$bizcontentarray=array(
		    'out_order_no'=>$out_order_no,
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
	        $res=$this->object_to_array($result->$responseNode);
	        return $res;
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
		$res=json_encode($response);
		if(curl_errno($ch)){//出错则显示错误信息
			echo"错误：";
			print curl_error($ch);
		}
		curl_close($ch); //关闭curl链接
		return $res;
	}
	function object_to_array($obj){
		$_arr = is_object($obj)? get_object_vars($obj) :$obj;
		foreach ($_arr as $key => $val){
			$val=(is_array($val)) || is_object($val) ? $this->object_to_array($val) :$val;
			$arr[$key] = $val;
		}
		return $arr;
	}
}
