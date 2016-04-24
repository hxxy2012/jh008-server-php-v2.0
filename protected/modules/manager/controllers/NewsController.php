<?php
class NewsController extends ManagerController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + add, update, dealAct, dealVip, updateHomeAdverts', // we only allow deletion via POST request
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
     * 资讯搜索
     */
    public function actionNews()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'typeId' => Yii::app()->request->getParam('typeId'),
                'tagId' => Yii::app()->request->getParam('tagId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, typeId, page, size', 'required'),
                array('cityId, typeId, tagId, page, size', 'numerical', 'integerOnly' => true),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsInfoM::model()->newsM($ck->cityId, $ck->typeId, $ck->tagId, $ck->keyWords, $ck->page, $ck->size);
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
        
        $rst = NewsInfoM::model()->fullProfileM(NULL, $ck->newsId);
        Yii::app()->res->output(Error::NONE, 'news info success', $rst);
    }
    
    
    /**
     * 资讯收藏者列表
     */
    public function actionLovs()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getParam('newsId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('newsId, cityId, page, size', 'required'),
                array('newsId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsLovUserMapM::model()->lovUsersM($ck->newsId, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'news lovs success', $rst);
    }
    
    
    /**
     * 资讯分享者列表
     */
    public function actionShares()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getParam('newsId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('newsId, cityId, page, size', 'required'),
                array('newsId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsShareM::model()->shareUsersM($ck->newsId, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'news shares success', $rst);
    }
    
    
    /**
     * 资讯评论列表
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
        
        $rst = NewsCommentM::model()->commentsM($ck->newsId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'news comments success', $rst);
    }
    
    
    /**
     * 资讯相关活动列表
     */
    public function actionActs()
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
        
        $rst = NewsActMapM::model()->actsM($ck->newsId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'news acts success', $rst);
    }
    
    
    /**
     * 资讯相关达人
     */
    public function actionVips()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getParam('newsId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('newsId, cityId, page, size', 'required'),
                array('newsId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsVipMapM::model()->vipsM($ck->newsId, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'news vips success', $rst);
    }
    
    
    /**
     * 首页轮播列表
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
        
        $rst = KeyValInfoM::model()->getHomeAdvertsM($ck->cityId, $ck->page ,$ck->size);
        Yii::app()->res->output(Error::NONE, 'home adverts success', $rst);
    }
    
    
    /**
     * 添加资讯
     */
    public function actionAdd()
    {
        $ck = Rules::instance(
            array(
                'title' => Yii::app()->request->getPost('title'),
                'intro' => Yii::app()->request->getPost('intro'),
                'detail' => Yii::app()->request->getPost('detail'),
                'city_id' => Yii::app()->request->getPost('cityId'),
                'type_id' => Yii::app()->request->getPost('typeId'),
                'price' => Yii::app()->request->getPost('price'),
                'tag_id' => Yii::app()->request->getPost('tagId'),
                'img_id' => Yii::app()->request->getPost('hImgId'),
                'lov_base_num' => Yii::app()->request->getPost('lovBaseNum'),
                'share_base_num' => Yii::app()->request->getPost('shareBaseNum'),
            ),
            array(
                array('city_id, type_id', 'required'),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 32, 'isDiff' => TRUE),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 64),
                array('city_id, type_id, tag_id, img_id, lov_base_num, share_base_num', 'numerical', 'integerOnly' => TRUE),
                array('price', 'numerical', 'integerOnly' => FALSE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new NewsInfo();
        $ck->setModelAttris($model);
        $rst = NewsInfoM::model()->addM($model, $ck->price);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news add success', array('news_id' => $model->id));
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news add fail' . json_encode($model->getErrors()));
    }
    
    
    /**
     * 修改资讯
     */
    public function actionUpdate()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getPost('newsId'),
                'title' => Yii::app()->request->getPost('title'),
                'intro' => Yii::app()->request->getPost('intro'),
                'detail' => Yii::app()->request->getPost('detail'),
                'city_id' => Yii::app()->request->getPost('cityId'),
                'type_id' => Yii::app()->request->getPost('typeId'),
                'price' => Yii::app()->request->getPost('price'),
                'tag_id' => Yii::app()->request->getPost('tagId'),
                'img_id' => Yii::app()->request->getPost('hImgId'),
                'lov_base_num' => Yii::app()->request->getPost('lovBaseNum'),
                'share_base_num' => Yii::app()->request->getPost('shareBaseNum'),
            ),
            array(
                array('newsId', 'required'),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 32, 'isDiff' => TRUE),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 64),
                array('newsId, city_id, type_id, tag_id, img_id, lov_base_num, share_base_num', 'numerical', 'integerOnly' => TRUE),
                array('price', 'numerical', 'integerOnly' => FALSE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = NewsInfo::model()->findByPk($ck->newsId);
        if (empty($model)){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'news is not exist');
        }
        
        $ck->setModelAttris($model);
        $rst = NewsInfoM::model()->updateM($model, $ck->price);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news update fail' . json_encode($model->getErrors()));
    }
    
    
    /**
     * 修改资讯状态
     */
    public function actionUpdateStatus() 
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getPost('newsId'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('newsId, status', 'required'),
                array('newsId', 'numerical', 'integerOnly' => true),
                array('status', 'in', 'range' => array(
                    ConstStatus::DELETE,
                    ConstActStatus::NOT_COMMIT,
                    ConstActStatus::CHECK_WAITING,
                    ConstActStatus::CHECKING,
                    ConstActStatus::NOT_PASS,
                    ConstActStatus::NOT_PUBLISH,
                    ConstActStatus::PUBLISHING,
                    ConstActStatus::OFF_PUBLISH,
                    )),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = NewsInfoM::model()->upNewsStatusM(NULL, $ck->newsId, $ck->status);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news status update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news status update fail');
    }
    
    
    /**
     * 更新资讯的活动关联
     */
    public function actionDealAct()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getPost('newsId'),
                'actId' => Yii::app()->request->getPost('actId'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('newsId, actId, status', 'required'),
                array('newsId, actId', 'numerical', 'integerOnly' => TRUE),
                array('status', 'in', 'range' => array(
                    ConstStatus::DELETE,
                    ConstStatus::NORMAL,
                    )),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        if (ConstStatus::DELETE == $ck->status) {
            $rst = NewsActMapM::model()->delM($ck->newsId, $ck->actId);
        }  else {
            $rst = NewsActMapM::model()->updateM(NULL, $ck->newsId, $ck->actId);
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news deal act success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news deal act fail');
    }
    
    
    /**
     * 更新资讯的达人关联
     */
    public function actionDealVip()
    {
        $ck = Rules::instance(
            array(
                'newsId' => Yii::app()->request->getPost('newsId'),
                'vipId' => Yii::app()->request->getPost('vipId'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('newsId, vipId, status', 'required'),
                array('newsId, vipId', 'numerical', 'integerOnly' => TRUE),
                array('status', 'in', 'range' => array(
                    ConstStatus::DELETE,
                    ConstStatus::NORMAL,
                    )),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        if (ConstStatus::DELETE == $ck->status) {
            $rst = NewsVipMapM::model()->delM($ck->newsId, $ck->vipId);
        }  else {
            $rst = NewsVipMapM::model()->updateM(NULL, $ck->newsId, $ck->vipId);
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news deal vip success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news deal vip fail');
    }
    
    
    /**
     * 修改首页轮播
     */
    public function actionUpdateHomeAdverts()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getPost('cityId'),
                'newsIds' => Yii::app()->request->getPost('newsIds', array()),
            ),
            array(
                array('cityId', 'required'),
                array('cityId', 'numerical', 'integerOnly' => TRUE),
                array('newsIds', 'CArrNumV', 'maxLen' => 120),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = KeyValInfoM::model()->upHomeAdvertsM($ck->cityId, $ck->newsIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'news update home adverts success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'news update home adverts fail');
    }
    
}
