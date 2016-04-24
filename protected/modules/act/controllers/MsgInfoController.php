<?php

class MsgInfoController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delMsg, delAll, setRead', // we only allow deletion via POST request
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
     * 获取消息列表
     */
    public function actionGetUMsgs() 
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $list = MsgRevUserMap::model()->getUMsgs(Yii::app()->user->id, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $list);
    }
    
    
    /**
     * 删除消息
     */
    public function actionDelMsg()
    {
        $ck = Rules::instance(
            array(
                'msgId' => Yii::app()->request->getPost('msgId'),
            ),
            array(
                array('msgId', 'required'),
                array('msgId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = MsgRevUserMap::model()->del($ck->msgId, Yii::app()->user->id);
        if ($r) {
            Yii::app()->res->output(Error::NONE, '删除成功');
        }  else {
            Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
        }
    }
    
    
    /**
     * 清空消息
     */
    public function actionDelAll()
    {
        $r = MsgRevUserMap::model()->delAll(Yii::app()->user->id);
        if ($r) {
            Yii::app()->res->output(Error::NONE, '清空成功');
        }  else {
            Yii::app()->res->output(Error::OPERATION_EXCEPTION, '清空失败');
        }
    }
    
    
    /**
     * 设置已读
     */
    public function actionSetRead()
    {
        $ck = Rules::instance(
            array(
                'msgId' => Yii::app()->request->getPost('msgId'),
            ),
            array(
                array('msgId', 'required'),
                array('msgId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = MsgRevUserMap::model()->setRead($ck->msgId, Yii::app()->user->id);
        if ($r) {
            Yii::app()->res->output(Error::NONE, '设置成功');
        }  else {
            Yii::app()->res->output(Error::OPERATION_EXCEPTION, '设置失败');
        }
    }
    
}