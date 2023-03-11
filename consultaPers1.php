<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	header('Location: Index.php');

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/options.php');
	
//Obtendo a lista de fornecedores, lojas e documentos:	
	$forns = 'SELECT codColab, fantasia FROM colaborador ORDER BY fantasia ASC';
	$lojas = 'SELECT codLoja, fantasia FROM loja ORDER BY codloja ASC';
	$docs  = 'SELECT codTp, tipo FROM tpDoc ORDER BY codtp ASC';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet - Consulta</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
<script language="javascript" type="text/javascript" src="Misc/DataCheck.js"></script>
</head>

<body onLoad="return Status();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Consultas</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><a href="principal.php" title="Tela principal" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/homeIco.gif" width="22" height="20" border="0" align="absmiddle" class="icones"></a>| <span class="result"><a href="javascript:;">Pr&eacute;-Configura&ccedil;&atilde;o</a></span></td>
  </tr>
</table>
<form action="resultados.php" method="get" style="margin:0;">
  <table width="764" border="0" cellspacing="0">
    <tr>
      <td width="160" height="24" class="colunaEsquerda">&nbsp;</td>
      <td width="409">&nbsp;</td>
      <td width="189">&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="LSloja">Loja : </label></td>
      <td class="colunaDireita">
	  <select name="LSloja" id="LSloja" onKeyPress="NextField('LSforn', event );">
        <option value="0">Todas</option>
        <?php
        //Escrevendo os lojas:
        options( $lojas );
       ?>
      </select></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="LSforn">Fornecedor : </label></td>
      <td class="colunaDireita">
	  <select name="LSforn" id="LSforn" onKeyPress="NextField('TXTnum', event );">
        <option value="0">Todos</option>
        <?php
        //Escrevendo os fornecedores:
        options( $forns );
       ?>
      </select></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTnum">N&uacute;mero : </label></td>
      <td class="colunaDireita"><input name="TXTnum" type="text" id="TXTnum" maxlength="12" onKeyPress="NextField('TXTag1', event );"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTag1">Agendamento : </label></td>
      <td class="colunaDireita"><input name="TXTag1" type="text" id="TXTag1" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTag2', event );"> 
        a 
        <input name="TXTag2" type="text" id="TXTag2" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTem1', event );"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTem1">Emiss&atilde;o : </label></td>
      <td class="colunaDireita"><input name="TXTem1" type="text" id="TXTem1" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTem2', event );"> 
        a 
        <input name="TXTem2" type="text" id="TXTem2" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTve1', event );"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTve1">Vencimento : </label></td>
      <td class="colunaDireita"><input name="TXTve1" type="text" id="TXTve1" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTve2', event );"> 
        a 
        <input name="TXTve2" type="text" id="TXTve2" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('LSdoc', event );"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="LSdoc">Tp. Doc. : </label></td>
      <td class="colunaDireita">
	  <select name="LSdoc" id="LSdoc" onKeyPress="NextField('RDfiscal', event );">
	  <option value="0">Todos os tipos</option>
	  <?php
	  //Escrevendo os documentos:
	  options( $docs );
	  ?>
      </select></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="RDfiscal">Fiscal : </label></td>
      <td class="colunaDireita"><input name="RDfiscal" type="radio" value="s" onKeyPress="NextField('RDpago', event );">
        Sim 
        <input name="RDfiscal" type="radio" value="n" onKeyPress="NextField('RDpago', event );"> 
        N&atilde;o </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="RDpago">Pago : </label></td>
      <td class="colunaDireita"><input name="RDpago" type="radio" value="p" onKeyPress="NextField('LSordem[]', event );"> 
        Sim 
        <input name="RDpago" type="radio" value="n" onKeyPress="NextField('LSordem[]', event );"> 
        N&atilde;o </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign="top" class="colunaEsquerda"><label for="LSordem[]">Ordenar resultados por : </label></td>
      <td class="colunaDireita"><select name="LSordem[]" size="4" multiple id="LSordem" onKeyPress="NextField('Submit', event );">
        <option value="4">Loja</option>
        <option value="3">Fornecedor</option>
        <option value="2">Emissao</option>
        <option value="1">Vencimento</option>
      </select></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita"><input type="submit" name="Submit" value="Pesquisar"> 
        &nbsp; 
      <input type="reset" name="Reset" value="Limpar"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="colunaEsquerda">&nbsp;</td>
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
