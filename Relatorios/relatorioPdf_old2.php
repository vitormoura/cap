<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	header('Location: Index.php');
	
	
//Teste simples, depois melhore com a verificaçãodo usuário
if( !isset( $_GET['LSforn']) || !isset( $_GET['LSloja']) || !isset( $_GET['TXTnum']))
	die('Ocorreu um erro imprevisto, por favor contate o seu administrador.');

//Importando as bibliotecas principais:
	require_once('../Engine/driver.php');
	require_once('../Engine/sprintsql.php');
	require_once('../Engine/restaurar.php');
	include_once ('fpdf/pdml.php');

//Valores default para cada item enviado: ( Em caso de terem sido deixados em branco )

	$loja  = ( $_GET['LSloja'] )? $_GET['LSloja'] : '%';
	$forn  = ( $_GET['LSforn'] )? $_GET['LSforn'] : '%';
	$user  = ( $_GET['LSuser'] )? $_GET['LSuser'] : '%';
	$num   = ( $_GET['TXTnum'] )? $_GET['TXTnum'].'%' : '%';
	
	//Construindo pares de Emissao - Vencimento e Agendamento, de acordo com as informações passadas pelo usuário
	list( $emis1, $emis2 ) = testarDatas( $_GET['TXTem1'], $_GET['TXTem2'] );
	list( $venc1, $venc2 ) = testarDatas( $_GET['TXTve1'], $_GET['TXTve2'] );
	list( $agen1, $agen2 ) = testarDatas( $_GET['TXTag1'], $_GET['TXTag2'] );
	
	$doc    = ( $_GET['LSdoc'] )? $_GET['LSdoc'] : '%';
	$fisc   = ( isset( $_GET['RDfiscal']) )? $_GET['RDfiscal'] : '%';
	$stat   = ( isset( $_GET['RDpago']) )?   $_GET['RDpago']   : '%';
	
	//Listando as strings que serão utilizadas para construir o sistema de ordenação dos resultados:
	$campos = array( 1 =>' documento.vencimento ASC,',
					 3 =>' documento.fornecedor ASC,',
					 2 =>' documento.emissao ASC,',
					 4 =>' documento.loja ASC,'
					);

	//Ordenação começando...
	$ordenarPor = 'ORDER BY';
	
	//Usuário definiu algum critério ?
	if( isset( $_GET['LSordem'] ))
	{
		
		$ordem  = $_GET['LSordem'];	
		
		foreach ( $ordem AS $value )
		{
			$ordenarPor .= $campos[$value];
		}
		
		//Eliminando a última vírgula depois da iteração pelo array
		$ordenarPor = substr( $ordenarPor, 0, strlen( $ordenarPor ) - 1 );		
	}
	else
		//Ordenação padrão:
		$ordenarPor .= ' documento.codDoc DESC';
	
	
	//Mega query que pesquisa segundo os parametros fornecidos:
	$result = sprintsql("SELECT tpdoc.tipo, colaborador.fantasia AS forn, loja.fantasia AS loja, documento.numero, documento.emissao, documento.valor, documento.gravacao,
						 documento.contabil, documento.codDoc, pagamento.codPag, pagamento.vencimento, pagamento.aPagar, pagamento.descontos,
						 pagamento.acrescimos, pagamento.abatimentos, pagamento.situacao, usuario.login
						 FROM documento, tpdoc, colaborador, pagamento, usuario, loja
						 WHERE tpdoc.codtp = documento.codtp AND documento.fornecedor = colaborador.codcolab AND documento.codDoc = pagamento.codDoc AND documento.codUser = usuario.codUser
						 AND documento.loja = loja.codLoja AND documento.loja LIKE %s AND documento.fornecedor LIKE %s AND documento.codUser LIKE %s AND documento.numero LIKE %s AND documento.gravacao BETWEEN %t AND %t AND documento.emissao BETWEEN %t AND %t 
						 AND documento.vencimento BETWEEN %t AND %t AND documento.codTp LIKE %s AND documento.contabil LIKE %s AND pagamento.situacao LIKE %s $ordenarPor", 
						 $loja, $forn, $user, $num, $agen1, $agen2, $emis1, $emis2, $venc1, $venc2, $doc, $fisc, $stat, $ordenarPor
						 );
						 
	$rs    = dbQuery( $result );
	$total = (int)howMany( $rs );
?>
<pdml>
<head>
<title>Sample PDML document</title>
<font face=Arial size=8pt>
<page orientation="landscape">
</head>
<body>
<div top=1cm left=25pt width=90% height=90%> 
	<column count=1 break=page>
	<multicell>
	<b>
	<cell width=40 next=right align=left>Loja</cell>
	<cell width=80 next=right align=left>Fornecedor</cell>
	<cell width=80 next=right align=left>Documento</cell>
	<cell width=80 next=right align=left>Número</cell>
	<cell width=60 next=right align=center>Emissão</cell>
	<cell width=60 next=right align=center>Vencimento</cell>
	<cell width=60 next=right align=right>Bruto</cell>
	<cell width=60 next=right align=right>Desc.</cell>
	<cell width=60 next=right align=right>Acresc.</cell>
	<cell width=60 next=right align=right>Abat.</cell>
	<cell width=80 next=right align=right>Líquido</cell>
	<cell width=40 next=right align=center>Fiscal</cell>
	<cell width=20 next=right align=center>Pg</cell>
	</b>
	
	<br>
	<br height=3>
	<line>
	<br>
	
	<?php
	
	$totalBruto = 0;
	$totalAcres = 0;
	$totalDesc  = 0;
	$totalDed   = 0;
	$totalLiq   = 0;
	
	while( $dados = rsFetch( $rs ) )
		{
						
			$forn    = &restaurar( $dados['forn']);
			$loja    = &restaurar( $dados['loja']);
			$num     = &restaurar( $dados['numero']);
			$emissao = &restaurar( $dados['emissao'], 'data' );
			$venc    = &restaurar( $dados['vencimento'], 'data');
			$valorB  = &restaurar( $dados['valor'], 'valor');
			$desc    = &restaurar( $dados['descontos'], 'valor' );;
			$acres   = &restaurar( $dados['acrescimos'], 'valor');
			$abat    = &restaurar( $dados['abatimentos'], 'valor' );
			$valorL  = &restaurar((( $dados['valor'] + $dados['acrescimos'] ) - ( $dados['descontos'] + $dados['abatimentos'] )), 'valor');
			$docu     = $dados['tipo'];
			$contabil = $dados['contabil'];
			$stat    = &restaurar( $dados['situacao']);
			
			$totalBruto += $dados['valor'];
			$totalAcres += $dados['acrescimos'];
			$totalDesc  += $dados['descontos'];
			$totalDed   += $dados['abatimentos'];
			
			$totalLiq += ( $dados['valor'] + $dados['acrescimos'] ) - ( $dados['descontos'] + $dados['abatimentos'] );
	
	
	
	
	echo <<<TEXTO
	
	<cell width=40 next=right align=left>$loja</cell>
	<cell width=80 next="right" align=left>$forn</cell>
	<cell width=80 next="right" align=left>$docu</cell>
	<cell width=80 next=right align=left>$num</cell>
	<cell width=60 next=right align=center>$emissao</cell>
	<cell width=60 next=right align=center>$venc</cell>
	<cell width=60 next=right align=right>$valorB</cell>
	<cell width=60 next=right align=right>$desc</cell>
	<cell width=60 next=right align=right>$acres</cell>
	<cell width=60 next=right align=right>$abat</cell>
	<cell width=80 next=right align=right>$totalLiq</cell>
	<cell width=40 next=right align=center>$contabil</cell>
	<cell width=20 next=right align=center>$stat</cell>
	<br height=12>
	
TEXTO;
		
}
?>
</multicell>
</column>
</div>
</body>
</pdml>
