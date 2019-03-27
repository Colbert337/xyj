<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
class Alert extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->library('functions');
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
	//服务城市+操作提示
	public function alert(){
		switch ($_POST['msg']) {
			case "city":
				$data = array(
				0=>'服务城市:北京 上海 青岛  西安 郑州 秦皇岛',
				1=>'其他城市陆续排入中，敬请期待...'
				);
				break;
			case "chairnote":
				$url="http://java.xingoxing.com/api/rent";//"http://server.571cn.com:12580/sg-rest-api/api/rent";
				//$url="http://server.571cn.com:12580/api/rent";
				if($_POST['lockcode']){
					$url="http://java.xingoxing.com/api/devices";
					$data=array(
		              'devices'=>$_POST['lockcode'],
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
					$res = $this->object_to_array(json_decode(json_decode($this->curlpost($url, $data))));
					if($res['data'][0]['state'] != 1){
						$this->functions->debuglog('设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
						$data = array(
						0=>'N',
						1=>'该设备已离线，请更换立桩重试，请在设备通电(绿灯常亮)的情况下使用'
						);
					}else if(!$res['data'][0]['bikeCode'] || $res['data'][0]['bikeCode'] == null){
						$this->functions->debuglog('设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
						$data = array(
						0=>'N',
						1=>'该立桩上无使用车辆，请更换立桩重试'
						);
					}else{
						$data = array(
						0=>'请在设备通电(绿灯常亮)的情况下使用，首次租借起24小时内（一个封顶收费时限内），同一个支付宝账号使用共享轮椅叠加计时'
						);
						if($_POST['branchid']){
							$address=$this->rentaddress($_POST['branchid']);
							if($address != 'NO'){
								$data[0]=$data[0].',可归还地点:'.$address;
							}
						}
					}
				}else{
					$data = array(
					0=>'请在设备通电(绿灯常亮)的情况下使用，首次租借起24小时内（一个封顶收费时限内），同一个支付宝账号使用共享轮椅叠加计时'
					);
					if($_POST['branchid']){
						$address=$this->rentaddress($_POST['branchid']);
						if($address != 'NO'){
							$data[0]=$data[0].',可归还地点:'.$address;
						}
					}
				}
				break;
			case "chairnote2":
				$url="http://java.xingoxing.com/api/rent";//"http://server.571cn.com:12580/sg-rest-api/api/rent";
				//$url="http://server.571cn.com:12580/api/rent";
				if($_POST['lockcode']){
					$url="http://java.xingoxing.com/api/devices";
					$data=array(
		              'devices'=>$_POST['lockcode'],
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
					$res = $this->object_to_array(json_decode(json_decode($this->curlpost($url, $data))));
					if($res['data'][0]['state'] != 1){
						$this->functions->debuglog('设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
						$data = array(
						0=>'N',
						1=>'该设备已离线，请更换立桩重试，请在设备通电(绿灯常亮)的情况下使用'
						);
					}else if(!$res['data'][0]['bikeCode'] || $res['data'][0]['bikeCode'] == null){
						//$this->functions->debuglog('设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
						$data = array(
						0=>'N',
						1=>'该立桩上无使用车辆，请更换立桩重试'
						);
					}else{
						$data = array(
						0=>'请在设备通电(绿灯常亮)的情况下使用。首次租借起24小时内（一个封顶收费时限内），同一个微信账号使用共享轮椅叠加计时'
						);
						if($_POST['branchid']){
							$address=$this->rentaddress($_POST['branchid']);
							if($address != 'NO'){
								$data[0]=$data[0].',可归还地点:'.$address;
							}
						}
					}
				}else{
					$data = array(
					0=>'请在设备通电(绿灯常亮)的情况下使用。首次租借起24小时内（一个封顶收费时限内），同一个微信账号使用共享轮椅叠加计时'
					);
					if($_POST['branchid']){
						$address=$this->rentaddress($_POST['branchid']);
						if($address != 'NO'){
							$data[0]=$data[0].',可归还地点:'.$address;
						}
					}				
				}
				break;
			case "chairnote3":
				$data = array(
				0=>'请在设备通电(绿灯闪烁)的情况下使用。首次租借起24小时内（一个封顶收费时限内），同一个支付宝账号使用共享轮椅叠加计时'
				);
				if($_POST['branchid']){
					$address=$this->rentaddress($_POST['branchid']);
					if($address != 'NO'){
						$data[0]=$data[0].',可归还地点:'.$address;
					}
				}
				break;
			case "chairnote4":
				$data = array(
				0=>'请在设备通电(绿灯闪烁)的情况下使用。首次租借起24小时内（一个封顶收费时限内），同一个微信账号使用共享轮椅叠加计时'
				);
				if($_POST['branchid']){
					$address=$this->rentaddress($_POST['branchid']);
					if($address != 'NO'){
						$data[0]=$data[0].',可归还地点:'.$address;
					}
				}
				break;
			case "chairnote5":
			    $url="http://java.xingoxing.com/api/rent";//"http://server.571cn.com:12580/sg-rest-api/api/rent";
			    //$url="http://server.571cn.com:12580/api/rent";
			    if($_POST['lockcode']){
			        $url="http://java.xingoxing.com/api/devices";
			        $data=array(
			            'devices'=>$_POST['lockcode'],
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
			        $res = $this->object_to_array(json_decode(json_decode($this->curlpost($url, $data))));
			        if($res['data'][0]['state'] != 1){
			            $this->functions->debuglog('设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
			            $data = array(
			                0=>'N',
			                1=>'该设备已离线，请更换立桩重试，请在设备通电(绿灯常亮)的情况下使用'
			            );
			        }else if(!$res['data'][0]['bikeCode'] || $res['data'][0]['bikeCode'] == null){
			            $this->functions->debuglog('设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
			            $data = array(
			                0=>'N',
			                1=>'该立桩上无使用车辆，请更换立桩重试'
			            );
			        }else{
			            $data = array(
			                0=>'1.请在设备通电(绿灯常亮)的情况下使用，首次租借起24小时内（一个封顶收费时限内），同一个支付宝账号使用共享轮椅叠加计时',
			                1=>'2.扫码成功后，若未立即取出轮椅，请稍候',
			                2=>'3.归还成功后，听到"归还成功，祝您身体健康"的语音证明还车成功',
			                3=>'4.轮椅不慎丢失请联系工作人员进行赔偿，否则有可能降低个人信誉分数',
			                4=>'5.使用完毕后，必须归还到本医院',
			                5=>'6.超过72小时未归还，雨诺有权追究扣除轮椅押金，并有可能降低个人信誉分数'
			            );
			            if($_POST['branchid']){
			                $address=$this->rentaddress($_POST['branchid']);
			                if($address != 'NO'){
			                    $data[6]='7.可归还地点:'.$address;
			                }
			            }
			        }
			    }else{
			        $data = array(
			            0=>'1.请在设备通电(绿灯常亮)的情况下使用，首次租借起24小时内（一个封顶收费时限内），同一个支付宝账号使用共享轮椅叠加计时',
			            1=>'2.扫码成功后，若未立即取出轮椅，请稍候',
			            2=>'3.归还成功后，听到"归还成功，祝您身体健康"的语音证明还车成功',
			            3=>'4.轮椅不慎丢失请联系工作人员进行赔偿，否则有可能降低个人信誉分数',
			            4=>'5.使用完毕后，必须归还到本医院',
			            5=>'6.超过72小时未归还，雨诺有权追究扣除轮椅押金，并有可能降低个人信誉分数'
			        );
			        if($_POST['branchid']){
			            $address=$this->rentaddress($_POST['branchid']);
			            if($address != 'NO'){
			                $data[6]='7.可归还地点:'.$address;
			            }
			        }
			    }		    
			    break;
			case "chairnote6";
			    $lockurl = 'http://140.143.129.247:8081/api/lockStatus?lockCode='.$_POST['lockcode'];
			    $res =  $this->functions->object_to_array(json_decode(json_decode($this->functions->curlpost($lockurl, ''))));
			    if($res['lockStatus'] != '1'){
			        $this->functions->debuglog('云锁设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
			        $data = array(
			            0=>'N',
			            1=>'该设备已离线，请更换立桩重试，请在设备通电(绿灯闪烁)的情况下使用'
			        );
			    }else if(empty($res['rfid'])){
			        $this->functions->debuglog('云锁设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
			        $data = array(
			            0=>'N',
			            1=>'该立桩上无使用车辆，请更换立桩重试'
			        );
			    }else{
			        $data = array(
			            0=>'1.请在设备通电(绿灯常亮)的情况下使用，首次租借起24小时内（一个封顶收费时限内），同一个支付宝账号使用共享轮椅叠加计时',
			            1=>'2.扫码成功后，若未立即取出轮椅，请稍候',
			            2=>'3.归还成功后，听到"归还成功，祝您身体健康"的语音证明还车成功',
			            3=>'4.轮椅不慎丢失请联系工作人员进行赔偿，否则有可能降低个人信誉分数',
			            4=>'5.使用完毕后，必须归还到本医院',
			            5=>'6.超过72小时未归还，雨诺有权追究扣除轮椅押金，并有可能降低个人信誉分数'
			        );
			        if($_POST['branchid']){
			            $address=$this->rentaddress($_POST['branchid']);
			            if($address != 'NO'){
			                $data[6]='7.可归还地点:'.$address;
			            }
			        }
			    }
			    break;
			case "chairnote7":
			    $url="http://java.xingoxing.com/api/rent";//"http://server.571cn.com:12580/sg-rest-api/api/rent";
			    //$url="http://server.571cn.com:12580/api/rent";
			    if($_POST['lockcode']){
			        $url="http://java.xingoxing.com/api/devices";
			        $data=array(
			            'devices'=>$_POST['lockcode'],
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
			        $res = $this->object_to_array(json_decode(json_decode($this->curlpost($url, $data))));
			        if($res['data'][0]['state'] != 1){
			            $this->functions->debuglog('设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
			            $data = array(
			                0=>'N',
			                1=>'该设备已离线，请更换立桩重试，请在设备通电(绿灯常亮)的情况下使用'
			            );
			        }else if(!$res['data'][0]['bikeCode'] || $res['data'][0]['bikeCode'] == null){
			            $this->functions->debuglog('设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
			            $data = array(
			                0=>'N',
			                1=>'该立桩上无使用车辆，请更换立桩重试'
			            );
			        }else{
			            $data = array(
			                0=>'1.请在设备通电(绿灯常亮)的情况下使用，首次租借起24小时内（一个封顶收费时限内），同一个微信账号使用共享轮椅叠加计时',
			                1=>'2.扫码成功后，若未立即取出轮椅，请稍候',
			                2=>'3.归还成功后，听到"归还成功，祝您身体健康"的语音证明还车成功',
			                3=>'4.轮椅不慎丢失请联系工作人员进行赔偿，否则有可能降低个人信誉分数',
			                4=>'5.使用完毕后，必须归还到本医院',
			                5=>'6.超过72小时未归还，雨诺有权追究扣除轮椅押金，并有可能降低个人信誉分数'
			            );
			            if($_POST['branchid']){
			                $address=$this->rentaddress($_POST['branchid']);
			                if($address != 'NO'){
			                    $data[6]='7.可归还地点:'.$address;
			                }
			            }
			        }
			    }else{
			        $data = array(
			            0=>'1.请在设备通电(绿灯常亮)的情况下使用，首次租借起24小时内（一个封顶收费时限内），同一个微信账号使用共享轮椅叠加计时',
			            1=>'2.扫码成功后，若未立即取出轮椅，请稍候',
			            2=>'3.归还成功后，听到"归还成功，祝您身体健康"的语音证明还车成功',
			            3=>'4.轮椅不慎丢失请联系工作人员进行赔偿，否则有可能降低个人信誉分数',
			            4=>'5.使用完毕后，必须归还到本医院',
			            5=>'6.超过72小时未归还，雨诺有权追究扣除轮椅押金，并有可能降低个人信誉分数'
			        );
			        if($_POST['branchid']){
			            $address=$this->rentaddress($_POST['branchid']);
			            if($address != 'NO'){
			                $data[6]='7.可归还地点:'.$address;
			            }
			        }
			    }		    
			    break;
			case "chairnote8";
			$lockurl = 'http://140.143.129.247:8081/api/lockStatus?lockCode='.$_POST['lockcode'];
			$res =  $this->functions->object_to_array(json_decode(json_decode($this->functions->curlpost($lockurl, ''))));
			if($res['lockStatus'] != '1'){
			    $this->functions->debuglog('云锁设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
			    $data = array(
			        0=>'N',
			        1=>'该设备已离线，请更换立桩重试，请在设备通电(绿灯闪烁)的情况下使用'
			    );
			}else if(empty($res['rfid'])){
			    $this->functions->debuglog('云锁设备异常：'.$_POST['lockcode'].json_encode($res,JSON_UNESCAPED_UNICODE));
			    $data = array(
			        0=>'N',
			        1=>'该立桩上无使用车辆，请更换立桩重试'
			    );
			}else{
			    $data = array(
			        0=>'1.请在设备通电(绿灯闪烁)的情况下使用，首次租借起24小时内（一个封顶收费时限内），同一个微信账号使用共享轮椅叠加计时',
			        1=>'2.扫码成功后，若未立即取出轮椅，请稍候',
			        2=>'3.归还成功后，听到"归还成功，祝您身体健康"的语音证明还车成功',
			        3=>'4.轮椅不慎丢失请联系工作人员进行赔偿，否则有可能降低个人信誉分数',
			        4=>'5.使用完毕后，必须归还到本医院',
			        5=>'6.超过72小时未归还，雨诺有权追究扣除轮椅押金，并有可能降低个人信誉分数'
			    );
			    if($_POST['branchid']){
			        $address=$this->rentaddress($_POST['branchid']);
			        if($address != 'NO'){
			            $data[6]='7.可归还地点:'.$address;
			        }
			    }
			}
			break;
			default:
				echo "no chose";
		}
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
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
?>