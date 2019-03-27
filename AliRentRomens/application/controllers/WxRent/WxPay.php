<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
require_once dirname(__FILE__).'/../../libraries/log.php';
require_once dirname(__FILE__).'/../../libraries/RSA.php';
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
class WxPay{
	public $appid = 'wxa5d85fdc620e20bb';
	public $mch_id = '1415605302';
	public $key = 'fc68fe4ec8e4e338e391177a4b723b63';
	public $SSLCERT_PATH = '/var/www/AliRentRomens/application/wxpem/apiclient_cert.pem';
	public $SSLKEY_PATH = '/var/www/AliRentRomens/application/wxpem/apiclient_key.pem';
	//微信唤起APP支付
	public function call_wxpay(){
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$total_fee = (float)$_POST['total_fee']*100;
		$parameters = array(
            'appid' => $this->appid, //小程序ID  
            'mch_id' => $this->mch_id, //商户号  
            'nonce_str' => uniqid(), //随机字符串  
	        'body' => $_POST['body'],
	        'out_trade_no'=>$_POST['out_order_no'],
	        'total_fee'=>(int)$total_fee,
	        'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],
	        'notify_url'=>'http://xyj.yiyao365.cn/AliRentRomens/index.php/WxRent/WxXcx/pay_back',
	        'trade_type'=>'JSAPI',
	        'openid'=>$_POST['openid']
		);
	 $parameters['sign'] = $this->getSign($parameters);
	 $xmlData = $this->arrayToXml($parameters);
	 $return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
	 $data = array(
	    'appId'=>$this->appid,
	    'timeStamp'=>(string)time(),
	    'nonceStr'=>$parameters['nonce_str'],
	    'package'=>'prepay_id='.$return['prepay_id'],
	    'signType'=>'MD5',
	 );
	 $this->setRedisKeyVal('prepayid'.$_POST['out_order_no'],$return['prepay_id'],604800);
	// $this->debuglog($return['prepay_id']);
	 $data['paySign'] = $this->getSign($data);
	 $return['wxdata'] = $data;
	 echo json_encode($return, JSON_UNESCAPED_UNICODE);
	}
	//微信退款
	public function pay_refund($out_order_no,$total_fee,$refund_fee){
		$url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
		$parameters = array(
		  'appid'=>$this->appid,
		  'mch_id' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		  'out_trade_no'=>$out_order_no,
		  'out_refund_no'=>'r'.$out_order_no,
		  'total_fee'=>(int)((float)$total_fee*100),
		  'refund_fee'=>(int)((float)$refund_fee*100)
		);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlSSLCurl($xmlData, $url));
		$this->debuglog('退款结果'.json_encode($return,JSON_UNESCAPED_UNICODE));
		return $return;
	}
	//微信手动退款
	public function bypay_refund($out_order_no,$total_fee,$refund_fee){
		echo "<pre>";
		$url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
		$parameters = array(
		  'appid'=>$this->appid,
		  'mch_id' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		  'out_trade_no'=>$out_order_no,
		  'out_refund_no'=>'r'.$out_order_no,
		  'total_fee'=>(int)((float)$total_fee*100),
		  'refund_fee'=>(int)((float)$refund_fee*100)
		);
		var_dump($parameters);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlSSLCurl($xmlData, $url));
		var_dump($return);
	}
	//退款查询
	public function sel_refund($order_no){
	    echo "<pre>";
		$url = 'https://api.mch.weixin.qq.com/pay/refundquery';
		$parameters = array(
		  'appid'=>$this->appid,
		  'mch_id' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		    'out_refund_no'=>'r'.$order_no,
		);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
		$rent = ($return[cash_fee]-$return['refund_fee'])/100;
		if($rent < 0){
		    $rent = 0;
		}else{
		    $rent = ceil($rent);
		}
		if($return['refund_success_time_0']){
		    $data['renturngoodstime']=$return['refund_success_time_0'];
		}
		var_dump($rent);
		var_dump($return);	
	}
	//退款查询
	public function sel_refund_check(){
	    $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
	    $parameters = array(
	        'appid'=>$this->appid,
	        'mch_id' => $this->mch_id, //商户号
	        'nonce_str' => uniqid(), //随机字符串
	        'out_refund_no'=>'r'.$_POST['order_no'],
	    );
	    $parameters['sign'] = $this->getSign($parameters);
	    $xmlData = $this->arrayToXml($parameters);
	    $return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
	    $rent = ($return[cash_fee]-$return['refund_fee'])/100;
	    if($rent < 0){
	        $rent = 0;
	    }else{
	        $rent = ceil($rent);
	    }
	    $data = array(
	        'order_no'=>$_POST['order_no'],
	        'rent'=>(String)$rent,
	    );
	    if($return['refund_success_time_0']){
	        $data['returngoodstime']=$return['refund_success_time_0'];
	    }
	    echo json_encode($data);
	}
	//退款查询接口
    public function sel_refund_api($out_order_no){
		$url = 'https://api.mch.weixin.qq.com/pay/refundquery';
		$parameters = array(
		  'appid'=>$this->appid,
		  'mch_id' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		  'out_refund_no'=>'r'.$out_order_no,
		);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
		return $return;
	}
    //微信退款API
	public function pay_refund_api($out_order_no,$total_fee,$refund_fee){
		$url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
		$parameters = array(
		  'appid'=>$this->appid,
		  'mch_id' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		  'out_trade_no'=>$out_order_no,
		  'out_refund_no'=>'r'.$out_order_no,
		  'total_fee'=>(int)((float)$total_fee*100),
		  'refund_fee'=>(int)((float)$refund_fee*100)
		);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlSSLCurl($xmlData, $url));
		return $return;
	}
	//企业转账
	public function Wx_transf($out_order_no){
		//$url = 'https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank';
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		$parameters = array(
		  'mch_appid'=>'wxc6283d4008bb8ea0',
		  'mchid' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		  'partner_trade_no'=>$out_order_no,
		  'openid'=>'oWgn7wDLiEX3eHyr-0DtyTLb9xQk',
		  'check_name'=>'NO_CHECK',
		  'amount'=>'1',
		  'desc'=>'轮椅收入转账',
		  'spbill_create_ip'=>$_SERVER['SERVER_ADDR']
		);
//        $rsa = new RSA(file_get_contents('/var/www/AliRentRomens/application/wxpem/newpubkey.pem'), '');
//        var_dump($rsa);die;
//        $parameters = array(
//		  'mch_id' => $this->mch_id, //商户号  
//          'nonce_str' => uniqid(), //随机字符串  
//		  'partner_trade_no'=>$out_order_no,
//		  'enc_bank_no'=>$this->publicEncrypt('6217922200880913',$pubkey),
//		  'enc_true_name'=>$this->publicEncrypt('刘宜昊',$pubkey),
//		  'amount'=>'1',
//		  'desc'=>'轮椅收入转账',
//		  'bank_code'=>'1004'
//		);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlSSLCurl($xmlData, $url));
		var_dump($return);
	}
	//获取微信商户号公钥
	public function get_key(){
		$url = 'https://fraud.mch.weixin.qq.com/risk/getpublickey';
		$parameters = array(
		  'mch_id' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		  'sign_type'=>'MD5',
		);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlSSLCurl($xmlData, $url));
		echo"<pre>";
		print_r($return);
	
	}
	private static function postXmlCurl($xml, $url, $second = 30)
	{
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);


		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);
		set_time_limit(0);


		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			//throw new WxPayException("curl出错，错误码:$error");
		}
	}
	//需要使用证书的请求
	function postXmlSSLCurl($xml,$url,$second=30)
	{
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		//设置证书
		//使用证书：cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT, $this->SSLCERT_PATH);
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY, $this->SSLKEY_PATH);
		//post提交方式
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		}
		else {
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>";
			curl_close($ch);
			return false;
		}
	}

	//数组转换成xml
	private function arrayToXml($arr) {
		$xml = "<root>";
		foreach ($arr as $key => $val) {
			if (is_array($val)) {
				$xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
			} else {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			}
		}
		$xml .= "</root>";
		return $xml;
	}


	//xml转换成数组
	private function xmlToArray($xml) {


		//禁止引用外部xml实体


		libxml_disable_entity_loader(true);


		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);


		$val = json_decode(json_encode($xmlstring), true);


		return $val;
	}
	//作用：生成签名
	private function getSign($Obj) {
		foreach ($Obj as $k => $v) {
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//签名步骤二：在string后加入KEY
		$String = $String . "&key=" . $this->key;
		//签名步骤三：MD5加密
		$String = md5($String);
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		return $result_;
	}
	///作用：格式化参数，签名过程需要使用
	private function formatBizQueryParaMap($paraMap, $urlencode) {
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		}
		return $reqPar;
	}
    public function debuglog($log){
		$date = date("Y-m-d");
		$dubug = $log;
		$logHandler= new CLogFileHandler(dirname(__FILE__).'/../../logs/'.$date.'.log');
		$log = Log::Init($logHandler, 15);
		Log::DEBUG($dubug);
	}
    //根据key 设置redis的值value
	function setRedisKeyVal($key,$val,$timeout=0){
		$redis = new Redis();
		$redis->connect( '127.0.0.1', 6379 );
		//$redis->auth($this->C('REDIS_PWD'));
		$redis->auth('romens');
		$redis->select(2);
		if($timeout==0){
			$redis->set($key,$val);
		}else{
			$redis->set($key,$val, $timeout);
		}
	}
}