<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mclientes extends CI_model{
private $id;
private $numError=0;
private $descError="";
	function __construct()
	{
		parent::__construct();
	}

	function agregar($id_cond_iva,$observaciones,$id_persona,$id_cliente_representa=0,$habilitado=1,$id_usuario,$id_empresa)
	{ $this->load->database();		
		$this->numError=0;
	  	$this->descError="";
	  	$id_cliente=0;
      $str_id_cliente_representa=($id_cliente_representa>0)?$id_cliente_representa:"NULL";
		try {
			
	 		$this->db->query("INSERT INTO clientes (id_persona,id_cond_iva,observaciones,fe_alta,id_cliente_representante,habilitado,id_usuario_estado,fe_estado,id_empresa) values 
	 			                                       ($id_persona,$id_cond_iva,'$observaciones',now(),$str_id_cliente_representa,$habilitado,$id_usuario,now(),$id_empresa)");
	 		$error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {            	
            	$this->numError=$error["code"];
                $this->descError=$error['message'];	
            }else
            {
            $id_cliente=$this->db->insert_id();	
            }
			
			
		} catch (Exception $e) {
			$this->numError=1;
	  	    $this->descError=$e->getMessage();
		}			
			return $id_cliente;
	  }

	
	  function actualizar($id_cliente,$campos)
	  {
	  		$exito=false;
	  		$this->numError=0;
	  		$this->descError="";
	  		try {
	  			$this->load->database();
	  			$habilitado=$campos['habilitado'];
		  		$rows=array();
		  		$query=$this->db->query("select * from clientes where id_cliente=$id_cliente");         
             	$rows=$query->result_array();
             	
             	 if($campos['id_empresa']!=$rows[0]['id_empresa']){
             	 	$this->numError=4;
	  		        $this->descError="No tiene permitido cambiar datos de un cliente ajeno";
		  			return $exito;	
             	 }
		  		if (!$id_cliente>0)
		  		{
		  			$this->numError=3;
	  		        $this->descError="no ingreso ID";
		  			return $exito;
		  		}
		  		
		  		 
             	 //si el estado viejo coincide con el actual, no hay que actualizar nada, sino pongo los datos del usuario
             	 if($rows[0]['habilitado']==$campos['habilitado']){
             	 	unset($campos['habilitado']);
             	 	unset($campos['id_usuario_estado']);
             	 }else{
             	 	$campos['fe_estado']=date("Y-m-d H:i:s");
             	 }
		  		$where= array('id_cliente' => $id_cliente);	  		
				$query=$this->db->update('clientes', $campos,$where);
		  		
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
	  /*function obtenersaldo_afavor(){
	  		$numError=0;
        $descError="";
        $this->load->database();
        $rows=array();
         try{
            $query=$this->db->query($stmt);         
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {
                
                $this->numError=$error["code"];
                $this->descError=$error['message']; 
                
            }else
            {
             $rows=$query->result_array();                       
            }
            
        } 
        catch (Exception $e) {
        $this->numError=1;
        $this->descError=$e->getMessage();
         }
        return $rows;
	  }*/

	  function obtenertotales_facturados($id_cliente){
	  	
	  	$this->numError=0;
      $this->descError="";
      $this->load->database();
      $rows=array();
	     try{
	        /*$stmt="SELECT SUM(v.importe_total) AS importe_total,SUM(v.importe_iva) AS importe_iva,SUM(v.importe_neto) AS importe_neto,SUM(v.importe_base) AS importe_base,SUM(v.importe_pago) AS importe_pago  FROM vercomprobantes v  WHERE v.id_cliente=$id_cliente AND ((v.afip=0 AND v.estado='E') OR (v.estado IN('E','F') AND v.id_tipo_comp IN(1,2,5)))";*/
          $stmt="select SUM(v.importe_total) AS importe_total,SUM(v.importe_iva) AS importe_iva,SUM(v.importe_neto) AS importe_neto,SUM(v.importe_base) AS importe_base,SUM(v.pagado) AS importe_pago,cliente_deuda(v.id_cliente)
 as deuda from vercomp_pagados v  WHERE v.id_cliente=".$id_cliente;
    
	        $query=$this->db->query($stmt);         
	        $error=$this->db->error();
	        if (isset($error['code']) && $error['code']>0) {                
	            $this->numError=$error["code"];
	            $this->descError=$error['message'];                 
	        }else
	        {
	         $rows=$query->result_array();                       
	        }	        
	    } 
      catch (Exception $e) {
      $this->numError=1;
      $this->descError=$e->getMessage();
       }
       return $rows;
	  }//obtener_total_facturados
	  //adeudado por clientes que no sean genericos
	  function obtenertotal_adeudado($id_cliente){
	  	$this->numError=0;
      $this->descError="";
      $this->load->database();
      $adeudado=0;
      $rows=array();
	     try{
	     	//se define como lo adeudado, aquellas facturas/ordenes de pedido cuyo importe total no se haya pagado y esten en su estado EMITIDO o FACTURADO
	        /*$stmt="SELECT if(SUM(v.importe_total)-SUM(v.importe_pago)<0,0,SUM(v.importe_total)-SUM(v.importe_pago)) AS adeudado  FROM vercomprobantes v  WHERE v.id_cliente=$id_cliente 
  AND  ((v.afip=0 AND v.estado='E') OR (v.estado IN('E','F') AND v.id_tipo_comp IN(1,2,5)))";*/
  			$stmt="select cliente_deuda(id_cliente) as adeudado from verclientes where id_cliente=$id_cliente and cf<>1";
	        $query=$this->db->query($stmt);         
	        $error=$this->db->error();
	        if (isset($error['code']) && $error['code']>0) {                
	            $this->numError=$error["code"];
	            $this->descError=$error['message'];                 
	        }else
	        {
	         $rows=$query->result_array();                       
	         if(count($rows)>0){
	         $adeudado=(float)$rows[0]['adeudado'];
	         }
	        }	        
	    } 
      catch (Exception $e) {
      $this->numError=1;
      $this->descError=$e->getMessage();
       }
       return $adeudado;	  	
	  }//total_adeudado

	  function saldo_afavor($id_cliente){
	  	$saldo_afavor=0;
	  	$stmt="select cliente_deuda(id_cliente) as adeudado from verclientes where id_cliente=$id_cliente and cf<>1";
	        $query=$this->db->query($stmt);         
	        $error=$this->db->error();
	        if (isset($error['code']) && $error['code']>0) {                
	            $this->numError=$error["code"];
	            $this->descError=$error['message'];                 
	        }else
	        {
	         $rows=$query->result_array();                       
	         if(count($rows)>0){
	         $adeudado=(float)$rows[0]['adeudado'];
		         if($adeudado<0){
		         $saldo_afavor=$saldo_afavor*-1;	
		         }
	         }
	     }
	  	return $saldo_afavor;
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
