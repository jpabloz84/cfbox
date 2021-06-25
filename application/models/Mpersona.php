<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mpersona extends CI_model{
private $id;
private $numError=0;
private $descError="";

	function __construct()
	{
		parent::__construct();
	}

	function agregar($apellido,$nombres,$tipo_docu,$nro_docu,$sexo,$id_loc,$calle,$nro,$piso,$dpto,$cp,$car_tel,$nro_tel,$path_img,$email,$id_loc_naci=0,$fe_naci='',$tipo_persona='F',$cuit='')
	{ 
		$this->load->database();		

		
		$this->numError=0;
	  	$this->descError="";
	  	$id_persona=0;
	  	try {
	  		
	 	$data = array('apellido' => strtoupper($apellido),'nombres' => strtoupper($nombres), 'tipo_docu' => $tipo_docu, 'nro_docu' => $nro_docu
	 		, 'sexo' =>$sexo , 'id_loc' => $id_loc,'calle'=>$calle,'nro'=>$nro,'piso'=>$piso,'dpto'=>$dpto,'cp'=>$cp,'car_tel'=>$car_tel,'nro_tel'=>$nro_tel, 'img_profile'=>$path_img,'email'=>$email,'tipo_persona'=>$tipo_persona,'cuit'=>$cuit);

	 	if($id_loc_naci>0 && $tipo_persona=="F")
	 	{
	 		$data['id_loc_nacimiento']=$id_loc_naci;
	 	}
	 	
	 		$this->db->insert('personas',$data);	
			$id_persona=$this->db->insert_id();
			if($this->db->affected_rows()>0)
			{	
				if($fe_naci!='')
	 			{
	 				$strdate=todate($fe_naci,103);
	 			$this->db->query("update personas set fe_nacimiento='$strdate' where id_persona=$id_persona");
	 			$error=$this->db->error();
            		if (isset($error['code']) && $error['code']!=0) {            	
            			$this->numError=$error["code"];
                		$this->descError=$error['message'];	
            		}
	 			}
				
			}else
			{
				$error=$this->db->error();
            	if (isset($error['code']) && $error['code']!=0) {            	
            		$this->numError=$error["code"];
                	$this->descError=$error['message'];	
            	}
			}	
	  	} catch (Exception $e) {
	  		$this->numError=1;
	  	    $this->descError=$e->getMessage();
	  	}
		
			
			return $id_persona;
	  }


	  function agrega($datos,$forzarguardar=false)
	{ /*
	$apellido,$nombres,$tipo_docu,$nro_docu,$sexo,$id_loc,$calle,$nro,$piso,$dpto,$cp,$car_tel,$nro_tel,$path_img,$email,$id_loc_naci=0,$fe_naci='',$tipo_persona='F',$cuit=''*/
		$this->load->database();		
		$this->numError=0;
	  $this->descError="";
	  	$id_persona=0;
	  	$data=array();
	  	try {
	  		foreach ($datos as $campo => $valor) {
	  			if($campo=='id_loc_nacimiento' && $valor>0){
	  				$data['id_loc_nacimiento']=$valor;
	  				continue;
	  			}
	  			if($campo=='fe_nacimiento'){
	  				continue;
	  			}
	  			$data[$campo]=$valor;
	  		}
	  		if($forzarguardar){
	 			unset($data['id_empresa']);
	  		}
	 
	 		$data['apellido']=strtoupper($data['apellido']);
	 		$data['nombres']=strtoupper($data['nombres']);
	 		$this->db->insert('personas',$data);	

	 		$error=$this->db->error();
  		if (isset($error['code']) && $error['code']!=0) {            	
  			$this->numError=$error["code"];
      		$this->descError=$error['message'];	
  		}
			$id_persona=$this->db->insert_id();
			if($this->db->affected_rows()>0 && $this->numError==0)
			{	
				if(isset($datos['fe_nacimiento']) && $datos['fe_nacimiento']!='')
	 			{
	 				$strdate=todate($datos['fe_nacimiento'],103);
	 			$this->db->query("update personas set fe_nacimiento='$strdate' where id_persona=$id_persona");
	 			$error=$this->db->error();
            		if (isset($error['code']) && $error['code']!=0) {            	
            			$this->numError=$error["code"];
                		$this->descError=$error['message'];	
            		}
	 			}
				
			}else
			{
				$error=$this->db->error();
            	if (isset($error['code']) && $error['code']!=0) {            	
            		$this->numError=$error["code"];
                	$this->descError=$error['message'];	
            	}
			}	
	  	} catch (Exception $e) {
	  		$this->numError=1;
	  	    $this->descError=$e->getMessage();
	  	}
		
			
			return $id_persona;
	  }


	  //$forzarguardar fuerza el guardado de personas (esto es cuando la persona es un administrador que no pertenece a ninguna empresa)
	  function actualizar($id_persona,$campos,$forzarguardar=false)
	  {
	  		$exito=false;
	  		$this->numError=0;
	  		$this->descError="";
	  		try {
	  			$this->load->database();
		  		$rows=array();
		  		if (!$id_persona>0)
		  		{
		  			return $exito;
		  		}
		  		if($forzarguardar){
             	 	unset($campos['id_empresa']);
             	}

		  		$rows=array();
		  		$query=$this->db->query("select * from personas where id_persona=$id_persona");         
             	 $rows=$query->result_array();
             	 if(isset($campos['id_empresa']) && isset($rows[0]['id_empresa'])){
             	 	if($campos['id_empresa']!=$rows[0]['id_empresa']){
             	 	$this->numError=4;
	  		        $this->descError="No tiene permitido cambiar datos de un persona ajena";
		  			return $exito;	
             	 	}
             	 }

             	 
		  		//$data=array('apellido' => $apellido,'nombres' => $nombres, 'tipo_docu' => $tipo_docu, 'nro_docu' => $nro_docu, 'sexo' =>$sexo , 'id_loc' => $id_loc,'telefono'=>$telefono,'domicilio'=>$domicilio, 'img_profile'=>$path_img,'email'=>$email);	  		
		  		$fe_nacimiento="";
				if(isset($campos['fe_nacimiento']))
				{ 
					$fe_nacimiento=($campos['fe_nacimiento']!="")?$campos['fe_nacimiento']:"";
					$campos['fe_nacimiento']="";
				}
				$campos['apellido']=strtoupper($campos['apellido']);
	 			$campos['nombres']=strtoupper($campos['nombres']);
		  		$where= array('id_persona' =>$id_persona);	  		
				$query=$this->db->update('personas', $campos,$where);
		  		if($fe_nacimiento!="")
		  		{//haho esto porque sino no m toma las funciones de convercion
		  			$strdate=todate($fe_nacimiento,103);
		  			$this->db->query("update personas set fe_nacimiento='$strdate' where id_persona=$id_persona");
		  		}

				if($query)
				{
					$exito=true;
				}else
				{
					$error=$this->db->error();
            		if (isset($error['code']) && $error['code']!=0) {            	
            		$this->numError=$error["code"];
                	$this->descError=$error['message'];	
            		}
				}
	  			
	  		} catch (Exception $e) {
	  			$this->numError=1;
	  		$this->descError=$e->getMessage();
	  		}
	  		
			return $exito;
	  }

	  function eliminar($id_persona)
	  {
	  		$exito=false;
	  		$this->load->database();
	  		$rows=array();
	  		$query=$this->db->query("delete from persona where id_persona=$id_persona");			
			if($query)
			{
				$exito=true;
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
