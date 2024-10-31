<?php
/*
Autor: Rodrigo Henning C. Rodrigues
Email: rodrigohenning@hotmail.com
Data: 15/08/2017
Objetivo: consultar processos portal de transparencia do empreendedor para implantação no web site
**********************************************************************************

***********************************************************************************			
*/

$cpf_cliente= $_POST['cpf'];
$cnpj_cliente= $_POST['cnpj'];

// echo "<br>CPF".$cpf_cliente;
// echo "<br>CNPJ".$cnpj_cliente;

include 'includes/conexaoPDO.php';


if (!empty($cpf_cliente)) {

$sql_verifica_cpf = "  SELECT DISTINCT "
			        . "  dadosbasico.cliente.clb_codigo, "
				    . "  dadosbasico.cliente.clb_razaosocial, "
				    . "  dadosbasico.cliente.clb_cpf"
					. "	 FROM "
					. "	 dadosbasico.cliente "
					. "	 WHERE "
					. "	 dadosbasico.cliente.clb_excluido_s_n = 'N' and"
					. "	 dadosbasico.cliente.clb_cpf='".$cpf_cliente."'"
					. "	 ORDER BY "
					. "	 dadosbasico.cliente.clb_razaosocial";

try {

  $stmt = $db->query($sql_verifica_cpf);
  $result = $stmt->setFetchMode(PDO::FETCH_NUM);

  while ($row = $stmt->fetch()) { 
         		$clb_codigo = $row[0];
				$clb_razaosocial =  trim($row[1]);  
				$clb_cpf =   trim($row[2]);
		
  }
}
catch (PDOException $e) {
  print $e->getMessage();
}

	if (!empty($clb_codigo)) {

		// entra para puxar processos vinculados no cliente 
						$sql_porc = "  SELECT DISTINCT "
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

						  $stmt = $db->query($sql_porc);
						  $result = $stmt->setFetchMode(PDO::FETCH_NUM);

						  while ($row = $stmt->fetch()) { 
						         		$proc_cod = $row[0];
										$proc_numero=  trim($row[1]);  
										$proc_descricao =   trim($row[2]);
									
						  }
						}
						catch (PDOException $e) {
						  print $e->getMessage();
						}

					echo "<meta http-equiv=\"refresh\" content=\"3;url=exibe_consulta_processo.php?clientecpf=".$clb_cpf."\">"; 

	}else{

			       $log="Nenhum Registro Vinculado ao CPF";      
                   echo "<meta http-equiv=\"refresh\" content=\"0;url=consultaempreendedor.php?log=".$log."\">"; 

	}


}else{     

						$sql_verifica_cnpj = "  SELECT DISTINCT "
						        . "  dadosbasico.cliente.clb_codigo, "
							    . "  dadosbasico.cliente.clb_razaosocial, "
							    . "  dadosbasico.cliente.clb_cnpj"
								. "	 FROM "
								. "	 dadosbasico.cliente "
								. "	 WHERE "
								. "	 dadosbasico.cliente.clb_excluido_s_n = 'N' and"
								. "	 dadosbasico.cliente.clb_cnpj='".$cnpj_cliente."'"
								. "	 ORDER BY "
								. "	 dadosbasico.cliente.clb_razaosocial";

			try {

			  $stmt = $db->query($sql_verifica_cnpj);
			  $result = $stmt->setFetchMode(PDO::FETCH_NUM);

			  while ($row = $stmt->fetch()) { 
			         		$clb_codigo = $row[0];
							$clb_razaosocial =  trim($row[1]);  
							$clb_cnpj =   trim($row[2]);
					
			  }
			}
			catch (PDOException $e) {
			  print $e->getMessage();
			}

				if (!empty($clb_codigo)) {

					// entra para puxar processos vinculados no cliente 
									$sql_porc = "  SELECT DISTINCT "
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

									  $stmt = $db->query($sql_porc);
									  $result = $stmt->setFetchMode(PDO::FETCH_NUM);

									  while ($row = $stmt->fetch()) { 
									         		$proc_cod = $row[0];
													$proc_numero=  trim($row[1]);  
													$proc_descricao =   trim($row[2]);
												
									  }
									}
									catch (PDOException $e) {
									  print $e->getMessage();
									}

							        
			                   echo "<meta http-equiv=\"refresh\" content=\"3;url=exibe_consulta_processo.php?clientecnpj=".$clb_cnpj."\">"; 

				}else{

						       $log="Nenhum Registro Vinculado ao CNPJ";      
			                   echo "<meta http-equiv=\"refresh\" content=\"0;url=consultaempreendedor.php?log=".$log."\">"; 

				}
 

 }


?>
