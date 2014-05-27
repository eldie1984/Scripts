<?php

/**
 * This is the model class for table "TBL_USER".
 *
 * The followings are the available columns in table 'TBL_USER':
 * @property integer $ID
 * @property string $USERNAME
 * @property string $EMAIL
 * @property string $PASSWORD
 * @property string $LAST_LOGIN_TIME
 * @property string $CREATE_TIME
 * @property double $CREATE_USER_ID
 * @property string $UPDATE_TIME
 * @property double $UPDATE_USER_ID
 */
class User extends TrackStarActiveRecord
{
	public $PASSWORD_repeat;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'TBL_USER';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('USERNAME, EMAIL, PASSWORD, PASSWORD_repeat', 'required'),
			array('USERNAME, EMAIL, PASSWORD', 'length', 'max'=>255),
			array('EMAIL, USERNAME', 'unique'),
			array('EMAIL', 'email'),
			array('PASSWORD', 'compare'),
			array('PASSWORD_repeat', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ID, USERNAME, EMAIL, PASSWORD, LAST_LOGIN_TIME, CREATE_TIME, CREATE_USER_ID, UPDATE_TIME, UPDATE_USER_ID', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'USERNAME' => 'Username',
			'EMAIL' => 'Email',
			'PASSWORD' => 'Password',
			'LAST_LOGIN_TIME' => 'Last Login Time',
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
		$criteria->compare('USERNAME',$this->USERNAME,true);
		$criteria->compare('EMAIL',$this->EMAIL,true);
		$criteria->compare('PASSWORD',$this->PASSWORD,true);
		$criteria->compare('LAST_LOGIN_TIME',$this->LAST_LOGIN_TIME,true);
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
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/*public function behaviors()
	{
		return array(
			'CTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				//'createAttribute' => 'CREATE_TIME',
				'updateAttribute' => 'UPDATE_TIME',
				'setUpdateOnCreate' => true,
			),
		);
	}*/

	 public function init()
  {
     $this->LAST_LOGIN_TIME=new CDbExpression('null');
  }

  	/**
	* apply a hash on the password before we store it in the database
	*/
	protected function afterValidate()
	{
		parent::afterValidate();
		if(!$this->hasErrors())
			$this->PASSWORD = $this->hashPassword($this->PASSWORD);
	}
	/**
	* Generates the password hash.
	* @param string password
	* @return string hash
	*/
	public function hashPassword($password)
	{
		return md5($password);
	}

	/**
	* Checks if the given password is correct.
	* @param string the password to be validated
	* @return boolean whether the password is valid
	*/
	public function validatePassword($password)
	{
		return $this->hashPassword($password)===$this->PASSWORD;
	}
}
