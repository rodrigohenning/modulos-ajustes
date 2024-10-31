<?php
/*
Autor: Rodrigo Henning C. Rodrigues
Email: rodrigohenning@hotmail.com
Data: 01/08/2016
Objetivo: Criterios de login para Segurança
- verifica usuario ou senha 
- includ para verificar acessos ou tentativas de descoberta de senhas 
- data ultimo acesso com 45 dias 
- tempo da senha criada. dias  (se não direcionar para atualizar senhas)
**********************************************************************************

***********************************************************************************			
*/
session_start();
session_destroy();
session_start();

$orgao= $_POST['orgao'];
$login= $_POST['login'];
$senha= $_POST['password'];

$mes = date("m");
$dia =  date("j");
$ano = date("Y");
				
include 'includes/conexaoPDO.php';

$sql = " SELECT "
		                  . " controleusuario.usuario.emp_codigo, "
						  . " controleusuario.usuario.fun_codigo, "
						  . " controleusuario.usuario.usu_login, "
						  . " geral.funcionario.fun_nome, "
						  . " geral.funcionario.are_codigo, "
						  . " geral.funcionario.crg_codigo, "
						  . " geral.area.are_sigla,"
        				  . " geral.area.are_descricao, "
        				  . " projeto.secretaria_x_projeto.pjb_codigo, "
						   . " geral.secretaria.emp_municipio "
        				  . " FROM " 
						  . " controleusuario.usuario "
						  . " LEFT OUTER JOIN geral.funcionario ON "
						  . " (controleusuario.usuario.fun_codigo=geral.funcionario.fun_codigo) "
                           . " INNER JOIN geral.secretaria ON "
						   . " (controleusuario.usuario.emp_codigo=geral.secretaria.emp_codigo) "
						  . " LEFT OUTER JOIN geral.area ON "
						  . " (geral.funcionario.emp_codigo=geral.area.emp_codigo) AND "
						  . " (geral.funcionario.are_codigo=geral.area.are_codigo) "
						  . " INNER JOIN projeto.secretaria_x_projeto ON"
						  . " (projeto.secretaria_x_projeto.emp_codigo=controleusuario.usuario.emp_codigo)"
						  . " WHERE "
						  . " (geral.funcionario.fun_excluido_s_n = 'N') AND "
						  . " (controleusuario.usuario.usu_excluido_s_n = 'N') AND "
						  . " (controleusuario.usuario.emp_codigo = '" .$orgao. "') AND "
     					  . " (controleusuario.usuario.usu_login = '" .$login. "') AND " 
						  . " (controleusuario.usuario.usu_senha = '" .sha1($senha). "') AND "
         				  . " (controleusuario.usuario.usu_senhaativa_s_n = 'S') AND "
						  . " (geral.area.are_excluido_s_n = 'N')" ;

try {

  $stmt = $db->query($sql);
  $result = $stmt->setFetchMode(PDO::FETCH_NUM);

  while ($row = $stmt->fetch()) { 
         		$emp_codigo =  trim($row[0]);
				$fun_codigo =  $row[1];  
				$usu_login =   trim($row[2]);
				$fun_nome =    trim($row[3]);
				$are_codigo =  $row[4];
				$crg_codigo =  $row[5];
				$are_sigla =   $row[6];
				$are_descricao = $row[7];
				$pjb_codigo =  $row[8];
     			$emp_municipio=$row[9];				
  }
}
catch (PDOException $e) {
  print $e->getMessage();
}


if (!empty($usu_login)) {

		// Puxa ultima data de acesso $dataultimoacesso
						$sqlacesso = " SELECT dataultimoacesso "
									  . " FROM "
									  . " geral.parametro_acesso "
									  . " WHERE " 
									  . " emp_codigo  = '" . $emp_codigo. "' AND "
									  . " anoacesso  = '"  . date('Y').   "' AND "
									  . " fun_codigo = '".$fun_codigo."' ";			
						try {

						  $stmt = $db->query($sqlacesso);
						  $result = $stmt->setFetchMode(PDO::FETCH_NUM);

						  while ($row = $stmt->fetch()) { 
						         		$dataultimoacesso =  $row[0];
									
						  }
						}
						catch (PDOException $e) {
						  print $e->getMessage();
						}

        //Calcula quantos dias desde o ultimo acesso. 
		// Define os valores a serem usados
		$data_inicial = date('d-m-Y',strtotime($dataultimoacesso));
		$data_final = date("d-m-Y");
		// Cria uma função que retorna o timestamp de uma data no formato DD/MM/AAAA
		function geraTimestamp($data) {
		$partes = explode('-', $data);
		return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
		}
		// Usa a função criada e pega o timestamp das duas datas:
		$time_inicial = geraTimestamp($data_inicial);
		$time_final = geraTimestamp($data_final);
		// Calcula a diferença de segundos entre as duas datas:
		$diferenca = $time_final - $time_inicial; // 19522800 segundos
		// Calcula a diferença de dias
		$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
		// Exibe uma mensagem de resultado:
		//echo "A diferença entre as datas ".$data_inicial." e ".$data_final." é de <strong>".$dias."</strong> dias";


		if (empty($dataultimoacesso)){

					$acesso=1;
					$data= date("Y-n-d h:i:s");
					$ano= date("Y");

					// insere no banco o registro de parametro de acesso
					$sqlgeralacesso = "INSERT INTO geral.parametro_acesso (emp_codigo, fun_codigo, qtdadeacesso, dataultimoacesso, anoacesso) VALUES ('".$emp_codigo."', '".$fun_codigo."', '".$acesso."', '".$data."', '".$ano."') ";
					$stmt = $db->query($sqlgeralacesso);
		}



	if ($dias<45) {
	           // verifica se a senha inspirou

				
					// Puxa data da senha $datasenha
						$sqlacesso = " SELECT ultima_troca_senha "
									  . " FROM "
									  . " controleusuario.usuario "
									  . " WHERE " 
									  . " emp_codigo  = '" . $emp_codigo. "' AND "
									  . " fun_codigo = '".$fun_codigo."' ";		
						try {

						  $stmt = $db->query($sqlacesso);
						  $result = $stmt->setFetchMode(PDO::FETCH_NUM);

						  while ($row = $stmt->fetch()) { 
						         		$datasenha =  $row[0];
									
						  }
						}
						catch (PDOException $e) {
						  print $e->getMessage();
						}

		$data_inicial = date('d-m-Y',strtotime($datasenha));
		$data_final = date("d-m-Y");

		$time_inicial = geraTimestamp($data_inicial);
		$time_final = geraTimestamp($data_final);

		$diferenca = $time_final - $time_inicial; // 19522800 segundos
		// Calcula a diferença de dias
		$diasS = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
		// Exibe uma mensagem de resultado:
		//echo "A diferença entre as datas ".$data_inicial." e data senha ".$data_final." é de <strong>".$diasS."</strong> dias";

		if ($diasS<180) {

					// Se OK inicia session 

		
       					$ufe_codigo_acesso="AC";
	
						session_register('ufe_codigo_acesso');
						$GLOBALS['ufe_codigo_acesso'] = $ufe_codigo_acesso;

						session_register('fun_codigo');
						session_register('usu_login');
						session_register('fun_nome');
						session_register('emp_codigo');

						session_register('are_codigo');
						session_register('crg_codigo');
						session_register('are_sigla');
						session_register('are_descricao');
						session_register('emp_municipio');


						$_SESSION['ufe_codigo_acesso'] = $ufe_codigo_acesso;			
						$_SESSION['emp_codigo'] = $emp_codigo;
						$_SESSION['fun_codigo'] = $fun_codigo;
						$_SESSION['usu_login'] = $usu_login;
						$_SESSION['fun_nome'] = $fun_nome;
						$_SESSION['are_codigo'] = $are_codigo;
						$_SESSION['crg_codigo'] = $crg_codigo;
						$_SESSION['are_sigla'] = $are_sigla;
						$_SESSION['are_descricao'] = $are_descricao;
						$_SESSION['emp_municipio'] = $emp_municipio;


						//iserir dados de log no na tabela 
						$ip = $_SERVER['REMOTE_ADDR'];
						$query = "INSERT INTO controleusuario.logsistema (emp_codigo, fun_codigo, prg_codigo, log_acao, log_texto, log_ip) VALUES ('".$emp_codigo."', '".$fun_codigo."', '0000', 'Login', 'Entrou no sistema', '".$ip."') ";
						$stmt = $db->query($query);

						/*	if(!$stmt){
						echo pg_last_error($db);
						echo "Errro ao inserir\n";
						} else {
						echo "inserido com sucesso \n";
						}
						*/   
						// inseri ultimo aceeso e quantidade no ano 
						$sqlacesso = " SELECT qtdadeacesso "
						. " FROM "
						. " geral.parametro_acesso "
						. " WHERE " 
						. " emp_codigo  = '" . $emp_codigo . "' AND "
						. " anoacesso  = '" . date('Y') . "' AND "
						. " fun_codigo = '".$fun_codigo."' ";			
						try {

						$stmt = $db->query($sqlacesso);
						$result = $stmt->setFetchMode(PDO::FETCH_NUM);

						while ($row = $stmt->fetch()) { 
						$acesso =  $row[0];

						}
						}
						catch (PDOException $e) {
						print $e->getMessage();
						}

						$acesso++;
						$data= date("Y-n-d h:i:s");
						$ano= date("Y");

						$sqlgeralacesso = " UPDATE geral.parametro_acesso "
						. " SET emp_codigo='".$emp_codigo."', fun_codigo='".$fun_codigo."', qtdadeacesso='".$acesso."', dataultimoacesso='".$data."', anoacesso='".$ano."' "
						. " WHERE " 
						. " anoacesso = '".$ano."' AND "
						. " fun_codigo = '".$fun_codigo."' ";
						$stmt = $db->query($sqlgeralacesso);

						echo "<html><head><title>Usu rio autenticado</title>";
						echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">";

						if($pjb_codigo==17 or $pjb_codigo==19 or $pjb_codigo==23 or $pjb_codigo==24){
						echo "<meta http-equiv=\"refresh\" content=\"0;url=modulos/consultarelatorio/60.consulta/pautatrabalho/wFrm0502_PautaTrabalho.php\">";
						}elseif($pjb_codigo==18){
						echo "<meta http-equiv=\"refresh\" content=\"0;url=modulos/consultarelatorio/60.consulta/pautatrabalho/wFrm0797_PautaTrabalho.php\">";
						}elseif($pjb_codigo==22){
						echo "<meta http-equiv=\"refresh\" content=\"0;url=modulos/consultarelatorio/60.consulta/pautatrabalho/wFrm0744_PautaTrabalho.php\">";
						}			 
						echo "</head><body></body></html>";
	
		}else{
				//SENHA Expirou
						

				include 'includes/conexaoPDO.php';
					$sql2 = " SELECT "
					. " controleusuario.usuario.emp_codigo, "
					. " controleusuario.usuario.fun_codigo, "
					. " controleusuario.usuario.usu_login, "
					. " geral.funcionario.fun_nome, "
					. " geral.funcionario.are_codigo, "
					. " geral.funcionario.crg_codigo, "
					. " geral.area.are_sigla,"
					. " geral.area.are_descricao, "
					. " projeto.secretaria_x_projeto.pjb_codigo, "
					. " geral.secretaria.emp_municipio "
					. " FROM " 
					. " controleusuario.usuario "
					. " LEFT OUTER JOIN geral.funcionario ON "
					. " (controleusuario.usuario.fun_codigo=geral.funcionario.fun_codigo) "
					. " INNER JOIN geral.secretaria ON "
					. " (controleusuario.usuario.emp_codigo=geral.secretaria.emp_codigo) "
					. " LEFT OUTER JOIN geral.area ON "
					. " (geral.funcionario.emp_codigo=geral.area.emp_codigo) AND "
					. " (geral.funcionario.are_codigo=geral.area.are_codigo) "
					. " INNER JOIN projeto.secretaria_x_projeto ON"
					. " (projeto.secretaria_x_projeto.emp_codigo=controleusuario.usuario.emp_codigo)"
					. " WHERE "
					. " (geral.funcionario.fun_excluido_s_n = 'N') AND "
					. " (controleusuario.usuario.usu_excluido_s_n = 'N') AND "
					. " (controleusuario.usuario.emp_codigo = '" .$orgao. "') AND "
					. " (controleusuario.usuario.usu_login = '" .$login. "') AND " 
					. " (controleusuario.usuario.usu_senhaativa_s_n = 'S') AND "
					. " (geral.area.are_excluido_s_n = 'N')" ;

					// retona os dado do usuario que tentou autenticar 
					try {
					$stmt = $db->query($sql2);
					$result = $stmt->setFetchMode(PDO::FETCH_NUM);
					while ($row = $stmt->fetch()) { 

					$emp_codigo =  trim($row[0]);
					$fun_codigo =  $row[1];  
					$usu_login =   trim($row[2]);
					$fun_nome =    trim($row[3]);
					$are_codigo =  $row[4];
					$crg_codigo =  $row[5];
					$are_sigla =   $row[6];
					$are_descricao= $row[7];
					$pjb_codigo =  $row[8];
					$emp_municipio=$row[9];	

					}

					}
					catch (PDOException $e) {
					print $e->getMessage();
					}				

					// copia ip do computador 
					$ip = $_SERVER['REMOTE_ADDR'];
					// insere no banco o registro de flaha de login
					$query = "INSERT INTO controleusuario.logsistema (emp_codigo, fun_codigo, prg_codigo, log_acao, log_texto, log_ip) VALUES ('".$emp_codigo."', '".$fun_codigo."', '9999', 'SenhaExpirou', 'TrocaObrigatoria', '".$ip."') ";
					$stmt = $db->query($query);

					$log="Atualização de Senha Obrigatória!";
					//redireciona para pagina onde acontece o erro de login para proxima digitaçao 
					//echo "<meta http-equiv=\"refresh\" content=\"0;url=index_newSenha.php?log=$log\">";

			echo "<meta http-equiv=\"refresh\" content=\"0;url=index_newSenha.php?emp_codigo=".$emp_codigo."&usu_login=".$usu_login."&log=".$log."\">";
			


		}

	}else{

				// Usuario Bloqueou 
						
				include 'includes/conexaoPDO.php';
					$sql2 = " SELECT "
					. " controleusuario.usuario.emp_codigo, "
					. " controleusuario.usuario.fun_codigo, "
					. " controleusuario.usuario.usu_login, "
					. " geral.funcionario.fun_nome, "
					. " geral.funcionario.are_codigo, "
					. " geral.funcionario.crg_codigo, "
					. " geral.area.are_sigla,"
					. " geral.area.are_descricao, "
					. " projeto.secretaria_x_projeto.pjb_codigo, "
					. " geral.secretaria.emp_municipio "
					. " FROM " 
					. " controleusuario.usuario "
					. " LEFT OUTER JOIN geral.funcionario ON "
					. " (controleusuario.usuario.fun_codigo=geral.funcionario.fun_codigo) "
					. " INNER JOIN geral.secretaria ON "
					. " (controleusuario.usuario.emp_codigo=geral.secretaria.emp_codigo) "
					. " LEFT OUTER JOIN geral.area ON "
					. " (geral.funcionario.emp_codigo=geral.area.emp_codigo) AND "
					. " (geral.funcionario.are_codigo=geral.area.are_codigo) "
					. " INNER JOIN projeto.secretaria_x_projeto ON"
					. " (projeto.secretaria_x_projeto.emp_codigo=controleusuario.usuario.emp_codigo)"
					. " WHERE "
					. " (geral.funcionario.fun_excluido_s_n = 'N') AND "
					. " (controleusuario.usuario.usu_excluido_s_n = 'N') AND "
					. " (controleusuario.usuario.emp_codigo = '" .$orgao. "') AND "
					. " (controleusuario.usuario.usu_login = '" .$login. "') AND " 
					. " (controleusuario.usuario.usu_senhaativa_s_n = 'S') AND "
					. " (geral.area.are_excluido_s_n = 'N')" ;

					// retona os dado do usuario que tentou autenticar 
					try {
					$stmt = $db->query($sql2);
					$result = $stmt->setFetchMode(PDO::FETCH_NUM);
					while ($row = $stmt->fetch()) { 

					$emp_codigo =  $row[0];
					$fun_codigo =  $row[1];  
					$usu_login =   $row[2];
					$fun_nome =    $row[3];
					$are_codigo =  $row[4];
					$crg_codigo =  $row[5];
					$are_sigla =   $row[6];
					$are_descricao = $row[7];
					$pjb_codigo =  $row[8];
					$emp_municipio=$row[9];				
					}

					}
					catch (PDOException $e) {
					print $e->getMessage();
					}				

					// copia ip do computador 
					$ip = $_SERVER['REMOTE_ADDR'];
					// insere no banco o registro de flaha de login
					$query = "INSERT INTO controleusuario.logsistema (emp_codigo, fun_codigo, prg_codigo, log_acao, log_texto, log_ip) VALUES ('".$emp_codigo."', '".$fun_codigo."', '9999', 'LoginErro', 'Senha Expirou', '".$ip."') ";
					$stmt = $db->query($query);


					$sqlbloqueiosenha = " UPDATE controleusuario.usuario "
						. " SET usu_senhaativa_s_n='N'"						
						. " WHERE " 
						. " emp_codigo = '".$emp_codigo."' AND "
						. " fun_codigo = '".$fun_codigo."' ";
						$stmt = $db->query($sqlbloqueiosenha);





					$log="Usuário Bloqueado! Procure Administração";
					//redireciona para pagina onde acontece o erro de login para proxima digitaçao 
					echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php?log=$log\">";

	}

}else{


		#senha errada/login inesistente 


						include 'includes/conexaoPDO.php';
		$sql2 = " SELECT "
		. " controleusuario.usuario.emp_codigo, "
		. " controleusuario.usuario.fun_codigo, "
		. " controleusuario.usuario.usu_login, "
		. " geral.funcionario.fun_nome, "
		. " geral.funcionario.are_codigo, "
		. " geral.funcionario.crg_codigo, "
		. " geral.area.are_sigla,"
		. " geral.area.are_descricao, "
		. " projeto.secretaria_x_projeto.pjb_codigo, "
		. " geral.secretaria.emp_municipio "
		. " FROM " 
		. " controleusuario.usuario "
		. " LEFT OUTER JOIN geral.funcionario ON "
		. " (controleusuario.usuario.fun_codigo=geral.funcionario.fun_codigo) "
		. " INNER JOIN geral.secretaria ON "
		. " (controleusuario.usuario.emp_codigo=geral.secretaria.emp_codigo) "
		. " LEFT OUTER JOIN geral.area ON "
		. " (geral.funcionario.emp_codigo=geral.area.emp_codigo) AND "
		. " (geral.funcionario.are_codigo=geral.area.are_codigo) "
		. " INNER JOIN projeto.secretaria_x_projeto ON"
		. " (projeto.secretaria_x_projeto.emp_codigo=controleusuario.usuario.emp_codigo)"
		. " WHERE "
		. " (geral.funcionario.fun_excluido_s_n = 'N') AND "
		. " (controleusuario.usuario.usu_excluido_s_n = 'N') AND "
		. " (controleusuario.usuario.emp_codigo = '" .$orgao. "') AND "
		. " (controleusuario.usuario.usu_login = '" .$login. "') AND " 
		. " (controleusuario.usuario.usu_senhaativa_s_n = 'S') AND "
		. " (geral.area.are_excluido_s_n = 'N')" ;

		// retona os dado do usuario que tentou autenticar 
		try {
		$stmt = $db->query($sql2);
		$result = $stmt->setFetchMode(PDO::FETCH_NUM);
		while ($row = $stmt->fetch()) { 

			$emp_codigo =  $row[0];
		$fun_codigo =  $row[1];  
		$usu_login =   $row[2];
		$fun_nome =    $row[3];
		$are_codigo =  $row[4];
		$crg_codigo =  $row[5];
		$are_sigla =   $row[6];
		$are_descricao = $row[7];
		$pjb_codigo =  $row[8];
		$emp_municipio=$row[9];				
		}

		}
		catch (PDOException $e) {
		print $e->getMessage();
		}				

		// copia ip do computador 
		$ip = $_SERVER['REMOTE_ADDR'];
		// insere no banco o registro de flaha de login
		$query = "INSERT INTO controleusuario.logsistema (emp_codigo, fun_codigo, prg_codigo, log_acao, log_texto, log_ip) VALUES ('".$emp_codigo."', '".$fun_codigo."', '9999', 'LoginErro', 'Tentativa de Login', '".$ip."') ";
		$stmt = $db->query($query);

		$log="Senha/Usuário Incorreto";
		//redireciona para pagina onde acontece o erro de login para proxima digitaçao 
		echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php?log=$log\">";

}

?>
