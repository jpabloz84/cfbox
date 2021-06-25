<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mpermisos extends CI_model{
private $id;

	function __construct()
	{
		parent::__construct();
	}

	function alta($id_usuario,$nombre,$url,$usuario,$clave,$observaciones,&$exito)
	{ $this->load->database();
		$id_cuenta=0;
		$exito=false;
			/*$query=$this->db->query("insert into (nombre,url,usuario,clave,observaciones,fecha_modificacion,id_usuario) values('$nombre','$url','$clave','$observaciones',now(),$id_usuario)");*/
	 	$data = array('nombre' => $nombre,'url' => $url, 'usuario' => $usuario, 'clave' => $clave, 'fecha_modificacion' =>'now()' , 'observaciones' => $observaciones);
	 
	 		$this->db->insert('cuentas',$data);	
			$id_cuenta=$this->db->insert_id();
			if($this->db->affected_rows()>0)
			{
				$exito=true;
			}
			
			return $id_cuenta;
	  }

  	function alta_extra($id_cuenta,$nombre,$valor,&$exito)
	{ $this->load->database();
		$id_dato=0;
		$exito=false;
			/*$query=$this->db->query("insert into (nombre,url,usuario,clave,observaciones,fecha_modificacion,id_usuario) values('$nombre','$url','$clave','$observaciones',now(),$id_usuario)");*/
	 	$data = array('nombre' => $nombre,'valor' => $valor, 'id_cuenta' => $id_cuenta);
	 
	 		$this->db->insert('datos_extras',$data);	
			$id_dato=$this->db->insert_id();
			if($this->db->affected_rows()>0)
			{
				$exito=true;
			}
			
		return $id_dato;
	  }

	  function obtenerCuentasUser($id_usuario,&$numrows=0)
	  {
	  	$this->load->database();
	  	$rows=array();
		$query=$this->db->get_where('cuentas', array('id_usuario' => $id_usuario));
		$rows=$query->result_array();
		$numrows=$query->num_rows();
		
			return $rows;	
	  }

	  function allRoles($id_rol=1)
	  {
	  	 	$this->load->database();
	  		$rows=array();
	  		$query=null;
	  		//si el rol es sistema o administrador
	  		if($id_rol<3 )
	  		{
	  			$query=$this->db->get_where('rol','id_rol>='.$id_rol);
	  		}
			
			$rows=$query->result_array();		
			return $rows;	
	  }

	  function sucursales($id_usuario)
	  {
	  	 	$this->load->database();
	  		$rows=array();
	  		$query=null;
	  		//si el rol es sistema accede a cualquier sucursal
	  		$qu=$this->db->get_where('usuario','id_usuario='.$id_usuario);
	  		$ru=$qu->result_array();	
	  		$id_rol=$ru[0]['id_rol'];
	  		$id_sucursal=$ru[0]['id_sucursal'];
	  		$query=null;
	  		if($id_rol==1 )
	  		{
	  			$query=$this->db->query("SELECT * from versucursales ORDER BY empresa , sucursal");
	  		}else{
	  			$query=$this->db->query("SELECT * FROM versucursales WHERE id_empresa in(select id_empresa from sucursal where id_sucursal=$id_sucursal) ORDER BY empresa, sucursal");						
	  		}
	  		$rows=$query->result_array();		
			return $rows;	
	  }

	  function getpermisos($id_rol)
	  {
	  	 	$this->load->database();
	  		$rows=array();
	  		$query=$this->db->query("SELECT * FROM verrol_permisos WHERE id_rol=$id_rol ORDER BY nro_permiso_modulo, nro_permiso");			
			$rows=$query->result_array();		
			return $rows;	
	  }

	  function rolmodulo_update($id_rol,$nro_permiso_modulo,$suma_logica)
	  {
	  		$exito=false;
	  		$this->load->database();
	  		$rows=array();
	  		
	  		//$stmt="update rol_modulo set permiso=$suma_logica where id_rol=$id_rol and nro_permiso_modulo=$nro_permiso_modulo";
	  		//print_r($stmt);
	  		$data=array();
	  		$data['permiso']=$suma_logica;
	  		$where= array('id_rol' =>$id_rol , 'nro_permiso_modulo' =>$nro_permiso_modulo);	  		
			$query=$this->db->update('rol_modulo', $data,$where);
	  		//$query=$this->db->query($stmt);
	  		$af=$this->db->affected_rows();
	  		//echo "aca ".$af;
			if($query)
			{
				$exito=true;
			}
			return $exito;
	  }

	  function rolpermiso_clear($nro_permiso_modulo)
	  {
	  		$exito=false;
	  		$this->load->database();
	  		$rows=array();
	  		$query=$this->db->query("delete from rol_permiso where nro_permiso_modulo=$nro_permiso_modulo");			
			if($query)
			{
				$exito=true;
			}
			return $exito;
	  }

	  function rolpermiso_add($nro_permiso,$nro_permiso_modulo,$permiso_desc)
	  {
	  		$exito=false;
	  		$this->load->database();
	  		$data = array('nro_permiso' => $nro_permiso ,'nro_permiso_modulo' => $nro_permiso_modulo ,'permiso_desc' => $permiso_desc);
			$query=$this->db->insert('rol_permiso', $data); 

	  		
			//if($this->db->affected_rows()>0)
			if($query)
			{
				$exito=true;
			}
			return $exito;
	  }

}

?>
