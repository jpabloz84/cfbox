<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pagos extends CI_Controller {
private $visitante;
private $id_usuario;
private $id_sucursal;
private $id_caja_op;
private $log_activo=false;
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");
		$this->load->model("entidades/mclientes");		
		$this->load->model("mservice");
		$this->load->model("mpagos");
		$this->load->model('seguridad/mconfiguracion');
		$this->load->model('operaciones/meventos');
		$this->load->model("seguridad/mtoken");
		$this->visitante=unserialize($this->session->visitante);
		$log_activo=false;
		if($this->visitante!=false){
		$log_activo=(int)$this->mconfiguracion->getvalor("log_activo",$this->visitante->get_id_empresa());	
		}
		
		$this->log_activo=($log_activo==1)?true:false;
	}
	 function index()
	{	

		if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',1))
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
		//$this->visitante=unserialize($this->session->visitante);
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

function generar(){
	date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$this->id_sucursal=$this->visitante->get_id_sucursal();
	$this->id_usuario=$this->visitante->get_id();  
	$importe_pago=0;
	$importe_total=0;
	$deuda=0;
	$saldoafavor=0;
	$pago=array();	
	$this->id_caja_op=$this->visitante->get_id_caja_opera();
	$pagos_inserted=array();
 	if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',2))
	{
		$this->load->view('vlogin');
		return; 
	}
	$campos=json_decode(file_get_contents('php://input'), true);					
	
	try {
		$this->mservice->begin_trans();
		if($this->mservice->numError()!=0)
	 		{
	 			$this->fwxml->numError=$this->mservice->numError();
		  	$this->fwxml->descError="Error begin_trans:".$this->mservice->descError();
		  	 goto salir;
	 		}
				$monto_abona=(float)$campos['monto_abona'];				
				$id_cliente=$campos['id_cliente'];				
				$pago['pago']=$monto_abona;
				$pago['id_tipo_pago']=$campos['id_tipo_pago'];
				$pago['observacion']=$campos['observacion'];
				$pago['fe_pago']=date('Y-m-d H:i:s');
				$pago['id_cliente']=$id_cliente;
				$arrPagosPendientes=array(3,4,5,8);
				$pago['estado']=$this->mpagos->getestado_inicial($campos['id_tipo_pago']);				
				$pago['fe_estado']=date('Y-m-d H:i:s');
				$pago['id_usuario']=$this->id_usuario;
				$pago['id_usuario_estado']=$this->id_usuario;
				$pago['id_caja_op']=$this->id_caja_op;
				$pago['id_sucursal']=$this->visitante->get_id_sucursal();
				$id_pago=$this->mservice->insertar('pagos',$pago);	
				$id_empresa=$this->visitante->get_id_empresa();
				$this->mtoken->rollbackonerror=false;
            	$parametrostoken=array('id_empresa' =>$id_empresa ,'id_pago'=>$id_pago);
            	$token=$this->mtoken->generar_token(1,$this->id_usuario,$parametrostoken);	
            	$this->numError=$this->mtoken->numError();
            	$this->descError=$this->mtoken->descError();
            	$this->db->update("pagos",array('token'=>$token),array('id_pago'=>$id_pago));						
				$pagos_inserted[]=$id_pago;
		 		if($this->mservice->numError()!=0 )
		 		{
		 		$this->fwxml->numError=$this->mservice->numError();
			  	$this->fwxml->descError="Error insertar pago:".$this->mservice->descError();
			  	goto salir;
		 		}
				 //si el tipo de pago tiene parametros, lo inserto
				 $artpparams=$this->mservice->getwhere("tipo_pagos_parametros","id_tipo_pago=".$campos['id_tipo_pago'],"");
				if(count($artpparams)>0)
				{	$parametros=array();
					foreach ($campos['tipospagos'] as $opcion) {
					if($opcion['id_tipo_pago']==$campos['id_tipo_pago'])
						{
						$params=$opcion['parametros'];
						break;
						}
					}
							
					foreach ($params as $paramitem) {
						$parametros['id_pago']=$id_pago;
						$parametros['nro_parametro']=$paramitem['nro_parametro'];
						$parametros['valor']=$paramitem['valor'];
						$this->mservice->insertar('pagos_parametros',$parametros);
						if($this->mservice->numError()!=0 )
				 		{
				 			$this->fwxml->numError=$this->mservice->numError();
					  	$this->fwxml->descError="Error insertar parametros del pago:".$this->mservice->descError();
					  	goto salir;
				 		}
					}							
				}//if insercion de parametros

				$this->mservice->commit_trans();		
				if($this->mservice->numError()!=0)
		 		{
		 			$this->fwxml->numError=$this->mservice->numError();
			  	$this->fwxml->descError="Error al inicializar transaccion:".$this->mservice->descError();
			  	 goto salir;
		 		}		
		}catch (Exception $e){
		$this->fwxml->numError=100;
    $this->fwxml->descError=$e->getMessage();    
  }//catch
	salir:
		if($this->fwxml->numError!=0)
		{
			$this->mservice->rollback_trans();				
		}
		$res=$this->mservice->getwhere('versaldo_afavor','id_cliente='.$id_cliente,'');	
		if(count($res)>0){
		$saldoafavor=$res[0]['saldo_afavor'];
		}
		$res=$this->mclientes->obtenertotales_facturados($id_cliente);
		if(count($res)>0){
			$importe_pago=(float)$res[0]['importe_pago'];
			$importe_total=(float)$res[0]['importe_total'];
			$deuda=$importe_total-$importe_pago;
		}
		$this->fwxml->arrResponse['importe_pago']=$importe_pago;
		$this->fwxml->arrResponse['importe_total']=$importe_total;
		$this->fwxml->arrResponse['deuda']=$deuda;
		$this->fwxml->arrResponse['id_pago']=$pagos_inserted;
		$this->fwxml->arrResponse['saldoafavor']=$saldoafavor;
	$this->fwxml->ResponseJson();
}//generar





function imputar(){
	date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$id_sucursal=$this->visitante->get_id_sucursal();
	$id_usuario=$this->visitante->get_id();
	$id_proceso=0;
	$saldoafavor=0;
	$importe_total=0;
	$importe_pago=0;
	$deuda=0;
	if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',4))
	{
		$this->load->view('vlogin');
		return; 
	}

	$id_cliente=(int)$this->input->post("id_cliente");
	//mientras no sea el cliente consumido final
	$cli=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente.' and ifnull(cf,0)<>1 and id_empresa='.$this->visitante->get_id_empresa(),'');	
	if($id_cliente>0 && count($cli)>0){
		if(!$this->mpagos->imputar($id_cliente,$id_usuario,$id_proceso))
			{			
			$this->fwxml->numError=$this->mpagos->numError();
			$this->fwxml->descError=$this->mpagos->descError();
			}
			
			$res=$this->mservice->getwhere('versaldo_afavor','id_cliente='.$id_cliente,'');	
			
			if(count($res)>0){
				$saldoafavor=$res[0]['saldo_afavor'];
			}
			$res=$this->mclientes->obtenertotales_facturados($id_cliente);
			
			if(count($res)>0){
				$importe_pago=(float)$res[0]['importe_pago'];
				$importe_total=(float)$res[0]['importe_total'];
				$deuda=$importe_total-$importe_pago;
			}
			$this->fwxml->arrResponse['importe_pago']=$importe_pago;
			$this->fwxml->arrResponse['importe_total']=$importe_total;
			$this->fwxml->arrResponse['deuda']=$deuda;
			$this->fwxml->arrResponse['saldoafavor']=$saldoafavor;
	}else{
		$this->fwxml->numError=15;
			$this->fwxml->descError="Cliente no valido para imputar pagos";
	}
	$this->fwxml->arrResponse['id_proceso']=$id_proceso;		
	$this->fwxml->ResponseJson();
}//imputar


function anularpago(){
	
	date_default_timezone_set('America/Argentina/Buenos_Aires');		
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->fwxml->arrResponse=array();
	$id_sucursal=$this->visitante->get_id_sucursal();
	$id_usuario=$this->visitante->get_id();
	$id_pago=0;
	//si tiene permisos para anular
	if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',8))
	{
		$this->load->view('vlogin');
		return; 
	}

	$id_pago=(int)$this->input->post("id_pago");
	if($id_pago>0){
		
		if(!$this->mpagos->anular($id_pago,$id_usuario,true))
			{			
			$this->fwxml->numError=$this->mpagos->numError();
			$this->fwxml->descError=$this->mpagos->descError();
			}
		}else{
		$this->fwxml->numError=15;
		$this->fwxml->descError="pago invalido";
		}
	
	$this->fwxml->ResponseJson();
}//anular

	function listener()
	{
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$this->fwxml->arrResponse=array();
		 $arrRow=array();

	 	if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',4))
		{
			$this->load->view('vlogin');
			return; 
		}
		echo $metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE

		$cmp=json_decode(file_get_contents('php://input'), true);						
		$id_pago=$cmp["id_pago"];
		$observacion=$cmp["observacion"];
		
		if($metodo=="DELETE" && (int)$id_pago > 0)
		{	

			$this->anular($id_pago);
			return;
		}
		//actualizacoines
		if($metodo=="PUT" && (int)$id_pago > 0)
		{
			$campos=array('observacion'=>$observacion);
			$condicional=array('id_pago' => $id_pago);
			$this->mservice->actualizar_registro('pagos',$campos,$condicional);
			
		}

		
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
    $this->fwxml->descError=$this->mservice->descError();
    }
    
    $this->fwxml->ResponseJson();		

	}
	function exportar(){
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$filereport="";
  	$fecha_desde=trim($this->input->post("fecha_desde"));
  	$fecha_hasta=trim($this->input->post("fecha_hasta"));
  	
  	$id_cliente=$this->input->post("id_cliente");
  	$id_pago=$this->input->post("id_pago");
  	$anulados=$this->input->post("anulados");
  	$cond="id_cliente=$id_cliente ";
  	if($id_pago!=""){
  		$cond.=" and id_pago=$id_pago ";
  	}else{
  		if($fecha_desde!=""){
  			$cond.=" and fe_pago>='$fecha_desde' ";
  		}
  		if($fecha_hasta!=""){
  			$cond.=" and fe_pago<ADDDATE('".$fecha_hasta."',1) ";
  		}
  		//si es distinto de uno, agrego solamente los pagos que no estan anulados
  		if($anulados!=1){
  			$cond.=" and estado<>'A' ";
  		}
  		
  	}
  	//print_r($cond);
  	
  	try {
  		$this->load->library('documento');
  		$cmp=array("id_pago","pago","fe_pago","strnombrecompleto","observacion","tipo_pago","usuario","estado_pago_desc","usuario_estado","fe_estado","imputado","resta_imputar","id_proceso");
			$xmldata=$this->mservice->getxmldata("verpagos_imputar",$cmp,$cond," fe_pago desc");
			
				if($this->mservice->numError()==0)
				{ 
				$this->documento->parseXsl('tpl/Excel_tpl.xsl',$xmldata);
				$file="pagos_".randString(6).".xls";

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

		$this->fwxml->ResponseJson();		
	}//exportar

	function pdfpago($id_pago,$id_empresa,$attachar=false){
		$this->load->model('operaciones/mcomprobantes');
		$res=$this->mservice->getwhere('verpagos','id_pago='.$id_pago." and id_empresa=".$id_empresa." and estado='C'",'');	
		$id_sucursal=$res[0]['id_sucursal'];
		$id_usuario_estado=$res[0]['id_usuario_estado'];
		$rsu=$this->mservice->getwhere('usuario','id_usuario='.$id_usuario_estado,'');
		$rs1=$this->mservice->getwhere('versucursales','id_sucursal='.$id_sucursal,'');	
		$sucursal=$rs1[0];
		$deuda=0;
		$fileattach="";
		$empresastr="";
		$str_id_pago=sprintf("%'.08d\n", $id_pago);
		if(count($res)>0){
			$pago=$res[0];
			$pago['id_pago']=sprintf("%'.08d\n", $pago['id_pago']);
			$pago['fe_pago']= datetostr($pago['fe_pago'],'string');

			$id_cliente=$pago['id_cliente'];
			$pago['cuitempresa']=$sucursal['cuil'];
			$pago['empresa']=$sucursal['empresa'];
			$empresastr=str_replace(" ", "_", $pago['empresa']);
			$pago['localidad_sucursal']=$sucursal['localidad_sucursal'];
			$pago['provincia_sucursal']=$sucursal['provincia_sucursal'];
			$pago['sucursal']=$sucursal['sucursal'];

			$rescli=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente,'');
			if(count($rescli)>0){
				$pago['cuit']=$rescli[0]['cuit'];				

			}	
			$rsSaldo=$this->mservice->getwhere('versaldo_afavor','id_cliente='.$id_cliente,'');	
			$saldoafavor=0;
			if(count($rsSaldo)>0){
				$saldoafavor=(float)$rsSaldo[0]['saldo_afavor'];

			}
			$resfacturado=$this->mclientes->obtenertotales_facturados($id_cliente);
			if(count($resfacturado)>0){
				$importe_pago=(float)$resfacturado[0]['importe_pago'];
				$importe_total=(float)$resfacturado[0]['importe_total'];
				$deuda=$importe_total-$importe_pago;
				$deuda=round($deuda,2,PHP_ROUND_HALF_UP);
			}
			$pago['deuda']=$deuda-$saldoafavor;
			$detallecomp="";
			if($pago['id_comp_ref']>0){
				$detallecomp=$this->mcomprobantes->get_detalle_recibo($pago['id_comp_ref']);
			}
			
			if($rsu[0]['firma']!=""){			  
			  $pago['imgfirma']= $rsu[0]['firma'];
			}
			if($detallecomp==""){
			$this->load->view("operaciones/tplrecibo",$pago);	
			}else{
			$pago['detalle_det']=$detallecomp;
			$this->load->view("operaciones/tplrecibo_comp",$pago);	
			}
			
			
			$html=$this->output->get_output();		
			$this->load->library('pdf');
			$this->dompdf->loadHtml($html);
			$this->dompdf->setPaper('A4','portait');
			$this->dompdf->render();
			if($attachar){
				$path_pdf="files/tmp/recibo_".$empresastr."_".$str_id_pago.".pdf";      
      			$output = $this->dompdf->output();
      			file_put_contents($path_pdf, $output);
      			$fileattach=$path_pdf;
			}else{
				//lo muestra por pantalla
			$this->dompdf->stream('recibo_'.$id_pago.'.pdf',array('Attachment' => 0));	
			}
		 

		}else{
			echo "No existe el pago";
		}
		
		return $fileattach;

	}//pdfpagos


		//on submit del mercado pago cheout, verifica los pagos para el comprobante pasado por parametro
	function pagoverify(){
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$this->fwxml->arrResponse=array();
  	$prefijo_referencia="";
  	$infoArr=array('montoacreditado'=>0,'actualiza'=>0);
		if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',16))
		{
		$this->fwxml->numError=-10;
		$this->fwxml->descError="sin permisos para realizar esta acción";
		goto salir;
		}
		$suma=0;
		try {
			$id_pago=(int)$this->input->post("id_pago");
			if(!$id_pago>0){
			$this->fwxml->numError=-11;
			$this->fwxml->descError="identificativo de pago no ingresado";
			goto salir;
			}
			$rs=$this->mservice->query("SELECT * FROM verpagos WHERE id_pago=$id_pago");
			if($rs[0]['estado']=="P" || $rs[0]['estado']=="L"){ //si el estado del pago es pendiente o parcial
				$suma=(float)$rs[0]['pago'];	
				//hay pago de tipo mercado pago pendiente, verifico en mp si el mismo se incremento
				if($suma>0){
					$this->load->library('Mpwrapper');	
					$prefix_mpsistema=$this->mconfiguracion->getvalor("prefix_mpsistema");
					$prefix_mod=$this->mconfiguracion->getvalor("prefix_mpmodpago");
					$prefijo_referencia=$prefix_mpsistema.$prefix_mod.str_pad($this->visitante->get_id_empresa(),3,"0",STR_PAD_LEFT).$id_pago;
					$token_access=trim($this->mconfiguracion->getvalor("mp_token_access",$this->visitante->get_id_empresa()));
					$mp_modo_produccion=(int)$this->mconfiguracion->getvalor("mp_modo_produccion",$this->visitante->get_id_empresa());
					$sandbox_mode=($mp_modo_produccion==1)?false:true;
					$this->mp->init($token_access,$sandbox_mode);			
					$objpagos=null;
					
					$montoacreditado=(float)$this->mp->montoAcreditado($prefijo_referencia,$objpagos);
					$estadopagoMP=$this->mp->estadoPago($objpagos);
					if($this->log_activo){
						$this->meventos->registrar("Modulo PAGOS:pago $id_pago estadoMP -> $estadopagoMP",var_export($objpagos, true));
					}
					$infoArr['montoacreditado']=$montoacreditado;
					//si se ha pagado el total o parcial mente, o bien son montos mayores a cero pero distintos, hay que actualizar comp_pagos
					if(($estadopagoMP=="P"|| $estadopagoMP=="L" ||  $estadopagoMP=="C") && $montoacreditado>0){
						$infoArr['actualiza']=1; //actualizo cuando el monto que se acredito , supera el q hay q PAGAR para este pago
						$imputado=$this->mpagos->imputarPagoPendiente($id_pago,$montoacreditado,$this->visitante->get_id(),$this->visitante->get_id_caja(),$estadopagoMP);
						if(!$imputado){							
							$this->fwxml->numError=$this->mpagos->numError();
							$this->fwxml->descError=$this->mpagos->descError();
						}else{
							if($estadopagoMP=="C"){
								//$this->bingo->generar($id_pago); //genero los bingos en funcion de los comprobantes que pagué
							}
							$this->fwxml->descError="se han acreditado ".$montoacreditado." pesos";
						}
					}
				}else{					
					$this->fwxml->numError=15;
					$this->fwxml->descError="el pago a abonar no es mayor a cero";
				}
			}else{

				if($rs[0]['estado']=="C"){ //el pago ya fue acreditado por otro canal
					$this->fwxml->numError=0;
					$this->fwxml->descError="El pago ya fué acreditado";	
					$infoArr['montoacreditado']=$rs[0]['pago'];
				}else{
					$this->fwxml->numError=5;
					$this->fwxml->descError="el pago no disponible para ser abonado";	
				}
			}
			
			
		} catch (Exception $e) {
			$this->fwxml->numError=99;
			$this->fwxml->descError=$e->getMessage();
		}
		
		
		salir:
		$this->fwxml->arrResponse=$infoArr;
		$this->fwxml->ResponseJson();	

	}//pagosverify


public function pagarMP(){

date_default_timezone_set('America/Argentina/Buenos_Aires');		
$this->fwxml->numError=0;
$this->fwxml->descError="";
$this->fwxml->arrResponse=array();
$token_access='';
$prefijo_referencia="";
if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',16))
{
	$this->fwxml->numError=-10;
	$this->fwxml->descError="sin permisos para realizar esta acción";
	goto salir;
}
try {
		$id_cliente=0;
		$this->load->library('Mpwrapper');		
		$this->load->model('seguridad/mconfiguracion');
	

		$token_access=$this->mconfiguracion->getvalor("mp_token_access",$this->visitante->get_id_empresa());
		$sandbox_mode=($this->mconfiguracion->getvalor("mp_modo_produccion",$this->visitante->get_id_empresa())==1)?false:true;

	

		$this->mp->init($token_access,$sandbox_mode);
		$this->mp->aprobados=false; //que traiga todo (success,pendientes,failures)
		$id_pago=$this->input->post("id_pago");
		$rs=$this->mservice->getwhere("pagos","id_pago=".$id_pago,"");					
		$articulos=array();
		$id_comp_ref=$rs[0]['id_comp_ref'];
		$desc_comprobante="";
		if(isset($id_comp_ref)){
			if($id_comp_ref>0){
				$rc=$this->mservice->get("vercomprobantes",array("CASE WHEN afip=1 THEN CONCAT(tipo_comp,' ptovta ',CAST(nro_talonario AS CHAR),' N° comp ',CAST(nro_comp AS CHAR)) ELSE CONCAT('Comprobante N° ',CAST(id_comp AS CHAR)) END AS comp_desc"),"id_comp=".$id_comp_ref,"");
				$comp=$rc[0];
				if($comp['comp_desc']!=""){
					$desc_comprobante=$comp['comp_desc'].". ";
				}

			}
		}


		foreach ($rs as  $r) {
			$id_cliente=$r['id_cliente'];
			$articulo=array('id' =>$r['id_pago'] ,'titulo'=>$desc_comprobante.'Ref. pago N° '.$r['id_pago'],'cantidad'=>1,'divisa'=>'ARS','precio_unitario'=>$r['pago']);
			array_push($articulos, $articulo);
		}

 		$rs=$this->mservice->getwhere("verclientes","id_cliente=".$id_cliente,"");						
		$cli=$rs[0];
		
		$pagante=array('nombres' =>$cli['nombres'] ,'apellido'=>$cli['apellido'],'tipo_docu'=>$cli['documento'],'nro_docu'=>$cli['nro_docu']);
		if($cli['email']!=""){
			$pagante['email']=$cli['email'];
		}
		if($cli['car_tel']!=""){
			$pagante['tel_caracteristica']=$cli['car_tel'];
		}
		if($cli['nro_tel']!=""){
			$pagante['tel_numero']=$cli['nro_tel'];
		}
	  if($cli['calle']!=""){
    $pagante['calle']=$cli['calle']; 
    }
    if($cli['nro']!=""){
    $pagante['calle_nro']=$cli['nro']; 
    }
    if($cli['cp']!=""){
    $pagante['cp']=$cli['cp']; 
    }
		$this->mp->successurl=base_url()."index.php/webservice/cronmp";
		$this->mp->pendingurl=base_url()."index.php/webservice/pending";
		$this->mp->failureurl=base_url()."index.php/webservice/failure";
		$this->mp->notificacionesurl=base_url()."index.php/webservice/notificaciones";
		$prefix_mpsistema=$this->mconfiguracion->getvalor("prefix_mpsistema");
		$prefix_mod=$this->mconfiguracion->getvalor("prefix_mpmodpago");
		$prefijo_referencia=$prefix_mpsistema.$prefix_mod.str_pad($this->visitante->get_id_empresa(),3,"0",STR_PAD_LEFT);
		$this->mp->prefijo_refexterna=$prefijo_referencia;
		$preference_id=$this->mp->getinitpoint($id_pago,$articulos,$pagante);
		if($this->mp->numError()==0){			 
       $data['data-public-key']=$token_access;
       $data['data-preference-id']=$preference_id;
       $this->fwxml->arrResponse=$data;
		}else{
			$this->fwxml->descError=$this->mp->descError();
			$Error=$this->mp->Error();
			$this->fwxml->arrResponse=$Error;
		}

	} catch (Exception $e) {
	$this->fwxml->numError=-6;
	$this->fwxml->descError=$e->getMessage();
	
	}
salir:
$this->fwxml->ResponseJson();	
}


function send_mail(){
   $this->fwxml->numError=0;
   $this->fwxml->descError="";
   $this->fwxml->arrResponse=array();
   if(!$this->loginOn()){
    echo  "no cuenta con autorizacion para realizar esta accion";
    return;
   }
   $this->load->library("mailer");
   
   
   $rs=null;
   $id_empresa=$this->visitante->get_id_empresa();
   $id_pago= $_REQUEST['id_pago'];
   $email= $_REQUEST['email'];
   $id_usuario= $this->visitante->get_id();
   $path_pdf=$this->pdfpago($id_pago,$id_empresa,true);
   $rs=$this->mservice->get("empresa",array("*"),"id_empresa=$id_empresa");
   $rspago=$this->mservice->get("verpagos",array("*"),"id_empresa=$id_empresa and id_pago=$id_pago");
  
  $strnombrecompleto="";
  
   if(count($rs)>0){
   	$logo=$rs[0]['logo'];
   	$strnombrecompleto=$rspago[0]['strnombrecompleto'];
   	$implementacion_nombre=$rs[0]['empresa'];
     ob_start();
     include("tpl/mail_recibo_pago.php");
     $htmlcuerpo = ob_get_clean();
     $parametros=array();
     $rs=$this->mservice->get("empresa_datos_pagina",array("*"),"id_empresa=$id_empresa");
      foreach ($rs as $fila) {
        $parametros[$fila['variable']]=$fila['valor'];
      }
    
      $remitente=$parametros['email'];
      $con=array('host' =>$parametros['email_host'],'ptosalida' =>$parametros['email_pto'],'ssl'=>($parametros['email_ssl']==1)?true:false,'mail'=> $remitente,'pass'=>$parametros['email_pwd']);
    $empresa=$parametros['meta_autor'];
    $ex=$this->mailer->sendsimplemail($email,"recibo de pago ".$id_pago,$htmlcuerpo,$remitente,$empresa,$con, array($path_pdf));

    if(!$ex){
      $this->fwxml->numError=2;
      $this->fwxml->descError=$this->mailer->descError();
    }
    unlink($path_pdf);//elimino archivo pdf 
    }else{
      $this->fwxml->numError=1;
      $this->fwxml->descError="Disculpe. No podemos generar el pdf. Intente luego";
       
    }
   $this->fwxml->ResponseJson();
  }

function recibo($token){
	$valores=$this->mtoken->getvalores($token);
	if(isset($valores['id_pago']) && isset($valores['id_empresa'])){
		$this->pdfpago($valores['id_pago'],$valores['id_empresa']);
	}

}

}//class
