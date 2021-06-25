<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Configuraciones extends CI_Controller {

	function  __construct()
	{  
		parent::__construct();
		//$this->load->library('session');		
		//$this->load->helper('url');
		//$this->load->library('Fwxml');		
		$this->load->model("mservice");
		$this->visitante=unserialize($this->session->visitante);
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
	
	
	 function index()
	{
		if(!$this->loginOn() || !$this->visitante->permitir('prt_configuracion',32))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->view('vconfiguraciones');
	}	
	function listar()
	{
		$res= array();
		$arr=$this->mservice->getwhere('configuracion','id_empresa is null','variable ASC');
		if($this->mservice->numError()==0)
		{
			$res=json_encode($arr);
		}
		echo $res;
	}

	function update()
	{	$this->fwxml->numError=0;
    $this->fwxml->descError="";
    $this->fwxml->arrResponse=array();
		$arrRow=array();
	 	if(!$this->loginOn() || !$this->visitante->permitir('prt_configuracion',32))
		{
			$this->load->view('vlogin');
			return; 
		}
		
		$pk=$this->input->post('pk');
		$name=$this->input->post('name');
		$id=0;
		$arreglo=array();
		$valor="";
		if(strpos($name,"elemento-")!==false){
			$id=str_replace("elemento-", "",$name);
			$valor=$this->input->post('value');
			$arreglo = array('variable' => $valor);
		}


		if(strpos($name,"valor-")!==false){
			$id=str_replace("valor-", "",$name);
			$valor=$this->input->post('value');
			$arreglo = array('valor' => $valor);
		}

		if(strpos($name,"descripcion-")!==false){
			$id=str_replace("descripcion-", "",$name);
			$valor=$this->input->post('value');
			$arreglo = array('descripcion' => $valor);
		}
		
		
		$condicional=array('variable' => $id);
		$this->mservice->actualizar_registro('configuracion',$arreglo,$condicional);
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
        $this->fwxml->descError=$this->mservice->descError();
        }
    
        $this->fwxml->ResponseJson();		
	}//update

	function listener($id_param="")
	{
		$this->fwxml->numError=0;
    $this->fwxml->descError="";
    $this->fwxml->arrResponse=array();
		 $arrRow=array();
		 
	 	if(!$this->loginOn() || !$this->visitante->permitir('prt_configuracion',32))
		{
			$this->load->view('vlogin');
			return; 
		}

		$metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE
		if($metodo=="DELETE" && $id_param !="")
		{		
		$campos=array('variable' =>$id_param);
		$id=$this->mservice->eliminar_registro('configuracion',$campos);		
		}
		if($metodo=="POST")
		{
		$campos=json_decode(file_get_contents('php://input'), true);				
		$this->mservice->insertar('configuracion',$campos);
		}
		if($this->mservice->numError()!=0)
		{
		 $this->fwxml->numError=$this->mservice->numError();
     $this->fwxml->descError=$this->mservice->descError();
    }
    $this->fwxml->ResponseJson();		

	}

	

}
