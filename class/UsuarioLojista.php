<?php
	include_once("./../class/Usuario.php");

	/******************************************************************************
	* Class for ProjectPay.Usuario_Lojista
	*******************************************************************************/

	class UsuarioLojista extends Usuario
	{
		/**
		* @var string
		*/
		private $Cnpj;

		/**
		* @var string
		*/
		private $RazaoSocial;

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
				
				$query = "SELECT CNPJ, RAZAO_SOCIAL FROM USUARIO_LOJISTA WHERE ID = ".$this->getId();
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

		public function setCnpj($Cnpj='')
		{
			$this->Cnpj = $Cnpj;
			return true;
		}

		public function getCnpj()
		{
			return $this->Cnpj;
		}

		public function setRazaoSocial($RazaoSocial='')
		{
			$this->RazaoSocial = $RazaoSocial;
			return true;
		}

		public function getRazaoSocial()
		{
			return $this->RazaoSocial;
		}

	} // END class UsuarioLojista

?>