<?php

class DynamicController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + add, delete, addComment, delComment', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('list', 'friends', 'photos', 'profile'),
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
     * 用户动态
     */
    public function actionList()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
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
        
        $rst = UserDynamic::model()->dynamics($ck->uid, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'dynamic list success', $rst);
    }
    
    
    /**
     * 好友动态
     */
    public function actionFriends()
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
        
        $rst = array();
        if (Yii::app()->user->isGuest) {
            $rst = KeyValInfo::model()->getCityVipRandomDynamics($ck->cityId, $ck->page, $ck->size, NULL);
        }  else {
            $rst = FriendDynamic::model()->dynamics(Yii::app()->user->id, $ck->cityId, $ck->page, $ck->size);
        }
        Yii::app()->res->output(Error::NONE, 'friend dynamics success', $rst);
    }
    
    
    /**
     * 动态图片
     */
    public function actionPhotos()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
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
        
        $rst = DynamicImgMap::model()->userImgs($ck->uid, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'dynamic photos success', $rst);
    }
    
    
    /**
     * 用户动态详情
     */
    public function actionProfile()
    {
        $ck = Rules::instance(
            array(
                'dynamicId' => Yii::app()->request->getParam('dynamicId'),
            ),
            array(
                array('dynamicId', 'required'),
                array('dynamicId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserDynamic::model()->dynamic($ck->dynamicId);
        Yii::app()->res->output(Error::NONE, 'dynamic profile success', array('dynamic' => $rst));
    }
    
    
    /**
     * 发动态
     */
    public function actionAdd()
    {
        $ck = Rules::instance(
            array(
                'content' => Yii::app()->request->getPost('content'),
                'imgIds' => Yii::app()->request->getPost('imgIds', array()),
            ),
            array(
                array('content', 'CZhEnV', 'min' => 1, 'max' => 240),
                array('imgIds', 'CArrNumV', 'maxLen' => 9),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserDynamic::model()->add(Yii::app()->user->id, $ck->content, $ck->imgIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'add user dynamic success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'add user dynamic fail');
    }
    
    
    /**
     * 删除动态
     */
    public function actionDelete()
    {
        $ck = Rules::instance(
            array(
                'dynamicId' => Yii::app()->request->getPost('dynamicId'),
            ),
            array(
                array('dynamicId', 'required'),
                array('dynamicId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = UserDynamic::model()->del($ck->dynamicId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'delete user dynamic success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'delete user dynamic fail');
    }
    
    
    /**
     * 动态的评论
     */
    public function actionComments()
    {
        $ck = Rules::instance(
            array(
                'dynamicId' => Yii::app()->request->getParam('dynamicId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('dynamicId, page, size', 'required'),
                array('dynamicId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = DynamicComment::model()->comments($ck->dynamicId, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'dynamic comments success', $rst);
    }


    /**
     * 发动态的评论
     */
    public function actionAddComment()
    {
        $ck = Rules::instance(
            array(
                'dynamic_id' => Yii::app()->request->getPost('dynamicId'),
                'at_id' => Yii::app()->request->getPost('atId'),
                'content' => Yii::app()->request->getPost('content'),
            ),
            array(
                array('dynamic_id', 'required'),
                array('dynamic_id, at_id', 'numerical', 'integerOnly' => TRUE),
                array('content', 'CZhEnV', 'min' => 1, 'max' => 120),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = DynamicComment::model()->add(
                $ck->dynamic_id, 
                Yii::app()->user->id, 
                $ck->at_id,
                $ck->content
                );
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'add dynamic comment success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'add dynamic comment fail');
    }
    
    
    /**
     * 删除动态的评论
     */
    public function actionDelComment()
    {
        $ck = Rules::instance(
            array(
                'commentId' => Yii::app()->request->getPost('commentId'),
            ),
            array(
                array('commentId', 'required'),
                array('commentId', 'numerical', 'integerOnly' => TRUE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = DynamicComment::model()->del($ck->commentId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'delete dynamic comment success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'delete dynamic comment fail');
    }
    
}
