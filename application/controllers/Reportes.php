<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {
private $visitante;
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");		
		$this->load->model("mservice");
		$this->visitante=unserialize($this->session->visitante);
	}
	 function index()
	{	

		if(!$this->loginOn() || !$this->visitante->permitir('prt_reportes',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		$id_empresa=$this->visitante->get_id_empresa();
		$this->load->model("mservice");
		$arrVariales=array();		
		$arrVariales['tipo_comprobantes']=$this->mservice->getSinwhere("tipo_comprobante","tipo_comp asc");
		$arrVariales['tipo_pagos']=$this->mservice->getSinwhere("tipo_pagos","tipo_pago asc");		
		$arrVariales['cajas']=$this->mservice->getCajas();		
		//print_r($arrVariales['cajas']);
		$arrVariales['estados']=$this->mservice->getwhere("estado","comprobante=1","orden asc");
		$arrVariales['talonarios']=$this->mservice->getSinwhere("vertalonarios","tipo_comp asc");		
		$this->load->view('vreportes',$arrVariales);
		
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

		
	function exportar()
	{
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$this->fwxml->xmlResponse="";
  	$this->load->library('documento');
  	$filecomp='';
	$arrRow=array();
	$fechastr=date("Ymd");
	$id_empresa=$this->visitante->get_id_empresa();
	 	if(!$this->loginOn() || !$this->visitante->permitir('prt_reportes',1))
		{
			$this->load->view('vlogin');
			return; 
		}
		try {
			$xmldata=$this->input->post('strxml');
			$xmlobject=loadXML($xmldata);
			$reporte=$xmlobject->reporte;
			$tipo_reporte=$reporte['name'];
			$condicion="id_empresa=$id_empresa ";
			$detallado=0;
			if($tipo_reporte=="comprobantes")
			{
				foreach ($reporte->parametro as $parametro)
		    {
					$campo=$parametro['name'];
					$valor=$parametro;
				
				  if($campo=='fe_desde')
				  {
				  	$condicion.=" and fe_creacion>='".todate($valor,103)."'";
				  }
				  if($campo=='fe_hasta')
				  {
				  	$condicion.=" and fe_creacion<ADDDATE('".todate($valor,103)."',1)";
				  }

				  if($campo=='detallado' && $valor==1)
				  {
				  	$detallado=1;
				  }
				  if($campo=='id_comp')
				  {
				  	$condicion.=" and id_comp=$valor";
				  }
					
					if($campo=='id_talonario')
				  {
				  	$condicion.=" and id_talonario=$valor";
				  }

				  if($campo=='nro_comp')
				  {
				  	$condicion.=" and nro_comp=$valor";
				  }
				  if($campo=='afip')
				  {
				  	$condicion.=" and afip=$valor";
				  }
				  if($campo=='estado')
				  {
				  	$condicion.=" and estado='$valor'";
				  }
					
					if($campo=='id_comp_tipo')
				  {
				  	$condicion.=" and id_comp_tipo=$valor";
				  }
					
		    }	

		    	if($detallado==0){
		    	$col=array(  "id_comp","documento","nro_docu","cuit","strnombrecompleto","condicion","CASE WHEN afip=1 THEN 'COMPROBANTE AFIP' ELSE 'ORDEN DE PEDIDO' END tipo_presentacion","tipo_comp","nro_comp","nro_talonario","fe_creacion","usuario","sucursal","cae","fe_vencimiento","descripcion AS estado", "usuario_estado", "importe_total","importe_neto","importe_base","importe_iva","importe_pago","CASE WHEN deuda<0 or id_tipo_comp IN(3,4,14) THEN 0 ELSE deuda END AS deuda","id_comp_anula", "NOW() AS fecha_consulta");
				$customdatatype=array('importe_total' =>'money','importe_neto' =>'money','importe_base'=>'money','importe_iva'=>'money' ,'importe_pago'=>'money','deuda'=>'money');
				$xmldata=$this->mservice->getxmldata("vercomprobantes",$col,$condicion,"",$customdatatype);	
			}else{
				$col=array("id_comp","rtrim(ltrim(detalle)) as detalle","cantidad","precio_venta AS precio_unitario"," importe_item AS importe_total_item","documento","nro_docu","cuit","strnombrecompleto","condicion","CASE WHEN afip=1 THEN 'COMPROBANTE AFIP' ELSE 'ORDEN DE PEDIDO' END tipo_presentacion","tipo_comp","nro_comp","nro_talonario","fe_creacion","sucursal","descripcion AS estado"," usuario_estado"," importe_total","id_tipo","nro_tipo","NOW() AS fecha_consulta");
				$customdatatype=array('precio_unitario' =>'money','importe_total_item' =>'money','importe_total'=>'money','fe_creacion'=>'datetime' ,'fecha_consulta'=>'datetime');
				$xmldata=$this->mservice->getxmldata("vercomprobantes_det",$col,$condicion,"id_comp,detalle",$customdatatype);	

			}
				
				/*print_r($xmldata);
				die();*/
				$file="comprobantes_".$fechastr."_".$id_empresa.".xls";
				if($detallado==1){
				$file="detalle_comprobantes_".$fechastr."_".$id_empresa.".xls";	
				}
				
			}//if comprobantes

			if($tipo_reporte=="pagos")
			{
				foreach ($reporte->parametro as $parametro)
		    {
					$campo=$parametro['name'];
					$valor=$parametro;
				  if($campo=='fe_desde')
				  {
				  	$condicion.=" and fe_pago>='".todate($valor,103)."'";
				  }
				  if($campo=='fe_hasta')
				  {
				  	$condicion.=" and fe_pago<ADDDATE('".todate($valor,103)."',1)";
				  }
				  if($campo=='id_cliente')
				  {
				  	$condicion.=" and id_cliente=$valor";
				  }
					
					if($campo=='id_tipo_pago' && $valor!=0)
				  {
				  	$condicion.=" and id_tipo_pago=$valor";
				  }

				  if($campo=='caja' && $valor!=0)
				  {
				  	$condicion.=" and id_caja=$valor";
				  }
				  if($campo=='cuit')
				  {
				  	$condicion.=" and cuit='$valor'";
				  }
					
					if($campo=='nro_docu')
				  {
				  	$condicion.=" and nro_docu=$valor";
				  }
					
		    }	

		    $customdatatype=array('pago' =>'money');
				$col=array("id_pago","pago","fe_pago","documento","nro_docu","cuit","strnombrecompleto","observacion","tipo_pago","usuario","id_caja_op","fe_inicio","caja","sucursal","CASE WHEN anulado=1 THEN 'PAGO ANULADO' ELSE 'REALIZADO' END estado","fe_anulado","usuario_anula", "NOW() AS fecha_consulta");
				$xmldata=$this->mservice->getxmldata("verpagos",$col,$condicion,"",$customdatatype);
				
				
				$file="pagos_".$fechastr."_".$id_empresa.".xls";
			}//if pagos


		


			
				if($this->mservice->numError()==0)
				{ 
					$this->documento->parseXsl('tpl/Excel_tpl.xsl',$xmldata);				
					$filereport=$this->documento->save('files/reportes/'.$file);	
					if($this->documento->numError()==0){			
					$this->fwxml->descError=$filereport;				
					}else
					{
					$this->fwxml->numError=	$this->documento->numError();
					$this->fwxml->descError=	$this->documento->descError();
					}
				}else
				{$this->fwxml->numError=	$this->mservice->numError();
				$this->fwxml->descError=	$this->mservice->descError();
				}
		}catch (Exception $e){
	  	$this->fwxml->numError=1;
			$this->fwxml->descError=$e->getMessage();
	  }   		
  	$this->fwxml->Response();
	}


	//dado el campo de upload , alamcena el archivo subido con el patron de nombre dado por el segundo parametro
	/*function saveuploadfile($campo,$namefilesaved='XXXXX')
	{
		$pathfinal="";
		if($_FILES[$campo]['name'] != ""){ // El campo contiene algo...        
		            // Primero, hay que validar que se trata de un CRT/KEY
		            $allowedExts = array("crt", "key","CRT","KEY");
		            $tmp=explode(".", $_FILES[$campo]["name"]);
		            $extension = end($tmp);
		            if (in_array($extension, $allowedExts)) {
		                // el archivo es un CRT/KEY, entonces...   
		                $tmp=explode('.', $_FILES[$campo]['name']);
		                $extension = end($tmp);
		                $certificado = $namefilesaved."_".substr(md5(uniqid(rand())),0,6).".".$extension;
		                $directorio =  realpath(__DIR__ . '/../../')."/files/cert"; // directorio de tu elección	                
		                // almacenar imagen en el servidor
		                move_uploaded_file($_FILES[$campo]['tmp_name'], $directorio.'/'.$certificado);		
		                //unlink($directorio.'/'.$certificado);
		               $pathfinal="files/cert/".$certificado;
		                
		            }
		        }//campo imagen
		return $pathfinal;
	}*/
	

}//class
