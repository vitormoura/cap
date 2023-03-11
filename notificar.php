<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	die('Usuário não autenticado, por favor efetue seu login');

if( !empty( $_POST['TXTdata'] ) && !empty( $_POST['LStipo']) && !empty( $_POST['TEXTmsg'] ))
{
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');
	
	dbQuery( sprintsql( 'INSERT INTO lembrete ( tipo, ref, lida, dtLemb, mensagem ) VALUES ( %s, %s, %s, %t, %s )',
						$_POST['LStipo'], 'NULL', 'n', $_POST['TXTdata'], $_POST['TEXTmsg'] ));
						
	exit('<script language="javascript"> opener.document.location.reload(); self.close(); </script>');
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Nova Notifica&ccedil;&atilde;o</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/DataCheck.js"></script>
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
<script language="javascript" type="text/javascript" src="Misc/notificar.js"></script>
</head>
<body onLoad="Init();">
<table width="485" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="158" height="42" align="center"><img src="Imagens/IntranetLogo.gif" width="141" height="13"></td>
    <td width="145" align="center">&nbsp;</td>
    <td width="174" align="center">Notifica&ccedil;&atilde;o</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><span class="titulo">Nova Notifica&ccedil;&atilde;o </span></td>
  </tr>
</table>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin:0;" onSubmit="return noBlank( itens, msg );">
  <table width="485" height="360" border="0" cellspacing="0">
    <tr>
      <td align="right" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td width="159" align="right" class="colunaEsquerda"><label for="LSloja"></label></td>
      <td width="322" class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSforn"></label></td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTnum"></label></td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTemis">Data da Notifica&ccedil;&atilde;o :</label></td>
      <td class="colunaDireita"><input name="TXTdata" type="text" id="TXTdata" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('LStipo', event );"></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTvenc">Tipo : </label></td>
      <td class="colunaDireita"><select name="LStipo" id="LStipo" onKeyPress="NextField('TEXTmsg', event );">
        <option value="Importante">Lembrete Importante</option>
        <option value="Comum">Lembrete Comum</option>
        <option value="Trivial">Mensagem Trivial</option>
      </select></td>
    </tr>
    <tr>
      <td align="right" valign="top" class="colunaEsquerda"><label for="TXTval">Mensagem : </label>        <label for="TXTdesc"></label>        <label for="LSdoc"></label>        <label for="RDfiscal"></label></td>
      <td valign="top" class="colunaDireita">                          <label></label>        <label>
        <textarea name="TEXTmsg" cols="30" rows="5" id="TEXTmsg"></textarea>
      </label></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSestado"></label></td>
      <td class="colunaDireita"><input type="submit" name="Submit" value="Gravar notificação"></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTcep"></label></td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" class="rodape">&nbsp;</td>
    </tr>
  </table>
</form>
</body>
</html>
