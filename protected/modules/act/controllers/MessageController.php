<?php

class MessageController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + add, delete, delContact, shieldContact, delShield', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(''),
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
     * 用户私信联系人
     */
    public function actionContacts()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserContact::model()->contacts(Yii::app()->user->id, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'user contacts success', $rst);
    }
    
    
    /**
     * 删除联系人
     */
    public function actionDelContact()
    {
        $ck = Rules::instance(
            array(
                'contactId' => Yii::app()->request->getPost('contactId'),
            ),
            array(
                array('contactId', 'required'),
                array('contactId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserContact::model()->del(Yii::app()->user->id, $ck->contactId);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'del user contact success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'del user contact fail');
    }

    
    /**
     * 屏蔽联系人
     */
    public function actionShieldContact()
    {
        $ck = Rules::instance(
            array(
                'contactId' => Yii::app()->request->getPost('contactId'),
            ),
            array(
                array('contactId', 'required'),
                array('contactId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserContact::model()->shield(Yii::app()->user->id, $ck->contactId);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'shield user contact success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'shield user contact fail');
    }
    
    
    /**
     * 取消屏蔽联系人
     */
    public function actionDelShield()
    {
        $ck = Rules::instance(
            array(
                'contactId' => Yii::app()->request->getPost('contactId'),
            ),
            array(
                array('contactId', 'required'),
                array('contactId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserContact::model()->delShield(Yii::app()->user->id, $ck->contactId);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'shield user contact success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'shield user contact fail');
    }


    /**
     * 用户私信记录
     */
    public function actionHistory()
    {
        $ck = Rules::instance(
            array(
                'contactId' => Yii::app()->request->getParam('contactId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('contactId, page, size', 'required'),
                array('contactId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserMessageMap::model()->messages(Yii::app()->user->id, $ck->contactId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'user messages success', $rst);
    }
    
    
    /**
     * 发私信
     */
    public function actionAdd()
    {
        $ck = Rules::instance(
            array(
                'rev_id' => Yii::app()->request->getPost('revId'),
                'content' => Yii::app()->request->getPost('content'),
            ),
            array(
                array('rev_id', 'required'),
                array('rev_id', 'numerical', 'integerOnly' => TRUE),
                array('content', 'CZhEnV', 'min' => 1, 'max' => 120),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserMessageMap::model()->addUserMsg(Yii::app()->user->id, $ck->rev_id, $ck->content);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'add user message success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'add user message fail');
    }
    
    
    /**
     * 删除私信
     */
    public function actionDelete()
    {
        $ck = Rules::instance(
            array(
                'messageId' => Yii::app()->request->getPost('messageId'),
            ),
            array(
                array('messageId', 'required'),
                array('messageId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserMessageMap::model()->del($ck->messageId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'del user message success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'del user message fail');
    }
    
}
