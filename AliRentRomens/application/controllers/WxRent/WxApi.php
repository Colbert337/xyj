<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
require_once dirname(__FILE__).'/../../libraries/log.php';
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
class WxApi{
	public $appid = 'wxa5d85fdc620e20bb';
	public $mch_id = '1415605302';
	public $secret = '14691da2b1c3ba690ec2e644e4592b71';
	public $key = 'fc68fe4ec8e4e338e391177a4b723b63';
	public $SSLCERT_PATH = '/var/www/AliRentRomens/application/wxpem/apiclient_cert.pem';
	public $SSLKEY_PATH = '/var/www/AliRentRomens/application/wxpem/apiclient_key.pem';
    //发送归还模板消息
	public function tempsend($access_token,$openid,$prepay_id,$orderno,$create,$return,$money){
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
	//发送租借模板消息
 	public function rent_tempsend($access_token,$openid,$prepay_id,$orderno,$goodsname,$dmoney,$branchid){
	   $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;   
	    $returnaddress=$this->rentaddress($branchid);
	    if($returnaddress == 'NO'){
	        $returnaddress = '感谢您使用雨诺器械租,祝您生活愉快';
	    }else{
	        $returnaddress = '可归还地点'.$returnaddress;
	    }
	    //$this->debuglog($returnaddress);
	    $data = array(
	        'touser'=>$openid,
	        'template_id'=>'vE32QYrfZca6aRlZScMaDDEj5MZwPsIef3cuyjjGGnc',
	        'page'=>'pages/index/orderList',
	        'form_id'=>$prepay_id,
	        'data'=>array(
	            'keyword1'=>array(
	                'value'=>$orderno
	            ),
	            'keyword2'=>array(
	                'value'=>$goodsname
	            ),
	            'keyword3'=>array(
	                'value'=>$dmoney
	            ),
	            'keyword4'=>array(
	                'value'=>$returnaddress
	            )
	        )
	    );
	    $res=$this->curlpost($url, json_encode($data));
	    return $res;
	}
	//获取accesstoken
	public function get_access_token(){
		$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
	    $res=$this->object_to_array(json_decode(file_get_contents($url)));
	    return $res['access_token'];
	}
	//查询可租借和归还的位置
	public function rentaddress($order){
	    $post_arr=array(
	        'QueryType'=>'query_main',
	        'Params'=>'{"branchguid":"'.$order.'"}',
	        'UserGuid'=>'ODh8QHJvbWVucw--'
	    );
	    $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
	    $res=$this->object_to_array($this->curlPostArray($url, $post_arr));
	    if(!$res['status']){
	        $address = str_replace("-",",",$res['msg']);
	        return  $address;
	    }else{
	        return  'NO';
	    }
	}
    //获取accesstoken_api
	public function tempsend_api(){
		if(!$_POST['appid'] || empty($_POST['appid'])){
		  $data = array(
		   'code'=>'1',
	       'msg'=>'appid不能为空'
	      );
	      echo json_encode($data,JSON_UNESCAPED_UNICODE);die;
		}
	    if(!$_POST['secret'] || empty($_POST['secret'])){
		  $data = array(
		   'code'=>'1',
	       'msg'=>'秘钥不能为空'
	      );
	      echo json_encode($data,JSON_UNESCAPED_UNICODE);die;
		}
		$access_token = $this->getRedisByKey($_POST['appid'].'access_token');
		//$this->debuglog('$res'.$access_token);
		if(!$access_token || empty($access_token)){
			$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$_POST['appid'].'&secret='.$_POST['secret'];
	        $res=$this->object_to_array(json_decode(file_get_contents($url)));
	       //$this->debuglog('relit'.json_encode($res));
	        $access_token = $res['access_token'];
	        $this->setRedisKeyVal($_POST['appid'].'access_token',$access_token,3600);
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token='.$access_token;
		if($_POST['type'] == 'mp'){
			$value =array();
			foreach($_POST as $k=> $val) {
				if(substr($k,0,7) == 'keyword'){
					$value[$k]['value']=$val;
				}
			}
            $value['first']['value'] = $_POST['first'];
            $value['remark']['value']=$_POST['remark'];
		  $data = array(
	      'touser'=>$_POST['openid'],
	      'mp_template_msg'=>array(
	         'appid'=>$_POST['mp_appid'],
	         'template_id'=>$_POST['tempid'],
	         'data'=>$value
	      )
	    );
	       if(!empty($_POST['url'])){
	    	   $data['mp_template_msg']['url']=$_POST['url'];
	       }else{
	    	   $data['mp_template_msg']['miniprogram']['appid']=$_POST['appid'];
	    	   $data['mp_template_msg']['miniprogram']['pagepath']=$_POST['page'];
	       }
		}else{
		    $value =array();
			foreach($_POST as $k=> $val) {
				if(substr($k,0,7) == 'keyword'){
					$value[$k]['value']=$val;
				}
			}
		   $data = array(
	         'touser'=>$_POST['openid'],
		     'weapp_template_msg'=>array(
		        'template_id'=>$_POST['tempid'],
		        'page'=>$_POST['page'],
		        'form_id'=>$_POST['form_id'],
		        'data'=>$value
		       )
                
	        );
	        if(!empty($_POST['emp_word'])){
	        	$data['weapp_template_msg']['emphasis_keyword']=$_POST['emp_word'].'.DATA';
	        }
		}
	    //$this->debuglog(json_encode($data));
	    $a=$this->curlpost($url, json_encode($data));
	    echo $a;	
		//发送模板消息
	}
    //微信退款
	public function pay_refund(){
		$url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
		$parameters = array(
		  'appid'=>$this->appid,
		  'mch_id' => $this->mch_id, //商户号  
          'nonce_str' => uniqid(), //随机字符串  
		  'out_trade_no'=>$_POST['out_order_no'],
		  'out_refund_no'=>'r'.$_POST['out_order_no'],
		  'total_fee'=>(int)((float)$_POST['total_fee']*100),
		  'refund_fee'=>(int)((float)$_POST['refund_fee']*100)
		);
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlSSLCurl($xmlData, $url));
		$this->debuglog('PC退款结果'.json_encode($return,JSON_UNESCAPED_UNICODE));
		if($return['return_code'] == 'FAIL'){
			$data = array(
			   'code'=>'1',
			   'msg'=>$return['return_msg']
			);
		}else{
			if($return['result_code'] == 'FAIL'){
				$data = array(
			      'code'=>'1',
			      'msg'=>$return['err_code_des']
			    );
			}else{
				$data = array(
			      'code'=>'0',
			      'msg'=>'退款成功'
			    );
			}
		}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
	}
    public function debuglog($log){
		$date = date("Y-m-d");
		$dubug = $log;
		$logHandler= new CLogFileHandler(dirname(__FILE__).'/../../logs/'.$date.'.log');
		$log = Log::Init($logHandler, 15);
		Log::DEBUG($dubug);
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
    function object_to_array($obj){
		$_arr = is_object($obj)? get_object_vars($obj) :$obj;
		foreach ($_arr as $key => $val){
			$val=(is_array($val)) || is_object($val) ? $this->object_to_array($val) :$val;
			$arr[$key] = $val;
		}
		return $arr;
	}
    //根据key 获取redis的值value
	function getRedisByKey($key){
		$redis = new Redis();
		$redis -> connect( '127.0.0.1', 6379 );
		//$redis->auth($this->C('REDIS_PWD'));
		$redis->auth('romens');
		$redis ->select(2);
		return $redis ->get($key);
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
	function curlPostArray($url,$data){
	    $ch = curl_init(); //初始化curl
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, FALSE);
	    curl_setopt($ch, CURLOPT_URL, $url);//设置链接
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
	    curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据
	    $response = curl_exec($ch);//接收返回信息
	    $charset='UTF-8';
	    if(!curl_errno($ch)) {
	        $info = curl_getinfo($ch);
	        
	        $content_type=explode('charset=', $info['content_type']);
	        if(count($content_type)==2){
	            $charset=$content_type[1];
	        }
	    }
	    
	    if($charset=='GBK'){
	        $res=json_decode(iconv("GBK","UTF-8",$response));
	    }else{
	        $res=json_decode($response);
	    }
	    
	    curl_close($ch); //关闭curl链接
	    return $res;
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
?>
