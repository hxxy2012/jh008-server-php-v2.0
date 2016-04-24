<?php

class NewsController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + share, lov, delLov, comment, delComment', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('news', 'newsInfo', 'lovNews', 'commentNews', 'comments', 'listVips', 'homeAdverts', 'detailweb'),
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
     * 资讯搜索
     */
    public function actionNews()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'tagId' => Yii::app()->request->getParam('tagId'),
                'typeId' => Yii::app()->request->getParam('typeId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 10),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, tagId, typeId, page, size', 'numerical', 'integerOnly' => true),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = NewsInfo::model()->news($ck->cityId, $ck->typeId, $ck->tagId, $ck->keyWords, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'news success', $rst);
    }
    
    
    /**
     * 资讯详情
     */
    public function actionNewsInfo()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getParam('newsId'),
            ),
            array(
                array('newsId', 'required'),
                array('newsId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $news = NewsInfo::model()->fullProfile(NULL, $ck->newsId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'news info success', array('news' => $news));
    }
    
    
    /**
     * 收藏的资讯
     */
    public function actionLovNews()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'typeId' => Yii::app()->request->getParam('typeId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('uid, page, size', 'required'),
                array('uid, typeId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsLovUserMap::model()->news($ck->uid, $ck->typeId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'lov news success', $rst);
    }
    
    
    /**
     * 评论过的资讯
     */
    public function actionCommentNews()
    {
        $ck = Rules::instance(
            array(
                'uid' => Yii::app()->request->getParam('uid', Yii::app()->user->id),
                'typeId' => Yii::app()->request->getParam('typeId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('uid, typeId, page, size', 'required'),
                array('uid, typeId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsComment::model()->news($ck->uid, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'comment news success', $rst);
    }
    
    
    /**
     * 资讯的评论
     */
    public function actionComments()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getParam('newsId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('newsId, page, size', 'required'),
                array('newsId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsComment::model()->comments($ck->newsId, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'news comments success', $rst);
    }
    
    
    /**
     * 相关活动
     */
    public function actionListActs()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getParam('newsId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('newsId, page, size', 'required'),
                array('newsId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsActMap::model()->acts($ck->newsId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'news acts success', $rst);
    }
    
    
    /**
     * 相关达人
     */
    public function actionListVips()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'newsId' => Yii::app()->request->getParam('newsId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, newsId, page, size', 'required'),
                array('cityId, newsId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsVipMap::model()->vips($ck->cityId, $ck->newsId, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'news vips success', $rst);
    }
    
    
    /**
     * 分享
     */
    public function actionShare()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getParam('newsId'),
                'shareType' => Yii::app()->request->getParam('shareType'),
            ),
            array(
                array('newsId, shareType', 'required'),
                array('newsId', 'numerical', 'integerOnly' => true),
                array('shareType', 'in', 'range' => array(1, 2, 3, 4)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsShare::model()->addShare($ck->newsId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id, $ck->shareType);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news share success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news share fail');
    }
    
    
    /**
     * 收藏
     */
    public function actionLov()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getPost('newsId'),
            ),
            array(
                array('newsId', 'required'),
                array('newsId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsLovUserMap::model()->addLove($ck->newsId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news lov success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news lov fail');
    }
    
    
    /**
     * 取消收藏
     */
    public function actionDelLov()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getPost('newsId'),
            ),
            array(
                array('newsId', 'required'),
                array('newsId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsLovUserMap::model()->delLove($ck->newsId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news del lov success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news del lov fail');
    }
    
    
    /**
     * 评论
     */
    public function actionComment()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getPost('newsId'),
                'content' => Yii::app()->request->getPost('content'),
            ),
            array(
                array('newsId', 'required'),
                array('newsId', 'numerical', 'integerOnly' => true),
                array('content', 'CZhEnV', 'max' => 120),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsComment::model()->add($ck->newsId, Yii::app()->user->id, $ck->content);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news comment success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news comment fail');
    }
    
    
    /**
     * 删除评论
     */
    public function actionDelComment()
    {
        $ck = Rules::instance(
            array(
                'commentId' => Yii::app()->request->getPost('commentId'),
            ),
            array(
                array('commentId', 'required'),
                array('commentId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsComment::model()->del($ck->commentId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news del comment success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news del comment fail');
    }
    
    
    /**
     * 获取首页轮播
     */
    public function actionHomeAdverts()
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

        $rst = KeyValInfo::model()->getHomeAdverts($ck->cityId, $ck->page ,$ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'home adverts success', $rst);
    }
    
    
    /**
     * 查看资讯详情的网页
     */
    public function actionDetailweb()
    {
        $this->layout = '//layouts/blank';
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getParam('newsId'),
            ),
            array(
                array('newsId', 'required'),
                array('newsId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, "Invalid");
            //throw new CHttpException(404, '未找到活动');
        }
        
        //$model = NewsInfo::model()->fullProfile(NULL, $ck->newsId);
        $model = NewsInfo::model()->findByPk($ck->newsId);
        if (empty($model)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'Not found');
            //throw new CHttpException(404, '未找到活动内容');
        }
        
        $this->render('detailweb', array('news' => $model));
    }
    
}
