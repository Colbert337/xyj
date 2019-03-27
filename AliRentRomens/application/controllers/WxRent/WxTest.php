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
	        //$this->functions->debuglog('���ģ��'.json_encode($tem));
	    }
	    var_dump($tem);
	}
	//��ҵת��
	public function Wx_transf($out_order_no){
		$url = 'https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank';
		//$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
//		$parameters = array(
//		  'mch_appid'=>$this->appid,
//		  'mchid' => $this->mch_id, //�̻���  
//          'nonce_str' => uniqid(), //����ַ���  
//		  'partner_trade_no'=>$out_order_no,
//		  'openid'=>'oWgn7wDLiEX3eHyr-0DtyTLb9xQk',
//		  'check_name'=>'NO_CHECK',
//		  'amount'=>'1',
//		  'desc'=>'��������ת��',
//		  'spbill_create_ip'=>$_SERVER['SERVER_ADDR']
//		);
        $rsa = new RSA('/var/www/AliRentRomens/application/wxpem/newpubkey.pem', '');
        //var_dump($rsa->encrypt('6217922200880913'));die;
        $parameters = array(
		  'mch_id' => $this->mch_id, //�̻���  
          'nonce_str' => uniqid(), //����ַ���  
		  'partner_trade_no'=>$out_order_no,
		  'enc_bank_no'=>$rsa->encrypt('6217922200880913'),
		  'enc_true_name'=>$rsa->encrypt('�����'),
		  'amount'=>'1',
		  //'desc'=>'��������ת��',
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
		//���ó�ʱ
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //�ϸ�У��
		//����header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//Ҫ����Ϊ�ַ������������Ļ��
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post�ύ��ʽ
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);


		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);
		set_time_limit(0);


		//����curl
		$data = curl_exec($ch);
		//���ؽ��
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			//throw new WxPayException("curl����������:$error");
		}
	}
	//��Ҫʹ��֤�������
	function postXmlSSLCurl($xml,$url,$second=30)
	{
		$ch = curl_init();
		//��ʱʱ��
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		//�������ô�������еĻ�
		//curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//����header
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		//Ҫ����Ϊ�ַ������������Ļ��
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		//����֤��
		//ʹ��֤�飺cert �� key �ֱ���������.pem�ļ�
		//Ĭ�ϸ�ʽΪPEM������ע��
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT, $this->SSLCERT_PATH);
		//Ĭ�ϸ�ʽΪPEM������ע��
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY, $this->SSLKEY_PATH);
		//post�ύ��ʽ
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		$data = curl_exec($ch);
		//���ؽ��
		if($data){
			curl_close($ch);
			return $data;
		}
		else {
			$error = curl_errno($ch);
			echo "curl����������:$error"."<br>";
			curl_close($ch);
			return false;
		}
	}

	//����ת����xml
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


	//xmlת��������
	private function xmlToArray($xml) {


		//��ֹ�����ⲿxmlʵ��


		libxml_disable_entity_loader(true);


		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);


		$val = json_decode(json_encode($xmlstring), true);


		return $val;
	}
	//���ã�����ǩ��
	private function getSign($Obj) {
		foreach ($Obj as $k => $v) {
			$Parameters[$k] = $v;
		}
		//ǩ������һ�����ֵ����������
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//ǩ�����������string�����KEY
		$String = $String . "&key=" . $this->key;
		//ǩ����������MD5����
		$String = md5($String);
		//ǩ�������ģ������ַ�תΪ��д
		$result_ = strtoupper($String);
		return $result_;
	}
	///���ã���ʽ��������ǩ��������Ҫʹ��
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