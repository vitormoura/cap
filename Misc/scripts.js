function noSubmition ( form ) { 
/*
Funcao     : O botao submit do formulario fica inativo ate o usuario preencher todos os campos. 
Autor      : Vitor ( Indrema )
Escrito em : 28/08/2004
*/

	var i;
	var x = 0;
	
	
	for(i = 0; i < form.elements.length; i++) { 	 //Inicio do loop que vai percorrer todo o formulario
		if ( form.elements[i].value != '' ) { 		 // Testa se o campo atual nao esta em branco
			x++; 									 // Soma 1 a variavel x, nosso flag de teste
			
			if ( form.elements[i].type == 'submit' ) {// Identifica se o elemento atual e o botao submit
				var button = form.elements[i] 		  // Se sim, grava o elemento na variavel button
			}
		}
	}
		//Depois de analizar cada elemento do form ele testa a quantidade de elementos que nao estao vazios
		//este valor precisa ser igual ao numero de elementos do form para que o botao possa ser acionado
		( form.elements.length == x ) ? button.disabled = false : button.disabled = true;
	
}

function noBlank( itens, msg )
{ 
	var x;
	var n  = itens.length;
	
	//Testamos se todos os campos foram preenchidos certinho...
	for(x = 0; x < n; x++)
	{
		if( itens[x].value == '' )
		{
			window.alert( msg );
			return false
		}
			
	}
}

function Init()
{
/**
Esta função tem por objetivo preparar o scripting das páginas de acordo com a opção desejada:
**/
//Posiciona o cursor no primeiro campo:
	document.forms[0].elements[0].focus();
	noSubmition( document.forms[0]);
//Mudando barra de status
	//window.status('Distrital Intranet');
	
	return true;
}