<?php
    if(!defined('IndexPage'))
        header('Location:./');
?>
<!DOCTYPE html>
<html>
<head>
	<link href="./css/admin.css" rel="stylesheet">
	<script type="text/javascript" src="./js/admin.js"></script>
</head>
<body>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col col-sm-8">
					<h3 class="card-title">Transferências</h3>
				</div>
				<div class="col col-sm-4 text-right">
					
				</div>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-striped table-bordered table-list" id="pnlTransferencias">
				<thead>
					<tr>
						<th>Data</th>
						<th>Beneficiário</th>
						<th>Valor</th>
						<th>Status</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	<div id="loadingModal" class="modal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog modal-sm vertical-align-center">
				<div class="modal-content">
					<div class="modal-body text-center">
						<div class="loader"></div>
						<br />
						<span>Carregando...</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="transferirModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Transferir</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="./transaction/" method="POST">
					<input type="hidden" id="funcionario" name="id" value="" />
					<div class="modal-body" style="padding: 0;">
						
					</div>
					<div class="modal-footer">
						<button class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
						<button class="btn btn-warning" onclick="confirmar();">Confirmar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>