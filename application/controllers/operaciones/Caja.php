<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Caja extends CI_Controller {
private $visitante;
private $definiciones=array(); //para uso del oyente
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mconfiguracion");
		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");		
		$this->load->model("mservice");
		$this->load->model("seguridad/mcaja");
		$this->load->library("listener");
		$this->definiciones['comprobantesAdeudados']="select id_comp,id_talonario,IFNULL(nro_comp,0) as nro_comp,IFNULL(nro_talonario,0) as nro_talonario,cae,id_cliente,strnombrecompleto,domicilio,telefono,nro_docu,tipo_docu,documento,tipo_persona_desc,descripcion_loc,descripcion_pro,importe_total,importe_neto,importe_base,importe_iva,importe_exento,importe_descuento,DATE_FORMAT(fe_creacion,'%d/%m/%Y %r') as fe_creacion,DATE_FORMAT(fe_comp,'%d/%m/%Y %r') as fe_comp,DATE_FORMAT(fe_vencimiento,'%d/%m/%Y %r') as fe_vencimiento,cuit,estado,descripcion,estadoclass,usuario_estado,usuario,afip,id_sucursal,sucursal,id_cond_iva,condicion,id_tipo_comp,tipo_comp,tipo_comp_afip,importe_pago,path_comp,id_comp_anula,deuda from vercomprobantes where id_comp in(SELECT distinct id_comp FROM vercomp_pagados WHERE debe>0) and estado in('E','F') and movimiento='I' order by fe_creacion asc";
		$this->visitante=unserialize($this->session->visitante);
	}
	function listener(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_caja',1))
		{
			$this->load->view('vlogin');
			return; 
		}
    $json=json_decode($this->input->post("data"),true);    
    $definicion=$this->input->post("definicion");
    $this->listener->get($this->definiciones[$definicion],$json); 
  }

	 function index()
	{	

		if(!$this->loginOn() || !$this->visitante->permitir('prt_caja',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		
		$arrVariales=array();
		
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;		
		$arrVariales['id_sucursal']=$this->visitante->get_id_sucursal();		
		$arrVariales['id_caja']=$this->visitante->get_id_caja();		
		//si tiene mercado pago, es porque esta habilitado el servicio
		$token_access_mp=$this->mconfiguracion->getvalor("mp_token_access",$this->visitante->get_id_empresa());
		$arrVariales['mercadopagoactivo']=(trim($token_access_mp)=="")?0:1;
		$saldo_caja=$this->mcaja->get_saldo($this->visitante->get_id_caja_opera());
		$arrVariales['saldo_caja']=$saldo_caja;		
		$arrVariales['deben']=$this->mcaja->get_comp_deben($this->visitante->get_id_empresa());
		$arrVariales['recaudacion']=$this->mcaja->get_recaudacion($this->visitante->get_id_caja_opera());
		$this->load->view('operaciones/vcaja',$arrVariales);
		
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

function agregar_movimiento(){
	date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	if(!$this->loginOn() || !$this->visitante->permitir('prt_caja',2))
	{
	$this->fwxml->numError=1;
	$this->fwxml->descError="No tiene permiso para realizar esta accion";
	goto salir;
	}

	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();
	$id_caja_opera=$this->visitante->get_id_caja_opera();
  $path='';
	$arrRow=array();
	$campos=json_decode(file_get_contents('php://input'), true);					
	
	try {
		$id_operacion_tipo=(int)$campos["id_operacion_tipo"];
		$monto=(float)$campos["monto"];		
		$detalle=trim($campos["detalle"]);
		$this->mservice->begin_trans();
		if($this->mservice->numError()!=0)
	 		{
	 			$this->fwxml->numError=$this->mservice->numError();
		  	$this->fwxml->descError="Error al inicializar transaccion:".$this->mservice->descError();
		  	 goto salir;
	 		}
	 		if($detalle==""){
	 		$rs=$this->mservice->getwhere("operaciones_tipo","id_operacion_tipo=$id_operacion_tipo",$orderby='');
	 		$detalle=$rs[0]['operacion_tipo'];
	 		}

	 		$ret=array('id_operacion_tipo' =>$id_operacion_tipo ,'detalle'=>$detalle,'id_usuario'=>$this->id_usuario,'id_caja_op'=>$id_caja_opera,'fecha'=>date('Y-m-d H:i:s'),'id_operacion_tipo'=>$id_operacion_tipo,'detalle'=>$detalle,'monto'=>$monto);
	 			$id_remito=$this->mservice->insertar('operaciones',$ret);
	 			if($this->mservice->numError()!=0)
		 		{
		 			$this->fwxml->numError=$this->mservice->numError();
			  	$this->fwxml->descError="Error al crear operacion:".$this->mservice->descError();
			  	 goto salir;
		 		}
	 		
				$this->mservice->commit_trans();	 			
				if($this->mservice->numError()!=0)
		 		{
		 			$this->fwxml->numError=$this->mservice->numError();
			  	$this->fwxml->descError="Error commit_trans:".$this->mservice->descError();
			  	$this->mservice->rollback_trans();
		 		}
	}catch (Exception $e) {
		$this->fwxml->numError=100;
    $this->fwxml->descError=$e->getMessage();
    $this->mservice->rollback_trans();
  }//catch
	salir:
		if($this->fwxml->numError!=0)
		{
			$this->mservice->rollback_trans();				
		}
		$saldo_caja=$this->mcaja->get_saldo($this->visitante->get_id_caja_opera());
		$recaudacion=$this->mcaja->get_recaudacion($this->visitante->get_id_caja_opera());
		$this->fwxml->arrResponse['saldo_caja']=$saldo_caja;
		$this->fwxml->arrResponse['recaudacion']=$recaudacion;	
		$this->fwxml->ResponseJson();
}//agregar_movimiento


function exportar(){
		date_default_timezone_set('America/Argentina/Buenos_Aires');		
		$this->fwxml->numError=0;
		$this->fwxml->descError="";
		$this->fwxml->arrResponse=array();
		if(!$this->loginOn() || !$this->visitante->permitir('prt_caja',1))
		{
		$this->fwxml->numError=1;
		$this->fwxml->descError="No tiene permiso para realizar esta accion";
		goto salir;
		}
  	$filereport="";
  	$fecha_desde=trim($this->input->post("fecha_desde"));
  	$fecha_hasta=trim($this->input->post("fecha_hasta"));
  	$id_empresa=$this->visitante->get_id_empresa();
  	$emp=$this->mservice->getwhere("empresa","id_empresa=$id_empresa")[0];
  	
  	$cond="id_caja=".$this->visitante->get_id_caja();
  	
  		if($fecha_desde!=""){  			
  			$fecha_desde=todate($fecha_desde,103);
  			$cond.=" and DATE(fe_inicio)>='$fecha_desde' ";
  		}
  		if($fecha_hasta!=""){
  			$fecha_hasta=todate($fecha_hasta,103);
  			$cond.=" and DATE(fe_inicio)<ADDDATE('".$fecha_hasta."',1) ";
  		}
  		
  	
  	try {
  		$this->load->library('documento');
  		$cmp=array("id_caja","id as id_caja_op","detalle","CASE WHEN movimiento='I' THEN monto ELSE monto*-1 END as monto","CASE WHEN movimiento='I' THEN 'ingreso' ELSE 'egreso' END AS movimiento","fecha","usuario","origen","fe_inicio");

  		//SELECT id,id_caja,detalle,monto,CASE WHEN movimiento='I' THEN 'ingreso' ELSE 'egreso' END AS movimiento,fecha, usuario, origen,fe_inicio FROM vercaja_movimientos WHERE id_caja=1 AND DATE(fe_inicio)>='' AND DATE(fe_inicio)<'' ORDER BY fecha
  		$customfielddatatype=array('monto' =>'money',"fecha"=>"datetime","fe_inicio"=>"datetime");
			$xmldata=$this->mservice->getxmldata("vercaja_movimientos",$cmp,$cond," fecha asc",$customfielddatatype);
			
				if($this->mservice->numError()==0)
				{ 
				$this->documento->parseXsl('tpl/Excel_tpl.xsl',$xmldata);
				$strdate=date("Ymd");
				$file="informe_cajas_".str_replace(" ","_", $emp["empresa"])."_".$strdate.".xls";

				$filereport=$this->documento->save('files/reportes/'.$file);	
					if($this->documento->numError()==0){			
					$this->fwxml->descError=	$filereport;				
					}else
					{
					$this->fwxml->numError=	$this->documento->numError();
					$this->fwxml->descError=	$this->documento->descError();
					}
				}else
				{				
							$this->fwxml->numError=	$this->mservice->numError();
							$this->fwxml->descError=	$this->mservice->descError();
				}
  		
  	} catch (Exception $e) {
  		$this->fwxml->numError=100;
    	$this->fwxml->descError=$e->getMessage();      		
  	}
  	salir:
		$this->fwxml->ResponseJson();		
	}//exportar

	

}//class
