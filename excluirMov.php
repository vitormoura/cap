<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 2 )
	die('Usuário não autenticado, por favor efetue seu login');

if( !empty( $_POST['TXTpsw'] ) && !empty( $_POST['HDmov']))
{
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');
	
	$sucess = false;
	
	//Testando se a senha é válida:
	$teste = dbQuery(sprintsql("SELECT codUser FROM usuario WHERE codUser = 1 AND senha = %s", md5( $_POST['TXTpsw'] )));

	//Se a senha digitada realmente casar com esta: ( revisar este procedimento )
	if( $dados = rsFetch( $teste ) )
	{
		dbQuery( sprintsql( 'UPDATE pagamento SET situacao = "n", codMov = 0 WHERE codMov = %d', $_POST['HDmov'] ));
		dbQuery( sprintsql( 'DELETE FROM movimentacao WHERE codMov = %d', $_POST['HDmov'] ));
		$sucess = true;
		
	}
			
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Excluir Movimenta&ccedil;&atilde;o</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>
<body onLoad="Init();">
<table width="485" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="158" height="42" align="center"><img src="Imagens/IntranetLogo.gif" width="141" height="13"></td>
    <td width="145" align="center">&nbsp;</td>
    <td width="174" align="center">&nbsp;</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><span class="titulo">Excluir Movimenta&ccedil;&atilde;o </span></td>
  </tr>
</table>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin:0;" onSubmit="return alerta();">
  <table width="485" height="361" border="0" cellspacing="0">
    <tr>
      <td align="right" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td width="159" height="49" align="right" class="colunaEsquerda"><label for="LSloja"></label></td>
      <td width="322" class="tituloGrande3">Alerta importante</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSforn"></label></td>
      <td class="colunaDireita">Para excluir uma movimenta&ccedil;&atilde;o, &eacute; necess&aacute;rio fornecer uma senha administrativa com poderes especiais para concluir a opera&ccedil;&atilde;o. Os pagamentos que comp&otilde;em a movimenta&ccedil;&atilde;o voltar&atilde;o a condi&ccedil;&atilde;o inicial 'a pagar' e voc&ecirc; poder&aacute; ent&atilde;o refazer o processo.</td>
    </tr>
    <tr>
      <td height="36" align="right" class="colunaEsquerda"><label for="TXTnum"></label></td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td height="26" align="right" class="colunaEsquerda"><label for="TXTemis">Senha administrativa :</label></td>
      <td class="colunaDireita"><input name="TXTpsw" type="password" id="TXTpsw">
        <input type="submit" name="Submit" value="Excluir">
      <input name="HDmov" type="hidden" id="HDmov" value="<?php echo $_GET['codigo']; ?>"></td>
    </tr>
    <tr>
      <td height="24" align="right" valign="top" class="colunaEsquerda"><label for="TXTdesc"></label>        <label for="LSdoc"></label>        <label for="RDfiscal"></label></td><td valign="top" class="colunaDireita">                          <label></label>        <label>
      </label></td>
    </tr>
    <tr>
      <td height="24" align="right" class="colunaEsquerda"><label for="TXTcep"></label></td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" class="rodape">&nbsp;</td>
    </tr>
  </table>
</form>
</body>
</html>
<?php
	
	//Alerta a tentativa do usuário:
	if( isset( $sucess ) && $sucess == true )
		exit('<script language="javascript"> window.alert("Movimentação excluída"); opener.document.location.reload(); self.close(); </script>');
	else if( isset( $sucess ) && $sucess == false )
		exit('<script language="javascript">window.alert("Senha administrativa inválida"); self.close();</script>');
?>