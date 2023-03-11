<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 2 )
	header('Location: Index.php');
	

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/options.php');
	
//Obtendo a lista de contas bancárias e formas de pagamento:
	$contas = dbQuery( 'SELECT conta.codConta, conta.cc, banco.nome  FROM conta, banco WHERE conta.codBanco = banco.codBanco ORDER BY conta.codConta ASC');
	$formas = 'SELECT codForma, formaPag FROM formapag ORDER BY codForma ASC';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet - Consultar Pagamentos</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
<script language="javascript" type="text/javascript" src="Misc/DataCheck.js"></script>
</head>

<body onLoad="return Status();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="158" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="377" align="center">&nbsp;</td>
    <td width="221" align="center">Consultar Pagamentos</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><a href="principal.php" title="Tela principal" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/homeIco.gif" width="22" height="20" border="0" align="absmiddle" class="icones"></a></td>
  </tr>
</table>
<form action="resultadosPag.php" method="get" onSubmit="" style="margin:0;">
  <table width="764" border="0" cellspacing="0">
    <tr>
      <td width="160" height="146" class="colunaEsquerda"><img src="Imagens/bigMov.gif" width="108" height="120" class="icones"></td>
      <td colspan="2" class="containerTitulo"><p><span class="tituloGrande">Consultar Pagamentos</span></p>
      <p class="result">Atrav&eacute;s do formul&aacute;rio abaixo voc&ecirc; poder&aacute; filtrar movimenta&ccedil;&otilde;es realizadas por suas  baixas de acordo com os seus crit&eacute;rios de pesquisa.</p></td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td width="409" class="colunaDireita">&nbsp;	  </td>
      <td width="189">&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="LSforn">
      <label for="LSconta">Conta  : </label>
      </label></td>
      <td class="colunaDireita">
	  <select name="LSconta" id="LSconta" onKeyPress="NextField('LSforma', event );">
        <option value="0">Todas</option>
        <?php
      		
	  $options = '';
			
	  while ( $linha = rsFetch( $contas ) )
	  {
		echo '<option value="'.$linha['codConta'].'">';
		echo $linha['nome'].' - '.$linha['cc']."</option>\n";
	  }
					
	  ?>
      </select></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="LSforma">Forma de Pagamento : </label></td>
      <td class="colunaDireita">
      <select name="LSforma" id="LSforma" onKeyPress="NextField('TXTdt1', event );">
      <option value="0">Todas</option>
      <?php
      //Escrevendo as formas de pagamento:
      	options( $formas );
      ?>
      </select>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTdt1">Data do Pagamento : </label></td>
      <td class="colunaDireita"><input name="TXTdt1" type="text" id="TXTdt1" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onKeyDown="NextField('TXTdt2', event );"> 
        a 
        <input name="TXTdt2" type="text" id="TXTdt2" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onKeyDown="NextField('TXTnum', event );"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTnum">N&uacute;mero : </label></td>
      <td class="colunaDireita"><input name="TXTnum" type="text" id="TXTnum" maxlength="8" onKeyPress="NextField('submit', event );"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="24" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita"><input type="submit" id="submit "name="submit" value="Pesquisar"> 
</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="111" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" class="rodape">&nbsp;</td>
    </tr>
  </table>
</form>
</body>
</html>
