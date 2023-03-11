// Biblioteca de chamadas comuns:

function mascarar(src, mask) {
/*
Funcao     : Mascara para campos de data.
Autor      : Desconhecido - Script baixado no site www.scriptbrasil.com.br
Escrito em : Incerto ( Editado por mim em 17/02/2005 - Suporte ao Firefox adicionado )
*/
			
		var i = src.value.length;
		var saida = mask.substring(i,i+1);
		
		//Testando o browser...
		if( document.all )
			var ascii = event.keyCode;
		else
			var ascii = window.captureEvents( Event.KEYPRESS );		
		
		if (saida == "A") {
			if ((ascii >=97) && (ascii <= 122)) { Event.keyCode -= 32; }
			else { Event.keyCode = 0; }
		} else if (saida == "0") {
			if ((ascii >= 48) && (ascii <= 57)) { return }
			else { Event.keyCode = 0 }
		} else if (saida == "#") {
			return;
		} else {
			src.value += saida;
			i += 1
			saida = mask.substring(i,i+1);
			if (saida == "A") {
				if ((ascii >=97) && (ascii <= 122)) { Event.keyCode -= 32; }
				else { Event.keyCode = 0; }
			} else if (saida == "0") {
				if ((ascii >= 48) && (ascii <= 57)) { return }
				else { Event.keyCode = 0 }
			} else { return; }
		}
}

function Confirm( Frase )
{
	if(window.confirm('Deseja realmente excluir este '+Frase+' ?'))
		return true;
	else
		return false;
}

function isCiente( Frase )
{
	if(window.confirm('Confirma estar ciente das informações ?'))
		return true;
	else
		return false;
}

function NextField( next, e )
{
	tecla   = 13; 			//Altere para a tecla desejada, neste caso 13 significa ENTER
	campo   = eval( document.getElementById( next ) );
	whichCode = (window.Event) ? e.which : e.keyCode;

	if( whichCode == tecla || whichCode == 0 )
	{
		campo.focus();
		window.event.returnValue = false; //Atributo salvador lembra ?
		
	}
	return false;
	
}

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}

function abrirJanelaDeAgendamento( url )
{
	//Abre uma janela de acordo com as caracteristicas abaixo, apontando para o parametro URL
	win = window.open(url, "janela", "menubar=no, resizable=no, width=485, height=467");
	win.focus();
}

function abrirJanela( url )
{
	//Abre uma janela de acordo com as caracteristicas abaixo, apontando para o parametro URL
	win = window.open(url, "janela", "menubar=no, resizable=no, width=485, height=467");
	win.focus();
}

function abrirJanelaModal( url )
{
	//if( document.all )
		//Janela modal real, no caso do internet explorer
		//window.showModalDialog( url,'micro','dialogHeight=177px ; dialogWidth=284px ; help=no ; status=no');
	//else
		//No caso do firefox, simulamos a janela modal com uma janela tradicional
		win = window.open( url, "micro", " status=no, menubar=no, resizable=no, scrollbars=no, width=277, height=144");
		win.focus();
		
}

function abrirJanelaComum( url )
{
	win = window.open( url, "micro", " status=no, menubar=no, resizable=no, scrollbars=yes, width=503, height=440");
	win.focus();
}

function Status()
{
	window.status = "Distrital Intranet";
	return true;
}

function Cor( table, row, state )
{
	table = eval( document.getElementById( table ) );
	
	switch ( state )
	{
		case 'over':
		table.rows[row].bgColor = 'D9DEE4'; //Cor quando o mouse esta Over
		break;
		
		case 'out':
		table.rows[row].bgColor = 'ffffff'; // Cor quando o mouse Sai
		break;
		
		case 'click':
		table.rows[row].bgColor = 'ff0000'; // Cor quando se clica
		break;
	}
}

function alerta()
{
	if( document.forms[0].elements[0].value == "" )
	{
		window.alert('Preencha o campo "senha" e tente novamente');
		return false;
	}
	
	return true;
}
	
	
		
