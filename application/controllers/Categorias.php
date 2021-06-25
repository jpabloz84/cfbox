<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends CI_Controller {

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
		$id_empresa=$this->visitante->get_id_empresa();
		$vars['id_empresa']=$id_empresa;
		$this->load->view('vcategorias',$vars);
	}	
	function listar()
	{
		$res= array();
		$id_empresa=$this->visitante->get_id_empresa();
		$arr=$this->mservice->getwhere('categorias','id_empresa='.$id_empresa,'categoria ASC');
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
			$arreglo = array('categoria' => $valor);
		}


		if(strpos($name,"abreviacion-")!==false){
			$id=str_replace("abreviacion-", "",$name);
			$valor=$this->input->post('value');
			$arreglo = array('abreviacion' => $valor);
		}
		
		$condicional=array('id_categoria' => $id);
		$this->mservice->actualizar_registro('categorias',$arreglo,$condicional);
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
		$campos=array('id_categoria' =>$id_param);
		$id=$this->mservice->eliminar_registro('categorias',$campos);		
		}
		if($metodo=="POST")
		{
		$campos=json_decode(file_get_contents('php://input'), true);		
		$campos['id_empresa']		=$this->visitante->get_id_empresa();
		$id=$this->mservice->insertar('categorias',$campos);
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
