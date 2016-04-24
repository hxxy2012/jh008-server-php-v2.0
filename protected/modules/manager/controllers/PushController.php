<?php
class PushController extends ManagerController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + sendSystemMsg, pushMsg, pushMsgForNews', // we only allow deletion via POST request
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
     * 获取系统消息列表
     */
    public function actionSystemMsgs()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid'),
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 10000),
            ),
            array(
                array('uid, page, size', 'required'),
                array('uid, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = SystemMsgUserMapM::model()->userSystemMsgsM($ck->uid, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'user system msgs success', $rst);
    }
    
    
    /**
     * 给用户发系统消息
     */
    public function actionSendSystemMsg()
    {
        $ck = Rules::instance(
            array(
                'content' => Yii::app()->request->getPost('content'),
                'toUserIds' => Yii::app()->request->getPost('toUserIds', array()),
            ),
            array(
                array('content, toUserIds', 'required'),
                array('content', 'length', 'max' => 256),
                array('toUserIds', 'CArrNumV', 'maxLen' => 120),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = SystemMsgUserTaskM::model()->addM($ck->content, $ck->toUserIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'send system msg success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'send system msg fail');
    }
    
    
    /**
     * 给用户发推送消息
     */
    public function actionPushMsg()
    {
        $ck = Rules::instance(
            array(
                'title' => Yii::app()->request->getPost('title'),
                'descri' => Yii::app()->request->getPost('descri'),
                'sendType' => Yii::app()->request->getPost('sendType'),
                'tag' => Yii::app()->request->getPost('tag'),
                'uid' => Yii::app()->request->getPost('uid'),
            ),
            array(
                array('title, descri, sendType', 'required'),
                array('title', 'length', 'max' => 12),
                array('descri', 'length', 'max' => 64),
                array('sendType', 'in', 'range' => array(1, 2, 3)),
                array('tag', 'length', 'max' => 120),
                array('uid', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $taskSendType = NULL;
        if (1 == $ck->sendType) {
            $taskSendType = ConstPushMsgTaskType::TO_All;
        }elseif (3 == $ck->sendType) {
            $taskSendType = ConstPushMsgTaskType::TO_USER;
        }  else {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'no tag push');
        }
        
        $rst = PushMsgTask::model()->add($ck->sendType, $ck->uid, NULL, $ck->title, $ck->descri, array());
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'push msg success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'push msg fail');
    }
    
    
    /**
     * 给资讯所在城市用户发资讯相关推送消息
     */
    public function actionPushMsgForNews()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getPost('newsId'),
                'title' => Yii::app()->request->getPost('title'),
                'descri' => Yii::app()->request->getPost('descri'),
            ),
            array(
                array('newsId, title, descri', 'required'),
                array('newsId', 'numerical', 'integerOnly' => true),
                array('title', 'length', 'max' => 12),
                array('descri', 'length', 'max' => 64),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = PushMsgTask::model()->add(ConstPushMsgTaskType::TO_NEWS_CITY_ALL, $ck->newsId, NULL, $ck->title, $ck->descri, array());
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'push msg for news success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'push msg for news fail');
    }
    
}
