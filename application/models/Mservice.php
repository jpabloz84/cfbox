<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mservice extends CI_model{
private $numError=0;
private $descError="";
public $laststmt="";
private $field_data;
private $_affected_rows=0;
private $datatype=array('varchar'=>'string','int'=>'int','float'=>'float','tinyint'=>'short','datetime'=>'dateTime','date'=>'date','money'=>'money');
/*private $tipos=array(0=>'decimal',1=>'boolean',2=>'short',3=>'long',4=>'float', 5=>'double', 6=>'null',7=>'timestamp',8 => 'unsignedLong',9=>'int',10=>'date',11=>'time',12=>'datetime',13=>'gYear',14=>'date',15=>'string',16=>'boolean',17=>'decimal',18=>'enum',19=>'set',20=>'blob',21=>'blob',22=>'blob', 23=>'blob',24=>'string', 25=>'string',253=>'string',26=>'geometry');*/
		function __construct()
		{
			parent::__construct();
            $this->load->database();
            $this->db->query("SET LOCAL time_zone='-03:00'");//seteo zona horaria buenos aires
		}


	  function get($tabla,$campos,$condicion="1=1",$orderby="")
	  {$this->field_data=null;
	  	$numError=0;
	  	$descError="";

	  	
	  	$rows=array();
	  	$stmt="";
	  	 $stmtcol="*";
	  	 try {	  	 	
	  	 	if($condicion=="")
	  	 		{$condicion="1=1";}
		  	   foreach ($campos as $key => $columna) {
		  		if($stmtcol=="*")
		  		{
		  		$stmtcol=$columna;	
		  		}else
		  		{
		  			$stmtcol.=",".$columna;	
		  		}	  		
		  	}
		  	$stmt.="select $stmtcol from $tabla where $condicion ";



		  	if($orderby!="")
		  	{
		  		$stmt.=" order by $orderby";
		  	}
		  	
            //echo $stmt;
            //die();
		  	$query=$this->db->query($stmt);
            $this->field_data=$query->field_data();
		  	$error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {
            	
            	$this->numError=$error["code"];
                $this->descError=$error['message'];	
                
            }else
            {            
                $rows=$query->result_array();                       
        	}
			
		} 
		catch (Exception $e) {
	  	$this->numError=1;
	  	$this->descError=$e->getMessage();
	  	 }
		return $rows;	
	  }

      function query($stmt)
      {

        $numError=0;
        $descError="";
        
        $rows=array();
        //$stmt="";
        $this->laststmt=$stmt;
         try{
            $query=$this->db->query($stmt);         
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {
                
                $this->numError=$error["code"];
                $this->descError=$error['message']; 
                
            }else
            {
             $rows=$query->result_array();                       
            }
            
        } 
        catch (Exception $e) {
        $this->numError=1;
        $this->descError=$e->getMessage();
         }
        return $rows;
      }

      //$customfielddatatype array de tipos de datos personalizados para columnas
      function getxmldata($tabla,$campos,$condicion="1=1",$orderby="",$customfielddatatype=array())
      {
        
        //$cols=array('nombre'=>'','type'=>'','personalizada' => false,'orden' => 0);
        $cols=array();
        try{
        $filas=$this->get($tabla,$campos,$condicion,$orderby);
        $infocampos=$this->field_data;
       // $infocampos=$this->db->field_data($tabla);
        
        $strXML="";
        $cabecera='<xml xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882" 
        xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
        xmlns:rs="urn:schemas-microsoft-com:rowset" 
        xmlns:z="#RowsetSchema"><s:Schema id="RowsetSchema"><s:ElementType name="row" content="eltOnly" rs:updatable="true">';
        
        $columnas=array();
        $strCuerpo="";
        $pos=1;
        
            foreach ($infocampos as $valor) {

                $name=$valor->name;

                $columnas[]=$name;          
                $type=$valor->type;
                $tipodato=(isset($this->datatype[$type]))?$this->datatype[$type]:'string';
                if(count($customfielddatatype)>0){
                    if(isset($customfielddatatype[$name])){
                        $customtype=$customfielddatatype[$name];
                        $tipodato=(isset($this->datatype[$customtype]))?$this->datatype[$customtype]:$tipodato;
                    }
                }
                
                $strtype= "dt:type='".$tipodato."'";
                $max_length=$valor->max_length ;
                $strnull=' rs:maybenull="true" ';
                $strkeycolumn=($valor->primary_key == 1)?" rs:keycolumn='true' ":' ';
                $strfixedlength=($valor->primary_key == 1)?'rs:fixedlength="true" ':' ';
                $cabecera.="<s:AttributeType name='$name' rs:number='$pos'
                rs:basetable='$tabla' rs:basecolumn='$name'    $strkeycolumn > 
                <s:datatype $strtype dt:maxLength='$max_length' $strfixedlength $strnull /></s:AttributeType>";
                $pos++;
            }//fin del for
            $cabecera.='<s:extends type="rs:rowbase"/></s:ElementType></s:Schema>';
            $strCuerpo="<rs:data>";
            $strFila='';
            //print_r("condicion: $condicion");
            
            
             foreach ($filas as $fila)                 
             {$strFila.='<z:row ';                
                    for ($i=0; $i<count($columnas);$i++){
                        $namecol=$columnas[$i];
                        $valor=$fila[$namecol];
                        $strFila.="  $namecol ='$valor'  ";
                    }
             $strFila.='  ></z:row>';
             }
            $strCuerpo.=$strFila.'</rs:data></xml>';
            $strXML=$cabecera.$strCuerpo;
        } 
        catch (Exception $e) {
        $this->numError=2;
        $this->descError=$e->getMessage();
         }

        return $strXML;
        }



      function begin_trans()
      { $this->load->database();
        $this->db->trans_begin();
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {                
                $this->numError=$error["code"];
                $this->descError=$error['message']; 
            }
      }

      function commit_trans()
      {
            
             if ($this->db->trans_status() === FALSE)
                { 
                $error=$this->db->error();
                //echo "aca1".$error['code'].$error['message'];
                $this->numError=1; //$error['code'];
                $this->descError=$error['message'];
                $this->db->trans_rollback();
                }else{
                    //echo "aca2";
                    $this->db->trans_commit();
                } 
            
      }

      function rollback_trans()
      {
            $this->db->trans_rollback();
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {
                
                $this->numError=$error["code"];
                $this->descError=$error['message']; 
                
            }
      }
	  function getDocumentos()
     {
      
    
    $this->numError=0;
    $this->descError="";
    $arr=array();
        try {
        	    $tabla="documento";
                $arrcol=array('tipo_docu','documento');            
                $arr=$this->get($tabla,$arrcol,'','documento');
        } catch (Exception $e) {
        	$this->numError=1;
    		$this->descError=$e->getMessage();
        	
        }
        return $arr;
    }

     function getCondicionesIva()
     {
      
    
    $this->numError=0;
    $this->descError="";
    $arr=array();
        try {
                $tabla="cond_iva";
                $arrcol=array('id_cond_iva','condicion','comp_tipo');            
                $arr=$this->get($tabla,$arrcol,'','condicion');
        } catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();
            
        }
        return $arr;
    }

    
     function getCategorias()
     {
      
    
    $this->numError=0;
    $this->descError="";
    $arr=array();
        try {
        	    $tabla="categorias";
                $arrcol=array('id_categoria','categoria');            
                $arr=$this->get($tabla,$arrcol,'','categoria');
        } catch (Exception $e) {
        	$this->numError=1;
    		$this->descError=$e->getMessage();
        	
        }
        return $arr;
    }

    function getCajas()
     {
    $this->numError=0;
    $this->descError="";
    $arr=array();
        try {
                $stmt="SELECT c.id_caja,c.caja,c.id_sucursal,s.sucursal FROM cajas c JOIN sucursal s ON c.id_sucursal=s.id_sucursal";                
                $arr=$this->query($stmt);
        } catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();
            
        }
        return $arr;
    }

    function  getwhere($tabla,$where,$orderby='')
     {
    $this->numError=0;
    $this->descError="";
    $arr=array();
        try {
                
                $arrcol=array('*');            
                $arr=$this->get($tabla,$arrcol,$where,$orderby);
        } catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();
            
        }
        return $arr;
    }

    function getSinwhere($tabla,$orderby='')
    {	//$orderby='title DESC, name ASC'
    	$this->numError=0;
    	$this->descError="";
    	$arr=array();
    	$query=null;
        try {
            $this->load->database();
        	$this->db->from($tabla);
    	    if($orderby!="")
    	    {
    	    	$this->db->order_by($orderby);
    	    }
			$query=$this->db->get();
    	    $error=$this->db->error();
        	if (isset($error['code']) && $error['code']>0) {            	
        	$this->numError=$error["code"];
            $this->descError=$error['message'];	
            
        	}else
        	{
        	$arr=$query->result_array();			
    		}                
                
        } catch (Exception $e) {
        	$this->numError=1;
    		$this->descError=$e->getMessage();
        	
        }
        return $arr;
    }


       function insertar_registro($tabla,$variables){
      $query=$this->db->insert($tabla, $variables);
        }

        function registrar_proceso($id_usuario,$tipo_proceso,$descripcion){
            $id_proceso=0;            
            $this->load->database();                        
            //$stmt="insert into procesos (id_usuario,fe_proceso,detalle,tipo_proceso) values($id_usuario,now(),$tipo_proceso,'$descripcion')";
            //$query=$this->db->query($stmt);         
            $campos=array('id_usuario' =>$id_usuario ,'tipo_proceso'=>$tipo_proceso,'detalle'=>$descripcion);            
            $id_proceso=$this->insertar("procesos",$campos);
            $this->db->query("update procesos set fe_proceso=now() where id_proceso=$id_proceso");         
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {
                $this->numError=$error["code"];
                $this->descError=$error['message']; 
            }
            return $id_proceso;
        }

       function insertar($tabla,$campos) 
       {
        $id=0;
        $this->numError=0;
        $this->descError="";
        try {
            $this->load->database();
            $this->db->set($campos);
            $this->db->insert($tabla);
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']!=0) {                
            $this->numError=$error["code"];
            $this->descError=$error['message'];             
            }
            $id=$this->db->insert_id();
         }catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();            
        }
        return $id;
       }

       //soporta fechas-> el arrary debe ser tridimensional $col['valor']$col['datatype']$col['col']
       function insertDb($tabla,$arrCampos)
       {
        $id=0;
        $this->numError=0;
        $this->descError="";
        $stmtcol="";
        $stmtvalues="";
        try {
            $this->load->database();
            foreach ($arrCampos as $campos) {
                $datatype='varchar';
                 $valor="";
                 $strvalores="";
                 $columna="";

                if(isset($campos['datatype']))
                {
                    $datatype=$campos['datatype'];
                }

                $columna=$campos['col'];

                if($datatype=="int" ||  $datatype=="tinyint" ||  $datatype=="float" ||  $datatype=="bit" ||  $datatype=="boolean")
                {

                    $valor=$campos['valor'];
                }
                else
                {
                        $valor="'".$campos['valor']."'";
                }

                
                if($stmtcol=="")
                {
                $stmtcol=$columna;  
                }else
                {
                $stmtcol.=",".$columna; 
                }

                if($strvalores=="")
                {
                $strvalores=$valor;  
                }else
                {
                $strvalores.=",".$valor; 
                }

            }

            $this->db->query("insert into $tabla ($stmtcol) values ($strvalores) ");
            $this->db->insert($tabla);
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']!=0) {                
            $this->numError=$error["code"];
            $this->descError=$error['message'];             
            }
            $id=$this->db->insert_id();
         }catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();            
        }
        return $id;
       }


       function eliminar_registro($tabla,$condicional){
         
        $this->numError=0;
        $this->descError="";
        $this->_affected_rows=0;
        try {
            $this->load->database();
            $this->db->delete($tabla, $condicional);
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']!=0) {                
            $this->numError=$error["code"];
            $this->descError=$error['message'];             
            }
            $this->_affected_rows=$this->db->affected_rows();
        } catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();            
        }
        
       }
       function affected_rows(){
        return $this->_affected_rows;
       }

       function actualizar_registro($tabla,$columnas,$condicional){
        $this->numError=0;
        $this->descError="";
        try {
            $this->load->database();            
            //$this->db->where($condicional);
            $this->db->update($tabla,$columnas,$condicional);
            $error=$this->db->error();                        
            if (isset($error['code']) && $error['code']!=0) {                
            $this->numError=$error["code"];
            $this->descError=$error['message'];             
            }
        } catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();            
        }
       
      }

      function update($tabla,$arrCampos,$arrCondicionales){
        $this->numError=0;
        $this->descError="";
        try {
             $this->load->database();            
            $this->db->update($tabla,$arrCampos,$arrCondicionales);     
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']!=0) {                
            $this->numError=$error["code"];
            $this->descError=$error['message'];             
            }
        } catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();            
        }
       
      }

      function getprepared($stmt,$params=null){
        $this->numError=0;
        $this->descError="";
        $rs=array();
        $query=null;
        try {

            $this->load->database();            
            if(isset($params)){ 
            if(count($params)>0){
            $query=$this->db->query($stmt, $params);           
            }else{
            $query=$this->db->query($stmt);               
            }
            
            }else{
            $query=$this->db->query($stmt);       
            }
            
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']!=0) {                
            $this->numError=$error["code"];
            $this->descError=$error['message'];             
            }else{
            $rs=$query->result_array();    
            }
            
        } catch (Exception $e) {
            $this->numError=1;
            $this->descError=$e->getMessage();            
        }
        return $rs;
      }


     function numError()
	 {
		return $this->numError;
	 }

	function descError()
	{
		return $this->descError;
	}




}


?>
