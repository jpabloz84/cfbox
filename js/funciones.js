


function mostrar_ocultar_tab()
{
var tipo_comprobante=parseInt($F('tipo_comprobante'))

	if(tipo_comprobante!=1)
	{

	$('seccion_comprobantes').setStyle({display:'block'});
	}else
	{
		$('seccion_comprobantes').setStyle({display:'none'});
	}
}


function consulta_estadisticas(){
    //alert("Atencion! \n\r\n\rEsta operacion puede tardar varios minutos dependiendo su conexión a internet y otros factores.")
	//consultar_listas();
	//consultar_ganancias();
	//consultar_topten();
	//consultar_VentasGastos();
	consultar_FactuacionGastos();
}

var stiloAnt
var consultando=false
var eleEvent=new Array()
function verDetalles(element,idVendedor,descVendedor,mes,anio){
var estaConEvento=false;
for (i=0;i<eleEvent.length;i++)
    {
    if(eleEvent[i]==element)
        {
            estaConEvento=true;
            break
        }
    }

    if(estaConEvento)
    {
        return
    }else
    {
        eleEvent.push(element)        
     }

	new Tagtip(element, '', {
		ajax: { url: 'estadisticas.php',
			method: 'post',
			parameters: { 'viene':'detalles', 'idVendedor':idVendedor, 'mes':mes, 'anio':anio  },
			onComplete: function(response) {
                            
                        }
		},
		ajaxRefresh: false,
                showTrigger: 'click',
                hideTrigger:'dblclick',
		style: 'styled3',
		title: "Comprobantes de "+descVendedor,
		align: 'bottomMiddle'
	});
        
}




var eleEventF=new Array()
function verDetalles_facturacion(element,idVendedor,descVendedor,mes,anio){
var estaConEvento=false;
for (i=0;i<eleEventF.length;i++)
    {
    if(eleEventF[i]==element)
        {
            estaConEvento=true;
            break
        }
    }

    if(estaConEvento)
    {
        return
    }else
    {
        eleEventF.push(element)        
     }

	new Tagtip(element, '', {
		ajax: { url: 'estadisticas.php',
			method: 'post',
			parameters: { 'viene':'detalles_facturacion', 'idVendedor':idVendedor, 'mes':mes, 'anio':anio  },
			onComplete: function(response) {
                            
                        }
		},
		ajaxRefresh: false,
                showTrigger: 'click',
                hideTrigger:'dblclick',
		style: 'styled3',
		title: "Comprobantes de "+descVendedor,
		align: 'bottomMiddle'
	});
        
}




function outTrStyle(el)
{
    $(el).className=stiloAnt
    
}
function overTrStyle(el)
{
stileAnt=$(el).className
$(el).className="hover"
}


function mostrarFactura(codfact)
{
    var id_ancla="#an_"+codfact
    jQuery(id_ancla).colorbox({
			href: "../print/nota_pedido.php?codfactura="+codfact+"&consultaEstadistica=ok" });
		jQuery(id_ancla).colorbox({
			width:"770px",
			height:"600px",
			close : "VOLVER",
			iframe:true,
			onOpen:function(){ },
			onComplete:function(){
				backup_scroll=parent.window.scrollY;
				parent.window.scrollTo(0,0);
			}, //cuando se abre */
			onCleanup:function(){

				parent.window.scrollTo(0,parseInt(backup_scroll));
				

			} //cuando se esta por cerrar */
			}
		);


}


function consultar_ganancias()
{
	var mes=$F('mes1');
	var anio=$F('anio1');
	

	if(isNaN(mes) || mes<1 || mes>12)
	{alert('mes invalido')
		return
	}


	if(isNaN(anio))
	{alert('Año invalido')
		return
	}

new Ajax.Request('estadisticas.php?obj=cons_ganancias',{    
		 	method:'get',
		 	parameters :{mes:mes,anio:anio},		
			onSuccess: function(transport){

		        $('divGanancias').update(transport.responseText);		        	
		    	
		    },
			onFailure: function(){alert("error al consultar");}
			});

}


function verestadisticafina(mes,anio){

	/*var mes=$F('mes1');
	var anio=$F('anio1');*/
	if(!($('inp_fina').checked))
	{
		return
	}
	if(isNaN(mes) || mes<1 || mes>12)
	{alert('mes invalido')
		return
	}


	if(isNaN(anio))
	{alert('Año invalido')
		return
	}

	new Ajax.Request('estadisticas.php?obj=cons_ventasGatos',{    
			method:'get',
			asynchronous:true,
			onLoading:$('divVentasGatos').update("<div id='accion'> <img src='../img/loading.gif' alt='Cargando...' /> Recopilando Información... Aguarde.</div>"),
			parameters :{mes:mes,anio:anio},		
			onSuccess: function(transport){	                
	       	 	$('divVentasGatos').update(transport.responseText);	    	
	    	},
			onFailure: function(){
	            alert("error al consultar");
	         }
		});
}

function consultar_FactuacionGastos()
{
	var mes=$F('mes1');
	var anio=$F('anio1');
	$('divVentasGatos').update("");
	if(isNaN(mes) || mes<1 || mes>12)
	{alert('mes invalido')
		return
	}


	if(isNaN(anio))
	{alert('Año invalido')
		return
	}

	new Ajax.Request('estadisticas.php?obj=cons_FactuacionGastos',{    
			method:'get',
			asynchronous:true,
			onLoading:$('divFactuacionGastos').update("<div id='accion'> <img src='../img/loading.gif' alt='Cargando...' /> Recopilando Información... Aguarde.</div>"),
			parameters :{mes:mes,anio:anio},		
			onSuccess: function(transport){	                
	       	 	$('divFactuacionGastos').update(transport.responseText);	    	
	    	},
			onFailure: function(){
	            alert("error al consultar");
	         }
		});
}





function consultar_listas()
{
	var mes=$F('mes1');
	var anio=$F('anio1');
	

	if(isNaN(mes) || mes<1 || mes>12)
	{alert('mes invalido')
		return
	}


	if(isNaN(anio))
	{alert('Año invalido')
		return
	}

new Ajax.Request('estadisticas.php?obj=cons_listas',{    
		 	method:'get',
		 	parameters :{mes:mes,anio:anio},		
			onSuccess: function(transport){

		        $('divRecaudacion').update(transport.responseText);		        	
		    	
		    },
			onFailure: function(){alert("error al consultar");}
			});

}

function consultar_topten()
{

	var mes=$F('mes1');
	
	if(isNaN(mes) || mes<1 || mes>12)
	{alert('mes invalido')
		return
	}

var anio=parseInt($F('anio1'));
	if(isNaN(anio))
	{alert('Año invalido')
		return
	}
new Ajax.Request('estadisticas.php?obj=cons_topten',{    
		 	method:'get',
		 	parameters :{mes:mes,anio:anio},		
			onSuccess: function(transport){

		        $('divTopten').update(transport.responseText);		        	
		       // $('mes1').focus();
		        $('mes1').select();		    	
		    },
			onFailure: function(){alert("error al consultar");}
			});
}




function imprimir_etiquetas()
{
var url="articulo.php?obj=imprimir_etiquetas";
var impresora=$F('btnimp');
var copias=$F('txtCopias');
var id_articulo=$F('idart');

var cop=parseInt(copias);


if(isNaN(cop))
{
alert(" La cantidad de copias a imprimir es invalida");
	return; 
}

if(id_articulo=="")
{
	alert(" Parece que el sistema no encuentra un codigo para imprimir")
	return;
}

	new Ajax.Request(url,{    
		 	method:'get',
		 	parameters :{id_articulo:id_articulo,copias:copias,impresora:impresora},		
			onSuccess: function(transport){

		        if(transport.responseText=="ok"){
		        	
		        	alert("Datos enviados a la impresora");

		    	}else
		    	{
		    		alert(transport.responseText);
		    	}
		    	
		    },
			onFailure: function(){alert("error al borrar");}
			});

}
function verificar_estado_webservice()
{
alert(" Este proceso verifica el estado del Web service afip, como tambien, generara el Ticket de acceso para iniciar el servicio vigente por 12 horas");
new Ajax.Request("iframe_reconfirmar.php?obj=verificar_webservice" ,
		 {method: 'get',
			 parameters :{},
			 onSuccess :function(transport)
			 {	
			 respuesta=transport.responseText;
			
				 alert(respuesta);
			},
			onFailure : function()
			{alert("Error al consultar...");
			 }
		 }
		 );



}

function sincronizar_webservice()
{
alert(" Este proceso verifica el estado del Web service afip, como tambien, generara el Ticket de acceso para iniciar el servicio vigente por 12 horas\n . Tambien sincronizara datos con el servidor de flash");

new Ajax.Request("iframe_reconfirmar.php?obj=resetear_estado_webservice" ,
		 {method: 'get',
			 parameters :{},
			 onSuccess :function(transport)
			 {	
			 respuesta=transport.responseText;
			
				 alert(respuesta);
			},
			onFailure : function()
			{alert("Error al consultar...");
			 }
		 }
		 );



}

function reconfirmar()
{
	var codfactura=parseInt($F('codfacturaseleccionada'));

	var tipo_comprobante=parseInt($F('tipo_comprobante'));
	
	var fecha_comp=$F('txt_fecha_comp');
	var mensaje_envio=' Usted esta a punto de declarar una factura en la afip con la fecha de hoy. Desea continuar?'

	if(tipo_comprobante=='2' || tipo_comprobante=='3')
	{
        var importe=$F('importe')
	generar_nota_credito(codfactura,tipo_comprobante,importe,fecha_comp)
        return

	}


	if(!esCadenaEntero(String(codfactura)))
	{
		alert('Parece que no ha ingresado una factura a reconfirmar. Por favor, ingrese el codigo de factura a enviar al afip');
		return
	}else
	{

new Ajax.Request("iframe_reconfirmar.php?obj=buscar_si_existe_comp" ,
		 {method: 'get', async:false,
			 parameters :
			 {codfactura:codfactura},
			 onSuccess :function(transport)
			 {	
			 respuesta=transport.responseText;
				 if(respuesta!="OK")
				 {
				 alert("El comprobante que intenta enviar a afip, ya fue enviado y tiene su Codigo de Autorizacion Electronico. Verifique esto por favor");
				 

				 }else
				 {
				 	var res=confirm(mensaje_envio);
					if(res)
					{
				new Ajax.Request("iframe_reconfirmar.php?obj=reconfirmar" ,
						 {method: 'get',
							 parameters :
							 {codfactura:codfactura,importe:0,fecha_comp:fecha_comp,tipo_comprobante:tipo_comprobante},
							 onSuccess :function(transport)
							 {	
							 respuesta=transport.responseText;
							var resp=new Array() ;
								 resp=respuesta.split(";");
								 if(resp[0]=="OK")
								 {
								 	urlnuevo='../print/'+String(resp[1]);	
									printWin=window.open(urlnuevo, '_blank', 'scrollbars=yes,width=700,height=750,top=100,left=200');	
									printWin.print();

								 }else
								 {
								 	alert(respuesta+' . No se hicieron los cambios.');
								 }
							return;

							},
							onFailure : function()
							{alert("Error al consultar...");
							 }
						 }
						 );
					}//fin del IF res

				 }//fin del else que entra porq la respuesta fue OK





			

			},
			onFailure : function()
			{alert("Error al consultar...");
			 }
		 }
		 );

	}//fin del else donde verifica si se envio la factura o no


	
}

function generar_nota_credito(codfactura,tipo_comprobante,importe,fecha_comp)
{
    
            if(!esCadenaReal(String(importe)))
            {
                    alert(" Importe de comprobante invalido.");
                    return;
            }

            if(!es_fecha(fecha_comp) ||  fecha_comp=='')
            {
                alert(" Fecha de comprobante invalida. Respete el formato dd-mm-yyyy");
                return;
            }

            if(!esCadenaEntero(String(codfactura)))
            {
             alert("El nro de comprobante para generarle una nota de credito es invalido")
             return
            }
		mensaje_envio=' Usted esta a punto de declarar una nota de credito en afip. Desea continuar?'
                if(confirm(mensaje_envio))
                    {

                      new Ajax.Request("iframe_reconfirmar.php?obj=reconfirmar" ,
						 {method: 'get',
							 parameters :
							 {codfactura:codfactura,importe:importe,fecha_comp:fecha_comp,tipo_comprobante:tipo_comprobante},
							 onSuccess :function(transport)
							 {
							 respuesta=transport.responseText;
							var resp=new Array() ;
								 resp=respuesta.split(";");
								 if(resp[0]=="OK")
								 {
								 	urlnuevo='../print/'+String(resp[1]);
									printWin=window.open(urlnuevo, '_blank', 'scrollbars=yes,width=700,height=750,top=100,left=200');
									printWin.print();

								 }else
								 {
								 	alert(respuesta+' . No se hicieron los cambios.');
								 }
							return;

							},
							onFailure : function()
							{alert("Error al consultar...");
							 }
						 }
						 );

                    }
    
}



function Buscar_factura(){

var codfactura=parseInt($F('codfactura'));

	if(isNaN(codfactura)){
		alert('Codigo invalido de factura');
		return
	}

		
	var url="../print/nota_pedido.php?codfactura="+codfactura+"&print=ok";
	$('codfacturaseleccionada').value=codfactura;
	$('frame_datos').src=url;
}


function calcular(id_valor,valor)
{
	
	var cantidad=parseFloat($('cant_'+String(id_valor)).value);	
	if(isNaN(cantidad)){
		cantidad=0;
	}
	var resultado=valor*cantidad;	
	
	$('tot_'+id_valor).update(String(resultado.toFixed(2)));
	recontar();
}

function recontar(){

	 var cont=$F('contador_reg');
	var res=0;
	for(i=0;i<cont;i++)
	{
		 res+=parseFloat($("tot_"+String(i)).innerHTML)

	}
	var total_calculadora=res.toFixed(2);
	var imprime=total_calculadora;

	$('total').update(String(number_format(total_calculadora, 2, ',', '.')));
	$("total_calculadora").value=total_calculadora;

	var saldo_actual=parseFloat($("saldo_actual_actualizado").value);
	var dif = saldo_actual-total_calculadora;
	$('dif').update(String(number_format(dif.toFixed(2), 2, ',', '.')));
}


function number_format (number, decimals, dec_point, thousands_sep) {

var n = number, prec = decimals;

var toFixedFix = function (n,prec) {
    var k = Math.pow(10,prec);
    return (Math.round(n*k)/k).toString();
};

n = !isFinite(+n) ? 0 : +n;
prec = !isFinite(+prec) ? 0 : Math.abs(prec);
var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;

var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;

var abs = toFixedFix(Math.abs(n), prec);
var _, i;

if (abs >= 1000) {
    _ = abs.split(/\D/);
    i = _[0].length % 3 || 3;

    _[0] = s.slice(0,i + (n < 0)) +
          _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
    s = _.join(dec);
} else {
    s = s.replace('.', dec);
}

var decPos = s.indexOf(dec);
if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
    s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
}
else if (prec >= 1 && decPos === -1) {
    s += dec+new Array(prec).join(0)+'0';
}
return s;}




/*
function set_informes_varios()
{
	debugger

var tipo=parseInt($F('tipo_proceso'));

var parametros;
if(isNaN(tipo))
{
alert('No ha seleccionado un informe');
		return;	
}
var url="iframe_procesos_varios.php?obj=procesar&nroproc="+tipo;

if(tipo==1)
{

	var id_proveedor=parseInt($F('id_proveedor'));

	if(isNaN(id_proveedor))
	{
		alert('No ha seleccionado un proveedor');
		return;
	}else
	{
		parametros={id_proveedor:id_proveedor};
	}

}



if(tipo==2)
{

	var id_localidad=$F('id_localidad');
        var id_provincia=$F('id_provincia');
	var id_vendedor=$F('id_vendedor')
        var tipo_lista=$F('tipo_lista')
parametros={id_localidad:id_localidad,id_vendedor:id_vendedor,tipo_lista:tipo_lista,id_provincia:id_provincia}
	

}

//$('frame_datos').src=url;

new Ajax.Request(url ,
		 {method: 'get',
			 parameters :parametros,
			 evalScripts: true,
			  onLoading:$('conten').update("<div id='accion'> <img src='../img/loading.gif' alt='Cargando...' /> Espere por favor...</div>"),
			 onSuccess :function(transport)
			 {	
			 	$('conten').update("")
			 pdf=transport.responseText;

			 window.open("mostrar_informes.php?viene=mostrarINF&ar="+pdf, '_blank', 'scrollbars=yes,width=710,height=750,top=0,left=100');	
			},
			onFailure : function()
			{alert("Error al consultar...");
			 }
		 }
		 );




}//fin de la funcion informes varios


*/


function mostrarfiltros()
{

	var tipo=$F('tipo_informe')
	if(tipo=='4' || tipo=='9')
	{

		$('seccion_comparativos').setStyle({display:'block'});
	}else
	{
		$('seccion_comparativos').setStyle({display:'none'});
	}


	if(tipo=='5')
	{

		$('seccion_informes_ventas').setStyle({display:'block'});
	}else
	{
		$('seccion_informes_ventas').setStyle({display:'none'});
	}
        if(tipo=='7' || tipo=='8')
	{

		$('seccion_informe_facturas_ctacte').setStyle({display:'block'});
	}else
	{
		$('seccion_informe_facturas_ctacte').setStyle({display:'none'});
	}


        if(tipo=='10' )
	{

		$('seccion_informe_ventas_por_vendedor').setStyle({display:'block'});
	}else
	{
		$('seccion_informe_ventas_por_vendedor').setStyle({display:'none'});
	}



}


function set_informes()
{
var mostrar="mostrarINF"; //German
var tipo=$F('tipo_informe')
var fecha_dsd=$F('txt_fecha_dsd');
var codfactura=parseInt($('codfactura').value);

	if(!es_fecha(fecha_dsd))
	{
		if(!((tipo=='4' || tipo=='10' || tipo=='9') && codfactura!=""))
		{
		alert('Fecha  desde invalida');
		return
		}
                if(tipo=='7')
              {
                  alert('Fecha  desde invalida');
              }
	}

	var fecha_hst=$F('txt_fecha_hst');

	if(!es_fecha(fecha_hst))
	{if(!((tipo=='4' || tipo=='10'  || tipo=='9') && codfactura!=""))
		{
		alert('Fecha  hasta invalida');
		return
		}
           if(tipo=='7')
              {
                  alert('Fecha  hasta invalida');
                  return
              }
	}

	//reportes/ventas_por_vendedores.php
	

if(tipo=='11' || tipo=='12') {	
	var url="iframe_informes.php?obj=reporte_citi_ventas&nrorpt="+tipo;
	mostrar="mostrarciti"; //German
 } 

	else {var url="iframe_informes.php?obj=exportar_reporte&nrorpt="+tipo;}

	//var url="iframe_informes.php?obj=exportar_reporte&nrorpt="+tipo;
 /*switch(tipo){
      case "1":url='';
      break;
      case "2":url='reportes/VENTAS_POR_PROVINCIASsmry.php';
        
        break;
      default:''
  }*/
    var fd=''
    var fdsd=''
if(fecha_dsd!="")
    {
    fd=fecha_dsd.split("-");
    fdsd=fd[2]+"-"+fd[1]+"-"+fd[0];
    }

var fh=''
var fhst=''
if(fecha_hst!='')
    {
 fh=fecha_hst.split("-");
 fhst=fh[2]+"-"+fh[1]+"-"+fh[0];
    }


//var url='ver_ventas_vendedor.php?viene=consultar&fechadsd='+fecha_dsd+'&fechahst='+fecha_hst;
if(tipo=='7' || tipo=='8')
{
    var id_vendedor=$F('id_vendedor2')
    if(id_vendedor!='')
        {
            url+="&id_vendedor="+String(id_vendedor);
        }

}




if(tipo=='4' || tipo=='9')
{
	

	
	var id_cliente="";
	
if(!isNaN(codfactura))
{
	url+="&codfactura="+String(codfactura);
}

	if($F('id_cliente')==undefined ||  $F('id_cliente')=='')
	{
		id_cliente='todos';
	}else
	{
		id_cliente=$F('id_cliente');
	}
	url+='&id_cliente='+id_cliente;
}


if(tipo=="5")
{

	alert("Atencion, este informe tambien contiene los comprobantes erroneos y/o rechazados")
	if($('cksolofacturas').checked)
		{
		url+='&f=1'	;
		}
		else
		{
			url+='&f=0'	;
		}
}

if(tipo=="10")
    {
        var mes_periodo=parseInt($F('mes_periodo'));
        var anio_periodo=parseInt($F('anio_periodo'));
        if(isNaN(mes_periodo) || isNaN(anio_periodo) || $F('mes_periodo')=="" || $F('anio_periodo')=="" || !(mes_periodo>0 && mes_periodo<13))
            {alert("El periodo ingresado es invalido. Por favor ingrese mes y año")
                return;

            }
        url+='&mes_periodo='+mes_periodo+'&anio_periodo='+anio_periodo;
    }

url+='&fedsd='+fdsd+'&fehst='+fhst;

//$('frame_datos').src=url;
var formatoExcel=($('formatoExcel').checked)?1:0
new Ajax.Request(url ,
		 {method: 'post',
			 parameters :{formatoExcel:formatoExcel},
			  onLoading:$('conten').update("<div id='accion'> <img src='../img/loading.gif' alt=' Espere por favor...' /> Generando la información...</div>"),
			 onSuccess :function(transport)
			 {
			 	$('conten').update("")
			 pdf=transport.responseText;

			 window.open("mostrar_informes.php?viene="+mostrar+"&ar="+pdf, '_blank', 'scrollbars=yes,width=710,height=750,top=0,left=100');	//German
			},
			onFailure : function()
			{alert("Error al consultar...");
			 }
		 }
		 );




}





function validar_porcentaje(e,p) /*administracion\articulo.php*/
{

pr_cpra=parseFloat( document.form_jp.elements['PME_data_costo'].value)

	if(p==1)
	{porcentaje=parseFloat( $('porc1').value);
		
		if(!(isNaN(pr_cpra) || isNaN(porcentaje)) )// si es un numero
		{
			prp1=parseFloat(document.form_jp.elements['PME_data_precio'].value);
			if(!isNaN(prp1))
			{
				var res=((porcentaje/100)+1)*pr_cpra;
				document.form_jp.elements['PME_data_precio'].value=decimales(String(res),2);
				$("porc1").setStyle({backgroundColor: '#ADFFB9'});
			}else
			{
				document.form_jp.elements['PME_data_precio'].value=0;
				$("porc1").setStyle({backgroundColor: '#ADFFB9'});
			}
		}else
		{
			document.form_jp.elements['PME_data_precio'].value=0;
			$("porc1").setStyle({backgroundColor: '#ADFFB9'});
		}//fin de la actualizacion precio 1

	}//para precio 1
	if(p==2)
	{porcentaje=parseFloat( $('porc2').value);
		
		if(!(isNaN(pr_cpra) || isNaN(porcentaje)) )// si es un numero
		{
			prp1=parseFloat(document.form_jp.elements['PME_data_precio2'].value);
			if(!isNaN(prp1))
			{var res=((porcentaje/100)+1)*pr_cpra;
				document.form_jp.elements['PME_data_precio2'].value=decimales(String(res),2);
				$("porc2").setStyle({backgroundColor: '#ADFFB9'});
			}else
			{
				document.form_jp.elements['PME_data_precio2'].value=0;
				$("porc2").setStyle({backgroundColor: '#ADFFB9'});
			}
		}else
		{
			document.form_jp.elements['PME_data_precio2'].value=0;
			$("porc2").setStyle({backgroundColor: '#ADFFB9'});
		}//fin de la actualizacion precio 2

	}//para precio 2
	if(p==3)
	{porcentaje=parseFloat( $('porc3').value);
		
		if(!(isNaN(pr_cpra) || isNaN(porcentaje)) )// si es un numero
		{
			prp1=parseFloat(document.form_jp.elements['PME_data_precio3'].value);
			if(!isNaN(prp1))
			{var res=((porcentaje/100)+1)*pr_cpra;
				document.form_jp.elements['PME_data_precio3'].value=decimales(String(res),2);
				$("porc3").setStyle({backgroundColor: '#ADFFB9'});
			}else
			{
				document.form_jp.elements['PME_data_precio3'].value=0;
				$("porc3").setStyle({backgroundColor: '#ADFFB9'});
			}
		}else
		{
			document.form_jp.elements['PME_data_precio3'].value=0;
			$("porc3").setStyle({backgroundColor: '#ADFFB9'});
		}//fin de la actualizacion precio 2

	}//para precio 2
	
	
}//fin de la funcion validar porcentaje

function buscar_articulo(id_patron)
{
patron=$(id_patron).value;
new Ajax.Request("../includes/paginador.php" ,
		 {method: 'get',
			 parameters :
			 {patron:patron},
			 onSuccess :function(transport)
			 {	
			 res=transport.responseText;
			$('contenedor').update(res);	 
				 
			},
			onFailure : function()
			{alert("Error al consultar...");
			 }
		 }
		 );


}
function Pagina_Cambia(que){
var evt = document.createEvent('MouseEvent');
evt.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
var mi_link = document.getElementById(que);
mi_link.dispatchEvent(evt);
}


function Pagina(nropagina){

 //donde se mostrará los registros

 parametros="";
 if(($('buscando').value != -1) && ($('buscando').value!=0)) {
	parametros="../includes/paginador.php?pag="+nropagina+"&patron="+patron; 
}
 else 
 {
	 if(($('categoria').value != -1) && ($('categoria').value!=0)) // si tiene que paginar por una categoria q se esta mostrando
	{parametros="../includes/paginador.php?pag="+nropagina+"&obj="+$('categoria').value;}
	else{
	 	parametros="../includes/paginador.php?pag="+nropagina; //es el paginador de todos los articulos
	}

}
 
//var send=$(id_form).serialize()+"&"+accion;//+agrega;
//alert(send);
 		

 new Ajax.Request(page_act,{
  method: 'post',
  parameters : parametros,
  evalScripts: true,
  onLoading: function(){$('contenedor').update('<img src="anim.gif">');},
  onSuccess : function(transport){   
 
   $('contenedor').update(transport.responseText);
  
   cambios_luego_del_ajax();

  },
  onFailure : function(){$('contenedor').update('No se pudo cargar...')}
 });
 
 
}

//seccion dedicada a la carga de promociones en elaborar_pedido.php>anexar_promociones
var filas_promo=0;
var suma_promo=0;
var promos_encestadas = new Array() ;
function anexar_promocion()
{
obj_tabla=document.getElementById('promociones');//tabla general
obj_prom=document.getElementById('id_prom');//es donde se encuentra el id de la promo 
obj_cant=document.getElementById('cantidad_promo');//input donde se ingresa la cantidad
obj_precio=document.getElementById('precio_promo');//span donde se estampo el precio unitario


obj_descrip=document.getElementById('descrip_prom');//span donde se estampo la descripcion
cant=obj_cant.value;
id_prom=obj_prom.value;
if (id_prom!="" && es_entero(cant)){ 
 descripcion=obj_descrip.innerHTML;
 precio=parseFloat(obj_precio.innerHTML);
 for  (i=0;i<promos_encestadas.length;i++)
 {
	if(promos_encestadas[i]==id_prom)//si ya existe la promocion en la orden de pedido q NO se encesta
	{
	alert("La promocion ya se encuentra encestada en la orden de pedido. No se puede ingresar nuevamente.");
	return false;
	}
	
}
promos_encestadas.push(id_prom);
total=cant*precio;
filas_promo+=1;
obj_tabla.innerHTML+='<tr class="pme-row-1"><td class="pme-key-1" ><input name="checkp'+filas_promo+'" type="checkbox" checked="checked"  id="checkp'+filas_promo+'" onchange="actualizar_total_parcial_promo(\'total_parcial_promocion\',\'totalp\',\'checkp\')"/>&nbsp;&nbsp;Promoci&oacute;n&nbsp;&nbsp;'+filas_promo+' :'+ descripcion+'</td><td class="pme-key-1">cantidad:'+cant+'<input type="hidden" id="cantp'+filas_promo+'" name="cantp'+filas_promo+'" value="'+cant+'" /><input type="hidden" id="promo'+filas_promo+'" name="promo'+filas_promo+'" value="'+id_prom+'" /></td></tr><tr class="pme-row-1"><td class="pme-value-1">Precio Unitario:$'+precio+'&nbsp;&nbsp;<td class="pme-value-1">total :$<span id="totalp'+filas_promo+'"  >'+total+'</span></td></tr>';	

 
actualizar_total_parcial_promo('total_parcial_promocion','totalp','checkp');
 }
 else
 {
	 alert('Faltan datos o hay datos invalidos.');
 }
}


//los prefijo son los nombre q se le dan a cada elemento y que luego sigue el dato fila concatenado para formar el id del elemento
function actualizar_total_parcial_promo(id_span,prefijo_total,prefijo_ck)
{total_parcial=0;
i=1;
	while(i<=filas_promo)
	{
	id_ck=prefijo_ck+i;
	id_tot=prefijo_total+i;
		if(document.getElementById(id_ck).checked)
		{t=parseFloat(document.getElementById(id_tot).innerHTML);

		total_parcial+=t;
			
		}
	i++;	
	}//fin del while
	document.getElementById(id_span).innerHTML=total_parcial;
	return;
}



function consultar_localidades()//consulta las localidades y las estampa en el name_select
{
obj_pro=document.getElementById('provincia');	
obj_loc=document.PME_sys_form.elements['PME_data_id_localidad'];//recupero el select
//alert(obj_loc.value);
id_pro=obj_pro.value;
new Ajax.Request("../includes/consultas.php?obj=consultar_localidades" ,
		 {method: 'get',
			 parameters :
			 {id_pro:id_pro},
			 onSuccess :function(transport)
			 {	
			 res=transport.responseText;
			$('loca').update(res);	 
				 
			},
			onFailure : function()
			{alert("Error al consultar...");
			 }
		 }
		 );
	


}



var filas_promo=0;
//var suma=0;
var art_promo=new Array();

function set_variable(nombre_var,valor)//sirve para inicializar variable globales del javasript
{
	if(nombre_var=='filas_promo')
	{
	filas_promo=valor;

	}
	if(nombre_var=='filas')
	{
	filas=valor;
	}
	if(nombre_var=='art_promo')
	{
		
	art_promo=valor;
	}

}

function validar_promo_change(id_promo){//verifica que haya seleccionado al menos un check
obj_desc=document.getElementById('descripcion');
obj_cant=document.getElementById('cantidad1');
obj_precio=document.getElementById('precio');
if(validar_promo())
{
new Ajax.Request("../includes/consultas.php?obj=modificar_promocion" ,
			 {method: 'get',
				 parameters :
				 {descripcion:obj_desc.value,cantidad:obj_cant.value,precio:obj_precio.value,id:id_promo},
				 onSuccess :function(transport)
				 {promo=transport.responseText;
					 if(promo!="error")
					 {
						enviar_articulos_promo(promo,filas_promo);						
						
					alert("La promoci\u00f3n se modific\u00f3 correctamente");	
					paginas('promocion_2.php','contenedor','');					
					 }
					 else
					 {
					alert("No se pudo modificar la promocion.");
					}//fin del if			

					$('formulario').reset();//reseteo el formulario
					filas_promo=0;//seteo variable global
					art_promo=new Array();



				},
				onFailure : function()
				{alert("Error al consultar...");
				 }
			 }
			 );
}

}//fin de validar promo add




function validar_promo_add(){//verifica que haya seleccionado al menos un check
obj_desc=document.getElementById('descripcion');
obj_cant=document.getElementById('cantidad1');
obj_precio=document.getElementById('precio');
if(validar_promo())
{
new Ajax.Request("../includes/consultas.php?obj=generar_promocion" ,
			 {method: 'get',
				 parameters :
				 {descripcion:obj_desc.value,cantidad:obj_cant.value,precio:obj_precio.value},
				 onSuccess :function(transport)
				 {promo=transport.responseText;
					 if(promo!="error")
					 {
						enviar_articulos_promo(promo,filas_promo);						
						
					alert("La promoci\u00f3n se gener\u00f3 correctamente");	
					paginas('promocion_2.php','contenedor','');					
					 }
					 else
					 {
					alert("No se pudo generar la promocion.");
					}//fin del if			

					$('formulario').reset();//reseteo el formulario
					filas_promo=0;//seteo variable global
					art_promo=new Array();



				},
				onFailure : function()
				{alert("Error al consultar...");
				 }
			 }
			 );
}

}//fin de validar promo add

function validar_promo()//verifica que haya seleccionado al menos un check
{ 
hay_articulos=almenos_unck("check",filas_promo);

if(hay_articulos== false)//si no hay articulos Y promociones
{
alert('Ups!!! Parece que no se ha seleccionado o anexado ning\u00fan articulo a la promoci\u00f3n.');
return false;
}

obj_desc=document.getElementById('descripcion');
obj_cant=document.getElementById('cantidad1');
obj_precio=document.getElementById('precio');

if(obj_desc.value=="")
{
alert("No ha ingresado la descripci\u00f3n de la promoci\u00f3n.");
obj_desc.focus();
return false;
}

if(es_entero(obj_cant.value)==false)
{
alert("hay datos invalidos.");
obj_cant.focus();
return false;
}

if(es_numero(obj_precio.value)==false)
{
alert("hay datos invalidos.");
obj_precio.focus();
return false;
}

return true;
}//fin de validar pedido
function enviar_articulos_promo(orden,cont)//anexa los articulos seleccionado en elaborar_orden.php a la orden antes generada
{
for (i=1;i<=cont;i++)
{
	id_ck="check"+i;	
	if(document.getElementById(id_ck).checked)
	{
	art="art"+i;
	id_cant="cant"+i;
	id_art=document.getElementById(art).value;
	cant=document.getElementById(id_cant).value;
	new Ajax.Request("../includes/consultas.php?obj=anexar_articulo_promo" ,
			 {method: 'get',
				 parameters :
				 {id_articulo:id_art,id_promo:promo,cantidad:cant},
				 onSuccess :function(transport)
				 {	
					 if(transport.responseText=="error")
					 {
						alert("Problemas al anexar los articulos a la promocion "+orden);
					 }
					 
				},
				onFailure : function()
				{alert("Error al consultar...");
				 }
			 }
			 );
	
	}//fin del if de los articulos selecionados
	
}//fin del for

}//fin de la funcion enviar_articulos





//tabla:id de tabla donde se insertaran los datos 
//id_prod:input hidden del producto seleccionado;
//id_cant:input de la cantidad del producto seleccionado


function anexar_articulo_apromo()
{
obj_tabla=document.getElementById('articulos');//tabla general
obj_prod=document.getElementById('id_prod');//es donde se encuentra el id del producto seleccionado
obj_cant=document.getElementById('cantidad');//input donde se ingresa la cantidad
//obj_precio=document.getElementById('precio_unit');//span donde se estampo el precio unitario
obj_descrip=document.getElementById('descripcion2');//span donde se estampo la descripcion
cant=obj_cant.value;
id_art=obj_prod.value;
if (id_art!="" && es_entero(cant)){ 
// if (cant > 1) desc=desc+" x "+cant;
// preciototal=pre * cant; 
 descripcion=obj_descrip.innerHTML;
 //precio=parseFloat(obj_precio.innerHTML);
for  (i=0;i<art_promo.length;i++)
 {
	if(art_promo[i]==id_art)//si ya existe el articulo en la orden de pedido q NO se enceste
	{
	alert("El articulo ya se encuentra encestado en promocion. No se puede ingresar nuevamente.");
	return false;
	}
	
}
art_promo.push(id_art);
  

filas_promo+=1;
obj_tabla.innerHTML+='<tr class="pme-row-0"><td class="pme-key-0"><input name="check'+filas_promo+'" type="checkbox" checked="checked"  id="check'+filas_promo+'" />Producto&nbsp;&nbsp;'+filas_promo+' :'+ descripcion+'</td><td class="pme-value-0">Cantidad:<input type="text" size="3" id="cant'+filas_promo+'" name="cant'+filas_promo+'" value="'+cant+'" class="pme-input-0" /><input type="hidden" id="art'+filas_promo+'" name="art'+filas_promo+'" value="'+id_art+'" /></td></tr>';	


 
//actualizar_total_parcial('total_parcial_articulos','total','check');
 }
 else
 {
	 alert('Faltan datos o hay datos invalidos.');
 }
}









function validar_pedido_add()//verifica que haya seleccionado al menos un check
{ 
hay_articulos=almenos_unck("check",filas);
hay_promociones=almenos_unck("checkp",filas_promo);;
if(hay_articulos== false && hay_promociones== false)//si no hay articulos Y promociones
{
alert('Ups!!! Parece que no se ha seleccionado o anexado ning\u00fan articulo/promoci\u00f3n a la orden de pedido.');
return false;
}

id_cli=document.getElementById('cliente').value;
vendedor=document.getElementById('vendedor').value;
id_trans=document.getElementById('transporte').value;
priori=document.getElementById('prioridad').value;
forma_pago=document.getElementById('forma_pago').value;
obs=document.getElementById('observaciones').value;

new Ajax.Request("../includes/consultas.php?obj=generar_orden" ,
			 {method: 'get',
				 parameters :
				 {id_vendedor:vendedor,id_cliente:id_cli,id_transporte:id_trans,forma_pago:forma_pago,prioridad:priori,observacion:obs},
				 onSuccess :function(transport)
				 {orden=transport.responseText;
					 if(orden!="error")
					 {
						if(hay_articulos)
						{
						enviar_articulos(orden,filas);
						}
						if(hay_promociones)
						{
						enviar_promociones(orden,filas_promo);
						}
					alert("El pedido se gener\u00f3 correctamente con el n\u00famero de orden: "+orden);	
					paginas('pedido.php','contenedor','');
					
					 }
					 else
					 {
					alert("No se pudo generar la orden de pedido.");
					}//fin del if			

					$('formulario').reset();//reseteo el formulario
					filas=0;
					suma=0;
					art_encestados = new Array() ;


				},
				onFailure : function()
				{alert("Error al consultar...");
				 }
			 }
			 );
}//fin de validar pedido
function enviar_articulos(orden,cont)//anexa los articulos seleccionado en elaborar_orden.php a la orden antes generada
{
for (i=1;i<=cont;i++)
{
	id_ck="check"+i;	
	if(document.getElementById(id_ck).checked)
	{
	art="art"+i;
	id_cant="cant"+i;
	id_art=document.getElementById(art).value;
	cant=document.getElementById(id_cant).value;
	new Ajax.Request("../includes/consultas.php?obj=anexar_articulo_orden" ,
			 {method: 'get',
				 parameters :
				 {id_articulo:id_art,id_orden:orden,cantidad:cant},
				 onSuccess :function(transport)
				 {	
					 if(transport.responseText=="error")
					 {
						alert("Problemas al anexar los articulos a la orden "+orden);
					 }
					 
				},
				onFailure : function()
				{alert("Error al consultar...");
				 }
			 }
			 );
	
	}//fin del if de los articulos selecionados
	
}//fin del for

}//fin de la funcion enviar_articulos

function enviar_promociones(orden,cont)//anexa las promociones seleccionadas en elaborar_orden.php a la orden antes generada
{
for (i=1;i<=cont;i++)
{
	id_ck="checkp"+i;	
	if(document.getElementById(id_ck).checked)
	{
	pro="promo"+i;
	id_cant="cantp"+i;
	id_pro=document.getElementById(pro).value;
	cant=document.getElementById(id_cant).value;
	new Ajax.Request("../includes/consultas.php?obj=anexar_promocion_orden" ,
			 {method: 'get',
				 parameters :
				 {id_promo:id_pro,id_orden:orden,cantidad:cant},
				 onSuccess :function(transport)
				 {	
					 if(transport.responseText=="error")
					 {
						alert("Problemas al anexar la promocion a la orden "+orden);
					 }
					 
				},
				onFailure : function()
				{alert("Error al consultar...");
				 }
			 }
			 );
	
	}//fin del if de los promociones
	
}//fin del for

}//fin de la funcion enviar_articulos






function obtener_categoria(id_cliente,id_span)//dado al input del cliente selecionado , obetngo la categoria y la estampo en el span
{
	cliente=document.getElementById(id_cliente).value;
	span=document.getElementById(id_span);
	new Ajax.Request("../includes/consultas.php?obj=obtener_categoria" ,
			 {method: 'get',
				 parameters :
				 {cliente:cliente},
				 onSuccess :function(transport)
				 {	
					 if(transport.responseText=="error")
					 {
						alert("Problemas al consultar categoria");
					 }
					 span.innerHTML=transport.responseText;
					 return;
				},
				onFailure : function()
				{alert("Error al consultar...");
				 }
			 }
			 );
	
}//fin de la funcion enviar_articulos





function almenos_unck(prefijo_ck,cont)//verifica que haya seleccionado al menos un check
{ 
	for (i=1;i<=cont;i++)
	{
	id_ck=prefijo_ck+i;	
	if(document.getElementById(id_ck).checked)
	{
	return true;
	}
	
	}//fin del for
return false;
}



var filas=0;
var suma=0;
//tabla:id de tabla donde se insertaran los datos 
//id_prod:input hidden del producto seleccionado;
//id_cant:input de la cantidad del producto seleccionado
var art_encestados = new Array() ;
function anexar_articulo(tabla,id_prod,id_cant)
{
obj_tabla=document.getElementById('articulos');//tabla general
obj_prod=document.getElementById('id_prod');//es donde se encuentra el id del producto seleccionado
obj_cant=document.getElementById('cantidad');//input donde se ingresa la cantidad
obj_precio=document.getElementById('precio_unit');//span donde se estampo el precio unitario


obj_descrip=document.getElementById('descripcion');//span donde se estampo la descripcion

cant=obj_cant.value;
id_art=obj_prod.value;
if (id_art!="" && es_entero(cant)){ 
// if (cant > 1) desc=desc+" x "+cant;
// preciototal=pre * cant; 
 descripcion=obj_descrip.innerHTML;
 precio=parseFloat(obj_precio.innerHTML);
 for  (i=0;i<art_encestados.length;i++)
 {
	if(art_encestados[i]==id_art)//si ya existe el articulo en la orden de pedido q NO se enceste
	{
	alert("El articulo ya se encuentra encestado en la orden de pedido. No se puede ingresar nuevamente.");
	return false;
	}
	
}
art_encestados.push(id_art);
  
 /*document.getElementById('ventas').innerHTML+='<div id="prod'+filas+'" name="prod'+filas+'" style="width:80%; position:relative; float:left;">'+desc+'</div><div id="pre'+filas+'"  style="width:15%; position:relative; float:left;">$'+preciototal+'</div> <div > <input name="check'+filas+'" type="checkbox" checked="checked"  id="check'+filas+'" /><input type="text" id="cant'+filas+'" name="cant'+filas+'" value="'+cant+'" size="3"/><input name="m_unit'+filas+'" type="hidden"  id="m_unit'+filas+'" value="'+pre+'" /><input name="p_prod'+filas+'" type="hidden"  id="p_prod'+filas+'" value="'+pre+'" /><input name="idprod'+filas+'" type="hidden"  id="idprod'+filas+'" value="'+id_prod+'" /><input name="nameprod'+filas+'" type="hidden"  id="nameprod'+filas+'" value="'+desc+'" /></div> ';
*/
  
    
  /*  suma+=preciototal;        
    document.getElementById('total').innerHTML='<div style="width:80%; position:relative; float:left;"><strong>Total</strong></div><div style="width:20%; position:relative; float:left;"><strong>$'+suma+'</strong></div>';*/
	
var total=cant*precio;


filas+=1;
obj_tabla.innerHTML+='<tr class="pme-row-1"><td class="pme-key-1" ><input name="check'+filas+'" type="checkbox" checked="checked"  id="check'+filas+'" onchange="actualizar_total_parcial(\'total_parcial_articulos\',\'total\',\'check\')"/>&nbsp;&nbsp;Producto&nbsp;&nbsp;'+filas+' :'+ descripcion+'</td><td class="pme-key-1">cantidad:'+cant+'<input type="hidden" id="cant'+filas+'" name="cant'+filas+'" value="'+cant+'" /><input type="hidden" id="art'+filas+'" name="art'+filas+'" value="'+id_art+'" /></td></tr><tr class="pme-row-1"><td class="pme-value-1">Precio Unitario: $'+precio+'&nbsp;&nbsp;<td class="pme-value-1">total :$<span id="total'+filas+'"  >'+total+'</span></td></tr>';	

 
actualizar_total_parcial('total_parcial_articulos','total','check');
 }
 else
 {
	 alert('Faltan datos o hay datos invalidos.');
 }
}


//los prefijo son los nombre q se le dan a cada elemento y que luego sigue el dato fila concatenado para formar el id del elemento
function actualizar_total_parcial(id_span,prefijo_total,prefijo_ck)
{total_parcial=0;
i=1;
	while(i<=filas)
	{
	id_ck=prefijo_ck+i;
	id_tot=prefijo_total+i;
		if(document.getElementById(id_ck).checked)
		{t=parseFloat(document.getElementById(id_tot).innerHTML);

		total_parcial+=t;
			
		}
	i++;	
	}//fin del while
	document.getElementById(id_span).innerHTML=total_parcial;
	return;
}





var tab_anterior= "PME_dhtml_tab0";

function mostrar_tab(tab_name,form)
{
formulario=document.getElementById(form);	
 document.getElementById(tab_anterior+"_down_label").className = "pme-tab";
 document.getElementById(tab_anterior+"_down_link").className = "pme-tab";
 document.getElementById(tab_name+"_down_label").className = "pme-tab-selected";
 document.getElementById(tab_name+"_down_link").className = "pme-tab-selected";
 document.getElementById(tab_anterior).style.display = "none";
 document.getElementById(tab_name).style.display = "block";
tab_anterior= tab_name;
// formulario.PME_sys_cur_tab.value = tab_name;
} 





var PME_js_cur_tab = "PME_dhtml_tab0";

function PME_js_show_tab(tab_name)
{
 document.getElementById(PME_js_cur_tab+"_down_label").className = "pme-tab";
 document.getElementById(PME_js_cur_tab+"_down_link").className = "pme-tab";
 document.getElementById(tab_name+"_down_label").className = "pme-tab-selected";
 document.getElementById(tab_name+"_down_link").className = "pme-tab-selected";
 document.getElementById(PME_js_cur_tab).style.display = "none";
 document.getElementById(tab_name).style.display = "block";
 PME_js_cur_tab = tab_name;
 document.PME_sys_form.PME_sys_cur_tab.value = tab_name;
} 

function es_alfanumerico(valor){ //verifica q sea numero con coma (valor flotante)
	if(/^[A-Za-z0-9_-]*$/.test(valor)){
	return true;	
	}else{
	return false;
	}

}// es numero

function es_numero(valor){ //verifica q sea numero con coma (valor flotante)
	if(/^\d+\.?\d*$/.test(valor)){
	return true;	
	}else{
	return false;
	}

}// es numero

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

/*********************validacion de cliente*****************************************/
function validar_cliente()
{
	//inputs=$('formulario').serialize();//+agrega;
	
//	datos=inputs.split("&"); 

	btn_cuenta=document.PME_sys_form.elements['PME_data_cuenta'];

	btn_apenom=document.PME_sys_form.elements['PME_data_apenom'];
	btn_direccion=document.PME_sys_form.elements['PME_data_direccion'];
	btn_cuilt=document.PME_sys_form.elements['PME_data_cuilt'];
	btn_email=document.PME_sys_form.elements['PME_data_email'];

		if(btn_cuenta.value=="")
		{
		alert("Debe estar cargado el numero de cuenta");
		btn_cuenta.focus();
		return;
		}

		if(btn_apenom.value=="")
		{
		alert("Complete Nombre y Apellido");
		btn_apenom.focus();
		return false;
		}

		if(btn_direccion.value=="")
		{
		alert("Cargue Direccion");
		btn_direccion.focus();
		return false;
		}

		if(btn_cuilt.value!="")
		{
			if(!es_entero(btn_cuilt.value))
				{
				alert("El cuit debe contener solo 11 simbolos numericos");
				btn_cuilt.focus();
				return false;
					
			}
		}



/*
		if(btn_email.value=="")
		{
		alert("Complete el dato por favor");
		btn_email.focus();
		return false;
		}else
		{if(!es_email(btn_email.value))
			{
			alert("El email tiene caracteres invalidos.");
			btn_email.focus();
			return false;
				
			}

		}
*/



return true;	

}


function valida_cliente_add()
{
if(validar_cliente())
{
carga_pantalla('PME_sys_saveadd=Grabar');
}
return;
}

function valida_cliente_change()
{
if(validar_cliente())
{

carga_pantalla('PME_sys_savechange=Grabar');
}
return;
}

/*********************validacion de vendedor*****************************************/
function validar_vendedor()
{

	btn_usuario=document.PME_sys_form.elements['PME_data_usuario'];
	btn_pass=document.PME_sys_form.elements['PME_data_contrasenia'];
	btn_apenom=document.PME_sys_form.elements['PME_data_apenom'];
	btn_dni=document.PME_sys_form.elements['PME_data_dni'];

if(btn_usuario.value=="")
{
alert("Complete el dato por favor");
btn_usuario.focus();
return;
}

if(btn_pass.value=="")
{
alert("Complete el dato por favor");
btn_pass.focus();
return false;
}else{if(!es_alfanumerico(btn_pass.value))
	{
	alert("El password del vendedor contiene no numericos alfanumericos");
	btn_pass.focus();
	return false;
		
	}
}

if(btn_apenom.value=="")
{
alert("Complete el dato por favor");
btn_apenom.focus();
return false;
}

if(btn_dni.value=="")
{
alert("Complete el dato por favor");
btn_dni.focus();
return false;
}else
{if(!es_entero(btn_dni.value))
	{
	alert("Dni contiene simbolos no numericos");
	btn_dni.focus();
	return false;
		
	}

}
return true;	

}


function valida_vendedor_add()
{
if(validar_vendedor())
{
	
carga_pantalla('PME_sys_saveadd=Grabar');
}
return;
}

function valida_vendedor_change()
{
if(validar_vendedor())
{

carga_pantalla('PME_sys_savechange=Grabar');
}
return;
}


/*********************validacion de promocion*****************************************/
function validar_promocion()
{

	btn_descripcion=document.PME_sys_form.elements['PME_data_descripcion'];
	btn_precio=document.PME_sys_form.elements['PME_data_precio'];
	btn_cantidad=document.PME_sys_form.elements['PME_data_cantidad'];

	/*
	btn_articulo1=document.PME_sys_form.elements['PME_data_articulo_1'];
	btn_cant_art1=document.PME_sys_form.elements['PME_data_cant_1'];
	
	btn_articulo2=document.PME_sys_form.elements['PME_data_articulo_2'];
	btn_cant_art2=document.PME_sys_form.elements['PME_data_cant_2'];
	
	btn_articulo3=document.PME_sys_form.elements['PME_data_articulo_3'];
	btn_cant_art3=document.PME_sys_form.elements['PME_data_cant_3'];
	
	btn_articulo4=document.PME_sys_form.elements['PME_data_articulo_4'];
	btn_cant_art4=document.PME_sys_form.elements['PME_data_cant_4'];
*/

if(btn_descripcion.value=="")
{
alert("Complete el dato por favor");
btn_descripcion.focus();
return false;
}
if(btn_precio.value=="")
{
alert("Complete el dato por favor");
btn_precio.focus();
return false;
}else
{if(!es_numero(btn_precio.value))
	{
	alert("El precio no es valido.");
	btn_precio.focus();
	return false;
		
	}

}
/*
if(btn_articulo1.value==0 && btn_articulo2.value==0 && btn_articulo3.value==0 && btn_articulo4.value==0)
{
alert("Debe seleccionar al menos un producto. ");
return false;
}else
{
 if(btn_articulo1.value!=0)
 {
	if(!es_entero(btn_cant_art1.value))
	{alert("Hay un dato que no es valido.");
		btn_cant_art1.focus();
		return false;
	}
 }
 if(btn_articulo2.value!=0)
 {
	if(!es_entero(btn_cant_art2.value))
	{alert("Hay un dato que no es valido.");
		btn_cant_art2.focus();
		return false;
	}
 }
  if(btn_articulo3.value!=0)
  {
	if(!es_entero(btn_cant_art3.value))
	{alert("Hay un dato que no es valido.");
		btn_cant_art3.focus();
		return false;
	}
  }
   if(btn_articulo4.value!=0)
   {
	if(!es_entero(btn_cant_art4.value))
	{alert("Hay un dato que no es valido.");
		btn_cant_art4.focus();
		return false;
	}	
  }
}
*/

return true;	

}

function valida_promocion_add()
{
if(validar_promocion())
{
carga_pantalla('PME_sys_saveadd=Grabar');
}
return;
}

function valida_promocion_change()
{
if(validar_cliente())
{

carga_pantalla('PME_sys_savechange=Grabar');
}
return;
}






function dato_repetido(valor,tabla, columna)//busca en la base de datos el VALOR si esta repetido en la columna de la tabla 
{									//pasada como parametros
var bandera=false;
	new Ajax.Request("../includes/consultas.php?obj=repetido" ,
			 {method: 'get',
				 parameters :
				 {valor:valor,tabla:tabla,columna:columna},
				 onSuccess :function(transport)
				 {			
/*				  var bandera = transport.responseText;
                callback(bandera);*/
				  bandera = transport.responseText.evalJSON();



				//validar_articulo2(transport.responseText);
				
// bandera=transport.responseText;
//				return transport.responseText;
				},
				onFailure : function()
				{alert("Error al consultar...");
				 }
			 }
			 );

 return bandera;
}


/*********************validacion de articulo*****************************************/
function validar_articulo(id_form,Accion)
{

obj=document.getElementById(id_form);



	btn_desc=obj.elements['PME_data_descripcion'];
	btn_codigo=obj.elements['PME_data_codigo'];
	costo=obj.elements['PME_data_costo'];
	btn_precio=obj.elements['PME_data_precio'];
	btn_precio2=obj.elements['PME_data_precio2'];
	btn_precio3=obj.elements['PME_data_precio3'];
	btn_stock=obj.elements['PME_data_stock'];
	btn_stock_minimo_pagina=obj.elements['PME_data_stock_minimo_pagina'];
	
	
	if(btn_stock_minimo_pagina.value=="" || !es_numero(btn_stock_minimo_pagina.value))
	{
	alert("Complete el dato por favor o coloque valor numérico");
	btn_stock_minimo_pagina.focus();
	return false;
	}
	
	if(btn_codigo.value=="")
	{
	alert("Complete CODIGO por favor");
	btn_codigo.focus();
	return false;
	}

	if(btn_desc.value=="")
	{
	alert("Complete DESCRIPCION por favor");
	btn_desc.focus();
	return false;
	}	

	if(btn_precio.value=="")
	{
	alert("Complete PRECIO (Lista 1) por favor");
	btn_precio.focus();
	return false;
	}else
	{if(!es_numero(btn_precio.value))
		{
		alert("El valor del precio 1 no es valido");
		btn_precio.focus();
		return false;
			
		}
	
	}
	
	if(btn_precio2.value=="")
	{
	alert("Complete PRECIO (Lista 2) por favor");
	btn_precio2.focus();
	return false;
	}else
	{if(!es_numero(btn_precio2.value))
		{
		alert("El valor del precio 2 no es valido");
		btn_precio2.focus();
		return false;
			
		}
	
	}
	
	if(btn_precio3.value=="")
	{
	alert("Complete PRECIO (Lista 3) por favor");
	btn_precio3.focus();
	return false;
	}else
	{if(!es_numero(btn_precio3.value))
		{
		alert("El valor del precio no es valido");
		btn_precio3.focus();
		return false;
			
		}
	
	}

	if(parseFloat(costo.value)>=parseFloat(btn_precio.value)){
		if(!confirm("Seguro PRECIO DE VENTA (LISTA 1) menor o igual a PRECIO DE COSTO?")) {
			btn_precio.focus();
			return false;
		}
	}
	if(parseFloat(costo.value)>=parseFloat(btn_precio2.value)){
		if(!confirm("Seguro PRECIO DE VENTA (LISTA 2) menor o igual a PRECIO DE COSTO?")) {
			btn_precio2.focus();
			return false;	
		}
	}
	if(parseFloat(costo.value)>=parseFloat(btn_precio3.value)){
		if(!confirm("Seguro PRECIO DE VENTA (LISTA 3) menor o igual a PRECIO DE COSTO?")){
			btn_precio3.focus();
			return false;	
		}
	}		
	
	
	/*if(btn_stock.value=="")
	{
	alert("Complete el dato por favor");
	btn_stock.focus();
	return false;
	}else
	{stk=parseInt(btn_stock.value)
		if(isNaN(stk))
		{
		alert("El valor del stock no es valido");
		btn_stock.focus();
		return false;
			
		}
	
	}*/
if(Accion=='C')
{
	var agrega_stock=parseInt($F('agrega_stock'));
	if($F('agrega_stock')=="")
	{
		$('agrega_stock').value="0";
		var agrega_stock=0;
	}

	if(isNaN(agrega_stock)){
		alert("El valor del stock a agregar no es valido");
		btn_stock.focus();
		return false;			
	}

}
return true;	


}

/*
function validar_articulo2(respuesta)
{
	if(respuesta=="true")	
	{
	alert("El codigo ingresado ya se encuentra asociado a otro articulo. Ingrese otro codigo o elimine el articulo con este codigo.");
	btn_codigo.focus();
	return false;
	}

	if(btn_precio.value=="")
	{
	alert("Complete el dato por favor");
	btn_precio.focus();
	return false;
	}else
	{if(!es_numero(btn_precio.value))
		{
		alert("El valor del precio no es valido");
		btn_precio.focus();
		return false;
			
		}
	
	}
	
	if(btn_stock.value=="")
	{
	alert("Complete el dato por favor");
	btn_stock.focus();
	return false;
	}else
	{if(!es_entero(btn_stock.value))
		{
		alert("El valor del stock no es valido");
		btn_stock.focus();
		return false;
			
		}
	
	}

return true;	

}

*/
function grabarycontinuar(){
	$("grabar_continuar").value="1";
	copy=$$('select[name="PME_data_id_proveedor"]')[0];
	$("proximo_proveedor").value=$F(copy);

	copy2=$$('select[name="PME_data_id_categoria"]')[0];
	$("proxima_categoria").value=$F(copy2);	

	valida_articulo_add("form_jp"); 
}

function valida_articulo_add(id_form){

var codigo_elem=document.form_jp.elements['PME_data_codigo'];
var desc_elem=document.form_jp.elements['PME_data_descripcion'];
var codigo=document.form_jp.elements['PME_data_codigo'].value;

if(codigo=="" || !es_numero(codigo)){
	alert("CODIGO NO VALIDO");
	codigo_elem.value='';
	codigo_elem.focus();
	return;	
}

new Ajax.Request("iframe_articulos.php?obj=comprobar_codigo",
			 {method: 'get',
			 async:false,
				 parameters : {codigo:codigo},
				 onSuccess :function(transport)

				{
					paquete=transport.responseText				 				
							if(paquete!="OK")
							{
								alert("#ERROR - CODIGO: ("+codigo+") - YA EXISTE."); 
								codigo_elem.focus();
								return;
							}else{
									//ahora valido DESCRIPCION
									var descripcion=document.form_jp.elements['PME_data_descripcion'].value;
									new Ajax.Request("iframe_articulos.php?obj=comprobar_descripcion",
											{
												method: 'get',
												async:false,
													 parameters : {descripcion:descripcion},
													 onSuccess :function(transport)

													{
														paquete=transport.responseText				 				
																if(paquete!="OK")
																{
																	alert("Ya existe un articulo con la misma descripcion: "+descripcion); 
																	desc_elem.focus();
																	return;
																}else{
																	if(id_form=="solo_info"){
																		return;
																	}
																	if(validar_articulo(id_form,'A'))
																	{
																		$(id_form).action='articulo.php?PME_sys_saveadd=Grabar';
																		
																		$(id_form).submit();	//esta funcion se encuentra en cargar_paginas.js
																		//carga_pantalla('PME_sys_saveadd=Grabar');
																	}
																	return;

																}
																 
														},
													onFailure : function()
													{alert("Error al consultar si ya existe descripcion...");
													 }
										}
								 	);
							
							}
							 
					},
				onFailure : function()
				{alert("Error al consultar codigo..."); exit=1;
				 }
			 }
			 );

}//fin del valida add


function valida_articulo_change(id_form){
	
	var codigo=document.form_jp.elements['PME_data_codigo'].value;
	var id=document.form_jp.elements['PME_sys_rec'].value;

	new Ajax.Request("iframe_articulos.php?obj=comprobar_codigo_cambio" ,
				 {method: 'get',
					 parameters :
					 {id:id,codigo:codigo},
					 onSuccess :function(transport)

					{
						paquete=transport.responseText		

								if(paquete!="OK")
								{
								alert("El codigo de articulo "+codigo+" ya existe en el sistema para otro articulo. No se pueden realizar los cambios "); 
								return;
								}else{


									//ahora valido DESCRIPCION
									var descripcion=document.form_jp.elements['PME_data_descripcion'].value;
									new Ajax.Request("iframe_articulos.php?obj=comprobar_descripcion",
											{
												method: 'get',
												async:false,
													 parameters : {id:id,descripcion:descripcion},
													 onSuccess :function(transport)

													{
														paquete=transport.responseText				 				
																if(paquete!="OK")
																{
																	alert("Ya existe un articulo con la misma descripcion: "+descripcion); 
																	return;
																}else{
																		if(validar_articulo(id_form,'C'))
																		{
																		$(id_form).action='articulo.php?PME_sys_savechange=Grabar';
																		$(id_form).submit();	//esta funcion se encuentra en cargar_paginas.js
																		//carga_pantalla2('PME_sys_savechange=Grabar',id_form);	//esta funcion se encuentra en cargar_paginas.js
																		//carga_pantalla('PME_sys_savechange=Grabar');
																		}
																		return;
																}
																 
														},
													onFailure : function()
													{alert("Error al consultar si ya existe descripcion...");
													 }
										}
								 	);


								}
								 
						},
					onFailure : function()
					{alert("Error al consultar rubros...");
					 }
				 }
				 );
	

}//fin del valida change



function popup(param){
	

	
	window.open('detalle.php?id='+param, '_blank', 'scrollbars=yes,width=600,height=600,top=100,left=500');return false;
	
}

function abrir(param,alto,ancho){
	

	
	window.open(param, '_blank', 'scrollbars=yes,width='+ancho+',height='+alto+',top=100,left=200');return false;
	
}




function mover_arriba(id)
{
odiv=document.getElementById(id);
odiv.style.backgroundPosition="0px 61px";
}
function mover_abajo(id)
{
odiv=document.getElementById(id);
odiv.style.backgroundPosition="0px 0px";
}

function mostrar(id){
odiv=document.getElementById(id);
if(odiv.style.display==""){
odiv.style.display="none";
}else{
odiv.style.display="";
}

}


function recargar_rubro(id_select,id_div){
obj_cat=document.getElementById(id_select);
cat=obj_cat.value;

new Ajax.Request("consultas.php?obj=consultar_rubros" ,
			 {method: 'get',
				 parameters :
				 {categoria:cat},
				 onSuccess :function(transport)
				 {				 				
				refrescar(transport,id_div);				
				},
				onFailure : function()
				{alert("Error al consultar rubros...");
				 }
			 }
			 );


}

function recargar_rubro2(id_select,id_div){
obj_cat=document.getElementById(id_select);
cat=obj_cat.value;

new Ajax.Request("../../admin/consultas.php?obj=consultar_rubros" ,
			 {method: 'get',
				 parameters :
				 {categoria:cat},
				 onSuccess :function(transport)
				 {				 				
				refrescar(transport,id_div);				
				},
				onFailure : function()
				{alert("Error al consultar rubros...");
				 }
			 }
			 );


}


function refrescar(transport, porcion){
	
	$(porcion).update(transport.responseText);

}//fin de la funcion refresacar



function volver_recargar(url){
	window.opener.document.location.replace(url);
	window.close();
	
	}

function writeImage(id_input,id_img) {
   var data = $(id_input).files.item(0).getAsDataURL();
   $(id_img).src = 'data:' + data;

 }

function Desaprobar_pedido(orden)
{
	new Ajax.Request("../includes/consultas.php?obj=desaprobar_pedido" ,
			 {method: 'get',
				 parameters :
				 {orden:orden},
				 onSuccess :function(transport)
				 {
					 paquete=transport.responseText				 				
					 if(paquete=="ok")
					 {
						alert("Pedido Desaprobado."); 
						$('estado').update("Desaprobado");
						$('PME_data_estado').value="Desaprobado";
					}
					else
					{
					alert("Problemas del sistema al desaprobar pedido."); 
					}
				},
				onFailure : function()
				{alert("Error al consultar rubros...");
				 }
			 }
			 );

}


function validar_aprobacion_pedido(orden)
{
	
var obj_est=document.getElementById('est_pedido').value;

//obj_est.selectedIndex=1;//selecciono la segunda opcion (desaprobado)
//estado=obj_est.value;

if(obj_est=="desaprobado")
	{
		
	new Ajax.Request("../includes/consultas.php?obj=aprobar_pedido" ,
			 {method: 'get',
				 parameters :
				 {orden:orden},
				 onSuccess :function(transport)
					 {paquete=transport.responseText				 				
							if(paquete=="YA APROBADO")
							{
							alert("Pedido aprobado con anterioridad."); 
							return;
							}
							 $('info').update(paquete);
							 est=document.getElementById('Resultado').value;
						 alert(est);					 
							 if(est=="desaprobado")
							 {
							// obj_est.selectedIndex=1;//selecciono la segunda opcion (desaprobado)
							$('estado').update('Desaprobado');
							alert("No se puede aprobar la orden de pedido. Vea el informe final!");

							}
							 else
							 {
							alert("Se pudo Aprobar la orden de pedido correctamente!");	 			

							 $('estado').update('Aprobado');		
							 }
					},
				onFailure : function()
				{alert("Error al consultar rubros...");
				 }
			 }
			 );
	
	}//fin del if
	

}//fin de validar_aprobacion_pedido




//----- 	MENU DESPLEGABLE ****

/* modifica las caracteristicas de los menus hijos */
function menu_set(){
 var i,d='',h="<sty"+"le type=\"text/css\">",tA=navigator.userAgent.toLowerCase();if(window.opera){
 if(tA.indexOf("opera 5")>-1||tA.indexOf("opera 6")>-1){return;}}if(document.getElementById){
 for(i=1;i<20;i++){d+='ul ';h+="\n#menunav "+d+"{position:absolute;left:-9000px;width:11em;}";}
 document.write(h+"\n<"+"/sty"+"le>");}}menu_set();
/* modifica caracteristicas de apertura de menus */
function menu_init(){
 var i,g,tD,tA,tU,pp,lvl,tn=navigator.userAgent.toLowerCase();if(window.opera){
 if(tn.indexOf("opera 5")>-1||tn.indexOf("opera 6")>-1){return;}}else if(!document.getElementById){return;}
 menup=arguments;menuct=new Array;tD=document.getElementById('menunav');
if(tD){tA=tD.getElementsByTagName('A');
 for(i=0;i<tA.length;i++){tA[i].menucl=menuct.length;
menuct[menuct.length]=tA[i];g=tA[i].parentNode.getElementsByTagName("UL");
 tA[i].menusub=(g)?g[0]:false;ev=tA[i].getAttribute("onmouseover");
if(!ev||ev=='undefined'){tA[i].onmouseover=function(){
 menu_trig(this);};}ev=tA[i].getAttribute("onfocus");
if(!ev||ev=='undefined'){tA[i].onfocus=function(){menu_trig(this);};}
 if(tA[i].menusub){pp=tA[i].parentNode;
lvl=0;while(pp){if(pp.tagName&&pp.tagName=="UL"){lvl++;}pp=pp.parentNode;}
 tA[i].menulv=lvl;}}tD.onmouseout=menu_close;menu_open();}
}
function menu_trig(a){
 var b,t;if(document.menut){clearTimeout(document.menut);}document.menua=1;
b=(a.menusub)?'menu_show(':'menu_tg(';
 t='document.menut=setTimeout("'+b+a.menucl+')",160)';eval (t);
}
/*muestra los menus */
function menu_show(a,bp){
 var u,lv,oft,ofr,uw,uh,pp,aw,ah,adj,mR,mT,wW=0,
wH,w1,w2,w3,sct,pw,lc,pwv,xx=0,yy=0,wP=true;
 var iem=(navigator.appVersion.indexOf("MSIE 5")>-1)?true:false,dce=document.documentElement,dby=document.body;
document.menua=1;
 if(!bp){menu_tg(a);}u=menuct[a].menusub;
if(u.menuax&&u.menuax==1){return;}u.menuax=1;
lv=(menup[0]==1&&menuct[a].menulv==1)?true:false;
menuct[a].className=menuct[a].className.replace("menutrg","menuon");oft=parseInt(menup[3]);
ofr=parseInt(menup[4]);
 uw=u.offsetWidth;uh=u.offsetHeight;pp=menuct[a];aw=pp.offsetWidth;ah=pp.offsetHeight;while(pp){xx+=(pp.offsetLeft)?pp.offsetLeft:0;
 yy+=(pp.offsetTop)?pp.offsetTop:0;if(window.opera||navigator.userAgent.indexOf("Safari")>-1){
 if(menuct[a].menulv!=1&&pp.nodeName=="BODY"){yy-=(pp.offsetTop)?pp.offsetTop:0;}}pp=pp.offsetParent;}
 if(iem&&navigator.userAgent.indexOf("Mac")>-1){yy+=parseInt(dby.currentStyle.marginTop);}adj=parseInt((aw*ofr)/100);mR=(lv)?0:aw-adj;
 adj=parseInt((ah*oft)/100);mT=(lv)?0:(ah-adj)*-1;w3=dby.parentNode.scrollLeft;if(!w3){w3=dby.scrollLeft;}w3=(w3)?w3:0;
 if(dce&&dce.clientWidth){wW=dce.clientWidth+w3;}else if(dby){wW=dby.clientWidth+w3;}if(!wW){wW=0;wP=false;}wH=window.innerHeight;
 if(!wH){wH=dce.clientHeight;if(!wH||wH<=0){wH=dby.clientHeight;}}sct=dby.parentNode.scrollTop;if(!sct){sct=dby.scrollTop;if(!sct){
 sct=window.scrollY?window.scrollY:0;}}pw=xx+mR+uw;if(pw>wW&&wP){mR=uw*-1;mR+=10;if(lv){mR=(wW-xx)-uw;}}lc=xx+mR;if(lc<0){mR=xx*-1;}
 pw=yy+uh+ah+mT-sct;pwv=wH-pw;if(pwv<0){mT+=pwv;}u.style.marginLeft=mR+'px';u.style.marginTop=mT+'px';
 if(menup[2]==1){if(!iem){menu_anim(a,20);}}u.className="menushow";
}
/* oculta los menus */
function menu_hide(u){
 var i,tt,ua;u.menuax=0;u.className="menuhide";
ua=u.parentNode.firstChild;ua.className=ua.className.replace("menuon","menutrg");
}
function menu_tg(a,b){
 var i,u,tA,tU,pp;tA=menuct[a];pp=tA.parentNode;while(pp){if(pp.tagName=="UL"){break;}pp=pp.parentNode;}if(pp){
 tU=pp.getElementsByTagName("UL");for(i=tU.length-1;i>-1;i--){if(b!=1&&tA.menusub==tU[i]){continue;}else{menu_hide(tU[i]);}}}
}
function menu_close(evt){
 var pp,st,tS,m=true;evt=(evt)?evt:((event)?event:null);st=document.menua;if(st!=-1){if(evt){
 tS=(evt.relatedTarget)?evt.relatedTarget:evt.toElement;if(tS){pp=tS.parentNode;while(pp){if(pp&&pp.id&&pp.id=="menunav"){m=false;
 document.menua=1;break;}pp=pp.parentNode;}}if(m){document.menua=-1;if(document.menut){clearTimeout(document.menut);}
 document.menut=setTimeout("menu_clr()",360);}}}
}
function menu_clr(){
 var i,tU,tUU;document.menua=-1;tU=document.getElementById('menunav');if(tU){tUU=tU.getElementsByTagName("UL");if(tUU){
 for(i=tUU.length-1;i>-1;i--){menu_hide(tUU[i]);}}}
}
/* crea la animación */
function menu_anim(a,st){
 var g=menuct[a].menusub,sp=30,inc=20;st=(st>=100)?100:st;g.style.fontSize=st+"%";if(st<100){st+=inc;setTimeout("menu_anim("+a+","+st+")",sp);}
}
function menu_mark(){document.menuop=arguments;}
function menu_open(){
 var i,x,tA,op,pp,wH,tA,aU,r1,k=-1,kk=-1,mt=new Array(1,'','');if(document.menuop){mt=document.menuop;}op=mt[0];if(op<1){return;}
 tA=document.getElementById('menunav').getElementsByTagName("A");wH=window.location.href;r1=/index\.[\S]*/i;for(i=0;i<tA.length;i++){
 if(tA[i].href){aU=tA[i].href.replace(r1,'');if(op>0){if(tA[i].href==wH||aU==wH){k=i;kk=-1;break;}}if(op==2){if(tA[i].firstChild){
 if(tA[i].firstChild.nodeValue==mt[1]){kk=i;}}}if(op==3 && tA[i].href.indexOf(mt[1])>-1){kk=i;}if(op==4){for(x=1;x<mt.length;x+=2){
 if(wH.indexOf(mt[x])>-1){if(tA[i].firstChild&&tA[i].firstChild.data){if(tA[i].firstChild.data==mt[x+1]){kk=i;break;}}}}}}}k=(kk>k)?kk:k;
 if(k>-1){pp=tA[k].parentNode;while(pp){if(pp.nodeName=="LI"){pp.firstChild.className="menumark"+" "+pp.firstChild.className;}
 pp=pp.parentNode;}}if(kk>-1){document.menuad=1;}
}


function consultar_ventas_vendedores()
{
var fecha_dsd=$F('txt_fecha_dsd');

	if(!es_fecha(fecha_dsd))
	{
		alert('Fecha  desde invalida');
		return
	}

	var fecha_hst=$F('txt_fecha_hst');

	if(!es_fecha(fecha_hst))
	{
		alert('Fecha  hasta invalida');
		return
	}

	var url='ver_ventas_vendedor.php?viene=consultar&fechadsd='+fecha_dsd+'&fechahst='+fecha_hst;


$('frame_datos').src=url;
}


function tabular(e,obj) {
  tecla=(document.all) ? e.keyCode : e.which;
            if(tecla!=13) return;
            frm=obj.form;
            for(i=0;i<frm.elements.length;i++) 
                if(frm.elements[i]==obj) 
                { 
                    if (i==frm.elements.length-1) 
                        i=-1;
                    break 
                }
    // ACA ESTA EL CAMBIO disabled, Y PARA SALTEAR CAMPOS HIDDEN
            if ((frm.elements[i+1].disabled ==true) || (frm.elements[i+1].type=='hidden') )    
                tabular(e,frm.elements[i+1]);
 // ACA ESTA EL CAMBIO readOnly 
            else if (frm.elements[i+1].readOnly ==true )    
                tabular(e,frm.elements[i+1]);
            else {
                if (frm.elements[i+1].type=='text') //VALIDA SI EL CAMPO ES TEXTO
                {frm.elements[i+1].select();};   // AÑADIR LOS CORCHETES Y ESTA INSTRUCCION 
                frm.elements[i+1].focus();
            }
            return false;  
} 


function dibujar_inpt_dolar()
{
	var mes_dsd=$F('mes_dsd1')
	var anio_dsd=$F('anio_dsd1')
	var mes_hst=$F('mes_hst2')
	var anio_hst=$F('anio_hst2')
    var tabla_dolar_html=""
	var fechadesde=new Date(anio_dsd+'/'+mes_dsd+'/1')
	var fechahasta=new Date(anio_hst+'/'+mes_hst+'/1')
	
//si la fecha hasta es mayor a la desde
var strperiodoID=""
var strMes=""
	if(fechahasta>=fechadesde)
	{tabla_dolar_html="<table style='border-color: #707070;border-style: solid;border-width: 1px'><tr><th style='text-align:center;width:18%'>PERIODO</th><th style='width:7%;text-align:center'>DOLAR</th><th style='text-align:center;width:18%'>PERIODO</th><th style='width:7%;text-align:center'>DOLAR</th><th style='text-align:center;width:18%'>PERIODO</th><th style='width:7%;text-align:center'>DOLAR</th><th style='text-align:center;width:18%'>PERIODO</th><th style='width:auto;text-align:center'>DOLAR</th></tr>"

		
	       var TDs=0

	     
			while(fechadesde<=fechahasta)
			{				  
					mes_pivot=parseInt(fechadesde.getMonth())+1
					anio_pivot=parseInt(fechadesde.getFullYear())
					strperiodoID=fechadesde.getFullYear()+((mes_pivot<10)?"0"+mes_pivot.toString():mes_pivot.toString())
					
					if(TDs==0)
					{
					tabla_dolar_html+="<tr>"		
					}
			

					switch (mes_pivot) 
					{
					case 1: 
					strMes="ENERO"
					break;
					case 2:
		            strMes="FEBRERO"
					break;			
					case 3:
		            strMes="MARZO"
					break;		
					case 4:
		            strMes="ABRIL"
					break;
					case 5:
		            strMes="MAYO"
					break;			
					case 6:
		            strMes="JUNIO"
					break;
					case 7:
		            strMes="JULIO"
					break;
					case 8:
		            strMes="AGOSTO"
					break;
					case 9:
		            strMes="SEPTIEMBRE"
					break;			
					case 10:
		            strMes="OCTUBRE"
					break;
					case 11:
		            strMes="NOVIEMBRE"
					break;
					case 12:
		            strMes="DICIEMBRE"
					break;
					default:break;	

					}

					tabla_dolar_html+="<td style='text-align:right'>"+strMes+" - "+anio_pivot.toString()+"</td><td><input type='text' value='0' id='"+strperiodoID+"' style='width:50px;text-align:center'/></td>"
					if(TDs==3)
					{					
						tabla_dolar_html+="</tr>"	
					}
			
				
					TDs++;
					if(TDs==4)
					{
						TDs=0;					
					}
					
					fechadesde=new Date(fechadesde.getFullYear()+'/'+(mes_pivot+1)+'/1')
			}
			
	//si faltan TDs por completar, anexo los restantes
		switch (TDs) 
				{
				case 1: 
				tabla_dolar_html+="<td></td><td></td><td></td><td></td><td></td><td></td></tr>"
				break;
				case 2:
	            tabla_dolar_html+="<td></td><td></td><td></td><td></td></tr>"
				 break;	
				 case 3:
	            tabla_dolar_html+="<td></td><td></td></tr>"
				 break;	
				 
				default:break;	
				}
	tabla_dolar_html+="</table>"	

	}
	$('tbl_dolar').update(tabla_dolar_html);
}


var strperiododesde=""
var respuestaDRW=""
var arrVendedores

function  consulta_estadisticas_graficos()
{

	var mes_dsd1=parseInt($F('mes_dsd1'))
	var anio_dsd1=parseInt($F('anio_dsd1'))
	var mes_hst2=parseInt($F('mes_hst2'))
	var anio_hst2=parseInt($F('anio_hst2'))
    var tabla_dolar_html=""
    strperiododesde=$F('anio_dsd1').toString()+$F('mes_dsd1').toString()
	var strfedesde=anio_dsd1+mes_dsd1
	var strfehasta=anio_hst2+mes_hst2

	//si la fecha hasta es mayor a la desde
	var strperiodoID=""
	var strMes=""
	var paramXML='<cuerpo><parametros>'
	var val
	var id_input
		if(parseInt(strfehasta)>=parseInt(strfedesde))
		{

			$$('#tbl_dolar input[type=text]').each(function (e){
				
				 val=e.value
				 id_input=e.id
				if(es_numero(val))
				{
					paramXML+='<periodo><valor>'+id_input+'</valor><dolar>'+val+'</dolar></periodo>'
				}
			});
		}
		paramXML+='</parametros></cuerpo>'

 new Ajax.Request("estadisticas.php?obj=consultar_datos_graficos" ,
           {method: 'post',
           asynchronous:false,
           onLoading:$('chart_div').update("<div id='accion'> <img src='../img/loading.gif' alt='Cargando...' /> Recopilando Información... Aguarde.</div>"),
             parameters :{strxml:escape(paramXML)},
             onSuccess :function(transport)
             {        
               respuestaDRW=transport.responseText;
             //alert(respuesta);
             if(respuestaDRW!="")
             {
             cargar_vendedores()
             dibujar_tbl_vendedores()
             dibujar()
             }else
             {
             	limpiar_draw()
             }
             },
            onFailure : function()
            {alert("Error al enviar datos...");
             }
           }
           );


}

var arrDataVendedores=[]
var arrVendedores=[]
function cargar_vendedores()
{
	arrDataVendedores=[]
	arrVendedores=[]

	if(respuestaDRW!="")
	{

       obj = JSON.parse(respuestaDRW);           
      
      
      //busco todos los vendedores
      for( i=0;i<obj.length ;i++)
      {
      	objPeriodo=obj[i]      	
      	var vendedores=objPeriodo.vendedores;
      	for(j=0;j<vendedores.length;j++)
      	{
      		var existeVendedor=false;
      		for(x=0;x<arrVendedores.length;x++)
      		{
      			if(arrVendedores[x]==vendedores[j].id_vendedor)
      			{
      				existeVendedor=true;
      				break;
      			}      			

      		}
      		if(!existeVendedor)
      		{      				
      				arrVendedores.push(vendedores[j].id_vendedor)
      				arrDataVendedores.push(vendedores[j].nombre)
      		}
      	}
      }//busqueda de vendedores


	}

}




function dibujar_tbl_vendedores()	
{
var strVendedoresHTML="<table style='border-color: #707070;border-style: solid;border-width: 1px'>"
	if(respuestaDRW!="" && arrVendedores.length>0)
	{var tdS=0
		for (i=0;i<arrVendedores.length;i++)
		{
			if(tdS==0)
			{
			strVendedoresHTML+="<tr>"
			}
			strVendedoresHTML+="<td style='text-align:center'><input type='checkbox' id='chkvend"+arrVendedores[i]+"' checked='checked'  onclick='check_lista()' /></td><td style='text-align:left'>"+arrDataVendedores[i]+"</td>"

			if(tdS==4)
			{
				tdS=0
				strVendedoresHTML+="</tr>"
			}
			tdS++
		}
/*relleno de tds con los que faltan*/
	switch (tdS) 
				{
				case 1: 
				strVendedoresHTML+="<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>"
				break;
				case 2:
	            strVendedoresHTML+="<td></td><td></td><td></td><td></td><td></td><td></td></tr>"
				 break;			
				 case 3:
	            strVendedoresHTML+="<td></td><td></td><td></td><td></td></tr>"
				 break;
				 case 4:
	            strVendedoresHTML+="<td></td><td></td></tr>"
				 break;			
				default:break;	
				}

	}
strVendedoresHTML+="</table>"

$('tbl_vendedores').update(strVendedoresHTML)

}




function limpiar_draw()
{
	respuestaDRW=""
	$('chart_div').update("");
}




function dibujar() {


/*habilito o no el check que dibuja en diagramas de barras*/
var vendedores_checks=0
$$('#tbl_vendedores input[type=checkbox]').each(function (e){						
	if($(e.id).checked)
	{
					vendedores_checks++
	}		

});



if(vendedores_checks==1)
{
	$('barras').disabled=false
	
}else
{
	$('barras').disabled=true
	$('barras').checked=false
}


	if(respuestaDRW!="" && ($('lista1').checked || $('lista2').checked || $('lista3').checked || $('lista4').checked))
    {
    	if($('barras').checked)
    	{
    		check_barras()
    		return
    	}
    	//google.load('visualization', '1.0', {'packages':['corechart']});
    	var strTitulo=""

    		if($('lista1').checked)
	      	{
	      	strTitulo+="lista 1"
	      	}	
            if($('lista2').checked)
	      	{
		      	if(strTitulo!="")
		      	{
		      	strTitulo+=" + "	
		      	}
		      strTitulo+="lista 2"	
	      	}	

	      	if($('lista3').checked)
	      	{
		      	if(strTitulo!="")
		      	{
		      	strTitulo+=" + "	
		      	}
		      strTitulo+="lista 3"	
	      	}	

	      	if($('lista4').checked)
	      	{
		      	if(strTitulo!="")
		      	{
		      	strTitulo+=" + "	
		      	}
		      strTitulo+="lista manual"	
	      	}
	      strTitulo=" "+strTitulo+"."				
	      				
//	  google.load('visualization', '1', {packages: ['corechart', 'line']});
      //google.setOnLoadCallback(drawCurveTypes);


      var data = new google.visualization.DataTable();
      var definioVendedores=false

      obj = JSON.parse(respuestaDRW);
      //data.addColumn('number', 'X');
      data.addColumn('string', 'X'); //defino que los periodos son string
      arrRows=[];
     // arrVendedores=[];
      //Agrego a los vendedores
      
      for(x=0;x<arrVendedores.length;x++)
      {
      	var strvendedorid='chkvend'+String(arrVendedores[x])
      		if($(strvendedorid).checked)
      		{
      				data.addColumn('number', arrDataVendedores[x]);
      				
      		}		

      }

     /* for( i=0;i<obj.length ;i++)
      {
      	objPeriodo=obj[i]      	
      	var vendedores=objPeriodo.vendedores;
      	for(j=0;j<vendedores.length;j++)
      	{
      		var existeVendedor=false;
      		for(x=0;x<arrVendedores.length;x++)
      		{
      			if(arrVendedores[x]==vendedores[j].id_vendedor)
      			{
      				existeVendedor=true;
      				break;
      			}      			

      		}
      		var strvendedorid='chkvend'+String(vendedores[j].id_vendedor)
      		if(!existeVendedor && $(strvendedorid).checked)
      		{
      				data.addColumn('number', vendedores[j].nombre);
      				//arrVendedores.push(vendedores[j].id_vendedor)
      				//arrDataVendedores.push(vendedores[j].nombre)
      		}
      	}
      }*///busqueda de periodos y vendedores

      //dibujar_tbl_vendedores()

 		//por cada periodo y por cada vendedor, pongo el valor correspondiente
      for(i=0;i<obj.length;i++)
      {
      	objPeriodo=obj[i]
      	var per=String(objPeriodo.periodo)
      	var vendedores=objPeriodo.vendedores
      	
      	/*cambio el formato fecha*/
      	var strPeriodo=per.substring(4,6)+'/'+per.substring(0,4)
      	 


      	var fila=[]
      	fila.push(strPeriodo)
      	
	       for(x=0;x<arrVendedores.length;x++)
		   {var valorY=0;
		   	var valorYstr=""
		   	strvendedorid='chkvend'+String(arrVendedores[x])	
				   	if($(strvendedorid).checked)
				   	{
					      	  for(j=0;j<vendedores.length;j++)
						      	{ 	      	
						      			if(arrVendedores[x]==vendedores[j].id_vendedor )
						      			{
						      				var precio_dolar=parseFloat(vendedores[j].dolar)
						      				if($('lista1').checked)
						      				{ 
						      					valorY+=parseFloat(vendedores[j].acumulado_lista1)		
						      					
						      				}	
						      				if($('lista2').checked)
						      				{
						      					valorY+=parseFloat(vendedores[j].acumulado_lista2)
						      				}	
						      				if($('lista3').checked)
						      				{
						      					valorY+=parseFloat(vendedores[j].acumulado_lista3)
						      				}	
						      				if($('lista4').checked)
						      				{
						      					valorY+=parseFloat(vendedores[j].acumulado_precio_manual)
						      				}	
						      				if($('dolar').checked && precio_dolar>0)
						      				{
						      					valorY/=precio_dolar
						      				}
						      				valorYstr=valorY.toFixed(2)
						      				valorY=parseFloat(valorYstr)
						      				break;
						      			}      			

						      	}
						      var simbolo_peso="$ "	
							if($('dolar').checked )						      	
							{
								simbolo_peso="u$u "	
							}

						arrValorY={v:valorY, f:simbolo_peso+valorYstr}      	
				      fila.push(arrValorY);
				    }
		   }
	     arrRows.push(fila);
     
      }

     
      data.addRows(arrRows);
     var subtitulo="(ventas en pesos)"
     if($('dolar').checked)
     {
     	subtitulo="(ventas en dolares)"
     }

      var options = {
      	  
          title: strTitulo+' '+subtitulo ,          
          legend: { position: 'bottom', alignment: 'start' },
           chartArea:{left:"15%",width:"90%"},
        hAxis: {
          title: 'periodo',
          minValue: 0

        },
        vAxis: {          
          minValue: 0
        },
        series: {
          1: {curveType: 'function'}
        },
        width: 780,
        height:800
      };

			var simbolo_peso="$ "	
			if($('dolar').checked )						      	
			{
			simbolo_peso="u$u "	
			}
   options['vAxis']['format'] = simbolo_peso+' #,##0.00';

      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
      chart.draw(data, options);
      
    }
}   

function check_lista()
{




/*controlo que todos los inputs de dolares sean mayores a cero*/
	if($('dolar').checked)
	{
	var valoresDOLAR=true
		$$('#tbl_dolar input[type=text]').each(function (e){						
						 val=e.value
						 id_input=e.id
						if((es_numero(val)==false) || (val<=0))
						 {valoresDOLAR=false
						  return
						 }
					});
		if(!valoresDOLAR)
		{
			$('dolar').checked=false
			alert("Hay valores de la cotizacion del dolar en al menos un periodo que no es valido")
			return
		}
	}//dolar

if($('dolar').checked && ($('lista1').checked || $('lista2').checked || $('lista3').checked || $('lista4').checked))
{
dibujar()
return
}

if($('lista1').checked || $('lista2').checked || $('lista3').checked || $('lista4').checked)
{
   dibujar()
   return
}
	
		limpiar_draw()
	
}

function check_barras()
{



	if($('barras').checked==false)
	{
		check_lista();
		return
	}
var id_vend=""
$$('#tbl_vendedores input[type=checkbox]').each(function(e){
		var stridchk=e.id
		if($(stridchk).checked)
		{
			id_vend = stridchk.replace("chkvend", "");
			return;
		}
})


if(respuestaDRW!="" && ($('lista1').checked || $('lista2').checked || $('lista3').checked || $('lista4').checked))
    {
    	
    	var strTitulo=""

    		if($('lista1').checked)
	      	{
	      	strTitulo+="lista 1"
	      	}	
            if($('lista2').checked)
	      	{
		      	if(strTitulo!="")
		      	{
		      	strTitulo+=" + "	
		      	}
		      strTitulo+="lista 2"	
	      	}	

	      	if($('lista3').checked)
	      	{
		      	if(strTitulo!="")
		      	{
		      	strTitulo+=" + "	
		      	}
		      strTitulo+="lista 3"	
	      	}	

	      	if($('lista4').checked)
	      	{
		      	if(strTitulo!="")
		      	{
		      	strTitulo+=" + "	
		      	}
		      strTitulo+="lista manual"	
	      	}
	      strTitulo=" "+strTitulo+"."				
	      				
//	  google.load('visualization', '1', {packages: ['corechart', 'line']});
      //google.setOnLoadCallback(drawCurveTypes);

     

      obj = JSON.parse(respuestaDRW);
      
      arrRows=[];
     var strNombreVendedor=""
      arrRows.push(['Periodo', 'Ventas', 'Gastos','Recaudacion'])
      for(x=0;x<arrVendedores.length;x++)
      {
      	if(arrVendedores[x]==id_vend)
      	{
		strNombreVendedor=arrDataVendedores[x]
		strTitulo="Vendedor: "+strNombreVendedor+" - Datos para: "+strTitulo
      	}      	
      	break;
      }

 		//por cada periodo y por cada vendedor, pongo el valor correspondiente
      for(i=0;i<obj.length;i++)
      {
      	objPeriodo=obj[i]
      	var per=String(objPeriodo.periodo)
      	var vendedores=objPeriodo.vendedores
      	
      	/*cambio el formato fecha*/
      	var strPeriodo=per.substring(4,6)+'/'+per.substring(0,4)     	 


      	var fila=[]
      	fila.push(strPeriodo)
      	
	     var valorYVentas=0;
		 var valorYstrVentas=""		   	
		 var valorYGastos=0;
		 var valorYstrGastos=""		   	
		 var valorYRecaudacion=0;
		 var valorYstrRecaudacion=""	
		  for(j=0;j<vendedores.length;j++)
		  { 	      	
				if(id_vend==vendedores[j].id_vendedor)
				{
					var precio_dolar=parseFloat(vendedores[j].dolar)
					if($('lista1').checked)
					{ 
					valorYVentas+=parseFloat(vendedores[j].acumulado_lista1)								      					
					}	
	  				if($('lista2').checked)
	  				{
	  					valorYVentas+=parseFloat(vendedores[j].acumulado_lista2)
	  				}	
	  				if($('lista3').checked)
	  				{
	  					valorYVentas+=parseFloat(vendedores[j].acumulado_lista3)
	  				}	
	  				if($('lista4').checked)
	  				{
	  					valorYVentas+=parseFloat(vendedores[j].acumulado_precio_manual)
	  				}	
	  				if($('dolar').checked && precio_dolar>0)
	  				{
	  					valorYVentas/=precio_dolar
	  				}
	  				valorYstrVentas=valorYVentas.toFixed(2)
	  				valorYVentas=parseFloat(valorYstrVentas)


	  				valorYGastos=parseFloat(vendedores[j].Gastos)*-1
	  				valorYRecaudacion=parseFloat(vendedores[j].recaudacion)
	  				if($('dolar').checked && precio_dolar>0)
	  				{
	  					valorYGastos/=precio_dolar
	  					valorYRecaudacion/=precio_dolar
	  				}
	  				valorYstrGastos=valorYGastos.toFixed(2)
	  				valorYGastos=parseFloat(valorYstrGastos)

	  				valorYstrRecaudacion=valorYRecaudacion.toFixed(2)
	  				valorYRecaudacion=parseFloat(valorYstrRecaudacion)
	  				break;
				}

			}
			var simbolo_peso="$ "	
			if($('dolar').checked )						      	
			{
			simbolo_peso="u$u "	
			}

			//arrValorVentaY={v:valorYVentas, f:simbolo_peso+valorYstrVentas}      	
			fila.push(valorYVentas);
			//arrValorGastosY={v:valorYGastos, f:simbolo_peso+valorYstrGastos}      	
			fila.push(valorYGastos);
			fila.push(valorYRecaudacion);
				    
		   
	     arrRows.push(fila);
     
      }

     
     // data.addRows(arrRows);
      strTitulo+=" (ventas en pesos)"
     if($('dolar').checked)
     {
     	strTitulo+="(ventas en dolares)"
     }

   // var chart = new google.visualization.BarChart(arrRows);

	 var data = google.visualization.arrayToDataTable(arrRows);

  /* var options = {
          chart: {
            title: 'Company Performance',
            subtitle: 'Sales, Expenses, and Profit: 2014-2017',
          },
          bars: 'horizontal' // Required for Material Bar Charts.
        };*/
      var options = {        
        title: strTitulo,
        hAxis: {
          title: 'Periodo',
          minValue: 0
        },
        vAxis: {
        	title: 'Ventas/Gastos'
        },
        bars: 'vertical',
         bar: {groupWidth: "30%"},
        //width: 780,
        width: 780,
        height:800,
        legend: 'none'
      };


var simbolo_peso="$ "	
			if($('dolar').checked )						      	
			{
			simbolo_peso="u$u "	
			}
   options['vAxis']['format'] = simbolo_peso+' #,##0.00';
new google.charts.Bar(document.getElementById('chart_div')).draw(data,
                google.charts.Bar.convertOptions(options)
            );

 /*new google.visualization.BarChart(document.getElementById('chart_div')).
            draw(data,
                options
            );*/
 

     

      
    }//if




}//fin del check barras