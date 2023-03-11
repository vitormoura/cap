<?php
//Arquivo de geração de PDFs -- Versão BETA em testes -- 31/08/05

require_once 'fpdf/fpdf.php';
define('FPDF_FONTPATH','font/');

//session_start();

//Autenticando o usuário:
//if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	//header('Location: Index.php');


//Teste simples
if( !isset( $_GET['LSforn']) || !isset( $_GET['LSloja']) || !isset( $_GET['TXTnum']))
	die('Ocorreu um erro imprevisto, por favor contate o seu administrador.');

//Importando as bibliotecas principais:
	require_once('../Engine/driver.php');
	require_once('../Engine/sprintsql.php');
	require_once('../Engine/restaurar.php');

//Valores default para cada item enviado: ( Em caso de terem sido deixados em branco )

	$loja  = ( $_GET['LSloja'] )? $_GET['LSloja'] : '%';
	$forn  = ( $_GET['LSforn'] )? $_GET['LSforn'] : '%';
	$user  = ( $_GET['LSuser'] )? $_GET['LSuser'] : '%';
	$num   = ( $_GET['TXTnum'] )? $_GET['TXTnum'].'%' : '%';
	$plano = ( $_GET['LSplano'] )? $_GET['LSplano'] : '%';
	
	//Construindo pares de Emissao - Vencimento, de acordo com as informações passadas pelo usuário
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
	$result = sprintsql("SELECT tpdoc.tipo, colaborador.fantasia AS forn, loja.fantasia AS loja, documento.numero, documento.emissao, documento.valor,
						 documento.contabil, documento.codDoc, pagamento.codPag, pagamento.vencimento, pagamento.aPagar, pagamento.descontos,
						 pagamento.acrescimos, pagamento.abatimentos, pagamento.situacao, usuario.login
						 FROM documento, tpdoc, colaborador, pagamento, usuario, loja
						 WHERE tpdoc.codtp = documento.codtp AND documento.fornecedor = colaborador.codcolab AND documento.codDoc = pagamento.codDoc AND documento.codUser = usuario.codUser
						 AND documento.loja = loja.codLoja AND documento.loja LIKE %s AND documento.fornecedor LIKE %s AND documento.codUser LIKE %s AND documento.numero LIKE %s AND documento.gravacao BETWEEN %t AND %t AND documento.emissao BETWEEN %t AND %t 
						 AND documento.vencimento BETWEEN %t AND %t AND documento.codTp LIKE %s AND documento.contabil LIKE %s AND pagamento.codPlano LIKE %s AND pagamento.situacao LIKE %s $ordenarPor", 
						 $loja, $forn, $user, $num, $agen1, $agen2, $emis1, $emis2, $venc1, $venc2, $doc, $fisc, $plano, $stat, $ordenarPor
						 );
						 
	$rs    = dbQuery( $result );
	$total = (int)howMany( $rs );
	//*/
	$doc   = new FPDF('L','cm');
	$doc->AddPage();
	$doc->SetFont('Arial','B',8);
	$doc->Image('../Imagens/IntraLogo.jpg',1,1,4.8683,0.3175);
	$doc->SetY(2.2);
	$doc->Write(0.5, 'Resultados da Pesquisa :');
	$doc->SetX(20);
	$doc->Write(0.5, $total.' itens combinam com as especificações');
	$doc->ln();
	$doc->ln();
	$doc->Cell(2,0.5,'Loja',0,0,'C' );
	$doc->Cell(3,0.5,'Fornecedor');
	$doc->Cell(3,0.5,'Documento');
	$doc->Cell(3,0.5,'Número',0,0,'L');
	$doc->Cell(2.1,0.5,'Emissão',0,0,'C');
	$doc->Cell(2.5,0.5,'Vencimento',0,0,'C');
	$doc->Cell(2.1,0.5,'Bruto',0,0,'R');
	$doc->Cell(1.9,0.5,'Desc',0,0,'R');
	$doc->Cell(1.9,0.5,'Acresc',0,0,'R');
	$doc->Cell(1.9,0.5,'Deduc',0,0,'R');
	$doc->Cell(2.1,0.5,'Líquido',0,0,'R');
	$doc->Cell(1.2,0.5,'Fiscal',0,0,'C');
	$doc->Cell(1,0.5,'Pg',0,0,'C');
	$doc->ln();
	$doc->Line(1,3.67,29,3.67);
	$doc->ln();
	
	
			$totalBruto = 0.0;
			$totalAcres = 0.0;
			$totalDesc  = 0.0;
			$totalDed   = 0.0;
			
	$doc->SetFont('Arial','',8);
	
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
	
			$doc->Cell(2,0.5, $loja);
			$doc->Cell(3,0.5,$forn );
			$doc->Cell(3,0.5,$docu );
			$doc->Cell(3,0.5,$num );
			$doc->Cell(2.1,0.5,$emissao,0,0,'C');
			$doc->Cell(2.5,0.5,$venc,0,0,'C');
			$doc->Cell(2.1,0.5,$valorB,0,0,'R');
			$doc->Cell(1.9,0.5,$desc,0,0,'R');
			$doc->Cell(1.9,0.5,$acres,0,0,'R');
			$doc->Cell(1.9,0.5,$abat,0,0,'R');
			$doc->Cell(2.1,0.5,$valorL,0,0,'R');
			$doc->Cell(1.2,0.5,$contabil,0,0,'C');
			$doc->Cell(1,0.5,$stat,0,0,'C');
			$doc->ln();
		}
		
	$doc->Ln();
	$doc->Line($doc->GetX(), $doc->GetY(),29, $doc->GetY());
	$doc->Ln();
	
	$doc->SetFont('Arial','B',8);
	
	$doc->Cell(2,0.5,'' );
	$doc->Cell(3,0.5,'' );
	$doc->Cell(3,0.5,'' );
	$doc->Cell(3,0.5,'' );
	$doc->Cell(2.1,0.5,'');
	$doc->Cell(2.5,0.5,'');
	$doc->Cell(2.1,0.5,restaurar( $totalBruto, 'valor'),0,0,'R');
	$doc->Cell(1.9,0.5,restaurar( $totalDesc, 'valor'),0,0,'R');
	$doc->Cell(1.9,0.5,restaurar( $totalAcres, 'valor'),0,0,'R');
	$doc->Cell(1.9,0.5,restaurar( $totalDed, 'valor'),0,0,'R');
	$doc->Cell(2.1,0.5,restaurar( ( $totalBruto + $totalAcres ) - ( $totalDesc + $totalDed ), 'valor' ),0,0,'R');
	$doc->Cell(1.2,0.5,'',0,0,'C');
	$doc->Cell(1,0.5,'',0,0,'C');
		
	$doc->Output('relatorio.pdf','D');
	//$doc->Output();
	
?>