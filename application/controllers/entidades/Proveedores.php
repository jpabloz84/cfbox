<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {
private $visitante;
	function  __construct()
	{  
		parent::__construct();		
		$this->load->model("seguridad/mpermisos");
		$this->load->model("seguridad/musuarios");
		$this->load->model("entidades/mproveedores");
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
		$this->load->view('entidades/vproveedores',$arrVariales);
		
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
		//$arrVariales['condicionesiva']=$this->mservice->getCondicionesIva();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['modo']='A';
		$arrVariales['id_persona']=0;
		$arrVariales['id_proveedor']=0;
		$arrVariales['callback']=base_url()."index.php/entidades/proveedores/";		
		
		$this->load->view('entidades/vproveedores_datos',$arrVariales);
	}
	
	

	function modificar($id_proveedor)
	{  

		if(!$this->loginOn() or !$this->visitante->permitir('prt_abm',2))
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
		$arrVariales['id_proveedor']=$id_proveedor;		
		$arrVariales['callback']=base_url()."index.php/entidades/proveedores/";			
		$this->load->view('entidades/vproveedores_datos',$arrVariales);
	}

function save()
{	$exito=false;
	$this->fwxml->numError=0;
	$this->fwxml->descError="";
	$this->load->model("mpersona");
	$modo=$this->input->post('modo');
	$numError=0;
	$descError='';
	$id_empresa=$this->visitante->get_id_empresa();
	if($this->loginOn() && (($modo=='M' && $this->visitante->permitir('prt_abm',2)) || ($modo=='A' && $this->visitante->permitir('prt_abm',2))))
	 {
	 	$id_persona=$this->input->post('id_persona');		
		$apellido=$this->input->post('inp_apellido');
		$nombres=$this->input->post('inp_nombres');
		$calle=$this->input->post('inp_calle');
		$nro=$this->input->post('inp_nro');
		$piso=$this->input->post('inp_piso');
		$dpto=$this->input->post('inp_dpto');
		$tipo_docu=$this->input->post('inp_tipo_docu');
		$nro_docu=$this->input->post('inp_nro_docu');
		$cp=$this->input->post('inp_cp');
		$cuit=$this->input->post('inp_cuit');
		$id_loc=$this->input->post('inp_localidad');
		$sexo=$this->input->post('sexoRadios');
		$provincia=$this->input->post('inp_provincia');
		$car_tel=$this->input->post('inp_car_tel');
		$nro_tel=$this->input->post('inp_nro_tel');
		$provincia_naci=$this->input->post('inp_provincia_naci');
		$localidad_naci=$this->input->post('inp_localidad_naci');
		//$id_cond_iva=$this->input->post('inp_condiciones');
		$proveedor=$this->input->post('inp_proveedor');
		$fe_naci=$this->input->post('inp_fe_naci');
		$email=$this->input->post('inp_email');
		$tipo_persona=$this->input->post('inp_tipo_persona');		
		$observaciones=$this->input->post('inp_observaciones');		
		$id_proveedor=$this->input->post('id_proveedor');
		$auspiciante=($this->input->post('inp_auspiciante')=="on")?1:0;
		
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
		        }//campo imagen*/

			if($id_persona>0)
			{
				
				$data=array('apellido' => $apellido,'nombres' => $nombres, 'tipo_docu' => $tipo_docu, 'nro_docu' => $nro_docu, 'sexo' =>$sexo , 'id_loc' => $id_loc,'cp'=>$cp,'car_tel'=>$car_tel,'nro_tel'=>$nro_tel,'calle'=>$calle,'nro'=>$nro,'piso'=>$piso,'dpto'=>$dpto, 'id_loc_nacimiento'=>$localidad_naci,'email'=>$email,"fe_nacimiento"=>$fe_naci,'cuit'=>$cuit, 'tipo_persona'=>$tipo_persona);	  		
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
				$id_persona=$this->mpersona->agregar($apellido,$nombres,$tipo_docu,$nro_docu,$sexo,$id_loc,$calle,$nro,$piso,$dpto,$cp,$car_tel,$nro_tel,$path_img,$email,$localidad_naci,$fe_naci,$tipo_persona,$cuit);

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
		{   $id_proveedor=0;
			$id_proveedor=$this->mproveedores->agregar($proveedor,$observaciones,$id_persona,$auspiciante,$id_empresa);
			if($this->mproveedores->numError()!=0)
				{
				$numError=4;
				$descError='Error al insertar : '.$this->mproveedores->descError()."\n";
				}
		}//A
		if($numError==0 && $modo=='M')
		{ $campos=array('proveedor'=>$proveedor,'observaciones' => $observaciones,'id_persona'=>$id_persona,'auspiciante'=>$auspiciante,'id_empresa'=>$id_empresa);

			
			if(!$this->mproveedores->actualizar($id_proveedor,$campos))
			{
				$numError=5;
				$descError='Error al modificar : '.$this->mproveedores->descError()."\n";
			}
			if($this->mproveedores->numError()!=0)
			{
			$numError=5;
			$descError='Error al modificar : '.$this->mproveedores->descError()."\n";
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
	$this->fwxml->arrResponse=array('id_proveedor' =>$id_proveedor);
	$this->fwxml->ResponseJson();
 }//save


}//class
