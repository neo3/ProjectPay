<?php
	include_once("./../class/Usuario.php");

	/******************************************************************************
	* Class for ProjectPay.Usuario_Padrao
	*******************************************************************************/

	class UsuarioPadrao extends Usuario
	{
		/**
		* @var string
		*/
		private $Cpf;

		/**
		* @var string
		*/
		private $NomeCompleto;

		public function __construct($Id='',$Email='',$Senha='')
		{
			$this->setId($Id);
			$this->setEmail($Email);
			$this->setSenha($Senha);

			$this->Load();
		}

		private function Load()
		{
			try
			{
				$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
						  mysqli_select_db($dblink, DB_BASE);

				parent::Login();
				
				$query = "SELECT CPF, NOME_COMPLETO FROM USUARIO_PADRAO WHERE ID = ".$this->getId();
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
						'message'	=> 'Usuário carregado com sucesso!',
						'id'		=> $this->getId()
					);	
				}
				else
					$response = array(
						'message'	=> 'Falha ao carregar o usuário!',
						'error'		=> mysqli_error($dblink)
					);
			}
			catch(Exception $ex)
			{
				$response = array(
					'message'	=> 'Falha no login!',
					'error'		=> $ex
				);
			}
		
			if (is_resource($dblink))
				mysqli_close($dblink);
			
			return $response;
		}

		public function Transfer($beneficiario='', $valor='')
		{
			try {
				if ($beneficiario != '' && $valor != '') {
					include_once("./../class/Carteira.php");
					include_once("./../class/Transacao.php");

					$carteiraBeneficiario = new Carteira($beneficiario);
					$carteiraPagador = new Carteira($this->getId());
					$response = $carteiraPagador->AtualizarSaldo($valor*(-1));
					$request = json_decode($response);
					$status = $request->{'message'};

					if (strtoupper($status) == 'SALDO ATUALIZADO!') {
						$transacao = new Transacao();

						$transacao->setOrigem($this->getId());
						$transacao->setDestino($beneficiario);
						$transacao->setValor($valor);
	
						$response = $transacao->Create();
						
						if ($transacao->getId() > 0) {
							$response = $transacao->Authorization();
							$request = json_decode($response);
							$status = $request->{'message'};

							if (strtoupper($status) != 'AUTORIZADO') {
								$transacao->setStatus(4);
								$transacao->Update();
								$carteiraPagador->AtualizarSaldo($valor);
				
								$response = array(
									'message'	=> 'Transação não autorizada!',
									'status'	=> $transacao->getStatus()
								);
							}
							else {
								$transacao->setStatus(2);
								$transacao->Update();
								$carteiraBeneficiario->AtualizarSaldo($valor);

								$response = $transacao->Notification();
								$request = json_decode($response);
								$status = $request->{'message'};

								if (strtoupper($status) == 'ENVIADO') {
									$transacao->setStatus(3);
									$transacao->Update();

									$response = array(
										'message'	=> 'Transferência realizada.',
										'saldo'		=> $carteiraPagador->getSaldo()
									);
								}
							}
						}
					}
				}
				else {
					$response = array(
						'message'	=> 'Falha na transferência!',
						'error'		=> 'Informações insuficientes.'
					);
				}
			} catch (Exception $ex) {
				$response = array(
					'message'	=> 'Falha na transferência!',
					'error'		=> $ex
				);
			}	
			
			return $response;
		}

		public function setCpf($Cpf='')
		{
			$this->Cpf = $Cpf;
			return true;
		}

		public function getCpf()
		{
			return $this->Cpf;
		}

		public function setNomeCompleto($NomeCompleto='')
		{
			$this->NomeCompleto = $NomeCompleto;
			return true;
		}

		public function getNomeCompleto()
		{
			return $this->NomeCompleto;
		}

	} // END class UsuarioPadrao

?>