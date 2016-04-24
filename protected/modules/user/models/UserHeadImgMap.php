<?php

/**
 * This is the model class for table "user_head_img_map".
 *
 * The followings are the available columns in table 'user_head_img_map':
 * @property string $id
 * @property string $u_id
 * @property string $img_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ImgInfo $img
 * @property UserInfo $u
 */
class UserHeadImgMap extends UserModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_head_img_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, img_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('u_id, img_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, img_id, status', 'safe', 'on'=>'search'),
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
			//'img' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkImg' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户头像关联id',
			'u_id' => '用户id',
			'img_id' => '图像id',
			'status' => '状态：-1删除，0可用，1当前使用',
		);
	}
    
    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    
    /**
     * 设置某张图为头像
     * @param type $id
     * @return boolean
     */
    public function setCurrImg($uid, $imgId) 
    {
        $uhi = $this->find('u_id=:uid and img_id=:imgId', array(
            ':uid' => $uid,
            ':imgId' => $imgId,
        ));
        if (empty($uhi)) {
            $modelImg = new UserHeadImgMap();
            $modelImg->u_id = $uid;
            $modelImg->img_id = $imgId;
            $modelImg->status = 0;
            $s = $modelImg->save();
            if (!$s) {
                return FALSE;
            }
            $uhi = $modelImg;
        }
        
        $this->updateAll(array('status' => 0), 'status=1 and u_id=:uid', array(':uid' => $uid));
        
        return $this->updateByPk($uhi->id, array('status' => 1));
    }
    
    
    /**
     * 获取当前头像路径
     * @param type $uid
     * @return null
     */
    public function getCurImgUrl($uid) 
    {
        $imgM = $this->find('u_id=:uid and status=1', array(':uid' => $uid));
        if (empty($imgM)) {
            return NULL;
        }
        $img = ImgInfo::model()->profile($imgM->img_id);
        if (empty($img)) {
            return NULL;
        }
        return $img['img_url'];
    }
    
    
    /**
     * 获取当前头像
     * @param type $uid
     */
    public function getCurrImg($uid) 
    {
        $imgM = $this->find('u_id=:uid and status=1', array(':uid' => $uid));
        if (empty($imgM)) {
            return NULL;
        }
        return ImgInfo::model()->profile($imgM->img_id);
    }
    
}
