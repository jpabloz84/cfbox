<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882"
				xmlns:rs='urn:schemas-microsoft-com:rowset' 
				xmlns:z='#RowsetSchema'
				xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
				xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
				xmlns="urn:schemas-microsoft-com:office:spreadsheet"
			  xmlns:o="urn:schemas-microsoft-com:office:office"
			  xmlns:x="urn:schemas-microsoft-com:office:excel"
			  xmlns:html="http://www.w3.org/TR/REC-html40"
	      xmlns:foo="http://www.broadbase.com/foo" extension-element-prefixes="msxsl" exclude-result-prefixes="foo"
				xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
				>
	<xsl:output encoding="UTF-16"/>
  <msxsl:script language="javascript" implements-prefix="foo">
    <![CDATA[
    var numHoja = 0
		function getHoja()
      {
      numHoja++
      return "Hoja" + numHoja
      }
		function test_ultima_fila(pos, max)
      {
      return (pos % max == 0)
      }
	  
	 
		]]>
  </msxsl:script>
	<xsl:template match="/">
		<xsl:processing-instruction name="mso-application">progid="Excel.Sheet"</xsl:processing-instruction>
		<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
		 xmlns:o="urn:schemas-microsoft-com:office:office"
		 xmlns:x="urn:schemas-microsoft-com:office:excel"
		 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
		 xmlns:html="http://www.w3.org/TR/REC-html40">
			<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
				<Author></Author>
				<LastAuthor></LastAuthor>
				<Created></Created>
				<Company>.</Company>
				<Version>10.6839</Version>
			</DocumentProperties>
			<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
				<DownloadComponents/>
				<LocationOfComponents HRef="file:///\\"/>
			</OfficeDocumentSettings>
			<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
				<WindowHeight>8835</WindowHeight>
				<WindowWidth>12780</WindowWidth>
				<WindowTopX>360</WindowTopX>
				<WindowTopY>105</WindowTopY>
				<ProtectStructure>False</ProtectStructure>
				<ProtectWindows>False</ProtectWindows>
			</ExcelWorkbook>
			<Styles>
				<Style ss:ID="Default" ss:Name="Normal">
					<Alignment ss:Vertical="Bottom"/>
					<Borders/>
					<Font/>
					<Interior/>
					<NumberFormat/>
					<Protection/>
				</Style>
				<Style ss:ID="s18" ss:Name="Moneda">
					<NumberFormat
					 ss:Format="_ &quot;$&quot;\ * #,##0.00_ ;_ &quot;$&quot;\ * \-#,##0.00_ ;_ &quot;$&quot;\ * &quot;-&quot;??_ ;_ @_ "/>
				</Style>
				<Style ss:ID="s20" ss:Name="Porcentual">
					<NumberFormat ss:Format="0%"/>
				</Style>
				<Style ss:ID="s22">
					<Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
					<Font x:Family="Swiss" ss:Bold="1"/>
				</Style>
				<Style ss:ID="s24">
					<NumberFormat ss:Format="Short Date"/>
				</Style>
				<Style ss:ID="s29" ss:Parent="s20">
					<NumberFormat ss:Format="0.000000"/>
				</Style>
				<Style ss:ID="s62">
					<Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
				</Style>
				<Style ss:ID="s63">
					<Alignment ss:Vertical="Bottom"/>
					<Borders/>
					<Font x:Family="Swiss" ss:Bold="1"/>
					<Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
					<Protection/>
				</Style>
				<Style ss:ID="s77">
					<Borders>
						<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
						<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
						<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
						<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
					</Borders>
					<Font ss:FontName="MS Sans Serif" x:Family="Swiss" ss:Size="8.5"/>
					<NumberFormat ss:Format="&quot;$&quot;\ #,##0.00"/>
				</Style>
				<Style ss:ID="s75">
					<Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
					<Borders>
						<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
						<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
						<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
						<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
					</Borders>
					<Font ss:FontName="MS Sans Serif" x:Family="Swiss" ss:Size="8.5"/>
					<NumberFormat/>
				</Style>
			</Styles>
			<xsl:for-each select="/xml/rs:data/z:row">
				<xsl:variable name="pos" select="position()-1"/>
  			<xsl:variable name="totalporpagina" select="65535"/>
  			<xsl:if test="(($pos mod $totalporpagina) = 0)">
						<Worksheet>
					        <xsl:attribute name="ss:Name">
					          <xsl:value-of select="foo:getHoja()"/>
					        </xsl:attribute> 
									<Table  x:FullColumns="1" x:FullRows="1" ss:DefaultColumnWidth="70">
										<Row ss:StyleID="s22">
											<xsl:apply-templates select="//xml/s:Schema/s:ElementType/s:AttributeType" mode="cabecera1"/>
										</Row>					
										<xsl:call-template name="tpl_detalles" >
                  		<xsl:with-param name="filaini" select="$pos" />
                  		<xsl:with-param name="totalreg" select="$totalporpagina" />
                		</xsl:call-template>				
									</Table>
									<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
										<PageSetup>
											<Header x:Margin="0"/>
											<Footer x:Margin="0"/>
											<PageMargins x:Bottom="0.984251969" x:Left="0.78740157499999996"
											 x:Right="0.78740157499999996" x:Top="0.984251969"/>
										</PageSetup>
										<Print>
											<ValidPrinterInfo/>
											<HorizontalResolution>600</HorizontalResolution>
											<VerticalResolution>600</VerticalResolution>
										</Print>
										<Selected/>
										<Panes>
											<Pane>
												<Number>3</Number>
												<ActiveRow>1</ActiveRow>
											</Pane>
										</Panes>
										<ProtectObjects>False</ProtectObjects>
										<ProtectScenarios>False</ProtectScenarios>
									</WorksheetOptions>
						</Worksheet>
					</xsl:if>
				</xsl:for-each>
			
			
		</Workbook>
	</xsl:template>	

	<xsl:template match="s:AttributeType" mode="cabecera1">
		<Cell  ss:StyleID="s63">
			<Data ss:Type="String">				
                     <xsl:value-of select="foo:formatfila1(string(@name))"/>
			</Data>
		</Cell>
	</xsl:template>

		<xsl:template name="tpl_detalles" match="xml/rs:data/z:row">    
    	<xsl:param name="filaini" />
    	<xsl:param name="totalreg" />
     	<xsl:for-each select="//z:row[((position() &gt; $filaini) and (position() &lt;= ($totalreg+$filaini)))]">
        <xsl:variable name="attr" select="@name" />
				<xsl:variable name="valor" select="string($fila/@*[name() = $attr])"/>
				<xsl:variable name="tipo_dato" select="./s:datatype/@dt:type" />
				<xsl:choose>
					<xsl:when test="$tipo_dato = 'dateTime'">
						<Cell ss:StyleID="s77">
							<!--<Data ss:Type="DateTime">2005-10-21T00:00:00.000</Data>-->
							<xsl:if test="$valor != ''">
								<Data ss:Type="DateTime"><xsl:value-of  select="substring($valor, 1, 19)" />.000</Data>
							</xsl:if>
						</Cell>
					</xsl:when>
					<xsl:when test="$tipo_dato = 'int' or $tipo_dato = 'i2' or $tipo_dato = 'ui1'">
						<Cell  ss:StyleID="s75">
							<xsl:if test="$valor != ''">
								<Data ss:Type="Number"><xsl:value-of  select="$valor" /></Data>
							</xsl:if>
						</Cell>
					</xsl:when>
					<xsl:when test="$tipo_dato = 'number'">
						<Cell ss:StyleID="s77">
							<xsl:if test="$valor != ''">
								<Data ss:Type="Number"><xsl:value-of  select="$valor" /></Data>
							</xsl:if>
						</Cell>
					</xsl:when>
					<xsl:when test="$tipo_dato = 'float'">
						<Cell ss:StyleID="s77">
							<xsl:if test="$valor != ''">
								<Data ss:Type="Number"><xsl:value-of  select="$valor" /></Data>
							</xsl:if>
						</Cell>
					</xsl:when>
					<xsl:otherwise>
						<Cell  ss:StyleID="s77">
							<Data  ss:Type="String"><xsl:value-of  select="$valor" /></Data>
						</Cell>
					</xsl:otherwise>
				</xsl:choose>
     </xsl:for-each>
</xsl:template>

</xsl:stylesheet>