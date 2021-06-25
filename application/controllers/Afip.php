<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Afip extends CI_Controller {

	function  __construct()
	{  
		parent::__construct();
		//$this->load->library('session');		
		//$this->load->helper('url');
		//$this->load->library('Fwxml');		
		$this->load->model("mservice");
		$this->visitante=unserialize($this->session->visitante);
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
	
	
	 function index()
	{
		 
		 if(!$this->loginOn() || !$this->visitante->permitir('prt_configuracion',1))
		{

			$this->load->view('vlogin');
			return; 
		}

		$arrVariales=array();
		$arrVariales['visitante']=$this->visitante;
		$arrVariales['talonarios']=$this->mservice->getSinwhere('vertalonarios','nro_talonario');
		$this->load->view('vafip',$arrVariales);
	}	

function testingsrv()
{
	$this->fwxml->numError=0;
  $this->fwxml->descError="";
  $this->fwxml->arrResponse=array();
$res="";
  $id_talonario=$this->input->post("id_talonario");
  $rst=$this->mservice->get("vertalonarios", array("*"),"id_talonario=$id_talonario","");
			if(!count($rst)>0)
			{	$this->numError=1;
				$this->descError="certificado o talonario no habilitado o disponible. Verifique su disponibilidad para el nro. $nro_talonario  de $tipo_comp (ID $id_talonario) o que el mismo no esté vencido";
				
			}else
			{
			$dettalonario=$rst[0];
			
			$id_certificado=$dettalonario['id_certificado'];
			$testing=($dettalonario['testing']==1)?true:false;
			$pathcarperta=realpath(".")."\\afip\\";
			$this->load->library('wsafip',array('pathcarpeta' => $pathcarperta));
			$this->wsafip->init($id_certificado,$this->mservice);
			
				$status=$this->wsafip->getStatusSrv();
				if($this->wsafip->numError==0)
				{
					$res.="<br/>Servidor de aplicaciones AFIP :".$status;
				}else
				{
					$res.="<br/>Error al consultar srv aplicaciones:".$this->wsafip->descError;
				}

				$status=$this->wsafip->getStatusDb();
				if($this->wsafip->numError==0)
				{
					$res.="<br/>Servidor de base de datos AFIP :".$status;
				}else
				{
					$res.="<br/>Error al consultar srv base de datos:".$this->wsafip->descError;
				}

				$status=$this->wsafip->getStatusAuth();
				if($this->wsafip->numError==0)
				{
					$res.="<br/>Servidor de Autenticidad AFIP :".$status;
				}else
				{
					$res.="<br/>Error al consultar srv de Autenticidad:".$this->wsafip->descError;
				}

				$certinfo=$this->wsafip->getInfocert();

				$fe_desde=(string)date(DATE_W3C,$certinfo['validFrom_time_t']);
				$fe=explode("T", $fe_desde);
				$ymddesde=$fe[0];
				$hmsdesde=substr($fe[1],0,8);
				$fe_desde=$ymddesde." ".$hmsdesde;
				$fe_hasta=(string)date(DATE_W3C,$certinfo['validTo_time_t']);
				$fe=explode("T", $fe_hasta);
				$ymdhasta=$fe[0];
				$hmshasta=substr($fe[1],0,8);
				$fe_hasta=$ymdhasta." ".$hmshasta;
				$res.="<br/>certificado vigente desde $fe_desde hasta $fe_hasta";
				$this->fwxml->descError=$res;
			}
	$this->fwxml->ResponseJson();		
}

function generata()
{
	$this->fwxml->numError=0;
  $this->fwxml->descError="";
  $this->fwxml->arrResponse=array();
	$res="";

try{
	
	$id_talonario=$this->input->post("id_talonario");
  $rst=$this->mservice->get("vertalonarios", array("*"),"id_talonario=$id_talonario","");
		if(!count($rst)>0)
		{	$this->numError=1;
				$this->descError="certificado o talonario no habilitado o disponible. Verifique su disponibilidad para el nro. $nro_talonario  de $tipo_comp (ID $id_talonario)";
				
		}else
		{
			$dettalonario=$rst[0];			
			$id_certificado=$dettalonario['id_certificado'];
			$params=array('pathcarpeta'=>realpath(".")."\\afip\\");
			$this->load->library('wsafip',$params);
			$this->wsafip->init($id_certificado,$this->mservice);

			$taOk=$this->wsafip->verificaTA();
			if(!$taOk)
			{
				if(!$this->wsafip->obtener_TA())
				{
					$res.="<br/>Error al obtener_TA : (Error nro.: ".$this->wsafip->numError.") ".$this->wsafip->descError;
				}else{
					$taOk=$this->wsafip->verificaTA();
					if(!$taOk)
					{
					$res.="<br/>Se generó un ticket de acceso, pero no se pudo comprobar:(".$this->wsafip->numError.") ";
					$res.=$this->wsafip->descError;				
					}
				}
				
			}
			if($taOk)
			{
				$res.="<br/>El TA se generó correctamente";
			}				
		$this->fwxml->descError=$res;
	 }
	}catch (Exception $e) {
		$this->fwxml->numError=-1;
		$this->fwxml->descError=$e->getMessage();
	}
	$this->fwxml->ResponseJson();		
}


function lastcmp()
{

$this->fwxml->numError=0;
  $this->fwxml->descError="";
  $this->fwxml->arrResponse=array();
	$res="";
  $id_talonario=$this->input->post("id_talonario");
  $rst=$this->mservice->get("vertalonarios", array("*"),"id_talonario=$id_talonario","");
			if(!count($rst)>0)
			{	$this->numError=1;
				$this->descError="certificado o talonario no habilitado o disponible. Verifique su disponibilidad para el nro. $nro_talonario  de $tipo_comp (ID $id_talonario)";
				
			}else
			{
			$dettalonario=$rst[0];
			$id_certificado=$dettalonario['id_certificado'];
			$params=array('pathcarpeta'=>realpath(".")."\\afip\\");
			$this->load->library('wsafip',$params);
			$this->wsafip->init($id_certificado,$this->mservice);
			$this->wsafip->cargar_TA();
			$taOk=$this->wsafip->verificaTA();
			if(!$taOk)
			{
					$res.="<br />Error al verificarTicket de acceso: ".$this->wsafip->descError;				
			}else
			{
				$nrocmp=$this->wsafip->RecuperaLastCMP($dettalonario['nro_talonario'], $dettalonario['tipo_comp_afip']);
				$res.="<br/>Ultimo comprobante para ".$dettalonario['tipo_comp']." talonario ".$dettalonario['nro_talonario'].": ".$nrocmp;
			}
				$this->fwxml->descError=$res;
			}
	$this->fwxml->ResponseJson();		

}



function getptovta()
{

$this->fwxml->numError=0;
  $this->fwxml->descError="";
  $this->fwxml->arrResponse=array();
	$res="";
  $id_talonario=$this->input->post("id_talonario");
  $rst=$this->mservice->get("vertalonarios", array("*"),"id_talonario=$id_talonario","");
  $data="";
			if(!count($rst)>0)
			{	$this->numError=1;
				$this->descError="certificado o talonario no habilitado o disponible. Verifique su disponibilidad para el nro. $nro_talonario  de $tipo_comp (ID $id_talonario)";
				
			}else
			{
			$dettalonario=$rst[0];
			$id_certificado=$dettalonario['id_certificado'];
			$params=array('pathcarpeta'=>realpath(".")."\\afip\\");
			$this->load->library('wsafip',$params);
			$this->wsafip->init($id_certificado,$this->mservice);
			
			$this->wsafip->cargar_TA();
			$taOk=$this->wsafip->verificaTA();
			$cuitempresa=$dettalonario['cuit'];
			if(!$taOk)
			{
					$res.="<br />Error al verificarTicket de acceso: ".$this->wsafip->descError;				
			}else
			{
				$result=$this->wsafip->FEParamGetPtosVenta();

				if($this->wsafip->numError!=0){
					$this->fwxml->numError=3;
				$res.="<br/>No se pudo consultar: ".$cuitempresa.":".$this->wsafip->descError;		
				}else{
					$xmlresponse=$this->wsafip->getLastResponseXml();
				//$res.="<br/>Puntos de ventas habilitado para cuit ".$cuitempresa.":".$xmlresponse;	
				$this->fwxml->arrResponse=htmlspecialchars($xmlresponse);		
				}
				
			}
				$this->fwxml->descError=$res;
			}
	$this->fwxml->ResponseJson();		

}




	function listar()
	{
		$res= array();
		$arr=$this->mservice->getSinwhere('categorias','categoria ASC');
		if($this->mservice->numError()==0)
		{
			$res=json_encode($arr);
		}
		echo $res;
	}

	function update()
	{	$this->fwxml->numError=0;
    	$this->fwxml->descError="";
    	$this->fwxml->arrResponse=array();
    	return;
		 $arrRow=array();
	 	if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}
		$pk=$this->input->post('pk');
		$name=$this->input->post('name');
		$id=0;
		$arreglo=array();
		$valor="";
		if(strpos($name,"elemento-")!==false){
			$id=str_replace("elemento-", "",$name);
			$valor=$this->input->post('value');
			$arreglo = array('categoria' => $valor);
		}


		if(strpos($name,"abreviacion-")!==false){
			$id=str_replace("abreviacion-", "",$name);
			$valor=$this->input->post('value');
			$arreglo = array('abreviacion' => $valor);
		}
		
		$condicional=array('id_categoria' => $id);
		$this->mservice->actualizar_registro('categorias',$arreglo,$condicional);
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
        $this->fwxml->descError=$this->mservice->descError();
        }
    
        $this->fwxml->ResponseJson();		
	}//update

	function listener($id_param=0)
	{
		$this->fwxml->numError=0;
    	$this->fwxml->descError="";
    	$this->fwxml->arrResponse=array();
		 $arrRow=array();

	 	if(!$this->loginOn())
		{
			$this->load->view('vlogin');
			return; 
		}

		$metodo=$this->input->method(TRUE); //create:POST,update:PUT destroy:DELETE
		if($metodo=="DELETE" && $id_param > 0)
		{		
		$campos=array('id_categoria' =>$id_param);
		$id=$this->mservice->eliminar_registro('categorias',$campos);		
		}
		if($metodo=="POST")
		{
		$campos=json_decode(file_get_contents('php://input'), true);				
		$id=$this->mservice->insertar('categorias',$campos);
		$this->fwxml->arrResponse=array('id' => $id);
		}

		
		if($this->mservice->numError()!=0)
		{
		$this->fwxml->numError=$this->mservice->numError();
        $this->fwxml->descError=$this->mservice->descError();
        }
    
        $this->fwxml->ResponseJson();		

	}

	

}
