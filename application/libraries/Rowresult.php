<?php
class Rowresult
{	

private $num_rows=0;  //cantidad de filas obtenida en una consulta
private $eof=false; //fin de registros
private $indexRow=0; //fin de registros
private $numError;
private $descError;
private $cn;
private $mysqli;
private $resultado;
private $fila=array();	
private $tiposXLS=array(0=>'decimal',1=>'boolean',2=>'short',3=>'long',4=>'float', 5=>'double', 6=>'null',7=>'timestamp',8 => 'unsignedLong',9=>'int',10=>'date',11=>'time',12=>'datetime',13=>'gYear',14=>'date',15=>'string',16=>'boolean',17=>'decimal',18=>'enum',19=>'set',20=>'blob',21=>'blob',22=>'blob', 23=>'blob',24=>'string', 25=>'string',253=>'string',26=>'geometry');
	function __construct()
	{	$this->numError=0;
		$this->indexRow=0;
		$this->num_rows=0;
		
	}
	function __destruct()
	{	$this->resultado=null;
	}


	function Getvalue($columna)
	{$valor=null;
	 
		if(!$this->eof)
		{
			/*if(is_int ($columna))
			{
				$fila = $this->resultado->fetch_row();
			}
			else
			{
				$fila = $this->resultado->fetch_assoc();
			}*/
			
			$valor=$this->fila[$columna];
		}
		return $valor;
	}
	function Next() //siguiente fila de un conjunto de resultados
	{$this->indexRow++;
		if($this->indexRow >= ($this->num_rows()))
		{
			$this->eof=true;
			$this->resultado->free_result();				
		}else
		{
		$this->resultado->data_seek($this->indexRow);	
		$this->fila=$this->resultado->result_array();
		}
		
	}
	function Eof()
	{

		return $this->eof;
	}

	function GetInfoCampos()
	{$info_campo="";
		$this->numError=0;
		try {
    	 $info_campo = $this->resultado->list_fields();
		} catch (Exception $e) {
			$this->numError=6;
    	$this->descError='Excepción capturada: '.$e->getMessage();
		}

		
		 return $info_campo;

        /*COMO SE CONSULTAN LOS CAMPOS
        foreach ($info_campo as $valor) {
            printf("Nombre:           %s\n",   $valor->name);
            printf("Tabla:            %s\n",   $valor->table);
            printf("Longitud máx.:    %d\n",   $valor->max_length);
            printf("Longitud:         %d\n",   $valor->length);
            printf("Nº conj. caract.: %d\n",   $valor->charsetnr);
            printf("Banderas:         %d\n",   $valor->flags);
            printf("Tipo:             %d\n\n", $valor->type);
        }*/
	}

	function NumRows()
	{
		return $this->num_rows;
	}

	function GetRsData()
	{

		$this->numError	=0;
		$strXML="";
		$cabecera='<xml xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882" 
		xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
		xmlns:rs="urn:schemas-microsoft-com:rowset" 
		xmlns:z="#RowsetSchema"><s:Schema id="RowsetSchema"><s:ElementType name="row" content="eltOnly" rs:updatable="true">';
		$infoCampos=$this->GetInfoCampos();
		$columnas=array();
		$strCuerpo="";
		$pos=1;
		foreach ($infoCampos as $valor) {
			$name=$valor->name;
			$columnas[]=$name;			
			 $type=$valor->type;			
			$strtype= "dt:type='".$this->tiposXLS[$type]."'";
			
			/*tipos de datos
	 	MYSQL_TYPE_DECIMAL [1] => MYSQL_TYPE_TINY [2] => MYSQL_TYPE_SHORT [3] => MYSQL_TYPE_LONG [4] => MYSQL_TYPE_FLOAT [5] => MYSQL_TYPE_DOUBLE [6] => MYSQL_TYPE_NULL [7] => MYSQL_TYPE_TIMESTAMP [8] => MYSQL_TYPE_LONGLONG [9] 
	 	=> MYSQL_TYPE_INT24 [10] => MYSQL_TYPE_DATE [11] => MYSQL_TYPE_TIME [12] => MYSQL_TYPE_DATETIME [13] => MYSQL_TYPE_YEAR [14] 
	 	=> MYSQL_TYPE_NEWDATE [15] => MYSQL_TYPE_VARCHAR [16] => MYSQL_TYPE_BIT [17] 
	 	=> MYSQL_TYPE_NEWDECIMAL [18] => MYSQL_TYPE_ENUM [19] => MYSQL_TYPE_SET [20] => 
	 	MYSQL_TYPE_TINY_BLOB [21] => MYSQL_TYPE_MEDIUM_BLOB [22] => MYSQL_TYPE_LONG_BLOB [23] => MYSQL_TYPE_BLOB [24] => MYSQL_TYPE_VAR_STRING [25] => MYSQL_TYPE_STRING [26] => MYSQL_TYPE_GEOMETRY 
		*/

			$tabla=$valor->table;
			$max_length=$valor->max_length ;
			$strnull=($valor->not_null==1)?' rs:maybenull="false" ':' rs:maybenull="true" ';
			$strkeycolumn=($valor->primary_key == 1)?" rs:keycolumn='true' ":' ';
			$strfixedlength=($valor->primary_key == 1)?'rs:fixedlength="true" ':' ';
			$cabecera.="<s:AttributeType name='$name' rs:number='$pos'
	        rs:basetable='$tabla' rs:basecolumn='$name'    $strkeycolumn > 
	        <s:datatype $strtype dt:maxLength='$max_length' $strfixedlength $strnull /></s:AttributeType>";
	        $pos++;
		}//fin del for
 	 $cabecera.='<s:extends type="rs:rowbase"/></s:ElementType></s:Schema>';
 	 $res=$this->resultado;
 	 $strCuerpo="<rs:data>";
 	 $strFila='';
 	 while($fila = $res->result_array())
 	 {$strFila.='<z:row ';
 	 	
 	 		for ($i=0; $i<count($columnas);$i++) {
 	 			$namecol=$columnas[$i];
 	 			$valor=$fila[$namecol];
 	 			
 	 			  $strFila.="  $namecol ='$valor'  ";
 	 			
 	 		}
 	 		$strFila.='  ></z:row>'; 	  	
 	 		
 	  }
	$strCuerpo.=$strFila.'</rs:data></xml>';
	 $strXML=$cabecera.$strCuerpo;
	return $strXML;
	
	}

	function getxmldata($arrRows)
	{ 	$strXML="";		
		$strFila='<resultados>';	
		if(count($arrRows)>0)
		{
			$columnas=array_keys($arrRows[0]);
		}
		foreach ($arrRows as $fila){
		$strFila.='<row>'; 
			for ($i=0; $i<count($columnas);$i++) {
 	 			 $namecol=trim($columnas[$i]);
 	 			$valor=trim($fila[$namecol]);
 	 			$strFila.=trim("<$namecol>$valor</$namecol>");
 	 		}
        $strFila.='</row>';
        }
 	 $strFila.="</resultados>";		
	 $strXML.="<xml version='1.0'>".$strFila."</xml>";
	return $strXML;

	}


	function GetJsonData()
	{ 	$strXML="";
		$res=$this->resultado;
		$infoCampos=$this->GetInfoCampos();
		$columnas=array();
		
		$pos=1;
		foreach ($infoCampos as $valor){
			$name=$valor->name;
			$columnas[]=$name;
                 }

 	 
 	 $strFila='[';
         //$res->data_seek(0);
 	 while($fila = $res->result_array())
 	 {  $strFila.='{'; 	 	
 	 		for ($i=0; $i<count($columnas);$i++) {
 	 			$namecol=trim($columnas[$i]);
 	 			$valor=trim($fila[$namecol]);
 	 			
 	 			  $strFila.=trim("'$namecol':'$valor'");
 	 			
 	 		}
 	 	$strFila.='}';
 	  }
 	  $strFila.="]";
		
	 $strXML.=$strFila;
	return $strXML;

	}

	function NumError()
	{
		return $this->NumError;
	}

	function DescError()
	{
		return $this->DescError;
	}


}//



?>
