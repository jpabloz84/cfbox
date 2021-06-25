<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mproveedores extends CI_model{
private $id;
private $numError=0;
private $descError="";
	function __construct()
	{
		parent::__construct();
	}

	function agregar($proveedor,$observaciones,$id_persona,$auspiciante,$id_empresa)
	{ $this->load->database();		
		$this->numError=0;
	  	$this->descError="";
	  	$id_proveedor=0;
		try {
			
	 		$this->db->query("INSERT INTO proveedores(id_persona,proveedor,observaciones,fe_alta,auspiciante,id_empresa) values 
	 			                                       ($id_persona,'$proveedor','$observaciones',now(),$auspiciante,$id_empresa)");
	 		$error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {            	
            	$this->numError=$error["code"];
                $this->descError=$error['message'];	
            }else
            {
            $id_proveedor=$this->db->insert_id();	
            }
			
			
		} catch (Exception $e) {
			$this->numError=1;
	  	    $this->descError=$e->getMessage();
		}			
			return $id_proveedor;
	  }
  	
	  function actualizar($id_proveedor,$campos)
	  {
	  		$exito=false;
	  		$this->numError=0;
	  		$this->descError="";
	  		try {
	  			$this->load->database();
		  		$rows=array();
		  		if (!$id_proveedor>0)
		  		{
		  			$this->numError=3;
	  		        $this->descError="no ingreso ID";
		  			return $exito;
		  		}
		  		//$data=array('apellido' => $apellido,'nombres' => $nombres, 'tipo_docu' => $tipo_docu, 'nro_docu' => $nro_docu, 'sexo' =>$sexo , 'id_loc' => $id_loc,'telefono'=>$telefono,'domicilio'=>$domicilio, 'img_profile'=>$path_img,'email'=>$email);	  		
				
		  		$where= array('id_proveedor' => $id_proveedor);	  		
				$query=$this->db->update('proveedores', $campos,$where);
		  		
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
