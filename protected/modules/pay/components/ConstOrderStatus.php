<?php

/**
 * 订单状态
 */
class ConstOrderStatus {
    
    //已删除-1
    const DELETE = -1;
    
    //待支付
    const WAIT_PAY = 0;
    
    //支付失败
    const FAIL_PAY = 1;
    
    //支付成功
    const HAS_PAY = 2;
    
    //待发货
    const WAIT_DELIVER = 3;
    
    //已发货
    const HAS_DELIVER = 4;
    
    //发货失败
    const DELIVER_FAIL = 5;
    
}

?>
