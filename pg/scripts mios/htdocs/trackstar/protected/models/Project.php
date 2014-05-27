<?php

/**
 * This is the model class for table "TBL_PROJECT".
 *
 * The followings are the available columns in table 'TBL_PROJECT':
 * @property double $ID
 * @property string $NAME
 * @property string $DESCRIPTION
 * @property string $CREATE_TIME
 * @property double $CREATE_USER_ID
 * @property string $UPDATE_TIME
 * @property double $UPDATE_USER_ID
 */
class Project extends TrackStarActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'TBL_PROJECT';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('NAME', 'required'),
			array('NAME, DESCRIPTION', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ID, NAME, DESCRIPTION, CREATE_TIME, CREATE_USER_ID, UPDATE_TIME, UPDATE_USER_ID', 'safe', 'on'=>'search'),
		);
	}

	/**
	* @return array relational rules.
	*/
	public function relations()
	{
		return array(
			'issues' => array(self::HAS_MANY, 'ISSUE', 'PROJECT_ID'),
			'users' => array(self::MANY_MANY, 'USER', 'TBL_PROJECT_USER_ASSIGNMENT(PROJECT_ID, USER_ID)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'NAME' => 'Name',
			'DESCRIPTION' => 'Description',
			'CREATE_TIME' => 'Create Time',
			'CREATE_USER_ID' => 'Create User',
			'UPDATE_TIME' => 'Update Time',
			'UPDATE_USER_ID' => 'Update User',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('ID',$this->ID);
		$criteria->compare('NAME',$this->NAME,true);
		$criteria->compare('DESCRIPTION',$this->DESCRIPTION,true);
		$criteria->compare('CREATE_TIME',$this->CREATE_TIME,true);
		$criteria->compare('CREATE_USER_ID',$this->CREATE_USER_ID);
		$criteria->compare('UPDATE_TIME',$this->UPDATE_TIME,true);
		$criteria->compare('UPDATE_USER_ID',$this->UPDATE_USER_ID);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Project the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	/**
	* @return array of valid users for this project, indexed by user IDs
	*/
	public function getUserOptions()
	{
		$usersArray = CHtml::listData($this->users, 'ID', 'USERNAME');
			return $usersArray;
	}
}
