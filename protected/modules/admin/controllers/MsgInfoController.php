<?php

class MsgInfoController extends AdminController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + addMsgType, updateMsgType, delMsgType, addMsg, updateMsg, delMsg, addPush', // we only allow deletion via POST request
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
     * 添加消息类型
     */
    public function actionAddMsgType()
    {
        $ck = Rules::instance(
            array(
                'name' => Yii::app()->request->getPost('name'),
                'is_broadcast' => Yii::app()->request->getPost('isBroadcast', 1),
            ),
            array(
                array('name, is_broadcast', 'required'),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('is_broadcast', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new MsgType();
        $ck->setModelAttris($model);
        $r = $model->add();
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了消息类型');
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加失败');
    }
    
    
    /**
     * 修改消息类型
     */
    public function actionUpdateMsgType()
    {
        $ck = Rules::instance(
            array(
                'msgTypeId' => Yii::app()->request->getPost('msgTypeId'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('msgTypeId', 'required'),
                array('msgTypeId', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = MsgType::model()->findByPk($ck->msgTypeId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '不存在此类型');
        }
        
        $ck->setModelAttris($model);
        
        $r = $model->updateType();
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改了消息类型');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 删除消息类型
     */
    public function actionDelMsgType() 
    {
        $ck = Rules::instance(
            array(
                'msgTypeId' => Yii::app()->request->getPost('msgTypeId'),
            ),
            array(
                array('msgTypeId', 'required'),
                array('msgTypeId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = MsgType::model()->findByPk($ck->msgTypeId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '不存在此类型');
        }
        
        $r = $model->del();
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了消息类型');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }

    
    /**
     * 获取消息类型
     */
    public function actionGetMsgType()
    {
        $ck = Rules::instance(
            array(
                'msgTypeId' => Yii::app()->request->getPost('msgTypeId'),
            ),
            array(
                array('msgTypeId', 'required'),
                array('msgTypeId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $msgType = MsgType::model()->getType($ck->msgTypeId);
        
        return Yii::app()->res->output(Error::NONE, '获取成功', array('type' => $msgType));
    }
    
    
    /**
     * 获取消息类型列表
     */
    public function actionGetMsgTypes() 
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
            ),
            array(
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = MsgType::model()->searchTypes($ck->keyWords);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取消息类型列表（回收站）
     */
    public function actionGetDelMsgTypes() 
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
            ),
            array(
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = MsgType::model()->searchTypes($ck->keyWords, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }

    
    /**
     * 添加消息
     */
    public function actionAddMsg()
    {
        $ck = Rules::instance(
            array(
                'type_id' => Yii::app()->request->getPost('typeId'),
                'content' => Yii::app()->request->getPost('content'),
                'filter' => Yii::app()->request->getPost('filter'),
                'isPublishNow' => Yii::app()->request->getPost('isPublishNow', 1),
                'publish_time' => Yii::app()->request->getPost('publishTime'),
            ),
            array(
                array('type_id, isPublishNow', 'required'),
                array('type_id', 'numerical', 'integerOnly' => true),
                array('content', 'CZhEnV', 'min' => 1, 'max' => 120),
                array('filter', 'CZhEnV', 'min' => 1, 'max' => 60),
                array('isPublishNow', 'in', 'range' => array(0, 1)),
                array('publish_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        if (!$ck->isPublishNow && empty($ck->publish_time)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数 no pushlishTime');
        }
        
        $model = new MsgInfo();
        $ck->setModelAttris($model);
        $r = $model->add($ck->isPublishNow);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了消息');
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加失败');
    }
    
    
    /**
     * 修改消息
     */
    public function actionUpdateMsg()
    {
        $ck = Rules::instance(
            array(
                'msgId' => Yii::app()->request->getPost('msgId'),
                'title' => Yii::app()->request->getPost('title'),
                'content' => Yii::app()->request->getPost('content'),
                'filter' => Yii::app()->request->getPost('filter'),
            ),
            array(
                array('msgId', 'required'),
                array('msgId', 'numerical', 'integerOnly' => true),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('content', 'CZhEnV', 'min' => 1, 'max' => 120),
                array('filter', 'CZhEnV', 'min' => 1, 'max' => 60),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = MsgInfo::model()->findByPk($ck->msgId);
        $ck->setModelAttris($model);
        $r = $model->updateMsg();
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改了消息');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 查看消息信息
     */
    public function actionGetMsgInfo()
    {
        $ck = Rules::instance(
            array(
                'msgId' => Yii::app()->request->getParam('msgId'),
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
        
        $msg = MsgInfo::model()->getMsg($ck->msgId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('msg' => $msg));
    }
    
    
    /**
     * 获取某个消息的接收用户列表
     */
    public function actionGetMsgRevUsers()
    {
        $ck = Rules::instance(
            array(
                'msg_id' => Yii::app()->request->getParam('msgId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('msg_id, page, size', 'required'),
                array('msg_id, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = MsgRevUserMapAdmin::model()->getMsgRevUsers($ck->msg_id, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取某个用户的消息列表
     */
    public function actionGetUserMsgs()
    {
        $ck = Rules::instance(
            array(
                'u_id' => Yii::app()->request->getParam('uid'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('u_id, page, size', 'required'),
                array('u_id, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = MsgRevUserMapAdmin::model()->getUserMsgs($ck->u_id, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 删除用户消息关联
     */
    public function actionDelUserMsg()
    {
        $ck = Rules::instance(
            array(
                'u_id' => Yii::app()->request->getPost('uid'),
                'msg_id' => Yii::app()->request->getPost('msgId'),
            ),
            array(
                array('u_id, msg_id', 'required'),
                array('u_id, msg_id', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        
        $r = MsgRevUserMap::model()->del($ck->msg_id, $ck->u_id);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了用户消息');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }


    /**
     * 删除消息
     */
    public function actionDelMsg()
    {
        $ck = Rules::instance(
            array(
                'msg_id' => Yii::app()->request->getParam('msgId'),
            ),
            array(
                array('msg_id', 'required'),
                array('msg_id', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = MsgInfo::model()->findByPk($ck->msg_id);
        if (empty($model)) {
            return Yii::app()->res->output(Error::RECORD_NOT_EXIST, '不存在此消息');
        }
        
        $r = $model->del();
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了消息');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }
    
    
    /**
     * 获取消息列表
     */
    public function actionGetMsgs()
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
        
        $rst = MsgInfo::model()->searchMsgs($ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取消息列表（回收站）
     */
    public function actionGetDelMsgs()
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
        
        $rst = MsgInfo::model()->searchMsgs($ck->page, $ck->size, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 添加push类型
     */
    public function actionAddPushType()
    {
        $ck = Rules::instance(
            array(
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('name', 'required'),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new PushMsgType();
        $ck->setModelAttris($model);
        $r = $model->add();
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了push类型');
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加失败');
    }
    
    
    /**
     * 修改push类型
     */
    public function actionUpdatePushType()
    {
        $ck = Rules::instance(
            array(
                'pushTypeId' => Yii::app()->request->getPost('pushTypeId'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('pushTypeId', 'required'),
                array('pushTypeId', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = PushMsgType::model()->findByPk($ck->pushTypeId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '不存在此类型');
        }
        
        $ck->setModelAttris($model);
        
        $r = $model->updateType();
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改了push类型');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 删除push类型
     */
    public function actionDelPushType() 
    {
        $ck = Rules::instance(
            array(
                'pushTypeId' => Yii::app()->request->getPost('pushTypeId'),
            ),
            array(
                array('pushTypeId', 'required'),
                array('pushTypeId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = PushMsgType::model()->findByPk($ck->pushTypeId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '不存在此类型');
        }
        
        $r = $model->del();
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了push类型');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }

    
    /**
     * 获取push类型
     */
    public function actionGetPushType()
    {
        $ck = Rules::instance(
            array(
                'pushTypeId' => Yii::app()->request->getPost('pushTypeId'),
            ),
            array(
                array('pushTypeId', 'required'),
                array('pushTypeId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $pushMsgType = PushMsgType::model()->getType($ck->pushTypeId);
        
        return Yii::app()->res->output(Error::NONE, '获取成功', array('type' => $pushMsgType));
    }
    
    
    /**
     * 获取push类型列表
     */
    public function actionGetPushTypes() 
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
            ),
            array(
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = PushMsgType::model()->searchTypes($ck->keyWords);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取push类型列表（回收站）
     */
    public function actionGetDelPushTypes() 
    {
        $ck = Rules::instance(
            array(
                'keyWords' => Yii::app()->request->getParam('keyWords'),
            ),
            array(
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = PushMsgType::model()->searchTypes($ck->keyWords, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 添加push消息
     */
	public function actionAddPush()
	{
        $ck = Rules::instance(
            array(
                'send_type' => Yii::app()->request->getPost('sendType'),
                'recvArr' => Yii::app()->request->getPost('recvArr'),
                'type_id' => Yii::app()->request->getPost('typeId'),
                'title' => Yii::app()->request->getPost('title'),
                'text' => Yii::app()->request->getPost('text'),
                'url' => Yii::app()->request->getPost('url'),
                'filter' => Yii::app()->request->getPost('filter'),
                'publish_time' => Yii::app()->request->getPost('publishTime'),
                'isSendNow' => Yii::app()->request->getPost('isSendNow', 0),
            ),
            array(
                array('send_type, type_id, isSendNow', 'required'),
                array('send_type', 'numerical', 'integerOnly' => true),
                array('recvArr', 'CArrNumV', 'maxLen' => 512),
                array('type_id', 'numerical', 'integerOnly' => true),
                array('title', 'CZhEnV', 'max' => 16),
                array('text, filter', 'CZhEnV', 'max' => 32),
                array('url', 'CZhEnV', 'max' => 120),
                array('publish_time', 'date', 'format' => 'yyyy-mm-dd hh:ii:ss'),
                array('isSendNow', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new PushMsgInfo();
        $ck->setModelAttris($model);
        $model->recv = json_encode($ck->recvArr);
        
        $r = $model->add($ck->isSendNow);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了push消息');
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加失败');
	}
    
    
    /**
     * 获取push消息列表
     */
    public function actionGetPushs()
    {
        $ck = Rules::instance(
            array(
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = PushMsgInfo::model()->searchPushs($ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
}