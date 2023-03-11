<?php
session_start();

//Matando qualquer sessão já ativa:
if( isset( $_SESSION['usuario'] ) || isset( $_SESSION['nivel'] ) )
{
	unset( $_SESSION['usuario']);
	unset( $_SESSION['nivel']);
}

//Testando se o usuario esta tentando efetuar o login:
if( !empty( $_POST['TXTuser'] ) && !empty( $_POST['TXTpsw'] ) )
{
	//Importando Bibliotecas:
	require_once('Engine/Driver.php');
	
	//Preparando as informações:
	$user  = trim( addslashes( $_POST['TXTuser'] ));
	$senha = md5( $_POST['TXTpsw'] );
	
	//Pesquisando na base de dados:
	$rs    = dbQuery("SELECT codUser, login, nivel FROM usuario WHERE login = '$user' AND senha = '$senha'");
	
	//Testa se tudo deu certo:
	if( $dados = rsFetch( $rs ) )
	{
		//Cria variáveis de sessão:
		$_SESSION['usuario'] = $dados['codUser'];
		$_SESSION['login'] = $dados['login'];
		$_SESSION['nivel']   = $dados['nivel'];
	
		//Redirecionando usuario:
		header('Location: Principal.php');
		exit;
	}
	else 
		//Mensagem de erro, caso os dados sejam inválidos:
		$erro = 'Usuário ou senha inválidos, tente novamente.';
}
?>	
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet</title>
<script language="javascript" type="text/javascript" src="Misc/scripts.js"></script>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="return Init();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Login</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3">&nbsp;</td>
  </tr>
</table>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="margin:0;" onSubmit="return noSubmition( this );">
  <table width="764" border="0" cellspacing="0">
    <tr>
      <td width="160" rowspan="2" align="left" class="colunaEsquerda"><p><img src="Imagens/loginImage.gif" width="119" height="122" class="icones"> </p></td>
      <td height="42" align="left" class="containerTitulo"><span class="tituloGrande">Contas a pagar 2.1</span></td>
    </tr>
    <tr>
      <td width="606" height="124" align="left" class="containerTitulo"><ul class="result">
          <li>Preencha os dados de acordo com as informa&ccedil;&otilde;es passadas por seu administrador.</li>
          <li>Tenha em mente que as opera&ccedil;&otilde;es dentro do sistema ser&atilde;o monitoradas, portanto n&atilde;o 'empreste' sua senha a terceiros. </li>
      </ul></td>
    </tr>
    <tr>
      <td height="24" align="left" class="colunaEsquerda">Login : </td>
      <td width="606" align="left" class="colunaDireita"><input name="TXTuser" type="text" id="TXTuser" onFocus="noSubmition( this.form );" onKeyPress="noSubmition( this.form );"></td>
    </tr>
    <tr>
      <td height="24" align="left" class="colunaEsquerda">Senha : </td>
      <td width="606" align="left" class="colunaDireita"><input name="TXTpsw" type="password" id="TXTpsw" onFocus="noSubmition( this.form );" onKeyPress="noSubmition( this.form );"></td>
    </tr>
    <tr>
      <td height="24" align="left" class="colunaEsquerda">&nbsp;</td>
      <td align="left" class="colunaDireita"><?php echo @$erro; ?></td>
    </tr>
    <tr>
      <td height="24" align="left" class="colunaEsquerda">&nbsp;</td>
      <td align="left" class="colunaDireita"><input type="submit" name="Submit" value="Confirmar" onMouseOver="noSubmition( this.form );" onKeyPress="noSubmition( this.form );"></td>
    </tr>
    <tr>
      <td height="57" align="left" class="colunaEsquerda">&nbsp;</td>
      <td width="606" align="left">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" class="rodape">Bueno Design & Indrema Studios -  Vers&atilde;o 2.1</td>
    </tr>
  </table>
</form>
</body>
</html>
