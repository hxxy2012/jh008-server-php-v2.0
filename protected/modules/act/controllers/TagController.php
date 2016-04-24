<?php

class TagController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + ', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('actTags', 'userTags'),
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
     * 活动的分类
     */
    public function actionActTags()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = array();
        if (empty($ck->cityId)) {
            $rst = ActTag::model()->tags($ck->page, $ck->size);
        }  else {
            $rst = CityActTagMap::model()->tags($ck->cityId, $ck->page, $ck->size);
        }
        
        Yii::app()->res->output(Error::NONE, 'act tags success', $rst);
    }
    
    
    /**
     * 用户的标签
     */
    public function actionUserTags()
    {
        $ck = Rules::instance(
            array(
                'type' => Yii::app()->request->getParam('type', 1),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('type, page, size', 'required'),
                array('type', 'in', 'range' => array(1, 2)),
                array('page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = array();
        if (1 == $ck->type) {
            $rst = VipTag::model()->tags($ck->page, $ck->size);
        }  else {
            $rst = UserTag::model()->tags($ck->page, $ck->size);
        }
        Yii::app()->res->output(Error::NONE, 'user tags success', $rst);
    }
    
}
