<?php

/**
 * This is the model class for table "org_info".
 *
 * The followings are the available columns in table 'org_info':
 * @property string $id
 * @property string $name
 * @property string $intro
 * @property string $logo_img_id
 * @property string $own_id
 * @property string $contact_way
 * @property string $address
 * @property integer $status
 * @property string $creat_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActInfo[] $actInfos
 * @property CustomExtOrgMap[] $customExtOrgMaps
 * @property OrgCityMap[] $orgCityMaps
 * @property ImgInfo $logoImg
 * @property UserInfo $own
 * @property OrgLovUserMap[] $orgLovUserMaps
 * @property OrgManagerMap[] $orgManagerMaps
 */
class OrgInfo extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'org_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('own_id, creat_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name, contact_way, address', 'length', 'max'=>255),
			array('logo_img_id, own_id', 'length', 'max'=>10),
			array('intro', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, intro, logo_img_id, own_id, contact_way, address, status, creat_time, modify_time', 'safe', 'on'=>'search'),
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
			//'actInfos' => array(self::HAS_MANY, 'ActInfo', 'org_id'),
			//'customExtOrgMaps' => array(self::HAS_MANY, 'CustomExtOrgMap', 'o_id'),
			//'orgCityMaps' => array(self::HAS_MANY, 'OrgCityMap', 'org_id'),
			//'logoImg' => array(self::BELONGS_TO, 'ImgInfo', 'logo_img_id'),
			//'own' => array(self::BELONGS_TO, 'UserInfo', 'own_id'),
			//'orgLovUserMaps' => array(self::HAS_MANY, 'OrgLovUserMap', 'org_id'),
			//'orgManagerMaps' => array(self::HAS_MANY, 'OrgManagerMap', 'org_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '组织id',
			'name' => '名称',
			'intro' => '简介',
			'logo_img_id' => 'logo图片id',
			'own_id' => '组织群主id',
			'contact_way' => '联系方式',
			'address' => '地址',
			'status' => '状态',
			'creat_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrgInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 基本资料
     * 
     * @param type $id 社团id
     * @param type $model
     * @param type $currUid 当前用户id
     */
    public function profile($id, $model = NULL, $currUid = NULL)
    {
        if (empty($model) && !empty($id)) {
            $model = $this->findByPk($id);
        }
        if (empty($model)) {
            return NULL;
        }
        $org = array();
        $org['id'] = $model->id;
        $org['subject'] = $model->name;
        $org['lov_user_num'] = OrgLovUserMap::model()->getLovedNum($model->id);
        $org['act_num'] = $this->getActNum($model->id);
        $org['is_loved'] = empty($currUid) ? 0 : OrgLovUserMap::model()->isLoved($model->id, $currUid);
        if (!empty($model->logo_img_id)) {
            $img = ImgInfo::model()->profile($model->logo_img_id);
            if (!empty($img)) {
                $org['icon_url'] = $img['img_url'];
            }
        }
        return $org;
    }
    
    
    /**
     * 详情
     * 
     * @param type $id 社团id
     * @param type $model
     * @param type $currUid 当前用户id
     */
    public function fullProfile($id, $model = NULL, $currUid = NULL)
    {
        if (empty($model) && !empty($id)) {
            $model = $this->findByPk($id);
        }
        if (empty($model)) {
            return NULL;
        }
        $org = array();
        $org['id'] = $model->id;
        $org['subject'] = $model->name;
        $org['lov_user_num'] = OrgLovUserMap::model()->getLovedNum($model->id);
        $org['act_num'] = $this->getActNum($model->id);
        $org['intro'] = $model->intro;
        $org['contact_way'] = $model->contact_way;
        $org['address'] = $model->address;
        $org['is_loved'] = empty($currUid) ? 0 : OrgLovUserMap::model()->isLoved($model->id, $currUid);
        if (!empty($model->logo_img_id)) {
            $img = ImgInfo::model()->profile($model->logo_img_id);
            if (!empty($img)) {
                $org['icon_url'] = $img['img_url'];
            }
        }
        return $org;
    }
    
    
    /**
     * 社团近期活动
     * 
     * @param type $orgId 社团id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function acts($orgId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.status', ConstActStatus::PUBLISHING);
        $cr->order = 't.id desc';
        $count = ActInfo::model()->count($cr);
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = ActInfo::model()->findAll($cr);
        
        $acts = array();
        foreach ($rst as $v) {
            $act = ActInfo::model()->profile($v, NULL, NULL, FALSE);
            if (empty($act)) {
                continue;
            }
            array_push($acts, $act);
        }
        
        return array(
            'total_num' => $count,
            'acts' => $acts,
        );
    }
    
    
    /**
     * 获取社团的活动数
     * 
     * @param type $orgId 社团id
     */
    public function getActNum($orgId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.status', ConstActStatus::PUBLISHING);
        return ActInfo::model()->count($cr);
    }
    
}
