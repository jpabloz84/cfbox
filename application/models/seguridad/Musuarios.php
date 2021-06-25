<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Musuarios extends CI_model{
private $id;
private $numError=0;
private $descError="";
	function __construct()
	{
		parent::__construct();
	}

	function agregar($usuario,$clave,$id_rol,$id_persona,$habilitado,$id_sucursal="")
	{ $this->load->database();
		
		$this->numError=0;
	  	$this->descError="";
	  	$id_usuario=0;
		try {
			$data = array('usuario' => $usuario,'clave' => $clave, 'id_rol' => $id_rol, 'id_persona' => $id_persona, 'habilitado' =>$habilitado);
			if($id_sucursal!=""){
				$data['id_sucursal']=$id_sucursal;
			}
	 
	 		$this->db->insert('usuario',$data);	
			$id_usuario=$this->db->insert_id();
			if(!$this->db->affected_rows()>0)
			{
			$this->numError=2;
	  	    $this->descError="no se pudo insertar usuario";
			}	
		} catch (Exception $e) {
			$this->numError=1;
	  	    $this->descError=$e->getMessage();
		}
			
	 	
			
			return $id_usuario;
	  }

  	
	  function actualizar($id_usuario,$campos)
	  {
	  		$exito=false;
	  		$this->numError=0;
	  		$this->descError="";
	  		try {
	  			$this->load->database();
		  		$rows=array();
		  		if (!$id_usuario>0)
		  		{
		  			$this->numError=3;
	  		        $this->descError="no ingreso usuario";
		  			return $exito;
		  		}
		  		//$data=array('apellido' => $apellido,'nombres' => $nombres, 'tipo_docu' => $tipo_docu, 'nro_docu' => $nro_docu, 'sexo' =>$sexo , 'id_loc' => $id_loc,'telefono'=>$telefono,'domicilio'=>$domicilio, 'img_profile'=>$path_img,'email'=>$email);	  		
				
		  		$where= array('id_usuario' => $id_usuario);	  		
				$query=$this->db->update('usuario', $campos,$where);
		  		
				if($query)
				{
					$exito=true;
				}else
				{
					$this->numError=2;
	  		        $this->descError="error al actualizar";
				}
	  			
	  		} catch (Exception $e) {
	  		$this->numError=1;
	  		$this->descError=$e->getMessage();
	  		}
	  		
			return $exito;
	  }

	function obtenerUsuarios(&$numrows=0,$id_rol=0)
  	{
  	$this->load->database();
  	$rows=array();
  	$query=null;
  	if ($id_rol==1) {
  	$query=$this->db->get('verusuarios');
  	}else
  	{
  	$query=$this->db->get_where('verusuarios','id_rol<>1');
  	}
	
	$rows=$query->result_array();
	$numrows=$query->num_rows();
		return $rows;	
  	}

  	function eliminar($id_usuario)
  	{
  	$this->load->database();
  	$rows=array();  	
  	$id_persona=0;
  	$id_cliente=0;
  	$eliminarpersona=true;

  	//adminsitrador o agente web
			if($id_usuario==1 || $id_usuario==27){
				$this->numError=1;
					$this->descError="no se permite eliminar este usuario";
					return false;
			}

  	$query=$this->db->query("select  * from usuario where id_usuario=$id_usuario");
		$rows=$query->result_array();
		$numrows=$query->num_rows();
			if($numrows>0){
				$id_persona=(int)$rows[0]['id_persona'];
				$query=$this->db->get_where('clientes',"id_persona=$id_persona");
				$rp=$query->result_array();
				if(count($rp)>0){
					$id_cliente=(int)$rp[0]['id_cliente'];
				}
			}
			//consumidor final
			if($id_cliente==3){
				$this->numError=1;
					$this->descError="no se permite eliminar este cliente";
					return false;
			}

			if($id_cliente>0){
				//checkeo si hay pagos asociados al cliente
				$q=$this->db->get_where('pagos',"id_cliente=$id_cliente");
				$rs=$q->result_array();
				if(count($rs)>0){
					$this->numError=1;
					$this->descError="hay pagos asociados a este cliente";
					return false;
				}
				//checkeo si hay comprobantes asociados al cliente
				$q=$this->db->get_where('comp',"id_cliente=$id_cliente or id_cliente_representante=$id_cliente");
				$rs=$q->result_array();
				if(count($rs)>0){
					$this->numError=1;
					$this->descError="hay comprobantes asociados a este cliente";
					return false;
				}

				//checkeo si hay cartones asociados al cliente
				$q=$this->db->get_where('cartones',"id_cliente=$id_cliente");
				$rs=$q->result_array();
				if(count($rs)>0){
					$this->numError=1;
					$this->descError="hay cartones asociados a este cliente";
					return false;
				}

				//checkeo si hay premios asociados al cliente
				$q=$this->db->get_where('premios',"id_cliente=$id_cliente");
				$rs=$q->result_array();
				if(count($rs)>0){
					$this->numError=1;
					$this->descError="hay premios asociados a este cliente";
					return false;
				}


			}


			//checkeo si hay pagos asociados al usuario
			$q=$this->db->get_where('pagos',"id_usuario=$id_usuario or id_usuario_estado=$id_usuario");
			$rs=$q->result_array();
			if(count($rs)>0){
				$this->numError=1;
				$this->descError="hay pagos modificados o agregados a este usuario";
				return false;
			}
			//checkeo si hay comprobantes asociados al usuario
			$q=$this->db->get_where('comp',"id_usuario=$id_usuario or id_usuario_estado=$id_usuario");
			$rs=$q->result_array();
			if(count($rs)>0){
				$this->numError=1;
				$this->descError="hay comprobantes asociados a este usuario";
				return false;
			}

			//checkeo si hay premios asociados al cliente
			$q=$this->db->get_where('premios',"id_usuario_entrega=$id_usuario");
			$rs=$q->result_array();
			if(count($rs)>0){
				$this->numError=1;
				$this->descError="hay premios entregados por este usuario";
				return false;
			}

			//checkeo si hay sorteos asociados al usuario
			$q=$this->db->get_where('bingo_sorteo',"id_usuario=$id_usuario");
			$rs=$q->result_array();
			if(count($rs)>0){
				$this->numError=1;
				$this->descError="hay sorteos asociados a este usuario";
				return false;
			}

			//checkeo si hay bingos asociados al usuario
			$q=$this->db->get_where('bingos',"id_operador_estado=$id_usuario");
			$rs=$q->result_array();
			if(count($rs)>0){
				$this->numError=1;
				$this->descError="hay bingos manipulados a este usuario";
				return false;
			}

			//checkeo si hay clientes de esta misma persona , asociado la misma persona
			$q=$this->db->get_where('clientes',"id_persona=$id_persona and id_cliente<>$id_cliente");
			$rs=$q->result_array();
			if(count($rs)>0){
				$eliminarpersona=false;
			}
			if($id_cliente>0){
				$this->db->query("delete from checkout_detalle	where id_pedido in(select id_pedido from checkout_presupuesto where id_cliente=$id_cliente)");
				$this->db->query("delete from checkout_presupuesto	where id_cliente=$id_cliente");
				$this->db->query("delete from clientes	where id_cliente=$id_cliente");
				$error=$this->db->error();
	      if (isset($error['code']) && $error['code']>0) {            	
	      	$this->numError=$error["code"];
	        $this->descError=$error['message'];
	        return false;	
	      }
			}
				
				if($id_usuario>0){
					$this->db->query("delete from log_login	where id_usuario=$id_usuario");
					$error=$this->db->error();
					if (isset($error['code']) && $error['code']>0) {            	
	      	$this->numError=$error["code"];
	        $this->descError=$error['message'];
	        return false;	
	      	}
	      	$this->db->query("delete from usuario_parametro	where id_usuario=$id_usuario");
	      	$error=$this->db->error();
					if (isset($error['code']) && $error['code']>0) {            	
	      	$this->numError=$error["code"];
	        $this->descError=$error['message'];
	        return false;	
	      	}

	      	$this->db->query("delete from usuario	where id_usuario=$id_usuario");
	      	$error=$this->db->error();
					if (isset($error['code']) && $error['code']>0) {            	
	      	$this->numError=$error["code"];
	        $this->descError=$error['message'];
	        return false;	
	      	}
				}

				if($eliminarpersona && $id_persona>0){
					$this->db->query("delete from personas	where id_persona=$id_persona");
					$error=$this->db->error();
					if (isset($error['code']) && $error['code']>0) {            	
	      	$this->numError=$error["code"];
	        $this->descError="id_persona $id_persona:".$error['message'];
	        return false;	
	      	}
				}
			
		return true;	
  	}

	  

      function numError()
	 {
		return $this->numError;
	 }

	function descError()
	{
		return $this->descError;
	}	  




}

?>
