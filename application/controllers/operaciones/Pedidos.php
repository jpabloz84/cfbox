<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {
private $visitante;
private $definiciones=array(); //para uso del oyente

private $id_sucursal;
private $id_empresa;
private $id_usuario;
	function  __construct()
	{ date_default_timezone_set('America/Argentina/Buenos_Aires');    
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");		
		$this->load->library("listener");
		$this->load->model("mservice");
    $this->load->model("operaciones/mpedidos");
    
		$this->visitante=unserialize($this->session->visitante);
		
    /*cargo las definiciones de consultas sql*/
    $this->listener->load_defs($this->mpedidos->definiciones($this->visitante->get_id_empresa()));
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
		$arrVariales['visitante']=$this->visitante;	
		$arrVariales['estados']	=$this->mpedidos->estados();
		
		$this->load->view('operaciones/vpedidos',$arrVariales);
		
	}	

	function loginOn(){
		$exito=true;
		
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

		
	function listener(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_pedidos',1))
		{
			$this->load->view('vlogin');
			return; 
		}
    $json=json_decode($this->input->post("data"),true);    
    $definicion=$this->input->post("definicion");
    $where=array();
    if($definicion=="selpedidos"){
    	$nro_pedido=$json['nro_pedido'];
      $fecha=$json['fecha'];
    	$nombres=$json['nombres'];    	
    	$estado=$json['estado'];    	
    	if($nro_pedido!=""){
    		$where['pedido']="and id_pedido=$nro_pedido";
    	}else{
    		if($fecha!=""){
    		$where['fecha']=" and fecha>='".todate($fecha,103)."' and fecha<ADDDATE('".todate($fecha,103)."',1)";	
    		}
    		if($estado!=""){
    		$where['estado']=" and estado='$estado'";
    		}
    		if($nombres!=""){
    			$where['nombres']=" and strnombrecompleto like '%$nombres%'";
    		}
    	}
    }
    if($definicion=="selpedidosproductos"){
    	$id_pedido=$json['id_pedido'];
    	$where['idpedido']=" and id_pedido=$id_pedido";
    }
    $this->listener->get_transform($definicion,$where);

  }

  function guardar(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_pedidos',2))
		{
			$this->fwxml->numError=0;
      $this->fwxml->descError="sin permisos";
      $this->fwxml->ResponseJson();
			return; 
		}
		$campos=json_decode(file_get_contents('php://input'), true);
    $metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE
    
    if($metodo=="PUT")
    {
    $entregado=($campos['entregado']==1)?1:0;
    $fecha=date("Y-m-d H:i:s");
    if(($entregado==1 && $this->visitante->permitir('prt_pedidos',2))
      ||($entregado==0 && $this->visitante->permitir('prt_pedidos',3))){
      $this->mentregas->marcar_entrega($entregado,$this->visitante->get_id(),$campos['id_carton'],$campos['nro_premio']);
    
    $this->fwxml->numError=$this->mentregas->numError();
    $this->fwxml->descError=$this->mentregas->descError();
    }

    if($entregado==1 && !$this->visitante->permitir('prt_pedidos',2)){
     $this->fwxml->descError="no tiene permisos para otorgar (entregar) premios";
    }
    if($entregado==0 && !$this->visitante->permitir('prt_pedidos',4)){
     $this->fwxml->descError="no tiene permisos para deshacer entregas";
    }
   
   
    }

    
    if($this->mservice->numError()!=0)
    {
    $this->fwxml->numError=$this->mservice->numError();
    $this->fwxml->descError=$this->mservice->descError();
    }
    
		$this->fwxml->ResponseJson();
	}

function cambiarestado(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_pedidos',2))
		{
			$this->fwxml->numError=0;
      $this->fwxml->descError="sin permisos";
      $this->fwxml->ResponseJson();
			return; 
		}
		$id_pedido=$this->input->post("id_pedido");
		$estado=$this->input->post("estado");
		if(!$this->mpedidos->cambiarestado($id_pedido,$estado,$this->visitante->get_id_empresa())){
			$this->fwxml->numError=$this->mpedidos->numError();
			$this->fwxml->descError=$this->mpedidos->descError();
		}
	$this->fwxml->ResponseJson();
}
  
	

}//class
