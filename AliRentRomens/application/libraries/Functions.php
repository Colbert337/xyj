<?php
require_once dirname(__FILE__).'/../libraries/log.php';
//require_once dirname(__FILE__).'/../libraries/ObjectUtil.php';
class Functions {
	//post 异步  data非json
	//	function C($className)
	//	{
	//		return LtObjectUtil::singleton($className);
	//	}
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
	//根据key 查看是否存在redis的key
	function exitisRedisKey($key){
		$redis = new Redis();
		$redis -> connect( '127.0.0.1', 6379 );
		//$redis->auth($this->C('REDIS_PWD'));
		$redis->auth('romens');
		$redis ->select(2);
		$isexitis=$redis->exists($key);
		return $isexitis;
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
	//根据key 删除redis的值value
	function delRedisByKey($key){
		$redis = new Redis();
		$redis -> connect( '127.0.0.1', 6379 );
		//$redis->auth(C('REDIS_PWD'));
		$redis->auth('romens');
		$redis ->select(2);
		$redis->del($key);
	}
	public function debuglog($log){
		$date = date("Y-m-d");
		$dubug = $log;
		$logHandler= new CLogFileHandler(dirname(__FILE__).'/../logs/'.$date.'.log');
		$log = Log::Init($logHandler, 15);
		Log::DEBUG($dubug);
	}
	public function alireturn($log){
		$date = date("Y-m-d");
		$dubug = $log;
		$logHandler= new CLogFileHandler(dirname(__FILE__).'/../alireturn/'.$date.'.log');
		$log = Log::Init($logHandler, 15);
		Log::DEBUG($dubug);
	}
	//时间戳转换天时分秒
	function Sec2Time($time){
		if(is_numeric($time)){
			$value = array(
      "days" => 0, "hours" => 0,
      "minutes" => 0, "seconds" => 0,
			);
			if($time >= 86400){
				$value["days"] = floor($time/86400);
				$time = ($time%86400);
			}
			if($time >= 3600){
				$value["hours"] = floor($time/3600);
				$time = ($time%3600);
			}
			if($time >= 60){
				$value["minutes"] = floor($time/60);
				$time = ($time%60);
			}
			$value["seconds"] = floor($time);
			//return (array) $value;
			$t=$value["days"] ."天".$value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
			Return $t;

		}else{
			return (bool) FALSE;
		}
	}
	//下载图片
	function dlfile($file_url, $save_to)
	{
		$in=  fopen($file_url, "rb");
		$out=  fopen($save_to, "wb");
		while ($chunk = fread($in,8192))
		{
			fwrite($out, $chunk, 8192);
		}
		fclose($in);
		fclose($out);
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

?>