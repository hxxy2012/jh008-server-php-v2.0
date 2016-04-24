<?php

class AlipayTool extends CComponent
{
    //参数配置
    //合作身份者id，以2088开头的16位纯数字
    public $partner;
    
    //商户的私钥（后缀是.pen）文件相对路径
    public $privateKeyPath;
    
    //支付宝公钥（后缀是.pen）文件相对路径
    public $aliPublicKeyPath;

    protected $alipayService;
    

    public function init() 
    {
        $this->alipayService = new AlipayNotifyUrl($this->partner, $this->privateKeyPath, $this->aliPublicKeyPath);
    }
    
    
    /**
     * 验证回调url的参数是否合法
     * 
     * @return bool
     */
    public function notifyUrlValid()
    {
        return $this->alipayService->notifyurl();
    }
    
    
    /**
     * 回调业务处理
     * 
     * @param type $data
     */
    public function notifyProcess($data)
    {
        Order::model()->alipayNotify($data);
        //V2.0
        PayOrder::model()->pay($data['out_trade_no'], $data['total_fee'], ConstPayPlatform::ALIPAY, $data['trade_no'], $data['buyer_email'], $data['gmt_payment']);
    }

}

?>
