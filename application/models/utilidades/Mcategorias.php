<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mcategorias extends CI_model{
private $id;
private $numError=0;
private $descError="";
	function __construct()
	{
		parent::__construct();
	}

	function agregar($nombre)
	{ $this->load->database();
		
		$this->numError=0;
	  	$this->descError="";
	  	$id=0;
		try {
			$data = array('nombre' => $nombre);
	 
	 		$this->db->insert('orientacion',$data);	
			$id=$this->db->insert_id();
			if(!$this->db->affected_rows()>0)
			{
			$this->numError=2;
	  	    $this->descError="no se pudo insertar";
			}	
		} catch (Exception $e) {
			$this->numError=1;
	  	    $this->descError=$e->getMessage();
		}
			return $id;
	  }

  	
	  function actualizar($id,$campos)
	  {
	  		$exito=false;
	  		$this->numError=0;
	  		$this->descError="";
	  		try {
	  			$this->load->database();
		  		$rows=array();
		  		if (!$id_usuario>0)
		  		{
		  			$this->numError=3;
	  		        $this->descError="no ingreso dato";
		  			return $exito;
		  		}
		  		//$data=array('apellido' => $apellido,'nombres' => $nombres, 'tipo_docu' => $tipo_docu, 'nro_docu' => $nro_docu, 'sexo' =>$sexo , 'id_loc' => $id_loc,'telefono'=>$telefono,'domicilio'=>$domicilio, 'img_profile'=>$path_img,'email'=>$email);	  		
				
		  		$where= array('id' => $id);	  		
				$query=$this->db->update('orientacion', $campos,$where);
		  		
				if($query)
				{
					$exito=true;
				}else
				{
					$this->numError=2;
	  		        $this->descError="error al actualizar";
				}
	  			
	  		} catch (Exception $e) {
	  		$this->numError=1;
	  		$this->descError=$e->getMessage();
	  		}
	  		
			return $exito;
	  }

	function obtener(&$numrows=0,$id=0)
  	{
  	$this->load->database();
  	$rows=array();
  	$query=null;
	  	if ($id==0) {
	  		$this->db->order_by("nombre","asc");
	  		$query=$this->db->get('orientacion');
	  	}else
	  	{
	  		$query=$this->db->get_where('orientacion','id=$id');
	  	}	
	$rows=$query->result_array();
	$numrows=$query->num_rows();
	return $rows;	
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
