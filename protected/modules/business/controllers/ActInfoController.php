<?php

class ActInfoController extends BusinessController
{
    
    public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + update, add, commit, publish, offPublish, del, markCheckin, unMarkCheckin, upCheckinDescri', // we only allow deletion via POST request
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
     * 获取商家的活动
     */
    public function actionBusinessActs()
    {
        $ck = Rules::instance(
            array(
                'tStatus' => Yii::app()->request->getParam('tStatus'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
            ),
            array(
                array('tStatus', 'in', 'range' => array(1, 2, 3, 4)),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        $acts = ActBusinessMap::model()->getActs(Yii::app()->user->id, $ck->tStatus, $ck->keyWords);
        Yii::app()->res->output(Error::NONE, '获取成功', array('acts' => $acts));
    }

    
    /**
     * 获取商家的活动详情
     */
    public function actionBusinessAct()
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        if (!ActBusinessMap::model()->isExist($ck->actId, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $act = ActBusinessMap::model()->getAct($ck->actId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('act' => $act));
    }

    
     /**
     * 获取活动的图片
     */
    public function actionGetImgs() 
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        if (!ActBusinessMap::model()->isExist($ck->actId, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $imgs = ActImgMap::model()->getImgs($ck->actId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('imgs' => $imgs));
    }
    
    
    /**
     * 获取可供筛选的标签
     */
    public function actionGetSltTags() 
    {
        $tags = TagInfo::model()->getSltTags(FALSE);
        Yii::app()->res->output(Error::NONE, '获取成功', array('tags' => $tags));
    }
    
    
    /**
     * 获取所有的标签
     */
    public function actionGetAllTags() 
    {
        $tags = TagInfo::model()->getAllTags(FALSE);
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        if (!ActBusinessMap::model()->isExist($ck->actId, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $tags = ActTagMap::model()->getTags($ck->actId);
        Yii::app()->res->output(Error::NONE, '获取成功', array('tags' => $tags));
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
                'city_id' => Yii::app()->request->getPost('cityId', 1),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'addr_city' => Yii::app()->request->getPost('addrCity'),
                'addr_area' => Yii::app()->request->getPost('addrArea'),
                'addr_road' => Yii::app()->request->getPost('addrRoad'),
                'addr_num' => Yii::app()->request->getPost('addrNum'),
                'addr_route' => Yii::app()->request->getPost('addrRoute'),
                'contact_way' => Yii::app()->request->getPost('contactWay'),
                'b_time' => Yii::app()->request->getPost('bTime'),
                'e_time' => Yii::app()->request->getPost('eTime'),
                't_status_rule' => Yii::app()->request->getPost('tStatusRule'),
                'weekRules' => Yii::app()->request->getPost('weekRules'),
                'detail' => Yii::app()->request->getPost('detail'),
                'detail_all' => Yii::app()->request->getPost('detailAll'),
                'can_enroll' => Yii::app()->request->getPost('canEnroll'),
                'tagIds' => Yii::app()->request->getPost('tagIds'),
                'h_img_id' => Yii::app()->request->getPost('headImgId'),
                'imgIds' => Yii::app()->request->getPost('imgIds'),
            ),
            array(
                array('actId, city_id', 'required'),
                array('actId, city_id', 'numerical', 'integerOnly' => true),
                array('title', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 256),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('addr_city, addr_area, addr_road', 'CZhEnV', 'min' => 1, 'max' => 12),
                array('addr_num', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('addr_route', 'CZhEnV', 'min' => 1, 'max' => 120),
                array('contact_way', 'length', 'max' => 48),
                array('b_time, e_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('t_status_rule, can_enroll', 'in', 'range' => array(0, 1)),
                array('weekRules', 'CArrNumV', 'maxLen' => 7, 'minNum' => 0, 'maxNum' => 6),
                array('detail', 'CZhEnV', 'min' => 1, 'max' => 256),
                //array('detail_all', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('tagIds', 'CArrNumV', 'maxLen' => 2),
                array('h_img_id', 'numerical', 'integerOnly' => true),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        $model = ActInfo::model()->findByPk($ck->actId);
        $isExist = ActBusinessMap::model()->isExist(
                $model->id,
                Yii::app()->user->id);
        if (empty($model) || !$isExist){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        if (1 == $ck->t_status_rule) {
            $bW = date('w', strtotime($ck->b_time));
            $eW = date('w', strtotime($ck->e_time));
            if (empty($ck->weekRules) || !in_array($bW, $ck->weekRules) || !in_array($eW, $ck->weekRules)) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'time rule error');
            }
        }
        
        $ck->setModelAttris($model);
        $r = ActBusinessMap::model()->updateAct($model, $ck->tagIds, $ck->imgIds);
        if ($r) {
            if (1 == $model->t_status_rule) {
                $rstArr = ArrTool::uniqueAscStr($ck->weekRules);
                ActTimeStatusRule::model()->addWeek($model->id, $rstArr);
            }  else {
                ActTimeStatusRule::model()->delWeek($model->id);
            }
            
            BusinessOperateLog::model()->log(Yii::app()->user->id, '修改了活动' . $model->id);
            return Yii::app()->res->output(Error::NONE, '修改成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '修改失败');
    }
    
    
    /**
     * 活动相关图片文件上传
     */
    public function actionActImgUp() {
        $ck = Rules::instance(
            array(
                'actImg' => CUploadedFile::getInstanceByName('actImg'),
                'isReturnUrl' => Yii::app()->request->getPost('isReturnUrl', 0),
            ),
            array(
                array('actImg', 'required'),
                array('actImg', 'file', 'allowEmpty' => true,
                    'types' => 'jpg',
                    'maxSize' => 1024 * 1024 * 1, 
                    'tooLarge' => '上传文件超过 1024 * 1024 kb，无法上传。',
                ),
                array('isReturnUrl', 'in', 'range' => array(0, 1)),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        //保存图像文件到指定目录
        $iu = Yii::app()->imgUpload->uActImg($ck->actImg);
        if (!$iu) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片上传失败');
        }
        
        //图像信息表插入
        $imgInfo = new ImgInfo();
        if (!$imgInfo->ins($ck->actImg->name, $iu)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片保存失败');
        }
        
        $imgUpBusiness = new ImgUpBusinessMap();
        //图像上传者关联表插入
        if (!$imgUpBusiness->ins($imgInfo->id, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::OPERATION_EXCEPTION, '图片保存失败');
        }
        
        $ret = array();
        $ret['img_id'] = $imgInfo->id;
        if ($ck->isReturnUrl) {
            $ret['img_url'] = Yii::app()->imgUpload->getDownUrl($imgInfo->img_url);
        }
        BusinessOperateLog::model()->log(Yii::app()->user->id, '上传了活动相关图片' . $imgInfo->id);
        Yii::app()->res->output(Error::NONE, '图片上传成功', $ret);
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
                'city_id' => Yii::app()->request->getPost('cityId'),
                'lon' => Yii::app()->request->getPost('lon'),
                'lat' => Yii::app()->request->getPost('lat'),
                'addr_city' => Yii::app()->request->getPost('addrCity'),
                'addr_area' => Yii::app()->request->getPost('addrArea'),
                'addr_road' => Yii::app()->request->getPost('addrRoad'),
                'addr_num' => Yii::app()->request->getPost('addrNum'),
                'addr_route' => Yii::app()->request->getPost('addrRoute'),
                'contact_way' => Yii::app()->request->getPost('contactWay'),
                'b_time' => Yii::app()->request->getPost('bTime'),
                'e_time' => Yii::app()->request->getPost('eTime'),
                't_status_rule' => Yii::app()->request->getPost('tStatusRule'),
                'weekRules' => Yii::app()->request->getPost('weekRules'),
                'detail' => Yii::app()->request->getPost('detail'),
                'detail_all' => Yii::app()->request->getPost('detailAll'),
                'can_enroll' => Yii::app()->request->getPost('canEnroll'),
                'tagIds' => Yii::app()->request->getPost('tagIds'),
                'h_img_id' => Yii::app()->request->getPost('headImgId'),
                'imgIds' => Yii::app()->request->getPost('imgIds'),
            ),
            array(
                array('title', 'CZhEnV', 'min' => 1, 'max' => 16),
                array('intro', 'CZhEnV', 'min' => 1, 'max' => 256),
                array('city_id', 'numerical', 'integerOnly' => true),
                array('lon, lat', 'numerical', 'integerOnly' => FALSE),
                array('addr_city, addr_area, addr_road', 'CZhEnV', 'min' => 1, 'max' => 12),
                array('addr_num', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('addr_route', 'CZhEnV', 'min' => 1, 'max' => 120),
                array('contact_way', 'length', 'max' => 48),
                array('b_time, e_time', 'type', 'datetimeFormat' => 'yyyy-mm-dd hh:mm:ss', 'type' => 'datetime'),
                array('t_status_rule, can_enroll', 'in', 'range' => array(0, 1)),
                array('weekRules', 'CArrNumV', 'maxLen' => 7, 'minNum' => 0, 'maxNum' => 6),
                array('detail', 'CZhEnV', 'min' => 1, 'max' => 256),
                //array('detail_all', 'CZhEnV', 'min' => 1, 'max' => 24),
                array('tagIds', 'CArrNumV', 'maxLen' => 2),
                array('h_img_id', 'numerical', 'integerOnly' => true),
                array('imgIds', 'CArrNumV', 'maxLen' => 3),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        if (1 == $ck->t_status_rule) {
            $bW = date('w', strtotime($ck->b_time));
            $eW = date('w', strtotime($ck->e_time));
            if (empty($ck->weekRules) || !in_array($bW, $ck->weekRules) || !in_array($eW, $ck->weekRules)) {
                return Yii::app()->res->output(Error::PARAMS_ILLEGAL, 'time rule error');
            }
        }
        
        $model = new ActInfo();
        $ck->setModelAttris($model);
        $r = ActBusinessMap::model()->addAct($model, $ck->tagIds, $ck->imgIds, Yii::app()->user->id);
        if ($r) {
            if (1 == $model->t_status_rule) {
                $rstArr = ArrTool::uniqueAscStr($ck->weekRules);
                ActTimeStatusRule::model()->addWeek($model->id, $rstArr);
            }  else {
                ActTimeStatusRule::model()->delWeek($model->id);
            }
            
            BusinessOperateLog::model()->log(Yii::app()->user->id, '添加了活动' . $model->id);
            return Yii::app()->res->output(Error::NONE, '添加成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '添加失败');
    }
    
    
    /**
     * 提交活动
     */
    public function actionCommit()
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        if (!ActBusinessMap::model()->isExist($ck->actId, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $r = ActInfo::model()->commitAct($ck->actId);
        if ($r) {
            BusinessOperateLog::model()->log(Yii::app()->user->id, '提交了活动' . $ck->actId);
            return Yii::app()->res->output(Error::NONE, '提交成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '提交失败');
    }
    
    
    /**
     * 发布活动
     */
    public function actionPublish()
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        if (!ActBusinessMap::model()->isExist($ck->actId, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $r = ActInfo::model()->publishAct($ck->actId);
        if ($r) {
            BusinessOperateLog::model()->log(Yii::app()->user->id, '发布了活动' . $ck->actId);
            return Yii::app()->res->output(Error::NONE, '发布成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '发布失败');
    }
    
    
    /**
     * 下架活动
     */
    public function actionOffPublish()
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        if (!ActBusinessMap::model()->isExist($ck->actId, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $r = ActInfo::model()->offPublishAct($ck->actId);
        if ($r) {
            BusinessOperateLog::model()->log(Yii::app()->user->id, '下架了活动' . $ck->actId);
            return Yii::app()->res->output(Error::NONE, '下架成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '下架失败');
    }
    
    
    /**
     * 删除活动
     */
    public function actionDel()
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        if (!ActBusinessMap::model()->isExist($ck->actId, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $r = ActInfo::model()->delAct($ck->actId);
        if ($r) {
            BusinessOperateLog::model()->log(Yii::app()->user->id, '删除了活动' . $ck->actId);
            return Yii::app()->res->output(Error::NONE, '删除成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '删除失败');
    }
    
    
    /**
     * 签到的活动
     */
    public function actionCheckinActs()
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
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        $acts = ActBusinessMap::model()->getActsWithCheckin(Yii::app()->user->id, $ck->keyWords);
        Yii::app()->res->output(Error::NONE, '获取成功', array('acts' => $acts));
    }
    
    
    /**
     * 活动的签到信息及用户信息
     */
    public function actionCheckinUsers()
    {
        $ck = Rules::instance(
            array(
                'actId' => Yii::app()->request->getParam('actId'),
                'keyWords' => Yii::app()->request->getParam('keyWords'),
                'page' => Yii::app()->request->getParam('page', 1),
                'size' => Yii::app()->request->getParam('size', 1024),
            ),
            array(
                array('actId, page, size', 'required'),
                array('actId, page, size', 'numerical', 'integerOnly' => true),
                array('keyWords', 'CZhEnV', 'min' => 0, 'max' => 16),
            )
        );

        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        if (!ActBusinessMap::model()->isExist($ck->actId, Yii::app()->user->id)) {
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '活动不存在');
        }
        
        $rst = ActCheckin::model()->getCheckinWithUsers($ck->actId, $ck->keyWords, $ck->page, $ck->size);
        Yii::app()->res->output(Error::NONE, '获取成功', $rst);
    }
    
    
    /**
     * 标注用户签到
     */
    public function actionMarkCheckin()
    {
        $ck = Rules::instance(
            array(
                'checkinId' => Yii::app()->request->getPost('checkinId'),
            ),
            array(
                array('checkinId', 'required'),
                array('checkinId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        $r = ActCheckin::model()->mark($ck->checkinId);
        if ($r) {
            BusinessOperateLog::model()->log(Yii::app()->user->id, '标注了签到用户');
            return Yii::app()->res->output(Error::NONE, '标注成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '标注失败');
    }
    
    
    /**
     * 取消标注用户签到
     */
    public function actionUnMarkCheckin()
    {
        $ck = Rules::instance(
            array(
                'checkinId' => Yii::app()->request->getPost('checkinId'),
            ),
            array(
                array('checkinId', 'required'),
                array('checkinId', 'numerical', 'integerOnly' => true),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        $r = ActCheckin::model()->unMark($ck->checkinId);
        if ($r) {
            BusinessOperateLog::model()->log(Yii::app()->user->id, '取消标注了签到用户');
            return Yii::app()->res->output(Error::NONE, '取消标注成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '取消标注失败');
    }
    
    
    /**
     * 修改标签备注
     */
    public function actionUpCheckinDescri()
    {
        $ck = Rules::instance(
            array(
                'checkinId' => Yii::app()->request->getPost('checkinId'),
                'descri' => Yii::app()->request->getPost('descri'),
            ),
            array(
                array('checkinId', 'required'),
                array('checkinId', 'numerical', 'integerOnly' => true),
                array('descri', 'CZhEnV', 'min' => 0, 'max' => 60),
            )
        );
        
        $cko = $ck->validate();
        if (!$cko){
            return Yii::app()->res->output(Error::PARAMS_ILLEGAL, '非法参数');
        }
        
        $r = ActCheckin::model()->updateDescri($ck->checkinId, $ck->descri);
        if ($r) {
            BusinessOperateLog::model()->log(Yii::app()->user->id, '修改了签到用户备注');
            return Yii::app()->res->output(Error::NONE, '备注成功');
        }
        Yii::app()->res->output(Error::OPERATION_EXCEPTION, '备注失败');
    }
    
}