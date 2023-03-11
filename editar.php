<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	die('Usuário não autenticado, por favor efetue seu login');

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');
	
//O usuário esta tentando editar o documento ?
if( !empty( $_POST['LSloja'])  && !empty( $_POST['LSforn']) && !empty( $_POST['TXTnum'])  && !empty( $_POST['TXTemis']) && isset( $_POST['HDcodCom']) &&
	!empty( $_POST['TXTvenc']) && !empty( $_POST['TXTval']) && !empty( $_POST['LSdoc'])   && !empty( $_POST['RDfiscal']) && !empty( $_POST['HDcod']) && !empty( $_POST['LSplano']))
{
	
	$codNote = (int)$_POST['HDcodCom'];		//Capturando o codigo da observação
	$obs     = trim( @$_POST['TEXTobs'] );	//Limpando o texto da observação
	
	//No próximo bloco, faremos uma série testes para decidir o que fazer com as observações, uma vez que estão 
	//separadas do documento na base de dados...

	//Se obs não for 0e possuir conteudo, significa que o doc tinha uma, portanto editamos para o novo conteudo
	if( !empty( $obs ) && $codNote != 0 )
	{
		$updateSql = sprintsql('UPDATE anotacao SET nota=%s WHERE codNote = %d', $obs, $codNote );
		dbQuery( $updateSql );
	}
	//Se obs for igual a zero e possuir conteudo, significa que o usuário preencheu seus dados agora, portanto inserimos 
	else if( !empty( $obs ) && $codNote == 0 )
	{
		$insertSql = sprintsql('INSERT INTO anotacao ( nota ) VALUES ( %s )', $obs );
		$codNote   = dbQuery( $insertSql );
	}
	//Por fim resta se a obs existia, mas foi apagada... neste caso a deletamos da base de dados
	else if( empty( $obs ) && $codNote != 0 )
	{
		dbQuery( sprintsql( 'DELETE FROM anotacao WHERE codNote = %d', $codNote ));
		$codNote = 0;	//Zeramos o código para editar corretamente o documento logo abaixo
	}
	
	//Preparamos a edição do documento com os dados enviados: ( sprintsql.php formata todas as entradas )
	$updateSql = sprintsql('UPDATE documento SET codTp=%d, codNote=%d, loja=%d, fornecedor=%d,
							 numero=%s, emissao=%t, vencimento=%t, valor=%f, contabil=%s , base=%f WHERE documento.codDoc = %d',
							$_POST['LSdoc'], $codNote, $_POST['LSloja'], $_POST['LSforn'], $_POST['TXTnum'],
							$_POST['TXTemis'], $_POST['TXTvenc'], $_POST['TXTval'], $_POST['RDfiscal'], $_POST['TXTbase'], $_POST['HDcod']
						  );
	
	//Banco de dados executa...
	dbQuery( $updateSql );
	
	//Preparamos a edição da fatura com os dados enviados: ( sprintsql.php formata todas as entradas )
	$updateSql = sprintsql('UPDATE pagamento SET codPlano=%d, vencimento=%t, aPagar=%f, descontos=%f, acrescimos=%f, abatimentos=%f, comentario=%s WHERE codDoc = %d',
							(int)$_POST['LSplano'], $_POST['TXTvenc'], $_POST['TXTval'], $_POST['TXTdesc'], $_POST['TXTacres'], $_POST['TXTded'], $_POST['TXTcom'], $_POST['HDcod']
							);
	
	//Banco de dados executa...						
	dbQuery( $updateSql );
	
	//Para fechar a janela eu mato a janela escrevendo um comando em javascript para o browser:
	exit( '<script language="javascript"> opener.document.location.reload(); self.close(); </script>' );
	
	//Fique atento ao bug do update... essa janela precisa fechar de qualquer forma nesse ponto !
	
}

//Nesta hipótese o usuário acaba de acessar, precisamos então capturar os dados do documento selecionado:
$documento = dbQuery( sprintsql('SELECT documento.codTp, documento.loja, documento.fornecedor, documento.numero, documento.emissao, documento.valor, documento.base,
						   documento.contabil, documento.codNote, documento.codDoc, pagamento.codPag, pagamento.codPlano, pagamento.vencimento, pagamento.aPagar, pagamento.descontos,
						   pagamento.acrescimos, pagamento.abatimentos, pagamento.comentario 
						   FROM documento, pagamento
						   WHERE documento.codDoc = pagamento.codDoc AND pagamento.situacao = "n" AND documento.codDoc = %d', @(int)$_GET['codigo']
						 )
					);

//Capturando os dados...
$dados     = rsFetch( $documento );

//Se o documento possuir comentário, consultamos o banco de dados:
if( $dados['codNote'] )
{
	$nota       = dbQuery( sprintsql( 'SELECT nota FROM anotacao WHERE codNote = %d', $dados['codNote']));
	$comentario = rsFetch( $nota );
}

//Obtendo a lista de fornecedores, lojas e documentos:	
	$forns  = 'SELECT codColab, fantasia, destaque FROM colaborador ORDER BY  fantasia ASC';
	$lojas  = 'SELECT codLoja, fantasia FROM loja ORDER BY destaque, fantasia ASC';
	$docs   = 'SELECT codTp, tipo FROM tpDoc ORDER BY codTp ASC';
	$planos = 'SELECT codPlano, plano FROM PlanoDeContas ORDER BY codPlano ASC';

//Importando as bibliotecas responsáveis por formatação e layout:
	require_once('Engine/options.php');
	require_once('Engine/restaurar.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Editar Agendamento</title>

<!-- Impotando a coleção de scripts e estilos css -->
	<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
	<script language="javascript" type="text/javascript" src="Misc/agendamento.js"></script>
	<script language="javascript" type="text/javascript" src="Misc/DataCheck.js"></script>
	<script language="javascript" type="text/javascript" src="Misc/mask.js"></script>
	<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>

<body onLoad="Init(); apurarDesconto();">
<table width="485" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="158" height="42" align="center"><img src="Imagens/IntranetLogo.gif" width="141" height="13"></td>
    <td width="145" align="center">&nbsp;</td>
    <td width="174" align="center">Agenda</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><span class="titulo">Editar Agendamento</span></td>
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
	  	options( $lojas, $dados['loja'] );
	?>
      </select>
	  </td>
    </tr>
	<div id="box" style="position:absolute; left:315px; top:237px; width:150px; height:125px; z-index:99;" onDblClick="fecharBox('num');"><label for="TXTacres">Acrescimos :</label>
  <input type="text" name="TXTacres" id="TXTacres" maxlenght="10" onKeyPress="return(currencyFormat(this,'.',',', event, 'TXTded' ));" value="<?php echo ( (float)$dados['acrescimos'] )? restaurar( $dados['acrescimos'], 'valor'): ''; ?>">
  <br> 
  <label for="TXTded">Outras Dedu&ccedil;&otilde;es :</label>
  <input type="text" name="TXTded" id="TXTded" maxlenght="10" onKeyPress="return(currencyFormat(this,'.',',', event, 'TXTcom' ));" value="<?php echo ( (float)$dados['abatimentos'] )? restaurar( $dados['abatimentos'], 'valor'): ''; ?>">
  <br>
  <label for="TXTcom">Coment&aacute;rios :</label>
  <input type="text" name="TXTcom" id="TXTcom" maxlenght="50" value="<?php echo restaurar( $dados['comentario'], 'string'); ?>">
</div>
<div id="boxObs" style="position:absolute; left:314px; top:237px; width:150px; height:105px; z-index:100" onDblClick="fecharBox('obs');"><label for="TEXTobs">Observa&ccedil;&otilde;es :</label>
<textarea name="TEXTobs" id="TEXTobs" cols="15" rows="4"><?php echo @restaurar( $comentario['nota'], 'string'); ?></textarea></div>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSforn">Fornecedor : </label></td>
      <td class="colunaDireita">
	<select name="LSforn" id="LSforn" onKeyPress="NextField( 'TXTnum', event );">
    <?php
		//Escrendo os itens:
	  	options( $forns, $dados['fornecedor'] );
	?>	  
    </select>
	</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTnum">N&uacute;mero : </label></td>
      <td class="colunaDireita"><input name="TXTnum" type="text" id="TXTnum" maxlength="12" onKeyPress="NextField( 'TXTemis', event );" value="<?php echo restaurar( $dados['numero'], 'string'); ?>"></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTemis">Emiss&atilde;o  : </label></td>
      <td class="colunaDireita"><input name="TXTemis" type="text" id="TXTemis" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTvenc', event );" value="<?php echo restaurar( $dados['emissao'], 'data'); ?>"></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTvenc">Vencimento : </label></td>
      <td class="colunaDireita"><input name="TXTvenc" type="text" id="TXTvenc" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');"  onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTbase', event );" value="<?php echo restaurar( $dados['vencimento'], 'data'); ?>"></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda">Base p/ c&aacute;lculo : </td>
      <td class="colunaDireita"><input name="TXTbase" type="text" id="TXTbase" onKeyPress="return(currencyFormat(this,'.',',', event, 'TXTval' ));" maxlength="10" value="<?php echo restaurar( $dados['base'], 'valor'); ?>">
	  <a href="javascript:;" title="Atrav&eacute;s do preenchimento deste campo, &eacute; poss&iacute;vel analizar posteriormente o perfil de descontos deste fornecedor.">[ Info ]</a>
	  </td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="TXTval">Valor : </label></td>
      <td class="colunaDireita"><input name="TXTval" type="text" id="TXTval" onKeyPress="return(currencyFormat(this,'.',',', event, 'TXTdesc' ));" maxlength="10" value="<?php echo restaurar( $dados['valor'], 'valor'); ?>"> <a id="btnBox" href="javascript:;" onClick="abrirBox('num');" title="Outros valores"><img src="Imagens/calcIco.gif" width="15" height="16" border="0" align="absmiddle"></a></td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTdesc">Desc. Financeiro : </label></td>
      <td class="colunaDireita"><input name="TXTdesc" type="text" id="TXTdesc" onKeyPress="return(currencyFormat(this,'.',',', event, 'LSplano' ));" maxlength="10" value="<?php echo ( (float)$dados['descontos'] )? restaurar( $dados['descontos'], 'valor'): ''; ?>" onBlur="apurarDesconto();"> <span id="descDisplay">&nbsp;</span>
        </td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSplano">Despesa : </label></td>
      <td class="colunaDireita">
	  <select name="LSplano" class="Listas" id="LSplano" onKeyPress="return(currencyFormat(this,'.',',', event, 'LSdoc' ));">
	  <?php
		//Escrendo os itens:
	  	options( $planos, $dados['codPlano'] );
	  ?>
      </select>
	  <a href="javascript:;" title="Informe-se junto ao seu reponsável qual a op&ccedil;&atilde;o indicada dependendo dos produtos/serviços constantes neste documento.">[ Info ]</a>
	  </td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="LSdoc">Tp. Doc  : </label></td>
      <td class="colunaDireita">
	  <select name="LSdoc" class="Listas" id="LSdoc" onKeyPress="NextField( 'RDfiscal', event );">
    <?php
		//Escrendo os itens:
	  	options( $docs, $dados['codTp'] );
	?>
	  </select>
      <a id="btnBoxObs" href="javascript:;" onClick="abrirBox('obs');" title="Observações"><img src="Imagens/comentIco.gif" width="16" height="15" border="0" align="absmiddle"></a></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"><label for="RDfiscal">Fiscal : </label></td>
      <td class="colunaDireita">
	  <input name="RDfiscal" type="radio" value="s" onKeyPress="NextField( 'Submit', event );" <?php echo ( $dados['contabil'] == 's' )? 'checked':''; ?>>
      <label>Sim </label>
      <input name="RDfiscal" type="radio" value="n" onKeyPress="NextField( 'Submit', event );" <?php echo ( $dados['contabil'] == 'n' )? 'checked':''; ?>>
      <label>N&atilde;o</label></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda"></td>
      <td class="colunaDireita"><input type="submit" name="Submit" value="Editar "></td>
    </tr>
    <tr>
      <td align="right" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" class="rodape">&nbsp;
      <span id="statusMsg">Processando informa&ccedil;&otilde;es...</span>
      </td>
    </tr>
  </table>
<input type="hidden" name="HDcodCom" id="HDcodCom" value="<?php echo $dados['codNote']; ?>">
<input type="hidden" name="HDcod" id="HDcod" value="<?php echo $dados['codDoc']; ?>">
</form>
</body>
</html>