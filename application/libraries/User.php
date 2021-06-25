<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User {
	private $m;
	private $permisos=array();	
	private $userconect;
	private $variables=array();	
	 function __construct(){     
	$this->m =& get_instance();    //--------------
    $this->m->load->model('muser');
     
    }
     function conectarse($user,$pass,$id_sucusal=0)
    { $exito=false;
    	
    	if($this->m->muser->conectarse($user,$pass,$id_sucusal))
    	{
    	
			$exito=true;
			$this->userconect=$this->m->muser;
			$this->permisos=$this->m->muser->get_permisos();
    	}
    	return $exito;
    }

     function permitir($permiso_modulo,$bit_permiso)
	{$exito=false;
		/*echo "permit";
		print_r($this->permisos);*/
		if (array_key_exists ($permiso_modulo, $this->permisos)) {
			
    	$permiso=$this->permisos[$permiso_modulo];    	    	
    	$exito=(($permiso & $bit_permiso)>0);
		}
		return $exito;
	}

	 function cargar_permisos_front()
	{
		$string="";
			$string.="<script>\n";	
		 	foreach ($this->permisos as $clave => $valor) {
 			$nro_permiso = $valor;
			$permiso_modulo=$clave;
			$string.="var $permiso_modulo=$nro_permiso;\n ";
			}
			$string.="function permitir(nro_permiso,bit_permiso){\n ";			
				$string.="return ((nro_permiso & bit_permiso) > 0)\n";				
			$string.="}\n";
			$string.="</script>\n";
	return $string;		
	}//cargar_permisos_front

	 function logueado()
	{//echo "objeto:".print_r($this->m->muser);
		return $this->userconect->logueado();
	}
	/*setea las variables de configuracion genericas*/
	function set_conf($conf)
	{//echo "objeto:".print_r($this->m->muser);
		return $this->variables=$conf;
	}
	function get_conf(){
		return $this->variables;
	}

	 function get_rol() 
	{
	return $this->userconect->get_rol();
	}

	function get_id_sucursal() 
	{
	return $this->userconect->get_id_sucursal();
	}

	function get_id_empresa() 
	{
	return $this->userconect->get_id_empresa();
	}

	function set_empresa($id_empresa)
	{
	return $this->userconect->set_empresa($id_empresa);
	}

	function set_sucursal($id_sucusal)
	{
	return $this->userconect->set_sucursal($id_sucusal);
	}

	 function get_id_rol()
	{
	return $this->userconect->get_id_rol();
	}
	 function get_user()
	{
	return $this->userconect->get_user();
	}
	 function get_id()
	{
	return $this->userconect->get_id();
	}
	 function get_name()
	{
	return $this->userconect->get_name();
	}

		 function get_id_caja_opera()
	{
	return $this->userconect->get_id_caja_opera();
	}

	  function get_permisos()
	{
	return $this->userconect->get_permisos();
	}

	 function get_profile()
	{
	return $this->userconect->get_profile();
	}
	 function get_thumbs_profile()
	{
	return $this->userconect->get_thumbs_profile();
	}

	function get_id_caja(){
	return $this->userconect->get_id_caja();	
	}
	
	function set_id_caja_opera($id_caja_opera){
		
		$this->userconect->set_id_caja_opera($id_caja_opera);
	}

	function loginOn(){
		$CI =& get_instance();
		$CI->load->library('session');
		$visitante=unserialize($CI->session->visitante);
		$exito=true;
		
		if(!isset($visitante) || $visitante==null)
		{		
		$exito=false;
		}

		if($exito && !$visitante->logueado())
		{		
		$exito=false;
		}
		return $exito;
	}
	

}
?>