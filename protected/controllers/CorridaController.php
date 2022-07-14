<?php

class CorridaController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * Return data to browser as JSON and end application.
	 * @param array $data
	 */
	protected function renderJSON($data)
	{
		header('Content-type: application/json');
		echo CJSON::encode($data);

		foreach (Yii::app()->log->routes as $route) {
			if ($route instanceof CWebLogRoute) {
				$route->enabled = false; // disable any weblogroutes
			}
		}
		Yii::app()->end();
	}

	public function actionTeste()
	{
		// $method = $_SERVER['REQUEST_METHOD'];

		$data = file_get_contents('php://input');
		$data = CJSON::decode($data);
		return $this->renderJSON(array('data' => $data));
	}

	public function actionCriaCorrida()
	{
		$data = file_get_contents('php://input');
		$data = CJSON::decode($data);
		date_default_timezone_set('America/Sao_Paulo');




		//cadastra corrida
		$corrida = new Corrida();
		$corrida->passageiro_id = $this->validaPassageiro($data['passageiro']['id']); //valida passageiro
		$corrida->endereco_origem = $data['origem']['endereco'];
		$corrida->endereco_destino = $data['destino']['endereco'];
		$corrida->data_inicio = date('d/h/Y - g:i a');


		$response = array(
			'id' => $corrida->id,
			'corrida' => $corrida,
		);
		// $corrida->save();
		return $this->renderJSON(
			array(
				'sucesso' => true,
				'corrida' => $response,
				'motorista' => 'Preencher com dados do motorista'
			)
		);
	}

	public function validaPassageiro($passageiro)
	{
		//$passageiro = Corrida::model()->findByPk($passageiro); // TODO: Precisa de uma estrutura para saber que uma corrida esta em andamento
		// $query = $Yii::app()->db->createCommand();
		// $passageiro = $query->select('*')->from('corrida')->where('passageiro_id = :passageiro_id', array(':passageiro_id' => $passageiro));
		//$passageiro = $passageiro->queryRow();
		//$passageiro = $query->select('*')->from('tbl_corrida');
		//$passageiro = $passageiro->where('status = :status')->addParams([':status' => 'Em andamento']);
		$validation = Yii::app()->db->createCommand()
			->select('*')
			->from('tbl_corrida')
			->where('id=:id AND status=:status', array(':id' => $passageiro, ':status' => 'Em andamento'))
			->queryRow();
			
		if ($validation)
			return $this->erroCorrida('Passageiro já está em uma corrida');
		return $passageiro;
	}

	public function erroCorrida($msg)
	{
		return $this->renderJSON(array(
			'sucesso' => false,
			'erro' => $msg,
		));
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
