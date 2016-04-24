<?php

/**
 * 交易流水类型
 */
class ConstTransType {
    
    //收入
    const INCOME = 'income';
    
    //支出
    const EXPENDITURE = 'expenditure';
    
    //充值
    const RECHARGE = 'recharge';
    
    //提现
    const WITHDRAW_CASH = 'withdraw_cash';
    
    //申请提现
    const APPLY_WITHDRAW_CASH = 'apply_withdraw_cash';
    
    //申请提现退费
    const REFUND_APPLY_WITHDRAW_CASH = 'refund_apply_withdraw_cash';
    
    //暂存申请提现
    const TEM_APPLY_WITHDRAW_CASH = 'tmp_apply_withdraw_cash';
    
    //暂存申请提现退费
    const REFUND_TEM_APPLY_WITHDRAW_CASH = 'refund_tmp_apply_withdraw_cash';
    
    //退费
    const REFUND = 'refund';
    
    //手续费
    const FEE = 'fee';
    
    //收取活动报名费
    const REV_ACT_ENROLL = 'act_enroll';
    
    //收取报名费的手续费
    const REV_ACT_ENROLL_FEE = 'rev_act_enroll_fee';
    
    //短信费
    const SMS = 'sms';
    
    //支付活动报名费
    const PAY_ACT_ENROLL = 'pay_act_enroll';
    
}

?>
