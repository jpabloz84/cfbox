<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Muser extends CI_model{
private $id;
private $usuario;
private $clave;
private $apenom;
private $id_rol;
private $rol;
private $id_empresa="";
private $id_sucursal="";
private $id_caja_opera=0;
private $conectado;
private $profile=BASE_FW.'assets/img/default-large.jpg';
private $thumbs_profile=BASE_FW.'assets/img/default-large.jpg';
private $permisos=array();
function __construct()
{
	parent::__construct();
}

function conectarse($user,$pass,$id_sucursal=0)
{ $this->load->database();
		$exito=false;
		$query=$this->db->query("SELECT u.id_usuario,u.usuario,u.clave,CONCAT(e.apellido,', ',e.nombres) as apenom,r.id_rol,r.rol,u.id_sucursal,e.img_profile,em.id_empresa FROM usuario u JOIN rol r ON u.id_rol=r.id_rol   JOIN personas e ON u.id_persona=e.id_persona 
		LEFT OUTER JOIN sucursal su on  su.id_sucursal=u.id_sucursal 
		LEFT OUTER JOIN empresa em on su.id_empresa=em.id_empresa  
  WHERE u.usuario='$user' AND  u.clave='$pass' AND u.habilitado=TRUE");
		if($query->num_rows()>0)
		{
			$exito=true;
			$row= $query->row();
			//print_r($row);
			$this->id = $row->id_usuario;
			$this->usuario = $row->usuario;
			$this->id_sucursal =($row->id_sucursal==null || $row->id_sucursal=="")?"":$row->id_sucursal;
			$this->id_empresa = $row->id_empresa; //si es usuario de rol sistemas, que no tenga empresa
			if($id_sucursal>0 && $row->id_rol==1){
				$q2=$this->db->query("select * from versucursales where id_sucursal=$id_sucursal");
				$row2= $q2->row();
				$this->id_sucursal=$id_sucursal;
				$this->id_empresa=$row2->id_empresa;
			}
			$this->apenom=$row->apenom;
			$this->clave=$row->clave;
			$this->id_rol=$row->id_rol;
			if(trim($row->img_profile)!='' && $row->img_profile!=null){
				$this->profile=base_url().trim($row->img_profile);
				$this->thumbs_profile=str_replace("img","thumbs", $this->profile);
			}
			$this->rol=$row->rol;			
			$this->conectado=true;	
		}

		if($exito)
			{
				$rs=$this->db->query("SELECT DISTINCT nro_permiso_modulo,permiso,permiso_modulo FROM verrol_permisos WHERE id_rol=$this->id_rol ORDER BY nro_permiso_modulo");

				foreach ($rs->result() as $row)
				{
        		
        		$nro_permiso = $row->permiso;				
				$this->permisos[$row->permiso_modulo]=$nro_permiso;				
				}
				$rs->free_result();
				
			
			}
		return $exito;
	}


	  function logueado()
	{
		return $this->conectado;
	}

	  function habilitado($hash = '')
	{ $exito=false;
		$stmt="SELECT u.id_usuario FROM usuario u WHERE u.id_usuario=".$this->id." AND  u.habilitado=TRUE";
		$this->load->database();
		if($hash!='')
		{
		$stmt="SELECT u.id_usuario FROM usuario u WHERE u.hash=".$hash." AND  u.habilitado=TRUE";
		}
			$rs=$this->db->query($stmt);
			if($rs->num_rows()>0)
			{
				$exito=true;
			}
			$rs->free_result();
		

		return $exito;
	}
	 function set_empresa($id_empresa) 
	{
	return $this->id_empresa=$id_empresa;
	}

	function set_sucursal($id_sucursal) 
	{
	return $this->id_sucursal=$id_sucursal;
	}

	 function get_rol() 
	{
	return $this->rol;
	}


	 function get_id_rol()
	{
	return $this->id_rol;
	}
	 function get_profile()
	{
	return $this->profile;
	}
	 function get_thumbs_profile()
	{
	return $this->thumbs_profile;
	}
	 function get_user()
	{
	return $this->usuario;
	}
	 function get_id()
	{
	return $this->id;
	}
	 function get_name()
	{
	return $this->apenom;
	}


	function get_id_sucursal()
	{
	return $this->id_sucursal;
	}

	function get_id_empresa()
	{
	return $this->id_empresa;
	}
	function set_id_caja_opera($id_caja_opera){
		
		$this->id_caja_opera=$id_caja_opera;
	}
	function get_id_caja_opera()
	{
	return $this->id_caja_opera;
	}

	function get_id_caja()
	{$id_caja=0;
		$this->load->database();		
		if($this->id_sucursal!=null){
			$stmt="SELECT * FROM cajas  WHERE id_sucursal=".$this->id_sucursal;
			$q=$this->db->query($stmt);
			if($q->num_rows()>0)
			{
				$rs=$q->result_array();
				$id_caja=$rs[0]['id_caja'];
			}
			$q->free_result();
		}

	return $id_caja;
	}
	  function get_permisos()
	{
	return $this->permisos;
	}

}

?>
