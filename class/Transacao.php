<?php 

/******************************************************************************
* Class for pagamentos.transacao
*******************************************************************************/

class Transacao
{
	/**
	* @var int
	* Class Unique
	*/
	private $Id;

	/**
	* @var date
	*/
	public $Data;

	public $Origem;

	public $Destino;

	/**
	* @var decimal
	*/
	public $Valor;

	public $Status;

	public function __construct($Id=0)
	{
		$this->setId($Id);
		$this->Load();
	}

	public static function Historic()
	{
		$dblink = null;

		try
		{
			$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
			mysqli_select_db($dblink, DB_BASE);
			mysqli_set_charset($dblink,'utf8');
		}
		catch(Exception $ex)
		{
			echo "Não foi possível conectar em ".DB_HOST.":".DB_BASE."\n";
			echo "Erro: ".$ex->message;
			exit;
		}
		
		$query = "SELECT ID FROM TRANSACAO ORDER BY DATA DESC";
		$result = mysqli_query($dblink,$query);
		$lstTransacoes = array();

		while($row = mysqli_fetch_assoc($result))
		{
			foreach($row as $key => $value) 
			{
				$transacao = new Transacao($value);
			}
			$lstTransacoes[] = $transacao;
		}

		if(is_resource($dblink)) mysqli_close($dblink);
		return $lstTransacoes;
	}

	private function Load()
	{
        $dblink = null;

		try
		{
			$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
			mysqli_select_db($dblink, DB_BASE);
		}
		catch(Exception $ex)
		{
			echo "Não foi possível conectar em ".DB_HOST.":".DB_BASE."\n";
			echo "Erro: ".$ex->message;
			exit;
        }
        
		$query = "SELECT t.DATA,
					CASE
						WHEN EXISTS (SELECT u.NOME_COMPLETO FROM usuario_padrao u WHERE u.ID = t.ORIGEM) THEN (SELECT u.NOME_COMPLETO FROM usuario_padrao u WHERE u.ID = t.ORIGEM)
						ELSE (SELECT l.RAZAO_SOCIAL FROM usuario_lojista l WHERE l.ID = t.ORIGEM)
					END AS ORIGEM,
					CASE
						WHEN EXISTS (SELECT u.NOME_COMPLETO FROM usuario_padrao u WHERE u.ID = t.DESTINO) THEN (SELECT u.NOME_COMPLETO FROM usuario_padrao u WHERE u.ID = t.DESTINO)
						ELSE (SELECT l.RAZAO_SOCIAL FROM usuario_lojista l WHERE l.ID = t.DESTINO)
					END AS DESTINO,
					t.VALOR, s.descricao AS STATUS
					FROM TRANSACAO t, status_transacao s WHERE t.status = s.id AND t.id = ".$this->getId();

		$result = mysqli_query($dblink,$query);

		while($row = mysqli_fetch_assoc($result))
			foreach($row as $key => $value)
			{
				$column_name = str_replace('-','_',$key);
				$column_name = str_replace('_', ' ', $column_name);
				$column_name = strtolower($column_name);
				$column_name = str_replace(' ', '', ucfirst(ucwords($column_name)));
				$this->{"set$column_name"}($value);
			}

		if(is_resource($dblink)) mysqli_close($dblink);
	}

	public static function LoadUsuario()
	{
		session_start();

		$dblink = null;

		try
		{
			$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
			mysqli_select_db($dblink, DB_BASE);
		}
		catch(Exception $ex)
		{
			echo "Não foi possível conectar em ".DB_HOST.":".DB_BASE."\n";
			echo "Erro: ".$ex->message;
			exit;
        }
		
		$query = "SELECT ID FROM TRANSACAO WHERE ORIGEM = ".$_SESSION['id']." ORDER BY DATA DESC";
		$result = mysqli_query($dblink,$query);

		$lista_transacoes = array();

		while($row = mysqli_fetch_assoc($result))
		{
			foreach($row as $key => $value) 
			{
				$transacao = new Transacao($value);
			}
			$lista_transacoes[] = $transacao;
		}

		if(is_resource($dblink)) mysqli_close($dblink);

		return $lista_transacoes;
	}

    public function Exist()
	{
		$dblink = null;

		try
		{
			$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
			mysqli_select_db($dblink, DB_BASE);
		}
		catch(Exception $ex)
		{
			echo "Não foi possível conectar em ".DB_HOST.":".DB_BASE."\n";
			echo "Erro: ".$ex->message;
			exit;
        }
        
		$query = "SELECT * FROM TRANSACAO WHERE ID = '{$this->getId()}'";

		$result = mysqli_query($dblink,$query);

		if(is_resource($dblink)) mysqli_close($dblink);
		if(mysqli_num_rows($result)>0) return true;
		else return false;
	}

	public function Create()
	{
		if ($this->getOrigem() != '' && $this->getDestino() != '' && $this->getValor() != '') {
			try
			{
				$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
						  mysqli_select_db($dblink,DB_BASE);
			
				$query = "INSERT INTO TRANSACAO (DATA,ORIGEM,DESTINO,VALOR,STATUS) 
						  VALUES (now(), '".mysqli_real_escape_string($dblink,$this->getOrigem())."',
										 '".mysqli_real_escape_string($dblink,$this->getDestino())."',
										 ".mysqli_real_escape_string($dblink,$this->getValor()).",1);";
		
				if (mysqli_query($dblink,$query))
					$this->setId(mysqli_insert_id($dblink));
	
				$response = array(
					'message'	=> 'Transação iniciada!',
					'id'		=> $this->getId()
				);
			}
			catch(Exception $ex)
			{
				$response = array(
					'message'	=> 'Falha na criação da transação!',
					'error'		=> $e
				);
			}
		}
		else {
			$response = array(
				'message'	=> 'Falha na criação da transação!',
				'error'		=> 'Informações insuficientes.'
			);
		}
		
		if (is_resource($dblink))
			mysqli_close($dblink);

		return $response;
	}

    public function Authorization()
	{
		try
		{
			$opts = array('http' =>
				array(
					'method'  => 'GET',
					'timeout' => 10
				)
			);

			$timeout  = stream_context_create($opts);
			
			// Verificar autorização
			$response = @file_get_contents('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6', false, $timeout);

			if($response === FALSE) {
				$this->setStatus(5);
				$this->Update();
				
				$response = json_encode(array(
					'message'	=> 'Falha na autorização da transação!'
				));
			}
		}
		catch(Exception $ex)
		{
			$this->setStatus(5);
			$this->Update();
			
			$response = json_encode(array(
				'message'	=> 'Falha na autorização da transação!',
				'error'		=> $ex
			));
		}
		
		return $response;
	}

	public function Notification()
	{
		try
		{
			$opts = array('http' =>
				array(
					'method'  => 'GET',
					'timeout' => 10
				)
			);

			$timeout  = stream_context_create($opts);
			
			// Notificar Usuário
			$response = @file_get_contents('https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04', false, $timeout);

			if($response === FALSE) {
				$response = json_encode(array(
					'message'	=> 'Ocorreu uma falha durante o envio da transferência!'
				));
			}
		}
		catch(Exception $ex)
		{
			$response = json_encode(array(
				'message'	=> 'Ocorreu uma falha durante o envio da transferência!',
				'error'		=> $ex
			));
		}
		
		return $response;
	}

    public function Update()
	{
		try
		{
			$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
			mysqli_select_db($dblink,DB_BASE);
		
			$query = "UPDATE TRANSACAO SET STATUS = ".mysqli_real_escape_string($dblink,$this->getStatus()).", DATA = now() WHERE ID = ".$this->getId();
	
			if(!mysqli_query($dblink,$query))
				$response = array(
					'message'	=> 'Falha na atualização da transação!',
					'error'		=> mysqli_error($dblink)
				);
			else
				$response = array(
					'message'	=> 'Transação atualizada!',
					'status'	=> $this->getStatus()
				);
		}
		catch(Exception $ex)
		{
			$response = array(
				'message'	=> 'Falha na atualização da transação!',
				'error'		=> $ex
			);
		}
	
		if (is_resource($dblink))
			mysqli_close($dblink);
		
		return $response;
	}

	public function setId($Id='')
	{
		$this->Id = $Id;
		return true;
	}

	public function getId()
	{
		return $this->Id;
	}

	public function setData($Data='')
	{
		$this->Data = $Data;
		return true;
	}

	public function getData()
	{
		return $this->Data;
	}

	public function setOrigem($Origem='')
	{
		$this->Origem = utf8_encode($Origem);
		return true;
	}

	public function getOrigem()
	{
		return $this->Origem;
	}

	public function setDestino($Destino='')
	{
		$this->Destino = utf8_encode($Destino);
		return true;
	}

	public function getDestino()
	{
		return $this->Destino;
	}

	public function setValor($Valor='')
	{
		$this->Valor = $Valor;
		return true;
	}

	public function getValor()
	{
		return $this->Valor;
	}

	public function setStatus($Status='')
	{
		$this->Status = utf8_encode($Status);
		return true;
	}

	public function getStatus()
	{
		return $this->Status;
	}

} // END class Transacao

?>