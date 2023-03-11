<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	header('Location: Index.php');


//Importando bibliotecas necessárias:
require_once('Engine/driver.php');
require_once('Engine/sprintsql.php');
require_once('Engine/restaurar.php');

$hoje   = time();
$notifs = dbQuery("SELECT * FROM lembrete WHERE dtLemb <= $hoje ORDER BY dtLemb ASC");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>
<body onLoad="return Status()">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Principal</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><a href="agenda.php" title="Agenda" onMouseOver="return Status()" onMouseOut="return Status()"><img src="Imagens/agendaIco.gif" width="22" height="20" align="absmiddle" border="0" class="icones"></a><a href="consultas.php" title="Consultas" onMouseOver="return Status()" onMouseOut="return Status()"><img src="Imagens/pesquisa.gif" width="20" height="21" align="absmiddle" border="0" class="icones"></a><a href="notificacoes.php" title="Notificações" onMouseOver="return Status()" onMouseOut="return Status()"><img src="Imagens/notifIco.gif" width="20" height="18" border="0" align="absmiddle" class="icones"></a><a href="pagamentos.php" title="Pagamentos" onMouseOver="return Status()" onMouseOut="return Status()"><img src="Imagens/calcIco2.gif" width="19" height="21" border="0" align="absmiddle" class="icones"></a><a href="consultarPag.php" title="Consultar Pagamentos" onMouseOver="return Status()" onMouseOut="return Status()"><img src="Imagens/pagsIco.gif" width="21" height="22" border="0" align="absmiddle" class="icones"></a><a href="consultarPlanos.php" title="Apuração" onMouseOver="return Status()" onMouseOut="return Status()"><img src="Imagens/Grafico.gif" width="30" height="30" border="0" align="middle" class="icones"></a></td>
  </tr>
</table>
<table width="764" border="0" cellspacing="0">
  <tr>
    <td height="83" valign="top" class="containerTitulo"><p class="tituloGrande3">Contas a Pagar 2.1 - Marine</p>
      <p class="tituloGrande3">Bem vindo ao sistema de gerenciamento de contas a pagar da nossa intranet.</p>
      <ul class="result">
        <li> Talvez voc&ecirc; n&atilde;o saiba, mas j&aacute; fazem mais de dois anos que utilizamos esse sistema para gerenciar nossas contas a pagar. E para comemorar, avisamos que a vers&atilde;o 3.0 deste sistema j&aacute; esta em desenvolvimento, trazendo in&uacute;meros novos recursos que prometem agilizar o fluxo de trabalho dos usu&aacute;rios. Aguardem !</li>
        <li>Obrigado &agrave; todos os usu&aacute;rios do sistema que contribuiram para a sua consolida&ccedil;&atilde;o. ( &Iacute;ndrema - Adm. do Sistema ) <br>
        </li>
      </ul></td>
  </tr>
  <tr>
    <td height="38" valign="middle" class="line"><span class="tituloGrande3">Notifica&ccedil;&otilde;es de hoje : </span></td>
  </tr>
  <tr align="center">
    <td height="82" align="center" valign="top"><table width="757" border="0" cellspacing="0">
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
    <td height="27" valign="top" class="line">&nbsp;</td>
  </tr>
  <tr align="left" valign="middle">
    <td height="95" class="result"><p align="right"><img src="Imagens/innet.gif" width="169" height="56"><span class="tituloGrande3"> </span></p>
    </td>
  </tr>
  <tr align="center">
    <td height="24" valign="top" class="rodape">&nbsp;</td>
  </tr>
</table>
</body>
</html>
