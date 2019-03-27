<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
defined('BASEPATH') OR exit('No direct script access allowed');
require_once dirname(__FILE__).'/../WxRent/WxPay.php';
require_once dirname(__FILE__).'/../AliRent/AliZhimaAPI.php';
require_once dirname(__FILE__).'/../../libraries/Page.php';
class Cloud extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->library('functions');
	}
	public function mysql(){
		echo "<pre>";
		$this->load->database();
		$this->load->dbforge();
//        $data = array(
//          'name'=>'zhangyue',
//          'username'=>'张月',
//          'pwd'=>'123456',
//          'permission'=>'h'
//        );
//         $this->db->insert('users',$data);
// 		//$query->result_array();
        
// 		$query = $this->db->query("DELETE FROM users WHERE name='wangyue';");
// 		$query->result_array();
		$query = $this->db->query("SELECT * FROM users;");
		var_dump($query->result_array());
	}
	public function htos(){
		$this->load->view('Cloud/htos');
	}
	public function login($menu){
		if(!empty($_POST)){
			$this->load->database();
			$username = $_POST['username'];
			$query = $this->db->query("SELECT pwd FROM users where name='$username';");
			$res=$query->result_array();
			if(!empty($res)){
				if($res[0]['pwd'] == $_POST['password']){
					echo '<script>alert(\'登陆成功\');window.location.href=\'http://xyj.yiyao365.cn/AliRentRomens/index.php/Cloud/Cloud/menu\';</script>';
				}else{
					echo '<script>alert(\'密码错误\');</script>';
				}
			}else{
				echo '<script>alert(\'账号错误\');</script>';
			}
		}
		$this->load->view('Cloud/login');
	}
	public function menu($menu){
		$this->load->view('Cloud/menu');
	}
	public function index($lock){
		if(!empty($lock)){
		    $post_arr=array(
		        'QueryType'=>'get_goodsinfo',
		        'Params'=>'{"qrcode":"'.$lock.'"}',
		        'UserGuid'=>'ODh8QHJvbWVucw--'
		    );
		    $url='http://mshop.yiyao365.cn/wsapi/v1/alirent';
		    $res=$this->object_to_array($this->functions->curlPostArray($url, $post_arr));
		    $lockcode=$res[0]['lockcode'];
		    $lockurl = 'http://140.143.129.247:8081/api/lockStatus?lockCode='.$lockcode;
			$c =  $this->functions->object_to_array(json_decode(json_decode($this->functions->curlpost($lockurl, ''))));
			$data['res'] = $c;
			$data['lockcode']=$lock;
		}
		$this->load->view('Cloud/index',$data);
	}
	public function locklog($orderno,$lockcode,$branch,$page){
		$branch=(urldecode($branch));
		$config['base_url'] = 'http://xyj.yiyao365.cn/AliRentRomens/index.php/Cloud/Cloud/locklog/'.$orderno.'/'.$lockcode.'/'.$branch.'/';
		if($orderno == 'no'){
			$orderno = '';
		}
		if($lockcode == 'no'){
			$lockcode = '';
		}
		if($branch == 'no'){
			$branch = '';
		}
		$this->load->database();
		if(!$page){
			$page = 0;
		}
		$this->load->library('pagination');
		if(!empty($orderno)|| !empty($lockcode) || !empty($branch)){
			$where = 'where ';
			if(!empty($orderno)){
				$where = $where.'ext_order_no = \''.$orderno.'\'';
			}
			if(!empty($lockcode)){
				if(strlen($where)>6){
					$where = $where.' and get_lock_id = \''.$lockcode.'\'';
				}else{
					$where = $where.'get_lock_id = \''.$lockcode.'\'';
				}
			}
			if(!empty($branch)){
				if(strlen($where)>6){
					$where = $where.' and shopname like \'%'.$branch.'%\'';
				}else{
					$where = $where.'shopname like \'%'.$branch.'%\'';
				}

			}
			$query = $this->db->query("SELECT lock_log.*,orders.shopname as borrow_shop_name FROM lock_log left join orders on lock_log.ext_order_no = orders.out_order_no $where order by convert(lock_log.start_time,datetime) desc limit $page,15;");
			$count = $this->db->query("SELECT lock_log.*,orders.shopname as borrow_shop_name FROM lock_log left join orders on lock_log.ext_order_no = orders.out_order_no $where;");
		}else{
			$query = $this->db->query("SELECT lock_log.*,orders.shopname as borrow_shop_name FROM lock_log left join orders on lock_log.ext_order_no = orders.out_order_no order by convert(lock_log.start_time,datetime) desc limit $page,15;");
			$count = $this->db->query("SELECT lock_log.*,orders.shopname as borrow_shop_name FROM lock_log left join orders on lock_log.ext_order_no = orders.out_order_no;");
		}
		$config['total_rows'] = count($count->result_array());
		$config['per_page'] = 15;
		$config['first_link']= '首页';
		$config['next_link']= '下一页';
		$config['prev_link']= '上一页';
		$config['last_link']= '尾页';
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['full_tag_close'] = '</ul>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li><a class="active">';
		$config['cur_tag_close'] = '</a></li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		$data = array(
    	  'query'=>$query->result_array(),
		  'page'=>$this->pagination->create_links(),
		  'orderno'=>$orderno,
		  'lockcode'=>$lockcode,
		  'branch'=>$branch
		);
		$this->load->view('Cloud/locklog',$data);
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