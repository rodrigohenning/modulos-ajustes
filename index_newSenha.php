<?php
/*
Autor: Rodrigo Henning C. Rodrigues
Email: rodrigohenning@hotmail.com
Data: 18/08/2016
Objetivo: Criterios de login para Segurança
- verifica usuario ou senha 
- includ para verificar acessos ou tentativas de descoberta de senhas 
- data ultimo acesso com 45 dias 
- tempo da senha criada. dias  (se não direcionar para atualizar senhas)
**********************************************************************************

***********************************************************************************			
*/
ini_set('default_charset','UTF-8');

if(isset($_GET['emp_codigo'])){ //segurança da pagina
}else{	
header('location: index.php');
}

if(isset($_GET['usu_login'])){ //segurança da pagina
}else{	
header('location: index.php');
}

$usu_login= $_GET['usu_login'];
$emp_codigo= $_GET['emp_codigo'];

$mes = date("m");
$dia =  date("j");
$ano = date("Y");
				
include 'includes/conexaoPDO.php';

$sql1 = " SELECT "
		                  . " controleusuario.usuario.emp_codigo, "
						  . " controleusuario.usuario.fun_codigo, "
						  . " controleusuario.usuario.usu_login, "
						  . " geral.funcionario.fun_nome, "
						  . " geral.funcionario.are_codigo, "
						  . " geral.funcionario.crg_codigo, "
						  . " geral.area.are_sigla,"
        				  . " geral.area.are_descricao, "
        				  . " projeto.secretaria_x_projeto.pjb_codigo, "
						   . " geral.secretaria.emp_razaosocial "
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
						  . " (controleusuario.usuario.emp_codigo = '" .$emp_codigo. "') AND "
     					  . " (controleusuario.usuario.usu_login = '" .$usu_login. "') AND " 
         				  . " (controleusuario.usuario.usu_senhaativa_s_n = 'S') AND "
						  . " (geral.area.are_excluido_s_n = 'N')" ;

try {

  $stmt = $db->query($sql1);
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
     			$emp_razaosocial=$row[9];				
  }
}
catch (PDOException $e) {
  print $e->getMessage();
}

?>


<html >
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
 <title>Sistema Estadual de Informações Ambientais - SEIAM/AC (IMAC)</title>

  <link href="includes/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="includes/bootstrap/css/styleLogin.css" rel="stylesheet">
 </head>

<body role="Atualização de Senha">



     <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://www.seiam.ac.gov.br"><img class="profile-img" src="banner/desenv/seiam.png" class="img-responsive" alt=""></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
             <li ><a href="http://www.imac.ac.gov.br" target="_blank"><img class="profile-img" src="banner/desenv/logo_IMAC.png" class="img-responsive" alt="" width= '161' height="55"></a></li>
            <!-- <li class="active"><a href="http://www.imac.ac.gov.br" target="_blank">Web Site IMAC</a></li>
            <li ><a href="../fiscalizacao/" target="_blank">Ficalização</a></li>
            <li><a href="#works">Works</a></li> -->
          </ul>
        </div> 
      </div>
    </div>


    <div id="headerwrap">
   

<div class="container" style="margin-top:10px" >
<div class="row">
  <div class="col-sm-5 col-md-6 col-sm-offset-3 col-md-offset-3">
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
                <address>                 
                     <p><b>Org&atilde;o:</b><?php echo" ".$emp_razaosocial;?></p>
                     <p><b>Nome:</b> <?php echo" ".$fun_nome; ?></p>
                     <p><b>Login:</b><?php echo" ".$usu_login; ?></p>
                </address>
          </p>
                     <hr align="center" width="100%" size="1" color="green">
               </div>


                	<!-- senha atual -->
                <div class="form-group">
                 <label></label>
                  <div class="input-group"> <span class="input-group-addon"> <i class="glyphicon glyphicon-lock"></i> </span>
                    <input class="form-control" placeholder="Senha Atual" name="password" type="password" value="" required="">
                  </div>
                </div>
                 
                 <!-- senha nova 01 -->
                 <div class="form-group">
                 <label></label>
                  <div class="input-group"> <span class="input-group-addon"> <i class="glyphicon glyphicon-lock"></i> </span>
                    <input class="form-control" placeholder="Nova Senha (Mínimo de 8 caracteres)" name="passwordnew01" type="password" value="" required="" pattern=".{8,16}" maxlength="16">
              
                  </div>
                </div>

                 <!-- senha nova 01 -->
                  <div class="form-group">
                 <label></label>
                  <div class="input-group" class="col-md-4"> <span class="input-group-addon"> <i class="glyphicon glyphicon-lock"></i> </span>
                    <input class="form-control" placeholder="Confirme Nova Senha " name="passwordnew02" type="password" value="" required="" pattern=".{8,16}" maxlength="16">
                   
                  </div>
                </div>



                <div class="form-group">
                 <label>
                   </label> <input type="submit" class="btn btn-success" value="Atualizar Senha" name="botao">
                </div>

              <div> 
                <center>
                <?php
                if(isset($_GET['log']))  
                {  
                     echo '<label class="text-danger">'.$_GET['log'].'</label>';  
                }  
                ?>  

                </center>
              </div> 



              </div>
            </div>
           
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
</div><! --/headerwrap -->
  </body>
</html>

<?php
// inicia o tratamento e verificaçao das senhas 

                if(isset($_POST['botao'])){  
        				

                    	$senhaAntiga= trim($_POST['password']);
                        $senhanew1= trim($_POST['passwordnew01']);
                        $senhanew2= trim($_POST['passwordnew02']);

               						
               						if ($senhanew1==$senhanew2){
 												


 												if ($senhanew1!=$senhaAntiga){

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
																  . " (controleusuario.usuario.emp_codigo = '" .$emp_codigo. "') AND "
										     					  . " (controleusuario.usuario.usu_login = '" .$usu_login. "') AND " 
																  . " (controleusuario.usuario.usu_senha = '" .sha1($senhaAntiga). "') AND "
										         				  . " (controleusuario.usuario.usu_senhaativa_s_n = 'S') AND "
																  . " (geral.area.are_excluido_s_n = 'N')" ;

										try {

										  $stmt = $db->query($sql);
										  $result = $stmt->setFetchMode(PDO::FETCH_NUM);

										  while ($row = $stmt->fetch()) { 
										         		$emp_codigo =  trim($row[0]);
														$fun_codigo =  $row[1];  
														$usu_loginFim =   trim($row[2]);
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

														if (!empty($usu_loginFim)) {
																	include 'includes/conexaoPDO.php';
																	$sqlgeralacesso = " UPDATE controleusuario.usuario "
																							. " SET usu_senha='".sha1($senhanew1)."', ultima_troca_senha='".date('Y-m-d')."' "
																							. " WHERE " 
																							. " emp_codigo = '".$emp_codigo."' AND "
																							. " fun_codigo = '".$fun_codigo."' ";	
																							$stmt = $db->query($sqlgeralacesso);


																		$log="Senha Ativa! e Atualizada com Sucesso! ";
																		//redireciona para pagina onde acontece o erro de login para proxima digitaçao 
																		echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php?log=$log\">";
														}else{

																echo "";


														$log="Senha Incorreta!";
														//redireciona para pagina onde acontece o erro de autenticação para troca de senha 
														echo "<meta http-equiv=\"refresh\" content=\"0;url=index_newSenha.php?emp_codigo=".$emp_codigo."&usu_login=".$usu_login."&log=".$log."\">";


														}		




 													
 												}else{

												$log="Senha Igual a Anterior!";
												//redireciona para pagina onde acontece o erro de autenticação para troca de senha 
												echo "<meta http-equiv=\"refresh\" content=\"0;url=index_newSenha.php?emp_codigo=".$emp_codigo."&usu_login=".$usu_login."&log=".$log."\">";


 												}
									
               					    }else{

									$log="Senhas não Conferem!";
									//redireciona para pagina onde acontece o erro de autenticação para troca de senha 
									echo "<meta http-equiv=\"refresh\" content=\"0;url=index_newSenha.php?emp_codigo=".$emp_codigo."&usu_login=".$usu_login."&log=".$log."\">";



               						}



                }  
                


?>
