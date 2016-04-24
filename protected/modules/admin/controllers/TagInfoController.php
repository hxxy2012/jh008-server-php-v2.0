<?php

class TagInfoController extends AdminController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + addTag, updateTag, delTag', // we only allow deletion via POST request
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
     * 添加标签
     */
    public function actionAddTag()
    {
        $ck = Rules::instance(
            array(
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('name', 'required'),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 8),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new TagInfo();
        $ck->setModelAttris($model);
        $r = TagInfo::model()->add($model);
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '添加了标签');
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加失败');
    }
    
    
    /**
     * 修改标签
     */
    public function actionUpdateTag() 
    {
        $ck = Rules::instance(
            array(
                'tagId' => Yii::app()->request->getPost('tagId'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('tagId', 'required'),
                array('tagId', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 8),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = TagInfo::model()->findByPk($ck->tagId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '不存在此标签');
        }
        
        $ck->setModelAttris($model);
        
        $r = TagInfo::model()->updateTag($model);
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '修改了标签');
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改失败');
    }

    
    /**
     * 删除标签
     */
    public function actionDelTag()
    {
        $ck = Rules::instance(
            array(
                'tagId' => Yii::app()->request->getPost('tagId'),
            ),
            array(
                array('tagId', 'required'),
                array('tagId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = TagInfo::model()->findByPk($ck->tagId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '不存在此标签');
        }
        
        $r = TagInfo::model()->del($ck->tagId);
        
        if ($r) {
            AdminOperateLog::model()->log(Yii::app()->user->id, '删除了标签');
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }

    
    /**
     * 获取标签列表
     */
    public function actionGetTags()
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
        
        $rst = TagInfoAdmin::model()->searchTags($ck->keyWords);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 获取标签列表（回收站）
     */
    public function actionGetDelTags()
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
        
        $rst = TagInfoAdmin::model()->searchTags($ck->keyWords, TRUE);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
}