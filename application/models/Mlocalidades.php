<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mlocalidades extends CI_model{
private $numError=0;
private $descError="";
		function __construct()
		{
			parent::__construct();
		}
	  function getLocalidades($id_provincia=0)
	  {
	  	$numError=0;
	  	$descError="";

	  	$this->load->database();
	  	$rows=array();
	  	
	  	$where="";
	  	if($id_provincia>0)
	  	{
	  		$where=" where id_pro=$id_provincia";
	  	}
	  	$stmt="select id_loc,id_pro, descripcion_loc from localidades  $where order by descripcion_loc";
	  	 try {	  	 	
		  	
		  	$query=$this->db->query($stmt);			
			$rows=$query->result_array();		
		} 
		catch (Exception $e) {
	  	$this->numError=1;
	  	$this->descError=$e->getMessage();
	  	 }
		return $rows;	
	  }


	  function getLocalidadesPagos($id=0)
	  {
	  	$numError=0;
	  	$descError="";

	  	$this->load->database();
	  	$rows=array();
	  	
	  	$where="where  l.sucursal_pago=1 ";
	  	if($id>0)
	  	{
	  		$where.=" and l.id_loc=$id ";
	  	}
	  	$stmt="SELECT l.id_loc,l.id_pro,l.descripcion_loc,p.descripcion_pro FROM localidades l JOIN provincias p ON l.id_pro=p.id_pro  $where order by descripcion_loc";
	  	 try {	  	 	
		  	
		  	$query=$this->db->query($stmt);			
			$rows=$query->result_array();		
		} 
		catch (Exception $e) {
	  	$this->numError=1;
	  	$this->descError=$e->getMessage();
	  	 }
		return $rows;	
	  }


	   function getProvincias($id_provincia=0)
	  {
	  	$numError=0;
	  	$descError="";

	  	$this->load->database();
	  	$rows=array();
	  	
	  	$where="";
	  	if($id_provincia>0)
	  	{
	  		$where=" where id_pro=$id_provincia";
	  	}
	  	$stmt="select id_pro, descripcion_pro from provincias  $where order by descripcion_pro";
	  	 try {	  	 	
		  	
		  	$query=$this->db->query($stmt);			
			$rows=$query->result_array();		
		} 
		catch (Exception $e) {
	  	$this->numError=1;
	  	$this->descError=$e->getMessage();
	  	 }
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
