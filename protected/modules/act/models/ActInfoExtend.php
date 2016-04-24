<?php

/**
 * This is the model class for table "act_info_extend".
 *
 * The followings are the available columns in table 'act_info_extend':
 * @property string $id
 * @property string $act_id
 * @property integer $show_enroll
 * @property string $enroll_b_time
 * @property string $enroll_e_time
 * @property string $call_phone
 * @property string $call_name
 * @property integer $show_verify
 * @property integer $show_pay
 * @property integer $product_id
 * @property integer $show_enroll_custom
 * @property integer $enroll_limit
 * @property integer $enroll_limit_num
 * @property integer $limit_sex_num
 * @property integer $limit_male_num
 * @property integer $limit_female_num
 * @property integer $can_with_people
 * @property integer $with_people_limit_num
 * @property integer $show_manager
 * @property integer $show_process
 * @property integer $show_menu
 * @property integer $show_attention
 * @property integer $show_busi
 * @property integer $show_navi
 * @property integer $show_place_img
 * @property integer $show_route_map
 * @property integer $show_location_share
 * @property integer $show_album
 * @property integer $can_upload_album
 * @property integer $specity_user_upload
 * @property integer $show_video
 * @property integer $show_message
 * @property integer $show_group
 * @property integer $show_notice
 * @property integer $show_more_addr
 * @property integer $show_prize
 * @property integer $show_checkin
 * @property integer $show_order
 * @property string $busi_intro
 * @property string $lunch_time
 * @property string $lunch_addr
 * @property string $supper_time
 * @property string $supper_addr
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 */
class ActInfoExtend extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_info_extend';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id', 'required'),
			array('show_enroll, show_verify, show_pay, product_id, show_enroll_custom, enroll_limit, enroll_limit_num, limit_sex_num, limit_male_num, limit_female_num, can_with_people, with_people_limit_num, show_manager, show_process, show_menu, show_attention, show_busi, show_navi, show_place_img, show_route_map, show_location_share, show_album, can_upload_album, specity_user_upload, show_video, show_message, show_group, show_notice, show_more_addr, show_prize, show_checkin, show_order', 'numerical', 'integerOnly'=>true),
			array('act_id', 'length', 'max'=>10),
			array('lunch_addr, supper_addr', 'length', 'max'=>64),
			array('enroll_b_time, enroll_e_time, busi_intro, lunch_time, supper_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, show_enroll, enroll_b_time, enroll_e_time, show_verify, show_pay, product_id, show_enroll_custom, enroll_limit, enroll_limit_num, limit_sex_num, limit_male_num, limit_female_num, can_with_people, with_people_limit_num, show_manager, show_process, show_menu, show_attention, show_busi, show_navi, show_place_img, show_route_map, show_location_share, show_album, can_upload_album, specity_user_upload, show_video, show_message, show_group, show_notice, show_more_addr, show_prize, show_checkin, show_order, busi_intro, lunch_time, lunch_addr, supper_time, supper_addr', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			//'act' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动扩展信息id',
			'act_id' => '活动id',
			'show_enroll' => '报名模块',
			'enroll_b_time' => '报名开始时间',
			'enroll_e_time' => '报名结束时间',
			'show_verify' => '报名审核模块',
			'show_pay' => '报名支付模块',
			'product_id' => '报名支付商品id',
			'show_enroll_custom' => '自定义报名信息模块',
			'enroll_limit' => '是否报名限制',
			'enroll_limit_num' => '报名限制人数',
			'limit_sex_num' => '是否分性别限制',
			'limit_male_num' => '限制男性人数',
			'limit_female_num' => '限制女性人数',
			'can_with_people' => '是否允许携带随行人员',
			'with_people_limit_num' => '随行人员限制人数',
			'show_manager' => '管理员模块',
			'show_process' => '流程模块',
			'show_menu' => '宴会菜单模块',
			'show_attention' => '注意事项模块',
			'show_busi' => '主办方介绍模块',
			'show_navi' => '导航模块',
			'show_place_img' => '场地平面图模块',
			'show_route_map' => '路线图模块',
			'show_location_share' => '位置共享模块',
			'show_album' => '相册模块',
			'can_upload_album' => '是否允许上传相册',
			'specity_user_upload' => '是否指定用户才能上传',
			'show_video' => '视频模块',
			'show_message' => '留言模块',
			'show_group' => '分组模块',
			'show_notice' => '最新通知模块',
			'show_more_addr' => '更多地点模块',
			'show_prize' => '奖品模块',
			'show_checkin' => '签到模块',
			'show_order' => '订购模块',
			'busi_intro' => '主办方介绍',
			'lunch_time' => '午餐时间',
			'lunch_addr' => '午餐地址',
			'supper_time' => '晚餐时间',
			'supper_addr' => '晚餐地址',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActInfoExtend the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 活动扩展模块及信息
     * 
     * @param type $actId 活动id
     */
    public function get($actId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('act_id', $actId);
        $model = $this->find($cr);
        if (empty($model)) {
            return NULL;
        }
        
        return $model;
    }
    
    
    /**
     * 模块设置情况
     * 
     * @param type $actId 活动id
     */
    public function modules($actId)
    {
        $model = $this->get($actId);
        if (empty($model)) {
            return NULL;
        }
        
        return array(
            'show_enroll' => $model->show_enroll,
            'show_verify' => $model->show_verify,
            'show_pay' => $model->show_pay,
            'show_enroll_custom' => $model->show_enroll_custom,
            'show_manager' => $model->show_manager,
            'show_process' => $model->show_process,
            'show_menu' => $model->show_menu,
            'show_attention' => $model->show_attention,
            'show_busi' => $model->show_busi,
            'show_navi' => $model->show_navi,
            'show_place_img' => $model->show_place_img,
            'show_route_map' => $model->show_route_map,
            'show_location_share' => $model->show_location_share,
            'show_album' => $model->show_album,
            'show_video' => $model->show_video,
            'show_message' => $model->show_message,
            'show_group' => $model->show_group,
            'show_notice' => $model->show_notice,
            'show_more_addr' => $model->show_more_addr,
            'show_prize' => $model->show_prize,
            'show_checkin' => $model->show_checkin,
            'show_order' => $model->show_order,
        );
    }
    
    
    /**
     * 更多活动信息
     * 
     * @param type $actId 活动id
     */
    public function moreInfo($actId) 
    {
        $model = $this->get($actId);
        if (empty($model)) {
            return NULL;
        }
        
        $moreInfo = array(
            'enroll_limit' => $model->enroll_limit,
            'enroll_limit_num' => $model->enroll_limit_num,
            'limit_sex_num' => $model->limit_sex_num,
            'limit_male_num' => $model->limit_male_num,
            'limit_female_num' => $model->limit_female_num,
            'can_with_people' => $model->can_with_people,
            'with_people_limit_num' => $model->with_people_limit_num,
        );
        
        //if (!empty($model->busi_intro)) {
        $moreInfo['busi_url'] = Yii::app()->webPage->getViewUrl('act/actMore/busiweb', array('actId' => $model->act_id));
        //}
        //活动报名支付商品信息
        if (1 == $model->show_pay) {
            $moreInfo['product'] = empty($model->product_id) ? NULL : Product::model()->profile($model->product_id);
        }
        return $moreInfo;
    }
    
    
    /**
     * 获取活动宴会信息（时间地点）
     * 
     * @param type $actId
     */
    public function getMenuInfo($actId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return NULL;
        }
        return array(
            'lunch_time' => $model->lunch_time,
            'lunch_addr' => $model->lunch_addr,
            'supper_time' => $model->supper_time,
            'supper_addr' => $model->supper_addr,
        );
    }
    
    
    /**
     * 商品id
     * 
     * @param type $actId
     */
    public function productId($actId)
    {
        $model = $this->get($actId);
        if (empty($model)) {
            return NULL;
        }
        return $model->product_id;
    }
    
}
