<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dbtable extends CI_Controller {

	function  __construct()
	{  
		parent::__construct();		
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
		$this->load->view('vdbtable');
	}	
	function listar()
	{
		$res= array();
		$arr=$this->mservice->getSinwhere('orientacion','nombre ASC');
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
		$id=str_replace("elemento-", "",$name);
		$valor=$this->input->post('value');
		$arreglo = array('nombre' => $valor);
		$condicional=array('id' => $id);
		$this->mservice->actualizar_registro('orientacion',$arreglo,$condicional);
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
		$campos=array('id' =>$id_param);
		$id=$this->mservice->eliminar_registro('orientacion',$campos);		
		}
		if($metodo=="POST")
		{
		$campos=json_decode(file_get_contents('php://input'), true);				
		$id=$this->mservice->insertar('orientacion',$campos);
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
