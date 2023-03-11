<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	die('Erro interno : Contate seu administrador');

//Importando bibliotecas:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');


if( isset( $_GET['codigo'] ) && is_numeric( $_GET['codigo'] ) )
{
	//Mensagens de layout, aqui encontram-se as defaults
	$msg    = 'Confirme os detalhes no gerenciador de notificações';
	$titulo = 'Notifica&ccedil;&atilde;o já gravada';
	$imagem = '<img src="Imagens/redoIco.gif" width="46" height="48" align="left" border="0" class="icones">';
	
	$info = dbQuery( sprintsql( 'SELECT codLemb FROM lembrete WHERE tipo = "Notificacao" AND ref = %s', $_GET['codigo'] ));
		
	if( !$dados = rsFetch( $info ) )
	{
		//Descubro junto ao banco de dados informações para escrever uma notificação legal...
		$info  = dbQuery( sprintsql('SELECT tpdoc.tipo, documento.codDoc, documento.vencimento, documento.numero, loja.fantasia AS loja, colaborador.fantasia AS forn FROM documento, colaborador, loja, pagamento, tpdoc
									  WHERE documento.codTp = tpdoc.codTp AND documento.fornecedor = colaborador.codColab AND documento.loja = loja.codLoja AND pagamento.situacao = "n" AND documento.codDoc = %d', $_GET['codigo'] ));	
		
		if( $teste = rsFetch( $info ) )
		{
			require_once('Engine/restaurar.php');

			//Calculando a data de notificação... será sempre 5 dias antes do vencimento:
			$lembrarEm = date("d-m-Y", ( ( (int)$teste['vencimento'] ) - ( 60 * 60 * 24 ) * 5 ) );
			
			//Escrevendo o conteudo da mensagem:
			$msg       = 'O boleto para o pagamento da(o) '.$teste['tipo'].' da(o) '.$teste['forn'].', número '.$teste['numero'].' com vencimento em '
							.restaurar( $teste['vencimento'], 'data').', ainda não nos foi enviado. Por favor contate o fornecedor.';
			
			
			//É importante lembrar o 'tipo' do lembrete, que deve casar com o mesmo que é exluido juntamente com 
			//os documentos... revise logo acima caso um dia altere esse método
			dbQuery(sprintsql( "INSERT INTO lembrete ( tipo, ref, lida, dtLemb, mensagem ) VALUES ( %s, %s, %s, %t, %s )",
					'Notificacao', $teste['codDoc'], 'n', $lembrarEm, $msg ));
			
			//Mudando as mensagens para confirmar a gravação:		
			$msg    = 'Voc&ecirc; ser&aacute; lembrado em '.$lembrarEm;
			$titulo = 'Notifica&ccedil;&atilde;o gravada';
			$imagem = '<img src="Imagens/okIco.gif" width="40" height="34" align="absmiddle" border="0" class="icones">';
		}
			
	}

}
else 
	die( 'Aconteceu um imprevisto, por favor contate o administrador');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>
<body onClick="self.close()">
<table width="277" border="0" cellspacing="0">
  <tr>
    <td width="275" height="39" class="topoSup"><span class="icones"><?php echo $titulo; ?></span></td>
  </tr>
  <tr>
    <td height="78" class="textoDestaque"><a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" onClick="opener.document.location = 'notificacoes.php';"><?php echo $imagem.$msg; ?></a></td>
  </tr>
  <tr>
    <td class="topoBase">&nbsp;</td>
  </tr>
</table>
</body>
</html>