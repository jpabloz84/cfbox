<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Servicios extends CI_Controller {
private $visitante;
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");		
		$this->load->model("mservice");
		$this->visitante=unserialize($this->session->visitante);
		date_default_timezone_set('America/Argentina/Buenos_Aires');    
	}
	 function index()
	{	

		if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$this->load->view('entidades/vservicios',$arrVariales);
		
	}	

	function loginOn(){
		$exito=true;
		$visitante=unserialize($this->session->visitante);
		if(!isset($this->visitante) || $this->visitante==null)
		{		
		$exito=false;
		}

		if($exito && !$this->visitante->logueado())
		{		
		$exito=false;
		}
		return $exito;	
	}

		
	function listener($id_param=0)
	{
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$this->fwxml->arrResponse=array();
		 $arrRow=array();
		 $id_empresa=$this->visitante->get_id_empresa();
	 	if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}

		$metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE
		//eliminacion
		if($metodo=="DELETE" && $id_param > 0)
		{		
		$campos=array('id_servicio' =>$id_param);
		$id=$this->mservice->eliminar_registro('servicios',$campos);		
		}
		//alta
		if($metodo=="POST")
		{
		$campos=json_decode(file_get_contents('php://input'), true);				
		unset($campos['modo']);
		$campos['id_empresa']=$id_empresa;
		$campos['fe_actualizacion']=date("Y-m-d H:i:s");
		$id=$this->mservice->insertar('servicios',$campos);
		$this->fwxml->arrResponse=array('id_servicio' => $id);;
		}
		//actualizacoines
		if($metodo=="PUT")
		{
		$campos=json_decode(file_get_contents('php://input'), true);				
		unset($campos['modo']);
		$campos['id_empresa']=$id_empresa;
		$campos['fe_actualizacion']=date("Y-m-d H:i:s");
		$condicional=array('id_servicio' => $campos['id_servicio']);
		$this->mservice->actualizar_registro('servicios',$campos,$condicional);
		$this->fwxml->arrResponse=array('id_servicio' =>  $campos['id_servicio']);;
		}

		
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
    $this->fwxml->descError=$this->mservice->descError();
    }
    
    $this->fwxml->ResponseJson();		

	}
	

}//class
