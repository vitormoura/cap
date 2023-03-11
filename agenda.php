<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	header('Location: Index.php');

	
//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');
	require_once('Engine/restaurar.php');
	
	$ultAgend = sprintsql('SELECT tpdoc.tipo, colaborador.fantasia AS forn, loja.fantasia AS loja, documento.numero, documento.emissao, documento.valor, documento.gravacao,
						   documento.contabil, documento.codDoc, pagamento.codPag, pagamento.vencimento, pagamento.aPagar, pagamento.descontos,
						   pagamento.acrescimos, pagamento.abatimentos, pagamento.situacao, usuario.login
						   FROM documento, tpdoc, colaborador, pagamento, usuario, loja
						   WHERE tpdoc.codtp = documento.codtp AND documento.fornecedor = colaborador.codcolab AND documento.codDoc = pagamento.codDoc
						   AND documento.coduser = usuario.coduser AND documento.loja = loja.codLoja AND pagamento.situacao = "n" AND documento.coduser = %d ORDER BY documento.codDoc DESC LIMIT 6',
						   $_SESSION['usuario']
						 );
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet - Agenda</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>
<body onLoad="Status();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Agenda</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><a href="principal.php" title="Tela principal" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/homeIco.gif" width="22" height="20" border="0" align="absmiddle" class="icones"></a><a href="consultas.php" title="Consultas" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/pesquisa.gif" width="20" height="20" border="0" align="absmiddle" class="icones"></a><a href="notificacoes.php" title="Notificações" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/notifIco.gif" width="20" height="18" border="0" align="absmiddle" class="icones"></a><a href="pagamentos.php" title="Pagamentos" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/calcIco2.gif" width="19" height="21" border="0" align="absmiddle" class="icones"></a>|<a href="javascript:;" title="Novo Agendamento" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/novoDoc.gif" width="20" height="21" border="0" align="absmiddle" class="icones" onClick="abrirJanelaDeAgendamento('Agendamento.php');"></a><a href="javascript:;" onclick="abrirJanela('AgendamentoEmBloco.php');" title="Agendamento em bloco"><img src="imagens/novoBlock.gif" align="absmiddle" border="0"/></a></td>
  </tr>
</table>
<table width="764" border="0" cellspacing="0">
  <tr>
    <td width="562" height="43" valign="middle" class="line"><span class="tituloGrande3">&Uacute;ltimos Agendamentos : </span></td>
    <td width="198" align="right" valign="middle" class="line">Usu&aacute;rio : <span style="font-weight:normal; padding:20px;"><?php echo @$_SESSION['login']; ?></span></td>
  </tr>
  <tr align="center">
    <td height="82" colspan="2" align="center" valign="top"><table width="761" border="0" cellspacing="0">
        <tr class="cabecalho">
          <td width="66"> Loja </td>
          <td width="99">Fornecedor</td>
          <td width="77"> N&uacute;mero </td>
          <td width="74">Emiss&atilde;o</td>
          <td width="79">Vencimento</td>
          <td width="61">Vl. Bruto </td>
          <td width="50">Desc.</td>
          <td width="49">Acresc.</td>
          <td width="49">Dedu&ccedil;.</td>
          <td width="65"> a Pagar </td>
          <td width="70">&nbsp;</td>
        </tr>
        <?php
		$rs    = dbQuery( $ultAgend );
			
        
		while( $dados = rsFetch( $rs ) )
		{
			$cod     = $dados['codDoc'];
			$forn    = &restaurar( $dados['forn']);
			$loja    = &restaurar( $dados['loja']);
			$num     = &restaurar( $dados['numero']);
			$emissao = &restaurar( $dados['emissao'], 'data' );
			$venc    = &restaurar( $dados['vencimento'], 'data');
			$valorB  = &restaurar( $dados['valor'], 'valor');
			$desc    = &restaurar( $dados['descontos'], 'valor' );;
			$acres   = &restaurar( $dados['acrescimos'], 'valor');
			$abat    = &restaurar( $dados['abatimentos'], 'valor' );
			$valorL  = &restaurar((( $dados['valor'] + $dados['acrescimos'] ) - ( $dados['descontos'] + $dados['abatimentos'] )), 'valor');
			$msg     = $dados['tipo'].' agendado(a) por '.ucfirst( $dados['login']).' em '.restaurar( $dados['gravacao'], 'data' );
			
echo <<<DADOS
		
        <tr class="celula">
          <td align="left" class="celula2" style="padding-left:10px">$loja</td>
          <td align="left" class="celula2" style="padding-left:10px">$forn</td>
          <td align="left" class="celula2" style="padding-left:10px">$num</td>
          <td align="center" class="celula2">$emissao</td>
          <td align="center" class="celula2">$venc</td>
          <td align="right" class="celula2">$valorB</td>
          <td align="right" class="celula2">$desc</td>
          <td align="right" class="celula2">$acres</td>
          <td align="right" class="celula2">$abat</td>
          <td align="right" class="celula2">$valorL</td>
          <td class="celula2">
             <a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" title="Editar Informações" onClick="abrirJanela('Editar.php?codigo=$cod');"><img src="Imagens/editIco.gif" width="15" height="14" align="absmiddle" border="0"></a>
             <a href="central.php?codigo=$cod&act=exc" onMouseOver="return Status()" onMouseOut="return Status()" title="Excluir documento" onClick="return Confirm('documento');"><img src="Imagens/excluir.gif" width="14" height="16" align="absmiddle" border="0"></a>
             <a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" title="Notificar falta de boleto..." onClick="abrirJanelaModal('notificado.php?codigo=$cod');"><img src="Imagens/notifIco.gif" width="15" height="14" align="absmiddle" border="0"></a>
			 <a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" title="$msg" onClick="abrirJanelaComum('documento.php?codigo=$cod');"><img src="Imagens/infoIco.gif" width="14" height="14" align="absmiddle" border="0"></a>
          </td>
        </tr>
DADOS;

		}
		?>
    </table></td>
  </tr>
  <tr align="center">
    <td height="27" colspan="2" valign="top" class="line">&nbsp;</td>
  </tr>
  <tr align="left" valign="middle">
    <td height="95" colspan="2" class="result">&nbsp;</td>
  </tr>
  <tr align="center">
    <td height="24" colspan="2" valign="top" class="rodape">&nbsp;</td>
  </tr>
</table>
</body>
</html>
