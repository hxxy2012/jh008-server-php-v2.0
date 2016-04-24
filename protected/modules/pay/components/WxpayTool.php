<?php

class WxpayTool extends CComponent
{
    //参数配置
    //APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
    public $appid;
    
    //MCHID：商户号（必须配置，开户邮件中可查看）
    public $mchid;
    
    //KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
    public $key;
    
    //APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）
    public $appsecret;
    
    //回调url
    public $notify_url;
    
    /**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
    public $sslcert_path;
    public $sslkey_path;
    
    /**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
    public $curl_proxy_host;
    public $curl_proxy_port;
    
    /**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
    public $report_level;

    protected $wxpayApiService;
    protected $wxpayNotify;

    public function init() 
    {
        WxPayConfig::init($this->appid, $this->mchid, $this->key, $this->appsecret, $this->notify_url, $this->sslcert_path, $this->sslkey_path, $this->curl_proxy_host, $this->curl_proxy_port, $this->report_level);
        $this->wxpayApiService = new WxPayApi();
        $this->wxpayNotify = new WxPayNotify();
    }
    
    
    /**
     * 统一下单
     */
    public function unifiedOrder($outTradeNo, $body, $totalPrice)
    {
        $totalFee = $totalPrice * 100.00;
        $inputObj = new MyWxPayData();
        $inputObj->SetOut_trade_no($outTradeNo);
        $inputObj->SetBody($body);
        $inputObj->SetTotal_fee($totalFee);
        $inputObj->SetTrade_type('APP');
        return $this->wxpayApiService->unifiedOrder($inputObj);
    }
    
    
    /**
     * 统一下单生成支付url
     */
    public function unifiedOrder2Url($outTradeNo, $body, $totalPrice)
    {
        $totalFee = $totalPrice * 100.00;
        //①、获取用户openid
        $tools = new WxPayTJsApiPay();
        $openId = $tools->GetOpenid();

        //②、统一下单
        $input = new MyWxPayData();
        $input->SetBody($body);
        $input->SetAttach($body);
        //$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetOut_trade_no($outTradeNo);
        $input->SetTotal_fee($totalFee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("recharge");
        //$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = $this->wxpayApiService->unifiedOrder($input);
        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        //获取共享收货地址js函数参数
        //$editAddress = $tools->GetEditAddressParameters();
        print_r('<br>param:');
        print_r(json_encode($jsApiParameters));
        exit();
    }
    
    
    /**
     * 验证回调url的参数是否合法
     * 
     * @return bool
     */
    public function notifyUrlValid()
    {
        return $this->wxpayNotify->Handle();
    }
    
    
    /**
     * 回调业务处理
     * 
     * @param type $data
     */
    public function notifyProcess($data)
    {
        Order::model()->wxpayNotify($data);
    }

}

?>
