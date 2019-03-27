<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors', '0');
error_reporting(E_ALL ^ E_NOTICE);
if(date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
require_once dirname(__FILE__).'/../../vaop/aop/AopClient.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayTradeAppPayRequest.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayTradeQueryRequest.php';
require_once dirname(__FILE__).'/../../aop/request/AlipayTradeRefundRequest.php';
require_once dirname(__FILE__).'/../WxRent/WxApi.php';
class AliPay extends CI_Controller {
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
    //支付宝退款
    public function pay_refund(){
        $request = new AlipayTradeRefundRequest ();
        $bizcontentarray=array(
            'trade_no'=>'201812271528272883',
            //'out_trade_no'=>'201810241017589104',
            'refund_amount'=>'70'
        );
        $bizcontent0 = json_encode($bizcontentarray, JSON_UNESCAPED_UNICODE);
        $bizcontent = iconv("UTF-8", "GBK", $bizcontent0);
        $request->setBizContent($bizcontent);
        //$signData = $request->getApiParas();
        //$sign = $this->aop->rsaSign($signData, $this->aop->signType);
        $result = $this->aop->execute($request);
        var_dump($result);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
}