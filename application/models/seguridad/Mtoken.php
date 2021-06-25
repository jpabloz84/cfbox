<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mtoken extends CI_model{
	private $visitante=null;
	private $numError=0;
	private $descError="";
  public $rollbackonerror=true;
	function __construct()
	{
		parent::__construct();		    
    //$this->load->database();      
    
		
	}

	function generar_token($id_token_def,$id_usuario,$parametros){		
	$this->numError=0;
  	$this->descError="";
  	$id_empresa=0;
  	$token="";
    if($this->rollbackonerror){
    $this->db->trans_begin();  
    }
  	
  	$query=$this->db->get_where('verusuarios', array('id_usuario' => $id_usuario));
  	$usu=$query->result_array()[0];
  	$id_empresa=$usu['id_empresa'];
  	$query=$this->db->query("SELECT def.`id_token_def`,def.`definicion`,def.`min_duracion`,p.`parametro` FROM token_definicion def JOIN token_parametros_def p ON def.`id_token_def`=p.`id_token_def` where def.id_token_def=$id_token_def");  	
  	$token_def_params=$query->result_array();

  	$min_duracion=$token_def_params[0]['min_duracion'];
  	$token=md5(uniqid());
  	$strfe_vencimiento="NULL";
  	if($min_duracion!=0){
  		$strfe_vencimiento="ADDDATE(NOW(),INTERVAL $min_duracion MINUTE)";
  	}
  	$this->db->query("insert into token (token,id_token_def,fe_creacion,fe_vencimiento,id_usuario) values ('$token',$id_token_def,now(),$strfe_vencimiento,$id_usuario)");
  		 $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message'];        
          if($this->rollbackonerror){
          $this->db->trans_rollback();
          }
          return "";
        }

    $id_token=$this->db->insert_id();
  	foreach ($token_def_params as $fila) {
  		//si no existe los parametros ingresados, sale  		
  		if(!isset($parametros[$fila['parametro']])){
			$this->numError=1;
  			$this->descError="faltan definir parametro ".$fila['parametro'];
        if($this->rollbackonerror){
  			$this->db->trans_rollback();
        }
  			return "";
  		}
  		$valor=$parametros[$fila['parametro']];
  		
  		$this->db->query("insert into token_parametros (id_token,parametro,valor) values($id_token,'".$fila['parametro']."','$valor')");
  		 $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message'];        
          if($this->rollbackonerror){
          $this->db->trans_rollback();
          }
          return "";
        }
  		
  	}//for
    if($this->rollbackonerror){
    	if ($this->db->trans_status() === FALSE)
      {
      $error=$this->db->error();
      $this->numError=$error['code'];
      $this->descError=$error['message'];
      $this->db->trans_rollback();
      $token="";
      }else{
          $this->db->trans_commit();
      } 
    }
return $token;
}

function getvalores($token)
{ 
$token=trim($token);
$valores=array();
$query=$this->db->query("SELECT parametro,valor FROM vertoken_parametro_valor where token='$token' AND  (ISNULL(fe_vencimiento) OR fe_vencimiento>NOW())");  	
	$params=$query->result_array();
	if(count($params)>0){
		foreach ($params as  $row) {
		$valores[$row['parametro']]=$row['valor'];
		}	
	}else{
		$this->numError=1;
		$this->descError="Token vencido o bien no existe";
	}
	
return $valores;	
}

function vigente($token)
{ 
	$vigente=false;
	$token=trim($token);
	$q=$this->db->query("select * from token where token='$token' AND  (ISNULL(fe_vencimiento) OR fe_vencimiento>NOW())");
	$rs=$q->result_array();
	if(count($rs)>0)
	{
		$vigente=true;
	}

	return $vigente;
}
function anular($token){
	$this->db->query("update token set fe_vencimiento=fe_creacion where token='$token'");
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
