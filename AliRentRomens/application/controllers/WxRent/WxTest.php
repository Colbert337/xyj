<?php
//header("Content-type: text/html; charset=utf-8");
//ini_set('display_errors', '0');
//error_reporting(E_ALL ^ E_NOTICE);
require_once '/var/www/AliRentRomens/application/libraries/RSA.php';
require_once dirname(__FILE__).'/WxApi.php';
//if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
class WxTest extends CI_Controller{
	public $appid = 'wxa5d85fdc620e20bb';
	public $mch_id = '1415605302';
	public $key = 'fc68fe4ec8e4e338e391177a4b723b63';
	public $SSLCERT_PATH = '/var/www/AliRentRomens/application/wxpem/apiclient_cert.pem';
	public $SSLKEY_PATH = '/var/www/AliRentRomens/application/wxpem/apiclient_key.pem';
	public function __construct()
	{
	    parent::__construct();
	    //		$this->mch_id = '1415605302';
	    //		$this->key = 'fc68fe4ec8e4e338e391177a4b723b63';
	    $this->load->library('functions');
	}
	public function test(){
	    $wxtemp = new WxApi();
	    $access_token = $this->functions->getRedisByKey('access_token');
	    if(!$access_token){
	        $access_token = $wxtemp->get_access_token();
	        $this->functions->setRedisKeyVal('access_token',$access_token,7200);
	    }
	    $post_arr=array(
	        'QueryType'=>'get_orderinfo',
	        'Params'=>'{"orderno":"201812211052182667"}',
	        'UserGuid'=>'ODh8QHJvbWVucw--'
	    );
	    $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
 	    $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
	    $res = $res[0];
	    $prepayid = $this->functions->getRedisByKey('prepayid201812211052182667');
	    $this->functions->debuglog('prepay'.$prepayid);
	    if($prepayid){
	        $wxtemp = new WxApi();
	        $access_token = $this->functions->getRedisByKey('access_token');
	        if(!$access_token){
	            $access_token = $wxtemp->get_access_token();
	            $this->functions->setRedisKeyVal('access_token',$access_token,7200);
	        }
	        $this->functions->debuglog('acc:'.$access_token.'openid:'.$res['USER_ID'].'pieid:'. $prepayid.'ordrer:'.$result['out_trade_no'].'name:'.$res['GOODS_NAME'].'dmoney:'.$res['DEPOSIT_AMOUNT'].'bud:'.$res['LEASEBRANCHGUID']);
	        $tem=$wxtemp->rent_tempsend($access_token,$res['USER_ID'], $prepayid,'201812211052182667',$res['GOODS_NAME'],$res['DEPOSIT_AMOUNT'],$res['LEASEBRANCHGUID']);
	        //$this->functions->debuglog('租借模板'.json_encode($tem));
	    }
	    var_dump($tem);
	}
	//企业转账
	public function Wx_transf($out_order_no){
		$url = 'https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank';
		//$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
//		$parameters = array(
//		  'mch_appid'=>$this->appid,
//		  'mchid' => $this->mch_id, //商户号  
//          'nonce_str' => uniqid(), //随机字符串  
//		  'partner_trade_no'=>$out_order_no,
//		  'openid'=>'oWgn7wDLiEX3eHyr-0DtyTLb9xQk',
//		  'check_name'=>'NO_CHECK',
//		  'amount'=>'1',
//		  'desc'=>'轮椅收入转账',
//		  'spbill_create_ip'=>$_SERVER['SERVER_ADDR']
//		);
        $rsa = new RSA('/var/www/AliRentRomens/application/wxpem/newpubkey.pem', '');
        //var_dump($rsa->encrypt('6217922200880913'));die;
        $parameters = array(
		  'mch_id' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		  'partner_trade_no'=>$out_order_no,
		  'enc_bank_no'=>$rsa->encrypt('6217922200880913'),
		  'enc_true_name'=>$rsa->encrypt('刘宜昊'),
		  'amount'=>'1',
		  //'desc'=>'轮椅收入转账',
		  'bank_code'=>'1004'
		);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlSSLCurl($xmlData, $url));
		var_dump($return);
	}
	function object_to_array($obj){
	    $_arr = is_object($obj)? get_object_vars($obj) :$obj;
	    foreach ($_arr as $key => $val){
	        $val=(is_array($val)) || is_object($val) ? $this->object_to_array($val) :$val;
	        $arr[$key] = $val;
	    }
	    return $arr;
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
}