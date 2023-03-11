<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 2 )
	header('Location: Index.php');
	

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/options.php');
	
//Obtendo a lista de fornecedores:
	$forns = 'SELECT codColab, fantasia FROM colaborador ORDER BY fantasia ASC';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet - Pagamentos</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
<script language="javascript" type="text/javascript" src="Misc/DataCheck.js"></script>
</head>
<body onLoad="return Status();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Pagamentos</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><a href="principal.php" title="Tela principal" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/homeIco.gif" width="22" height="20" border="0" align="absmiddle" class="icones"></a></td>
  </tr>
</table>
<form action="fatura.php" method="get" target="fatura" style="margin:0;" onSubmit="">
  <table width="764" border="0" cellspacing="0">
    <tr>
      <td width="160" height="148" class="colunaEsquerda"><img src="Imagens/bigCalc.gif" width="94" height="112" class="icones"></td>
      <td colspan="2" class="containerTitulo"><p><span class="tituloGrande">Pagamentos</span></p>
      <p>Atrav&eacute;s do formul&aacute;rio abaixo voc&ecirc; poder&aacute; filtrar os pagamentos para efetuar suas baixas. Selecione um fornecedor espec&iacute;fico e/ou per&iacute;odo para obter resultados mais concisos e f&aacute;ceis de trabalhar. </p></td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td width="409" class="colunaDireita">&nbsp;	  </td>
      <td width="189">&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="LSforn">Fornecedor : </label></td>
      <td class="colunaDireita">
	  <select name="LSforn" id="LSforn" onKeyPress="NextField('TXTve1', event );">
        <option value="0">Todos</option>
        <?php
        //Escrevendo os fornecedores:
        options( $forns );
       ?>
      </select></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTve1">Per&iacute;odo : </label></td>
      <td class="colunaDireita"><input name="TXTve1" type="text" id="TXTve1" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTve2', event );"> 
        a 
        <input name="TXTve2" type="text" id="TXTve2" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('CHKshow', event );"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita"><input name="CHKshow" type="checkbox" id="CHKshow" onKeyDown="NextField('Submit', event );" value="true" checked> 
        Mostrar somente documentos em aberto </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="24" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita"><input type="submit" name="Submit" value="Pesquisar"> 
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
