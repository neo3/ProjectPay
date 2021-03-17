<?php 

/******************************************************************************
* Class for pagamentos.status_transacao
*******************************************************************************/

class StatusTransacao
{
	/**
	* @var int
	* Class Unique
	*/
	private $Id;

	/**
	* @var string
	*/
	private $Descricao;

	public function __construct($Id='')
	{
		$this->setId($Id);
		$this->Load();
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
        
		$query = "SELECT * FROM STATUS_TRANSACAO WHERE ID = {$this->getId()}";
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

	public function setId($Id='')
	{
		$this->Id = $Id;
		return true;
	}

	public function getId()
	{
		return $this->Id;
	}

	public function setDescricao($Descricao='')
	{
		$this->Descricao = $Descricao;
		return true;
	}

	public function getDescricao()
	{
		return $this->Descricao;
	}

} // END class StatusTransacao

?>