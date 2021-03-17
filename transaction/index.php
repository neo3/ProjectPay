<?php
	try {
		include_once("./../functions/database.php");
		include_once("./../class/UsuarioPadrao.php");
		include_once("./../class/UsuarioLojista.php");
		include_once("./../class/Carteira.php");

		$json = file_get_contents('php://input');

		if ($json) {
			$request = json_decode($json);
			$value	 = $request->{'value'};
			$payer	 = new UsuarioPadrao($request->{'payer'});
			$payee	 = new UsuarioLojista($request->{'payee'});

			if(!is_numeric($value) || $value < 0) {
				$response = array(
					'message'	=> 'Falha na transação!',
					'error'		=> 'Valor inválido.'
				);
			}
			else if(strlen($payer->getCpf()) < 11) {
				$response = array(
					'message'	=> 'Falha na transação!',
					'error'		=> 'Pagador inválido.'
				);
			}
			else if(strlen($payee->getCnpj()) < 14) {
				$response = array(
					'message'	=> 'Falha na transação!',
					'error'		=> 'Beneficiário inválido.'
				);
			}
			else {
				$wallet	= new Carteira($request->{'payer'});
				$saldo	= $wallet->getSaldo();

				if(!$saldo) {
					$response = array(
						'message'	=> 'Falha na transação!',
						'error'		=> 'Carteira inválida.'
					);
				}
				else if($value > $saldo) {
					$response = array(
						'message'	=> 'Falha na transação!',
						'error'		=> 'Saldo insuficiente.'
					);
				}
				else {
					$response = $payer->Transfer($payee->getId(), $value);
				}
			}
		} else {
			$response = array(
				'message'	=> 'Falha na transação!',
				'error'		=> 'Informações inválidas.'
			);
		}
	} catch (Exception $ex) {
		$response = array(
			'message'	=> 'Falha na transação!',
			'error'		=> $ex
		);
	}

	echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>