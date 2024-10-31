    
<?php
// Inclui a classe
include('class/class-valida-cpf-cnpj.php');

if(isset($_POST['cpf']))  {   

  $cpf = $_POST['cpf'];

  $cpf_cnpj = new ValidaCPFCNPJ($cpf);
      // Verifica se o CPF ou CNPJ é válido
  if ($cpf_cnpj->valida()) {

      //echo 'CPF v&aacute;lido<br>'; // Retornará este valor      
      //$log="CPF: ".$cpf." Válido";      
      //echo "<meta http-equiv=\"refresh\" content=\"0;url=consulta_processo.php?cpf=".$cpf."\">"; 

    include ('consulta_processo.php');

  } else {

      //echo 'CPF Inv&aacute;lido<br>';
    $log=utf8_encode("CPF Inv&aacute;lido!!!");      
    echo "<meta http-equiv=\"refresh\" content=\"0;url=consultaempreendedor.php?log=".$log."\">"; 

  }
}  
if(isset($_POST['cnpj']))  {   

  $cnpj= $_POST['cnpj'];

  $cpf_cnpj = new ValidaCPFCNPJ($cnpj);
      // Verifica se o CPF ou CNPJ é válido

  if ($cpf_cnpj->valida()) {

    include ('consulta_processo.php');

  } else {

    $log=trim("CNPJ Inv&aacute;lido!!!");      
    echo "<meta http-equiv=\"refresh\" content=\"0;url=consultaempreendedor.php?log=".$log."\">"; 

  }
}  

ini_set('default_charset','UTF-8');
session_start();
session_destroy();

?>

<!DOCTYPE html>
<html >
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

<script src="includes/js/jquery.js" type="text/javascript"></script>
<script src="includes/js/jquery.maskedinput.js" type="text/javascript"></script>
<script src="includes/js/jquery.maskMoney.js" type="text/javascript"></script>

  <title>Sistema Estadual de Informa&ccedil;&otilde;es Ambientais - SEIAM/AC (IMAC)</title>

  <style type="text/css">
  .tabela {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 10px;
  }
  </style>

  <script>
  function formatar(mascara, documento){
    var i = documento.value.length;
    var saida = mascara.substring(0,1);
    var texto = mascara.substring(i)

    if (texto.substring(0,1) != saida){
      documento.value += texto.substring(0,1);
    }

  }
  </script>

  <link href="includes/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="includes/bootstrap/css/styleLogin.css" rel="stylesheet">
  <link href="includes/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

</head>

<body role="">

    <div class="container" style="margin-top:0px">
      <div class="row">
        <div class="col-sm-5 col-md-10 col-sm-offset-3 col-md-offset-1">
          <div class="panel panel-default">

            <div class="panel-body">
              <form role="form" action="consultaempreendedor.php" method="POST">
                <fieldset>
<!--             <div class="row" >
              <div class="center-block"> <img class="profile-img" src="banner/desenv/seiam.png" class="img-responsive" alt=""> </div>
              <hr>
            </div> -->
            <div class="row">
              <div class="col-sm-4 col-md-8  col-md-offset-1 ">

                <div class="form-group">       
                  <td height="34" colspan="2" valign="top"><b>Consulta do processos </b><br></td>
                </div>

<?php 
    
   if(isset($_GET['pesquisa'])){
       
       $pesquisa=($_GET['pesquisa']);
            

            if ($pesquisa=="CPF") {
              ?>
                <div class="form-group">       
                  <select id="tipoConsulta" name="tipoConsulta" class="form-control" onchange="document.location.href = this.value;">                        
                    <option value="consultaempreendedor.php?pesquisa=CPF" >CPF-Cadastro de Pessoas F&iacute;sicas </option>
                    <option value="consultaempreendedor.php?pesquisa=CNPJ">CNPJ-Cadastro Nacional de Pessoas Jur&iacute;dicas</option>               
                  </select>
                </div>
              <?php
              
            }elseif ($pesquisa=="CNPJ") {
              ?>
                <div class="form-group">       
                  <select id="tipoConsulta" name="tipoConsulta" class="form-control" onchange="document.location.href = this.value;"> 
                    <option value="consultaempreendedor.php?pesquisa=CNPJ">CNPJ-Cadastro Nacional de Pessoas Jur&iacute;dicas</option>                         
                    <option value="consultaempreendedor.php?pesquisa=CPF" >CPF-Cadastro de Pessoas F&iacute;sicas </option>                      
                  </select>
                </div>
              <?php            
           }

    }else{
      ?>
                <div class="form-group">       
                  <select id="tipoConsulta" name="tipoConsulta" class="form-control" onchange="document.location.href = this.value;" required="">                        
                    <option value="" >Selecione Tipo de Consulta </option>
                    <option value="consultaempreendedor.php?pesquisa=CPF" >CPF-Cadastro de Pessoas F&iacute;sicas </option>
                    <option value="consultaempreendedor.php?pesquisa=CNPJ">CNPJ-Cadastro Nacional de Pessoas Jur&iacute;dicas</option>               
                  </select>
                </div>

      <?php

    }
?>

<?php

if (!empty($_GET['pesquisa'])) {

  $tipoConsulta= ($_GET['pesquisa']);


  if ($tipoConsulta=='CNPJ') {
    ?>
    <div class="form-group">
      <label></label>
      <div class="input-group"> <span class="input-group-addon"> <i class="glyphicon glyphicon-paste"></i> </span>
        <input class="form-control" id="cnpj" placeholder="Informe o CNPJ" name="cnpj" type="text" autofocus maxlength="18" OnKeyPress="formatar('##.###.###/####-##', this)">
      </div>
    </div>
    <?php
  }else{
    ?>

    <div class="form-group">
     <label></label>
     <div class="input-group"> <span class="input-group-addon"> <i class="glyphicon glyphicon-paste"></i> </span>
      <input class="form-control" id="cpf" placeholder="Informe o CPF " name="cpf" type="text" value=""  maxlength="14" OnKeyPress="formatar('###.###.###-##', this)">
    </div>
  </div>
  <?php
}
}
?>   

<script type="text/javascript">
jQuery(function($){
$("#date").mask("99/99/9999",{placeholder:"dd/mm/aaaa"});
$("#phone").mask("(99) 99999-9999");
$("#phone2").mask("(99) 9999-9999");
$("#tin").mask("99-9999999");
$("#ssn").mask("999-99-9999");
$("#cnpj").mask("99.999.999/9999-99");
$("#cpf").mask("999.999.999-99");
});               
</script> 

<div class="form-group">
 <label>
 </label> 
 <input type="submit" class="btn btn-success" value="Consultar" id="ativo" name="ativo">
 <input class="btn btn-danger dropdown-toggle" type="reset" value="Limpar">            
</div>


<div> 
  <center>
  <?php 
  if($_POST['ativo']) 
   {  
       echo '<img src="includes/img/712.gif">';  
  }?>

  </center>
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
</body>
</html>


