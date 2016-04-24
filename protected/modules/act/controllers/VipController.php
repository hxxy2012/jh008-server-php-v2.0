<?php

class VipController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + apply', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('users', 'interview'),
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
     * 申请成为达人
     */
    public function actionApply()
    {
        $ck = Rules::instance(
            array(
                'real_name' => Yii::app()->request->getPost('realName'),
                'contact_phone' => Yii::app()->request->getPost('contactPhone'),
                'email' => Yii::app()->request->getPost('email'),
                'intro' => Yii::app()->request->getPost('intro'),
                'cityIds' => Yii::app()->request->getPost('cityIds'),
                'tagIds' => Yii::app()->request->getPost('tagIds', array()),
                'userTagIds' => Yii::app()->request->getPost('userTagIds', array()),
                'imgIds' => Yii::app()->request->getPost('imgIds', array()),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
            ),
            array(
                array('real_name', 'CZhEnV', 'min' => 1, 'max' => 32),
                array('contact_phone', 'length', 'max' => 32),
                array('email', 'email'),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 240),
                array('cityIds', 'CArrNumV', 'minLen' => 1, 'maxLen' => 3),
                array('tagIds', 'CArrNumV', 'minLen' => 1, 'maxLen' => 2),
                array('userTagIds', 'CArrNumV', 'maxLen' => 3),
                array('imgIds', 'CArrNumV', 'minLen' => 1, 'maxLen' => 3),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('address', 'CZhEnV', 'min' => 1, 'max' => 64),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new VipApply();
        $ck->setModelAttris($model);
        $rst = VipApply::model()->add($model, Yii::app()->user->id, $ck->cityIds, $ck->tagIds, $ck->userTagIds, $ck->imgIds);
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'vip apply commit success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'vip apply commit fail');
    }
    
    
    /**
     * vip用户搜索
     */
    public function actionUsers()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'tagId' => Yii::app()->request->getParam('tagId'),
                'sex' => Yii::app()->request->getParam('sex'),
                'userTagId' => Yii::app()->request->getParam('userTagId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, tagId, userTagId, page, size', 'numerical', 'integerOnly' => true),
                array('sex', 'in', 'range' => array(1, 2)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = VipSearch::model()->vips($ck->cityId, $ck->tagId, $ck->sex, $ck->userTagId, $ck->keyWords, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        //分标签取出推荐的达人（最多十个）
        $queue = array();
        if (1 == $ck->page) {
            $tagId = $ck->tagId;
            if (empty($ck->tagId)) {
                $tagId = 0;
            }
            $kvrst = KeyValInfo::model()->getRecommendUsers($ck->cityId, $tagId, 1, 10, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
            $queue = $kvrst['users'];
        }
        $rst['queue'] = $queue;
        Yii::app()->res->output(Error::NONE, 'vips success', $rst);
    }
    
    
    /**
     * 用户专访（只开放vip待定，合并到资讯模块）
     */
    public function actionInterview()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid'),
            ),
            array(
                array('uid', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = VipInterview::model()->news($ck->uid, 1, 12, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'act interview success', $rst);
    }
    
}
