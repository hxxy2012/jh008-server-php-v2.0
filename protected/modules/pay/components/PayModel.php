<?php

class PayModel extends CActiveRecord
{
    public static $dbT;
    
    public function getDbConnection()
	{
        if(self::$dbT === null) {
            self::$dbT=Yii::app()->getComponent('dbPay');
			if(!(self::$dbT instanceof CDbConnection))
				throw new CDbException(Yii::t('yii','Active Record requires a "dbPay" CDbConnection application component.'));
		}
        return self::$db = self::$dbT;
	}
    
}

?>
