
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {
private $visitante;
private $id_usuario;
private $id_sucursal;
private $lastrequest="";
private $lastresponse="";
function  __construct()
{  
	parent::__construct();		
	$this->load->library("listener");
	$this->load->model("seguridad/mpermisos");
	$this->load->model("seguridad/musuarios");
	$this->load->model("mservice");
	$this->load->model("mpagos");
	$this->load->model("entidades/mclientes");
	$this->load->model("operaciones/mpedidos");
	$this->load->model("operaciones/meventos");
	$this->load->model("operaciones/mventas");
	$this->load->library('Qrlib');
	$this->visitante=unserialize($this->session->visitante);
	/*cargo las definiciones de consultas sql*/
    $this->listener->load_defs($this->mventas->definiciones($this->visitante->get_id_empresa()));
}
 function index()
{	
	if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',1))
	{
		$this->load->view('vlogin');
		return; 
	}
}	

	

function loginOn(){
	$exito=true;
	$this->visitante=unserialize($this->session->visitante);
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

function actualizar_operador(){

	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();
	$id_comp=$this->input->post("id_comp");
	$id_vendedor_opera=$this->input->post("id_vendedor_opera");
 	if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',8))
	{
		$this->load->view('vlogin');
		return; 
	}

	$this->mventas->actualizar_vendedor($id_comp,$id_vendedor_opera);
	if($this->mventas->numError()!=0){
		$this->fwxml->numError=$this->mventas->numError();
		$this->fwxml->descError=$this->mventas->descError();
	}

	$this->fwxml->ResponseJson();		

}



//sea un comprobante, si fue presentado ante afip, crea su nota de credito, sino, simplemente lo anula
function anular(){
	date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();
	$id_comp_nc=0;
	$pagos=null;
	$representa=false;
	 $arrRow=array();
 	if(!$this->loginOn() || !$this->visitante->permitir('prt_comprobantes',8))
	{
		$this->load->view('vlogin');
		return; 
	}	
	$campos=json_decode(file_get_contents('php://input'), true);
	//print_r($campos);
	$id_comp=$campos['id_comp'];
	$comp=array();
  $companula=array();
try{
$this->mservice->begin_trans();
$rcomp=$this->mservice->getwhere("vercomprobantes","id_comp=".$id_comp." and ((estado='E' and cae is null) or (estado='F' and cae is not null))","");					

	if(!count($rcomp)>0)
		{
		$this->fwxml->numError=1;
  	$this->fwxml->descError="No hay comprobante por anular";
  	goto salir;
		}

$companula=$rcomp[0];
$representa=($companula['id_cliente_representante']>0)?true:false;//indica si el comprobante a anular pertenece a un representado
$id_tipo_comp=$rcomp[0]['id_tipo_comp'];
//anula pedido siempre y cuando el comprobante sea parte de un pedido
$this->mpedidos->anular_pedido($id_comp,$this->visitante->get_id_empresa());


 if($companula['estado']=='F'){
 	$this->fwxml->arrResponse['afip']=1;
 	//se marca como  anulado el comprobante en la BD
	$condicional=array('id_comp' => $id_comp);
	$cmp=array('estado'=>'A','id_usuario_estado'=>$this->id_usuario,'fe_estado'=>date('Y-m-d H:i:s'));
		//cambio a anulado el comprobante en cuestion
	$this->mservice->actualizar_registro('comp',$cmp,$condicional);
	if($this->mservice->numError()!=0)
	{
	$this->fwxml->numError=$this->mservice->numError();
	$this->fwxml->descError=$this->mservice->descError();
	goto salir;
	}
	//devuelvo aquellos articulos que mueven stock
	if(!$this->mventas->devolver_stock($id_comp))
	{
		$this->fwxml->numError=$this->mventas->numError();
		$this->fwxml->descError=$this->mventas->descError();		
	goto salir;
	}
	
	
	$id_tipo_anula=0;
	$tipocmpanula=$this->mservice->getwhere("tipo_comprobante","id_tipo_comp=$id_tipo_comp","");
	if(count($tipocmpanula)>0)
	{
		$id_tipo_anula=(int)$tipocmpanula[0]['id_tipo_anula'];
	}
	if(!$id_tipo_anula>0)
	{
	$this->fwxml->numError=2;
	$this->fwxml->descError="No hay comprobante afip configurar que permita anular";		
	goto salir;
	}
	$talonarios=$this->mservice->getwhere("vertalonarios","habilitado=1 and id_tipo_comp=$id_tipo_anula and id_sucursal=".$this->id_sucursal,"tipo_comp");					
	if($this->mservice->numError()!=0)
	{
	$this->fwxml->numError=$this->mservice->numError();
	$this->fwxml->descError="Error al consultar talonarios:".$this->mservice->descError();
 	 goto salir;
	}
	if(!count($talonarios)>0)
	{
	$this->fwxml->numError=4;
	$this->fwxml->descError="no hay talonario configurado para anular comprobante";		
	goto salir;
	}
	//creo el comprobante COMPLEMENTARIO en afip que permite anularlo
	if(!$this->mventas->crear_companula($id_comp,$this->id_usuario,$this->id_sucursal,$id_comp_nc))
	{
	$this->fwxml->numError=$this->mventas->numError();
	$this->fwxml->descError=$this->mventas->descError();
	goto salir;
	}

	if(!($this->enviarAfip($id_comp_nc)))
	{	$this->fwxml->arrResponse['lastrequest']=$this->lastrequest;
		$this->fwxml->arrResponse['lastresponse']=$this->lastresponse;
		$this->fwxml->numError=30;		//la nc se genero pero no se pudo enviar a afip
		if($this->descError==""){
			$this->fwxml->descError="No se pudo anular el comprobante. No se hicieron los cambios.";
		}else{
		$this->fwxml->descError=$this->descError;	
		}
	
	goto salir;
	}

	//actualizo el id del comprobante que lo anula
	$condicional=array('id_comp' => $id_comp);
	$cmp=array('id_comp_anula'=>$id_comp_nc);		
	$this->mservice->actualizar_registro('comp',$cmp,$condicional);
	if($this->mservice->numError()!=0)
	{
	$this->fwxml->numError=$this->mservice->numError();
	$this->fwxml->descError="Error actualizar el comprobante que lo anula:".$this->mservice->descError();
 	 goto salir;
	}


	$this->fwxml->arrResponse['id_comp']=$id_comp_nc;
 	}else
 	{
 		$this->fwxml->arrResponse['afip']=0;
 		$this->fwxml->arrResponse['id_comp']=$id_comp;
		$condicional=array('id_comp' => $id_comp);
		$cmp=array('estado'=>'A','id_usuario_estado'=>$this->id_usuario,'fe_estado'=>date('Y-m-d H:i:s'));
		$this->mservice->actualizar_registro('comp',$cmp,$condicional);
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
  	$this->fwxml->descError=$this->mservice->descError();		
  	goto salir;
		}
		if(!$this->mventas->devolver_stock($id_comp))
		{
		$this->fwxml->numError=$this->mventas->numError();
  	$this->fwxml->descError=$this->mventas->descError();		
  	goto salir;
		} 			
 	}//else anula orden de pedido

 	if(!$this->mpagos->anular_pagos_comp($id_comp,$this->id_usuario,false)){
		$this->fwxml->numError=$this->mpagos->numError();
		$this->fwxml->descError=$this->mpagos->descError();		
	goto salir;	
	}

//en ambos casos, hay que desimputar los pagos
	$this->mservice->eliminar_registro('comp_pagos',array('id_comp' => $id_comp));		
if($this->mservice->numError()!=0)
{
$this->fwxml->numError=$this->mservice->numError();
$this->fwxml->descError="Error al desimputar pagos del comprobante".$this->mservice->descError();
 goto salir;
}
//si existe presupuesto con id_comp afectado, lo anula
$this->mpedidos->anular_pedido($id_comp,$this->visitante->get_id_empresa());	

$this->mservice->commit_trans();
if($this->mservice->numError()!=0)
	{
	$this->fwxml->numError=$this->mservice->numError();
	$this->fwxml->descError="Error commit_trans:".$this->mservice->descError();
	$this->fwxml->arrResponse['id_comp']=0;
	 goto salir;
	}
}catch(Exception $e){
	$this->fwxml->numError=100;
  $this->fwxml->descError=$e->getMessage();
  $this->mservice->rollback_trans();
}
salir:
if($this->fwxml->numError!=0)
{
	$this->mservice->rollback_trans();				
}
//en el momento de anular, solo genera comprobante cuando se genere una nota de credito
if($this->fwxml->numError==0 && $id_comp_nc>0)			
{	
	$pathfile=$this->generarComprobante($id_comp_nc,true,$representa);
	$this->fwxml->arrResponse['path']=$pathfile;
}
//solo se envian datos del comprobante cuando se haya enviado a afip (NC)
if($id_comp_nc>0)
{
	$res=$this->mservice->getwhere("vercomprobantes","id_comp=$id_comp_nc","");
	if(count($res)>0)
	{$afipcmp=array("tipo_comp"=>$res[0]['tipo_comp'],"nro_talonario"=>$res[0]['nro_talonario'],"nro_comp"=>$res[0]['nro_comp'],"cae"=>$res[0]['cae']);
	$this->fwxml->arrResponse['afipcmp']=$afipcmp;	
	}	
}
$this->fwxml->ResponseJson();		
}//anular
//sea una comprobante en estado P o E, cambia al estado posterior
function cambiar_estado($id_comp=0){
	
	date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();
  $pagos=null;
	 $arrRow=array();
	 //si esta logeado y tiene permisos de vender
 	if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',2))
	{
		$this->load->view('vlogin');
		return; 
	}
	$campos=json_decode(file_get_contents('php://input'), true);					
	$comp=array();
}



//esta peticion genera un comprobante en P o E o F 
function generar()
{ 	$stlog="";
	date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();
	$id_vendedor_opera=0;
  $pagos=null;
	$arrRow=array();
	$talonario=null;
	$estadoOld="";
	$isUpdate=false;
	$compdetOld=array();
	$notadecredito=false;
	$stlog.="p1;";
 	if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',2))
	{
		$this->load->view('vlogin');
		return; 
	}
	$campos=json_decode(file_get_contents('php://input'), true);		
	$comp=array();
  $id_comp=(int)$campos['id_comp'];
  $id_pedido=(int)$campos['id_pedido'];
  $representa=((int)$campos['representa']==1)?true:false;

	try {
		$this->mservice->begin_trans();
		$stlog.="p2;";
		if($this->mservice->numError()!=0)
 		{
 		$this->fwxml->numError=$this->mservice->numError();
	  	$this->fwxml->descError="Error begin_trans:".$this->mservice->descError();
	  	 goto salir;
 		}

 		//cambia de estado a posterior si el estado es P o E 
		if($id_comp>0)
		{
			$stlog.="p3;";
			$isUpdate=true;
			$rscomp=$this->mservice->getwhere("vercomprobantes","id_comp=$id_comp and estado in('P','E')","");
			$stlog.="p3;";
			//si el comprobante no existe o no esta dentro de los estados validos a cambiar, que salga
				if(count($rscomp)>0){
					$stlog.="p4;";
					$cmOld=$rscomp[0];
					$estadoOld=$cmOld['estado']; //guardo el estado anterior
					$rsestados=$this->mservice->getwhere("estado","estado in('P','E','F')","orden asc");
					$compdetOld=$this->mservice->getwhere("comp_det","id_comp=$id_comp","");
					$guardar=false;

					foreach ($rsestados as $es) {
						$stlog.="p5;";
							if($guardar){
								$campos['estado']=$es['estado'];
								if($campos['estado']=="F")
								{
									$campos['afip']=1;
								}
								break;
							}
							//si es este estado, tomo el siguiente
							if($es['estado']==$cmOld['estado'])
							{
								$guardar=true;
							}
					}//for de estados
				}else{
					$stlog.="p6;";
				$this->fwxml->numError=11;
			  	$this->fwxml->descError="El comprobante no está en un estado modificable";
			  	 goto salir;
				}
			}//if si viene con comrpobante

			//obtengo los productos
		$productos=$campos['carrito'];
		//obtengo el cliente
		$cliente=$campos['cliente'];
		$ctacte=$campos['ctacte'];
		$id_tipo_comp=$campos['id_tipo_comp'];
		$afip=($campos['afip']==1)?true:false;
		$pagos=$campos['pagos'];
		$stlog.="p7;";
		//si desde el front, viene un nuevo comprobante , con bandera AFIP=1 , el estado que se quiere guardar es "F" de facturado
		if($afip && $campos['estado']=="E" && $id_comp==0)
		{
		$stlog.="p8;";
		$estado="F";
		}else{
		$stlog.="p9;";
		$estado=$campos['estado'];	
		}
		
		//$cond="habilitado=1 and id_tipo_comp=$id_tipo_comp and id_sucursal=".$this->id_sucursal;
		$talonarios=array();
		if($afip){
			$stlog.="p10;";
			$talonarios=$this->mservice->getwhere("vertalonarios","habilitado=1 and id_tipo_comp=$id_tipo_comp and id_sucursal=".$this->id_sucursal,"tipo_comp");
		
				if($this->mservice->numError()!=0)
		 		{
		 			$stlog.="p11;";
		 		$this->fwxml->numError=$this->mservice->numError();
			  	$this->fwxml->descError="Error al consultar talonarios:".$this->mservice->descError();
			  	 goto salir;
		 		}
				if(!count($talonarios)>0 && $afip && $campos['estado']=='F')
				{
					$stlog.="p12;";
				$this->fwxml->numError=1;
		  	  $this->fwxml->descError="No hay talonario habilitado para este tipo de comprobante";
		  	  $comp['id_talonario']='';			  	  
		  	  goto salir;
				}	
				if($afip && count($talonarios)>0){
					$stlog.="p13;";
					$talonario=$talonarios[0];
					if($talonario['movimiento']=='E' && strpos($talonario['tipo_comp'],"CREDITO")!==false){
						$stlog.="p14;";
						$notadecredito=true;
					}
				}
		}//afip
		
		
		 //if(count($talonarios)>0 && $afip && $estado=='F')
		if(count($talonarios)>0 && $afip)
		{
		$stlog.="p15;";
		$comp['id_talonario']=$talonarios[0]['id_talonario'];			
		}
		$comp['id_cliente']=$cliente['id_cliente'];
		//si tiene permisos para cambiar de vendedor que opera la venta
		if(isset($cliente['id_vendedor_opera'])){
			$stlog.="p16;";
			if($cliente['id_vendedor_opera']!="" && $cliente['id_vendedor_opera']!="0"  && $this->visitante->permitir('prt_operaciones',8)){
				$stlog.="p17;";
				$id_vendedor_opera=(int)$cliente['id_vendedor_opera'];
			}
		}				
		$importe_total=0;
		$importe_neto=0;
		$importe_iva=0;
		$importe_tipo=0;
		$importe_exento=0;
		$importe_base=0;
		$importe_descuento=0;
		$comp_det=array();
		$comp_pagos=array();
		$pago=array();
		$nro_item=0;
		//genero los detalles del comprobante en funcion de los productos seleccionados
		foreach ($productos as $prod) {							
			$stlog.="p18;";
			$importe_total=$importe_total+((float)$prod['importe_total']);
			$importe_base=$importe_base+(((float)$prod['importe_base'])*((int)$prod['cantidad']));
			//$total_venta=$importe_tipo+((float)$prod['importe_tipo']);
			$importe_iva=$importe_iva+(((float)$prod['importe_iva'])*((int)$prod['cantidad']));
			if(isset($prod['importe_exento']))
			{
				$importe_exento=$importe_exento+(((float)$prod['importe_exento'])*((int)$prod['cantidad']));
			}
			if(isset($prod['importe_descuento']))
			{
				$importe_descuento=$importe_descuento+((float)$prod['importe_descuento']);
			}
			$nro_item=$nro_item+1;
			$comp_det[$nro_item]['nro_item']=$nro_item;
			$comp_det[$nro_item]['id_tipo']=$prod['id_tipo'];
			$comp_det[$nro_item]['nro_tipo']=$prod['nro_tipo'];
			$comp_det[$nro_item]['precio_base']=$prod['importe_base'];					
			$comp_det[$nro_item]['precio_iva']=$prod['importe_iva'];
			$comp_det[$nro_item]['precio_venta']=$prod['importe_tipo'];
			$comp_det[$nro_item]['importe_item']=$prod['importe_total'];
			$comp_det[$nro_item]['cantidad']=$prod['cantidad'];
			$comp_det[$nro_item]['detalle']=trim($prod['producto']);
			$comp_det[$nro_item]['iva']=0;
			$comp_det[$nro_item]['importe_descuento']=(isset($prod['importe_descuento']))?$prod['importe_descuento']:0;

		$rsPr=$this->mservice->getwhere("verproductos","nro_tipo=".$prod['nro_tipo']." and id_tipo=".$prod['id_tipo']." and id_empresa=".$this->visitante->get_id_empresa(),"");

			if(count($rsPr)>0)
			{
				$stlog.="p19;";
				$comp_det[$nro_item]['iva']=$rsPr[0]['iva'];	
			}
		}//for que armar los detalles del comprobante

	$importe_neto=$importe_total-$importe_iva-$importe_exento+$importe_descuento;
	$comp['importe_base']=$importe_base;
	$comp['importe_neto']=$importe_neto;
	$comp['importe_descuento']=$importe_descuento;
	$comp['importe_exento']=$importe_exento;
	$comp['importe_iva']=$importe_iva;
	$comp['importe_total']=$importe_total;	
	if($campos['representa']==1){
	$stlog.="p20;";
	$comp['id_cliente_representante']=$cliente['id_cliente_representante'];
	$comp['cuit']=$cliente['cuit_representante'];
	}else{
	$stlog.="p21;";
	$comp['cuit']=$cliente['cuit'];	
	}
	
	$comp['estado']=$estado;
	//solo si es la primera vez que se inserta (se crea el comprobante), guardo el usuario
	if($id_comp==0)
	{
	$stlog.="p22;";
	$comp['id_usuario']=($id_vendedor_opera>0)?$id_vendedor_opera:$this->id_usuario;
	$comp['fe_creacion']=date('Y-m-d H:i:s');	
	}
	
	$comp['fe_estado']=date('Y-m-d H:i:s');	
	$comp['id_usuario_estado']=$this->id_usuario;
	$comp['afip']=$campos['afip'];
	$comp['id_sucursal']=$this->id_sucursal;		 		
	$comp['ctacte']=$campos['ctacte'];
	
	if($id_comp>0)
	{	
	$stlog.="p23;";
	$this->mservice->actualizar_registro('comp',$comp,array('id_comp' =>$id_comp));		
	}else{
	$stlog.="p24;";
	$id_comp=$this->mservice->insertar('comp',$comp);		
	}	
	if($this->mservice->numError()!=0)
	{
		$stlog.="p25;";
	$this->fwxml->numError=$this->mservice->numError();
	$this->fwxml->descError="Error insertar o actualizar comprobante:".$this->mservice->descError();
	 goto salir;
	}

	//elimino los detalles de los comprobantes en caso se que se cambie a un nuevo estado
	if($id_comp>0)
	{		
	$stlog.="p26;";
	$this->mservice->eliminar_registro('comp_det',array('id_comp' => $id_comp));		
	if($this->mservice->numError()!=0)
		{
		$stlog.="p27;";
		$this->fwxml->numError=$this->mservice->numError();
		$this->fwxml->descError="Error items del comprobante".$this->mservice->descError();
		 goto salir;
		}
	}	

	$ocurrencias=array();
	
	//recorro cada item y verifico si mueve o no stock
 		foreach ($comp_det as $det){
	 			$det['id_comp']=$id_comp;	 			
	 			$stlog.="p28;";
	 			//si es articulo, controlo que si mueve stock, hay en cantidad siempre y cuando se esté por guardar la factura en F o E (P ->NO) . 
	 			//Para notas de creditos se establece no actualziar stock ya que se supen que uno puede
	 			//confeccionar notas de creditos por otras indoles
	 			if($det['id_tipo']==1 && ($campos['estado']=='E' || $campos['estado']=='F') && !$notadecredito)
	 			{
	 				$stlog.="p29;";
	 				$ocurrencias=$this->mventas->actualizar_stock($det['id_tipo'],$det['nro_tipo'],$det['cantidad'],$compdetOld);
	 				if($this->mventas->numError()!=0){
	 					$stlog.="p30;";
	 					$this->fwxml->numError=$this->mventas->numError();
			  	$this->fwxml->descError="Error actualizar stock:".$this->mventas->descError();
			  	 goto salir;
	 				}
	 				
	 			}
	 			
	 			$this->mservice->insertar('comp_det',$det);
	 			if($this->mservice->numError()!=0)
		 		{$stlog.="p32;";
		 			$this->fwxml->numError=$this->mservice->numError();
			  	$this->fwxml->descError="Error insertar detalle del comprobante:".$this->mservice->descError();
			  	 goto salir;
		 		}
	 		}//for que controla cada detalle del comprobante

	 		if(count($ocurrencias)>0)
			{	
			$html="";
			$stlog.="p33;";
				foreach ($ocurrencias as $error_det) {
			 			$html.=$error_det."\n";
			 	}
	 			$this->fwxml->numError=10;
			  $this->fwxml->descError=$html;
			  goto salir;
	 		}
	 		
	 		//generare una orden de pago (no va a afip)	 		
			if(($estado=='E' || $estado=='F') && $id_pedido>0){				
			 	$this->mpedidos->facturar_pedido($id_pedido,$id_comp,$this->visitante->get_id_empresa());	
			 	if($this->mpedidos->numError()!=0)
			 		{	
			 		$this->fwxml->numError=$this->mpedidos->numError();
			  		$this->fwxml->descError=$this->mpedidos->descError();			
				  	 goto salir;
			 		}
			}	
		 	$this->fwxml->arrResponse['id_comp']=$id_comp;
	}catch (Exception $e){
		$stlog.="p36;";
		$this->fwxml->numError=100;
    $this->fwxml->descError=$e->getMessage();
    $this->mservice->rollback_trans();
  }
	salir:
			
		//se considera en estado emitido y se envia afip que si se rechaza, queda en emitido
		if($this->fwxml->numError==0 && $afip && ($campos['estado']=="E" || $campos['estado']=="F"))	
		{
			$stlog.="p36;";
				if(!$this->enviarAfip($id_comp))
				{//le saco que es afip porq no se pudo hacer
				$this->fwxml->arrResponse['lastrequest']=$this->lastrequest;
				$this->fwxml->arrResponse['lastresponse']=$this->lastresponse;
				$stlog.="p37;";
				if(!$notadecredito){
					$stlog.="p38;";
					$this->mservice->actualizar_registro("comp",array('afip' =>0,'estado'=>'E'),array('id_comp' =>$id_comp));
					$afip=false;
					$this->fwxml->numError=30;		//la venta se genero pero no se pudo enviar a afip
				}
				if($notadecredito){
					$stlog.="p39;";
					$afip=false;
					$this->fwxml->numError=31;		//la note de credito no se genero
					//$this->mservice->rollback_trans();
				}
				
				if($this->descError==""){
					$stlog.="p40;";
					$this->fwxml->descError="No se pudo procesar el comprobante en afip. Intente luego.";
				}else{
					$stlog.="p41;";
				$this->fwxml->descError=$this->descError;	
				}
			
			}else{
				$stlog.="p42;";
				if($afip){
					$stlog.="p43;";
					//si es una nota de credito, agregar como pago e imputar a favor del cliente
					if($notadecredito){
						$stlog.="p44;";
						//$this->imputar_pagos_comp($pagos,$id_comp);
						$pago=array('pago' =>$comp['importe_total'] ,'id_tipo_pago'=>10,'id_cliente'=>$comp['id_cliente'],'id_usuario'=>$comp['id_usuario'],'id_caja_op'=>$this->visitante->get_id_caja_opera(),'id_usuario_estado'=>$comp['id_usuario'] ,'id_comp_ref'=>$id_comp);
						if(!$this->mpagos->pagoadd($pago)){
							$stlog.="p45;";
							$this->fwxml->numError=$this->mpagos->numError();
							$this->fwxml->descError=$this->mpagos->descError();
						}else{
							$stlog.="p46;";
							//si ingreso pago, imputo
							$this->mpagos->imputar_pago($pago['id_pago'],$comp['id_usuario']);
						}
					}//nota de credito
				}
				
			}
			
		}

		
		//si dio error o era una nota de credito q no se pudo enviar, hacemos roll sino commit
		if($this->fwxml->numError!=0 && $this->fwxml->numError!=30)
		{
			$stlog.="p49;";
			$this->mservice->rollback_trans();				
		}else{
			$stlog.="p50;";
			$this->mservice->commit_trans();
			if($this->mservice->numError()!=0)
			{
				$stlog.="p51;";
			$this->fwxml->numError=$this->mservice->numError();
			$this->fwxml->descError="Error efectuar venta. Puede que no se haya hecho";
			$this->fwxml->arrResponse['id_comp']=0;
			$this->fwxml->arrResponse['error']=$this->mservice->descError();
			}
			$this->imputar_pagos_comp($pagos,$id_comp); //pongo aca porq sino no hace transsacciones correctas 
			//verifico si tiene saldo, imputo si tiene comprobantes  q adeuda(que no sea nota de credito)
			if($this->fwxml->numError==0 || $this->fwxml->numError==30 || !$notadecredito){
				$stlog.="p47;";
				//si es un cliente con cuenta corriente y no es consumido final, busco saldo a favor
				
				$saldo_afavor=$this->mclientes->saldo_afavor($cliente['id_cliente']);
				if($saldo_afavor>0){
					$stlog.="p48;";
						 $this->mpagos->imputar_con_saldo($id_comp,$this->id_usuario);
				}
				
				//agrego las cuotas necesarias hasta completar 12
				
			}
	 		

		}	
		//mientras no sea error de nota de credito, genero comprobante
		$pathfile="";
		if($this->fwxml->numError!=31){
		$stlog.="p52;";
		$pathfile=$this->generarComprobante($id_comp,$afip,$representa);
		$this->fwxml->arrResponse['path']=$pathfile;	
		}
		$res=$this->mservice->getwhere("vercomprobantes","id_comp=$id_comp","");
		$this->fwxml->arrResponse['afipcmp']="";
		if(count($res)>0)
		{	$stlog.="p53;";
			$afipcmp=array("tipo_comp"=>$res[0]['tipo_comp'],"nro_talonario"=>$res[0]['nro_talonario'],"nro_comp"=>$res[0]['nro_comp'],"cae"=>$res[0]['cae'],"estado" => $res[0]['estado']);

			$this->fwxml->arrResponse['afipcmp']=$afipcmp;	
			$this->fwxml->arrResponse['afip']=$res[0]['afip'];	
			$this->fwxml->arrResponse['estado']=$res[0]['estado'];	
		}
$this->fwxml->arrResponse['log']=$stlog;
$this->fwxml->ResponseJson();		
}
//se busca el articulo en el detalle viejo para sacar diferencia
function cant_articuloOLD($compdet,$id_articulo)
{	$cant=0;
	foreach ($compdet as $det) {
		if($det['id_tipo']==1 && $det['nro_tipo']==$id_articulo)
		{
			$cant=(float)$det['cantidad'];
			break;
		}
	}
	return $cant;
}



function imputar_pagos_comp($pagos,$id_comp=0)
{//pagos:array d elos pagos a insertar, $id_comp=id del comprobante, $modifcar:bandera que indicar que es la modificacion de los pagos del comprobante
	$exito=false;
	$pagos_inserted=array();
	$comp=array();
	$id_cliente=0;
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();
	$this->id_caja_op=$this->visitante->get_id_caja_opera();	
	//si viene con comprobante, se lo imputo a ese comprobante primero
	if($id_comp>0)
	{ 
		$ex=$this->mventas->imputar_pagos_comp($id_comp,$this->id_sucursal,$this->id_usuario,$this->id_caja_op,$pagos);
		if(!$ex){
			$this->fwxml->numError=$this->mventas->numError();
			$this->fwxml->descError=$this->mventas->descError();			
		}else{
			$pagos_inserted=json_encode($pagos);
			$exito=true;
		}
		$this->fwxml->arrResponse['pagos']=$pagos_inserted;		
	}else{//si hay comprobantes por imputar
		$this->fwxml->numError=$this->mservice->numError();
		$this->fwxml->descError="no hay comprobante ingresado";
	}
	return $exito;
	
}//imputar_pagos



	function generarComprobante($id_comp,$afip=false,$representante=false)
	{
		$filecomp='';

		$this->load->library('documento');
		$strpagos=$this->mpagos->getstrpagos($id_comp);
		$xmldata=$this->mservice->getxmldata("vercomprobantes_det_rpt",array("*,'$strpagos' as strpagos"),"id_comp=$id_comp","");
		$pathimgqr="";
		if($this->mservice->numError()==0)
		{ //solo cuando el comprobante se genera para AFIP y el mismo es un representado, se usa la plantilla para representante			
			if($afip && $representante){
			$this->documento->parseXsl('tpl/comprobantes_representante.xsl',$xmldata);
			}else{
			$this->documento->parseXsl('tpl/comprobantes.xsl',$xmldata);		
			}
		if($this->documento->numError()==0){
			$file=sprintf("%'.08d",$id_comp)."_".strtolower(randString(5)).".html";
			$filecomp=$this->documento->save('files/'.$file);	
			if($this->documento->numError()==0){
				$cols=array("path_comp" => $filecomp);
				$cond=array("id_comp"=> $id_comp);
				$this->mservice->update("comp",$cols,$cond);
					if($this->mservice->numError()!=0)
			 		{	
			 			$this->fwxml->numError=$this->mservice->numError();
				  		$this->fwxml->descError="Error actualizar comprobante:".$this->mservice->descError();
					}
				}else
				{	    
				$filecomp="";
				}
		}
		
	}

return $filecomp;

}//generar comprobante

	function enviarAfip($id_comp)
	{	
		$this->numError=0;
		$this->descError="";
		$cmp=array();
		$exito=false;
		 if($id_comp==0 || $id_comp==null)
		 {
		 	$this->numError=3;
			$this->descError="sin id_comp";
				return false;

		 }		 
		$rs=$this->mservice->get("vercomprobantes_afip",array("id_comp","id_talonario","nro_comp","tipo_comp","nro_talonario","id_cliente","strnombrecompleto","domicilio","telefono","nro_docu","tipo_docu","tipo_persona_desc","descripcion_loc","descripcion_pro","importe_total","importe_neto","importe_base","importe_iva","importe_exento","importe_descuento","cuit","id_cond_iva","condicion","id_tipo_comp","tipo_comp","tipo_comp_afip","now() as fecha_comp","ADDDATE(NOW(),1) as fecha_serv"),"id_comp=$id_comp and (cae='' or cae is null)","");

		if(count($rs)!=0)
		{	$comp=$rs[0];
			$id_talonario=$comp['id_talonario'];
			$tipo_comp=$comp['tipo_comp'];
			$nro_talonario=$comp['nro_talonario'];

			$rst=$this->mservice->get("vertalonarios", array("*"),"id_talonario=$id_talonario and habilitado=1 and cer_habilitado=1 and fe_vencimiento>=now()","");
			if(!count($rst)>0)
			{	$this->numError=1;
				$this->descError="certificado o talonario no habilitado o disponible. Verifique su disponibilidad para el nro. $nro_talonario  de $tipo_comp (ID $id_talonario) o que el mismo no esté vencido";
				return false;
			}
			$dettalonario=$rst[0];
			$id_certificado=$dettalonario['id_certificado'];			
			$params=array('pathcarpeta'=>realpath(".")."\\afip\\");
			$this->load->library('wsafip',$params);
			$this->wsafip->init($id_certificado,$this->mservice);


			$cmp=$this->wsafip->prepare();
			$taOk=$this->wsafip->verificaTA();
			
			if(!$taOk)
			{
				if(!$this->wsafip->obtener_TA())
				{
				$this->numError=$this->wsafip->numError;
				$this->descError=$this->wsafip->descError;
				return false;
				}
			
				$taOk=$this->wsafip->verificaTA();
				if(!$taOk)
				{
				$this->numError=$this->wsafip->numError;
				$this->descError=$this->wsafip->descError;
				return false;	
				}
			}
			
			//lo cargo, siempre y cuando haya pasado las validaciones anteriores
			if(!$this->wsafip->cargar_TA())
			{
				$this->numError=$this->wsafip->numError;
				$this->descError=$this->wsafip->descError;
				return false;
			}
			
			//verifico que tipos de produtos tiene
			$det=$this->mservice->query("select id_tipo from comp_det where id_comp=$id_comp group by id_tipo");
			$producto=false;
			$servicio=false;
			foreach ($det as $r) {
				if($r['id_tipo']==1)
				{
					$producto=true;
				}
				if($r['id_tipo']==2)
				{
					$servicio=true;
				}
			}
			
			if($producto && $servicio)
			{
			$cmp['Concepto']=3;	
			}
			if($producto && !$servicio)
			{
			$cmp['Concepto']=1;	
			}
			if(!$producto && $servicio)
			{
			$cmp['Concepto']=2;	
			}
			$cmp['DocNro']=$comp['cuit'];
			//si es factura C o recibo C
		  if($comp['tipo_comp_afip']==11 || $comp['tipo_comp_afip']==13 || $comp['tipo_comp_afip']==15)
		  {
		    if($comp['importe_total']<5000){
		      $cmp['DocTipo']=99;
		      $cmp['DocNro']=0;
		    }
		    $cmp['ImpOpEx']=0;
		    $cmp['ImpTotConc']=0;
		    $cmp['ImpIVA']=0;
		  }

		  
		  
		  //si es consumidor final
		  if($comp['id_cliente']==3){
		      $cmp['DocTipo']=99;
		      $cmp['DocNro']=0;
		    }else{
		    	$cmp['DocTipo']=80;
		    	$cmp['DocNro']=$comp['cuit'];
		    }
		    
		  $fecha = new DateTime($comp['fecha_comp']);
			$fecha_comp=  $fecha->format('Ymd');
			$fecha = new DateTime($comp['fecha_serv']);
			$fecha_serv=  $fecha->format('Ymd');

			//si es nota de credito, indico a que periodo corresponde la nota de credito
		  if($comp['tipo_comp_afip']==3 || $comp['tipo_comp_afip']==8 || $comp['tipo_comp_afip']==13)
		  {
		  	$cmp['PeriodoAsoc']=array('FchDesde' => $fecha_comp,'FchHasta' => $fecha_comp);
		  }

			$cbte=$this->wsafip->RecuperaLastCMP($comp['nro_talonario'], $comp['tipo_comp_afip']);
			if($this->wsafip->numError!=0)
			{
			$this->numError=$this->wsafip->numError;
			$this->descError=$this->wsafip->descError;		
			return false;
			}
			$cmp['CbteDesde']=(int)$cbte + 1;
			$cmp['CbteHasta']=(int)$cbte + 1;
			$cmp['CbteFch']=$fecha_comp;
			$cmp['ImpTotal']=$comp['importe_total'];
//
			$detiva=$this->mservice->query("select iva,sum(importe_item_iva) as total_iva,sum(importe_item_neto) as total_neto from vercomprobantes_det where iva>0 and id_comp=$id_comp group by iva");
			if(count($detiva)>0 && $comp['tipo_comp_afip']!=11)
			{
				$cmp['ImpNeto']=$comp['importe_neto'];	
				$arrAli=array();
				foreach ($detiva as $reg){					
						if($reg['iva']==0.105)
						{
							 	$arr=array();
						    $arr['Id']=4; //->10.5%
						    $arr['BaseImp']=$reg['total_neto'];
						    $arr['Importe']=round($reg['total_iva'],2);
						    $arrAli['AlicIva'][]=$arr;  
						}
						if($reg['iva']==0.210)
						{
							 	$arr=array();
						    $arr['Id']=5; //->21.0%
						    $arr['BaseImp']=$reg['total_neto'];
						    $arr['Importe']=round($reg['total_iva'],2);
						    $arrAli['AlicIva'][]=$arr;  
						}
				}
				 $cmp['Iva']=$arrAli;
				
			}else			
			{ //para facturas C o notas de creditos C	
				if($comp['tipo_comp_afip']==11 || $comp['tipo_comp_afip']==13){
					$cmp['ImpNeto']=$comp['importe_neto'];
					}else{
					$cmp['ImpTotConc']=$comp['importe_neto'];						
					}	
			}
				
			
			
			//$cmp['ImpNeto']=$comp['importe_neto'];
			$cmp['ImpIVA']=($comp['tipo_comp_afip']==11)?0:$comp['importe_iva'];

			if($cmp['Concepto']==2 || $cmp['Concepto']==3)
			{
			$cmp['FchServDesde']=$fecha_comp;   //Fecha de inicio  del abono para el  servicio a facturar. Dato  obligatorio  para concepto  2 o 3 (Servicios / Productos y Servicios). Formato  yyyymmdd
			$cmp['FchServHasta']=$fecha_comp;
			$cmp['FchVtoPago']=$fecha_serv; //Fecha de vencimiento  del  pago servicio a facturar. Dato  obligatorio  para concepto  2 o 3 (Servicios / Productos y Servicios). Formato  yyyymmdd. Debe ser igual o posterior a la fecha delcomprobante	
			}

			
			if($this->wsafip->enviar($cmp,$comp['tipo_comp_afip'],$comp['nro_talonario']))
			{ $this->lastrequest=$this->wsafip->getLastRequestXml();
				$this->lastresponse=$this->wsafip->getLastResponseXml();
				$res=$this->wsafip->reg_respuesta[0];
				$fe_vencimiento=strtodate($res['CAEFchVto'],'yyyymmdd','yyyy-mm-dd');
				$fe_proceso=strtodate($res['FchProceso'],'yyyymmddhhmmss','yyyy-mm-dd hh:mm:ss');
				$fe_comp=strtodate($res['CbteFch'],'yyyymmdd','yyyy-mm-dd');
				$cae=$res['CAE'];
				$nro_comp=$res['CbteDesde'];

				$cmpqr=array('fecha' =>$fe_comp
					,'cuitemisor'=>$this->wsafip->cuit
					,'ptovta'=>$comp['nro_talonario']
					,'tipocmp'=>$comp['tipo_comp_afip']
					,'nrocmp'=>$nro_comp
					,'importe_total'=>$comp['importe_total']
					,'tipodocrec'=>$cmp['DocTipo']
					,'nrodocrec'=>$cmp['DocNro']
					,'codaut'=>$cae);

				$pathfileqr=$this->wsafip->getQr($cmpqr,$this->qrlib);
				$campos=array("cae"=>$cae,"fe_vencimiento"=>$fe_vencimiento,"estado"=>"F","fe_estado"=>date('Y-m-d H:i:s'),"fe_procesado"=>$fe_proceso,"nro_comp"=>$nro_comp,"fe_comp"=>$fe_comp,'pathqr'=>$pathfileqr);

				$this->mservice->actualizar_registro("comp",$campos,array("id_comp"=>$id_comp));
				$exito=true;
				
			}else			
			{//siempre que falle, se pasa a emitido
			$this->lastrequest=$this->wsafip->getLastRequestXml();
			$this->lastresponse=$this->wsafip->getLastResponseXml();
			$campos['estado']="E";
			$this->mservice->actualizar_registro("comp",$campos,array('id_comp' =>$id_comp));
			$this->numError=$this->wsafip->numError;
			$this->descError=$this->wsafip->descError;		
			}

		}//if si hay para enviar
		else
		{
		$this->numError=1;
		$this->descError="No hay comprobante por enviar a autorizar";
		}
		return $exito;
	}

function changepagos(){
date_default_timezone_set('America/Argentina/Buenos_Aires');		
$this->fwxml->numError=0;
$this->fwxml->descError="";
$this->fwxml->arrResponse=array();
if(!$this->loginOn() || !$this->visitante->permitir('prt_comprobantes',16))
{
	$this->load->view('vlogin');
	return; 
}

try {
$this->id_sucursal=$this->visitante->get_id_sucursal();
$this->id_usuario=$this->visitante->get_id();
  	$pagos=null;
  	$id_comp=0;
  	$id_comp=$this->input->post("id_comp");
 		$pagos=json_decode($this->input->post("pagos"), true);

		$this->mservice->begin_trans();
			if(!$this->imputar_pagos_comp($pagos,$id_comp))
	 		{
	 				$this->mservice->rollback_trans();
	 		}else
	 		{
	 			$this->mservice->commit_trans();	 			
					if($this->mservice->numError()!=0)
			 		{
			 			$this->fwxml->numError=$this->mservice->numError();
				  		$this->fwxml->descError="Error commit_trans:".$this->mservice->descError();
				  		$this->mservice->rollback_trans();
			 		}
	 		}	
} catch (Exception $e) {
	$this->fwxml->numError=-6;
	$this->fwxml->descError=$e->getMessage();
	$this->mservice->rollback_trans();
}

$this->fwxml->ResponseJson();		
}//changepagos

function regenerarComprobante($id_comp){
	
	$exito=false;

	date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$filecomp="";
	if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',2))
	{
			$this->fwxml->numError=1;
			$this->fwxml->descError="No tiene permiso para realizar esta acción";
			goto salir2;		
	}

	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();
	
	
	try {
			$rs=$this->mservice->get("vercomprobantes",array('id_comp','afip','id_cliente_representante'),"id_comp=$id_comp","");
			if($this->mservice->numError()!=0){
				$this->fwxml->numError=$this->mservice->numError();
				$this->fwxml->descError=$this->mservice->descError();
				goto salir2;	
			}

			if(count($rs)>0){
				$comp=$rs[0];
				$afip=($comp['afip']==1)?true:false;
				$representa=($comp['id_cliente_representante']>0)?true:false;
				$filecomp=$this->generarComprobante($id_comp,$afip,$representa);
				if($filecomp==""){
					$this->fwxml->numError=1;
					$this->fwxml->descError="El archivo no se generó. Ver log";		
				}
				$exito=true;
			}else{
				$this->fwxml->numError=1;
				$this->fwxml->descError="El comprobante $id_comp no existe";	
			}
		
	} catch (Exception $e) {
		$this->fwxml->numError=-1;
		$this->fwxml->descError=$e->getMessage();		
	}
	salir2:
	if($exito){
		$base_url=base_url();
		$file=$base_url."/".$filecomp;
		$this->fwxml->descError="Comprobante generado correctamente: <a href='$file'>$file</a>";	
	}
	$this->fwxml->ResponseJson();	
}
	

	function listener(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',1))
		{
			$this->load->view('vlogin');
			return; 
		}
	    $json=json_decode($this->input->post("data"),true);    
	    $definicion=$this->input->post("definicion");
	    $where=array();
	    if($definicion=="selcliente_presupuesto"){
	    	$where['pedido']=" and id_pedido=".$json['id_pedido'];
	    }
	    if($definicion=="selcliente"){
	    $where['cliente']=" and id_cliente=".$json['id_cliente'];	
	    }
	    if($definicion=="pedido_detalle"){
	    	$where['pedido']=" and id_pedido=".$json['id_pedido'];	
	    }
	    if($definicion=="productos"){
	    	$where['idproducto']=" and id_producto='".$json['id_producto']."'";	
	    }
	    $this->listener->get_transform($definicion,$where);
  }//listener

}//class

