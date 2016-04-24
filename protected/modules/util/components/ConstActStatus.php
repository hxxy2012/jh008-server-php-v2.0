<?php

class ConstActStatus {

    //已删除
    const DELETE = -1;
    //未提交
    const NOT_COMMIT = 0;
    //待审核
    const CHECK_WAITING = 1;
    //审核中
    const CHECKING = 2;
    //未通过
    const NOT_PASS = 3;
    //未发布
    const NOT_PUBLISH = 4;
    //已发布
    const PUBLISHING = 5;
    //已下架
    const OFF_PUBLISH = 6;
    
}

?>
