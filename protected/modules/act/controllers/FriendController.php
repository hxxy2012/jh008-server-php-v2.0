<?php

class FriendController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + add, delete', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('list'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    
    /**
     * 用户好友
     */
    public function actionList()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'type' => Yii::app()->request->getParam('type'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('uid, cityId, type, page, size', 'required'),
                array('type', 'in', 'range' => array(1, 2)),
                array('uid, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserFans::model()->listUsers($ck->uid, $ck->cityId, $ck->type, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'user friends success', $rst);
    }
    
    
    /**
     * 加关注
     */
    public function actionAdd()
    {
        $ck = Rules::instance(
            array(
                'focusId' => Yii::app()->request->getPost('focusId'),
            ),
            array(
                array('focusId', 'required'),
                array('focusId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserFans::model()->add($ck->focusId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'add focus success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'add focus fail');
    }
    
    
    /**
     * 取消关注
     */
    public function actionDelete()
    {
        $ck = Rules::instance(
            array(
                'focusId' => Yii::app()->request->getPost('focusId'),
            ),
            array(
                array('focusId', 'required'),
                array('focusId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserFans::model()->del($ck->focusId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'del focus success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'del focus fail');
    }
    
}
