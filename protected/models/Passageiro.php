<?php

/**
 * This is the model class for table "tbl_passageiro".
 *
 * The followings are the available columns in table 'tbl_passageiro':
 * @property integer $id
 * @property string $nome
 * @property string $email
 * @property string $telefone
 * @property string $status
 * @property string $data
 * @property string $observacao
 */
class Passageiro extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_passageiro';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nome, email, telefone, status, data', 'required'),
			array('nome, email, telefone', 'length', 'max'=>128),
			array('nome', 'ext.EWordValidator', 'min'=>2,),
			//array('nome', 'have2Words', 'message'=>'O nome deve ter 2 palavras'),
			array('email', 'email'), // validate email format
			array('status', 'length', 'max'=>1),
			array('observacao', 'length', 'max'=>200),
			array ('status', 'in', 'range' => array ('A', 'I'), 'allowEmpty' => false, 'message' => 'O status deve ser A para ativo ou I para inativo'), // validate status
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, nome, email, telefone, status, data, observacao', 'safe', 'on'=>'search'),
		);
	}

	/** NOT WORKING
	 * Rule to verify number of words in a string
	 * 
	 * @param string $attribute
	 * @return boolean
	 */
	// public function have2Words($attribute):bool
	// {
	// 	$words = explode(' ', $this->$attribute);
	// 	if (count($words) < 2)
	// 		return false;

	// 	return true;
	// }
	


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
			'id' => 'ID',
			'nome' => 'Nome',
			'email' => 'Email',
			'telefone' => 'Telefone',
			'status' => 'Status',
			'data' => 'Data',
			'observacao' => 'Observacao',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('nome',$this->nome,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('telefone',$this->telefone,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('observacao',$this->observacao,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Passageiro the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
