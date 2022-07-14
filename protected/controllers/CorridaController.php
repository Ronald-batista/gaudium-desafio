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

	/**
	 * Testar retorno de dados em JSON.
	 */

	public function actionTeste()
	{
		// $method = $_SERVER['REQUEST_METHOD'];

		$data = file_get_contents('php://input');
		$data = CJSON::decode($data);
		return $this->renderJSON(array('data' => $data));
	}

	/**
	 * Criar uma nova corrida
	 */
	public function actionCriaCorrida()
	{
		$data = file_get_contents('php://input');
		$data = CJSON::decode($data);
		date_default_timezone_set('America/Sao_Paulo');

		
		//cadastra corrida
		$corrida = new Corrida();
		$corrida->passageiro_id = $this->validaPassageiro($data['passageiro']['id']); //valida passageiro
		
		$origem = $data['origem']['endereco'];
		$destino = $data['destino']['endereco'];

		if ($this->validaOrigemDestino($origem, $destino)); //valida origem e destino
		$corrida->endereco_origem = $data['origem']['endereco'];
		$corrida->endereco_destino = $data['destino']['endereco'];
		
		$corrida->data_inicio = date('d/h/Y - g:i a');
		$distancia = $this->calcPrevisaoChegada($data['origem']['lat'], $data['origem']['lng'], $data['destino']['lat'], $data['destino']['lng']); //calcula distancia



		$response = array(
			'id' => $corrida->id,
			'corrida' => $corrida,
			'distancia' => $distancia,
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


	/**
	 * Valida se o passageiro existe, está ativo e não possui outra corrida em andamento.
	 * @param int $idPassageiro ID do passageiro
	 * @return int ID do passageiro
	 */
	public function validaPassageiro($idPassageiro)
	{
		// verifica se passageiro existe
		$passageiro = Passageiro::model()->findByPk($idPassageiro);
		if ($passageiro === null) {
			return $this->ERROR('Passageiro não encontrado');
		}

		// verifica se passageiro está ativo
		$statusPassageiro = Yii::app()->db->createCommand()
		->select('*')
		->from('tbl_passageiro')
		->where('id=:id AND status=:status', array(':id' => $idPassageiro, ':status' => 'A'))
		->queryRow();
		if (!$statusPassageiro) {
			return $this->ERROR('Passageiro não está ativo');
		}

		// verifica se passageiro possui corrida em andamento
		$validation = Yii::app()->db->createCommand()
			->select('*')
			->from('tbl_corrida')
			->where('id=:id AND status=:status', array(':id' => $idPassageiro, ':status' => 'Em andamento'))
			->queryRow();
		if ($validation)
			return $this->ERROR('Passageiro já está em uma corrida');

		return $idPassageiro;
	}

	public function validaOrigemDestino($origem, $destino)
	{
		if ($origem == $destino)
			return $this->ERROR('Origem e destino não podem ser iguais');
		return true;
	}

	public function ERROR($msg)
	{
		
		return $this->renderJSON(array(
			'sucesso' => false,
			'erro' => $msg,
		));
	}

	/**
	 * Calcula entre dois pontos de latitude e longitude.
	 */
	function calcDistancia( $lat1 = '', $lon1 = '' , $lat2 = '' , $lon2 = '' ) 
	{

		if( $lat1 && $lon1 && $lat2 && $lon2 ) {
			$distancia = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad( $lon1 - $lon2 ));
			$distancia = acos($distancia);
			$distancia = rad2deg($distancia);
			$kilometro = $distancia* 60 * 1.1515 * 1.609344;
			return round($kilometro,2) ;

		} 

		return $this->ERROR('Erro ao calcular distância');
	
	
	}

	/**
	 * Calcula previsao de chegada da corrida
	 */
	function calcPrevisaoChegada($latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino)
	{
		$distancia = $this->calcDistancia($latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino);
		if ($distancia < 0.1) {
			return $this->ERROR('Distância muito curta');
		}
		$distancia = $distancia * 1000;
		$previsao_chegada = ($distancia / 200) + 3;
		
		if ($previsao_chegada > 480)
			return $this->ERROR('Previsão de chegada muito longa. A corrida não pode durar mais de 8 horas');

		return $previsao_chegada;

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
