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
	protected function renderJSON($data, $code_status)
	{
		header('Content-type: application/json', true, $code_status);
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
		return $this->renderJSON(array('data' => $data), 200);
	}


	/**
	 * Criar uma nova corrida
	 */
	public function actionCriaCorrida()
	{
		$token = getallheaders()['api-key'];
		$this->validaToken($token);
		$data = file_get_contents('php://input');
		$data = CJSON::decode($data);
		date_default_timezone_set('America/Sao_Paulo');

		//cria corrida
		$corrida = new Corrida();
		$corrida->passageiro_id = $this->validaPassageiro($data['passageiro']['id']); //valida passageiro
		$corrida->id = Corrida::model()->count() + 1;

		$origem = $data['origem']['endereco'];
		$destino = $data['destino']['endereco'];
		if ($this->validaOrigemDestino($origem, $destino)); //valida origem e destino
		$corrida->endereco_origem = $origem;
		$corrida->endereco_destino = $destino;

		$corrida->data_inicio = date('d-m-Y H:i', strtotime("now")); //data de solicitacao da corrida

		$distancia = $this->calcDistancia($data['origem']['lat'], $data['origem']['lng'], $data['destino']['lat'], $data['destino']['lng']);
		$previsao_chegada = $this->calcPrevisaoChegada($distancia); //calcula a previsao de chegada
		$previsao_chegada = $previsao_chegada[0]; // data da previsao de chegada
		$tempo_corrida = $previsao_chegada[1]; // tempo em minutos da corrida
		$corrida->previsao_chegada = $previsao_chegada;
		$corrida->data_finalizacao = $previsao_chegada; // este campo é atualizado novamente quando a corrida for finalizada

		$tarifa = $this->calcTarifa($distancia, $tempo_corrida); //calcula a tarifa
		$corrida->tarifa = $tarifa;

		$idMotorista = $this->atribuiMotorista();

		//atribui motorista
		if ($idMotorista != 0) {
			$corrida->motorista_id = $idMotorista;
			$motorista = Motorista::model()->findByPk($corrida->motorista_id); // dados do motorista escolhido
			$quantidadeCorridaDoMotorista = Corrida::model()->count('motorista_id = :motorista_id', array(':motorista_id' => $corrida->motorista_id)); //quantidade de corridas do motorista
			$corrida->status = 'Em andamento';
		} else {
			$corrida->motorista_id = null;
			$motorista = null;
			$quantidadeCorridaDoMotorista = null;
			$corrida->status = 'Não Atendida';
			$corrida->save();
			return $this->renderJSON(array('data' => 'Não há motoristas disponíveis para essa corrida'), 400);
		}

		$responseCorrida = array(
			'id' => $corrida->id,
			'previsao_chegada_destino' => $previsao_chegada,
		);

		$responseMotorista = array(
			'nome' => $motorista != null ? $motorista->nome : null,
			'placa' => $motorista != null ? $motorista->placa : null,
			'quantidade_corridas' => $motorista != null ? $quantidadeCorridaDoMotorista : null,

		);

		if (!$corrida->save()) { // identifica erro ao salvar corrida
			return $this->ErrorBadRequest('Erro ao criar corrida. Não foi possível salvar no banco de dados.');
		}

		return $this->renderJSON(
			array(
				'sucesso' => true,
				'corrida' => $responseCorrida,
				'motorista' => $responseMotorista,
				'corrida_completo' => $corrida,
				'motorista_completo' => $motorista,
			),
			200
		);
	}


	/**
	 * Finaliza corrida
	 */
	public function actionFinalizaCorrida()
	{
		$token = getallheaders()['api-key'];
		$this->validaToken($token);
		$data = file_get_contents('php://input');
		$data = CJSON::decode($data);
		date_default_timezone_set('America/Sao_Paulo');
		$this->validaDadosDeEntrada($data);
		
		$idCorrida = $data['corrida']['id'];
		$corrida = Corrida::model()->findByPk($idCorrida);
		if ($corrida == null) {
			return $this->ErrorBadRequest('Não foi encontrada a corrida com o id informado.');
		}
		
		$idMotorista = $data['motorista']['id'];
		if ($corrida->motorista_id !=  $idMotorista) 
			return $this->ErrorBadRequest('Motorista não corresponde à corrida.');

		if ($corrida->status == 'Em andamento') {
			$corrida->status = 'Finalizada';
			$corrida->data_finalizacao = date('d-m-Y H:i', strtotime("now"));
			$corrida->save();
			return $this->renderJSON(array('data' => 'Corrida finalizada com sucesso',), 200);
		}
		return $this->ErrorBadRequest('Corrida não está em andamento. Ja foi finalizada ou não foi atendida.');
	}

	/**
	 * Valida dados de entrada
	 * @param $data array dados de entrada
	 */
	public function validaDadosDeEntrada($data)
	{
		if (!isset($data['corrida']['id'])) { // verifica se o id da corrida foi passado
			return $this->ErrorBadRequest('Não foi informado o id da corrida.');
		} 
		if (!isset($data['motorista']['id'])) { // verifica se o id do motorista foi passado
			return $this->ErrorBadRequest('Não foi informado o id do motorista.');
		}
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
			return $this->ErrorBadRequest('Passageiro não encontrado');
		}

		// verifica se passageiro está ativo
		$statusPassageiro = Yii::app()->db->createCommand()
			->select('*')
			->from('tbl_passageiro')
			->where('id=:id AND status=:status', array(':id' => $idPassageiro, ':status' => 'A'))
			->queryRow();
		if (!$statusPassageiro) {
			return $this->ErrorBadRequest('Passageiro não está ativo');
		}

		// verifica se passageiro possui corrida em andamento
		$validation = Yii::app()->db->createCommand()
			->select('*')
			->from('tbl_corrida')
			->where('passageiro_id=:passageiro_id AND status=:status', array(':passageiro_id' => $idPassageiro, ':status' => 'Em andamento'))
			->queryRow();

		if ($validation)
			return $this->ErrorBadRequest('Passageiro já está em uma corrida');

		return $idPassageiro;
	}


	/**
	 * Valida se o origem e o destino são válidos.
	 * @param string $origem Endereço de origem
	 * @param string $destino Endereço de destino
	 * @return boolean
	 */
	public function validaOrigemDestino($origem, $destino)
	{
		if ($origem == $destino)
			return $this->ErrorBadRequest('Origem e destino não podem ser iguais');
		return true;
	}

	/**
	 * Mensagem de erro
	 */
	public function ErrorBadRequest($msg)
	{
		return $this->renderJSON(array(
			'sucesso' => false,
			'erro' => $msg,
		), 400);
	}

	/**
	 * Calcula entre dois pontos de latitude e longitude.
	 */
	function calcDistancia($lat1 = '', $lon1 = '', $lat2 = '', $lon2 = '')
	{

		if ($lat1 && $lon1 && $lat2 && $lon2) {
			$distancia = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon1 - $lon2));
			$distancia = acos($distancia);
			$distancia = rad2deg($distancia);
			$kilometro = $distancia * 60 * 1.1515 * 1.609344;
			return round($kilometro, 2);
		}

		return $this->ErrorBadRequest('Erro ao calcular distância');
	}

	/**
	 * Calcula previsao de chegada da corrida
	 * @param float $distancia Distancia em km
	 */
	function calcPrevisaoChegada($distancia)
	{
		date_default_timezone_set('America/Sao_Paulo');
		if ($distancia < 0.1) {
			return $this->ErrorBadRequest('Distância muito curta');
		}
		$distancia = $distancia * 1000;
		$previsao_chegada = ($distancia / 200) + 3;

		if ($previsao_chegada > 480)
			return $this->ErrorBadRequest('Previsão de chegada muito longa. A corrida não pode durar mais de 8 horas');

		$tempo = round($previsao_chegada, 0);
		$previsao_chegada = strtotime("+ $tempo minutes");
		$previsao_chegada = date('d-m-Y H:i', $previsao_chegada);

		// return $this->renderJSON(array(
		// 	'sucesso' => true,
		// 	'previsao_chegada' => $previsao_chegada,
		// ), 200);
		return array($previsao_chegada, $tempo);

		//	return ;
	}


	/**
	 * Calcula tarifa da corrida
	 * @param float $distancia Distancia da corrida
	 * @param int $previsao_chegada Previsao de chegada da corrida
	 */
	function calcTarifa($distancia, $previsao_chegada)
	{
		$tarifa = $distancia * 2 + $previsao_chegada * 0.5 + 5;
		return $tarifa;
	}

	/**
	 * Atribui motorista a corrida 
	 * Procura na tabela motorista e na tabela corrida, se existe motorista 
	 */
	public function atribuiMotorista()
	{
		// Busca por motoristas que ja realizam corrida, mas nao possuem corrida em andamento
		$MotoristaOcupados = Yii::app()->db->createCommand()
			->selectDistinct('motorista_id')
			->from('tbl_motorista')
			->join('tbl_corrida', 'tbl_motorista.status = "A" ')
			->where('tbl_corrida.status = :status', array(':status' => 'Em andamento'))
			->queryAll();

		for ($i = 0; $i < count($MotoristaOcupados); $i++) {
			$arrayMotoristaOcupados[] = $MotoristaOcupados[$i]['motorista_id'];
		}

		// Pegar todos motorista que possuem status ativo
		$motoristasStatusAtivo = Yii::app()->db->createCommand()
			->select('id')
			->from('tbl_motorista')
			->where('status = :status', array(':status' => 'A'))
			->queryAll();
		for ($i = 0; $i < count($motoristasStatusAtivo); $i++) {
			$arrayMotoristasStatusAtivo[] = $motoristasStatusAtivo[$i]['id'];
		}

		//Quantidade de motoristas
		$qtdMotoristas = Motorista::model()->count();
		$rangeMotorista = range(1, $qtdMotoristas);

		$removidoEmAndamento = array_diff($arrayMotoristaOcupados, $rangeMotorista);
		$motoristasDisponiveis = array_intersect($arrayMotoristasStatusAtivo, $removidoEmAndamento);

		return $motoristasDisponiveis == null ? null : $motoristasDisponiveis[0];
	}
	/**
	 * Valida o token de API
	 * @param string $token Token de autorização
	 */
	public function validaToken($token)
	{
		$stop = true;
		$file = fopen("protected/config/secret.txt", 'rb');
		while (false !== ($line = fgets($file))) {
			if (trim($line) === $token) {
				$stop = true;
				fclose($file);
				return;
			}
		}
		fclose($file);
		return $this->ErrorBadRequest('Token inválido');
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
