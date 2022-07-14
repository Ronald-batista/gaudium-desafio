<?php

/**
 * This is the model class for table "tbl_corrida".
 *
 * The followings are the available columns in table 'tbl_corrida':
 * @property integer $passageiro_id
 * @property integer $motorista_id
 * @property string $endereco_origem
 * @property string $endereco_destino
 * @property string $data_inicio
 * @property string $status
 * @property string $previsao_chegada
 * @property double $tarifa
 * @property string $data_finalizacao
 *
 * The followings are the available model relations:
 * @property TblMotorista $motorista
 * @property TblPassageiro $passageiro
 */
class Corrida extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_corrida';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('endereco_origem, endereco_destino, data_inicio, status, previsao_chegada, tarifa, data_finalizacao', 'required'),
			array('passageiro_id, motorista_id', 'numerical', 'integerOnly'=>true),
			array('tarifa', 'numerical'),
			array('endereco_origem, endereco_destino', 'length', 'max'=>256),
			array('status', 'length', 'max'=>30),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('passageiro_id, motorista_id, endereco_origem, endereco_destino, data_inicio, status, previsao_chegada, tarifa, data_finalizacao', 'safe', 'on'=>'search'),
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
			'motorista' => array(self::BELONGS_TO, 'TblMotorista', 'motorista_id'),
			'passageiro' => array(self::BELONGS_TO, 'TblPassageiro', 'passageiro_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'passageiro_id' => 'Passageiro',
			'motorista_id' => 'Motorista',
			'endereco_origem' => 'Endereco Origem',
			'endereco_destino' => 'Endereco Destino',
			'data_inicio' => 'Data Inicio',
			'status' => 'Status',
			'previsao_chegada' => 'Previsao Chegada',
			'tarifa' => 'Tarifa',
			'data_finalizacao' => 'Data Finalizacao',
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

		$criteria->compare('passageiro_id',$this->passageiro_id);
		$criteria->compare('motorista_id',$this->motorista_id);
		$criteria->compare('endereco_origem',$this->endereco_origem,true);
		$criteria->compare('endereco_destino',$this->endereco_destino,true);
		$criteria->compare('data_inicio',$this->data_inicio,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('previsao_chegada',$this->previsao_chegada,true);
		$criteria->compare('tarifa',$this->tarifa);
		$criteria->compare('data_finalizacao',$this->data_finalizacao,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Corrida the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
