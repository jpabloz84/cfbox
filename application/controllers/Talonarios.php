<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Talonarios extends CI_Controller {
private $visitante;
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");		
		$this->load->model("mservice");
		$this->visitante=unserialize($this->session->visitante);
	}
	 function index()
	{	

		if(!$this->loginOn() || !$this->visitante->permitir('prt_abm',8))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['tipo_comp']=$this->mservice->getSinwhere('tipo_comprobante','tipo_comp');
		$arrVariales['certificados']=$this->mservice->getSinwhere('certificados','fe_emision');
		$arrVariales['sucursales']=$this->mservice->getSinwhere('sucursal','sucursal');
		$this->load->view('vtalonarios',$arrVariales);
		
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

	 	if(!$this->loginOn() || !$this->visitante->permitir('prt_abm',8))
		{
			$this->load->view('vlogin');
			return; 
		}

		$metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE
		//eliminacion
		if($metodo=="DELETE" && $id_param > 0)
		{		
		$campos=array('id_talonario' =>$id_param);
		$id=$this->mservice->eliminar_registro('talonarios',$campos);		
		}
		//alta
		if($metodo=="POST")
		{
		$cmp=json_decode(file_get_contents('php://input'), true);				
		$campos=array('nro_talonario' =>$cmp['nro_talonario'],
		'habilitado'=>$cmp['habilitado'],'id_tipo_comp'=>$cmp['id_tipo_comp'],'id_sucursal'=>$cmp['id_sucursal'],'id_certificado'=>$cmp['id_certificado']);	
		$id=$this->mservice->insertar('talonarios',$campos);
		$this->fwxml->arrResponse=array('id_talonario' => $id);;
		}
		//actualizacoines
		if($metodo=="PUT")
		{
		$cmp=json_decode(file_get_contents('php://input'), true);				
		$campos=array('nro_talonario' =>$cmp['nro_talonario'],
		'habilitado'=>$cmp['habilitado'],'id_tipo_comp'=>$cmp['id_tipo_comp'],'id_sucursal'=>$cmp['id_sucursal'],'id_certificado'=>$cmp['id_certificado']);		
		$condicional=array('id_talonario' => $cmp['id_talonario']);
		$this->mservice->actualizar_registro('talonarios',$campos,$condicional);
		$this->fwxml->arrResponse=array('id_talonario' =>  $cmp['id_talonario']);;
		}
		
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
    $this->fwxml->descError=$this->mservice->descError();
    }
    
    $this->fwxml->ResponseJson();		
	}


	

}//class
