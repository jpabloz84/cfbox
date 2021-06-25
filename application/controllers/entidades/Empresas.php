<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Empresas extends CI_Controller {
private $visitante;
private $definiciones;

	function  __construct()
	{  
		parent::__construct();				
		$this->visitante=unserialize($this->session->visitante);
		$this->load->model("mservice");
		$this->load->library("listener");		
		$this->load->model("entidades/mempresa");		
		$this->load->model("seguridad/mconfiguracion");		
		$id_rol=$this->visitante->get_id_rol();
		$cond="";
		//sistema, ingresa a cualquier empresa
		if($id_rol==1){
			$cond="1=1";
		}else{
			$cond="id_empresa=".$this->visitante->get_id_empresa();
		}

		$this->definiciones['selempresa']="select *,DATE_FORMAT(inicio_actividades,'%d/%m/%Y') as ini_actividades from empresa where $cond and empresa like '%?%'";
		$this->definiciones['empresadata']="select e.*,DATE_FORMAT(inicio_actividades,'%d/%m/%Y') as ini_actividades,l.id_pro from empresa e left outer join verlocalidades l on e.id_loc=l.id_loc where id_empresa=?";
		$this->definiciones['empresa_datos_pagina']="select * from empresa_datos_pagina where id_empresa=?";
		$this->definiciones['empresa_configuracion']="select * from configuracion	 where id_empresa=?";
$this->definiciones['empresa_sucursales']="SELECT s.*,l.`id_pro` FROM sucursal s LEFT JOIN localidades l ON s.`id_loc`=l.`id_loc` where s.id_empresa=?";

		$this->definiciones['empresa_items']="SELECT i.id_tipo,i.`tipo`,i.vigente,i.default FROM vertipo_item i where i.id_empresa=? order by i.tipo";

		$this->definiciones['items']="select * from tipo_item order by tipo";


	}


	function listenfwt(){
		if(!$this->loginOn() || !$this->visitante->permitir('prt_configuracion',1))
		{
			$this->load->view('vlogin');
			return; 
		}
    $json=json_decode($this->input->post("data"),true);    
    $definicion=$this->input->post("definicion");
    
    if($definicion=="selempresa"){
    	$selecEmpresa=$this->definiciones['selempresa'];
    	$nombres=$json['nombres'];
    	if($nombres==""){    		
    		$selecEmpresa=str_replace("and empresa like '%?%'", "", $selecEmpresa);
    	}else{
    		$selecEmpresa=str_replace("?", $nombres, $selecEmpresa);
    	}
		$this->definiciones['selempresa']=$selecEmpresa;
		
    }
    
    $this->listener->get($this->definiciones[$definicion],$json); 
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
		$this->load->view('entidades/vempresas',$arrVariales);
		
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
		$this->fwxml->numError=0;
		$this->fwxml->descError="";
		$exito=true;
		$idempresa=0;
		if(!$this->loginOn() || !$this->visitante->permitir('prt_configuracion',64))
		{
			$this->fwxml->numError=1;
			$this->fwxml->descError="no tiene permisos para esto";
			$this->fwxml->ResponseJson(); 
			return;
		}
    $campos=json_decode(file_get_contents('php://input'), true);


    
    /*datos para tabla empresa*/
    $cmpempresa=array();
    $cmpempresa['id_empresa']=$campos['id_empresa'];
    $cmpempresa['empresa']=$campos['nombreempresa'];
    $cmpempresa['cuil']=$campos['cuil'];
    $cmpempresa['telefono']=$campos['telefono'];
    $cmpempresa['direccion']=$campos['direccion'];
    $cmpempresa['id_loc']=$campos['id_loc'];
    $cmpempresa['habilitado']=($campos['habilitado']==1)?1:0;
    $cmpempresa['ingresos_brutos']=$campos['ingresos_brutos'];
    $cmpempresa['inicio_actividades']=($campos['inicio_actividades']=="")?"":todate($campos['inicio_actividades'],103);
    $cmpempresa['id_cond_iva']=$campos['id_cond_iva'];
    $cmpempresa['page']=$campos['page'];
    if($campos['logo']!=""){
    $logoname="logo_".$cmpempresa['page'];
    $cmpempresa['logo']=$this->copy_img($campos['logo'],$logoname); 	
    }
    
    $idempresa=$this->mempresa->guardar($cmpempresa);
    $this->fwxml->arrResponse=array('id_empresa'=>$idempresa);
    $this->fwxml->numError=$this->mempresa->numError();    	
		$this->fwxml->descError=$this->mempresa->descError();
    $exito=($this->mempresa->numError()==0);


    //se guardan datos de la pagina en la tabla empresa_datos_pagina que luego esta info es para la configuracion general de la pagina
    if($exito){
    	$datos=array();
    	$datos_pagina=$campos['datos_pagina'];
    	$datos['page']=$datos_pagina['page'];
    	$datos['meta_autor']=$datos_pagina['meta_autor'];
    	$datos['meta_keywords']=$datos_pagina['meta_keywords'];
    	$datos['google_ads_cliente']=$datos_pagina['google_ads_cliente'];
    	$datos['fb_page']=$datos_pagina['fb_page'];
    	$datos['title_page']=$datos_pagina['title_page'];
    	$datos['fb_page_id']=$datos_pagina['fb_page_id'];
    	$datos['instagram_page']=$datos_pagina['instagram_page'];
    	$datos['twiter_page']=$datos_pagina['twiter_page'];
    	$datos['id_prov_default']=$datos_pagina['id_prov_default'];
    	$datos['id_loc_default']=$datos_pagina['id_loc_default'];
    	$datos['whatsapp1']=$datos_pagina['whatsapp1'];
    	$datos['whatsapp2']=$datos_pagina['whatsapp2'];
    	$datos['quienes_somos']=$datos_pagina['quienes_somos'];
    	$datos['titulo']=$datos_pagina['titulo'];
    	$datos['slogan']=$datos_pagina['slogan'];
    	$datos['localidad']=$datos_pagina['localidad_contacto'];
    	$datos['provincia']=$datos_pagina['provincia_contacto'];
    	$datos['codigo_postal']=$datos_pagina['codigo_postal'];
    	$datos['direccion']=$datos_pagina['direccion_contacto'];
    	$datos['email']=$datos_pagina['email'];
    	$datos['email_host']=$datos_pagina['email_host'];
    	$datos['email_pto']=$datos_pagina['email_pto'];
    	$datos['email_ssl']=$datos_pagina['email_ssl'];
    	$datos['email_pwd']=$datos_pagina['email_pwd'];
      $datos['page']=$datos_pagina['page'];
      $datos['meta_autor']=$datos_pagina['meta_autor'];
      $datos['video_live']=$datos_pagina['video_live'];
      $datos['video_live_code']=$datos_pagina['video_live_code'];
      $datos['atencion_publico']=$datos_pagina['atencion_publico'];

    	
    	$this->mempresa->guardar_datos_pagina($idempresa,$datos);
    	$this->fwxml->numError=$this->mempresa->numError();    	
			$this->fwxml->descError=$this->mempresa->descError();
    	$exito=($this->mempresa->numError()==0);
    }

    //se guardan datos de configuracion de la empresa
    if($exito){
    	$datos=array();
    	$configuracion=$campos['configuracion'];
    	$datos['total_max_comp_c']=$configuracion['total_max_comp_c'];
    	$datos['mp_token_access']=$configuracion['mp_token_access'];
    	$datos['mp_modo_produccion']=$configuracion['mp_modo_produccion'];    	
    	$datos['log_activo']=$configuracion['log_activo'];
    	$datos['orden_pedido']=$configuracion['orden_pedido'];
    	$datos['comprobantes']=$configuracion['comprobantes'];
    	$datos['guardar_compra']=$configuracion['guardar_compra'];
    	$datos['facebook_app_id']=$configuracion['fb_page_id'];

    	$this->mconfiguracion->configuracion_empresa($idempresa,$datos);
    	$this->fwxml->numError=$this->mconfiguracion->numError();    	
			$this->fwxml->descError=$this->mconfiguracion->descError();
    	$exito=($this->mconfiguracion->numError()==0);
    }

    if($exito){
    	$tipos_items=$campos['tipos_items'];
    	$exito=$this->mempresa->guardar_items($idempresa,$tipos_items);
    	if(!$exito){
    		$this->fwxml->numError=$this->mempresa->numError();    	
			$this->fwxml->descError=$this->mempresa->descError();
    	}
    }

	$this->fwxml->ResponseJson();

	}


	function copy_img($base64_string,$filename){
    $directorio =  realpath(__DIR__ . '/../../../')."/files/empresas/"; // directorio
    if (!is_dir($directorio)) {
      mkdir($directorio);
    }
    $path_img="";
    try {
    $data = explode(',', $base64_string);
    $extension ="png";
    $imgdata=null;
    if(count($data)>1){
    $imgdata=$data[1];  
    }else{
    $imgdata=$base64_string;
    }
    
    $output_file=$directorio.$filename.".".$extension;
    $file = fopen($output_file, "wb");
    fwrite($file, base64_decode($imgdata));
    fclose($file);
    
    $path_img="files/empresas/".$filename.".".$extension;
    } catch (Exception $e) {
      $path_img="";
    }
    return $path_img;
  }



	function guardar_sucursales()
	{  

		$this->fwxml->numError=0;
		$this->fwxml->descError="";
		$exito=true;
		$idempresa=0;
		if(!$this->loginOn() || !$this->visitante->permitir('prt_configuracion',64))
		{
			$this->fwxml->numError=1;
			$this->fwxml->descError="no tiene permisos para esto";
			$this->fwxml->ResponseJson(); 
			return;
		}
    $sucursales=json_decode($this->input->post("datasucursales"), true);

		foreach ($sucursales as  $sucursal) {
			$this->mempresa->guardar_sucursal($sucursal);
		}
		$this->fwxml->ResponseJson();
	}
	
	

	function eliminar_sucursal()
	{  

		if(!$this->loginOn() || !$this->visitante->permitir('prt_configuracion',64))
		{
			$this->fwxml->numError=1;
			$this->fwxml->descError="no tiene permisos para esto";
			$this->fwxml->ResponseJson(); 
			return;
		}
	  $id_sucursal=$this->input->post("id_sucursal");
	  if($id_sucursal>0){
	  $this->mempresa->eliminar_sucursal($id_sucursal);	
	  $this->fwxml->numError=$this->mempresa->numError();
		$this->fwxml->descError=$this->mempresa->descError();
	  }
	  
	  	$this->fwxml->ResponseJson(); 
	}

function save()
{	$exito=false;
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->load->model("mpersona");
	$modo=$this->input->post('modo');
	$numError=0;
	$descError='';
	if($this->loginOn() && (($modo=='M' && $this->visitante->permitir('prt_clientes',4)) || ($modo=='A' && $this->visitante->permitir('prt_clientes',2))))
	 {
	 	$id_persona=$this->input->post('id_persona');
		
		$apellido=$this->input->post('inp_apellido');
		$nombres=$this->input->post('inp_nombres');
		$domicilio=$this->input->post('inp_domicilio');
		$tipo_docu=$this->input->post('inp_tipo_docu');
		$nro_docu=$this->input->post('inp_nro_docu');
		$cuit=$this->input->post('inp_cuit');
		$id_loc=$this->input->post('inp_localidad');
		$sexo=$this->input->post('sexoRadios');
		$provincia=$this->input->post('inp_provincia');
		$telefono=$this->input->post('inp_telefono');
		$provincia_naci=$this->input->post('inp_provincia_naci');
		$localidad_naci=$this->input->post('inp_localidad_naci');
		$id_cond_iva=$this->input->post('inp_condiciones');
		$fe_naci=$this->input->post('inp_fe_naci');
		$email=$this->input->post('inp_email');
		$tipo_persona=$this->input->post('inp_tipo_persona');		
		$observaciones=$this->input->post('inp_observaciones');		
		$id_cliente=$this->input->post('id_cliente');
		
		$path_img="";
		$numError=0;
		$descError="";
	    
		try {
		 

		    if($_FILES['inp_img_profile']['name'] != ""){ // El campo foto contiene una imagen...        
		            // Primero, hay que validar que se trata de un JPG/GIF/PNG
		            $allowedExts = array("jpg", "jpeg", "gif", "png", "JPG", "GIF", "PNG");
		            $tmp=explode(".", $_FILES["inp_img_profile"]["name"]);
		            $extension = end($tmp);
		            if ((($_FILES["inp_img_profile"]["type"] == "image/gif")
		                    || ($_FILES["inp_img_profile"]["type"] == "image/jpeg")
		                    || ($_FILES["inp_img_profile"]["type"] == "image/png")
		                    || ($_FILES["inp_img_profile"]["type"] == "image/pjpeg"))
		                    && in_array($extension, $allowedExts)) {
		                // el archivo es un JPG/GIF/PNG, entonces...   
		                $tmp=explode('.', $_FILES['inp_img_profile']['name']);
		                $extension = end($tmp);
		                $foto = substr(md5(uniqid(rand())),0,10).".".$extension;
		                $directorio =  realpath(__DIR__ . '/../../../')."/files/profiles"; // directorio de tu elección	                
		                // almacenar imagen en el servidor
		                move_uploaded_file($_FILES['inp_img_profile']['tmp_name'], $directorio.'/'.$foto);
		                $minFoto = 'thumbs_'.$foto;
		                $resFoto = 'img_'.$foto;
		                resizeImagen($directorio.'/', $foto, 65, 65,$minFoto,$extension);
		                resizeImagen($directorio.'/', $foto,700,700,$resFoto,$extension);
		                unlink($directorio.'/'.$foto);
		               $path_img="files/profiles/".$resFoto;
		                
		            }
		        }//campo imagen

			if($id_persona>0)
			{
				
				$data=array('apellido' => $apellido,'nombres' => $nombres, 'tipo_docu' => $tipo_docu, 'nro_docu' => $nro_docu, 'sexo' =>$sexo , 'id_loc' => $id_loc,'telefono'=>$telefono,'domicilio'=>$domicilio, 'id_loc_nacimiento'=>$localidad_naci,'email'=>$email,"fe_nacimiento"=>$fe_naci,'cuit'=>$cuit, 'tipo_persona'=>$tipo_persona);	  		
				if($path_img!="")
				{
					$data['img_profile']=$path_img;
				}
				if(!$this->mpersona->actualizar($id_persona,$data))
				{
				$numError=1;
				$descError='Error al actualizar persona: '.$this->mpersona->descError()."\n";
				}
			}else
			{
				$id_persona=$this->mpersona->agregar($apellido,$nombres,$tipo_docu,$nro_docu,$sexo,$id_loc,$domicilio,$telefono,$path_img,$email,$localidad_naci,$fe_naci,$tipo_persona,$cuit);
				if($this->mpersona->numError()!=0)
				{
				$numError=3;
				$descError='Error al insertar persona: '.$this->mpersona->descError()."\n";
				}

			}

		}catch (Exception $e) {
		$numError=2;
		$descError='Excepción capturada: '.$e->getMessage()."\n";
		}//try
		if($numError==0 && $modo=='A')
		{   $id_cliente=0;
			$id_cliente=$this->mclientes->agregar($id_cond_iva,$observaciones,$id_persona);
			if($this->mclientes->numError()!=0)
				{
				$numError=4;
				$descError='Error al insertar : '.$this->mclientes->descError()."\n";
				}
		}//A
		if($numError==0 && $modo=='M')
		{ $campos=array('id_cond_iva' => $id_cond_iva,'observaciones' => $observaciones,'id_persona'=>$id_persona);

			
			if(!$this->mclientes->actualizar($id_cliente,$campos))
			{
				$numError=5;
				$descError='Error al modificar : '.$this->mclientes->descError()."\n";
			}
			if($this->mclientes->numError()!=0)
			{
			$numError=5;
			$descError='Error al modificar : '.$this->mclientes->descError()."\n";
			}
		}//M
		

	 }
	 else
	 {
	 	$numError=0;
		$descError='No inicio sesión';
	 }

	$this->fwxml->numError=$numError;
	$this->fwxml->descError=$descError;
	$this->fwxml->arrResponse=array('id_cliente' =>$id_cliente);
	$this->fwxml->ResponseJson();
 }//save


}//class
