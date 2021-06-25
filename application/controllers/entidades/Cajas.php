<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends CI_Controller {
private $visitante;
	function  __construct()
	{  
		parent::__construct();				
		$this->visitante=unserialize($this->session->visitante);
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
		$this->load->view('entidades/vclientes',$arrVariales);
		
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
	

	function ingresar_alta()
	{  

		if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}
		

	   $this->load->model("mlocalidades");
	   $this->load->model("mservice");
		$arrVariales=array();		
		$arrVariales['provincias']=$this->mlocalidades->getProvincias();
		$arrVariales['documentos']=$this->mservice->getDocumentos();
		$arrVariales['condicionesiva']=$this->mservice->getCondicionesIva();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['modo']='A';
		$arrVariales['id_persona']=0;
		$arrVariales['id_cliente']=0;
		$arrVariales['callback']=base_url()."index.php/entidades/clientes/";		
		
		$this->load->view('entidades/vclientes_datos',$arrVariales);
	}
	
	

	function modificar($id_cliente)
	{  

		if(!$this->loginOn() or !$this->visitante->permitir('prt_clientes',4))
		{
			return;
		}
	   $this->load->model("mlocalidades");
	   $this->load->model("mservice");
		$arrVariales=array();
		$arrVariales['provincias']=$this->mlocalidades->getProvincias();
		$arrVariales['documentos']=$this->mservice->getDocumentos();
		$arrVariales['condicionesiva']=$this->mservice->getCondicionesIva();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['modo']='M';
		$arrVariales['id_persona']=0;
		$arrVariales['id_cliente']=$id_cliente;		
		$arrVariales['callback']=base_url()."index.php/entidades/clientes/";			
		$this->load->view('entidades/vclientes_datos',$arrVariales);
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
