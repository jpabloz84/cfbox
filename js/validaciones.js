function es_numero(valor){ //verifica q sea numero con coma (valor flotante)
     if(/^\d+\.?\d*$/.test(valor)){
     return true;    
     }else{
     return false;
}
}

function es_alfanumerico(valor){ //verifica q sea numero con coma (valor flotante)
     if(/^[A-Za-z0-9_-]*$/.test(valor)){
     return true;    
     }else{
     return false;
     }

}


function es_entero(valor){ //verifica q sea numero con coma (valor flotante)
     if(/^[0-9]*$/.test(valor)){
     return true;    
     }else{
     return false;
     }

}// es numero

function es_email(valor)
{
var filter=/^[A-Za-z][A-Za-z0-9_.-]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
if (valor.length == 0 ) return true;
if (filter.test(valor))
return true;
else
return false;
}


function es_fecha(valor) //validar formato aaaa-mm-dd
{
	re=/^[0-9][0-9][0-9][0-9]\-[0-9][0-9]\-[0-9][0-9]$/
	    
	if(!re.exec(valor))
	{
	     return false;
	}
	else
	{     return true;
	}

}