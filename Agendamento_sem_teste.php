<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	die('Usuário não autenticado, por favor efetue seu login');


//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/options.php');
		
//Obtendo a lista de fornecedores, lojas, documentos e planos:	
	$forns  = 'SELECT codColab, fantasia FROM colaborador ORDER BY fantasia ASC';
	$lojas  = 'SELECT codLoja, fantasia FROM loja ORDER BY codloja ASC';
	$docs   = 'SELECT codTp, tipo FROM tpDoc ORDER BY codTp ASC';
	$planos = 'SELECT codPlano, plano FROM PlanoDeContas ORDER BY codPlano ASC';

//O usuário esta tentando cadastrar um novo documento ?
if( !empty( $_POST['LSloja'])  && !empty( $_POST['LSforn']) && !empty( $_POST['TXTnum'])  && !empty( $_POST['TXTemis']) &&
	!empty( $_POST['TXTvenc']) && !empty( $_POST['TXTval']) && !empty( $_POST['LSdoc'])   && !empty( $_POST['RDfiscal']) && !empty($_POST['LSplano']))
{

	//Importando a biblioteca sprintsql, que formata as queries:
	@require_once('Engine/sprintsql.php');
	
	$codNote    = null;		//Testaremos as anotações em seguida, por hora ela permanecerá vazia
	$user       = $_SESSION['usuario'];		//Codigo do usuario
	$dtGravacao =  date("d-m-Y");			//time(); 	//Capturando a hora atual
	
	//Testamos se o documento possui comentários, se sim, inserimos na base de dados e capturamos o código:
	if( !empty( $_POST['TEXTobs'] ) )
	{
		$insertSql = sprintsql('INSERT INTO anotacao ( nota ) VALUES ( %s )', $_POST['TEXTobs'] );
		$codNote   = dbQuery( $insertSql );
	}	
				
	//Agora inserimos os dados do documento propriamente dito, tudo processado pelas funções de formatação do sprintsql:
	$insertSql  = sprintsql('INSERT INTO documento ( codTp, codNote, codUser, gravacao, loja, fornecedor, numero, emissao, vencimento, valor, contabil )
					  VALUES ( %d, %d, %d, %t, %d, %d, %s, %t, %t, %f, %s )',
					   
						$_POST['LSdoc'], $codNote, $user, $dtGravacao, $_POST['LSloja'], $_POST['LSforn'], $_POST['TXTnum'],
						$_POST['TXTemis'], $_POST['TXTvenc'], $_POST['TXTval'], $_POST['RDfiscal']);

	//Banco de dados executa...
	$cod        = dbQuery( $insertSql );
	
	//Por fim preenchemos a fatura com o restante dos dados:
	$insertSql  = sprintsql('INSERT INTO pagamento ( codDoc, codPlano, vencimento, aPagar, descontos, acrescimos, abatimentos, comentario, situacao )
							 VALUES ( %d, %d, %t, %f, %f, %f, %f, %s, "n" )',
						$cod, (int)$_POST['LSplano'], $_POST['TXTvenc'], $_POST['TXTval'], $_POST['TXTdesc'], $_POST['TXTacres'], $_POST['TXTded'],
						$_POST['TXTcom'] );
					
	//Banco de dados executa... e fim do processo.						
	dbQuery( $insertSql );
			
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Novo Agendamento</title>

<!-- Impotando a coleção de scripts e estilos css -->
	<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
	<script language="javascript" type="text/javascript" src="Misc/agendamento.js"></script>
	<script language="javascript" type="text/javascript" src="Misc/DataCheck.js"></script>
	<script language="javascript" type="text/javascript" src="Misc/mask.js"></script>
	<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>

<body onLoad="Init();">
<table width="485" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="158" height="42" align="center"><img src="Imagens/IntranetLogo.gif" width="141" height="13"></td>
    <td width="145" align="center">&nbsp;</td>
    <td width="174" align="center">Agenda</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><span class="titulo">Novo Agendamento</span></td>
  </tr>
</table>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin:0" onSubmit="return noBlank( itens, msg );">
  <table width="485" height="360" border="0" cellspacing="0">
    <tr>
      <td align="right" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td width="159" align="right" class="colunaEsquerda"><label for="LSloja">Loja : </label></td>
      <td width="322" class="colunaDireita">
	  <select name="LSloja" id="LSloja" onKeyPress="NextField( 'LSforn', event );">
    <?php
		//Escrendo os itens:
	  	options( $lojas, @$_POST['LSloja'] );
	?>
      </select>
	  </td>
    </tr>
	<div id="box" style="position:absolute; left:315px; top:237px; width:150px; height:125px; z-index:99;" onDblClick="fecharBox('num');"><label for="TXTacres">Acrescimos :</label>
  <input type="text" name="TXTacres" id="TXTacres" maxlenght="10" onKeyPress="return(currencyFormat(this,'.',',', event, 'TXTded' ));">
  <br> 
  <label for="TXTded">Outras Dedu&ccedil;&otilde;es :</label>
  <input type="text" name="TXTded" id="TXTded" maxlenght="10" onKeyPress="return(currencyFormat(this,'.',',', event, 'TXTcom' ));">
  <br>
  <label for="TXTcom">Coment&aacute;rios :</label>
  <input type="text" name="TXTcom" id="TXTcom" maxlenght="50">
</div>
<div id="boxObs" style="position:absolute; left:314px; top:237px; width:150px; height:105px; z-index:100" onDblClick="fecharBox('obs');"><label for="TEXTobs">Observa&ccedil;&otilde;es :</label>
<textarea name="TEXTobs" id="TEXTobs" cols="15" rows="4"></textarea></div>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSforn">Fornecedor : </label></td>
      <td class="colunaDireita">
	<select name="LSforn" id="LSforn" onKeyPress="NextField( 'TXTnum', event );">
    <?php
		//Escrendo os itens:
	  	options( $forns, @$_POST['LSforn'] );
	?>	  
    </select>
	</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTnum">N&uacute;mero : </label></td>
      <td class="colunaDireita"><input name="TXTnum" type="text" id="TXTnum" maxlength="12" onKeyPress="NextField( 'TXTemis', event );"></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTemis">Emiss&atilde;o  : </label></td>
      <td class="colunaDireita"><input name="TXTemis" type="text" id="TXTemis" maxlength="10" value="<?php echo @$_POST['TXTemis']; ?>" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTvenc', event );"></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTvenc">Vencimento : </label></td>
      <td class="colunaDireita"><input name="TXTvenc" type="text" id="TXTvenc" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');"  onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTval', event );" value="<?php echo @$_POST['TXTvenc']; ?>"></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTval">Valor : </label></td>
      <td class="colunaDireita"><input name="TXTval" type="text" id="TXTval" onKeyPress="return(currencyFormat(this,'.',',', event, 'TXTdesc' ));" maxlength="10"></td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTdesc">Desc. Financeiro : </label></td>
      <td class="colunaDireita"><input name="TXTdesc" type="text" id="TXTdesc" onKeyPress="return(currencyFormat(this,'.',',', event, 'LSplano' ));" maxlength="10">
        <a id="btnBox" href="javascript:;" onClick="abrirBox('num');" title="Outros valores"><img src="Imagens/calcIco.gif" width="15" height="16" border="0" align="absmiddle"></a></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSplano">Despesa :</label> </td>
      <td class="colunaDireita">
	  <select name="LSplano" id="LSplano" onKeyPress="return(currencyFormat(this,'.',',', event, 'LSdoc' ));" class="Listas">
	  <?php
		//Escrendo os itens:
	  	options( $planos, @$_POST['LSplano'] );
	  ?>
      </select></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSdoc">Tp. Doc  : </label></td>
      <td class="colunaDireita">
	  <select name="LSdoc" id="LSdoc" onKeyPress="NextField( 'RDfiscal', event );" class="Listas">
    <?php
		//Escrendo os itens:
	  	options( $docs, @$_POST['LSdoc'] );
	?>
	  </select>
      <a id="btnBoxObs" href="javascript:;" onClick="abrirBox('obs');" title="Observações"><img src="Imagens/comentIco.gif" width="16" height="15" border="0" align="absmiddle"></a></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="RDfiscal">Fiscal : </label></td>
      <td class="colunaDireita"><input name="RDfiscal" type="radio" value="s" onKeyPress="NextField( 'Submit', event );" checked>
      <label>Sim </label>
        <input name="RDfiscal" type="radio" value="n" onKeyPress="NextField( 'Submit', event );">
        <label>N&atilde;o</label></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"></td>
      <td class="colunaDireita"><input type="submit" name="Submit" value="Cadastrar">
&nbsp;|&nbsp;
<input type="reset" name="Reset" value="Limpar"> </td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"></td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" class="rodape">&nbsp;
      <span id="statusMsg">Processando informa&ccedil;&otilde;es...</span>
      </td>
    </tr>
  </table>
</form>
</body>
<?php 
	//No finalzinho temos este pequeno teste... para dar reload na pagina de abertura só depois de um agendamento...
	if( isset( $_POST['TXTval'] )) echo '<script language="javascript">opener.document.location.reload();</script>'; 
?>
</html>