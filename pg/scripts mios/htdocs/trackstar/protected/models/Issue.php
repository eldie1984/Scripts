<?php

/**
 * This is the model class for table "TBL_ISSUE".
 *
 * The followings are the available columns in table 'TBL_ISSUE':
 * @property integer $ID
 * @property string $NAME
 * @property string $DESCRIPTION
 * @property double $PROJECT_ID
 * @property double $TYPE_ID
 * @property double $STATUS_ID
 * @property double $OWNER_ID
 * @property double $REQUESTER_ID
 * @property string $CREATE_TIME
 * @property double $CREATE_USER_ID
 * @property string $UPDATE_TIME
 * @property double $UPDATE_USER_ID
 */
class Issue extends TrackStarActiveRecord
{
	const TYPE_BUG=0;
	const TYPE_FEATURE=1;
	const TYPE_TASK=2;

	
	const STATUS_NOT_YET=0;
	const STATUS_STARTED=1;
	const STATUS_FINISHED=2;

	/**
	* Retrieves a list of issue types
	* @return array an array of available issue types.
	*/
	public function getTypeOptions()
	{
		return array(
			self::TYPE_BUG=>'Bug',
			self::TYPE_FEATURE=>'Feature',
			self::TYPE_TASK=>'Task',
			);
	}

	public function getStatusOptions()
	{
		return array(
			self::STATUS_NOT_YET=>'Not yet started',
			self::STATUS_STARTED=>'Started',
			self::STATUS_FINISHED=>'Finished',
			);
	}	
	public static function getAllowedTypeRange()
	{
		return array(
			self::TYPE_BUG,
			self::TYPE_FEATURE,
			self::TYPE_TASK,
			);
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'TBL_ISSUE';
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
			array('PROJECT_ID, TYPE_ID, STATUS_ID, OWNER_ID, REQUESTER_ID', 'numerical'),
			array('NAME', 'length', 'max'=>255),
			array('DESCRIPTION', 'length', 'max'=>4000),
			array('TYPE_ID', 'in', 'range'=>self::getAllowedTypeRange()),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ID, NAME, DESCRIPTION, PROJECT_ID, TYPE_ID, STATUS_ID, OWNER_ID, REQUESTER_ID, CREATE_TIME, CREATE_USER_ID, UPDATE_TIME, UPDATE_USER_ID', 'safe', 'on'=>'search'),
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
			'requester' => array(self::BELONGS_TO, 'User', 'REQUESTER_ID'),
			'owner' => array(self::BELONGS_TO, 'User', 'OWNER_ID'),
			'project' => array(self::BELONGS_TO, 'Project', 'PROJECT_ID'),
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
			'PROJECT_ID' => 'Project',
			'TYPE_ID' => 'Type',
			'STATUS_ID' => 'Status',
			'OWNER_ID' => 'Owner',
			'REQUESTER_ID' => 'Requester',
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
		$criteria->compare('PROJECT_ID',$this->PROJECT_ID);
		$criteria->compare('TYPE_ID',$this->TYPE_ID);
		$criteria->compare('STATUS_ID',$this->STATUS_ID);
		$criteria->compare('OWNER_ID',$this->OWNER_ID);
		$criteria->compare('REQUESTER_ID',$this->REQUESTER_ID);
		$criteria->compare('CREATE_TIME',$this->CREATE_TIME,true);
		$criteria->compare('CREATE_USER_ID',$this->CREATE_USER_ID);
		$criteria->compare('UPDATE_TIME',$this->UPDATE_TIME,true);
		$criteria->compare('UPDATE_USER_ID',$this->UPDATE_USER_ID);
		$criteria->condition='PROJECT_ID=:projectID';
		$criteria->params=array(':projectID'=>$this->PROJECT_ID);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Issue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	/**
	* @return string the status text display for the current issue
	*/
	public function getStatusText()
	{
		$statusOptions=$this->statusOptions;
		return isset($statusOptions[$this->STATUS_ID]) ? $statusOptions[$this->STATUS_ID] : "unknown status ({$this->STATUS_ID})";
	}
	/**
	* @return string the type text display for the current issue
	*/
	public function getTypeText()
	{
		$typeOptions=$this->typeOptions;
		return isset($typeOptions[$this->TYPE_ID]) ? $typeOptions[$this->TYPE_ID] : "unknown type ({$this->TYPE_ID})";
	}
}
