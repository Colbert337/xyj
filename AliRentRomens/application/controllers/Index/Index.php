<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
class Index extends CI_Controller {
	public function __construct()//1111
	{
		parent::__construct();
		$this->load->library('functions');
	}
	public function index($lockcode){
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'micromessenger') !==false){
          $oauthUrl = 'http://weixin.yiyao365.cn/index.php?g=Ability&m=WxRentLockcode&a=index&token=fdpbbq1480645793&lockcode='.$lockcode;	
		  header ( 'Location:' . $oauthUrl );
          exit();
		}elseif(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'alipayclient') !==false){
		  $url = 'alipays://platformapi/startapp?appId=2017051807276496&page=pages/index/Info&query=lockCode%3D'.$lockcode;
		 // $url = 'alipays://platformapi/startapp?appId=2017051807276496&page=pages/index/Info?lockCode='.$lockcode;
			header('Location:'.$url);
		  exit();
		}else{
			exit('请支付宝或微信内扫描使用');
		}

	}
}
?>