<?php
/*
Autor: Rodrigo Henning C. Rodrigues
Email: rodrigohenning@hotmail.com
Data: 30.03.2022
Objetivo: consultar processos portal de transparencia do empreendedor para implantação no web site
**********************************************************************************

***********************************************************************************     
*/

// require_once '../../../../../funcoes/funcao.php';
// $dirRel = new Utilidades;
// if ($_SESSION["emp_codigo"] == "" || $_SESSION["fun_codigo"] == "" || $_SESSION["are_codigo"] == "")
// 	header("Location: ".$dirRel->getPath()."mensagem/erro.php");

$tipologia = $_GET['tipologia'];
$area = 0;
$municipio = 0;
$dateinicial = $_GET['dateinicial'];
$datefinal = $_GET['datefinal'];
$tipo = $_GET['tipo'];


    //header("Content-type: application/vnd.ms-excel");
    //header("Content-type: application/force-download");
    // header("Content-Disposition: attachment; filename=Licenças Ambientais.xls");
    // header("Pragma: no-cache");
    // header('Cache-Control: max-age=0');
    // header('Expires: 0');


    // Se for o IE9, isso talvez seja necessário
    header('Cache-Control: max-age=1');


?>
<table border='1'  >

    <tr bgcolor="#8db561" >
        <td colspan="13" ><font color= '#FFFFFF' size='5'><center><b>Relat&oacute;rio de Empreendimentos</center></font></td>
    </tr>

    <tr bgcolor="#8db561" >
        <td><font color= '#FFFFFF'><b>NR. PROCESSO   </b></font></td>
        <td><font color= '#FFFFFF'><b>DATA FORMA&Ccedil;O  </b></font></td>
        <td><font color= '#FFFFFF'><b>DIVIS&Atilde;O        </b></font></td>
        <td><font color= '#FFFFFF'><b>GRUPO          </b></font></td>
        <td><font color= '#FFFFFF'><b>ATIVIDADE      </b></font></td>
        <td><font color= '#FFFFFF'><b>NR. LICEN&Ccedil;A    </b></font></td>
        <td><font color= '#FFFFFF'><b>NR. INDEREFIDO </b></font></td>
        <td><font color= '#FFFFFF'><b>DATA LICEN&Ccedil;A   </b></font></td>
        <td><font color= '#FFFFFF'><b>RAZ&Atilde;O SOCIAL   </b></font></td>
        <td><font color= '#FFFFFF'><b>CPF/CNPJ       </b></font></td>
        <td><font color= '#FFFFFF'><b>EMPREENDIMENTO </b></font></td>
        <td><font color= '#FFFFFF'><b>ENDERE&Ccedil;O       </b></font></td>
        <td><font color= '#FFFFFF'><b>MUNIC&Iacute;PIO      </b></font></td>
    </tr>


<?php



include 'includes/conexaoPDO.php';

$sql = " SELECT DISTINCT"
              . " licenciamento.processo.proc_numero||' - '||licenciamento.processo.proc_descricao AS proc_descricao,"
			  . " to_char(licenciamento.processo.proc_dataformacao, 'DD Mon YYYY') AS proc_dataformacao, "
			  . " licenciamento.atividade_divisao.atdv_descricao, "
			  . " licenciamento.atividade_grupo.atgp_descricao, "
			  . " licenciamento.atividade.ativ_descricao,"
			  . " licenciamento.processo_x_licencaemitida.prole_numerolicenca, "
			  . " licenciamento.processo_x_licencaemitida.prole_numeroindeferido, "
			  . " to_char(licenciamento.processo_x_licencaemitida.prole_datalancamento,'DD Mon YYYY HH24:MI') AS prole_datalancamento, "
			  . " CASE "
			  . " WHEN cliente_empree.clb_razaosocial is not null THEN "
			  . " cliente_empree.clb_razaosocial"
			  . " WHEN cliente_propr.clb_razaosocial is not null THEN "
			  . " cliente_propr.clb_razaosocial"
			  . " end AS clb_razaosocial, "
			  . " CASE "
			  . " WHEN cliente_empree.clb_cpf<>''  THEN "
			  . " cliente_empree.clb_cpf"
			  . " WHEN cliente_empree.clb_cnpj<>'' THEN "
			  . " cliente_empree.clb_cnpj "
			  . " WHEN cliente_propr.clb_cpf<>''  THEN "
			  . " cliente_propr.clb_cpf"
			  . " WHEN cliente_propr.clb_cnpj<>'' THEN "
			  . " cliente_propr.clb_cnpj "
			  . " end AS cpf_cnpj, "
			  . " CASE "
			  . " WHEN dadosbasico.propriedaderural.propr_codigo is not null AND dadosbasico.propriedaderural.propr_capr is not null  THEN "
			  . " 'CAPR {'||dadosbasico.propriedaderural.propr_capr||'} - '||dadosbasico.propriedaderural.propr_nome "
			  . " WHEN dadosbasico.propriedaderural.propr_codigo is not null AND "
			  . " dadosbasico.propriedaderural.propr_capr is null OR "
			  . " dadosbasico.propriedaderural.propr_capr=''  THEN "
			  . " dadosbasico.propriedaderural.propr_nome "
			  . " WHEN dadosbasico.empreendimento.empr_codigo is not null THEN "
			  . " 'CAE {'||dadosbasico.empreendimento.empr_codigo||'} - '||dadosbasico.empreendimento.empr_nome "
			  . " end AS empr_propr, "
			  . " CASE "
			  . " WHEN dadosbasico.propriedaderural.propr_codigo is not null  THEN "
			  . " tipolo_propr.tplo_descricao||' ' || "
			  . " dadosbasico.propriedaderuralendereco.proprend_endereco||', ' || "
			  . " dadosbasico.propriedaderuralendereco.proprend_complemento||' - ' || "
			  . " dadosbasico.propriedaderuralendereco.proprend_localizacao "
			  
			  . " WHEN dadosbasico.empreendimento.empr_codigo is not null  THEN "
			  . " tipologr_empree.tplo_descricao||' ' || "
			  . " dadosbasico.empreendimentoendereco.emen_endereco||', ' || "
			  . " dadosbasico.empreendimentoendereco.emen_complemento||' - ' || "
			  . " dadosbasico.empreendimentoendereco.emen_localizacao "
			  . " end AS endereco, "
			  . " CASE "
			  . " WHEN dadosbasico.propriedaderural.propr_codigo is not null  THEN "
        . " munic_propr.mun_descricao "
			  . " WHEN dadosbasico.empreendimento.empr_codigo is not null AND dadosbasico.empreendimentoendereco.emen_bairro is not null  THEN "
			  . " dadosbasico.empreendimentoendereco.emen_bairro||', '||munic_empree.mun_descricao "
			  . " WHEN dadosbasico.empreendimento.empr_codigo is not null AND dadosbasico.empreendimentoendereco.emen_bairro is null  THEN "
			  . " munic_empree.mun_descricao "
        . " end AS municipio "
			  . " FROM "
			  . " licenciamento.processo "
			  . " INNER JOIN licenciamento.processo_x_area ON "
			  . " (licenciamento.processo.proc_codigo = licenciamento.processo_x_area.proc_codigo) "
			  . " INNER JOIN licenciamento.processo_x_atividade ON "
			  . " (licenciamento.processo.proc_codigo = licenciamento.processo_x_atividade.proc_codigo) "
			  . " INNER JOIN licenciamento.atividade_divisao ON "
			  . " (licenciamento.processo_x_atividade.atdv_codigo = licenciamento.atividade_divisao.atdv_codigo) "
			  . " INNER JOIN licenciamento.atividade_grupo ON "
			  . " (licenciamento.processo_x_atividade.atdv_codigo = licenciamento.atividade_grupo.atdv_codigo) AND "
			  . " (licenciamento.processo_x_atividade.atgp_codigo = licenciamento.atividade_grupo.atgp_codigo) "
			  . " INNER JOIN licenciamento.atividade ON "
			  . " (licenciamento.processo_x_atividade.atdv_codigo = licenciamento.atividade.atdv_codigo) AND "
			  . " (licenciamento.processo_x_atividade.atgp_codigo = licenciamento.atividade.atgp_codigo) AND "
			  . " (licenciamento.processo_x_atividade.ativ_codigo = licenciamento.atividade.ativ_codigo) "
			  . " LEFT OUTER JOIN licenciamento.processo_x_licencaemitida ON "
			  . " (licenciamento.processo.proc_codigo = licenciamento.processo_x_licencaemitida.proc_codigo) "

			  . " LEFT OUTER JOIN dadosbasico.empreendimento_x_cliente_x_processo ON "
			  . " (licenciamento.processo.proc_codigo = dadosbasico.empreendimento_x_cliente_x_processo.proc_codigo) "
			  . " LEFT OUTER JOIN dadosbasico.empreendimento ON "
			  . " (dadosbasico.empreendimento_x_cliente_x_processo.empr_codigo = dadosbasico.empreendimento.empr_codigo) "
			  . " LEFT OUTER JOIN dadosbasico.empreendimentoendereco ON "
			  . " (dadosbasico.empreendimento.empr_codigo = dadosbasico.empreendimentoendereco.empr_codigo) AND "
			  . " (dadosbasico.empreendimentoendereco.emen_excluido_s_n='N')"
			  . " LEFT OUTER JOIN apoio.tipologradouro tipologr_empree  ON "
			  . " (dadosbasico.empreendimentoendereco.tplo_codigo = tipologr_empree.tplo_codigo) "
			  . " LEFT OUTER JOIN dadosbasico.cliente cliente_empree ON "
			  . " (dadosbasico.empreendimento_x_cliente_x_processo.clb_codigo = cliente_empree.clb_codigo) "
			  . " LEFT OUTER JOIN geometria.municipio munic_empree ON "
			  . " (dadosbasico.empreendimentoendereco.emen_municipio = munic_empree.mun_ibge) "

			  . " LEFT OUTER JOIN dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb ON "
			  . " (licenciamento.processo.proc_codigo = dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb.proc_codigo) "
			  . " LEFT OUTER JOIN dadosbasico.propriedaderural ON "
			  . " (dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb.propr_codigo = dadosbasico.propriedaderural.propr_codigo) "
			  . " LEFT OUTER JOIN dadosbasico.propriedaderuralendereco ON "
			  . " (dadosbasico.propriedaderural.propr_codigo = dadosbasico.propriedaderuralendereco.propr_codigo) AND "
			  . " (dadosbasico.propriedaderuralendereco.proprend_excluido_s_n='N')"
			  . " LEFT OUTER JOIN apoio.tipologradouro tipolo_propr ON "
			  . " (dadosbasico.propriedaderuralendereco.tplo_codigo = tipolo_propr.tplo_codigo) "
			  . " LEFT OUTER JOIN dadosbasico.unidadeconservacao ON "
			  . " (dadosbasico.propriedaderuralendereco.uncons_sequencia = dadosbasico.unidadeconservacao.uncons_sequencia) "
			  . " LEFT OUTER JOIN dadosbasico.projetoassentamento ON "
			  . " (dadosbasico.propriedaderuralendereco.projass_codigo = dadosbasico.projetoassentamento.projass_codigo) "
			  . " LEFT OUTER JOIN dadosbasico.gleba ON "
			  . " (dadosbasico.propriedaderuralendereco.projass_codigo = dadosbasico.gleba.projass_codigo) AND "
			  . " (dadosbasico.propriedaderuralendereco.gle_codigo = dadosbasico.gleba.gle_codigo) "
			  . " LEFT OUTER JOIN dadosbasico.cliente cliente_propr ON "
			  . " (dadosbasico.empreendimento_x_cliente_x_processo.clb_codigo = cliente_propr.clb_codigo) "
			  . " LEFT OUTER JOIN geometria.municipio munic_propr ON "
			  . " (dadosbasico.propriedaderuralendereco.proprend_municipio = munic_propr.mun_ibge) "
			  
			  . " INNER JOIN licenciamento.processo_x_tipoprocesso ON "
			  . " (licenciamento.processo.proc_codigo = licenciamento.processo_x_tipoprocesso.proc_codigo) "
			  . " INNER JOIN apoio.tipoprocesso ON "
			  . " (licenciamento.processo_x_tipoprocesso.protp_codigo = apoio.tipoprocesso.protp_codigo) AND "
			  . " (apoio.tipoprocesso.progr_codigo in (1,25,28))"
			  . " WHERE "
			  . " licenciamento.processo_x_licencaemitida.prole_numerolicenca is not null AND"
			  . " (licenciamento.processo.proc_excluido_s_n = 'N')";



if ($tipo==1){
       $sql .= "AND licenciamento.processo.proc_dataformacao between to_date('".$dateinicial."','dd/mm/yyyy') and to_date('".$datefinal."', 'dd/mm/yyyy')";

}elseif ($tipo==2) {
      $sql .= "AND licenciamento.processo_x_licencaemitida.prole_datalancamento between to_date('".$dateinicial."','dd/mm/yyyy') and to_date('".$datefinal."', 'dd/mm/yyyy')";
}


	if ($tipologia != 0)
      $sql .= "AND (apoio.tipoprocesso.protp_codigo = " . $tipologia . ") ";


   if ($area != 0)
      $sql .= "AND (licenciamento.processo_x_area.are_codigo = " . $area . ") ";


  	if ($municipio != 0){
      $sql .= " AND ((dadosbasico.empreendimentoendereco.emen_municipio = '" .$municipio. "') "
           . "  OR (dadosbasico.propriedaderuralendereco.proprend_municipio = '" .$municipio. "')) ";
  }



try {
  $stmt = $db->query($sql);
  $result = $stmt->setFetchMode(PDO::FETCH_NUM);


  while ($row = $stmt->fetch()) {
       echo "<tr><td><font size=2>".$row[0] ."</font></td><td><font size=2>". $row[1] ."</font></td><td><font size=2>". $row[2]."</font></td><td><font size=2>".$row[3]."</font></td><td><font size=2>".$row[4] ."</font></td><td><font size=2>-> " .$row[5]."</font></td><td><font size=2>-> ".$row[6]."</font></td><td><font size=2>".$row[7]."</font></td><td><font size=2>".$row[8]."</font></td><td><font size=2>".substr_replace($row[9], '***.***', 4, -3)."</font></td><td><font size=2>".$row[10]."</font></td><td><font size=2>".$row[11]."</font></td><td><font size=2>".$row[12]."</font></td></tr>";
  }
  $ativo="";
}
catch (PDOException $e) {
  print $e->getMessage();
}



$db = null;

?>

</table>


