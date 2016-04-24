<?php
class ActController extends ManagerController
{

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + addTag, updateTag, add, update, updateStatus, updateRecommends, dealNews, dealVip, addTag, updateTag', // we only allow deletion via POST request
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
     * 标签分类列表
     */
    public function actionTags()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('page, size', 'required'),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
                array('cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = array();
        if (empty($ck->cityId)) {
            $rst = ActTagM::model()->tagsM($ck->keyWords, $ck->page, $ck->size);
        }  else {
            $rst = CityActTagMapM::model()->tagsM($ck->cityId, $ck->page, $ck->size);
        }
        Yii::app()->res->output(Error::NONE, 'act tags success', $rst);
    }
    
    
    /**
     * 活动搜索
     */
    public function actionActs()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'timeStatus' => Yii::app()->request->getParam('timeStatus'),
                'tagId' => Yii::app()->request->getParam('tagId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, tagId, page, size', 'numerical', 'integerOnly' => true),
                array('timeStatus', 'in', 'range' => array(1, 2, 3, 4)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActInfoM::model()->actsM($ck->cityId, $ck->tagId, $ck->keyWords, $ck->timeStatus, $ck->page, $ck->size);
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
        
        $rst = ActInfoM::model()->fullProfileM(NULL, $ck->actId);
        Yii::app()->res->output(Error::NONE, 'act info success', array('act' => $rst));
    }
    
    
    /**
     * 推荐的活动列表
     */
    public function actionRecommendActs()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getParam('cityId'),
                'timeStatus' => Yii::app()->request->getParam('timeStatus'),
                'tagId' => Yii::app()->request->getParam('tagId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('cityId, page, size', 'required'),
                array('cityId, tagId, page, size', 'numerical', 'integerOnly' => true),
                array('timeStatus', 'in', 'range' => array(1, 2, 3, 4)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = KeyValInfoM::model()->getRecommendActsM($ck->cityId, $ck->timeStatus, $ck->tagId, $ck->keyWords, $ck->page ,$ck->size);
        Yii::app()->res->output(Error::NONE, 'act recommends success', $rst);
    }
    
    
    /**
     * 活动签到列表
     */
    public function actionCheckins()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, cityId, page, size', 'required'),
                array('actId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActCheckinM::model()->checkUsersM($ck->actId, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act checkins success', $rst);
    }
    
    
    /**
     * 活动报名列表
     */
    public function actionEnrolls()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, cityId, page, size', 'required'),
                array('actId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActEnrollM::model()->enrollUsersM($ck->actId, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act enrolls success', $rst);
    }
    
    
    /**
     * 活动收藏者列表
     */
    public function actionLovs()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, cityId, page, size', 'required'),
                array('actId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActLovUserMapM::model()->lovUsersM($ck->actId, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act lovs success', $rst);
    }
    
    
    /**
     * 活动分享者列表
     */
    public function actionShares()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, cityId, page, size', 'required'),
                array('actId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActShareM::model()->shareUsersM($ck->actId, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act shares success', $rst);
    }
    
    
    /**
     * 活动评论列表
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
        
        $rst = ActCommentM::model()->commentsM($ck->actId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act comments success', $rst);
    }
    
    
    /**
     * 活动相关资讯列表
     */
    public function actionNews()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'typeId' => Yii::app()->request->getParam('typeId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, page, size', 'required'),
                array('actId, typeId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActNewsMapM::model()->newsM($ck->actId, $ck->typeId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act news success', $rst);
    }
    
    
    /**
     * 活动相关达人
     */
    public function actionVips()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'cityId' => Yii::app()->request->getParam('cityId'),
                'page' => Yii::app()->request->getParam('page'),
                'size' => Yii::app()->request->getParam('size'),
            ),
            array(
                array('actId, cityId, page, size', 'required'),
                array('actId, cityId, page, size', 'numerical', 'integerOnly' => true),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = ActVipMapM::model()->vipsM($ck->actId, $ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'act vips success', $rst);
    }
    
    
    /**
     * 获取爆料
     */
    public function actionBrokeNews()
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
        
        $rst = BrokeNewsM::model()->brokeNewsListM($ck->cityId, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, 'broke news success', $rst);
    }
    
    
    /**
     * 添加标签分类
     */
    public function actionAddTag()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getPost('cityId'),
                'name' => Yii::app()->request->getPost('name'),
            ),
            array(
                array('name', 'required'),
                array('cityId', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 8),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = FALSE;
        if (empty($ck->cityId)) {
            $rst = ActTagM::model()->addM(NULL, $ck->name);
        }  else {
            $rst = CityActTagMapM::model()->addM($ck->cityId, $ck->name);
        }
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'add tag success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'add tag fail');
    }
    
    
    /**
     * 修改标签分类
     */
    public function actionUpdateTag() 
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getPost('cityId'),
                'tagId' => Yii::app()->request->getPost('tagId'),
                'name' => Yii::app()->request->getPost('name'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('tagId', 'required'),
                array('cityId, tagId', 'numerical', 'integerOnly' => true),
                array('name', 'CZhEnV', 'min' => 1, 'max' => 8),
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
            //删除标签
            if (empty($ck->cityId)) {
                $rst = ActTagM::model()->delM($ck->tagId);
            }  else {
                $rst = CityActTagMapM::model()->delM($ck->cityId, $ck->tagId);
            }
        }  else {
            //修改标签
            $rst = ActTagM::model()->updateM(NULL, $ck->tagId, $ck->name);
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'update tag success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'update tag fail');
    }
    
    
    /**
     * 添加活动
     */
    public function actionAdd()
    {
        $ck = Rules::instance(
            array(
                'title' => Yii::app()->request->getPost('title'),
                'intro' => Yii::app()->request->getPost('intro'),
                'cost' => Yii::app()->request->getPost('cost'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'addr_city' => Yii::app()->request->getPost('addrCity'),
                'addr_area' => Yii::app()->request->getPost('addrArea'),
                'addr_road' => Yii::app()->request->getPost('addrRoad'),
                'addr_num' => Yii::app()->request->getPost('addrNum'),
                'addr_name' => Yii::app()->request->getPost('addrName'),
                'addr_route' => Yii::app()->request->getPost('addrRoute'),
                'contact_way' => Yii::app()->request->getPost('contactWay'),
                'b_time' => Yii::app()->request->getPost('bTime'),
                'e_time' => Yii::app()->request->getPost('eTime'),
                't_status_rule' => Yii::app()->request->getPost('tStatusRule'),
                'weekRules' => Yii::app()->request->getPost('weekRules'),
                'detail' => Yii::app()->request->getPost('detail'),
                'can_enroll' => Yii::app()->request->getPost('canEnroll'),
                'city_id' => Yii::app()->request->getPost('cityId'),
                'tag_id' => Yii::app()->request->getPost('tagId'),
                'h_img_id' => Yii::app()->request->getPost('headImgId'),
                'imgIds' => Yii::app()->request->getPost('imgIds'),
                'lov_base_num' => Yii::app()->request->getPost('lovBaseNum'),
                'share_base_num' => Yii::app()->request->getPost('shareBaseNum'),
            ),
            array(
                array('city_id, tag_id', 'required'),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 64, 'isDiff' => TRUE),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 256),
                array('cost, lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('addr_city, addr_area, addr_road', 'CZhEnV', 'min' => 1, 'max' => 12),
                array('addr_num, addr_name', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('addr_route', 'CZhEnV', 'min' => 1, 'max' => 120),
                array('contact_way', 'length', 'max' => 48),
                array('b_time, e_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('t_status_rule, can_enroll', 'in', 'range' => array(0, 1)),
                array('weekRules', 'CArrNumV', 'maxLen' => 7, 'minNum' => 0, 'maxNum' => 6),
                //array('detail', 'CZhEnV', 'min' => 1, 'max' => 512),
                array('city_id, tag_id, h_img_id, lov_base_num, share_base_num', 'numerical', 'integerOnly' => TRUE),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = new ActInfo();
        $ck->setModelAttris($model);
        $rst = ActInfoM::model()->addM($model, $ck->weekRules, $ck->imgIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act add success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act add fail' . json_encode($model->getErrors()));
    }
    
    
    /**
     * 修改活动资料
     */
    public function actionUpdate() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'title' => Yii::app()->request->getPost('title'),
                'intro' => Yii::app()->request->getPost('intro'),
                'cost' => Yii::app()->request->getPost('cost'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'addr_city' => Yii::app()->request->getPost('addrCity'),
                'addr_area' => Yii::app()->request->getPost('addrArea'),
                'addr_road' => Yii::app()->request->getPost('addrRoad'),
                'addr_num' => Yii::app()->request->getPost('addrNum'),
                'addr_name' => Yii::app()->request->getPost('addrName'),
                'addr_route' => Yii::app()->request->getPost('addrRoute'),
                'contact_way' => Yii::app()->request->getPost('contactWay'),
                'b_time' => Yii::app()->request->getPost('bTime'),
                'e_time' => Yii::app()->request->getPost('eTime'),
                't_status_rule' => Yii::app()->request->getPost('tStatusRule'),
                'weekRules' => Yii::app()->request->getPost('weekRules'),
                'detail' => Yii::app()->request->getPost('detail'),
                'can_enroll' => Yii::app()->request->getPost('canEnroll'),
                'city_id' => Yii::app()->request->getPost('cityId'),
                'tag_id' => Yii::app()->request->getPost('tagId'),
                'h_img_id' => Yii::app()->request->getPost('headImgId'),
                'imgIds' => Yii::app()->request->getPost('imgIds'),
                'lov_base_num' => Yii::app()->request->getPost('lovBaseNum'),
                'share_base_num' => Yii::app()->request->getPost('shareBaseNum'),
            ),
            array(
                array('actId', 'required'),
                array('actId', 'numerical', 'integerOnly' => TRUE),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 64, 'isDiff' => TRUE),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 256),
                array('cost, lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('addr_city, addr_area, addr_road', 'CZhEnV', 'min' => 1, 'max' => 12),
                array('addr_num, addr_name', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('addr_route', 'CZhEnV', 'min' => 1, 'max' => 120),
                array('contact_way', 'length', 'max' => 48),
                array('b_time, e_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('t_status_rule, can_enroll', 'in', 'range' => array(0, 1)),
                array('weekRules', 'CArrNumV', 'maxLen' => 7, 'minNum' => 0, 'maxNum' => 6),
                //array('detail', 'CZhEnV', 'min' => 1, 'max' => 512),
                array('city_id, tag_id, h_img_id, lov_base_num, share_base_num', 'numerical', 'integerOnly' => TRUE),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $model = ActInfo::model()->findByPk($ck->actId);
        if (empty($model)){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'act is not exist');
        }
        
        $ck->setModelAttris($model);
        $rst = ActInfoM::model()->updateM($model, $ck->weekRules, $ck->imgIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act update fail' . json_encode($model->getErrors()));
    }
    
    
    /**
     * 修改活动状态
     */
    public function actionUpdateStatus() 
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('actId, status', 'required'),
                array('actId', 'numerical', 'integerOnly' => true),
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
        
        $rst = ActInfoM::model()->upActStatusM(NULL, $ck->actId, $ck->status);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act status update success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act status update fail');
    }
    
    
    /**
     * 修改推荐活动
     */
    public function actionUpdateRecommends()
    {
        $ck = Rules::instance(
            array(
                'cityId' => Yii::app()->request->getPost('cityId'),
                'actIds' => Yii::app()->request->getPost('actIds', array()),
            ),
            array(
                array('cityId', 'required'),
                array('cityId', 'numerical', 'integerOnly' => TRUE),
                array('actIds', 'CArrNumV', 'maxLen' => 120),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数' . json_encode($ck->getErrors()));
        }
        
        $rst = KeyValInfoM::model()->upRecommmendActsM($ck->cityId, $ck->actIds);
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act update recommends success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act update recommends fail');
    }
    
    
    /**
     * 更新活动的资讯关联
     */
    public function actionDealNews()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'newsId' => Yii::app()->request->getPost('newsId'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('actId, newsId, status', 'required'),
                array('actId, newsId', 'numerical', 'integerOnly' => TRUE),
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
            $rst = ActNewsMapM::model()->delM($ck->actId, $ck->newsId);
        }  else {
            $rst = ActNewsMapM::model()->updateM(NULL, $ck->actId, $ck->newsId);
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act deal news success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act deal news fail');
    }
    
    
    /**
     * 更新活动的达人关联
     */
    public function actionDealVip()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getPost('actId'),
                'vipId' => Yii::app()->request->getPost('vipId'),
                'status' => Yii::app()->request->getPost('status'),
            ),
            array(
                array('actId, vipId, status', 'required'),
                array('actId, vipId', 'numerical', 'integerOnly' => TRUE),
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
            $rst = ActVipMapM::model()->delM($ck->actId, $ck->vipId);
        }  else {
            $rst = ActVipMapM::model()->updateM(NULL, $ck->actId, $ck->vipId);
        }
        
        if ($rst) {
            return Yii::app()->res->output(Error::NONE, 'act deal vip success');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, 'act deal vip fail');
    }
    
}
