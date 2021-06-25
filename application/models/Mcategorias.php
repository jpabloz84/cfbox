<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mcategorias extends CI_model{
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




}

?>
