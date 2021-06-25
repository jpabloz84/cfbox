<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mcaja extends CI_model{
private $numError=0;
private $descError="";

	function __construct()
	{
		$this->load->database();
		parent::__construct();
	}

	function inicializarCaja($id_caja,$id_usuario,&$id_caja_op)
	{ 
		$valor=null;
	  $rows=array();
	  $exito=false;
	  $id_caja_op_ant=0;
	  $montoini=0;
    $this->numError=0;
    $this->descError="";
	  $this->db->trans_begin();
    $error=$this->db->error();
    if (isset($error['code']) && $error['code']>0) {                
        $this->numError=$error["code"];
        $this->descError=$error['message']; 

        return $exito;
    }

    try {
    	//si hay registro aqui es porque la caja para el dia está creada
			$q=$this->db->query("select * from cajas_operaciones where id_caja=$id_caja and DATE(fe_inicio)=DATE(NOW())");
			$qty=(int)$q->num_rows();
			if($qty>0){
				$rs=$q->result_array();
				$id_caja_op=$rs[0]['id_caja_op'];
				//echo 'test1';
				return true;
			}
			//echo 'test2'.$id_caja;
			//tomo la ultima caja que se abrio
			$q=$this->db->query("select * from cajas_operaciones where id_caja=$id_caja and fe_inicio<NOW() order by fe_inicio desc");
			$rs=$q->result_array();
			$qty=(int)$q->num_rows();
			//busco la ultima caja
			if($qty>0){
				//echo 'test3';
				$id_caja_op_ant=$rs[0]['id_caja_op'];
				$q=$this->db->query("SELECT SUM(CASE WHEN movimiento='E' THEN monto*-1 ELSE monto END) AS total FROM vercaja_movimientos WHERE id_caja_op=$id_caja_op_ant ");
				$rs1=$q->result_array();
				$qty1=(int)$q->num_rows();
				if($qty1>0){
					$montoini=(float)$rs1[0]['total']; //el monto inicio, va a ser el monto final de la ultima caja abierta
				}
			}
			//echo 'test4'.$id_caja_op;
			$this->db->query("update cajas_operaciones set fe_fin=now(),monto_fin=$montoini,id_usuario_fin=$id_usuario where id_caja_op=$id_caja_op_ant");

			$this->db->query("insert into cajas_operaciones (id_caja,fe_inicio,monto_ini,id_usuario_ini) values($id_caja,NOW(),$montoini,$id_usuario)");
			 $id_caja_op=$this->db->insert_id();
			 //echo 'test5';
			 if($this->db->trans_status() === FALSE)
	    {
	    $error=$this->db->error();
	    $this->numError=$error['code'];
	    $this->descError=$error['message'];
	    /*print_r($error);
	    echo 'test6';*/
	    $this->db->trans_rollback();
	    }else{
	    	//echo 'test7';
	        $this->db->trans_commit();
	        $exito=true;
	    } 
    	
    } catch (Exception $e) {
    	$this->numError=1;
	    $this->descError=$e->getMessage();
    	$this->db->trans_rollback();
    }

		return $exito;	
	 }

	   function numError()
	 {
		return $this->numError;
	 }

	function descError()
	{
		return $this->descError;
	}
	function get_saldo($id_caja_op){
		$q=$this->db->query("SELECT SUM(CASE WHEN movimiento='E' THEN monto*-1 ELSE monto END) AS total FROM vercaja_movimientos WHERE id_caja_op=$id_caja_op");

		$rs=$q->result_array();
		$total=(float)$rs[0]['total'];
		return $total;
	}

	function get_recaudacion($id_caja_op){
		$q=$this->db->query("SELECT SUM(CASE WHEN movimiento='E' THEN monto*-1 ELSE monto END) AS total FROM vercaja_movimientos WHERE id_caja_op=$id_caja_op and origen='pagos'");

		$rs=$q->result_array();
		$total=(float)$rs[0]['total'];
		return $total;
	}


	function get_comp_deben($id_empresa){
		//$q=$this->db->query("SELECT IFNULL(SUM(debe),0) as deben FROM vercomp_pagados");
		$stmt="SELECT SUM(cliente_deuda(c.`id_cliente`)) AS deben  FROM clientes c JOIN personas p ON c.`id_persona`=p.`id_persona` WHERE c.id_empresa=$id_empresa	AND (p.`cf`<>1 OR p.cf IS NULL) AND c.`habilitado`=1 AND cliente_deuda(c.`id_cliente`)>0";
		$q=$this->db->query($stmt);
		$rs=$q->result_array();
		$total=0;
		if(count($rs)>0){
		$total=(float)$rs[0]['deben'];	
		}
		
		return $total;
		
	}

}

?>
