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
height: 19cm;
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
margin-top: 990px;
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
margin-top: 72px;
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
	</style>
</head>
<body>
<xsl:variable name="filas" select="count(/xml/rs:data/z:row)"/>
<xsl:for-each select="/xml/rs:data/z:row">
  <xsl:variable name="pos" select="position()-1"/>
  <xsl:variable name="totalporpagina" select="40"/>
  <xsl:if test="(($pos mod $totalporpagina) = 0)">
    <xsl:variable name="pagina" select="floor($pos div $totalporpagina)+1"/>
    <br />
    <div class="hoja">
      <div style="font-size: 20px;font-weight: bold;margin-left: 443px;margin-top: 4px;position: absolute;">
          <div style="width: 100%">
            REMITO DE ENTRADA
          </div>
          <div style="width: 100%;text-align:right" >
            N° <xsl:value-of select="format-number(/xml/rs:data/z:row[position()=1]/@id_remito,'00000000')"/>
          </div>
      </div>
      <div class="header">
          <div>
            <table style="width: 100%">
              <thead></thead>
              <tbody>
                <tr>
                  <td style="border-right-style: groove;width: 54%;">
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
                        <tr><td>Cuit:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@empresa_cuil"/></td></tr>
                      </tbody>
                    </table>
                  </td>
                  <td>
                   <table   class="tblcliente2">          
                      <tbody>
                      <tr><td colspan="2" ><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@empresa_condicion"/></td></tr>
                      <tr><td>Ingresos brutos:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@ingresos_brutos"/></td></tr>
                      <tr><td>Inicio de actividades:</td><td>
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
            <xsl:variable name="datestr">
              <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@fecha" />
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
                  <tr>
                    <td>FECHA:</td>
                    <td>
                      <xsl:value-of select="$dd" />
                      <xsl:value-of select="'/'" />
                      <xsl:value-of select="$mm" />
                      <xsl:value-of select="'/'" />
                      <xsl:value-of select="$yyyy" />
                    </td>
                  </tr>
                  <tr>
                    <td>OPERADOR:</td><td>
                    <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@usuario_estado"/> 
                    </td>
                  </tr>
                  <tr>
                    <td>PROVEEDOR:</td><td> <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@proveedor"/> </td>
                  </tr>
                  <tr>
                    <td>DETALLE:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@detalle"/> </td>
                  </tr>
            </table>  
          </div>    
      </div>
      <div class="detalle" >
        <table class="tbldetalle">          
            <thead>          
              <tr>
                <th style="width:55%">Articulo</th>
                <th style="width:15%;text-align: right">cant. ant.</th>
                <th style="width:15%;text-align: right">cant.</th>
                <th style="width:15%;text-align: right;padding-right: 3px">stock</th>
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
          <th></th><th></th><th></th><th></th><th>TOTAL REMITO</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <td>
          </td>
          <td></td>
          <td></td>
          <td></td>
          <td><xsl:value-of select="sum(/xml/rs:data/z:row/@cantidad)"/> </td>      
          </tr>
          <tr>
            <td colspan="5" style="text-align: right">Pagina <xsl:value-of select="$pagina"/> </td>
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
        <td><xsl:value-of select="@articulo"/></td>        
        <td style="text-align: right;"><xsl:value-of select="format-number(@stock_ant,'000')"/></td>
        <td style="text-align: right;"><xsl:value-of select="format-number(@cantidad,'000')"/></td>
        <td style="text-align: right;"><xsl:value-of select="format-number(@stock,'000')"/></td>
    </tr>    
     </xsl:for-each>
</xsl:template>
</xsl:stylesheet>