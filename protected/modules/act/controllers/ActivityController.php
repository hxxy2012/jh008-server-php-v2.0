<?php

class ActivityController extends ActController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + share, checkin, lov, delLov, enroll, comment, delComment', // we only allow deletion via POST request
		);
	}

    
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions' => array('acts', 'act', 'recommendActs', 'lovActs', 'enrollActs', 'checkinActs', 'commentActs', 'comments', 'listNews', 'listVips', 'shareweb'),
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
     * 活动搜索
     */
    public function actionActs()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'tagId' => Yii::app()->request->getParam('tagId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'startTime' => Yii::app()->request->getParam('startTime'),
                'endTime' => Yii::app()->request->getParam('endTime'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, tagId, page, size', 'numerical', 'integerOnly' => true),
                array('startTime, endTime', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $rst = ActInfo::model()->acts($ck->cityId, $ck->tagId, $ck->startTime, $ck->endTime, $ck->keyWords, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'acts success', $rst);
    }
    
    
    /**
     * 活动详情
     */
    public function actionAct()
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

        $act = ActInfo::model()->fullProfile(NULL, $ck->actId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'act success', array('act' => $act));
    }
    
    
    /**
     * 推荐的活动
     */
    public function actionRecommendActs()
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

        $rst = KeyValInfo::model()->getRecommendActs($ck->cityId, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'recommend acts success', $rst);
    }
    
    
    /**
     * 收藏的活动
     */
    public function actionLovActs()
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
        
        $rst = ActLovUserMap::model()->acts($ck->uid, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'lov acts success', $rst);
    }
    
    
    /**
     * 报名的活动
     */
    public function actionEnrollActs()
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
        
        $rst = ActEnrollOld::model()->userEnrolls($ck->uid, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'enroll acts success', $rst);
    }
    
    
    /**
     * 签到的活动
     */
    public function actionCheckinActs()
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
        
        $rst = ActCheckin::model()->userCheckins($ck->uid, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'checkin acts success', $rst);
    }
    
    
    /**
     * 评论过的活动
     */
    public function actionCommentActs()
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
        
        $rst = ActComment::model()->acts($ck->uid, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'comment acts success', $rst);
    }
    
    
    /**
     * 活动的评论
     */
    public function actionComments()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActComment::model()->comments($ck->actId, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'act comments success', $rst);
    }
    
    
    /**
     * 相关资讯
     */
    public function actionListNews()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'typeId' => Yii::app()->request->getParam('typeId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, typeId, page, size', 'required'),
                array('actId, typeId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActNewsMap::model()->news($ck->actId, $ck->typeId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act news success', $rst);
    }
    
    
    /**
     * 相关达人
     */
    public function actionListVips()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'actId' => Yii::app()->request->getParam('actId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, actId, page, size', 'required'),
                array('cityId, actId, page, size', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActVipMap::model()->vips($ck->cityId, $ck->actId, $ck->page, $ck->size, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id);
        Yii::app()->res->output(Error::NONE, 'act vips success', $rst);
    }
    
    
    /**
     * 分享
     */
    public function actionShare()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'shareType' => Yii::app()->request->getParam('shareType'),
            ),
            array(
                array('actId, shareType', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
                array('shareType', 'in', 'range' => array(1, 2, 3, 4)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActShare::model()->addShare($ck->actId, Yii::app()->user->isGuest ? NULL : Yii::app()->user->id, $ck->shareType);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act share success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act share fail');
    }
    
    
    /**
     * 活动签到
     */
    public function actionCheckin()
    {
        $ck = Rules::instance(
            array(
                'act_id' => Yii::app()->request->getPost('actId'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
            ),
            array(
                array('act_id', 'required'),
                array('act_id', 'numerical', 'integerOnly' => true),
                array('lon, lat', 'numerical'),
                array('address', 'CZhEnV', 'max' => 32),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        if (ActCheckin::model()->check($ck->act_id, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::RECORD_HAS_EXIST, 'act checkin has exist');
        }
        
        $model = new ActCheckin();
        $ck->setModelAttris($model);
        $model->u_id = Yii::app()->user->id;
        
        $rst = ActCheckin::model()->add($model);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act checkin success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act checkin fail');
    }


    /**
     * 收藏
     */
    public function actionLov()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
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
        
        $rst = ActLovUserMap::model()->addLove($ck->actId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act lov success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act lov fail');
    }
    
    
    /**
     * 取消收藏
     */
    public function actionDelLov()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
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
        
        $rst = ActLovUserMap::model()->delLove($ck->actId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act del lov success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act del lov fail');
    }
    
    
    /**
     * 报名
     * version：2.0有效
     */
    public function actionEnroll()
    {
        $ck = Rules::instance(
            array(
                'act_id' => Yii::app()->request->getPost('actId'),
                'name' => Yii::app()->request->getPost('name'),
                'phone' => Yii::app()->request->getPost('phone'),
                'people_num' => Yii::app()->request->getPost('peopleNum'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
            ),
            array(
                array('act_id', 'required'),
                array('act_id, people_num', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'max' => 16),
                array('phone', 'length', 'max' => 32),
                array('lon, lat', 'numerical'),
                array('address', 'CZhEnV', 'max' => 64),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }

        $ret = ActEnrollOld::model()->checkEnroll($ck->act_id, Yii::app()->user->id);
        if ($ret) {
            return Yii::app()->res->output(Error::RECORD_HAS_EXIST, 'act enroll has exist');
        }
        
        $model = new ActEnrollOld();
        $ck->setModelAttris($model);
        $model->u_id = Yii::app()->user->id;
        
        $rst = ActEnrollOld::model()->add($model);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act enroll success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act enroll fail');
        
        //2.0版本报名禁用
        //Yii::app()->res->output(Error::VERSION_TOO_LOW, 'interface forbidden');
    }
    
    
    /**
     * 评论
     */
    public function actionComment()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'content' => Yii::app()->request->getPost('content'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
                array('content', 'CZhEnV', 'max' => 120),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActComment::model()->add($ck->actId, Yii::app()->user->id, $ck->content);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act comment success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act comment fail');
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
        
        $rst = ActComment::model()->del($ck->commentId, Yii::app()->user->id);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act del comment success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act del comment fail');
    }
    
    
    /**
     * 活动爆料
     */
    public function actionBrokeNews()
    {
        $ck = Rules::instance(
            array(
                'city_id' => Yii::app()->request->getPost('cityId'),
                'contact_phone' => Yii::app()->request->getPost('contactPhone'),
                'contact_address' => Yii::app()->request->getPost('contactAddress'),
                'intro' => Yii::app()->request->getPost('intro'),
                'imgIds' => Yii::app()->request->getPost('imgIds', array()),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'address' => Yii::app()->request->getPost('address'),
            ),
            array(
                array('city_id', 'numerical', 'integerOnly' => true),
                array('contact_phone, contact_address, address', 'length', 'max' => 64),
                array('intro', 'length', 'max' => 256),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new BrokeNews();
        $ck->setModelAttris($model);
        $rst = BrokeNews::model()->add($model, $ck->imgIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'user broke news success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'user broke news fail');
    }


    /**
     * 查看活动的分享网页
     */
    public function actionShareweb()
    {
        $this->layout = '//layouts/blank';
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, "Invalid");
            //throw new CHttpException(404, '未找到活动');
        }
        
        $rst = array();
        $act = ActInfo::model()->findByPk($ck->actId);
        if (empty($act)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'Not found');
            //throw new CHttpException(404, '未找到活动内容');
        }
        
        if (ConstActStatus::PUBLISHING != $act->status) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'act has undercarriage');
        }
        
        $rst['id'] = $act->id;
        $rst['title'] = $act->title;
        $rst['intro'] = $act->intro;
        $rst['lon'] = $act->lon;
        $rst['lat'] = $act->lat;
        $rst['addr_city'] = $act->addr_city;
        $rst['addr_area'] = $act->addr_area;
        $rst['addr_road'] = $act->addr_road;
        $rst['addr_num'] = $act->addr_num;
        $rst['addr_name'] = $act->addr_name;
        $rst['b_time'] = $act->b_time;
        $rst['e_time'] = $act->e_time;
        $rst['detail'] = $act->detail;
        
        $img = ImgInfo::model()->profile($act->h_img_id);
        $rst['h_img_url'] = empty($img) ? NULL : $img['img_url'];
        
        $extend = ActInfoExtend::model()->get($ck->actId) ;
        //报名时间
        $rst['enroll_b_time'] = empty($extend) ? $act->b_time : $extend->enroll_b_time;
        $rst['enroll_e_time'] = empty($extend) ? $act->e_time : $extend->enroll_e_time;
        //剩余多少秒
        $rst['enroll_rest_sec'] = strtotime($rst['enroll_e_time']) - time();
        if ($rst['enroll_rest_sec'] < 0) {
            $rst['enroll_rest_sec'] = 0;
        }
        //费用
        $rst['cost'] = 0;
        if (!empty($extend) && !empty($extend->product_id)) {
            $rst['cost'] = Product::model()->price($extend->product_id);
        }
        //已报名人数
        $rst['enroll_num'] = ActEnroll::model()->enrollPeopleNum($ck->actId);
        //评论数和前三条评论
        $comments = ActComment::model()->comments($ck->actId, 1, 3, NULL);
        $rst['comment_num'] = (isset($comments) && isset($comments['total_num'])) ? $comments['total_num'] : 0;
        $rst['comments'] = (isset($comments) && isset($comments['comments'])) ? $comments['comments'] : array();
        
        $this->render('shareweb', array('act' => $rst));
    }
    
}
