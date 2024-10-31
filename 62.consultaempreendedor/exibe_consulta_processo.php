<?php
/*
Autor: Rodrigo Henning C. Rodrigues
Email: rodrigohenning@hotmail.com
Data: 12/10/2016

consulta empreendedor
**********************************************************************************

***********************************************************************************			
*/


ini_set('default_charset','iso-8859-1');


if(isset($_GET['clientecpf'])){ //segurança da pagina
}else{
	if(isset($_GET['clientecnpj'])){
	}else{
		header('location: consultaempreendedor.php');
	}
}

$clb_cnpj_get= $_GET['clientecnpj'];
$clb_cpf_get= $_GET['clientecpf'];

$mes = date("m");
$dia =  date("j");
$ano = date("Y");

include 'includes/conexaoPDO.php';


if (isset($clb_cpf_get)) {

	$sql = "  SELECT DISTINCT "
	. "  dadosbasico.cliente.clb_codigo, "
	. "  dadosbasico.cliente.clb_razaosocial, "
	. "  dadosbasico.cliente.clb_cnpj,"
	. "  dadosbasico.cliente.clb_cpf"
	. "	 FROM "
	. "	 dadosbasico.cliente "
	. "	 WHERE "
	. "	 dadosbasico.cliente.clb_excluido_s_n = 'N' and"
	. "	 dadosbasico.cliente.clb_cpf='".$clb_cpf_get."'"
	. "	 ORDER BY "
	. "	 dadosbasico.cliente.clb_razaosocial";
	
	try {

		$stmt = $db->query($sql);
		$result = $stmt->setFetchMode(PDO::FETCH_NUM);

		while ($row = $stmt->fetch()) { 
			$clb_codigo = $row[0];
			$clb_razaosocial =  trim($row[1]); 
			$clb_cnpj =  trim($row[2]);   
			$clb_cpf =   trim($row[3]);						
		}
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}

}elseif (isset($clb_cnpj_get)) {
	
	$sql = "  SELECT DISTINCT "
	. "  dadosbasico.cliente.clb_codigo, "
	. "  dadosbasico.cliente.clb_razaosocial, "
	. "  dadosbasico.cliente.clb_cnpj,"
	. "  dadosbasico.cliente.clb_cpf"
	. "	 FROM "
	. "	 dadosbasico.cliente "
	. "	 WHERE "
	. "	 dadosbasico.cliente.clb_excluido_s_n = 'N' and"
	. "	 dadosbasico.cliente.clb_cnpj='".$clb_cnpj_get."'"
	. "	 ORDER BY "
	. "	 dadosbasico.cliente.clb_razaosocial";
	
	try {

		$stmt = $db->query($sql);
		$result = $stmt->setFetchMode(PDO::FETCH_NUM);

		while ($row = $stmt->fetch()) { 
			$clb_codigo = $row[0];
			$clb_razaosocial =  trim($row[1]); 
			$clb_cnpj =  trim($row[2]);   
			$clb_cpf =   trim($row[3]);						
		}
	}
	catch (PDOException $e) {
		print $e->getMessage();
	}
}

?>

<html >
<head>
	

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Sistema Estadual de Informa&ccedil;&otilde;es Ambientais - SEIAM/AC (IMAC)</title>

	<link href="includes/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="includes/bootstrap/css/styleLogin.css" rel="stylesheet">
</head>

<body role="">

	<div class="container" style="margin-top:0px" >
		<div class="row">
			<div class="col-sm-5 col-md-10 col-sm-offset-3 col-md-offset-1">
				<div class="panel panel-default">
					
					<div class="panel-body">

						<form role="form" action="#.php" method="POST">
							<fieldset>
<!--             <div class="row">
              <div class="center-block"> <center><img class="profile-img" src="banner/desenv/seiam.png" class="img-responsive" alt=""> </center></div>
              <hr>
          </div> -->
          <div class="row">
          	<div class="col-sm-15 col-md-10  col-md-offset-1 ">
          		<div >                         
          			<p>
          				<address >                 
          					<p><b>Raz&atilde;o Social/ Nome:</b><?php echo" ".$clb_razaosocial;?></p>
          					<p><b>CPF/CNPJ:</b> <?php echo" ".$clb_cpf."".$clb_cnpj;?></p> 
          				</address>
          			</p>
          			<hr align="center" width="100%" size="1" color="green">
          		</div>
          	</div>



          	<div>
          		<div class"container" class="col-sm-15 col-md-12  col-md-offset-0 " >
          			<table border='2' class="table table-hover" >

          				<tr bgcolor="#8db561" >
          					<td><font size='2' color= '#000000'><b>N&uacute;mero Processo &nbsp;&nbsp;&nbsp;</b></font></td>
          					<td><font size='2' color= '#000000'><center><b>Descri&ccedil;&atilde;o &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></center></font></td>
          					<td><font size='2' color= '#000000'><b>Verificar</b></font></td>
          				</tr>

          				<?php

          				$sql = "  SELECT DISTINCT "
          				. " licenciamento.processo.proc_codigo," 
          				. "  licenciamento.processo.proc_numero," 
          				. "  licenciamento.processo.proc_descricao" 
          				. "  FROM "
          				. "  licenciamento.processo "
          				. "  LEFT OUTER JOIN dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb ON "
          				. "  (licenciamento.processo.proc_codigo = dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb.proc_codigo)"
          				. "  LEFT OUTER JOIN dadosbasico.empreendimento_x_cliente_x_processo ON "
          				. "  (licenciamento.processo.proc_codigo = dadosbasico.empreendimento_x_cliente_x_processo.proc_codigo) "
          				. "  WHERE "
          				. "  licenciamento.processo.proc_excluido_s_n = 'N' AND "
          				. "  (( dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb.clb_codigo = '" . $clb_codigo. "') or" 
          					. "  (dadosbasico.empreendimento_x_cliente_x_processo.clb_codigo = '" . $clb_codigo. "' ))"
. "  ORDER BY "
. "  licenciamento.processo.proc_numero";

try {

	$stmt = $db->query($sql);
	$result = $stmt->setFetchMode(PDO::FETCH_NUM);

	while ($row = $stmt->fetch()) {

		if (isset($clb_cpf_get)) {
			echo "<tr><td><font size=2 color=#000000>".$row[1] ."</font></td><td><font size=2 color=#000000>". trim($row[2]) ."</font></td><td><font size=2 color=#000000><a href=exibe_consulta_processo.php?pro_cod=".$row[0]."&clientecpf=".$clb_cpf.">Status</a></font></td></tr></div></div>";
			
		}else{
			echo "<tr><td><font size=2 color=#000000>".$row[1] ."</font></td><td><font size=2 color=#000000>". trim($row[2]) ."</font></td><td><font size=2 color=#000000><a href=exibe_consulta_processo.php?pro_cod=".$row[0]."&clientecnpj=".$clb_cnpj_get.">Status</a></font></td></tr></div></div>";

		}		
	}

}
catch (PDOException $e) {
	print $e->getMessage();
}



				// if (isset($_GET['pro_cod'])) {

				// 		echo '<label class="text-danger">'.$_GET['pro_cod'].'</label>';  

				// 		$promo=$_GET['pro_cod'];
				// }
?>
</table>
</div>
</div>       
</form>
</div>

<!-- Aqui colocar resposta do status -->

<?php 	

if (isset($_GET['pro_cod'])) {
	$promo=$_GET['pro_cod'];
	
	$sql_02 = "   SELECT "
	. "  to_char(licenciamento.processomovimentacao.promo_datahoramovimentacao, "
		. " 'DD Mon YYYY HH:MM') AS promo_datahoramovimentacao, "
. "  geral.status.stat_descricao, "
. "  licenciamento.processomovimentacao.promo_texto ,"
. "  geral.funcionario.emp_codigo || ' - ' || "
. " geral.area.are_sigla || ' - ' || "
. " geral.funcionario.fun_nome AS destinatario, "
. " age(licenciamento.processomovimentacao.promo_datahorarecebimento, licenciamento.processomovimentacao.promo_datahoramovimentacao) as tempodecorridoreal," 
. " age(current_timestamp, (SELECT MIN(y.promo_datahoramovimentacao) AS data1amov FROM licenciamento.processomovimentacao y "
	. "WHERE y.proc_codigo = licenciamento.processomovimentacao.proc_codigo)) as tempodecorridototal "
. " FROM "
. " licenciamento.processomovimentacao "
. " INNER JOIN licenciamento.processo ON "
. " (licenciamento.processomovimentacao.proc_codigo=licenciamento.processo.proc_codigo) AND "
. " (licenciamento.processo.proc_codigo = '".$promo."') "
. " INNER JOIN geral.fluxoprocesso ON "
. " (licenciamento.processomovimentacao.profl_sequencia=geral.fluxoprocesso.profl_sequencia) "
. " INNER JOIN geral.status ON "
. " (geral.fluxoprocesso.profl_statcodigo_futuro=geral.status.stat_codigo)"
. " INNER JOIN geral.funcionario ON "
. " (licenciamento.processomovimentacao.promo_usuariodestino=geral.funcionario.fun_codigo) "
. " INNER JOIN geral.area ON "
. " (geral.funcionario.are_codigo=geral.area.are_codigo) "
. "ORDER by licenciamento.processomovimentacao.promo_sequencial , licenciamento.processomovimentacao.promo_datahoramovimentacao "; 

try {

	$stmt = $db->query($sql_02);
	$result = $stmt->setFetchMode(PDO::FETCH_NUM);
	$id=0;
	while ($row = $stmt->fetch()) {

		$ultimostatus[] = $row[1];
		$id++;
	}
}
catch (PDOException $e) {
	print $e->getMessage();
}
//echo $id;
$sql_Processo = " SELECT DISTINCT "
. "  licenciamento.processo.proc_numero" 
. "  FROM "
. "  licenciamento.processo "
. "  LEFT OUTER JOIN dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb ON "
. "  (licenciamento.processo.proc_codigo = dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb.proc_codigo)"
. "  LEFT OUTER JOIN dadosbasico.empreendimento_x_cliente_x_processo ON "
. "  (licenciamento.processo.proc_codigo = dadosbasico.empreendimento_x_cliente_x_processo.proc_codigo) "
. "  WHERE "
. "  licenciamento.processo.proc_excluido_s_n = 'N' AND "
. "  licenciamento.processo.proc_codigo= '".$promo."'";
try {

	$stmt = $db->query($sql_Processo);
	$result = $stmt->setFetchMode(PDO::FETCH_NUM);
	
	while ($row = $stmt->fetch()) {  
		$numero_processo = $row[0];
	}
}
catch (PDOException $e) {
	print $e->getMessage();
}


?>
<div>
	<div class"container" class="col-sm-5 col-md-12 col-sm-offset-1 col-md-offset-0" >
		<p>   <hr align="center" width="100%" size="1" color="green">
			<tr>
				<td><p align="Left"><b>Processo:</b><?php echo" ".$numero_processo;?></p></td>
				<td><p align="Left"><b><?php echo" ".$id;?> Status</b></p></td>
				<?php
				$n=0;
				 while ($n <= $id ) { 
				   ?>
					<td><p align="Left"><b></b><?php echo" ".$ultimostatus[$n];?></p></td>
	                <?php
	                 $n++;
	                 } 
	                 ?>
				
			</tr>                  
		</p>
	</div> 
</div>
<?php 
}
?>




</div> 
</div>
</div> 
</div>

</body>
</html>