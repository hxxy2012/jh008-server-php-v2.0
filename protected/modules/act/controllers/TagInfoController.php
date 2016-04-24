<?php

class TagInfoController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + setUTags', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('getSltTags', 'getAllTags', 'getActTags'),
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
     * 设置用户感兴趣的标签
     */
    public function actionSetUTags() 
    {
        $ck = Rules::instance(
            array(
                'tagIds' => Yii::app()->request->getPost('tagIds', array()),
            ),
            array(
                array('tagIds', 'required'),
                array('tagIds', 'CArrNumV', 'maxLen' => 5),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $r = TagUserMap::model()->setLovedTags($ck->tagIds, Yii::app()->user->id);
        if ($r) {
            Yii::app()->res->output(Error::NONE, '设置成功');
        }  else {
            Yii::app()->res->output(Error::OPERATION_EXCEPTION, '设置失败');
        }
    }
    
    
    /**
     * 获取用户感兴趣的标签
     */
    public function actionGetUTags() 
    {
        $tags = TagUserMap::model()->getTags(Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, '获取成功', array('tags' => $tags));
    }

    
    /**
     * 获取可供筛选的标签
     */
    public function actionGetSltTags() 
    {
        $ck = Rules::instance(
            array(
                'isMarkLoved' => Yii::app()->request->getParam('isMarkLoved', 0),
            ),
            array(
                array('isMarkLoved', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        if (Yii::app()->user->isGuest && $ck->isMarkLoved) {
            //return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数 not login but has isMarkLoved');
            $this->redirect(array('user/userInfo/notLogin'));
        }
        
        $tags = TagInfo::model()->getSltTags(
                $ck->isMarkLoved,
                Yii::app()->user->isGuest ? NULL : Yii::app()->user->id
                );
        Yii::app()->res->output(Error::NONE, '获取成功', array('tags' => $tags));
    }
    
    
    /**
     * 获取所有的标签
     */
    public function actionGetAllTags() 
    {
        $ck = Rules::instance(
            array(
                'isMarkLoved' => Yii::app()->request->getParam('isMarkLoved', 0),
            ),
            array(
                array('isMarkLoved', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        if (Yii::app()->user->isGuest && $ck->isMarkLoved) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数 not login but has isMarkLoved');
        }
        
        $tags = TagInfo::model()->getAllTags(
                $ck->isMarkLoved,
                Yii::app()->user->isGuest ? NULL : Yii::app()->user->id
                );
        Yii::app()->res->output(Error::NONE, '获取成功', array('tags' => $tags));
    }
    
    
    /**
     * 获取活动的标签
     */
    public function actionGetActTags()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $tags = ActTagMap::model()->getTags($ck->actId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('tags' => $tags));
    }
    
}
