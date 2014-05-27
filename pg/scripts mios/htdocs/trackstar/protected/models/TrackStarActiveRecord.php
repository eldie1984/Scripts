<?php
abstract class TrackStarActiveRecord extends CActiveRecord
{
	/**
	* Prepares create_user_id and update_user_id attributes before saving.
	*/
	protected function beforeSave()
	{
		if(null !== Yii::app()->user)
			//$id=Yii::app()->user->ID;
			$id=1;
		else
			$id=1;

		$this->CREATE_TIME=$this->UPDATE_TIME=new CDbExpression('sysdate');
		if($this->isNewRecord)
			$this->CREATE_USER_ID=$id;
			$this->UPDATE_USER_ID=$id;
		return parent::beforeSave();
	}
	/**
	* Attaches the timestamp behavior to update our create and update times
	*/
	/*public function behaviors()
	{
		return array(
			'CTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
			//	'createAttribute' => 'CREATE_TIME',
			//	'updateAttribute' => 'UPDATE_TIME',
				'setUpdateOnCreate' => true,
			),
		);
	}*/
}