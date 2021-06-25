<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Maccesos extends CI_model{
private $id;
private $numError=0;
private $descError="";
	function __construct()
	{
		 parent::__construct();
  	$this->load->database();  
	}
	
	function crear($campos,$id_usuario,&$id_acceso=0){
		$exito=true;
		$cmp=array(); 
		$cmp['id_usuario']=$id_usuario;
		$cmp['ultima_modificacion']=date("Y-m-d H:i:s");
		$this->db->set($cmp);
    $this->db->insert("accesos");
    $error=$this->db->error();
    if (isset($error['code']) && $error['code']!=0){
      $this->numError=$error["code"];
      $this->descError='Error al insertar datos:'.$error['message'];
      $exito=false;
    }
    if($exito){    
    	$id_acceso=$this->db->insert_id();
    	$nro_campo=0;
    	foreach ($campos as $campo) {
				$cmp=array(); 
				$cmp['id_acceso']=$id_acceso;
				$cmp['nro_campo']=$nro_campo;
				$cmp['campo']=$campo['campo'];
				$cmp['descripcion']=$campo['descripcion'];
				$cmp['valor']=$campo['valor'];
				$this->db->set($cmp);
    		$this->db->insert("accesos_campos");
    		$error=$this->db->error();
		    if (isset($error['code']) && $error['code']!=0){
		      $this->numError=$error["code"];
		      $this->descError='Error al insertar campos de accesos:'.$error['message'];
		      $exito=false;
		      break;
		    }
		    $nro_campo++;
			}
    }
		return $exito;
	}

	function modificar($id_acceso,$campos){
		$exito=true;
		$cmp=array(); 		
		$cmp['ultima_modificacion']=date("Y-m-d H:i:s");
		$this->db->set($cmp);
    $where=array('id_acceso'=>$id_acceso);
    $this->db->where($where);
    $this->db->update('accesos');
    $error=$this->db->error();
    if (isset($error['code']) && $error['code']!=0){
      $this->numError=$error["code"];
      $this->descError='Error al actualizar datos:'.$error['message'];
      $exito=false;
    }
		if($exito){
			$this->db->query("delete from accesos_campos where id_acceso=$id_acceso");
			if (isset($error['code']) && $error['code']!=0){
      $this->numError=$error["code"];
      $this->descError='Error al actualizar datos:'.$error['message'];
      $exito=false;
    	}
		}
    if($exito){    
    	
    	$nro_campo=0;
    	foreach ($campos as $campo) {
				$cmp=array(); 
				$cmp['id_acceso']=$id_acceso;
				$cmp['nro_campo']=$nro_campo;
				$cmp['campo']=$campo['campo'];
				$cmp['descripcion']=$campo['descripcion'];
				$cmp['valor']=$campo['valor'];
				$this->db->set($cmp);
    		$this->db->insert("accesos_campos");
    		$error=$this->db->error();
		    if (isset($error['code']) && $error['code']!=0){
		      $this->numError=$error["code"];
		      $this->descError='Error al insertar campos de accesos:'.$error['message'];
		      $exito=false;
		      break;
		    }
		    $nro_campo++;
			}
    }
		return $exito;
	}


	function eliminar($id_acceso){
		$exito=true;
		$this->db->query("delete from accesos_campos where id_acceso=$id_acceso");
		$error=$this->db->error();
    if (isset($error['code']) && $error['code']!=0){
      $this->numError=$error["code"];
      $this->descError='Error al eliminar campos de acceso:'.$error['message'];
      $exito=false;
    }else{
    	$this->db->query("delete from accesos where id_acceso=$id_acceso");	
    	$error=$this->db->error();
	    if (isset($error['code']) && $error['code']!=0){
	      $this->numError=$error["code"];
	      $this->descError='Error al eliminar acceso:'.$error['message'];
	      $exito=false;
	    }
    }
		
    
		return $exito;
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
