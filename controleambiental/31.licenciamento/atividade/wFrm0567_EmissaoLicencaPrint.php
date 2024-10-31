<?php
session_start();
require_once '../../../../funcoes/funcao.php';
$dirRel = new Utilidades;
if ($_SESSION["emp_codigo"] == "" || $_SESSION["fun_codigo"] == "" || $_SESSION["are_codigo"] == "")
	header("Location: ".$dirRel->getPath()."mensagem/erro.php");

require_once $dirRel->getPath().'funcoes/pdf.php';
require_once $dirRel->getPath().'funcoes/formatnumero.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Tableless.php';
require_once 'HTML/Template/IT.php';


define($dirRel->getPath().'includes/PARAGRAPH_STRING', '~~~');
$db->setFetchMode(DB_FETCHMODE_ASSOC);



if (empty($_POST['proc_codigo']))
      $proc_codigo = $_GET['proc_codigo'];
else
      $proc_codigo = $_POST['proc_codigo'];

if (empty($_POST['proc_codigo_vinculado']))
      $proc_codigo_vinculado = $_GET['proc_codigo_vinculado'];
else
      $proc_codigo_vinculado = $_POST['proc_codigo_vinculado'];

if (empty($_POST['tipo']))
      $tipo = $_GET['tipo'];
else
      $tipo = $_POST['tipo'];


if (empty($_POST['prole_numerolicenca']))
{
      $prole_numerolicenca = $_GET['prole_numerolicenca'];
}
else
{
      $prole_numerolicenca= $_POST['prole_numerolicenca'];
}


if(!empty($prole_numerolicenca))
{
	$prole_numerolicenca = strtr($prole_numerolicenca,'_\\','/');
}

if(!empty($proc_codigo_vinculado))
{
	$proc_leitura=$proc_codigo_vinculado;
}
elseif(!empty($proc_codigo))
{
	$proc_leitura=$proc_codigo;
}	

$sql_processo 	= " SELECT "
			    . " geral.secretaria.emp_razaosocial, " 
				. " geral.secretaria.emp_nomefantasia, "
                . " geral.secretaria.emp_endereco, "
                . " geral.secretaria.emp_complemento, "
                . " geral.secretaria.emp_localizacao, "
                . " geral.secretaria.emp_uf, "
                . " geral.secretaria.emp_razaosocial, "
                . " geometria.municipio.mun_descricao, "

				. " licenciamento.processo.emp_codigo AS emp_codigo_processo, " 
				. " licenciamento.processo.proc_numero, " 
				. " licenciamento.processo.proc_descricao, "
				. " upper(apoio.tipoprocesso.protp_sigla) AS protp_sigla , "
				. " upper(apoio.tipoprocesso.protp_descricao) AS protp_descricao , "
				. " licenciamento.minuta.minu_numero, "
				. " licenciamento.minuta.minu_motivo, "
				. " licenciamento.minuta.minu_prazovalidade, "
				. " licenciamento.minuta.minu_prazo_d_m_a, "
				. " licenciamento.minuta.minu_enderecoatividade, "
				. " licenciamento.minuta.minu_codigo, "
			
				. " licenciamento.atividade_divisao.atdv_descricao, "
				. " licenciamento.atividade_grupo.atgp_descricao, "
				. " licenciamento.atividade.ativ_descricao, "
				. " licenciamento.processo_x_licencaemitida.prole_numerolicenca, "
				. " licenciamento.processo_x_licencaemitida.prole_datalancamento, "
				
				. " CASE "
				. " WHEN dadosbasico.propriedaderural.propr_codigo is not null THEN "
				. "      CASE "
				. "         WHEN dadosbasico.propriedaderural.propr_capr is not null THEN "
	            . "              'CAPR: '||dadosbasico.propriedaderural.propr_capr||' - '||dadosbasico.propriedaderural.propr_nome"
				. "         ELSE "
				. "               dadosbasico.propriedaderural.propr_nome"
				. "      end "
				. " WHEN dadosbasico.empreendimento.empr_codigo is not null THEN "
				. "      'CAE: '||dadosbasico.empreendimento.empr_codigo||' - '||dadosbasico.empreendimento.empr_nome"
				. " end AS empr_capr, "
				
				. " dadosbasico.clientecontato.clc_nome, "
				. " dadosbasico.clientecontato.clc_funcao, "
				. " dadosbasico.clientecontato.clc_cpf, "
				. " dadosbasico.clientecontato.clc_rg, "
				. " dadosbasico.clientecontato.clc_endereco, "
				. " dadosbasico.clientecontato.clc_complemento, "
				. " dadosbasico.clientecontato.clc_bairro, "
				. " municipiocontato.ufe_codigo AS ufecontato, "
				. " municipiocontato.mun_descricao AS muncontato, "
				. " dadosbasico.clientecontato.clc_localizacao, "
				
				//empreendimento
				. " clienteempreendimento.clb_codigo AS clb_codigo_cae, "
				. " clienteempreendimento.clb_razaosocial AS clb_razaosocial_cae, "
				. " clienteempreendimento.clb_cnpj AS clb_cnpj_cae, "
				. " clienteempreendimento.clb_inscricaoestadual AS clb_inscricaoestadual_cae, "
				. " clienteempreendimento.clb_cpf AS clb_cpf_cae, "
				. " clienteempreendimento.clb_rg AS clb_rg_cae, "
				. " clienteempreendimento.clb_apelido AS clb_apelido_cae,"
		
				. " clienteendereco_cae_capr.cee_endereco AS cee_endereco_cae , "
				. " clienteendereco_cae_capr.cee_complemento AS cee_complemento_cae, "
				. " clienteendereco_cae_capr.cee_bairro AS cee_bairro_cae, "
				. " clienteendereco_cae_capr.cee_localizacao AS cee_localizacao_cae, "
				. " municipioclienteendereco_cae_capr.mun_descricao AS mun_descricao_cae, "
				. " municipioclienteendereco_cae_capr.ufe_codigo AS ufe_codigo_cae, "
				. " tipologradouroclienteendereco_cae_capr.tplo_descricao AS tplo_descricao_cae, "
				
				. " tiporequerenteclienteempreendimento.tprq_descricao AS tprq_descricao_cae, "

				. " dadosbasico.empreendimentoendereco.empr_codigo, "
				. " dadosbasico.empreendimentoendereco.emen_sequencia, "
				. " dadosbasico.empreendimentoendereco.emen_endereco, "
				. " dadosbasico.empreendimentoendereco.emen_complemento, "
				. " dadosbasico.empreendimentoendereco.emen_localizacao, "
				. " dadosbasico.empreendimentoendereco.emen_bairro, "
				. " municipioempreendimentoendereco.mun_descricao AS mun_empreendimento, "
				. " municipioempreendimentoendereco.ufe_codigo AS ufe_empreendimento,  "
				. " tipologradouroempreendimentoendereco.tplo_descricao AS tplo_descricaoempreendimento, "
				

		        //Propriedade
				. " clientepropriedade.clb_codigo AS clb_codigo_capr, "
				. " clientepropriedade.clb_razaosocial AS clb_razaosocial_capr , "
				. " clientepropriedade.clb_cnpj AS clb_cnpj_capr, "
				. " clientepropriedade.clb_inscricaoestadual AS clb_inscricaoestadual_capr, "
				. " clientepropriedade.clb_cpf AS clb_cpf_capr, "
				. " clientepropriedade.clb_rg AS clb_rg_capr , "
				. " clientepropriedade.clb_apelido AS clb_apelido_capr,"
		
				. " clienteenderecopropriedade.cee_endereco AS cee_endereco_capr , "
				. " clienteenderecopropriedade.cee_complemento AS cee_complemento_capr , "
				. " clienteenderecopropriedade.cee_bairro AS cee_bairro_capr, "
				. " clienteenderecopropriedade.cee_localizacao AS cee_localizacao_capr, "
				. " municipiopropriedaderuralendereco.mun_descricao AS mun_descricao_capr, "
				. " municipiopropriedaderuralendereco.ufe_codigo AS ufe_codigo_capr, "
				. " tiporequerenteclientepropriedade.tprq_descricao AS  tprq_descricao_capr, "
				. " tipologradouroclienteenderecopropriedade.tplo_descricao AS tplo_descricaoclienteendereco_capr, "

				. " dadosbasico.propriedaderuralendereco.proprend_endereco, "
				. " dadosbasico.propriedaderuralendereco.proprend_complemento, "
				. " dadosbasico.propriedaderuralendereco.proprend_localizacao, "
				. " municipiopropriedaderuralendereco.mun_descricao AS mun_propriedaderural, "
				. " municipiopropriedaderuralendereco.ufe_codigo AS ufe_propriedaderural,  "
                . " tipologradouropropriedaderuralendereco.tplo_descricao AS tplo_descricaopropriedade "
				
		
				. " FROM "
				. " licenciamento.processo "
				
				. " INNER JOIN licenciamento.processo_x_tipoprocesso ON "
				. " (licenciamento.processo.emp_codigo=licenciamento.processo_x_tipoprocesso.emp_codigo) AND "
				. " (licenciamento.processo.proc_codigo=licenciamento.processo_x_tipoprocesso.proc_codigo) AND "
				. " (licenciamento.processo_x_tipoprocesso.progr_codigo in (1,25,28))"
				
				. " INNER JOIN apoio.tipoprocesso ON "
				. " (licenciamento.processo_x_tipoprocesso.prfam_codigo=apoio.tipoprocesso.prfam_codigo) AND "
				. " (licenciamento.processo_x_tipoprocesso.progr_codigo=apoio.tipoprocesso.progr_codigo) AND "
				. " (licenciamento.processo_x_tipoprocesso.protp_codigo=apoio.tipoprocesso.protp_codigo) "
				
				. " LEFT OUTER JOIN licenciamento.minuta ON "
				. " (licenciamento.processo.emp_codigo=licenciamento.minuta.emp_codigo) AND "
				. " (licenciamento.processo.proc_codigo=licenciamento.minuta.proc_codigo) AND "
				. " (licenciamento.minuta.minu_excluido_s_n='N')"
				// empreendimento
				. " LEFT OUTER JOIN dadosbasico.empreendimento_x_cliente_x_processo ON "
				. " (licenciamento.processo.proc_codigo=dadosbasico.empreendimento_x_cliente_x_processo.proc_codigo) "
				. " LEFT OUTER JOIN dadosbasico.empreendimento ON "
				. " (dadosbasico.empreendimento_x_cliente_x_processo.empr_codigo=dadosbasico.empreendimento.empr_codigo) "
				. " LEFT OUTER JOIN dadosbasico.cliente clienteempreendimento ON "
				. " (dadosbasico.empreendimento_x_cliente_x_processo.clb_codigo=clienteempreendimento.clb_codigo)"
				. " LEFT OUTER JOIN apoio.tiporequerente tiporequerenteclienteempreendimento ON "
				. " (clienteempreendimento.tprq_codigo=tiporequerenteclienteempreendimento.tprq_codigo) "
				
				. " LEFT OUTER JOIN dadosbasico.empreendimentoendereco ON "
				. " (dadosbasico.empreendimento.empr_codigo=dadosbasico.empreendimentoendereco.empr_codigo) AND "
				. " (dadosbasico.empreendimentoendereco.emen_excluido_s_n='N')"
				. " LEFT OUTER JOIN apoio.tipologradouro tipologradouroempreendimentoendereco ON "
				. " (dadosbasico.empreendimentoendereco.tplo_codigo=tipologradouroempreendimentoendereco.tplo_codigo) "
				. " LEFT OUTER JOIN geometria.municipio municipioempreendimentoendereco ON "
				. " (dadosbasico.empreendimentoendereco.emen_municipio=municipioempreendimentoendereco.mun_ibge) "

	
				. " LEFT OUTER JOIN dadosbasico.clienteendereco clienteendereco_cae_capr ON "
				. " (clienteempreendimento.clb_codigo=clienteendereco_cae_capr.clb_codigo) AND "
				. " (clienteendereco_cae_capr.cee_excluido_s_n = 'N') "
				. " LEFT OUTER JOIN apoio.tipologradouro tipologradouroclienteendereco_cae_capr  ON "
				. " (clienteendereco_cae_capr.tplo_codigo=tipologradouroclienteendereco_cae_capr.tplo_codigo) "
				. " LEFT OUTER JOIN geometria.municipio municipioclienteendereco_cae_capr ON "
				. " (clienteendereco_cae_capr.cee_municipio=municipioclienteendereco_cae_capr.mun_ibge) "

				
				//propriedade
				. " LEFT OUTER JOIN dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb ON "
				. " (licenciamento.processo.proc_codigo=dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb.proc_codigo) "
				. " LEFT OUTER JOIN dadosbasico.propriedaderural ON "
				. " (dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb.propr_codigo=dadosbasico.propriedaderural.propr_codigo) "
				. " LEFT OUTER JOIN dadosbasico.cliente clientepropriedade ON "
				. " (dadosbasico.propriedaderural_x_processo_x_cliente_x_tpclb.clb_codigo=clientepropriedade.clb_codigo) "
				. " LEFT OUTER JOIN apoio.tiporequerente tiporequerenteclientepropriedade ON "
				. " (clientepropriedade.tprq_codigo=tiporequerenteclientepropriedade.tprq_codigo) "
		
				. " LEFT OUTER JOIN dadosbasico.propriedaderuralendereco ON "
				. " (dadosbasico.propriedaderural.propr_codigo=dadosbasico.propriedaderuralendereco.propr_codigo) AND "
				. " (dadosbasico.propriedaderuralendereco.proprend_excluido_s_n='N')"
				. " LEFT OUTER JOIN apoio.tipologradouro tipologradouropropriedaderuralendereco ON "
				. " (dadosbasico.propriedaderuralendereco.tplo_codigo=tipologradouropropriedaderuralendereco.tplo_codigo) "
				. " LEFT OUTER JOIN geometria.municipio municipiopropriedaderuralendereco ON "
				. " (dadosbasico.propriedaderuralendereco.proprend_municipio=municipiopropriedaderuralendereco.mun_ibge) "
		
				. " LEFT OUTER JOIN dadosbasico.clienteendereco clienteenderecopropriedade ON "
				. " (clientepropriedade.clb_codigo=clienteenderecopropriedade.clb_codigo) AND "
				. " (clienteenderecopropriedade.cee_excluido_s_n = 'N') "
				. " LEFT OUTER JOIN apoio.tipologradouro tipologradouroclienteenderecopropriedade  ON "
				. " (clienteenderecopropriedade.tplo_codigo=tipologradouroclienteenderecopropriedade.tplo_codigo) "
				. " LEFT OUTER JOIN geometria.municipio municipiopropriedade ON "
				. " (clienteenderecopropriedade.cee_municipio=municipiopropriedade.mun_ibge) "
		        
				// contato
				. " LEFT OUTER JOIN licenciamento.processo_x_clientecontato ON "
				. " (licenciamento.processo.emp_codigo=licenciamento.processo_x_clientecontato.emp_codigo) AND "
				. " (licenciamento.processo.proc_codigo=licenciamento.processo_x_clientecontato.proc_codigo) "
				. " LEFT OUTER JOIN dadosbasico.clientecontato ON "
				. " (licenciamento.processo_x_clientecontato.clb_codigo=dadosbasico.clientecontato.clb_codigo) AND "
				. " (licenciamento.processo_x_clientecontato.clc_sequencia=dadosbasico.clientecontato.clc_sequencia)"
				. " LEFT OUTER JOIN geometria.municipio municipiocontato ON "
				. " (dadosbasico.clientecontato.clc_municipio=municipiocontato.mun_ibge) "
				
				
				. " LEFT OUTER JOIN licenciamento.processo_x_atividade ON "
				. " (licenciamento.processo.proc_codigo=licenciamento.processo_x_atividade.proc_codigo) "
				. " LEFT OUTER JOIN licenciamento.atividade_divisao ON "
				. " (licenciamento.processo_x_atividade.atdv_codigo=licenciamento.atividade_divisao.atdv_codigo) "
				. " LEFT OUTER JOIN licenciamento.atividade_grupo ON "
				. " (licenciamento.processo_x_atividade.atdv_codigo=licenciamento.atividade_grupo.atdv_codigo) AND "
				. " (licenciamento.processo_x_atividade.atgp_codigo=licenciamento.atividade_grupo.atgp_codigo) "
				. " LEFT OUTER JOIN licenciamento.atividade ON "
				. " (licenciamento.processo_x_atividade.atdv_codigo=licenciamento.atividade.atdv_codigo) AND "
				. " (licenciamento.processo_x_atividade.atgp_codigo=licenciamento.atividade.atgp_codigo) AND "
				. " (licenciamento.processo_x_atividade.ativ_codigo=licenciamento.atividade.ativ_codigo) "
				
				. " LEFT OUTER JOIN licenciamento.processo_x_licencaemitida ON "
				. " (licenciamento.processo.proc_codigo=licenciamento.processo_x_licencaemitida.proc_codigo) "
				
				. " INNER JOIN geral.secretaria ON "
				. " (licenciamento.processo.emp_codigo = geral.secretaria.emp_codigo) "
				. " INNER JOIN geometria.municipio ON "
				. " geral.secretaria.emp_municipio = geometria.municipio.mun_ibge "

				. " WHERE "
				. " (licenciamento.processo.proc_codigo = ".$proc_leitura.")"
				. " ORDER BY "
				. " emp_codigo_processo"
				. " LIMIT 1";
$res_processo = $db->query($sql_processo);
debugDB($res_processo);

//echo $sql_processo."<hr>"."<hr>";

while ($rowProcesso =& $res_processo->fetchRow()) {
      $emp_codigo_processo = trim($rowProcesso['emp_codigo_processo']);
      $proc_numero = trim($rowProcesso['proc_numero']);
	  $proc_descricao = trim($rowProcesso['proc_descricao']);
	  $protp_descricao = strtoupper(trim($rowProcesso['protp_descricao']));
	  $protp_sigla = trim($rowProcesso['protp_sigla']);
	  $atdv_descricao = trim($rowProcesso['atdv_descricao']);
	  $atgp_descricao = trim($rowProcesso['atgp_descricao']);
  	  $ativ_descricao = trim($rowProcesso['ativ_descricao']);
	  $minu_motivo = trim($rowProcesso['minu_motivo']);
	  $minu_prazovalidade = trim($rowProcesso['minu_prazovalidade']);
	  $minu_prazo_d_m_a= trim($rowProcesso['minu_prazo_d_m_a']);
	  $minu_enderecoatividade= trim($rowProcesso['minu_enderecoatividade']);
	  $minu_codigo= trim($rowProcesso['minu_codigo']);

	  $prole_numerolicenca= trim($rowProcesso['prole_numerolicenca']);
	  $prole_datalancamento= trim($rowProcesso['prole_datalancamento']);
	 
	 if(!empty($rowProcesso['clb_codigo_cae']))
	 {
	 	$clb_razaosocial = trim($rowProcesso['clb_razaosocial_cae']);
	    $clb_apelido = trim($rowProcesso['clb_apelido_cae']);
	    $clb_cnpj = trim($rowProcesso['clb_cnpj_cae']);
	    $clb_inscricaoestadual = trim($rowProcesso['clb_inscricaoestadual_cae']);
	    $clb_cpf = trim($rowProcesso['clb_cpf_cae']);
	    $clb_rg = trim($rowProcesso['clb_rg_cae']);
    	$tprq_descricao = trim($rowProcesso['tprq_descricao_cae']);
	
	    $cee_endereco = trim($rowProcesso['cee_endereco_cae']);
	    $cee_complemento = trim($rowProcesso['cee_complemento_cae']);
	    $cee_bairro = trim($rowProcesso['cee_bairro_cae']);
	    $cee_localizacao = trim($rowProcesso['cee_localizacao_cae']);
	    $mun_descricao = trim($rowProcesso['mun_descricao_cae']);
	    $ufe_codigo = trim($rowProcesso['ufe_codigo_cae']);
  	    $tplo_descricao = trim($rowProcesso['tplo_descricao_cae']);
		
	 } 	
	 elseif(!empty($rowProcesso['clb_codigo_capr']))
	 {
	 	$clb_razaosocial = trim($rowProcesso['clb_razaosocial_capr']);
	    $clb_apelido = trim($rowProcesso['clb_apelido_capr']);
	    $clb_cnpj = trim($rowProcesso['clb_cnpj_capr']);
	    $clb_inscricaoestadual = trim($rowProcesso['clb_inscricaoestadual_capr']);
	    $clb_cpf = trim($rowProcesso['clb_cpf_capr']);
	    $clb_rg = trim($rowProcesso['clb_rg_capr']);
    	$tprq_descricao = trim($rowProcesso['tprq_descricao_capr']);
	
	    $cee_endereco = trim($rowProcesso['cee_endereco_capr']);
	    $cee_complemento = trim($rowProcesso['cee_complemento_capr']);
	    $cee_bairro = trim($rowProcesso['cee_bairro_capr']);
	    $cee_localizacao = trim($rowProcesso['cee_localizacao_capr']);

	    $mun_descricao = trim($rowProcesso['mun_descricao_capr']);
	    $ufe_codigo = trim($rowProcesso['ufe_codigo_capr']);
  	    $tplo_descricao = trim($rowProcesso['tplo_descricaoclienteendereco_capr']);
	 } 	
	 $clc_nome = trim($rowProcesso['clc_nome']);
	 $clc_funcao = trim($rowProcesso['clc_funcao']);
	 $clc_cpf = trim($rowProcesso['clc_cpf']);
	 $clc_rg = trim($rowProcesso['clc_rg']);
	 $clc_endereco = trim($rowProcesso['clc_endereco']);
	 $clc_complemento = trim($rowProcesso['clc_complemento']);
	 $clc_localizacao = trim($rowProcesso['clc_localizacao']);
	 $clc_bairro = trim($rowProcesso['clc_bairro']);
	 $muncontato = trim($rowProcesso['muncontato']);
	 $ufecontato = trim($rowProcesso['ufecontato']);


	  
	 $tplo_empreendimento = trim($rowProcesso['tplo_empreendimento']);
	 if(!empty($rowProcesso['proprend_endereco']))
	 {
  	 	$tplo = trim($rowProcesso['tplo_descricaopropriedade']);
	  	$endereco = trim($rowProcesso['proprend_endereco']);
	  	$complemento = trim($rowProcesso['proprend_complemento']);
	  	$localizacao = trim($rowProcesso['proprend_localizacao']);
	  	$municipio = trim($rowProcesso['mun_propriedaderural']);
	  	$ufe = trim($rowProcesso['ufe_propriedaderural']);
	 }
	 elseif(!empty($rowProcesso['emen_sequencia']))
	 {
	  	$tplo = trim($rowProcesso['tplo_descricaoempreendimento']);
	  	$endereco = trim($rowProcesso['emen_endereco']);
	  	$complemento = trim($rowProcesso['emen_complemento']);
	  	$localizacao = trim($rowProcesso['emen_localizacao']);
	  	$bairro = trim($rowProcesso['emen_bairro']);
	  	$municipio = trim($rowProcesso['mun_empreendimento']);
	  	$ufe = trim($rowProcesso['ufe_empreendimento']);
	 }
	 $empr_codigo = trim($rowProcesso['empr_codigo']);

	 if(empty($endereco)){
	 	$sql_end = " SELECT DISTINCT 
					  apoio.tipologradouro.tplo_descricao,
					  dadosbasico.propriedaderuralendereco.proprend_endereco,
					  dadosbasico.propriedaderuralendereco.proprend_complemento,
					  dadosbasico.propriedaderuralendereco.proprend_localizacao,
					  dadosbasico.propriedaderuralendereco.ufe_codigo,
					  geometria.municipio.mun_descricao
					FROM
					  dadosbasico.propriedaderural_x_empreendimento
					  INNER JOIN dadosbasico.propriedaderuralendereco ON 
					  (dadosbasico.propriedaderural_x_empreendimento.propr_codigo = dadosbasico.propriedaderuralendereco.propr_codigo)
					  INNER JOIN apoio.tipologradouro ON (dadosbasico.propriedaderuralendereco.tplo_codigo = apoio.tipologradouro.tplo_codigo)
					  INNER JOIN geometria.municipio ON (dadosbasico.propriedaderuralendereco.proprend_municipio = geometria.municipio.mun_ibge)
					WHERE
					  dadosbasico.propriedaderural_x_empreendimento.empr_codigo = ".$empr_codigo." AND 
					  dadosbasico.propriedaderuralendereco.proprend_excluido_s_n = 'N'";
		$res_end = $db->query($sql_end);
		debugDB($res_end);
	
		while ($rowEnd =& $res_end->fetchRow()) {
			$tplo = trim($rowEnd['tplo_descricao']);
			$endereco = trim($rowEnd['proprend_endereco']);
			$complemento = trim($rowEnd['proprend_complemento']);
			$localizacao = trim($rowEnd['proprend_localizacao']);
			$municipio = trim($rowEnd['mun_descricao']);
			$ufe = trim($rowEnd['ufe_codigo']);
		}
	 }
	 
	 
	 //print_r($rowProcesso);
	  
	  $empr_capr=trim($rowProcesso['empr_capr']);

      $emp_endereco = trim($rowProcesso['emp_endereco']);
      $emp_complemento = trim($rowProcesso['emp_complemento']);
      $emp_localizacao = trim($rowProcesso['emp_localizacao']);
      $emp_mundescricao = trim($rowProcesso['mun_descricao']);
      $emp_uf = trim($rowProcesso['emp_uf']);
	  
	  $emp_razaosocial= trim($rowProcesso['emp_razaosocial']);
	  $emp_nomefantasia= trim($rowProcesso['emp_nomefantasia']);

}

$endereco_cee = trim($tplo_descricao) ." ". trim($cee_endereco) ;

if(!empty($cee_complemento))
{
	$endereco_cee .= ", " . trim($cee_complemento);
}
if(!empty($cee_bairro))	
{
	$endereco_cee .= ", " . $cee_bairro . ", " . $mun_descricao . " - " . $ufe_codigo;     

}
else
{
	$endereco_cee .= ", " . $mun_descricao . " - " . $ufe_codigo;     
}

//echo $endereco_cee;
$endereco_cae_capr= trim($tplo)." ". trim($endereco) ;

  
 
if(!empty($complemento))
{
	$endereco_cae_capr .= ", " . trim($complemento);
}

if(!empty($localizacao))	
{
	//$endereco_cae_capr .= ", " . $localizacao;     

}


if(!empty($bairro))	
{
	$endereco_cae_capr .= ", " . $bairro . ", " . $municipio . " - " . $ufe;     

}
else
{
	$endereco_cae_capr .= ", " . $municipio . " - " . $ufe;     
}

//echo $endereco_cae_capr;



if(!empty($clc_complemento)){$enderecocontato= trim($clc_endereco) . ", " .trim($clc_complemento);}else{$enderecocontato= trim($clc_endereco);}
if(!empty($clc_localizacao))
{
	$enderecocontato.= ", " .trim($clc_localizacao); 
}

if(!empty($clc_bairro)){$enderecocontato.=", ".$clc_bairro.", ".$muncontato." - ".$ufecontato;}else{$enderecocontato.= ", ".$muncontato." - ".$ufecontato;}


if (!empty($clb_apelido))
{
	$clb_razaosocial=$clb_razaosocial . " - " . $clb_apelido;
}

//Arruma a validade
if ($minu_prazovalidade != '1'){
   if ($minu_prazo_d_m_a=="M�s") 
   {
   		$minu_prazo_d_m_a = "Meses";
   }
   else
   {
   		$minu_prazo_d_m_a = $minu_prazo_d_m_a . "s";
   }  
}

$extenso=num2words($minu_prazovalidade);



$sql_emissao = " SELECT "
             . " licenciamento.minutaemissao.miem_dataemissao, "
			 . " geral.funcionario.fun_nome, "
			 . " geral.cargo.crg_descricao, "
			 . " geral.parametro_secretario.texto_nomeacao, "
			 . " geral.parametro_secretario.tratamento,"
			 . " geral.parametro_secretario.sigla"
			 . " FROM "
			 . " licenciamento.minutaemissao "
			 . " INNER JOIN geral.funcionario ON "
			 . " (licenciamento.minutaemissao.fun_codigo_assinaturalicenca = geral.funcionario.fun_codigo)"
			 . " INNER JOIN geral.cargo ON "
			 . " (geral.funcionario.crg_codigo = geral.cargo.crg_codigo) "
			 . " INNER JOIN geral.parametro_secretario ON "
			 . " (licenciamento.minutaemissao.fun_codigo_assinaturalicenca = geral.parametro_secretario.fun_codigo)"
			 . " WHERE "
			 . " (licenciamento.minutaemissao.proc_codigo = ".$proc_leitura.")";
$res_emissao = $db->query($sql_emissao);
debugDB($res_emissao);

//Presidente
while ($rowEmissao =& $res_emissao->fetchRow()) {
      $miem_dataemissao = substr(trim($rowEmissao['miem_dataemissao']),0,10);
      $fun_nome_assinatura = trim($rowEmissao['fun_nome']);
      $crg_descricao_assinatura = trim($rowEmissao['crg_descricao']);
	  $texto_nomeacao= trim($rowEmissao['texto_nomeacao']);
	  $tratamento= trim($rowEmissao['tratamento']);
	  $sigla= trim($rowEmissao['sigla']);
}

$sql_condicionantes = " SELECT DISTINCT"
				   . " licenciamento.categoriaminutacondicionante.micoca_codigo, "
				   . " licenciamento.categoriaminutacondicionante.micoca_descricao "
				   . " FROM "
				   . " licenciamento.minutacondicionante "
				   . " INNER JOIN licenciamento.categoriaminutacondicionante ON "
				   . " (licenciamento.minutacondicionante.micoca_codigo = licenciamento.categoriaminutacondicionante.micoca_codigo) "
				   . " WHERE "
				   . " (licenciamento.minutacondicionante.proc_codigo = ".$proc_leitura.") AND "
				   . " (licenciamento.minutacondicionante.minu_codigo = ".$minu_codigo.") AND "
				   . " (licenciamento.minutacondicionante.micond_excluido_s_n = 'N')"
				   . " ORDER BY "
				   . " licenciamento.categoriaminutacondicionante.micoca_descricao";
$res_condicionantes =& $db->query($sql_condicionantes);
debugDB($res_condicionantes);
	
	
	//echo $sql_condicionantes;
	
	
$res =& $db->getOne($sql_condicionantes);
debugDB($res);


if(!empty($res['micoca_codigo']))
{
	$cond=1;
}
else
{
	$cond=0;
}


$sql_determinacao = " SELECT "
				. " licenciamento.determinacaoespecifica.dete_texto "
				. " FROM "
				. " licenciamento.minuta_x_determinacaoespecifica "
				. " INNER JOIN licenciamento.determinacaoespecifica ON "
				. " (licenciamento.minuta_x_determinacaoespecifica.emp_codigo=licenciamento.determinacaoespecifica.emp_codigo) AND "
				. " (licenciamento.minuta_x_determinacaoespecifica.dete_codigo=licenciamento.determinacaoespecifica.dete_codigo) "
				. " WHERE "
				. " (licenciamento.minuta_x_determinacaoespecifica.proc_codigo = ".$proc_leitura.") "
				. " ORDER BY licenciamento.determinacaoespecifica.dete_codigo ASC";
$res_determinacao = $db->query($sql_determinacao);
debugDB($res_determinacao);

$res =& $db->getOne($sql_determinacao);
debugDB($res);

if(!empty($res['dete_texto']))
{
	$dete=1;
}
else
{
	$dete=0;
}

$sql_enquadramento = " SELECT DISTINCT"
				 . " juridico.fundamentolegal.fule_codigo, "
				 . " juridico.fundamentolegal.fule_descricao, "
				 . " juridico.fundamentolegal.fule_classificacao_f_e_m "
				 . " FROM "
				 . " juridico.estruturajuridica_x_tipoprocesso_x_atividade_x_fundamentolegal"
				 . " INNER JOIN licenciamento.processo_x_atividade ON "
				 . " (juridico.estruturajuridica_x_tipoprocesso_x_atividade_x_fundamentolegal.emp_codigo = "
				 . "  licenciamento.processo_x_atividade.emp_codigo) AND "
				 . " (juridico.estruturajuridica_x_tipoprocesso_x_atividade_x_fundamentolegal.atdv_codigo = "
				 . "  licenciamento.processo_x_atividade.atdv_codigo) AND "
				 . " (juridico.estruturajuridica_x_tipoprocesso_x_atividade_x_fundamentolegal.atgp_codigo = "
				 . "  licenciamento.processo_x_atividade.atgp_codigo) AND "
				 . " (juridico.estruturajuridica_x_tipoprocesso_x_atividade_x_fundamentolegal.ativ_codigo = "
				 . "  licenciamento.processo_x_atividade.ativ_codigo) "
				 . " INNER JOIN juridico.fundamentolegal ON "
				 . " (juridico.estruturajuridica_x_tipoprocesso_x_atividade_x_fundamentolegal.emp_codigo = "
				 . "  juridico.fundamentolegal.emp_codigo) AND "
				 . " (juridico.estruturajuridica_x_tipoprocesso_x_atividade_x_fundamentolegal.fule_codigo = "
				 . "  juridico.fundamentolegal.fule_codigo) "
				 . " WHERE "
				 . " (licenciamento.processo_x_atividade.proc_codigo = ".$proc_leitura.") AND "
				 . " (juridico.estruturajuridica_x_tipoprocesso_x_atividade_x_fundamentolegal.estpat_excluido_s_n = 'N') AND "
				 . " (juridico.fundamentolegal.fule_excluido_s_n = 'N')";
$res_enquadramento = $db->query($sql_enquadramento);
debugDB($res_enquadramento);

$res =& $db->getOne($sql_enquadramento);
debugDB($res);



if(!empty($res['fule_descricao']))
{
	$enqu=1;
}
else
{
	$enqu=0;
}

$sql_mirecad = " SELECT DISTINCT "
             . " licenciamento.minutarecomendacaoadicional.mirecad_texto "
			 . " FROM "
			 . " licenciamento.minutarecomendacaoadicional "
			 . " WHERE "
			 . " (licenciamento.minutarecomendacaoadicional.proc_codigo = ".$proc_leitura.") AND "
			 . " (licenciamento.minutarecomendacaoadicional.minu_codigo = ".$minu_codigo.") AND "
			 . " (licenciamento.minutarecomendacaoadicional.mirecad_excluido_s_n = 'N')";
$res_mirecad = $db->query($sql_mirecad);
debugDB($res_mirecad);



$res =& $db->getOne($sql_mirecad);
debugDB($res);

if(!empty($res['mirecad_texto']))
{
	$mire=1;
}
else
{
	$mire=0;
}

$sql_procloc = " SELECT "
             . " apoio.tipopontogeo.tpptgeo_descricao, "
			 . " apoio.tipoprojecao.tpprj_descricao, "
			 . " geo_empreendimento.empreendimento_localizacao_dados.x, "
			 . " geo_empreendimento.empreendimento_localizacao_dados.y, "
			 . " geo_empreendimento.empreendimento_localizacao_dados.desc_ponto "
			 . " FROM "
			 . " licenciamento.parecertecnico_x_processo "
			 . " INNER JOIN licenciamento.parecertecnico ON "
			 . " (licenciamento.parecertecnico_x_processo.pate_sequencia = licenciamento.parecertecnico.pate_sequencia) "
			 . " INNER JOIN apoio.tipoparecertecnico ON "
			 . " (licenciamento.parecertecnico.pateti_codigo = apoio.tipoparecertecnico.pateti_codigo)"
			 . " INNER JOIN geo_empreendimento.empreendimento_localizacao_dados ON "
			 . " (licenciamento.parecertecnico_x_processo.pate_sequencia = geo_empreendimento.empreendimento_localizacao_dados.pate_sequencia) "
			 . " INNER JOIN apoio.tipopontogeo ON "
			 . " (geo_empreendimento.empreendimento_localizacao_dados.tpptgeo_codigo = apoio.tipopontogeo.tpptgeo_codigo) "
			 . " INNER JOIN apoio.tipoprojecao ON "
			 . " (geo_empreendimento.empreendimento_localizacao_dados.srid = apoio.tipoprojecao.tpprj_projecao) "
			 . " WHERE "
			 . " (licenciamento.parecertecnico_x_processo.proc_codigo = ".$proc_leitura.") AND "
			 . " (apoio.tipoparecertecnico.pateti_sigla = 'PTGEOAT') AND  "
			 . " (licenciamento.parecertecnico.pate_excluido_s_n = 'N')" 
			 . " ORDER BY "
			 . " geo_empreendimento.empreendimento_localizacao_dados.gid";
$res_procloc = $db->query($sql_procloc);
debugDB($res_procloc);
//echo $sql_procloc;
$res =& $db->getAll($sql_procloc);
debugDB($res);
$wctr=1;

if(empty($res))
{
	$sql_procloc = " SELECT "
             . " apoio.tipopontogeo.tpptgeo_descricao, "
			 . " apoio.tipoprojecao.tpprj_descricao, "
			 . " geo_propriedade.propriedaderural_localizacao_dados.x, "
			 . " geo_propriedade.propriedaderural_localizacao_dados.y, "
			 . " geo_propriedade.propriedaderural_localizacao_dados.desc_ponto "
			 . " FROM "
			 . " licenciamento.parecertecnico_x_processo "
			 . " INNER JOIN licenciamento.parecertecnico ON "
			 . " (licenciamento.parecertecnico_x_processo.pate_sequencia = licenciamento.parecertecnico.pate_sequencia) "
			 . " INNER JOIN apoio.tipoparecertecnico ON "
			 . " (licenciamento.parecertecnico.pateti_codigo = apoio.tipoparecertecnico.pateti_codigo)"
			 . " INNER JOIN geo_propriedade.propriedaderural_localizacao_dados ON "
			 . " (licenciamento.parecertecnico_x_processo.pate_sequencia = geo_propriedade.propriedaderural_localizacao_dados.pate_sequencia) "
			 . " INNER JOIN apoio.tipopontogeo ON "
			 . " (geo_propriedade.propriedaderural_localizacao_dados.tpptgeo_codigo = apoio.tipopontogeo.tpptgeo_codigo) "
			 . " INNER JOIN apoio.tipoprojecao ON "
			 . " (geo_propriedade.propriedaderural_localizacao_dados.srid = apoio.tipoprojecao.tpprj_projecao) "
			 . " WHERE "
			 . " (licenciamento.parecertecnico_x_processo.proc_codigo = ".$proc_leitura.") AND "
			 . " (apoio.tipoparecertecnico.pateti_sigla = 'PTGEOAT') AND  "
			 . " (licenciamento.parecertecnico.pate_excluido_s_n = 'N')" 
			 . " ORDER BY "
			 . " geo_propriedade.propriedaderural_localizacao_dados.gid";
	$res_procloc = $db->query($sql_procloc);
	debugDB($res_procloc);
	$res =& $db->getAll($sql_procloc);
	debugDB($res);
	$wctr=1;
}

if(empty($res))

{
	$sql_procloc = " SELECT DISTINCT "
				 . " apoio.tipopontogeo.tpptgeo_descricao, "
				 . " apoio.tipoprojecao.tpprj_descricao, "
				 . " licenciamento.processolocalizacao.procloc_lat_graus, "
				 . " licenciamento.processolocalizacao.procloc_lat_minuto, "
				 . " licenciamento.processolocalizacao.procloc_lat_segundo, "
				 . " licenciamento.processolocalizacao.procloc_long_graus, "
				 . " licenciamento.processolocalizacao.procloc_long_minuto, "
				 . " licenciamento.processolocalizacao.procloc_long_segundo, "
				 . " licenciamento.processolocalizacao.procloc_utm_x_a, "
				 . " licenciamento.processolocalizacao.procloc_utm_y_a, "
				 . " licenciamento.processolocalizacao.procloc_dec_x, "
				 . " licenciamento.processolocalizacao.procloc_dec_y, "
				 . " licenciamento.processolocalizacao.procloc_altitude, "
				 . " licenciamento.processolocalizacao.procloc_descricao "
				 . " FROM "
				 . " licenciamento.processolocalizacao "
				 . " INNER JOIN apoio.tipopontogeo ON  "
				 . " (licenciamento.processolocalizacao.tpptgeo_codigo = apoio.tipopontogeo.tpptgeo_codigo) "
				 . " INNER JOIN apoio.tipoprojecao ON "
				 . " (licenciamento.processolocalizacao.tpprj_codigo = apoio.tipoprojecao.tpprj_codigo) "
				 . " WHERE "
				 . " (licenciamento.processolocalizacao.proc_codigo = ".$proc_leitura.") AND "
				 . " (licenciamento.processolocalizacao.procloc_excluido_s_n = 'N')";
	$res_procloc = $db->query($sql_procloc);
	debugDB($res_procloc);
	
	$res =& $db->getAll($sql_procloc);
	debugDB($res);
	$wctr=0;
        //echo $sql_procloc;
}



if(!empty($res))
{
	$procloc=1;
	
}
else
{
	$procloc=0;
}

//echo $sql_procloc;


$pag1_tit1 = $protp_descricao. " n� ".$prole_numerolicenca;
$pag1_tit2 = "Referente a ".$protp_descricao. " n� ".$prole_numerolicenca;

$pag1_sub_tit0 = $empr_capr;
$pag1_sub_tit1 = "Processo Administrativo n� ".$proc_numero ;
$subtit=  $protp_descricao. " n� ".$prole_numerolicenca;


if(empty($miem_dataemissao))
{
	
	$paragrafo_1 = "         O <tb>" . $emp_razaosocial . " - " . $emp_nomefantasia . "</tb>,  atrav�s de <tb>(dados da assinatura)</tb> no uso de suas atribui��es que lhe confere a Lei n� 6.938, de 31 de Agosto de 1981, que disp�e sobre a Pol�tica Nacional do Meio Ambiente, regulamentada pelo Decreto n� 99.274, de 06 de Junho de 1990, Lei Ambiental Estadual n� 1.117 de 26 de Janeiro de 1994, bem como a Resolu��o do CONAMA n� 237, de 19 de Dezembro de 1997, e ainda a ".$texto_nomeacao." concede a presente <tb>" . $protp_descricao." </tb> n� <tb> ".$prole_numerolicenca."</tb>, a(ao) <tb>" .$clb_razaosocial. "</tb>,";
	
}
else
{
	$paragrafo_1 = "         O <tb>" . $emp_razaosocial . " - " . $emp_nomefantasia . "</tb>,  atrav�s de ".$tratamento." ".$crg_descricao_assinatura.", ".$sigla." <tb>".$fun_nome_assinatura."</tb> no uso de suas atribui��es que lhe confere a Lei n� 6.938, de 31 de Agosto de 1981, que disp�e sobre a Pol�tica Nacional do Meio Ambiente, regulamentada pelo Decreto n� 99.274, de 06 de Junho de 1990, Lei Ambiental Estadual n� 1.117 de 26 de Janeiro de 1994, bem como a Resolu��o do CONAMA n� 237, de 19 de Dezembro de 1997, e ainda a ".$texto_nomeacao." concede a presente <tb>" . $protp_descricao." </tb> n� <tb> ".$prole_numerolicenca."</tb>, a(ao) <tb>" .$clb_razaosocial. "</tb>,";
}


if ($tprq_descricao=="Pessoa Fisica")
{
	if($minu_enderecoatividade==1)
	{
		$paragrafo_1 = $paragrafo_1 . " residente na(o) ". $endereco_cee .", brasileiro(a), portador(a) do CPF n� " . $clb_cpf . " e RG n� " . $clb_rg . ", para a atividade de<tb> " . $minu_motivo .  "</tb>, localizado na(o) ".$endereco_cae_capr.".";
	}
	else
	{
		$paragrafo_1 = $paragrafo_1 . " residente na(o) ". $endereco_cee .", brasileiro(a), portador(a) do CPF n� " . $clb_cpf . " e RG n� " . $clb_rg . ",para a atividade de<tb> " . $minu_motivo .  "</tb>.";
	}
}
else
{
	if($minu_enderecoatividade==1)
	{
		$paragrafo_1 = $paragrafo_1 . " com CNPJ n� "  . $clb_cnpj; 
		
		if(empty($clb_inscricaoestadual) and $protp_sigla=='LP' )
		{
		    $paragrafo_1 = $paragrafo_1 .	", localizada na(o) " . $endereco_cee .", neste ato representado(a) pelo(a) Sr(a). <tb>" . $clc_nome . "</tb>, " .$clc_funcao.  ", brasileiro(a), portador do Registro Geral n� " . $clc_rg . ", inscrito no CPF n� " . $clc_cpf .  " residente e domiciliado na " .$enderecocontato.", para a atividade de  <tb>" .$minu_motivo. "</tb>, localizado na ".$endereco_cae_capr.".";
		}
		else
		{
		    $paragrafo_1 = $paragrafo_1 .	" e Inscri��o Estadual n� ".$clb_inscricaoestadual.", localizada na(o) " . $endereco_cee .", neste ato representado(a) pelo(a) Sr(a). <tb>" . $clc_nome . "</tb>, " .$clc_funcao.  "(a), brasileiro(a) portador do Registro Geral n� " . $clc_rg . ", inscrito no CPF n� " . $clc_cpf .  " residente e domiciliado na " .$enderecocontato.", para a atividade de <tb>" . $minu_motivo ."</tb>, localizado (a) em " .$endereco_cae_capr.".";
		
		
		}
	}
	else
	{
		
		$paragrafo_1 = $paragrafo_1 . " com CNPJ n� "  . $clb_cnpj;
		
		if(empty($clb_inscricaoestadual) and $protp_sigla=='LP' )
		{
			$paragrafo_1 = $paragrafo_1 . ", localizada na(o) " . $endereco_cee .", neste ato representado(a) pelo(a) Sr(a). <tb>" . $clc_nome . "</tb>, " .$clc_funcao.  ", brasileiro(a), portador do Registro Geral n� " . $clc_rg . ", inscrito no CPF n� " . $clc_cpf .  " residente e domiciliado na " .$enderecocontato.", para a atividade de <tb> " . $minu_motivo .  "</tb>.";
		}
		else
		{
	
			$paragrafo_1 = $paragrafo_1 . " e Inscri��o Estadual n� ".$clb_inscricaoestadual.", localizada na(o) " . $endereco_cee .", neste ato representado(a) pelo(a) Sr(a). <tb>" . $clc_nome . "</tb>, " .$clc_funcao.  "(a), brasileiro(a), portador(a) do Registro Geral n� " . $clc_rg . ", inscrito(a) no CPF n� " . $clc_cpf .  " residente e domiciliado(a) na " .$enderecocontato.", para a atividade de <tb> " . $minu_motivo .  "</tb>, localizado(a) em ".$endereco_cae_capr.".";
		}
	}  
}

if(!empty($prole_datalancamento))
{

	$mes = substr($prole_datalancamento,5,2);
	$dia =  substr($prole_datalancamento,8,2);
	$ano =  substr($prole_datalancamento,0,4);

	switch($mes){
		case "01" : $mes = "Janeiro";
		break;
		case "02" : $mes = "Fevereiro";
		break;
		case "03" : $mes = "Mar�o";
		break;
		case "04" : $mes = "Abril";
		break;
		case "05" : $mes = "Maio";
		break;
		case "06" : $mes = "Junho";
		break;
		case "07" : $mes = "Julho";
		break;
		case "08" : $mes = "Agosto";
		break;
		case "09" : $mes = "Setembro";
		break;
		case "10" : $mes = "Outubro";
		break;
		case "11" : $mes = "Novembro";
		break;
		case "12" : $mes = "Dezembro";
		break;
	}

}
else
{
	$mes = date("F");
	$dia = date("d");
	$ano = date("Y");
switch($mes){
    case "January" : $mes = "Janeiro";
    break;
    case "February" : $mes = "Fevereiro";
    break;
    case "March" : $mes = "Mar�o";
    break;
    case "April" : $mes = "Abril";
    break;
    case "May" : $mes = "Maio";
    break;
    case "June" : $mes = "Junho";
    break;
    case "July" : $mes = "Julho";
    break;
    case "August" : $mes = "Agosto";
    break;
    case "September" : $mes = "Setembro";
    break;
    case "October" : $mes = "Outubro";
    break;
    case "November" : $mes = "Novembro";
    break;
    case "December" : $mes = "Dezembro";
    break;
}



}


if(empty($prole_datalancamento))
{
	$paragrafo_2 = "         Esta <tb> ".$protp_descricao." </tb> � valida pelo per�odo de " .$minu_prazovalidade. " (".$extenso.") ".$minu_prazo_d_m_a." , a contar da presente data de seu recebimento, observando as condi��es deste documento e seus anexos que, embora n�o transcritas, s�o partes integrantes do mesmo. Sua renova��o dever� ser requerida com anteced�ncia de 120 (cento e vinte) dias de seu vencimento. A n�o renova��o ensejar� aplica��o de multa pelo �rg�o ambiental estadual."; 
}
elseif(substr($prole_datalancamento,0,10)<='2010-01-15')
{
	$paragrafo_2 = "         Esta <tb> ".$protp_descricao." </tb> � valida pelo per�odo de " .$minu_prazovalidade. " (".$extenso.") ".$minu_prazo_d_m_a." , a contar da presente data de seu recebimento, observando as condi��es deste documento e seus anexos que, embora n�o transcritas, s�o partes integrantes do mesmo. Sua renova��o dever� ser requerida com anteced�ncia de 90 (noventa) dias de seu vencimento. A n�o renova��o ensejar� aplica��o de multa pelo �rg�o ambiental estadual."; 
}
else
{
	$paragrafo_2 = "         Esta <tb> ".$protp_descricao." </tb> � valida pelo per�odo de " .$minu_prazovalidade. " (".$extenso.") ".$minu_prazo_d_m_a." , a contar da presente data de seu recebimento, observando as condi��es deste documento e seus anexos que, embora n�o transcritas, s�o partes integrantes do mesmo. Sua renova��o dever� ser requerida com anteced�ncia de 120 (cento e vinte) dias de seu vencimento. A n�o renova��o ensejar� aplica��o de multa pelo �rg�o ambiental estadual."; 
}
//PEGA A DATA ATUAL E TRADUZ PARA EXTENSO (BRASIL)
//-----------------------------------------------


$data = "$dia de $mes de $ano.";

$local_data = "Rio Branco (AC), ".$data."";


$presidente = $fun_nome_assinatura."\n".$crg_descricao_assinatura."\n";



//Constroi o texto da pagina 2 (Modelo de publica��o)
//---------------------------------------------------
$pag2_tit1 = "MODELO DE PUBLICA��O NO DI�RIO OFICIAL DO ESTADO E JORNAL DE CIRCULA��O LOCAL";

$paragrafo_3 = " Torna p�blico que recebeu do <tb>" . $emp_razaosocial . " - " . $emp_nomefantasia . "</tb>, a <tb>" . $protp_descricao." </tb> n� <tb> ".$prole_numerolicenca."</tb>, com validade de " .$minu_prazovalidade. " (".$extenso.") ".$minu_prazo_d_m_a." , para atividade de <tb> " . $minu_motivo .  "</tb>".", localizado � "./*.".$endereco_cee ."*/ "$endereco_cae_capr";



//Constroi o texto da pagina 3 (Termo de Compromisso)
//---------------------------------------------------

$pag3_tit1 = "TERMO DE COMPROMISSO ";

$paragrafo_4 = "         Pelo presente o(a) <tb>" . $clb_razaosocial.  "</tb>, ";


if ($tprq_descricao=="Pessoa Fisica")
{
	$paragrafo_4 = $paragrafo_4 . " residente e domiciliado na(o) " .$endereco_cee.", Brasileiro(a) portador do Registro Geral n� " . $clb_rg . " inscrito no CPF n� " . $clb_cpf;
    $compromissario = $clb_razaosocial."\nCompromiss�rio";
}
else
{
	$paragrafo_4 = $paragrafo_4 . " com CNPJ n� "  . $clb_cnpj . " e Inscri��o Estadual n� ".$clb_inscricaoestadual.", localizado na ". $endereco_cee.", neste ato representado(a) pelo(a) <tb>Sr(a). " . $clc_nome . "</tb>, brasileiro, " .$clc_funcao. " portador do Registro Geral n� ".$clc_rg. ", inscrito no CPF n� " . $clc_cpf.", residente e domiciliado na(o) " .$enderecocontato;  
    $compromissario = $clc_nome."\nCompromiss�rio";
}

$paragrafo_4 = $paragrafo_4 . ", declara neste e na melhor forma de direito, perante o <tb>" . $emp_razaosocial . " - " . $emp_nomefantasia. "</tb>, Autarquia Estadual criada pela Lei n� 851, de 23/10/1986, com sede " . $emp_endereco." n� " . $emp_complemento. " - " . $emp_localizacao. ", nesta cidade de " .$emp_mundescricao. " - " . $emp_uf . ", aqui neste ato representado pelo(a)seu ".$crg_descricao_assinatura.", que nos termos da <tb>Lei n� 6.938/81</tb> e <tb>Dec. n� 99.274/90, Art. 5� e 6� da Lei n� 7.347/85, Lei n� 1.117/94</tb>, e outros pertinentes, o compromisso de executar e fazer cumprir as seguintes <tb>DETERMINA��ES:</tb> ";  



$paragrafo_5 = "1. Determina��es Gerais";

$paragrafo_6  = "   1.1 Publicar, no prazo de 15 (quinze) dias, o recebimento da presente <tb>".$protp_descricao."</tb> no Di�rio Oficial do Estado e em 01 (um) jornal de circula��o local di�ria, conforme Resolu��o do CONAMA n� 006/86 ;";
$paragrafo_7  = "   1.2 Encaminhar ao <tb>".$emp_nomefantasia." </tb>, no prazo de 15 (quinze) dias, um exemplar do Di�rio Oficial do Estado e do jornal de circula��o local di�ria com as publica��es de recebimento da ".$protp_descricao. ";";

/*
if($protp_sigla=='LP')
{
	$paragrafo_8 = "    1.3 A <tb>".$protp_descricao."</tb> n� <tb>".$prole_numerolicenca ."</tb> s� d� direito a(ao) <tb>" .$clb_razaosocial. "</tb>, realizar <tb>PLANEJAMENTO E ELABORA��O DOS PROJETOS </tb>referentes a atividade de <tb>" .$atdv_descricao.", ".$atgp_descricao." ".  $ativ_descricao."</tb>;";

}
else
{
	$paragrafo_8 = "    1.3 A <tb>".$protp_descricao."</tb> n� <tb>".$prole_numerolicenca ."</tb> s� d� direito a(ao) <tb>" .$clb_razaosocial. "</tb> desenvolver a atividade de <tb>" .$atdv_descricao.", ".$atgp_descricao." ".  $ativ_descricao."</tb>;";
}
//$paragrafo_8 = "    1.3 A <tb>".$protp_descricao."</tb> n� <tb>".$prole_numerolicenca ."</tb> s� d� direito a(ao) <tb>" .$clb_razaosocial. "</tb> desenvolver a atividade de <tb>".$minu_motivo."</tb>;";
*/
//$minu_motivo

$paragrafo_9  = "   1.3 O  <tb>".$emp_nomefantasia." </tb> ficar� no direito de monitorar em qualquer tempo a atividade licenciada, bem como requisitar documenta��es complementares, caso sejam necess�rias;";

//Constroi o texto da pagina 4 (Determina��es Espec�ficas)
//--------------------------------------------------------
$cont=1;

$paragrafo_10 = "Localiza��o GeoReferenciada";
 

$paragrafo_11 = "Determina��o Especifica";

$paragrafo_12 = "Recomenda��o Adicional";

$paragrafo_13 = "Enquadramento Legal";

$paragrafo_14 = "Condicionante";


$final_termo_1 = "              As determina��es n�o s�o excludentes podendo o <tb>" .$emp_razaosocial . " - " . $emp_nomefantasia . " </tb> a qualquer momento, com base nas respostas ambientais frente �s interven��es objeto do presente licenciamento ambiental, propor novas determina��es, tudo em conson�ncia com harmonia do meio ambiente.";

$final_termo_2 = "              A falta do cumprimento de quaisquer determina��es, implicar� na suspens�o imediata da <tb> ".$pag1_tit1." </tb>, conforme o <tb>art. 106</tb> da <tb> Lei Estadual n� 1.117 </tb> de 26 de janeiro de 1.994, ficando sujeito as penalidades previstas em Lei. ";


$testemunha1 = "Testemunhas:\n\nNome: __________________________________________\nCPF:";
$testemunha2 = "Nome: __________________________________________\nCPF:";



// Preparacao para impressao
//Create a new PDF file
//----------------------
$pdf = new MeuPDF('P'); // relat�rio em orienta��o "paisagem L" ou "retrato P"
$pdf->AddFont('Tahoma','','tahoma.php');
$pdf->AddFont('Tahomabd','','tahomabd.php');
$pdf->SetMargins(12,5,12);
$pdf->SetAutoPageBreak(true, 35);
$pdf->SetName($emp_razaosocial . " - " . $emp_nomefantasia);
$pdf->Open();
$pdf->AddPage();
$pdf->SetMsg($tipo);




//SETA OS STILOS PARA COLOCAR ALGUMAS PALAVRAS EM NEGRITO OU ITALICO OU DE COR DIFERENTE ETC
//--------------
$pdf->SetStyle("tb","tahomabd","",12,"0,0,0");


//Set font and colors
//----------------------
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(.3);

//Restore font and colors
//----------------------
$pdf->SetFillColor(224,235,255);
$pdf->SetTextColor(0);


//Pagina 1
//---------------------------------------

$pdf->SetFont('Tahomabd','',14);
$pdf->SetMsg($tipo);
$pdf->SetY(29,9);
$pdf->SetFillColor(123,215,105);
$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
$pdf->SetFont('Tahoma','',8);
$pdf->Line(12,29,198,29,3);
$pdf->Line(12,35,198,35,3);

$pdf->SetFillColor(232,232,232);

$pdf->Ln();$pdf->Ln();
$pdf->SetFont('Tahoma','',10);
$pdf->MultiCellTag(0, 5, $paragrafo_1, 0, "J", 0);
$pdf->Ln();$pdf->Ln();
$pdf->MultiCellTag(0, 5, $paragrafo_2, 0, "J", 0);


$pdf->Ln();
$pdf->MultiCell(0,10,$local_data,0,'C');
$pdf->Ln();
$pdf->SetFont('Tahomabd','',12);
$pdf->MultiCell(0,5,$presidente,0,'C');
$pdf->MultiCell(0,5,$presidente1,0,'C');


//Pagina 2
//---------------------------------------
$pdf->AddPage('P');
$pdf->SetFont('Tahomabd','',14);
$pdf->SetMsg($tipo);
$pdf->SetY(29.5);
$pdf->SetFillColor(123,215,105);
$pdf->MultiCell(0,6,$pag2_tit1,0,'C',1);

$pdf->SetFont('Tahoma','',8);
$pdf->Line(12,29,198,29,3);
$pdf->Line(12,42,198,42,3);

$pdf->Ln();$pdf->Ln();$pdf->Ln();

$pdf->SetFont('Tahomabd','',10);
$pdf->MultiCellTag(0, 5, $clb_razaosocial, 1, "C", 0);
$pdf->SetFont('Tahoma','',10);
$pdf->MultiCellTag(0, 5, $paragrafo_3, 1, "J", 0);


//Pagina 3
//---------------------------------------
$pdf->AddPage('P');
$pdf->SetFont('Tahomabd','',14);
$pdf->SetMsg($tipo);
$pdf->SetY(29.5);
$pdf->SetFillColor(123,215,105);
$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);

$pdf->SetFont('Tahoma','',8);
$pdf->Line(12,29,198,29,3);
$pdf->Line(12,35,198,35,3);
$pdf->Ln();$pdf->Ln();

$pdf->SetFont('Tahomabd','',15);
$pdf->MultiCellTag(0, 5, $pag3_tit1, 0, "C", 0);
$pdf->Ln();$pdf->Ln();



$pdf->SetFont('Tahoma','',10);
$pdf->MultiCellTag(0, 5, $paragrafo_4, 0, "J", 0);
$pdf->Ln();

$pdf->SetFont('Tahomabd','',12);
$pdf->MultiCellTag(0, 5, $paragrafo_5, 0, "J", 0);
$pdf->Ln();
$pdf->SetFont('Tahoma','',10);
$pdf->MultiCellTag(0, 5, $paragrafo_6, 0, "J", 0);
$pdf->Ln();
$pdf->MultiCellTag(0, 5, $paragrafo_7, 0, "J", 0);
$pdf->Ln();
//$pdf->MultiCellTag(0, 5, $paragrafo_8, 0, "J", 0);
//$pdf->Ln();
$pdf->MultiCellTag(0, 5, $paragrafo_9, 0, "J", 0);
$pdf->Ln();

//Pagina 4
//---------------------------------------
$pdf->AddPage('P');
$pdf->SetFont('Tahomabd','',14);
$pdf->SetMsg($tipo);
$pdf->SetY(29.5);
$pdf->SetFillColor(123,215,105);
$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);

$pdf->SetFont('Tahoma','',8);
$pdf->Line(12,29,198,29,3);
$pdf->Line(12,35,198,35,3);
$pdf->Ln();$pdf->Ln();

$pdf->SetFont('Tahoma','',10);
$Y_Table_Position = ($pdf->GetY());




$cont=1;
$pdf->SetFont('Tahoma','',10);
$pdf->Ln();

if($procloc==1)
{
	if($wctr==0)
	{
		$pdf->SetY($pdf->GetY()+1);
		$Y_Fields_Name_position = ($pdf->GetY()+1);
		$cont=$cont+1;
		$pdf->SetY($Y_Fields_Name_position);
		$pdf->SetFont('Tahomabd','',12);
		$pdf->MultiCellTag(0, 5,$cont.". ".$paragrafo_10, 0, "J", 0);
		$pdf->Ln();
	
		$pdf->SetFillColor(232,232,232);
	
		$pdf->SetFont('Tahomabd','',7);
		$pdf->SetX(10);
		$pdf->Cell(70,5,'Tipo',1,0,'C',1);
		$pdf->SetX(80);
		$pdf->Cell(70,5,'Proje��o',1,0,'C',1);
		$pdf->SetX(150);
		$pdf->Cell(50,5,'Dados Espaciais',1,0,'C',1);
		$pdf->Ln();
		$Y_Table_Position = ($pdf->GetY());
		
		$pdf->SetFont('Tahoma','',7);
		
		$i = 0;
		$auxY = $Y_Table_Position;
	
		while ($rowProcloc =& $res_procloc->fetchRow()) {
				
				$tpptgeo_descricao=trim($rowProcloc['tpptgeo_descricao']);
				$procloc_descricao=trim($rowProcloc['procloc_descricao']);
				$tpprj_descricao=trim($rowProcloc['tpprj_descricao']);
					
				if(!empty($rowProcloc['procloc_lat_graus']))
				{
					$dados_x=$rowProcloc['procloc_lat_graus']." ".$rowProcloc['procloc_lat_minuto']." ".$rowProcloc['procloc_lat_segundo'];
					$dados_y=$rowProcloc['procloc_long_graus']." ".$rowProcloc['procloc_long_minuto']." ".$rowProcloc['procloc_long_segundo'];
				}
				elseif(!empty($rowProcloc['procloc_utm_x_a']))
				{
					$dados_x=$rowProcloc['procloc_utm_x_a'];
					$dados_y=$rowProcloc['procloc_utm_y_a'];
				}
				elseif(!empty($rowProcloc['procloc_dec_x']))
				{
					$dados_x=$rowProcloc['procloc_dec_x'];
					$dados_y=$rowProcloc['procloc_dec_y'];
				}
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(10);
				
				$pdf->MultiCellTag(70, 5, substr($procloc_descricao,0,55), 1, "J", 0);
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(80);
				$pdf->MultiCell(70,5,$tpprj_descricao,1,"J",2,1);
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(150);
				$pdf->MultiCell(50,5,$dados_x." || ".$dados_y,1,"C",0);
				$pdf->SetY($Y_Table_Position);
				
				if($Y_Table_Position>=250)
				{
					$pdf->AddPage('P');
					$pdf->SetFont('Tahomabd','',14);
					$pdf->SetMsg($tipo);
					$pdf->SetY(29.5);
					$pdf->SetFillColor(123,215,105);
					$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
					
					$pdf->SetFont('Tahoma','',8);
					$pdf->Line(12,29,198,29,3);
					$pdf->Line(12,35,198,35,3);
					$pdf->Ln();$pdf->Ln();
				
					$Y_Table_Position = 50;
					$pdf->SetFont('Tahoma','',7);
				}
				else
				{
					$Y_Table_Position = $Y_Table_Position + 5;
				}
				
		
				$i++;
		}
	}
	elseif($wctr==1)
	{
		$pdf->SetY($pdf->GetY()+1);
		$Y_Fields_Name_position = ($pdf->GetY()+1);
		$cont=$cont+1;
		$pdf->SetY($Y_Fields_Name_position);
		$pdf->SetFont('Tahomabd','',12);
		$pdf->MultiCellTag(0, 5,$cont.". ".$paragrafo_10, 0, "J", 0);
		$pdf->Ln();



		$pdf->SetFillColor(232,232,232);
	
		$pdf->SetFont('Tahomabd','',7);
		$pdf->SetX(10);
		$pdf->Cell(50,5,'Descri��o do Ponto',1,0,'C',1);
		$pdf->SetX(60);
		$pdf->Cell(70,5,'Proje��o',1,0,'C',1);
		$pdf->SetX(130);
		$pdf->Cell(60,5,'Dados Espaciais (x,y)',1,0,'C',1);
		$pdf->Ln();
		$Y_Table_Position = ($pdf->GetY());
		
		$pdf->SetFont('Tahoma','',7);
		
		$i = 0;
		$auxY = $Y_Table_Position;
	
		while ($rowProcloc =& $res_procloc->fetchRow()) 
		{
			$desc_ponto=substr(trim($rowProcloc['desc_ponto']),0,50);
			if($i==0)
			{
				$pdf->SetY($Y_Fields_Name_position + 5);

				$pdf->SetFont('Tahoma','',10);
				$pdf->SetX(40);
				$pdf->MultiCell(110,5,$pate_descricaopontogeo,0,"J",0,0);
				$pdf->Ln();
				$i=1;
			}
			$pdf->SetFont('Tahoma','',7);
					
			$tpptgeo_descricao=trim($rowProcloc['tpptgeo_descricao']);
			$tpprj_descricao=trim($rowProcloc['tpprj_descricao']);
					
			$dados_x=$rowProcloc['x'];
			$dados_y=$rowProcloc['y'];
				
			$pdf->SetY($Y_Table_Position);
			$pdf->SetX(10);
			$pdf->MultiCell(50,5,$desc_ponto,1,"J",2,1);
			$pdf->SetY($Y_Table_Position);
			$pdf->SetX(60);
			$pdf->MultiCell(70,5,$tpprj_descricao,1,"J",2,1);
			$pdf->SetY($Y_Table_Position);
			$pdf->SetX(130);
			$pdf->MultiCell(60,5,$dados_x." || ".$dados_y,1,"C",0);
			$pdf->SetY($Y_Table_Position);
			
			if($Y_Table_Position>=250)
			{
				$pdf->AddPage('P');
				$pdf->SetFont('Tahomabd','',14);
				$pdf->SetMsg($tipo);
				$pdf->SetY(29.5);
				$pdf->SetFillColor(123,215,105);
				$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
				
				$pdf->SetFont('Tahoma','',8);
				$pdf->Line(12,29,198,29,3);
				$pdf->Line(12,35,198,35,3);
				$pdf->Ln();$pdf->Ln();
			
				$Y_Table_Position = 50;
				$pdf->SetFont('Tahoma','',7);
			}
			else
			{
			$Y_Table_Position = $Y_Table_Position + 5;
			}
			$i++;
		}
	}
	$pdf->Ln();$pdf->Ln();
	$auxYFinal = $Y_Table_Position+$i;
}

if($Y_Table_Position>=200)
{
	$pdf->AddPage('P');
	$ipag++;
	$pdf->SetFont('Tahomabd','',14);
	$pdf->SetMsg($tipo);
	$pdf->SetY(29.5);
	$pdf->SetFillColor(123,215,105);
	$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
	
	$pdf->SetFont('Tahoma','',8);
	$pdf->Line(12,29,198,29,3);
	$pdf->Line(12,35,198,35,3);
	$pdf->Ln();$pdf->Ln();
    $Y_Table_Position = 4;
}
else
{
   $Y_Table_Position = ($pdf->GetY());
}

//Determinacoes
if($dete==1)
{
	$pdf->SetY($pdf->GetY()+1);
	$Y_Fields_Name_position = ($pdf->GetY()+1);
	$cont=$cont+1;
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetFont('Tahomabd','',12);
	$pdf->MultiCellTag(0, 5,$cont.". ".$paragrafo_11, 0, "J", 0);
	
	$pdf->SetFont('Tahoma','',10);
	$pdf->Ln();
	while ($rowDeterminacao =& $res_determinacao->fetchRow()) {
		  $contador = $contador + 1;
		  $pArabigo = $contador;
		  $determinacao = trim($rowDeterminacao['dete_texto']);
		  $pdf->SetFont('Tahoma','',10);
		  $pdf->MultiCellTag(0, 5, $cont.".".$pArabigo .". - ".$determinacao, 0, "J", 0);
		  $Y_Table_Position = $Y_Table_Position + 5;
	}
	
	$pdf->Ln();$pdf->Ln();
	//$auxYFinal = $Y_Table_Position+$i;
}

if($Y_Table_Position>=200)
{
	$pdf->AddPage('P');
	$ipag++;
	$pdf->SetFont('Tahomabd','',14);
	$pdf->SetMsg($tipo);
	$pdf->SetY(29.5);
	$pdf->SetFillColor(123,215,105);
	$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
	
	$pdf->SetFont('Tahoma','',8);
	$pdf->Line(12,29,198,29,3);
	$pdf->Line(12,35,198,35,3);
	$pdf->Ln();$pdf->Ln();
    $Y_Table_Position = 4;
}
else
{
   $Y_Table_Position = ($pdf->GetY());
}

//Recomendacoes adcionais
if($mire==1)
{
	$pdf->SetY($pdf->GetY()+1);
	$Y_Fields_Name_position = ($pdf->GetY()+1);
	$cont=$cont+1;
	$pdf->SetY($Y_Fields_Name_position);
	
	$pdf->SetFont('Tahomabd','',12);
	$pdf->MultiCellTag(0, 5,$cont.". ".$paragrafo_12, 0, "J", 0);
	$pdf->SetFont('Tahoma','',10);
	//$pdf->Ln();
	$contador=0;
	while ($rowMirecad =& $res_mirecad->fetchRow()) {
		$contador = $contador + 1;
		$pArabigo = $contador;
		$mirecad = trim($rowMirecad['mirecad_texto']);
        $pdf->SetFillColor( 232,232,232) ;
    	
		$pdf->SetY($pdf->GetY()+1);
		$Y_Table_Position = ($pdf->GetY()+2);
		
		if($Y_Table_Position>=230)
		{
			$pdf->AddPage('P');
			$ipag++;
			$pdf->SetFont('Tahomabd','',14);
			$pdf->SetMsg($tipo);
			$pdf->SetY(29.5);
			$pdf->SetFillColor(123,215,105);
			$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
			
			$pdf->SetFont('Tahoma','',8);
			$pdf->Line(12,29,198,29,3);
			$pdf->Line(12,35,198,35,3);
			$pdf->Ln();$pdf->Ln();
			
	          $Y_Table_Position = 30;
		}
        else
		{
    	    $Y_Table_Position = ($pdf->GetY()+2);
	    }
		$pdf->Ln();
		$pdf->SetFont('Tahoma','',10);
		$pdf->SetX(10);
		$pdf->MultiCellTag(190, 5, $cont.".".$pArabigo .". - ".$mirecad, 0, "J", 0);
		$pdf->SetFont('Tahomabd','',8);
	}
	$pdf->Ln();

}


if($Y_Table_Position>=200)
{
	$pdf->AddPage('P');
	$ipag++;
	$pdf->SetFont('Tahomabd','',14);
	$pdf->SetMsg($tipo);
	$pdf->SetY(29.5);
	$pdf->SetFillColor(123,215,105);
	$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
	
	$pdf->SetFont('Tahoma','',8);
	$pdf->Line(12,29,198,29,3);
	$pdf->Line(12,35,198,35,3);
	$pdf->Ln();$pdf->Ln();
	
    $Y_Table_Position = 4;
}
else
{
   $Y_Table_Position = ($pdf->GetY());
}

//enquadramento juridico
if($enqu==1)
{
	$pdf->SetY($pdf->GetY()+1);
	$Y_Fields_Name_position = ($pdf->GetY()+1);
	$cont=$cont+1;
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->SetFont('Tahomabd','',12);
	$pdf->MultiCellTag(0, 5,$cont.". ".$paragrafo_13, 0, "J", 0);
	$pdf->Ln();
    $wctrlin++;
	$pdf->SetFont('Tahoma','',10);
	$contador=0;
	
	$pdf->SetFillColor( 232,232,232) ;
	
	$pdf->SetFont('Tahomabd','',10);
	$pdf->SetX(28);
	$pdf->Cell(135,5,"Lei / Resolu��o",0,0,'L',1) ;
	$pdf->SetFont('Tahomabd','',8);
	$pdf->SetX(165);
	$pdf->Cell(30,5,'Classifica��o',0,0,'C',1);
		
	$pdf->Ln();
	$Y_Table_Position = ($pdf->GetY());
		
	$pdf->SetFont('Tahoma','',8);
		
	$i = 1;
	$Y_Fields_Name_position = ($pdf->GetY());
	$Y_Table_Position = ($pdf->GetY());
		
	while ($rowEnquadramento =& $res_enquadramento->fetchRow()) {
				
				$enquadramento = trim($rowEnquadramento['fule_descricao']);
				$classificacao=trim($rowEnquadramento['fule_classificacao_f_e_m']);

				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(28);
				$pdf->MultiCell(135,5,$cont.".".$i.". - ".$enquadramento,0,"J",0) ;
				$auxY = $pdf->GetY();
				$pdf->SetY($Y_Table_Position);
				$pdf->SetX(165);
				$pdf->MultiCell(30,5,$classificacao,0,"C",0);
				
				$Y_Table_Position=($auxY+1);
				$pdf->Ln();$pdf->Ln();
				
				$i++;
		}
}


if($Y_Table_Position>=200)
{
	$pdf->AddPage('P');
	$ipag++;
	$pdf->SetFont('Tahomabd','',14);
	$pdf->SetMsg($tipo);
	$pdf->SetY(29.5);
	$pdf->SetFillColor(123,215,105);
	$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
	
	$pdf->SetFont('Tahoma','',8);
	$pdf->Line(12,29,198,29,3);
	$pdf->Line(12,35,198,35,3);
	$pdf->Ln();$pdf->Ln();
    $Y_Table_Position = 4;
}
else
{
   $Y_Table_Position = ($pdf->GetY());
}


///condicionantes
if($cond==1)
{
	$pdf->SetY($pdf->GetY()+1);
	$Y_Fields_Name_position = ($pdf->GetY()+1);
	$cont=$cont+1;
	$pdf->SetY($Y_Fields_Name_position);
	$pdf->Ln();
	$pdf->SetFont('Tahomabd','',12);
	$pdf->MultiCellTag(0, 5,$cont.". ".$paragrafo_14, 0, "J", 0);
	$pdf->SetFont('Tahomabd','',10);
	$pdf->Ln();
	$contador=0;
	while ($rowCondicionante =& $res_condicionantes->fetchRow()) {
		  $contador = $contador + 1;
		  $pArabigo = $contador;
		  $micoca_codigo= trim($rowCondicionante['micoca_codigo']);
		  $micoca_descricao = trim($rowCondicionante['micoca_descricao']);
		  $strResultado = "";
		  
		  $sql_cond = " SELECT "
					   . " licenciamento.minutacondicionante.micond_texto"
					   //. " licenciamento.minutacondicionante.micond_prazo_d_m_a, "
					   //. " licenciamento.minutacondicionante.micond_prazovalidade, "
					  // . " CASE "
					  // . " WHEN licenciamento.minutacondicionante.micond_periodicidade is not null THEN "
					  // . "      licenciamento.minutacondicionante.micond_periodicidade "
					   //. " ELSE "
					   //. "       1 "
					   //. " end AS micond_periodicidade "
					   . " FROM "
					   . " licenciamento.minutacondicionante "
					   . " INNER JOIN licenciamento.categoriaminutacondicionante ON "
					   . " (licenciamento.minutacondicionante.micoca_codigo = licenciamento.categoriaminutacondicionante.micoca_codigo) AND "
					   . " (licenciamento.categoriaminutacondicionante.micoca_codigo = ".$micoca_codigo.") "
					   . " INNER JOIN licenciamento.minuta ON "
					   . " (licenciamento.minutacondicionante.minu_codigo = licenciamento.minuta.minu_codigo) AND "
					   . " (licenciamento.minuta.minu_excluido_s_n = 'N')"
					   . " WHERE "
					   . " (licenciamento.minutacondicionante.proc_codigo = ".$proc_codigo.") AND "
					   . " (licenciamento.minutacondicionante.minu_codigo = ".$minu_codigo.") AND "
					   . " (licenciamento.minutacondicionante.micond_excluido_s_n = 'N')"
					   . " ORDER BY "
					   . " licenciamento.categoriaminutacondicionante.micoca_descricao, "
					   . " licenciamento.minutacondicionante.micond_dataexecucao";
		$res_cond =& $db->query($sql_cond);
		debugDB($res_cond);
		$novocontador=0;
                //echo $sql_cond;
		$pdf->SetFillColor( 232,232,232) ;
    	
		$pdf->SetY($pdf->GetY()+1);
		$Y_Table_Position = ($pdf->GetY()+2);
	
		
		if($Y_Table_Position>=230)
		{
			$pdf->AddPage('P');
			$ipag++;
			$pdf->SetFont('Tahomabd','',14);
			$pdf->SetMsg($tipo);
			$pdf->SetY(29.5);
			$pdf->SetFillColor(123,215,105);
			$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);
			
			$pdf->SetFont('Tahoma','',8);
			$pdf->Line(12,29,198,29,3);
			$pdf->Line(12,35,198,35,3);
			$pdf->Ln();$pdf->Ln();
			
	        $Y_Table_Position = 40;
			$pdf->Ln();
			$pdf->Ln();
		}
        else
		{
    	   $Y_Table_Position = ($pdf->GetY()+11);
	    }

		$pdf->SetFillColor( 232,232,232) ;
		$pdf->SetFont('Tahomabd','',10);
		$pdf->SetX(10);
		$pdf->Cell(190,5,$cont.".".$pArabigo .". ".$micoca_descricao,0,0,'L',1) ;
		$pdf->SetFont('Tahomabd','',8);
	//	$pdf->SetX(147);
	//	$pdf->Cell(30,5,'Prazo de Execu��o',0,0,'C',1);
	//	$pdf->SetX(180);
	//	$pdf->Cell(20,5,'Periodicidade',0,0,'C',1);
		
		$pdf->Ln();


		$pdf->SetFont('Tahoma','',10);
		
		$i = 1;
		$Y_Table_Position = ($pdf->GetY());
		
		while ($rowCond =& $res_cond->fetchRow()) {
				
				//if(!empty($rowCond['micond_prazovalidade']))
				//{
				//	$micond_texto=trim($rowCond['micond_texto']).", no prazo de ".trim($rowCond['micond_prazovalidade'])." ".trim($rowCond['micond_prazo_d_m_a']).", com ".trim($rowCond['micond_periodicidade'])." ocorr�ncia(s).";
				//}
				//else
				//{
					$micond_texto=trim($rowCond['micond_texto']);
				//}	
					
				//$micond_prazo_d_m_a=trim($rowCond['micond_prazo_d_m_a']);
				//$micond_prazovalidade=trim($rowCond['micond_prazovalidade']);
				//$micond_periodicidade=trim($rowCond['micond_periodicidade']);

				$pdf->SetY($Y_Table_Position);
				
				if($Y_Table_Position>=230)
				{
					$pdf->AddPage('P');
					$ipag++;
					$pdf->SetFont('Tahomabd','',14);
					$pdf->SetMsg($tipo);
					$pdf->SetY(29.5);
					$pdf->SetFillColor(123,215,105);
					$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);

					$pdf->SetFont('Tahoma','',8);
					$pdf->Line(12,29,198,29,3);
					$pdf->Line(12,35,198,35,3);
					$pdf->Ln();$pdf->Ln();
					$Y_Table_Position = 40;
				    $pdf->Ln();
				}
				else
				{
				   $Y_Table_Position = ($pdf->GetY()+2);
				}
				$pdf->SetFillColor( 232,232,232) ;

				$pdf->SetFont('Tahoma','',10);
				$pdf->SetY($Y_Table_Position);
				$pdf->Ln();
				$auxY = $pdf->GetY();
				
				$pdf->SetX(10);
				$pdf->MultiCell(190,5,$cont.".".$pArabigo.".".$i.". - ".trim($micond_texto),0,"J",0) ;
			//	if(!empty($micond_prazovalidade))
			//	{
			//	    $auxY2 = $pdf->GetY();
			//		$pdf->SetY($auxY);
			//		$pdf->SetX(147);
			//		$pdf->MultiCell(30,5,$micond_prazovalidade." ".$micond_prazo_d_m_a,0,"C",0);
			//		$pdf->SetY($auxY);
			//		$pdf->SetX(180);
			//		$pdf->MultiCell(15,5,$micond_periodicidade,0,"C",0);
			//		
			//		$Y_Table_Position=($auxY2);
			//	}
			//	else
			//	{
					$Y_Table_Position = ($pdf->GetY());
			//	}
				$pdf->Ln();
				
				$i++;
		}
	
		$pdf->Ln();
		$pdf->Ln();

	}
}


//Pagina 5
//---------------------------------------
$pdf->AddPage('P');
$pdf->SetFont('Tahomabd','',14);
$pdf->SetMsg($tipo);
$pdf->SetY(29.5);
$pdf->SetFillColor(123,215,105);
$pdf->MultiCell(0,6,$pag1_tit1,0,'C',1);

$pdf->SetFont('Tahoma','',8);
$pdf->Line(12,29,198,29,3);
$pdf->Line(12,35,198,35,3);
$pdf->Ln();$pdf->Ln();

$pdf->MultiCellTag(0, 5, $final_termo_1, 0, "J", 0);
$pdf->Ln();

$pdf->MultiCellTag(0, 5, $final_termo_2, 0, "J", 0);
$pdf->Ln();$pdf->Ln();$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(0,10,$local_data,0,'C');
$pdf->Ln();
$pdf->SetY($pdf->GetY()+1);
$pdf->SetFont('Tahomabd','',10);
$pdf->SetX(20);
$pdf->MultiCell(80,5,$compromissario,0,'C');

$pdf->SetY($pdf->GetY()-10);
$pdf->SetX(110);
$pdf->MultiCell(0,5,$presidente,0,'C');

$pdf->Ln();$pdf->Ln();
$pdf->SetFont('Tahoma','',10);
$pdf->SetX(20);
$pdf->MultiCellTag(90, 5, $testemunha1, 0, "J", 0);
$pdf->SetY($pdf->GetY()-15);
$pdf->SetX(110);
$pdf->MultiCell(90,5,$testemunha2,0,'L');


$pdf->Output();


?>
