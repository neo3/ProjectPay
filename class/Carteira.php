<?php 

	/******************************************************************************
	* Class for pagamentos.carteira
	*******************************************************************************/

	class Carteira
	{
		/**
		* @var int
		* Class Unique
		*/
		private $Id;

		/**
		* @var string
		*/
		private $Usuario;

		/**
		* @var decimal
		*/
		private $Saldo;

		public function __construct($Usuario='')
		{
			$this->setUsuario($Usuario);
			$this->Load();
		}
		
		private function Load()
		{
			try
			{
				$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
						  mysqli_select_db($dblink, DB_BASE);
				
				$query = "SELECT * FROM CARTEIRA WHERE USUARIO = ".$this->getUsuario();
				$result = mysqli_query($dblink,$query);

				if($result) {
					while($row = mysqli_fetch_assoc($result))
						foreach($row as $key => $value)
						{
							$column_name = str_replace('-','_',$key);
							$column_name = str_replace('_', ' ', $column_name);
							$column_name = strtolower($column_name);
							$column_name = str_replace(' ', '', ucfirst(ucwords($column_name)));
							$this->{"set$column_name"}($value);
						}

					$response = array(
						'message'	=> 'Carteira carregada com sucesso!'
					);	
				}
				else
					$response = array(
						'message'	=> 'Falha ao carregar a carteira!'
					);
			}
			catch(Exception $ex)
			{
				$response = array(
					'message'	=> 'Falha ao carregar a carteira!',
					'error'		=> $ex
				);
			}

			if (is_resource($dblink))
				mysqli_close($dblink);
			
			return $response;
		}

		public function AtualizarSaldo($valor=0)
		{
			if (is_numeric($valor)) {
				try
				{
					$this->setSaldo($this->getSaldo() + $valor);
					
					$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
							  mysqli_select_db($dblink,DB_BASE);
				
					$query = "UPDATE CARTEIRA SET SALDO = ".$this->getSaldo()." WHERE USUARIO = ".$this->getUsuario();
			
					if(!mysqli_query($dblink,$query))
						$response = array(
							'message'	=> 'Falha na atualização do saldo!',
							'error'		=> mysqli_error($dblink)
						);
					else
						$response = array(
							'message'	=> 'Saldo atualizado!',
							'saldo'		=> $this->getSaldo()
						);
				}
				catch(Exception $ex)
				{
					$response = array(
						'message'	=> 'Falha na atualização do saldo!',
						'error'		=> $ex
					);
				}
		
				if (is_resource($dblink))
					mysqli_close($dblink);
			}
			else {
				$response = array(
					'message'	=> 'Falha na atualização do saldo!',
					'error'		=> 'Valor inválido.'
				);
			}
			
			return json_encode($response, JSON_UNESCAPED_UNICODE);
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

		public function setUsuario($Usuario='')
		{
			$this->Usuario = $Usuario;
			return true;
		}

		public function getUsuario()
		{
			return $this->Usuario;
		}

		public function setSaldo($Saldo='')
		{
			$this->Saldo = $Saldo;
			return true;
		}

		public function getSaldo()
		{
			return $this->Saldo;
		}

	} // END class Carteira

?>