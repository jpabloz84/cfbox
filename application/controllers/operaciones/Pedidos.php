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
	 function index($id_pedido=0)
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
		$arrVariales['id_pedido']=$id_pedido;
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

  /*function guardar(){
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
	}*/

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


 function nuevo()
	{	

		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['modo']="NP"; //nuevo pedido
		$arrVariales['origen']="operaciones/pedidos/nuevo/"; //nuevo pedido
		$this->load->view('operaciones/vconsultas',$arrVariales);
		
	}	

	function generar_pedido()
{
 
  $this->fwxml->numError=0; 
  $this->fwxml->descError="";
  $errores=array();
  
  $this->load->model("operaciones/mpresupuesto");
  $this->load->model("entidades/mproductos"); 
  
  $fecha=date("Y-m-d H:i:s");
  $id_empresa=$this->visitante->get_id_empresa();
  $message="pedido {id_pedido}\r\n";
  try{
    
    
    $chk=json_decode($this->input->post('chk'),true);

    $carrito=$chk['carrito'];
    $apellido=$chk['apellido'];
    $nombres=$chk['nombres'];
    $telefono=$chk['telefono'];
    $calle=$chk['calle'];
    $nro=$chk['nro'];
    $piso=$chk['piso'];
    $depto=$chk['depto'];
    $resto=$chk['resto'];
    $nota=$chk['nota'];
    $id_tipo_envio=$chk['id_tipo_envio'];
    $id_localidad=$chk['id_localidad'];
    $id_tipo_pago=$chk['id_tipo_pago'];
    $id_sucursal=$chk['id_sucursal'];
    $id_cliente=$chk['id_cliente'];
    $id_pedido=(isset($chk['id_pedido'])?$chk['id_pedido']:0);
    //var_dump($carrito);
    $productos=array();

    

    $presupuesto=array('id_pedido'=>$id_pedido,'id_cliente'=>$id_cliente,'fecha'=>$fecha,'id_empresa'=>$id_empresa,'apellido'=>$apellido,'nombres'=>$nombres,'telefono'=>$telefono,'calle'=>$calle,'nro'=>$nro,'piso'=>$piso,'depto'=>$depto,'resto'=>$resto,'id_tipo_envio'=>$id_tipo_envio,'id_tipo_pago'=>$id_tipo_pago,'id_sucursal'=>$id_sucursal,'id_localidad'=>$id_localidad,'nota'=>$nota);
    foreach ($carrito as  $p) {
    $productos[]=array('id_tipo' =>$p['id_tipo'] ,'nro_tipo'=>$p['nro_tipo'],'cantidad'=>$p['cantidad']);
    }
    $this->mpresupuesto->mproductos=$this->mproductos;
      $id_pedido=$this->mpresupuesto->generar_presupuesto($presupuesto,$productos,$id_empresa,$fecha,$errores);
      if($this->mpresupuesto->numError()!=0){
        $this->fwxml->numError=$this->mpresupuesto->numError();
        $this->fwxml->descError=$this->mpresupuesto->descError();
        if($this->fwxml->numError==1000){
          $this->fwxml->arrResponse['error_productos']=$errores;//devuelvo que productos tienen errores
        }
      }else{
        $mensaje=$this->mpresupuesto->getmessage($id_pedido);
        $this->fwxml->arrResponse['mensaje']=$mensaje;        
        $this->fwxml->arrResponse['id_pedido']=$id_pedido;
       
      }
  }catch (Exception $e){
     $this->fwxml->numError=1;
     $this->fwxml->descError=$e->getMessage(); 
  }  
  salir:
  $this->fwxml->ResponseJson();   
}//generar_pedido


  
	

}//class
