<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mconfiguracion extends CI_model{
	private $visitante=null;
	private $numError=0;
private $descError="";
	function __construct()
	{
		parent::__construct();
		//$this->visitante=unserialize($this->session->visitante);
	}

	function configuracion_empresa($id_empresa,$datos){
		$this->load->database();		
		$this->numError=0;
  	$this->descError="";
  	if(!$id_empresa>0){
    return false;
  	}

  	

  //elimino los datos previos
  $this->db->query("delete from configuracion where id_empresa=$id_empresa");
  //inserto los datos nuevos
  foreach ($datos as $variable => $valor) {
    $fila=array('variable'=>$variable,'valor'=>$valor,'id_empresa'=>$id_empresa);
    $this->db->insert('configuracion',$fila);
    $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message'];        
          return false;
        } 
   }
			
		return true;

	}

	function getvalor($variable,$id_empresa=0)
	{ $this->load->database();
		$valor=null;
	  $rows=array();
    if($id_empresa!=0){
    $query=$this->db->get_where('configuracion', array('variable' => $variable,'id_empresa'=>$id_empresa));  
    }else{
      $query=$this->db->get_where('configuracion', array('variable' => $variable));  
    }
		
		$rows=$query->result_array();
		$numrows=$query->num_rows();
		if($numrows>0){
			$valor=trim($rows[0]['valor']);
		}
		
			return $valor;	
	}

	function getvariables($id_empresa=0)
	{ $this->load->database();
		$valor=null;
	  $rows=array();
	  if(!$id_empresa>0){
	  	$id_empresa=$this->visitante->get_id_empresa();
	  }
		$query=$this->db->get_where('configuracion', array('id_empresa'=>$id_empresa));
		$rows=$query->result_array();
		$numrows=$query->num_rows();
		if($numrows>0){
			$valor=trim($rows[0]['valor']);
		}
			return $rows;	
	}


  function getvalor_dato_empresa($variable,$id_empresa){
    $this->load->database();
    $valor=null;
    $rows=array();
    
    $query=$this->db->get_where('empresa_datos_pagina', array('variable' => $variable,'id_empresa'=>$id_empresa));  
    $rows=$query->result_array();
    $numrows=$query->num_rows();
    if($numrows>0){
      $valor=trim($rows[0]['valor']);
    }
    
      return $valor;  

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
