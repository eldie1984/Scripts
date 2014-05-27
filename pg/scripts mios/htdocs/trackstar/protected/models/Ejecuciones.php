<?php

/**
 * This is the model class for table "EJECUCIONES".
 *
 * The followings are the available columns in table 'EJECUCIONES':
 * @property string $SESION
 * @property string $UPROC
 * @property string $MU
 * @property string $ESTADO
 * @property string $INICIO
 * @property string $FIN
 * @property string $HOST
 */
class Ejecuciones extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'EJECUCIONES';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('MU', 'length', 'max'=>10),
			array('ESTADO', 'length', 'max'=>20),
			array('FIN', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('SESION, UPROC, MU, ESTADO, INICIO, FIN, HOST', 'safe', 'on'=>'search'),
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
			'SESION' => 'Sesion',
			'UPROC' => 'Uproc',
			'MU' => 'Mu',
			'ESTADO' => 'Estado',
			'INICIO' => 'Inicio',
			'FIN' => 'Fin',
			'HOST' => 'Host',
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

		$criteria->compare('SESION',$this->SESION,true);
		$criteria->compare('UPROC',$this->UPROC,true);
		$criteria->compare('MU',$this->MU,true);
		$criteria->compare('ESTADO',$this->ESTADO,true);
		$criteria->compare('INICIO',$this->INICIO,true);
		$criteria->compare('FIN',$this->FIN,true);
		$criteria->compare('HOST',$this->HOST,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Ejecuciones the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	/**
	* @return string the status text display for the current issue
	*/
	public function getHostOnly()
	{

    $criteria = new CDbCriteria;
    $criteria->select = 'distinct(HOST)';
    //$criteria->alias = 'l';
    //$criteria->join = 'left join users u on (u.id = l.user_id) left join profile p on (p.user_id = l.user_id)';
    //$criteria->group = 'l.user_id';
    //$criteria->order = 'Logins desc';
    //$criteria->limit = '10';

    return new CActiveDataProvider($this, array(
        'criteria'=>$criteria,
    ));
	}

}
