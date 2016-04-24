<?php

/**
 * This is the model class for table "zzzzz_test_log".
 *
 * The followings are the available columns in table 'zzzzz_test_log':
 * @property string $id
 * @property string $content
 * @property string $create_time
 */
class ZzzzzTestLog extends CActiveRecord 
{

    public static $dbT;

    public function getDbConnection() {
        if (self::$dbT === null) {
            self::$dbT = Yii::app()->getComponent('dbAdmin');
            if (!(self::$dbT instanceof CDbConnection))
                throw new CDbException(Yii::t('yii', 'Active Record requires a "dbAct" CDbConnection application component.'));
        }
        return self::$db = self::$dbT;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'zzzzz_test_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('create_time', 'required'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, content, create_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => '测试时的打印日志id',
            'content' => '内容',
            'create_time' => '创建时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('create_time', $this->create_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ZzzzzTestLog the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function add($content) 
    {
        $model = new ZzzzzTestLog();
        $model->content = $content;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }

}
