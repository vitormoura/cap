<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	header('Location: Index.php');

//Importando bibliotecas necessárias:
require_once('Engine/driver.php');
require_once('Engine/sprintsql.php');
require_once('Engine/restaurar.php');

$notifs = dbQuery('SELECT * FROM lembrete ORDER BY dtLemb ASC');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet - Notifica&ccedil;&otilde;es</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>

<body onLoad="return Status();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Notifica&ccedil;&atilde;o</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><a href="principal.php" title="Tela principal" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/homeIco.gif" width="22" height="20" border="0" align="absmiddle" class="icones"></a><a href="agenda.php" title="Agenda" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/agendaIco.gif" width="22" height="19" border="0" align="absmiddle" class="icones"></a><a href="consultas.php" title="Consultas" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/pesquisa.gif" width="20" height="20" border="0" align="absmiddle" class="icones"></a>|<a href="javascript:;" title="Nova notificação" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/novoDoc.gif" width="20" height="21" border="0" align="absmiddle" class="icones" onClick="abrirJanela('notificar.php');"></a></td>
  </tr>
</table>
<table width="764" border="0" cellspacing="0">
  <tr>
    <td width="562" height="43" valign="middle" class="line"><span class="tituloGrande3">Notifica&ccedil;&otilde;es gravadas : </span></td>
    <td width="198" valign="middle" class="line">&nbsp;</td>
  </tr>
  <tr align="center">
    <td height="82" colspan="2" align="center" valign="top"><table width="757" border="0" cellspacing="0">
      <tr>
        <td width="18" class="line">&nbsp;</td>
        <td width="72" height="27" class="line"><span class="textoDestaque">Notificar</span></td>
        <td width="599" align="left" class="line"><span class="textoDestaque"> Mensagem</span></td>
        <td width="60" class="line"><span class="textoDestaque">Ciente ? </span></td>
      </tr>	  
      <?php
      
      while( $dados = rsFetch( $notifs ) )
      {
      		$ref   = (int)restaurar( $dados['ref'] );
      		$data  = restaurar( $dados['dtLemb'], 'data');
      		$msg   = restaurar( $dados['mensagem'] );
			$tipo  = restaurar( $dados['tipo'] );
			$cod   = $dados['codLemb'];
      		
      		//Definindo o grau do alerta...
      		if( $dados['tipo'] == 'Notificacao' ||  $dados['tipo'] == 'Importante' )
				$icone = 'Imagens/redLight.gif';
      		else if( $dados['tipo'] == 'Comum' )
      			$icone = 'Imagens/yellowLight.gif';
      		else 
      			$icone = 'Imagens/blueLight.gif';
      		
      echo <<<DADOS
      	<tr class="celula2">
      	<td height="21" valign="middle"><img src="$icone" width="16" height="16" class="icones" title="$tipo"></td>
        <td valign="middle" style="font-weight:bold;">$data</td>
        <td align="left">$msg</td>
        <td valign="middle" align="center"><a href="central.php?codigo=$cod&act=dnot" title="Ciente da mensagem" onClick="return isCiente();" onMouseOver="return Status();"  onMouseOut="return Status();"><img src="Imagens/miniOk.gif" width="16" height="16" border="0" class="icones"></a></td>
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
