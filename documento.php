<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	die('Usuário não autenticado, por favor efetue seu login');
	

if( !empty( $_GET['codigo']))
{

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');
	require_once('Engine/restaurar.php');

//Mega query que pesquisa segundo os parametros fornecidos:
	$result = sprintsql("SELECT tpdoc.tipo, colaborador.fantasia AS forn, loja.fantasia AS loja, documento.numero, documento.emissao, documento.valor,
						 documento.contabil, pagamento.codPag, pagamento.vencimento, pagamento.aPagar, pagamento.descontos, documento.codNote,
						 pagamento.acrescimos, pagamento.abatimentos, pagamento.situacao, pagamento.comentario, usuario.login, pagamento.codMov, PlanoDeContas.plano
						 FROM documento, tpdoc, colaborador, pagamento, usuario, loja, PlanoDeContas
						 WHERE tpdoc.codtp = documento.codtp AND documento.fornecedor = colaborador.codcolab AND documento.codDoc = pagamento.codDoc AND documento.codUser = usuario.codUser 
						 AND documento.loja = loja.codLoja AND
						 documento.codDoc = %d AND pagamento.codPlano = PlanoDeContas.codPlano",
						 $_GET['codigo']
						 );

	$rs    = dbQuery( $result );
		 
	if( !$dados = rsFetch( $rs ) )
		die( 'Ocorreu um erro imprevisto, contate o administrador');
		
	if( $dados['codNote'] )
	{
		$rs2 = dbQuery( sprintsql( 'SELECT nota FROM anotacao WHERE codNote = %d', $dados['codNote']));
		$obs = rsFetch( $rs2 );
	}
	
	if( $dados['codMov'] )
	{
		$result = sprintsql('SELECT banco.nome AS banco, formaPag.formaPag, conta.cc, movimentacao.numero, movimentacao.dtMov, movimentacao.codMov
							 FROM movimentacao, conta, formaPag, banco
							 WHERE movimentacao.codForma = formaPag.codForma AND movimentacao.codConta = conta.codConta AND banco.codBanco = conta.codBanco AND
							 movimentacao.codMov = %d',				 
							 $dados['codMov']
							 );
		$rs    = dbQuery( $result );

		if( !$dados2 = rsFetch( $rs ) )
			die( 'Ocorreu um erro imprevisto, contate o administrador');
		
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Informa&ccedil;&otilde;es Gerais</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>
<body onLoad="Init();">
<table width="485" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="158" height="42" align="center"><img src="Imagens/IntranetLogo.gif" width="141" height="13"></td>
    <td width="145" align="center">&nbsp;</td>
    <td width="174" align="center">Documento</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><span class="titulo">Informa&ccedil;&otilde;es Gerais </span></td>
  </tr>
</table>
<table width="485" height="608" border="0" cellspacing="0">
  <tr>
    <td height="26" align="right" class="colunaEsquerda">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="26" align="right" class="colunaEsquerda">&nbsp;</td>
    <td class="line">Documento : </td>
  </tr>
  <tr>
    <td width="159" height="24" align="right" class="colunaEsquerda">Loja :</td>
    <td width="322" class="colunaDireita"><?php echo restaurar($dados['loja']); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Fornecedor : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['forn']); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Documento : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['tipo']); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">N&uacute;mero : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['numero']); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Emiss&atilde;o : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['emissao'], 'data'); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" valign="top" class="colunaEsquerda">Vencimento : </td>
	<td valign="top" class="colunaDireita"><?php echo restaurar($dados['vencimento'], 'data'); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Fiscal : </td>
    <td class="colunaDireita"><?php echo ( $dados['contabil'] == 's' )? 'Sim' : 'Não'; ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Observa&ccedil;&otilde;es :</td>
    <td class="colunaDireita"><?php echo ( isset( $obs ) )? restaurar( $obs['nota'] ) : 'Nenhuma observação associada'; ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">&nbsp;</td>
    <td class="line">Informa&ccedil;&otilde;es sobre a fatura : </td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Despesa : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['plano']); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Valor Bruto : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['valor'], 'valor'); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Descontos : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['descontos'], 'valor'); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Acrescimos : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['acrescimos'], 'valor'); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Outras Dedu&ccedil;&otilde;es : </td>
    <td class="colunaDireita"><?php echo restaurar($dados['abatimentos'], 'valor'); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">L&iacute;quido a Pagar : </td>
    <td class="colunaDireita"><?php echo restaurar(( $dados['valor'] + $dados['acrescimos']) - ( $dados['abatimentos'] + $dados['descontos'] ), 'valor'); ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Coment&aacute;rios : </td>
    <td class="colunaDireita"><?php echo ( $dados['comentario'] )? restaurar($dados['comentario'], 'coment') : 'Nenhum comentário'; ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">&nbsp;</td>
    <td class="line">Informa&ccedil;&otilde;es sobre o Pagamento : </td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Conta  : </td>
    <td class="colunaDireita"><?php echo (isset( $dados2 ))? $dados2['banco'].' - '.$dados2['cc'] : '' ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Forma de Pagamento : </td>
    <td class="colunaDireita"><?php echo (isset( $dados2 ))? '<a href="relatorios/documentos.php?codigo='.$dados2['codMov'].'" target="_blank">'.restaurar( $dados2['formaPag'] ).'</a>' : '' ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">Data : </td>
    <td class="colunaDireita"><?php echo (isset( $dados2 ))? restaurar( $dados2['dtMov'], 'data' ) : '' ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">N&uacute;mero : </td>
    <td class="colunaDireita"><?php echo (isset( $dados2 ))? restaurar( $dados2['numero'] ) : '' ?></td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">&nbsp;</td>
    <td class="colunaDireita">&nbsp;</td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">&nbsp;</td>
    <td class="colunaDireita">&nbsp;</td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">&nbsp;</td>
    <td class="colunaDireita">&nbsp;</td>
  </tr>
  <tr>
    <td height="24" align="right" class="colunaEsquerda">&nbsp;</td>
    <td class="colunaDireita">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="rodape">&nbsp;</td>
  </tr>
</table>
</body>
</html>
