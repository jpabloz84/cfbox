<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Consultas extends CI_Controller {
private $visitante;
private $definiciones=array(); //para uso del oyente
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");
		$this->load->model("entidades/mclientes");
		$this->load->library("listener");
		$this->load->model("mservice");
		$this->visitante=unserialize($this->session->visitante);
		//cargo las definiciones de los descuentos
		$rs=$this->mservice->getwhere('descuentos','(now()>=fe_desde or fe_desde is null) and (fe_hasta is null or fe_hasta>now()) and  (id_empresa is null or id_empresa='.$this->visitante->get_id_empresa().")",'descripcion');
		foreach ($rs as  $rw) {
			$this->definiciones['descuento-'.$rw['id_descuento']]=$rw['formula'];
		}

		//cargo las definiciones de los descuentos
		$rs=$this->mservice->getwhere('recargos','(now()>=fe_desde or fe_desde is null)  and (fe_hasta is null or fe_hasta>now()) and (id_empresa is null or id_empresa='.$this->visitante->get_id_empresa().")",'descripcion');
		foreach ($rs as  $rw) {
			$this->definiciones['recargo-'.$rw['id_recargo']]=$rw['formula'];
		}

		
	}
	 function index()
	{	

		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['modo']="CC"; //cuenta corrientes consulta clientes
		$arrVariales['origen']="operaciones/consultas/"; //para saber de donde viene
		$this->load->view('operaciones/vconsultas',$arrVariales);
		
	}	



	function listener(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',1))
		{
			$this->load->view('vlogin');
			return; 
		}
    $json=json_decode($this->input->post("data"),true);    
    $definicion=$this->input->post("definicion");
    $this->listener->get($this->definiciones[$definicion],$json); 
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
	function operar($id_cliente){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',2))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->init_carrito($id_cliente);

	}//operar

	function nuevopedido($id_cliente){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',2))
		{
			$this->load->view('vlogin');
			return; 
		}

		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['id_cliente']=$id_cliente;
		$arrVariales['id_pedido']=0; //vacio: venta normal, cero:pedido nuevo,distinto de cero:edicion de pedido
		$arrVariales['id_comp']=0;
		$arrVariales['tipos_productos']=$this->mservice->getwhere('vertipo_item','vigente=1 and id_empresa='.$this->visitante->get_id_empresa());
		$arrVariales['categorias']=$this->mservice->getCategorias();
		$rscli=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa());
		$arrVariales['cliente']=$rscli[0];
		$id_empresa=$this->visitante->get_id_empresa();
			$rs=$this->mservice->get("empresa_datos_pagina",array("*"),"id_empresa=$id_empresa");
			foreach ($rs as $fila) {
				$arrVariales[$fila['variable']]=$fila['valor'];
			}
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		$this->load->view('operaciones/vpedido',$arrVariales);
		

	}//nuevo pedido

	function editarpedido($id_pedido){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',2))
		{
			$this->load->view('vlogin');
			return; 
		}
		$rsped=$this->mservice->getwhere('vercheckout_presupuesto','id_pedido='.$id_pedido.' and id_empresa='.$this->visitante->get_id_empresa());
		if(!count($rsped)>0)return;
		$id_cliente=$rsped[0]['id_cliente'];
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['id_cliente']=$id_cliente;
		$arrVariales['id_pedido']=$id_pedido; //vacio: venta normal, cero:pedido nuevo,distinto de cero:edicion de pedido
		$arrVariales['id_comp']=0;
		$arrVariales['tipos_productos']=$this->mservice->getwhere('vertipo_item','vigente=1 and id_empresa='.$this->visitante->get_id_empresa());
		$arrVariales['categorias']=$this->mservice->getCategorias();
		$rscli=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa());
		$arrVariales['cliente']=$rscli[0];
		$id_empresa=$this->visitante->get_id_empresa();
			$rs=$this->mservice->get("empresa_datos_pagina",array("*"),"id_empresa=$id_empresa");
			foreach ($rs as $fila) {
				$arrVariales[$fila['variable']]=$fila['valor'];
			}
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		$this->load->view('operaciones/vpedido',$arrVariales);
		

	}//operar

	function init_carrito($id_cliente,$id_pedido=""){
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['id_cliente']=$id_cliente;
		$arrVariales['id_pedido']=$id_pedido; //vacio: venta normal, cero:pedido nuevo,distinto de cero:edicion de pedido
		$arrVariales['id_comp']=0;
		$arrVariales['tipos_productos']=$this->mservice->getwhere('vertipo_item','vigente=1 and id_empresa='.$this->visitante->get_id_empresa());
		$arrVariales['categorias']=$this->mservice->getCategorias();
		$arrVariales['tipo_comprobantes']=$this->mservice->getwhere('vertalonarios','habilitado=1 and id_sucursal='.$this->visitante->get_id_sucursal(),'tipo_comp_afip');//$this->mservice->getSinwhere('tipo_comprobante','tipo_comp');
		//print_r($arrVariales['tipo_comprobantes']);
		$rscli=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa());
		$arrVariales['cliente']=$rscli[0];
		$arrVariales['tipo_pagos']=$this->mservice->getwhere('tipo_pagos','habilitado=1','orden');
		
		$id_empresa=$this->visitante->get_id_empresa();
			$rs=$this->mservice->get("empresa_datos_pagina",array("*"),"id_empresa=$id_empresa");
			foreach ($rs as $fila) {
				$arrVariales[$fila['variable']]=$fila['valor'];
			}
		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		$this->load->view('operaciones/voperar',$arrVariales);
	}

	function checkout($id_pedido){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',2))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['pedido']=$this->mservice->getwhere('vercheckout_presupuesto',"id_pedido=$id_pedido and id_empresa=".$this->visitante->get_id_empresa());
		//var_dump($arrVariales['pedido']);
		$id_cliente=$arrVariales['pedido'][0]['id_cliente'];
		$id_comp=$arrVariales['pedido'][0]['id_comp'];
		$arrVariales['id_cliente']=$id_cliente;
		$arrVariales['id_pedido']=$id_pedido;
		$arrVariales['id_comp']=$id_comp;
		$arrVariales['tipos_productos']=$this->mservice->getwhere('vertipo_item','vigente=1 and id_empresa='.$this->visitante->get_id_empresa());
		$arrVariales['categorias']=$this->mservice->getCategorias();
		$arrVariales['tipo_comprobantes']=$this->mservice->getwhere('vertalonarios','habilitado=1 and id_sucursal='.$this->visitante->get_id_sucursal(),'tipo_comp_afip');//
		$rscli=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa());
		$arrVariales['cliente']=$rscli[0];
		$arrVariales['tipo_pagos']=$this->mservice->getwhere('tipo_pagos','habilitado=1','orden');
		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}

		$id_empresa=$this->visitante->get_id_empresa();
			$rs=$this->mservice->get("empresa_datos_pagina",array("*"),"id_empresa=$id_empresa");
			foreach ($rs as $fila) {
				$arrVariales[$fila['variable']]=$fila['valor'];
			}


		$this->load->view('operaciones/voperar',$arrVariales);

	}//operar

	function cuenta($id_cliente){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['id_cliente']=$id_cliente;		
		$arrcli=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa(),'');	
		$arrVariales['cliente']=$arrcli[0];
		$arrVariales['saldo']=$this->mservice->getwhere('versaldo_afavor','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa(),'');	

		$arrVariales['tipo_pagos']=$this->mservice->getwhere('tipo_pagos','habilitado=1 and incide_caja=1','tipo_pago');
		$arrVariales['totales']=$this->mclientes->obtenertotales_facturados($id_cliente);
		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		$this->load->view('operaciones/vcuenta',$arrVariales);

	}//operar

	function cuentacc($id_cliente){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_pagos',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['id_cliente']=$id_cliente;		
		$arrcli=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa(),'');	
		$arrVariales['cliente']=$arrcli[0];
		$arrVariales['saldo']=$this->mservice->getwhere('versaldo_afavor','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa(),'');	

		$arrVariales['tipo_pagos']=$this->mservice->getwhere('tipo_pagos','habilitado=1 and incide_caja=1','tipo_pago');
		$arrVariales['totales']=$this->mclientes->obtenertotales_facturados($id_cliente);
		
		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		$this->load->view('operaciones/vcuentacc',$arrVariales);

	}//operarcc

	function editar($id_comp){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_operaciones',2))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$comp=$this->mservice->getwhere('vercomprobantes','id_comp='.$id_comp.' and id_empresa='.$this->visitante->get_id_empresa(),'');
		$arrVariales['comp']=$comp[0];
		$comppagos=$this->mservice->getwhere('vercomp_pagos','id_comp='.$id_comp.' and incide_caja=1 and id_empresa='.$this->visitante->get_id_empresa(),'');
		$pagos_id="";
		foreach ($comppagos as $cp) {
			if($pagos_id=="")
			{
				$pagos_id.=$cp['id_pago'];
			}else
			{
				$pagos_id.=",".$cp['id_pago'];
			}
			
		}
		$arrVariales['pagos_id']=$pagos_id;
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['id_cliente']=$comp[0]['id_cliente'];
		$arrVariales['id_comp']=$id_comp;
		$arrcli=$this->mservice->getwhere('verclientes','id_cliente='.$comp[0]['id_cliente'].' and id_empresa='.$this->visitante->get_id_empresa(),'');	
		$arrVariales['cliente']=$arrcli[0];
		$arrVariales['tipos_productos']=$this->mservice->getSinwhere('tipo_item','id_tipo');
		$arrVariales['categorias']=$this->mservice->getCategorias();
		$arrVariales['tipo_comprobantes']=$this->mservice->getwhere('vertalonarios','habilitado=1 and id_sucursal='.$this->visitante->get_id_sucursal(),'tipo_comp_afip asc');//$this->mservice->getSinwhere('tipo_comprobante','tipo_comp');
		$arrVariales['tipo_pagos']=$this->mservice->getwhere('tipo_pagos','habilitado=1','tipo_pago');
		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		$this->load->view('operaciones/voperar',$arrVariales);

	}//operar

	function comprobantes($id_cliente){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_comprobantes',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['id_cliente']=$id_cliente;
		$arrVariales['tipo_comprobantes']=$this->mservice->getSinwhere('tipo_comprobante','tipo_comp');
		$arrCliente=$this->mservice->getwhere('verclientes','id_cliente='.$id_cliente.' and id_empresa='.$this->visitante->get_id_empresa(),"");
		$arrVariales['cliente']=$arrCliente[0];
		//$arrVariales['estados']=$this->mservice->getSinwhere('estado','orden asc');
		$arrVariales['estados']=$this->mservice->getwhere('estado','comprobante=1',"orden asc");
		
		if($this->mservice->numError()!=0)
		{
			die($this->mservice->descError());
		}
		$this->load->view('operaciones/vcomprobantes',$arrVariales);

	}//comprobantes

	function comp_exportar(){
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$this->load->model("mservice");
  	$filereport="";
  	$id_cliente=$this->input->post("id_cliente");
  	$fecha_desde=trim($this->input->post("fecha_desde"));
  	$fecha_hasta=trim($this->input->post("fecha_hasta"));  	
  	$id_comp=$this->input->post("id_comp");
  	$afip=$this->input->post("afip");
  	$estado=$this->input->post("estado");
  	$cond="id_cliente=$id_cliente  and id_empresa=".$this->visitante->get_id_empresa();
  	if($id_comp!=""){
  		$cond.=" and id_comp=$id_comp ";
  	}else{
  		if($fecha_desde!=""){
  			$cond.=" and fe_creacion>='$fecha_desde' ";
  		}
  		if($fecha_hasta!=""){
  			$cond.=" and fe_creacion<ADDDATE('".$fecha_hasta."',1) ";
  		}
  		//si es distinto de uno, agrego solamente los pagos que no estan anulados
  		if($estado!=""){
  			$cond.=" and estado='$estado' ";
  		}
  		if($afip==1){
  		$cond.=" and afip=1 ";
  		}
  		
  	}
  	//print_r($cond);
  	
  	try {
  		$this->load->library('documento');
  		$cmp=array("id_comp","id_talonario","IFNULL(nro_comp,0) as nro_comp","IFNULL(nro_talonario,0) as nro_talonario","cae","id_cliente","strnombrecompleto","domicilio","telefono","nro_docu","tipo_docu","documento","tipo_persona_desc","descripcion_loc","descripcion_pro","importe_total","importe_neto","importe_base","importe_iva","importe_exento","importe_descuento","DATE_FORMAT(fe_creacion,'%d/%m/%Y %r') as fe_creacion","DATE_FORMAT(fe_comp,'%d/%m/%Y %r') as fe_comp","DATE_FORMAT(fe_vencimiento,'%d/%m/%Y %r') as fe_vencimiento","cuit","estado","descripcion","usuario_estado","usuario","afip","id_sucursal","sucursal","id_cond_iva","condicion","id_tipo_comp","tipo_comp","tipo_comp_afip","importe_pago","id_comp_anula");
			$xmldata=$this->mservice->getxmldata("vercomprobantes",$cmp,$cond," fe_creacion desc");
			
				if($this->mservice->numError()==0)
				{ 
				$this->documento->parseXsl('tpl/Excel_tpl.xsl',$xmldata);
				$file="comprobantes_cliente_".$id_cliente."_".strtolower(randString(6)).".xls";

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
	}



}//class
