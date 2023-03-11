<?PHP
//Escreve conjuntos de '<option>' de acordo com uma lista de codigo - item

function options( $query, $selected = 0 )
{
	@require_once('driver.php');
	
	$rs      = dbQuery( $query );
	$options = '';
	$destaques = '';
	
	//Escreve um par de <option></option> a cada iteração
	while ( $linha = rsFetch( $rs, 'NUM' ) )
	{
		///*
		$options .= "\t<option value=\"$linha[0]\"";
		
		//Testamos se o codigo atual é o que queremos que seja selecionado
		if( $selected == $linha[0] )
			$options .= ' selected="selected"';
		
		$options .= ">$linha[1]</option>\n";
		//*/
		//Testamos se este fornecedor é da classe destaque
		/*
		if( $linha[2] == 'e' )
			$destaques 	.= escrever_option( $linha, $selected );
		else
			$options 	.= escrever_option( $linha, $selected );
		*/
		
	}
	
		//Escrevemos primeiro os detaques ...
		//echo '<optgroup label="Destaques">';
		//echo $destaques;
		//echo '</optgroup>';
		
		//agora os demais...
		//echo '<optgroup label="Outros">';
		echo $options;
		//echo '</optgroup>';
	
}

function escrever_option( &$linha, $selected ) {

		$temp = "\t<option value=\"$linha[0]\"";
		
		//Testamos se o codigo atual é o que queremos que seja selecionado
		if( $selected == $linha[0] )
			$temp .= ' selected="selected"';
		
		return $temp.">$linha[1]</option>\n";
}
?>