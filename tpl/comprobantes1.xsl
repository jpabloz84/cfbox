<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882"
				xmlns:rs='urn:schemas-microsoft-com:rowset' 
				xmlns:z='#RowsetSchema'
				xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
	            xmlns:foo="http://www.broadbase.com/foo" extension-element-prefixes="msxsl" exclude-result-prefixes="foo">
  <xsl:output method="html" version="1.0" encoding="Latin-1" omit-xml-declaration="yes"/>
  <xsl:template match="/">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title></title>
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
border-style: groove;
margin-left: -3;
position: absolute;
margin-top: 265px;
border-color:#F5F5F5
}
.footer{
width: 20cm;
height: 61px;
border-style: groove;
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
<br />
<div class="hoja">
  <div style="font-size: 25px;font-weight: bold;margin-left: 419px;margin-top: 4px;position: absolute;">
      <div style="width: 100%">
      	<xsl:value-of select="/xml/rs:data/z:row[position()=1]/@tipo_comp"/>
      </div>
      <div style="width: 100%">
      	<xsl:choose>
			<xsl:when test="/xml/rs:data/z:row[position()=1]/@afip = 1">
			<xsl:value-of select="/xml/rs:data/z:row[position()=1]/@nro_talonario"/>-<xsl:value-of select="/xml/rs:data/z:row[position()=1]/@nro_comp"/>
			</xsl:when>					
			<xsl:otherwise>
				<xsl:value-of select="/xml/rs:data/z:row[position()=1]/@id_comp"/>
			</xsl:otherwise>
		</xsl:choose>
      </div>
  </div>
  <div class="header">
      <div>
        <table style="width: 100%">
        <tr>
          <td style="border-right-style: groove;">
            <table style="width: 100%;margin-bottom:10px">
                <tr><td style="font-size: 25px;text-align: center;" >{nombre del comercio}</td></tr>
                <tr><td style="font-size: 20px;text-align: center;">{nombre del duenio}</td></tr>
            </table>
              <table class="tblnegocio">
                <tr><td>Direccion:</td><td>{direccion}</td></tr>
                <tr><td>Localidad:</td><td>{localidad}-{provincia}</td></tr>
                <tr><td>Email:</td><td>{email}</td></tr>
                <tr><td>Teléfono:</td><td>{telefono}</td></tr>
                <tr><td>Cuit:</td><td>{cuit}</td></tr>
            </table>
          </td>
          <td>
            <table   class="tblcliente2">          
              <tr><td colspan="2" >{condicion_iva}</td></tr>
              <tr><td>Ingresos brutos:</td><td>{ingresos_brutos}</td></tr>
              <tr><td>Inicio de actividades:</td><td>{dd/mm/yyy}</td></tr>          
            </table>
          </td>      
        </tr>
      </table>  
      </div>
      <div style="width: 100%;border-top-style: double; ">
        <table class="tblcliente">          
              <tr>
                <td>Apellido y nombre:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@strnombrecompleto"/></td>
              </tr>
              <tr>
                <td>Domicilio:</td><td>
                <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@domicilio"/> - <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@descripcion_loc"/>
                (<xsl:value-of select="/xml/rs:data/z:row[position()=1]/@descripcion_pro"/>) 
                </td>
              </tr>
              <tr>
                <td>CUIT:</td><td> <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@cuit"/> </td>
              </tr>
              <tr>
                <td>Condicion:</td><td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@condicion"/> </td>
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
            <xsl:apply-templates select="xml/rs:data/z:row">
			</xsl:apply-templates>             
        </tbody>
      </table>  
  </div>
  <div class="footer">
  <table class="tblfooter">
    <thead>
      <tr>
      <th>CAE</th><th></th><th>NETO</th><th>IVA</th><th>TOTAL</th>
      </tr>
    </thead>
    <body>
      <tr>
      <td><xsl:value-of select="/xml/rs:data/z:row[position()=1]/@cae"/> </td>
      <td></td>
      <td>$ <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@importe_neto"/> </td>
      <td>$ <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@importe_iva"/> </td>
      <td>$ <xsl:value-of select="/xml/rs:data/z:row[position()=1]/@importe_total"/> </td>      
      </tr>
      <tr>
        <td colspan="5" style="text-align: right">Pagina 1</td>
        </tr>
    </body>
  </table>    
  </div>
</div>
</body>
</html>
</xsl:template>  
	<xsl:template match="z:row">
	<xsl:variable name="pos" select="position()"/>
		<tr>
	      <td><xsl:value-of select="@detalle"/></td>
	      <td style="text-align: right;">$ <xsl:value-of select="@precio_base"/></td>
	      <td style="text-align: right;">$ <xsl:value-of select="@precio_iva"/></td>
	      <td style="text-align: right;">$ <xsl:value-of select="@precio_venta"/></td>
	      <td style="text-align: right;"><xsl:value-of select="@cantidad"/></td>
	      <td style="text-align: right;">$ <xsl:value-of select="@importe_item"/></td>
	    </tr>
	</xsl:template>
</xsl:stylesheet>