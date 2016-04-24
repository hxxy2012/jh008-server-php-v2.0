<?php

class UserInfoController extends AdminController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delUser', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array(''),
				'users' => array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users' => array('@'),
			),
			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}
    
    
    /**
     * 获取用户信息
     */
    public function actionGetUserInfo()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getPost('uid'),
            ),
            array(
                array('uid', 'required'),
                array('uid', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $user = UserInfoAdmin::model()->getInfo($ck->uid);
        Yii::app()->res->output(Error::NONE, '获取成功', array('user' => $user));
    }


    /**
     * 删除用户
     */
    public function actionDelUser()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getPost('uid'),
            ),
            array(
                array('uid', 'required'),
                array('uid', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = UserInfo::model()->del($ck->uid);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了用户');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }


    /**
     * 获取用户列表
     */
    public function actionGetUsers()
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserInfoAdmin::model()->searchUsers($ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取用户列表（回收站）
     */
    public function actionGetDelUsers()
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserInfoAdmin::model()->searchUsers($ck->keyWords, $ck->page, $ck->size, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 统计信息
     */
    public function actionCountInfo()
    {
        $today = time();
        $yesterday = strtotime('yesterday');
        $sevenday = strtotime('-7 day');
        
        //今日起止时间
        $tStart = date('Y-m-d 00:00:00', $today);
        $tEnd = date('Y-m-d 23:59:59', $today);
        //昨日起止时间
        $yStart = date('Y-m-d 00:00:00', $yesterday);
        $yEnd = date('Y-m-d 23:59:59', $yesterday);
        //7日起止时间
        $sStart = date('Y-m-d 00:00:00', $sevenday);
        $sEnd = date('Y-m-d 23:59:59', $yesterday);
        
        $allNum = UserInfoAdmin::model()->countAllUsers();
        $todayNum = UserInfoAdmin::model()->countUsers($tStart, $tEnd);
        $yesterdayNum = UserInfoAdmin::model()->countUsers($yStart, $yEnd);
        $sevendayNum = UserInfoAdmin::model()->countUsers($sStart, $sEnd);
        
        Yii::app()->res->output(
                Error::NONE,
                '获取成功',
                array(
                    'all_num' => $allNum,
                    'today_num' => $todayNum,
                    'yesterday_num' => $yesterdayNum,
                    'sevenday_num' => $sevendayNum,
                )
                );
    }
    
    
    /**
     * 获取注册每天注册用户统计
     */
    public function actionGetRegistCount()
    {
        $ck = Rules::instance(
            array(
                'startDate' => Yii::app()->request->getParam('startDate'),
                'endDate' => Yii::app()->request->getParam('endDate'),
            ),
            array(
                array('startDate, endDate', 'date', 'format' => 'yyyy-mm-dd'),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserRegistCount::model()->getRegistCount($ck->startDate, $ck->endDate);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
}