<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Certificados extends CI_Controller {
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

		if(!$this->loginOn() || !$this->visitante->permitir('prt_abm',32))
		{
			$this->load->view('vlogin');
			return; 
		}
		$this->load->model("mservice");
		$arrVariales=array();		
		$this->load->view('vcertificados',$arrVariales);
		
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

		
	function listener($id_param=0)
	{
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$this->fwxml->arrResponse=array();
		 $arrRow=array();

	 	if(!$this->loginOn() || !$this->visitante->permitir('prt_abm',32))
		{
			$this->load->view('vlogin');
			return; 
		}

		$modo=$this->input->post("modo");
		$id_certificado=$this->input->post("id_certificado");
		$fe_emision=$this->input->post("fe_emision");
		$fe_vencimiento=$this->input->post("fe_vencimiento");
		$cuit=$this->input->post("cuit");
		$clave=$this->input->post("clave");
		$habilitado=($this->input->post("habilitado")=='true' || $this->input->post("habilitado")==1)?1:0;
		$testing=($this->input->post("testing")=='true' || $this->input->post("testing")==1)?1:0;
		$path="";
		$path_key="";
		$strfeemi=todate($fe_emision,112);
		$strfevenc=todate($fe_vencimiento,112);
		$strname=$strfeemi."_".$strfevenc;	

		//eliminacion
		if($modo=="D" && $id_param > 0)
		{		
		$campos=array('id_certificado' =>$id_certificado);
		$id=$this->mservice->eliminar_registro('certificados',$campos);		
		}
		//alta
		if($modo=="A")
		{$campos=array('habilitado'=>$habilitado,'testing'=>$testing,'cuit'=>$cuit,'clave'=>$clave);
			if($_FILES['path']['name']!="")
			{	
				$path=$this->saveuploadfile('path');
				$campos['path']=$path;
				$pathabsoluto=realpath(".")."\\".str_replace("/", "\\",$path);
				$campos=$this->armarinfo($campos,$pathabsoluto);
			}
			if($_FILES['path_key']['name']!="")
			{		
				$path_key=$this->saveuploadfile('path_key');
				$campos['path_key']=$path_key;
			}
			
			$id=$this->mservice->insertar('certificados',$campos);
			$this->fwxml->arrResponse=array('id_certificado' => $id);;
		}
		//actualizacoines
		if($modo=="E")
		{
			$campos=array('habilitado'=>$habilitado,'testing'=>$testing, 'cuit'=>$cuit ,'clave'=>$clave);
			if($_FILES['path']['name']!="")
			{
				$path=$this->saveuploadfile('path');
				$campos['path']=$path;
				$pathabsoluto=realpath(".")."\\".str_replace("/", "\\",$path);
				$campos=$this->armarinfo($campos,$pathabsoluto);
				
			}
			if($_FILES['path_key']['name']!="")
			{		
				$path_key=$this->saveuploadfile('path_key');				
				$campos['path_key']=$path_key;
			}
			$condicional=array('id_certificado=' => $id_certificado);
			$this->mservice->actualizar_registro('certificados',$campos,$condicional);
			$this->fwxml->arrResponse=array('id_certificado' =>  $id_certificado);
		}

		
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
    $this->fwxml->descError=$this->mservice->descError();
    }
    
    $this->fwxml->ResponseJson();		

	}

function armarinfo($campos,$pathabsoluto)
{
				//obtengo informacion del certificado
				$certinfo=openssl_x509_parse(file_get_contents($pathabsoluto));
				$name=$certinfo['name'];
				$campos['nombre']=$name;
				$subject="[CN]: ".$certinfo['subject']['CN']." [serialNumber]: ".$certinfo['subject']['serialNumber'];
				$campos['sujeto']=$subject;
				$issuer="[CN]: ".$certinfo['issuer']['CN'].", [O]: ".$certinfo['issuer']['O'];
				$campos['editor']=$issuer;
				$fe_desde=(string)date(DATE_W3C,$certinfo['validFrom_time_t']);
				$fe=explode("T", $fe_desde);
				$ymddesde=$fe[0];
				$hmsdesde=substr($fe[1],0,8);
				$fe_desde=$ymddesde." ".$hmsdesde;
				$campos['fe_emision']=$fe_desde;
				$fe_hasta=(string)date(DATE_W3C,$certinfo['validTo_time_t']);
				$fe=explode("T", $fe_hasta);
				$ymdhasta=$fe[0];
				$hmshasta=substr($fe[1],0,8);
				$fe_hasta=$ymdhasta." ".$hmshasta;
				$campos['fe_vencimiento']=$fe_hasta;
				return $campos;
}


	//dado el campo de upload , alamcena el archivo subido con el patron de nombre dado por el segundo parametro
	function saveuploadfile($campo,$namefilesaved='XXXXX')
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
	}
	

}//class
