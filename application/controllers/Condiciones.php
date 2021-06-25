<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Condiciones extends CI_Controller {

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
		$this->load->view('vcondiciones');
	}	
	function listar()
	{
		$res= array();
		$arr=$this->mservice->getSinwhere('cond_iva','condicion ASC');
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
	 	if(!$this->loginOn())
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
			$arreglo = array('condicion' => $valor);
		}


		if(strpos($name,"abreviacion-")!==false){
			$id=str_replace("abreviacion-", "",$name);
			$valor=$this->input->post('value');
			$arreglo = array('comp_tipo' => $valor);
		}
		
		$condicional=array('id_cond_iva' => $id);
		$this->mservice->actualizar_registro('cond_iva',$arreglo,$condicional);
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
        $this->fwxml->descError=$this->mservice->descError();
        }
    
        $this->fwxml->ResponseJson();		
	}//update

	function listener($id_param=0)
	{
		$this->fwxml->numError=0;
    	$this->fwxml->descError="";
    	$this->fwxml->arrResponse=array();
		 $arrRow=array();

	 	if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}

		$metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE
		if($metodo=="DELETE" && $id_param > 0)
		{		
		$campos=array('id_cond_iva' =>$id_param);
		$id=$this->mservice->eliminar_registro('cond_iva',$campos);		
		}
		if($metodo=="POST")
		{
		$campos=json_decode(file_get_contents('php://input'), true);				
		$id=$this->mservice->insertar('cond_iva',$campos);
		$this->fwxml->arrResponse=array('id' => $id);
		}

		
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
        $this->fwxml->descError=$this->mservice->descError();
        }
    
        $this->fwxml->ResponseJson();		

	}

	

}
