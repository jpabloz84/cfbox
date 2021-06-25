<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:msxsl="urn:schemas-microsoft-com:xslt"
				xmlns:foo="http://www.broadbase.com/foo" extension-element-prefixes="msxsl" exclude-result-prefixes="foo">

	<msxsl:script language="javascript" implements-prefix="foo">
		<![CDATA[	
		
		   	function str_to_script(cad)
		      {
		      var strreg = "\n"
		      var reg = new RegExp(strreg, "ig")
		      cad = cad.replace(reg, '\\n')
    		  
		      strreg = "'"
		      reg = new RegExp(strreg, "ig")
		      cad = cad.replace(reg, "\\'")
    		  
		      return cad
		      }
		  
		    function str_to_html(cad)
		      {
		      var strreg = "\n"
		      var reg = new RegExp(strreg, "ig")
		      cad = cad.replace(reg, '<br/>')
    		  
		      return cad
		      }  
		      
		    function eliminar_salto_linea(cad)
		     {
		      var strreg = "\n"
		      var reg = new RegExp(strreg, "ig")
		      cad = cad.replace(reg, ' ')
    		  
		      return cad
		     }    
            
            function formatear_aviso(str)
            {
             var max_caracteres = 35
             var bn = str.indexOf("\n")
             if (bn == -1)
               bn = 35
             if (bn >= max_caracteres) 
               {
               if (str.length >= 35)
                 {
                 for (var i = 35; i > 0; i--)
                   if (str.substr(i, 1) == ' ') 
                     {
                      bn = i
                      break    
                     }
                 }    
                 else    
                   bn = str.length
               if (i == 0)
                 bn = 35
               }
             
              str = "<b>" + str.substr(0,bn+1) + "</b>" +  str.substr(bn+1, str.length - bn)
              
              str = eliminar_salto_linea(str)
             
             return str
            
            }
            
			function rellenar_izq(numero, largo, relleno)
			{
			if (typeof(numero) == 'object')
			  numero = String(numero)
			var strNumero = numero.toString()
			if (strNumero.length > largo)
			  strNumero = strNumero.substr(1, largo)
			while(strNumero.length < largo)
			  strNumero = relleno + strNumero.toString() 
			return strNumero
			}
			
			function entero(numero)
			{
			var nro_entero = Math.floor(parseFloat(numero))
			return nro_entero
			}
			
			function decimal(numero)
			{
			numero = parseFloat(numero)
			var nro_entero = Math.floor(numero)
			numero = numero - nro_entero
			var nro_dec = Math.floor(numero * 100)
			return nro_dec
			}	
			
			function rellenar_der(numero, largo, relleno)
			{
			if (typeof(numero) == 'object')
			  numero = String(numero)
			var strNumero = numero.toString()
			if (strNumero.length > largo)
			  strNumero = strNumero.substr(1, largo)
			  
			while(strNumero.length < largo)
			  strNumero = strNumero.toString() + relleno
			return strNumero
			}
			
			
			 function parseFecha(strFecha)
			{
				var a = strFecha.replace('-', '/').replace('-', '/').replace('T', ' ') + '.'
				a = a.substr(0, a.indexOf('.'))
				var fe = new Date(Date.parse(a))
				return fe
			}
		//modo 1 = dd/mm/yyyy
        //modo 2 = mm/dd/yyyy
        //function FechaToSTR(objFecha, modo)
		function FechaToSTR(cadena)
          {
		  var objFecha = parseFecha(cadena)
		  if (isNaN(objFecha.getDate()))
		     return ''
		  var dia
		  var mes
		  var anio
		  if (objFecha.getDate() < 10)
		     dia = '0' + objFecha.getDate().toString()
		  else
		     dia = objFecha.getDate().toString() 
		  
		  if ((objFecha.getMonth() +1) < 10)
		     mes = '0' + (objFecha.getMonth()+1).toString()
		  else
		     mes = (objFecha.getMonth()+1).toString() 	 
		  anio = objFecha.getFullYear()  
          var modo = 1
          if (modo == 1) 
            return dia + '/' + mes + '/' + anio
          else
            return  mes + '/' + dia + '/' + anio
          }	 
          
    function HoraToSTR(cadena)
          {
		  var objFecha = parseFecha(cadena)
		  if (isNaN(objFecha.getDate()))
		     return ''
		  var hora
		  var minuto
		  var segundo
		  if (objFecha.getHours() < 10)
		     hora = '0' + objFecha.getHours().toString()
		  else
		     hora = objFecha.getHours().toString() 
		  
		  if ((objFecha.getMinutes() +1) < 10)
		     minuto = '0' + objFecha.getMinutes().toString()
		  else
		     minuto = objFecha.getMinutes().toString() 	 
         
      if ((objFecha.getSeconds() +1) < 10)
		     segundo = '0' + objFecha.getSeconds().toString()
		  else
		     segundo = objFecha.getSeconds().toString() 	    
         
		  anio = objFecha.getFullYear()  
          var modo = 1
          if (modo == 1) 
            return hora + ':' + minuto + ':' + segundo
          else
            return hora + ':' + minuto + ':' + segundo
          }
          
       function Mayusculas(texto)
        {
        return texto.toUpperCase()
        }
        
       function fecha_vencida(cadena)
		      {
		        var hoy = new Date();
		        var man = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
		        var objFecha = parseFecha(cadena)
		        var res = objFecha < man  
		        return res 
		      }    
          
    function replace(cad, buscar, remplazar)
           {
           cad = cad.toString()
           var re = new RegExp('\\\\',"ig")
           buscar = buscar.replace(re, '\\\\')
           var re = new RegExp(buscar,"ig")
           res = cad.replace(re, remplazar);
           return res
           }          

function cod_reemplazar(cade)
  {
  var cars = new Array()
  cars[0] = {}
  cars[0]['original'] = 'á'
  cars[0]['reemplazo'] = 'a'
  cars[1] = {}
  cars[1]['original'] = 'é'
  cars[1]['reemplazo'] = 'e'
  cars[2] = {}
  cars[2]['original'] = 'í'
  cars[2]['reemplazo'] = 'i'
  cars[3] = {}
  cars[3]['original'] = 'ó'
  cars[3]['reemplazo'] = 'o'
  cars[4] = {}
  cars[4]['original'] = 'ú'
  cars[4]['reemplazo'] = 'u'
  cars[5] = {}
  cars[5]['original'] = 'ñ'
  cars[5]['reemplazo'] = 'n'
  cars[6] = {}
  cars[6]['original'] = 'º'
  cars[6]['reemplazo'] = ''
  cars[7] = {}
  cars[7]['original'] = 'Á'
  cars[7]['reemplazo'] = 'A'
  cars[8] = {}
  cars[8]['original'] = 'É'
  cars[8]['reemplazo'] = 'E'
  cars[9] = {}
  cars[9]['original'] = 'Í'
  cars[9]['reemplazo'] = 'I'
  cars[10] = {}
  cars[10]['original'] = 'Ó'
  cars[10]['reemplazo'] = 'O'
  cars[11] = {}
  cars[11]['original'] = 'Ú'
  cars[11]['reemplazo'] = 'U'
  cars[12] = {}
  cars[12]['original'] = 'Ñ'
  cars[12]['reemplazo'] = 'N'
  
  var strreg = ""
  var reg
  for (var i = 0; i < 13; i++)
    {
    reg = new RegExp(cars[i]['original'], 'ig')
    cade = cade.replace(reg, cars[i]['reemplazo'])
    }
  
  return cade
}



 function solo_letras_numeros(cade)
{
			  var cars = new Array()
			  cars[0] = {}
			  cars[0]['original'] = 'á'
			  cars[0]['reemplazo'] = 'a'
			  
			  cars[1] = {}
			  cars[1]['original'] = 'é'
			  cars[1]['reemplazo'] = 'e'
			  
			  cars[2] = {}
			  cars[2]['original'] = 'í'
			  cars[2]['reemplazo'] = 'i'
			  
			  cars[3] = {}
			  cars[3]['original'] = 'ó'
			  cars[3]['reemplazo'] = 'o'
			  
			  cars[4] = {}
			  cars[4]['original'] = 'ú'
			  cars[4]['reemplazo'] = 'u'
			  
			  cars[5] = {}
			  cars[5]['original'] = 'ñ'
			  cars[5]['reemplazo'] = 'n'
			  
			  cars[6] = {}
			  cars[6]['original'] = 'º'
			  cars[6]['reemplazo'] = ''
			  
			  cars[7] = {}
			  cars[7]['original'] = 'Á'
			  cars[7]['reemplazo'] = 'A'
			  
			  cars[8] = {}
			  cars[8]['original'] = 'É'
			  cars[8]['reemplazo'] = 'E'
			  
			  cars[9] = {}
			  cars[9]['original'] = 'Í'
			  cars[9]['reemplazo'] = 'I'
			  
			  cars[10] = {}
			  cars[10]['original'] = 'Ó'
			  cars[10]['reemplazo'] = 'O'
			  
			  cars[11] = {}
			  cars[11]['original'] = 'Ú'
			  cars[11]['reemplazo'] = 'U'
			  
			  cars[12] = {}
			  cars[12]['original'] = 'Ñ'
			  cars[12]['reemplazo'] = 'N'
			  
			  cars[13] = {}
			  cars[13]['original'] = 'ñ'
			  cars[13]['reemplazo'] = 'n'
			  
			  cars[14] = {}
			  cars[14]['original'] = 'à'
			  cars[14]['reemplazo'] = 'a'
			  
			  cars[15] = {}
			  cars[15]['original'] = 'À'
			  cars[15]['reemplazo'] = 'A'
			  
			  cars[16] = {}
			  cars[16]['original'] = 'È'
			  cars[16]['reemplazo'] = 'E'
			  
			  cars[17] = {}
			  cars[17]['original'] = 'è'
			  cars[17]['reemplazo'] = 'e'
			  
			  cars[18] = {}
			  cars[18]['original'] = 'ì'
			  cars[18]['reemplazo'] = 'i'
			  
			  cars[19] = {}
			  cars[19]['original'] = 'Ì'
			  cars[19]['reemplazo'] = 'I'
			  
			  cars[20] = {}
			  cars[20]['original'] = 'ò'
			  cars[20]['reemplazo'] = 'o'
			  
			  cars[21] = {}
			  cars[21]['original'] = 'Ò'
			  cars[21]['reemplazo'] = 'O'
			  
			  cars[22] = {}
			  cars[22]['original'] = 'ù'
			  cars[22]['reemplazo'] = 'u'
			  
			  cars[23] = {}
			  cars[23]['original'] = 'Ù'
			  cars[23]['reemplazo'] = 'U'
			  
			 
			  
			  var strreg = ""
			  var reg
			  for (var i = 0; i < 23; i++)
				{    
				reg = new RegExp(cars[i]['original'], 'g')
				cade = cade.replace(reg, cars[i]['reemplazo'])
				}
				
				//caracteres aceptados, de los cuales elimino los que no estan contemplados
				 /*ABCDEFGHIJKLMNÑOPQRSTUVWXYZÜË,'/0123456789Ä()*/
			  var re=/[^a-zA-Z0-9ÜËÄ()\s]/g
			  var result = cade.replace(re, "");
			  
			  var re = /([\ \t]+(?=[\ \t])|^\s+|\s+$)/g
			  var result = result.replace(re, "");			  
			  
			  return result
}
			


		]]>
	</msxsl:script>
</xsl:stylesheet>