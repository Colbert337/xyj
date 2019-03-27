<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
require_once dirname(__FILE__).'/../../vaop/aop/AopClient.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayTradeAppPayRequest.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayTradeQueryRequest.php';
require_once dirname(__FILE__).'/../WxRent/WxApi.php';
class AliTest extends CI_Controller {
	public $aliappid;
	public $rsaPrivateKeyFilePath;
	public $alipayrsaPublicKey;
	public $token;
	public $aop;
	public $wxuser;
	public function __construct()//1111
	{
		parent::__construct();
		$this->aliappid = '2017051807276496';
		//merchant_rsa_private_key.pem路径111
		//$this->rsaPrivateKeyFilePath ='';
		$this->alipayrsaPublicKey ='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0HPBPqCEGAMxrGpP/YeethRP8XyEBdwVrLgnc4U12mdSY0UGpVqbwBYJdx56Sj0U2uzinREp6IutDpy+Oi8nbAobj8W55+GiK8OT3zAII0C4uDO1O0ddUY0lGhH4KAoDogupYmFVUOA8s3mEj6+ZphGIBOyDBXeSREZf0efL+rDjnv26EIdyFRn7Sg49AIkgW711n8xr0YyW0MF9tsAOjk/zeHPJdsA1IG+TBfW/qJExmAzp1qpgKM3WssWws2ZGB1UsVtfEPQG7rkon8PGrxwm8tf0qcfktIy3Bwk5YLG2OosDG8TwkQYPZRIrlUnLxlh7uwHxbiXr43pKxBwsjVwIDAQAB';
		$this->rsaPrivateKey='MIIEpAIBAAKCAQEA0HPBPqCEGAMxrGpP/YeethRP8XyEBdwVrLgnc4U12mdSY0UGpVqbwBYJdx56Sj0U2uzinREp6IutDpy+Oi8nbAobj8W55+GiK8OT3zAII0C4uDO1O0ddUY0lGhH4KAoDogupYmFVUOA8s3mEj6+ZphGIBOyDBXeSREZf0efL+rDjnv26EIdyFRn7Sg49AIkgW711n8xr0YyW0MF9tsAOjk/zeHPJdsA1IG+TBfW/qJExmAzp1qpgKM3WssWws2ZGB1UsVtfEPQG7rkon8PGrxwm8tf0qcfktIy3Bwk5YLG2OosDG8TwkQYPZRIrlUnLxlh7uwHxbiXr43pKxBwsjVwIDAQABAoIBAQCA+s7cmGeDkB5hR5rdDdh3Y1Qf4OKz2X0T1RKcGRW8YOgKgoBdOhZbIYeTzCjw3KCV4bNKan9a42oeO4A88kZbRFnPeRHR17wHhklt9QNkBL0HRP9jgYHNXx9Q5UN+Ssv6rWqOdBldJJKKnqsWWRoiNoDKQynC7Tx0wHKzp9B/+WrWtD9MbKlkttc/KMyEpRRj2+T9cO0mhwYNbroo+ezKlYhfED/3idgNCrfOhMxhllHmb6jm0BRIUtEuuTpc4O6cSlGFuLNN2ZOye+jcP2ibQLmo7MO4rrX1QUSGfZTg+0OYH3zvgAUjRgeYqiqdqmzHn2PxMsoZxqVtl1ZiqHTRAoGBAPrQmykJ6jF+4TJ9Ic7V8uO3yXPPFn1Z1O5bSZkr6a8pqTNSSvIBtVBKHrrC9fJnxz4q/C7A19G8LCJ6nwvKCBsq79uAWK+6bwQ93Y3dnOzKr7wjc6X+XpEVU+PgzEZIxNkh2MDjuPmxEOxfauWA3UED16Qp80lnoj/oLLDMSfn/AoGBANTC85orT+zPd4rjdffdELdFhvm+vonsX/w3pWmNuM9lWACE88LsFrVLXgcAgAui1qsn6kUHSJ9HEYYMdk7QTz2FVuREGHydaRw1HZ+i5OYcGqOKFNK3wbvXKOap8kToX7ujKquGHe7D6waCSiF33rDeKP3UlOfoNC2en5noZuapAoGBAKMJgSK/IC4GZQq1zokuCBJAgMI4Bk17XG+IhaH8qo3DTgpfXvpLY/oKBEmwu8FT9m8R8BXQIzph0GqlPMekD3rhgUM0/fFVBh9Cu8chHIXMB0oL3Xw0inJS49JIaWDyoormdoiEPtSIZhDQwaLoDmrZvY4n+s5ngE98c7iFQz0vAoGAcISlVdwgCanym4YNpkbIB1SCvGNu2vwiCv3WwcrMeQosjyHA1E4M+FXiZSuTjBPTGXMjhtwCQRHRp6XBj47EyVFSEagdlxGcO+mvP/Riv3sPb3uf5Yx+rXttSweHc3+82TvCXjGwdMwx6CBRWf/NypXC8fJRyY9YwOOJnlh0yvkCgYBuFIP0RlBa7066HL6MYUhLonp6EMGAKNFpGPZzJOMjm/jf0GaSmSiF3NdvTBmR0dQuNjV1GIBXOD2PybCGRD96UexdMt3S16jqrlYGHJSeoIg8h4X/QAUH+LnzO0HvaQTHwt/V+X6SBCtp3cFhTkQABxjSK4kXjla/psfUtkAdsQ==';
		$this->aop = new AopClient ();
		$this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$this->aop->appId=$this->aliappid;//小程序APPID
		$this->aop->alipayrsaPublicKey=$this->alipayrsaPublicKey;
		//$this->aop->rsaPrivateKeyFilePath=APP_PATH.'Lib/ORG/aop/key/merchant_rsa_private_key.pem';


		$rsaPublicKeyFilePath='/var/www/AliRentRomens/application/aop/key/xcxkey/rsa_public_key.pem';
		//$this->aop->alipayPublicKey=$rsaPublicKeyFilePath;
		$this->aop->rsaPublicKeyFilePath='/var/www/AliRentRomens/application/aop/key/xcxkey/rsa_public_key.pem';
		$this->aop->rsaPrivateKey=$this->rsaPrivateKey;
		$this->aop->apiVersion = '1.0';
		$this->aop->signType = 'RSA2';
		$this->aop->postCharset = 'UTF8';
		$this->aop->format = 'json';
		$this->load->library('functions');
	}
	public function test(){
	    echo "<pre>";
	    $this->load->database();
	    $this->load->dbforge();
// 	           $data = array(
// 	              'id'=>uniqid(),
// 	             'userid'=>'2088202423735071',
// 	             'couponname'=>'满100元减30元',
// 	             'lowpice'=>'100',
// 	             'pice'=>'30',
// 	             'status'=>'1'
// 	           );
// 	    $this->db->insert('alicoupon',$data);
// 	    $add = $this->db->query("alter table alicoupon add orderno varchar(50);");
// 	    $res = $add->result_array();
	    $query = $this->db->query("SELECT * FROM alicoupon;");
	    $result = $query->result_array();
// 	    if(empty($result)){
// 	        	    $data = array(
// 	        	          'id'=>uniqid(),
// 	        	          'userid'=>'2088902145150773',
// 	        	          'couponname'=>'无门槛减10元优惠券',
// 	        	          'lowpice'=>'0',
// 	        	          'pice'=>'10',
// 	        	          'status'=>'1'
// 	        	    );
// 	        	    $this->db->insert('alicoupon',$data);
// 	        	    $data = array(
// 	        	        'id'=>uniqid(),
// 	        	        'userid'=>'2088902145150773',
// 	        	        'couponname'=>'满100元减30元优惠券',
// 	        	        'lowpice'=>'100',
// 	        	        'pice'=>'30',
// 	        	        'status'=>'1'
// 	        	    );
// 	        	    $this->db->insert('alicoupon',$data);
// 	    }
	    var_dump($query->result_array());

	}
	public function redis(){
	    echo "<pre>";
	    $data = array(
	        array(
	            'rent'=>'错'
	        ),
	        array(
	            'rent'=>'错'
	        ),
	        array(
	            'rent'=>'错'
	        ),
	        array(
	            'rent'=>'错'
	        ),
	        array(
	            'rent'=>'错'
	        ),
	    );
	    $this->functions->setRedisKeyVal('checkcheck',json_encode($data,JSON_UNESCAPED_UNICODE),86400);
	    $orderinfo = $this->object_to_array(json_decode($this->functions->getRedisByKey('checkcheck')));
	    var_dump($orderinfo);
	}
	public function checkcheck($i){
	    echo "<pre>";
	    $i = (Int)($i);
	    $data = array(
	        array(
	            'rent'=>'错'
	        ),
	        array(
	            'rent'=>'错'
	        ),
	        array(
	            'rent'=>'错'
	        ),
	        array(
	            'rent'=>'错'
	        ),
	        array(
	            'rent'=>'错'
	        ),
	    );
	    $orderinfo = $this->object_to_array(json_decode($this->functions->getRedisByKey('checkcheck')));
	    if($i==count($orderinfo)){
	        var_dump('弄完了');
	        var_dump($this->object_to_array(json_decode($this->functions->getRedisByKey('checkcheck'))));
	    }else{
	        for($j=$i;$j<count($orderinfo);$j++){
	            if($data[$i]['rent']=='错'){
	                $orderinfo[$i]['rent']='对';
	                $this->functions->setRedisKeyVal('checkcheck',json_encode($orderinfo,JSON_UNESCAPED_UNICODE),86400);
	                var_dump('修改了第'.$i.'个');
	                $i++;        
	                sleep(1);
	                echo "<script>window.location.href = 'http://xyj.yiyao365.cn/AliRentRomens/index.php/AliRent/AliTest/checkcheck/$i';</script>";
	                break;
	                //header("location:http://xyj.yiyao365.cn/AliRentRomens/index.php/AliRent/AliTest/checkcheck/$i");
	                
	            }
	        }
	    }    
	}
	public function postcheck(){
	    $data = array(
	        'order_no'=>'20193121332151899'
	    );
	    $url = 'http://xyj.yiyao365.cn/AliRentRomens/index.php/WxRent/WxPay/sel_refund_check';
	    $res=$this->curlpost($url, $data);
	    var_dump(json_decode($res));
	}
	public function checkr(){
	    echo "<pre>";
	    $orderinfo = $this->object_to_array(json_decode($this->functions->getRedisByKey('checkcheck')));
	    var_dump($orderinfo);
	}
	public function ale(){
		echo "<pre>";
		$url="http://java.xingoxing.com/api/devices";
		$data=array(
		  'devices'=>'100000004386',
		);
		//$data['channel']='5ADC9AD1224C38886394C3FB45BD77FC';
		$data['channel']='MCH10221';
		foreach ($data as $key=>$value){
			$arr[$key] = $key;
		}
		sort($arr); //字典排序的作用就是防止因为参数顺序不一致而导致下面拼接加密不同
		// 2. 将Key和Value拼接
		$str = "5ADC9AD1224C38886394C3FB45BD77FC";
		//$str = "6VFYqDsOKttZSP7pXXJAMDXIrFIfeE3USLACrtq87wkfbKsSJVd1pPRLSWM8GUR2";
		foreach ($arr as $k => $v) {
			$str = $str.$arr[$k].$data[$v];
		}
		$data['sign']=strtoupper(md5($str));
		var_dump($data['sign']);
		$res = $this->object_to_array(json_decode(json_decode($this->curlpost($url, $data))));
		var_dump($res);
	}
	public function test_qiqi(){
		$this->functions->debuglog('本次关锁数据'.json_encode($_POST));
		$msg=array(
			  'code'=>'000000',
			  'msg'=>'ok'
			  );
			  echo json_encode($msg);
	}
	public function dangdang($order){
		$b = '1';
		echo "<pre>";
		$userid = '1111111';
		$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$order.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             if(!$res['ID']){
					             	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$order.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             }
					             $order_no = $res['ORDER_NO'];
					             $out_order_no = $res['OUT_ORDER_NO'];
					             $dmoney = $res['DEPOSIT_AMOUNT'];
					             $branchid = $res['LEASEBRANCHGUID'];
					             //11111
					             $isrent = $this->functions->getRedisByKey('AliRent'.$userid);
					             //$res['CREATETIME'] = '2018-08-23 10:00:00';
					             $redischeck=strtotime($res['CREATETIME']);
					             if(!$isrent){
					             	$timing=array(
		 		                       'check'=>'hasrent',
		 		                       'time'=>$redischeck,
		 		                       'usetime'=>0,
		 		                       'money'=>0
					             	);
					             	$timing=json_encode($timing);
					             	var_dump('用户第一次使用');
					             	$this->functions->setRedisKeyVal('AliRent'.$userid,$timing,86400);
					             }
					             //11111
					             $lockinfo=$this->object_to_array(json_decode($res['LOCKINFO']));
					             //查看是否组用过
					             $rentinfo = $this->object_to_array(json_decode($this->functions->getRedisByKey('AliRent'.$userid)));
					             var_dump($rentinfo);
					             if($rentinfo){
					             	$hastime = ($rentinfo['usetime']);
					             }else{
					             	$hastime = 0;
					             }
					             var_dump('用户已经使用：'.$hastime/60);
					             //$this->functions->debuglog('该用户已经使用：'.$hastime);
					             $rtime=$res['RETURNGOODSTIME'];
					             //$rtime = '2018-08-27 13:27:44';
					             $renttime =(strtotime($rtime)-strtotime($res['CREATETIME']))+$hastime;
					             $rentdate=$renttime/3600;
					             $rentday=ceil($renttime/86400);
					             $rent = 0;
					             $rentcoupon = 0;
					             $hassale = 'no';
					             var_dump('用户本次使用:'.(strtotime($rtime)-strtotime($res['CREATETIME']))/60);
					             var_dump($lockinfo);
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
					             $result['ONEDAYPRICE'] = '299';
					             if($renttime/86400>1){
					             	var_dump('用户使用超过一天');
					             	$a = fmod($rentdate,24);
					             	var_dump($rentdate);
					             	var_dump($a);
					             	//处理单天不封顶
					             	if($result['ONEDAYPRICE'] == $dmoney){
					             		var_dump('单天不封顶商品');
                                        $rent = (ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2))*$result['HOURPRICE']*0.5;
					             	    var_dump((ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)));
					             	}else{
					             		if($a==0){
					             			$rent = ((float)$rentday*$result['ONEDAYPRICE']);
					             		}else{
					             			if(substr($branchid,0,4) == 'QYFY' && ceil($a*2)%2 != 0){
					             				var_dump('111');
					             				$rent =(ceil($a*2)*(float)$result['HOURPRICE']*0.5-0.5)+(((float)$rentday-1)*$result['ONEDAYPRICE']);
					             			}else{
					             				$rent =ceil($a*2)*(float)$result['HOURPRICE']*0.5+(((float)$rentday-1)*$result['ONEDAYPRICE']);
					             			}
					             		}
					             	}
					             	 
					             }else{
					             	if(substr($branchid,0,4) == 'QYFY' && ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)%2 != 0){
					             		var_dump('111');
					             		$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$result['HOURPRICE']*0.5-0.5;
					             	}else{
					             		$rent =  ceil(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2)*(float)$result['HOURPRICE']*0.5;
					             	}

					             }
					             var_dump(($rentdate - (float)$lockinfo[$i-1]['SPEC2VALUE'])*2);
					             //var_dump((float)$result['HOURPRICE']);
					             var_dump('用户该使用的优惠金额：'.$rentcoupon);
					             var_dump('用户该使用的优惠外政策：'.$rent);
					             $rent = $rent + (float)$rentcoupon;
					             if($rent>(float)$result['ONEDAYPRICE']*(float)$rentday){
					             	$rent = (float)$result['ONEDAYPRICE']*(float)$rentday;
					             }
					             //协和医院48小时以上无封顶处理
					             if(substr($branchid,0,4) == 'BJXH' && $rentdate>48){
					             	var_dump('超出两小时：'.($rentdate-48));
					             	$rent =ceil(($rentdate-48)*2)*(float)$result['HOURPRICE']*0.5+(2*$result['ONEDAYPRICE']);
					             	var_dump('协和48以上订单租金：'.$rent);
					             }
					             }
					             if($rentinfo){
					             	$rentinfo['usetime'] = $renttime;
					             	$sholdpay = $rent  ;
					             	var_dump('用户已付'.$rentinfo['money']);
					             	$rent = $sholdpay - $rentinfo['money'];
					             	$rentinfo['money'] = $sholdpay;
					             	var_dump('用户应付:'.$rent);
					             	//$this->functions->debuglog('此次应付:'.$rent);
					             	$newtime = strtotime($rtime)-$rentinfo['time'];
					             	if($newtime<86400){
					             		$newtime = 86400 - $newtime;
					             	}
					             	$this->functions->setRedisKeyVal('AliRent'.$userid,json_encode($rentinfo),$newtime);
					             }
					             $this->functions->delRedisByKey('AliRent'.$userid);
					             $rent = (String)$rent;
					             var_dump($rent);
	}
	public function sel_order($order){
		echo"<pre>";
		$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"id":"'.$order.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             if(!$res['ID']){
					             	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"out_order_no":"'.$order.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             $res = $res[0];
					             }
					             if(!$res['ID']){
					             	$post_arr=array(
					             'QueryType'=>'get_orderinfo',
					             'Params'=>'{"orderno":"'.$order.'"}',
					             'UserGuid'=>'ODh8QHJvbWVucw--'
					             );
					             $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
					             $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
					             }
					             var_dump($res);
					             $temp = array(
		                            'tempid'=>'ac18f53a63064ab1a9e88962638099a5',
		                            'first'=>'租借成功，订单号:'.$res['OUT_ORDER_NO'],
		                            'keyword1'=>$res['CREATETIME'],
		                            'keyword2'=>$taketime,
		                            'keyword3'=>'以实际归还时间为准',
		                            'keyword4'=>$res['GOODS_NAME'],
		                            'keyword5'=>$res['OUT_ORDER_NO'],
		                            'keyword6'=>'400-686-0176',
		                            'remark'=>'感谢您使用雨诺信用借,祝您生活愉快'
		                            );
		                            var_dump($temp);
		                            if($res == null){
		                            	echo "no order";
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
	//支付宝订单查询
	public function sel(){
		$request = new AlipayTradeQueryRequest();
		$bizcontentarray=array(
				'out_trade_no'=>'2018627835100187',
		);
		$bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$request->setBizContent($bizcontent);
		//		        $signData = $request->getApiParas();
		//		        $sign = $this->aop->rsaSign($signData, $this->aop->signType);
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		//debuglog('发送模板结果:'.$resultCode.'msg:'.$result->$responseNode->msg);
		if(!empty($resultCode)&&$resultCode == 10000){
			echo "成功";
		} else {
			//echo "失败".iconv("GBK", "UTF-8",$result->$responseNode->sub_msg);
			var_dump($result->$responseNode);
		}
	}
	//支付宝唤起APP支付
	public function call_alipay(){
		$request = new AlipayTradeAppPayRequest();
		$subject = $_POST['goods_name'];
		$out_trade_no = $_POST['out_order_no'];
		$total_amount = $_POST['deposit_amount'];
		//		$bizcontentarray=array(
		//				'body'=>'小程序唤起支付接口测试',
		//				'subject'=>'支付测试标题',
		//				'out_trade_no'=>date("YmdHis").rand(1000,9999),
		//				'timeout_express'=>'30m',
		//		        'total_amount'=>'0.01',
		//		        'product_code'=>'QUICK_MSECURITY_PAY',
		//		        );
		//		$bizcontent0 = json_encode($bizcontentarray,JSON_UNESCAPED_UNICODE);
		//		$bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
		$bizcontent = "{\"body\":\"商品押金\","
		. "\"subject\": \"$subject\","
		. "\"out_trade_no\": \"$out_trade_no\","
		. "\"timeout_express\": \"30m\","
		. "\"total_amount\": \"$total_amount\","
		. "\"product_code\":\"QUICK_MSECURITY_PAY\""
		. "}";
		$request->setNotifyUrl('xyj.yiyao365.cn/AliRentRomens/index.php/AliRent/AliRent/call_alipayblack');
		$request->setBizContent($bizcontent);
		$result = $this->aop->sdkExecute($request);
		echo json_encode($result);
	}
}