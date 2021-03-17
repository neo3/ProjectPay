<?php 

	/******************************************************************************
	* Class for ProjectPay.Usuario
	*******************************************************************************/

	abstract class Usuario
	{
		/**
		* @var int
		* Class Unique
		*/
		private $Id;

		/**
		* @var string
		*/
		private $Email;

		/**
		* @var string
		*/
		private $Senha;

		/**
		* @var datetime
		*/
		private $Criado;

		/**
		* @var int
		*/
		private $Ativo;

		protected function Login()
		{
			try
			{
				$dblink = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
						  mysqli_select_db($dblink, DB_BASE);

				if($this->getId())
					$query = "SELECT * FROM USUARIO WHERE ID = ".$this->getId();
				else if($this->getEmail() && $this->getSenha())
					$query = "SELECT * FROM USUARIO WHERE EMAIL = '".$this->getEmail()."' AND SENHA = PASSWORD('".$this->getSenha()."')";
				
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
						'message'	=> 'Login realizado com sucesso!',
						'id'		=> $this->getId()
					);	
				}	
				else
					$response = array(
						'message'	=> 'Falha no login!',
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

		public function setEmail($Email='')
		{
			$this->Email = $Email;
			return true;
		}

		public function getEmail()
		{
			return $this->Email;
		}

		public function setSenha($Senha='')
		{
			$this->Senha = $Senha;
			return true;
		}

		public function getSenha()
		{
			return $this->Senha;
		}

		public function setCriado($Criado='')
		{
			$this->Criado = $Criado;
			return true;
		}

		public function getCriado()
		{
			return $this->Criado;
		}

		public function setAtivo($Ativo='')
		{
			$this->Ativo = $Ativo;
			return true;
		}

		public function getAtivo()
		{
			return $this->Ativo;
		}

	} // END class Usuario

?>