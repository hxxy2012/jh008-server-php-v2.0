<?php

class PrizeInfoController extends AdminController
{
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + addPrize, addAward, saveAwardUser', // we only allow deletion via POST request
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
     * 添加活动抽奖方案
     */
    public function actionAddPrize()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('actId, name', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 32),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new PrizeInfo();
        $ck->setModelAttris($model);
        $r = $model->addActPrize($ck->actId);
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了抽奖方案：' + $ck->name);
            return Yii::app()->res->output(Error::NONE, '添加抽奖方案成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加抽奖方案失败');
    }


    /**
     * 获取活动抽奖方案列表
     */
    public function actionGetPrizes()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'isOver' => Yii::app()->request->getParam('isOver'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
                array('isOver', 'in', 'range' => array(0, 1)),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActPrizeMap::model()->getPrizes($ck->actId, $ck->isOver);
        
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 添加活动抽奖方案的奖项
     */
    public function actionAddAward()
    {
        $ck = Rules::instance(
            array(
                'prizeId' => Yii::app()->request->getPost('prizeId'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('prizeId, name', 'required'),
                array('prizeId', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 32),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new AwardInfo();
        $ck->setModelAttris($model);
        $r = $model->addActAward($ck->prizeId);
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了抽奖奖项：' + $ck->name);
            return Yii::app()->res->output(Error::NONE, '添加抽奖奖项成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加抽奖奖项失败');
    }
    
    
    /**
     * 获取活动抽奖方案的奖项列表
     */
    public function actionGetAwards()
    {
        $ck = Rules::instance(
            array(
                'prizeId' => Yii::app()->request->getParam('prizeId'),
            ),
            array(
                array('prizeId', 'required'),
                array('prizeId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = PrizeAwardMap::model()->getAwards($ck->prizeId);
        
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 产生一个备选中奖者
     */
    public function actionMakeAwardUser()
    {
        $ck = Rules::instance(
            array(
                'awardId' => Yii::app()->request->getParam('awardId'),
                'startTime' => Yii::app()->request->getParam('startTime'),
                'endTime' => Yii::app()->request->getParam('endTime'),
                'needUserInfo' => Yii::app()->request->getParam('needUserInfo'),
                'includeWinners' => Yii::app()->request->getParam('includeWinners'),
            ),
            array(
                array('awardId, needUserInfo, includeWinners', 'required'),
                array('awardId', 'numerical', 'integerOnly' => true),
                array('startTime, endTime', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('needUserInfo, includeWinners', 'in', 'range' => array(0, 1)),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $user = AwardUserMap::model()->makeAwardUser($ck->awardId, $ck->startTime, $ck->endTime, $ck->includeWinners, $ck->needUserInfo);
        
        Yii::app()->res->output(Error::NONE, '获取成功', array('user' => $user));
    }
    
    
    /**
     * 保存一个中奖者
     */
    public function actionSaveAwardUser()
    {
        $ck = Rules::instance(
            array(
                'award_id' => Yii::app()->request->getPost('awardId'),
                'u_id' => Yii::app()->request->getPost('userId'),
            ),
            array(
                array('award_id, u_id', 'required'),
                array('award_id, u_id', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new AwardUserMap();
        $ck->setModelAttris($model);
        $r = $model->add();
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '保存了一个中奖者');
            return Yii::app()->res->output(Error::NONE, '保存中奖者成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '保存中奖者失败' . json_encode($model->getErrors()));
    }
    
    
    /**
     * 活动奖项的中奖者列表
     */
    public function actionGetAwardUsers()
    {
        $ck = Rules::instance(
            array(
                'awardId' => Yii::app()->request->getParam('awardId'),
            ),
            array(
                array('awardId', 'required'),
                array('awardId', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = AwardUserMap::model()->getUsers($ck->awardId);
        
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
}
