function DataCheck ( objeto ) 
{

// Esta funcao exige que seja passado como paramentro uma amostra de data, no formato
// 99/99/9999. Ela faz os testes e retorna TRUE caso a data seja valida ou caso 
// contrario FALSE.


//Preparando os dados obtidos

var amostra = objeto.value;

if ( amostra == '' )
{
	return true;
}

Data = amostra.split("-");

Dia = parseFloat( Data[0] );
Mes = parseFloat( Data[1] );
Ano = parseFloat( Data[2] );


//Fazendo os testes


//Testando se o ano e valido

if ( Ano > 1900 && Ano < 2100 )
{
	
//Testando se o mes e valido

	if ( Mes >= 1 && Mes <= 12 )
	{
	
//testando se o dia e valido		

		if ( ( Mes <= 7 && Mes % 2 || Mes > 7 && !( Mes % 2 ) ) && ( Dia >= 1 && Dia <= 31 ) )
		{ return true; }
	
		else if ( ( Mes <= 6 && !( Mes % 2 ) && Mes != 2 || Mes > 8 &&  Mes % 2 ) && ( Dia >= 1 && Dia <= 30 ) )
		{ return true; }

//Testando o dia em caso de ano bissexto

		else if ( ( Mes == 2 && !( Ano % 4 ) ) && ( Dia >= 1 && Dia <= 29 ) )
        { return true; }
	
		else if ( ( Mes == 2 && Ano % 4 ) && ( Dia >= 1 && Dia <= 28 ) )
 		{ return true; }
		
		else
		{
			window.alert('Data inválida');
			objeto.focus();
			return false;
		}
	}
	else
	{
		window.alert('Data inválida');
		objeto.focus();
		return false;
	}
	
}


//Caso todos os testes falhem em retornar verdadeiro, a funcao finaliza retornando falso

else 
{
	window.alert('Data inválida');
	objeto.focus();
	return false;
}
	

}

