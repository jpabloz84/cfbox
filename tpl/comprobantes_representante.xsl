<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882"
				xmlns:rs="urn:schemas-microsoft-com:rowset"
				xmlns:z="#RowsetSchema"
				xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
	            xmlns:foo="http://www.broadbase.com/foo" extension-element-prefixes="msxsl" exclude-result-prefixes="foo">
  <xsl:output method="html" version="1.0" encoding="utf-8" omit-xml-declaration="yes"/>
  <xsl:template match="/">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>		
	<style>
		
.hoja{
width: 20cm;
height: 28cm;
margin-left:5px;
border-style: groove;
font-family:"arial";
}
.header{
width: 20cm;
height: 7cm;
border-style: groove;
margin-left: -3;
border-top:none;
position: absolute;
border-color:#F5F5F5
}
.detalle{
width: 20cm;
height: 17cm;
margin-left: -3;
position: absolute;
margin-top: 265px;
border-color:#F5F5F5
}
.footer{
width: 20cm;
height: 61px;
margin-left: -3;
border-bottom:none;
position: absolute;
margin-top: 903px;
border-color:#F5F5F5
}

.tbldetalle{
width: 100%;
font-size: 12px;
border-spacing:0px;

}
.tbldetalle th{
font-size: 12px;
font-weight: bold;  
text-align: center;
vertical-align: bottom;
}
.tbldetalle  thead  tr {  
border-bottom: 1px double;
background-color:#CDCDD0 !important;
}
.tbldetalle tbody td{
padding-left: 3px;
}
.tblcliente{
width: 100%;
font-size: 12px;
margin-left:2px;
margin-top: 0px;
border-spacing:2px;
}
.tblcliente td:first-child{
width: 15%
}
.tblcliente td:nth-child(2){
width: 85%
}
.tblnegocio {
width:100%;
font-size: 12px;
}

.tblnegocio td:first-child{
width: 15%
}
.tblnegocio td:nth-child(2){
width: 85%
}

.tblcliente td:first-child{
width: 15%
}
.tblcliente td:nth-child(2){
width: 85%;
text-align: left;
}

.tblcliente2 {
width:100%;
font-size: 12px;
margin-top: 57px;
}
.tblcliente2 td:first-child{
width: 35%
}
.tblcliente2 td:nth-child(2){
width: 65%;
text-align: left;
}
.tblfooter{
width: 100%;
font-size: 12px;
border-spacing:0px;
}
.tblfooter  thead tr{        
background-color:#CDCDD0 !important;
}

.tblfooter  tbody  td {        
text-align: center
}

@media all {
div.saltopagina{
display: none;
}

}

@media print{
div.saltopagina{ 
display:block; 
page-break-before:always;
}
.tbldetalle  thead  tr {    
background-color:#CDCDD0 !important;
}
.tblfooter  thead tr{        
background-color:#CDCDD0 !important;
}
}




.tipofacturaframe{
  animation: none;
    position: absolute;
    border-bottom-style: groove;
    border-left-style: groove;
    border-right-style: groove;
    margin-left: 364;
    margin-top:23;
    text-align: center;
    z-index: 1;
    background-color: white;    
    font-size: 32px;
    font-weight: bolder;
    padding: 10px;
}

.talonario{
font-size: 20px;font-weight: bold;margin-left: 464px;margin-top: 27px;position: absolute;
}

	</style>
</head>
<body>
<xsl:variable name="filas" select="count(/xml/rs:data/z:row)"/>
<xsl:variable name="totalporpagina" select="40"/>
  <xsl:variable name="cantidadpaginas" select="floor($filas div $totalporpagina)+1"/>
<xsl:for-each select="/xml/rs:data/z:row">
  <xsl:variable name="pos" select="position()-1"/>

  <xsl:if test="(($pos mod $totalporpagina) = 0)">
    <xsl:variable name="pagina" select="floor($pos div $totalporpagina)+1"/>
    <br />
    <div class="hoja">
      <div  class="talonario" >
          <div style="width: 100%">
            <xsl:choose>
              <xsl:when test="(/xml/rs:data/z:row[position()=1]/@afip = 1) and (/xml/rs:data/z:row[position()=1]/@cae != '')">
                <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@tipo_comp_str"/>
              </xsl:when>
            </xsl:choose>            
          </div>
          <div style="width: 100%;text-align:right" >
            <xsl:choose>
              <xsl:when test="/xml/rs:data/z:row[position()=1]/@afip = 1">
                 <xsl:value-of select="format-number(/xml/rs:data/z:row[position()=1]/@nro_talonario,'0000')"/>-<xsl:value-of select="format-number(/xml/rs:data/z:row[position()=1]/@nro_comp,'00000000')"/>
              </xsl:when>         
              <xsl:otherwise>
                ORDEN DE PEDIDO <br/><xsl:value-of select="format-number(/xml/rs:data/z:row[position()=1]/@id_comp,'00000000')"/> 
              </xsl:otherwise>
        </xsl:choose>          
          </div>
      </div>
      <div class="header">
        <div class="tipofacturaframe">
         <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@tipo"/>
        </div>
          <div>
            <table style="width: 100%">
              <thead>
                <th colspan="2" style="border-bottom-style: groove;text-align: center;"><span style="margin-left: 25px;">ORIGINAL</span></th>
              </thead>
              <tbody>
                <tr>
                  <td style="border-right-style: groove;">
                    <table style="width: 100%;margin-bottom:10px">
                      <thead></thead>
                       <tbody>
                        <tr><td style="font-size: 25px;text-align: center;" ><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@empresa"/></td></tr>
                        <tr><td style="font-size: 20px;text-align: center;"></td></tr>
                      </tbody>
                    </table>
                      <table class="tblnegocio">
                       <thead></thead>
                       <tbody>
                        <tr><td>Direccion:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@sucursal_direccion"/></td></tr>
                        <tr><td>Localidad:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@localidad_sucursal"/> -(PROVINCIA DE <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@provincia_sucursal"/> )</td></tr>
                        
                        <tr><td>Teléfono:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@sucursal_telefono"/></td></tr>
                        <tr><td colspan="2" ><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@empresa_condicion"/></td></tr>
                      </tbody>
                    </table>
                  </td>
                  <td>
                    <table   class="tblcliente2">          
                      <tbody>
                        <tr>
                          <td colspan="2" style="text-align: left">fecha comprobante
                            <xsl:variable name="datecompstr">
                            <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@fe_creacion" />
                          </xsl:variable>
                          <xsl:variable name="yyyyc">
                          <xsl:value-of select="substring($datecompstr,1,4)" />
                          </xsl:variable>
                          <xsl:variable name="mmc">
                             <xsl:value-of select="substring($datecompstr,6,2)" />
                          </xsl:variable>
                          <xsl:variable name="ddc">
                             <xsl:value-of select="substring($datecompstr,9,2)" />
                          </xsl:variable>
                          <xsl:variable name="hsc">
                             <xsl:value-of select="substring($datecompstr,12,5)" />
                          </xsl:variable>            
                          <xsl:value-of select="$ddc" />
                          <xsl:value-of select="'/'" />
                          <xsl:value-of select="$mmc" />
                          <xsl:value-of select="'/'" />
                          <xsl:value-of select="$yyyyc" />
                          <xsl:text> </xsl:text>
                          <xsl:value-of select="$hsc" />
                        </td>
                       </tr>
                      <tr><td colspan="2">Cuit: <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@empresa_cuil"/></td>
                        </tr>
                      <tr><td>Ingresos brutos:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@ingresos_brutos"/></td></tr>
                      <tr><td colspan="2">Inicio de actividades:</td><td>
                        <xsl:variable name="dateiniciostr">
                        <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@inicio_actividades" />
                        </xsl:variable>
                        <xsl:variable name="yyyyi">
                        <xsl:value-of select="substring($dateiniciostr,1,4)" />
                        </xsl:variable>
                        <xsl:variable name="mmi">
                           <xsl:value-of select="substring($dateiniciostr,6,2)" />
                        </xsl:variable>
                        <xsl:variable name="ddi">
                           <xsl:value-of select="substring($dateiniciostr,9,2)" />
                        </xsl:variable>
                          <xsl:value-of select="$ddi" />
                          <xsl:value-of select="'/'" />
                          <xsl:value-of select="$mmi" />
                          <xsl:value-of select="'/'" />
                          <xsl:value-of select="$yyyyi" />
                      </td></tr>          
                     </tbody>
                    </table>
                  </td>      
                </tr>
              </tbody>
          </table>  
          </div>
          <div style="width: 100%;border-top-style: double; ">
            <table class="tblcliente">          
                  <tr>
                    <td>Apellido y nombre:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@strnombrecompleto_representante"/> (representado por <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@strnombrecompleto"/>)</td>
                  </tr>
                  <tr>
                    <td>Domicilio:</td><td>
                    <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@domicilio_representante"/> - <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@descripcion_loc_representante"/>
                    (<xsl:value-of select="/xml/rs:data/z:row[position()=1]/@descripcion_pro_representante"/>) 
                    </td>
                  </tr>
                  <tr>
                    <td>CUIT:</td><td> <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@cuit_representante"/> </td>
                  </tr>
                  <tr>
                    <td>Condicion iva:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@condicion_representante"/> </td>
                  </tr>
                   <tr>
                    <td>Condicion venta:</td>
                    <td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@condicion_venta"/> </td>
                  </tr>
            </table>  
          </div>    
      </div>
      <div class="detalle" >
        <table class="tbldetalle">          
            <thead>          
              <tr>
                <th style="width:50%">Producto</th>
                <th style="width:10%;text-align: right">precio/u</th>
                <th style="width:10%;text-align: right">iva/u</th>
                <th style="width:10%;text-align: right">venta/u</th>
                <th style="width:5%;text-align: right">cant</th>
                <th style="width:15%;text-align: right;padding-right: 3px">Total</th>
              </tr>
            </thead>
            <tbody>
                <xsl:call-template name="tpl_detalles" >
                  <xsl:with-param name="filaini" select="$pos" />
                  <xsl:with-param name="totalreg" select="$totalporpagina" />
                </xsl:call-template>
            </tbody>
          </table>  
      </div>
      <div class="footer">
      <table class="tblfooter">
        <thead>
          <tr>
          <th style="width:56%">PAGOS</th><th style="width:13%">NETO</th><th style="width:13%">IVA</th><th style="width:18%">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <td>
            <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@strpagos"/>
          </td>          
          <td>$ <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@importe_neto"/> </td>
          <td>$ <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@importe_iva"/> </td>
          <td>$ <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@importe_total"/> </td>      
          </tr>
          <tr>
            <td colspan="4">
            <table style="width: 100%">
              <tr>
                <td style="width: 20%">
                  <xsl:choose>
                  <xsl:when test="/xml/rs:data/z:row[position()=1]/@pathqr != ''">
                   <img width="104px" height="104"  >
                     <xsl:attribute name="src">
                      ../<xsl:value-of select="/xml/rs:data/z:row[position()=1]/@pathqr"/>
                      </xsl:attribute>
                  </img>
                </xsl:when>
              </xsl:choose>
                 
                </td>
                <td style="width:60%">
                   <xsl:choose >
                    <xsl:when test="/xml/rs:data/z:row[position()=1]/@pathqr != ''">
                        <img style="float: left;position: relative;"  height="50" src="data:image/png;base64,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
    "></img>
                          <span style="display: block;float: left;position: relative;font-weight: bold;font-size: 9px;text-align:left">
                          Comprobante autorizado<br/>
                          Esta administración Federal no se responsabiliza por los datos  ingresado en el detalle de la operación
                          </span>
                    </xsl:when>
                  </xsl:choose>                   
                </td>
                <td style="width:20%">
                   <span style="position:relative;float: left;font-weight: bold;font-size: 10px;text-align: left;">
                    Pág. <xsl:value-of select="$pagina"/>/<xsl:value-of select="$cantidadpaginas"/><br/> 
                    <xsl:choose >
                      <xsl:when test="/xml/rs:data/z:row[position()=1]/@cae != ''">
                        CAE N°: <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@cae"/> <br/>
                    Fecha de vto. cae: 
                        <xsl:variable name="datestr">
                           <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@fe_vencimiento" />
                        </xsl:variable>
                        <xsl:variable name="yyyy">
                        <xsl:value-of select="substring($datestr,1,4)" />
                        </xsl:variable>
                        <xsl:variable name="mm">
                           <xsl:value-of select="substring($datestr,6,2)" />
                        </xsl:variable>
                        <xsl:variable name="dd">
                           <xsl:value-of select="substring($datestr,9,2)" />
                        </xsl:variable>             
                        <xsl:value-of select="$dd" />
                        <xsl:value-of select="'/'" />
                        <xsl:value-of select="$mm" />
                        <xsl:value-of select="'/'" />
                        <xsl:value-of select="$yyyy" />
                      </xsl:when>
                    </xsl:choose>                    
                   </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        </tbody>
      </table>    
      </div>
    </div>
  </xsl:if>
</xsl:for-each>
</body>
</html>
</xsl:template>

<xsl:template name="tpl_detalles" match="xml/rs:data/z:row">    
    <xsl:param name="filaini" />
    <xsl:param name="totalreg" />
     <xsl:for-each select="//z:row[((position() &gt; $filaini) and (position() &lt;= ($totalreg+$filaini)))]"> 
      <tr>
        <td><xsl:value-of select="@detalle"/></td>
        <td style="text-align: right;">$ <xsl:value-of select="@precio_base"/></td>
        <td style="text-align: right;">$ <xsl:value-of select="@precio_iva"/></td>
        <td style="text-align: right;">$ <xsl:value-of select="@precio_venta"/></td>
        <td style="text-align: right;"><xsl:value-of select="@cantidad"/></td>
        <td style="text-align: right;">$ <xsl:value-of select="@importe_item"/></td>
    </tr>    
     </xsl:for-each>
</xsl:template>
</xsl:stylesheet>