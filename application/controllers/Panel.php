<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panel extends CI_Controller {
	private $visitante=null;	
	function  __construct()
	{  
		parent::__construct();		
		$this->visitante=unserialize($this->session->visitante);
		$this->load->model("mservice");		
	}
	 function index()
	{
		if(!isset($this->visitante) || $this->visitante==null)
		{
		redirect(base_url()."index.php/login");
			//$this->load->view('vlogin');
		return;
		}

		if(!$this->visitante->logueado())
		{
		redirect(base_url()."index.php/login");
			//$this->load->view('vlogin');
		return;
		}
		$this->load->model("seguridad/mconfiguracion");		
		$this->load->model("operaciones/mpedidos");		
		$itemsheader=array();
		$itemsheader['permisos_front']=$this->visitante->cargar_permisos_front();
		//$this->load->view('panel_header',$itemsheader);		
		$itemsheader['id_usuario']=$this->visitante->get_id();
		$itemsheader['visitante']=$this->visitante;
		
		$itemsheader['empresaname']="";
		$itemsheader['last_nro_pedido']=$this->mpedidos->last_nro_pedido($this->visitante->get_id_empresa());
		$itemsheader['empresa']=array();
		
		if($this->visitante->get_id_empresa()!=''){
			$rs=$this->mservice->get("empresa",array("*"),"id_empresa=".$this->visitante->get_id_empresa());
			//print_r($rs);
			$itemsheader['empresaname']=$rs[0]['empresa'];
			$itemsheader['empresa']=$rs[0];
		}else{
		$itemsheader['empresas']=$this->mservice->get("empresa",array("*"));		
		}

		
		$this->load->view('panel_header',$itemsheader);		
		$itemsbody=array();
		$itemsbody['visitante']=$this->visitante;
		$this->load->view('panel_body',$itemsbody);
		$itemsfooter['visitante']=$this->visitante;
		$this->load->view('panel_footer');
	}

	function carga()
	{

			$rs=$this->mservice->get("empresa",array("*"),"id_empresa=".$this->visitante->get_id_empresa());			
			$logo=BASE_FW."/assets/img/logo-lateral-ma.png";
			$empresa=$rs[0];
			if($empresa['logo']!=""){
				$logo=base_url().$empresa['logo'];
			}
			echo "<div id='content' class='content'><div style='margin:29px auto 0;text-align:center;'>
		<img src='".$logo."'></div></div>";
	}

	

	//obtengo las novedades globales del sistema
	function lastinfo()
	{
		$this->fwxml->numError=0;
  	$this->fwxml->descError="";
  	$this->fwxml->arrResponse=array();
  	$id_empresa=$this->visitante->get_id_empresa();
  	
		$arrJson=array();
		try {
		$strxml=loadXML($_REQUEST["strXml"]);
			foreach ($strxml->variable as $variable)
			{
			$name=$variable["name"];
			$lastvalue=$variable;
				if($name=='loginon')
				{	
					$arrJson[]= array("variable" => "$name" , "valor"=> ($this->loginOn()?1:0));
				}

				if($name=='crt_vencidos')
				{	//notifica 15 dias antes que se venza algun certificado
					$rs=$this->mservice->query("SELECT * FROM vercertificados WHERE habilitado=1 AND DATEDIFF(NOW(),fe_vencimiento)>-15 and id_empresa=$id_empresa");
					if(count($rs)>0){
					$arrJson[]= array("variable" => "$name" , "valor"=> 1,"det"=>$rs);	
					}else{
					$arrJson[]= array("variable" => "$name" , "valor"=> 0,"det"=>array());	
					}
					
				}

				if($name=='nro_pedido')
				{	//notifica 15 dias antes que se venza algun certificado
					$rs=$this->mservice->query("SELECT max(id_pedido) as nro_pedido FROM checkout_presupuesto WHERE  id_empresa=$id_empresa");
					if(count($rs)>0){
						$valor=$rs[0]['nro_pedido'];
						$arrJson[]= array("variable" => "$name" , "valor"=>$valor,"det"=>array());	
					}
					
				}

				
			}
			$this->fwxml->arrResponse=$arrJson;
  	}catch (Exception $e){
	  	$this->fwxml->numError=1;
			$this->fwxml->descError=$e->getMessage();
	  }   		
  		$this->fwxml->ResponseJson();		
	}//last info

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

}
