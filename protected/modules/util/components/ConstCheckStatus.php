<?php

/**
 * 审核状态
 */
class ConstCheckStatus {
    
    //已删除
    const DELETE = -1;
    //未提交
    const NOT_COMMIT = 0;
    //已提交审核
    const COMMIT = 1;
    //审核中
    const INVIEW = 2;
    //已通过
    const PASS = 3;
    //已拒绝
    const REFUSE = 4;
    
}

?>
