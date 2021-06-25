<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Remitos extends CI_Controller {
private $visitante;
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");
		$this->load->model("entidades/marticulos");
		
		$this->load->model("mservice");
		$this->visitante=unserialize($this->session->visitante);
	}
	 function index()
	{	

		if(!$this->loginOn() || !$this->visitante->permitir('prt_articulos',4))
		{
			$this->load->view('vlogin');
			return; 
		}
		
		$arrVariales=array();
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;		
		$arrVariales['id_remito']=0;		
		$arrVariales['estados']=$this->mservice->getwhere('estado','remito=1',"orden asc");
		$arrVariales['proveedores']=$this->mservice->getwhere('proveedores','',"proveedor asc");
		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		
		$this->load->view('operaciones/vremitos',$arrVariales);
		
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

function guardar(){
date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();
  $path='';
	$arrRow=array();
	$id_remito=0;
	$id_proveedor="";
	$detalle="";
	$estado='';
	$modificarprecio=0;
	$carrito=array();
 	if(!$this->loginOn() || !$this->visitante->permitir('prt_articulos',8))
	{
		$this->load->view('vlogin');
		return; 
	}
	$campos=json_decode(file_get_contents('php://input'), true);					
	$remito=array();
	try {
		$id_remito=(int)$campos["id_remito"];
		$id_proveedor=(int)$campos["id_proveedor"];
		$estado=$campos["estado"];
		$detalle=$campos["remito"];
		$modificarprecio=$campos["modificarprecio"];
		$carrito=$campos["carrito"];
		$this->mservice->begin_trans();
		if($this->mservice->numError()!=0)
	 		{
	 			$this->fwxml->numError=$this->mservice->numError();
		  	$this->fwxml->descError="Error al inicializar transaccion:".$this->mservice->descError();
		  	 goto salir;
	 		}
	 		if($id_remito>0){
	 			$cond=array('id_remito' => $id_remito);
				$this->mservice->eliminar_registro("remito_det", $cond);
				if($this->mservice->numError()!=0)
				{				
					$this->fwxml->numError=$this->mservice->numError();
					$this->fwxml->descError=$this->mservice->descError();
					goto salir;
				}
				$ret=array('remito' =>$detalle ,'estado'=>$estado,'id_usuario_estado'=>$this->id_usuario,'fe_estado'=>date('Y-m-d H:i:s'),'modificaprecio'=>$modificarprecio);
				$this->mservice->actualizar_registro("remito",$ret,array("id_remito"=>$id_remito));	
				if($this->mservice->numError()!=0)
		 		{
		 			$this->fwxml->numError=$this->mservice->numError();
			  	$this->fwxml->descError="Error al actualizar remito:".$this->mservice->descError();
			  	 goto salir;
		 		}
	 		}else{//limpia detalles del remito
	 			$ret=array('remito' =>$detalle ,'id_proveedor'=>$id_proveedor,'estado'=>$estado,'fecha'=>date('Y-m-d H:i:s'),'tipo'=>'E','id_usuario'=>$this->id_usuario,'id_usuario_estado'=>$this->id_usuario,'fe_estado'=>date('Y-m-d H:i:s'),'id_sucursal'=>$this->id_sucursal,'modificaprecio'=>$modificarprecio);
	 			$id_remito=$this->mservice->insertar('remito',$ret);
	 			if($this->mservice->numError()!=0)
		 		{
		 			$this->fwxml->numError=$this->mservice->numError();
			  	$this->fwxml->descError="Error al crear remito:".$this->mservice->descError();
			  	 goto salir;
		 		}
	 		}//remito=0
	 		$nro_item=0;
	 		foreach ($carrito as $articulo) {
	 			$nro_item+=1;
	 			$det=array('id_remito' =>$id_remito ,'nro_item'=>$nro_item,'id_articulo'=>$articulo['id_articulo'],'cantidad'=>$articulo['cantidad'],'precio_costo'=>$articulo['precio_costo']);
	 			//actualiza precio siempre y cuando el usuario lo haya seleccionado y el estado sea EMITIR
	 			if($estado=='E')
	 			{
				$det['precio_venta']=(float)$articulo['precio_venta'];
				$exito=$this->marticulos->actualizar_stock_precio($articulo['id_articulo'],$articulo['cantidad'],$det['precio_venta'],$modificarprecio);
					if(!$exito)
					{
						$this->fwxml->numError=$this->marticulos->numError();
						$this->fwxml->descError="Error al actualizar articulo ".$this->marticulos->descError();
					 goto salir;	
					}
					$rsart=$this->mservice->getwhere("articulos","id_articulo=".$articulo['id_articulo'],"");
					$det['stock']=$rsart[0]['stock'];
					$det['precio_base']=$rsart[0]['precio_base'];
					$det['precio_iva']=$rsart[0]['precio_iva'];
	 			}
	 			$this->mservice->insertar('remito_det',$det);
		 			if($this->mservice->numError()!=0)
			 		{
			 			$this->fwxml->numError=$this->mservice->numError();
				  	$this->fwxml->descError="Error al insertar detalles del remito:".$this->mservice->descError();
				  	 goto salir;
			 		}
	 		}//for de carrito

	 		//solo se genera comprobante cuando se pasa a emitido
	 		if($estado=='E'){
	 			$path=$this->generarComprobante($id_remito,$modificarprecio);
	 			if($this->fwxml->numError!=0){
	 				goto salir;
	 			}
	 		}
		
		$this->mservice->commit_trans();
	 			//$this->mservice->rollback_trans();
		if($this->mservice->numError()!=0)
 		{
 			$this->fwxml->numError=$this->mservice->numError();
	  	$this->fwxml->descError="Error commit_trans:".$this->mservice->descError();
	  	$this->mservice->rollback_trans();
 		}
	} catch (Exception $e) {
	$this->fwxml->numError=100;
    $this->fwxml->descError=$e->getMessage();
    $this->mservice->rollback_trans();
  }//catch
	salir:
		if($this->fwxml->numError!=0)
		{
			$this->mservice->rollback_trans();				
		}
		$res=$this->mservice->getwhere("verremitos","id_remito=$id_remito","");	
		if(count($res)>0)
		{
			$estado=$res[0]['estado'];	
		}
	$this->fwxml->arrResponse['estado']=$estado;
	$this->fwxml->arrResponse['id_remito']=$id_remito;
	$this->fwxml->arrResponse['path']=$path;
	$this->fwxml->ResponseJson();
}//guardar

function generarComprobante($id_remito,$modificarprecio)
	{
		$filecomp='';
		$this->load->library('documento');
		$this->id_sucursal=$this->visitante->get_id_sucursal();
		$rs=$this->mservice->get("versucursales",array('sucursal, direccion ,telefono,localidad_sucursal,provincia_sucursal,cuil, empresa,ingresos_brutos,condicion,inicio_actividades'),"id_sucursal=".$this->id_sucursal);
		$sucursal=$rs[0];
		$empresa=$sucursal['empresa'];
		$sucursal_direccion=$sucursal['direccion'];
		$localidad_sucursal=$sucursal['localidad_sucursal'];
		$provincia_sucursal=$sucursal['provincia_sucursal'];
		$sucursal_telefono=$sucursal['telefono'];
		$empresa_cuil=$sucursal['cuil'];
		$empresa_condicion=$sucursal['condicion'];
		$ingresos_brutos=$sucursal['ingresos_brutos'];
		$inicio_actividades=$sucursal['inicio_actividades'];
		
		/*SELECT sucursal, direccion ,telefono,localidad_sucursal,provincia_sucursal,cuil, empresa,ingresos_brutos,condicion,inicio_actividades FROM versucursales*/
		$xmldata=$this->mservice->getxmldata("verremitos_det",array("*,'$empresa' as empresa, '$sucursal_direccion' as sucursal_direccion,'$localidad_sucursal' as localidad_sucursal,'$provincia_sucursal' as provincia_sucursal,'$sucursal_telefono' as sucursal_telefono,'$empresa_cuil' as empresa_cuil,'$empresa_condicion' as empresa_condicion, '$ingresos_brutos' as ingresos_brutos, '$inicio_actividades' as inicio_actividades"),"id_remito=$id_remito","");
		if($this->mservice->numError()==0)
		{ 
			if($modificarprecio==1)
			{
			$this->documento->parseXsl('tpl/remito_conprecios.xsl',$xmldata);	
			}	else{
			$this->documento->parseXsl('tpl/remito_sinprecios.xsl',$xmldata);	
			}
		
		$file=randString(10).".html";
		$filecomp=$this->documento->save('files/'.$file);	
			if($this->documento->numError()==0){
				$cols=array("path" => $filecomp);
				$cond=array("id_remito"=> $id_remito);
				$this->mservice->update("remito",$cols,$cond);
					if($this->mservice->numError()!=0)
			 		{	
			 			$this->fwxml->numError=$this->mservice->numError();
				  	$this->fwxml->descError="Error actualizar comprobante:".$this->mservice->descError();
			 		}	
				//print_r("error:$id_comp->.".$this->mservice->descError());
			}else{
				$this->fwxml->numError=$this->documento->numError();
				$this->fwxml->descError="Error al crear documento:".$this->documento->descError();
			}
		}
		return $filecomp;
	}//generar comprobante



	function editar($id_remito){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_articulos',8))
		{
			$this->load->view('vlogin');
			return; 
		}
		$arrVariales=array();
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;		
		$arrVariales['estados']=$this->mservice->getwhere('estado','remito=1',"orden asc");
		$arrVariales['proveedores']=$this->mservice->getwhere('proveedores','',"proveedor asc");
		$arrVariales['id_remito']=$id_remito;		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		
		$this->load->view('operaciones/vremito_operar',$arrVariales);
		

	}//editar



	function alta(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_articulos',8))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		
		$arrVariales['visitante']=$this->visitante;		
		$arrVariales['id_remito']=0;
		$arrVariales['categorias']=$this->mservice->getCategorias();
		$arrVariales['estados']=$this->mservice->getwhere('estado','remito=1',"orden asc");
		$arrVariales['proveedores']=$this->mservice->getwhere('proveedores','',"proveedor asc");
		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		$this->load->view('operaciones/vremito_operar',$arrVariales);

	}//operar

	
	

}//class
