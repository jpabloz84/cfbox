<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	private $visitante=null;
	function __construct(){
		parent::__construct();
        /*$this->load->library('Fwxml');
        $this->load->library('User');
        $this->load->library('session');
        $this->load->helper('url');
		librerias cargadas en autoload
        */

        
    }

	function index()
	{	
		$this->visitante=unserialize($this->session->userdata('visitante'));
		
		if(!isset($this->visitante) || $this->visitante==null)
		{
		$this->load->view('vlogin');	
		}else
		{			
			if($this->visitante->logueado())
			{
				redirect(base_url().'index.php/panel');
			}else
			{
			$this->load->view('vlogin');		
			}
		}
		
	}
	

	function verify()
	{
		
		$this->fwxml->numError=0;
		$this->fwxml->descError="";
		$this->fwxml->xmlResponse="";
		$this->load->model("mservice");
		$user=$this->input->post('user');
		$pass=$this->input->post('pass');
		$id_sucursal=($this->input->post('id_sucursal')!=null)?$this->input->post('id_sucursal'):0;
		
		//$id_empresa=$this->input->post('id_empresa');
		if($this->user->conectarse($user,$pass,$id_sucursal))//si es usuario registrado
		{	//si es administrador, espera seleccionar sucursal
			$this->session->set_userdata('visitante',serialize($this->user));	
		
			if($this->user->get_id_rol()==1 && $this->user->get_id_caja()==0){
				$this->fwxml->numError=1000;
			$this->fwxml->descError="";
			$rs=$this->mservice->get("empresa",array("*"),null," empresa asc");
			$opciones="";
			foreach ($rs as  $fila) {
				$opciones.="<option value='".$fila["id_empresa"]."'>".$fila["empresa"]."</option>";
			}
			$this->fwxml->xmlResponse=$opciones;
			$this->fwxml->Response();
			return;
			}
			$error="";
			$ex=$this->inicializar_caja($error);
			/*$this->load->model('seguridad/mcaja');
			$id_caja_op=0;
			$id_caja=$this->user->get_id_caja();

			$id_usuario=$this->user->get_id();
			$exito=$this->mcaja->inicializarCaja($id_caja,$id_usuario,$id_caja_op);*/
			if(!$ex){
			$this->fwxml->numError=-3;
			$this->fwxml->descError=$error;
			$this->fwxml->xmlResponse='';
			}else{
				$this->load->model('seguridad/mconfiguracion');
				$conf=$this->mconfiguracion->getvariables($this->user->get_id_empresa());
				$this->user->set_conf($conf);
				$this->session->set_userdata('visitante',serialize($this->user));		
				$this->fwxml->numError=0;
				$this->fwxml->descError='index.php/panel';
				$this->fwxml->xmlResponse='';			
			}
			
		}
		else
		{
			//$_SESSION["visitante"]=serialize($this->user);	
			$this->fwxml->numError=-1;
			$this->fwxml->descError='bad login';
			$this->fwxml->xmlResponse='';
		}
		$this->fwxml->Response();
	}

	function salir()
	{
	//unset($_SESSION['visitante']);
    //session_destroy();
		 $this->session->unset_userdata('visitante');
		 $this->session->sess_destroy();
		 redirect(base_url());
	}

	function getsucursales(){

		
		$this->fwxml->numError=0;
		$this->fwxml->descError="";
		$this->fwxml->xmlResponse="";
		$this->load->model("mservice");
		$id_empresa=$this->input->post('id_empresa');
		$rs=$this->mservice->get("versucursales",array("*"),"id_empresa=$id_empresa"," sucursal asc");
			$opciones="";
			foreach ($rs as  $fila) {
				$opciones.="<option value='".$fila["id_sucursal"]."'>".$fila["sucursal"]." - ".$fila["direccion"]." (".$fila['localidad_sucursal']." ".$fila['provincia_sucursal'].")</option>";
			}
			$this->fwxml->xmlResponse=$opciones;

		$this->fwxml->Response();	
	}//getsucursales
	//cambio de sucursal para el operador que es generico (sistemas)
	function setsucursal(){

		$this->fwxml->numError=0;
		$this->fwxml->descError="";
		$this->fwxml->xmlResponse="";
		$id_sucursal=$this->input->post('id_sucursal');
		$id_empresa=$this->input->post('id_empresa');
		$this->visitante=unserialize($this->session->userdata('visitante'));
		//var_dump($this->visitante);
		if(!isset($this->visitante) || $this->visitante==null)
		{
		$this->load->view('vlogin');	
		}else
		{//echo "aca2";			//si no esta logueado o no es operador generico (sistemas)
			if(!$this->visitante->logueado() || $this->visitante->get_id_rol()!=1)
			{		
			
			$this->load->view('vlogin');
			return;		
			}
		}
		
		$this->visitante->set_sucursal($id_sucursal);
		$this->visitante->set_empresa($id_empresa);
		$error="";
		$ex=$this->inicializar_caja($error);
		if(!$ex){
		$this->fwxml->numError=-4;
		$this->fwxml->descError=$error;
		$this->fwxml->xmlResponse='';
		}else{
			

			$this->fwxml->numError=0;
			$this->fwxml->descError='index.php/panel';
			$this->fwxml->xmlResponse='';			
		}
		$this->fwxml->Response();

	}//setsucursal

	function inicializar_caja(&$descError){
		$exito=false;
		$this->visitante=unserialize($this->session->userdata('visitante'));
		
		if(!isset($this->visitante) || $this->visitante==null)
		{
		$this->load->view('vlogin');	
		}else
		{		//	echo "aca3";
			if($this->visitante->logueado())
			{
				$this->load->model('seguridad/mcaja');
				$id_caja_op=0;
				$id_caja=$this->visitante->get_id_caja();
				$id_usuario=$this->visitante->get_id();
				$ex=$this->mcaja->inicializarCaja($id_caja,$id_usuario,$id_caja_op);
				if($this->mcaja->numError()!=0){												
				$descError=$this->mcaja->descError();
				}else{
					$this->user->set_id_caja_opera($id_caja_op);
					$exito=true;			
				}
				
			}else
			{
			$this->load->view('vlogin');		
			}
		}
		return $exito;
	}//inicializar_caja
}
