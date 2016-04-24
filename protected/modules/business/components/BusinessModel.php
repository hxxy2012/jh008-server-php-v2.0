<?php

class BusinessModel extends CActiveRecord
{
    public static $dbT;
    
    public function getDbConnection()
	{
        if(self::$dbT === null) {
            self::$dbT=Yii::app()->getComponent('dbBusiness');
			if(!(self::$dbT instanceof CDbConnection))
				throw new CDbException(Yii::t('yii','Active Record requires a "dbBusiness" CDbConnection application component.'));
		}
        return self::$db = self::$dbT;
	}
    
}

?>
