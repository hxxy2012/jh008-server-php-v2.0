<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

class AlipayNotifyUrl{
    
    public $alipay_config = array();

    public function __construct($partner = NULL, $private_key_path = NULL, $ali_public_key_path = NULL)
    {
        //合作身份者id，以2088开头的16位纯数字
        $this->alipay_config['partner'] = $partner;
        //商户的私钥（后缀是.pen）文件相对路径
        $this->alipay_config['private_key_path'] = $private_key_path;
        //支付宝公钥（后缀是.pen）文件相对路径
        $this->alipay_config['ali_public_key_path'] = $ali_public_key_path;
        
        //签名方式 不需修改
        $this->alipay_config['sign_type']    = strtoupper('RSA');

        //字符编码格式 目前支持 gbk 或 utf-8
        $this->alipay_config['input_charset']= strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $this->alipay_config['cacert']    = getcwd().'\\cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $this->alipay_config['transport']    = 'http';
	}

    
    public function notifyurl() {
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        
        //return $verify_result;
        
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            
            
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号
            //$out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            //$trade_no = $_POST['trade_no'];
            //交易状态
            //$trade_status = $_POST['trade_status'];
            
            $data = array();
            $data['out_trade_no'] = Yii::app()->request->getPost('out_trade_no');
            $data['total_fee'] = Yii::app()->request->getPost('total_fee');
            $data['trade_no'] = Yii::app()->request->getPost('trade_no');
            $data['buyer_email'] = Yii::app()->request->getPost('buyer_email');
            $data['gmt_create_time'] = Yii::app()->request->getPost('gmt_create');
            $data['gmt_payment'] = Yii::app()->request->getPost('gmt_payment');
            
            $data['trade_status'] = Yii::app()->request->getPost('trade_status');

            if($data['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                    //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                    //如果有做过处理，不执行商户的业务程序
                
                Yii::app()->alipay->notifyProcess($data);
                
                //注意：
                //该种交易状态只在两种情况下出现
                //1、开通了普通即时到账，买家付款成功后。
                //2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if ($data['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                    //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                    //如果有做过处理，不执行商户的业务程序

                Yii::app()->alipay->notifyProcess($data);
                
                //注意：
                //该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";		//请不要修改或删除

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

}

?>