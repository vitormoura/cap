<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	die('Usuário não autenticado, por favor efetue seu login');


//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/options.php');

$lojas  = 'SELECT codLoja, fantasia FROM loja ORDER BY codloja ASC';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet</title>
<script language="javascript" type="text/javascript" src="Misc/DataCheck.js"></script>
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Resultados</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><a href="principal.php" title="Tela principal" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/homeIco.gif" width="22" height="20" border="0" align="absmiddle" class="icones"></a></td>
  </tr>
</table>
<form method="post" action="Relatorios/resultadosPlanos.php" target="resultPlanos" style="margin:0">
  <table width="764" border="0" cellspacing="0">
    <tr>
      <td width="160" height="148" class="colunaEsquerda">&nbsp;</td>
      <td colspan="2" class="containerTitulo"><p><span class="tituloGrande">Apura&ccedil;&atilde;o de Resultados </span></p>
          <p>Atrav&eacute;s do formul&aacute;rio abaixo voc&ecirc; poder&aacute; definir  a loja desejada, o per&iacute;odo de an&aacute;lise e por fim escrever um t&iacute;tulo para o seu relat&oacute;rio de resultados. </p></td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td width="409" class="colunaDireita">&nbsp;</td>
      <td width="189">&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">Loja :</td>
      <td class="colunaDireita">
	  <select name="LSloja" class="Listas" id="LSloja" onKeyPress="NextField( 'TXTdt1', event );">
	  <option value="0">Todas</option>
	  <?php
		//Escrendo os itens:
	  	options( $lojas );
      ?>
      </select></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">Refer&ecirc;ncia :</td>
      <td class="colunaDireita"><input name="TXTdt1" type="text" id="TXTdt1" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTdt2', event );"> 
        a 
        <input name="TXTdt2" type="text" id="TXTdt2" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTtitle', event );"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">T&iacute;tulo : </td>
      <td class="colunaDireita"><input name="TXTtitle" type="text" id="TXTtitle" size="47"></td>
      <td>&nbsp;</td>
    </tr>
	
    <tr>
      <td valign="top" class="colunaEsquerda">Filtro : </td>
      <td class="colunaDireita"><input name="RDcont" type="radio" value="all" checked>
        Todos os documentos<br> 
          <input name="RDcont" type="radio" value="true">
      Somente cont&aacute;beis<br> 
      <input name="RDcont" type="radio" value="false"> 
      Somente n&atilde;o Cont&aacute;beis </td>
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
&nbsp;      <input name="RSblank" type="reset" id="RSblank" value="Limpar"> </td>
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
