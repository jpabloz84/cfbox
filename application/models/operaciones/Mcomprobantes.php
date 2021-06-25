<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mcomprobantes extends CI_model{

private $numError=0;
private $descError="";
  function __construct()
  {
    parent::__construct();
    $this->load->database();
  }



  //genera la orden de pago
  function generar_orden($id_pago,$id_pedido,$id_usuario,$id_sucursal,$fecha){
     $this->numError=0;
     $this->descError="";
     $this->load->model("entidades/mproductos");
     $errores=array();
     $id_comp=0;
     $this->begin_trans();
     try {
      
      $this->db->from('vercheckout_presupuesto');
      $where=array('id_pedido' =>$id_pedido);
      $this->db->where($where);
      $q=$this->db->get();
      $rspresupuesto=$q->result_array();
      $presupuesto=$rspresupuesto[0];
      $id_empresa=$presupuesto['id_empresa'];
      $id_cliente=$presupuesto['id_cliente'];
      $importe_total=$presupuesto['importe_total'];
      $this->db->from('verpagos');
      $where=array('id_pago' =>$id_pago);
      $this->db->where($where);
      $q=$this->db->get();
      $rspago=$q->result_array();
      $pago=$rspago[0];
      $id_tipo_pago=$pago['id_tipo_pago'];
      $id_caja_op=$pago['id_caja_op'];
      $this->db->from('vercheckout_detalle');
      $where=array('id_pedido' =>$id_pedido);
      $this->db->where($where);
      $q=$this->db->get();
      $rsdetalle=$q->result_array();
      $comp=array();
      $comp['id_cliente']=$id_cliente;
      $comp['importe_total']=$importe_total;
      $comp['importe_neto']=$importe_total;
      $comp['importe_base']=$importe_total;
      $comp['importe_iva']=0;
      $comp['importe_descuento']=0;
      $comp['fe_creacion']=$fecha;
      $comp['cuit']='99999999999';
      $comp['estado']='E';    
      $comp['fe_estado']=$fecha;
      $comp['id_usuario']=$id_usuario;
      $comp['id_usuario_estado']=$id_usuario;
      $comp['afip']=0;      
      $comp['id_sucursal']=$id_sucursal;
      
      //$comp['id_pedido']=$id_pedido;
      $this->db->set($comp);
      $this->db->insert("comp");
      $error=$this->db->error();
      if (isset($error['code']) && $error['code']!=0){
        $this->numError=3;
        $this->descError='Error de base de datos al insertar en comp:'.$error['message'];     
        goto salir;
      }else{
      $id_comp=$this->db->insert_id();   
      }

      foreach ($rsdetalle as $itemcheckout) {
      $comp_det=array();
      $id_tipo=$itemcheckout['id_tipo'];
      $nro_tipo=$itemcheckout['nro_tipo'];
      $cantidad_solicitada=$itemcheckout['cantidad'];
      $producto=null;
      $disponible=$this->mproductos->verificar_disponibilidad($id_tipo,$nro_tipo,$id_empresa,$cantidad_solicitada,$errores,$producto);
      if(!$disponible){
        $this->numError=1;
        $this->descError=$errores[0];
        goto salir;
      }
      $comp_det['id_comp']=$id_comp;
      $comp_det['nro_item']=$itemcheckout['nro_detalle'];
      $comp_det['id_tipo']=$id_tipo;
      $comp_det['nro_tipo']=$nro_tipo;
      $comp_det['precio_base']=$itemcheckout['importe_unitario'];
      $comp_det['precio_iva']=0;
      $comp_det['precio_venta']=$itemcheckout['importe_unitario'];
      $comp_det['detalle']=$producto['producto'];
      $comp_det['cantidad']=$cantidad_solicitada;
      $comp_det['importe_descuento']=0;
      $comp_det['iva']=0;
      $this->db->set($comp_det);
      $this->db->insert("comp_det");
      $error=$this->db->error();
      if (isset($error['code']) && $error['code']!=0){
        $this->numError=4;
        $this->descError='Error de base de datos al insertar en comp_det:'.$error['message'];     
        goto salir;
      }
      if($id_tipo==7){
      $this->asignarcartones($nro_tipo,$id_cliente,$id_comp,$cantidad_solicitada);  
        if($this->numError!=0){
          goto salir;
        }
      }
      

    }
   $comp_pagos=array();
   $comp_pagos['id_comp']=$id_comp;
   $comp_pagos['id_pago']=$id_pago;
   $comp_pagos['monto']=$pago['pago'];
   $comp_pagos['fecha']=$fecha;
   $comp_pagos['id_usuario']=$id_usuario;
    $this->db->set($comp_pagos);
    $this->db->insert("comp_pagos");
    $error=$this->db->error();
    if (isset($error['code']) && $error['code']!=0){
      $this->numError=4;
      $this->descError='Error de base de datos al insertar en comp_pagos:'.$error['message'];     
      goto salir;
    }

   $cmp_presupuesto=array();
   $cmp_presupuesto['id_comp']=$id_comp;
   $cmp_presupuesto['fe_pagado']=$fecha;
   $cmp_presupuesto['pagado']=1;
   $this->db->set($cmp_presupuesto);
   $where=array('id_pedido' =>$id_pedido);
   $this->db->where($where);
   $this->db->update("checkout_presupuesto");
   $error=$this->db->error();
   if (isset($error['code']) && $error['code']!=0){
     $this->numError=4;
     $this->descError='Error de base de datos al actualizar presupuesto:'.$error['message'];     
      goto salir;
    }
   
     
   } catch (Exception $e) {
     $this->numError=5;
     $this->descError='Error script:'.$e->getMessage();  
   }
    
    salir:
    if($this->numError!=0){
       $this->rollback_trans();
       $id_comp=0;
    }else{
       $this->commit_trans();
    }
    
    return  $id_comp;
  }

function get_detalle_recibo($id_comp){
  $cad="";
  $this->numError=0;
  $this->descError="";        
  $q=$this->db->query("SELECT  importe_item,cantidad,detalle FROM vercomprobantes_det where id_comp=$id_comp order by nro_item");
  $rs=$q->result_array();
  foreach ($rs as  $fila) {
      $importe_item=$fila['importe_item'];
      $cantidad=$fila['cantidad'];
      $detalle=$fila['detalle'];
      if($cad==""){
      $cad.="$detalle (X $cantidad) por $".$importe_item;  
    }else{
      $cad.=", $detalle (X $cantidad) por $".$importe_item;  
    }
  }
  return $cad;
}//get_detalle_recibo


  function getestado_inicial($id_tipo_pago){

      $query=$this->db->query("select estado_inicial from tipo_pagos where id_tipo_pago=$id_tipo_pago ");         
       $rs=$query->result_array();
       return $rs[0]['estado_inicial'];
  }

   function numError()
   {
    return $this->numError;
   }

  function descError()
  {
    return $this->descError;
  }
  function begin_trans()
      { $this->load->database();
        $this->db->trans_begin();
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {                
                $this->numError=$error["code"];
                $this->descError=$error['message']; 
            }
      }

      function commit_trans()
      {
            
             if ($this->db->trans_status() === FALSE)
                {
                $error=$this->db->error();
                $this->numError=$error['code'];
                $this->descError=$error['message'];
                $this->db->trans_rollback();
                }else{
                    $this->db->trans_commit();
                } 
            
      }

      function rollback_trans()
      {
            $this->db->trans_rollback();
            $error=$this->db->error();
            if (isset($error['code']) && $error['code']>0) {
                
                $this->numError=$error["code"];
                $this->descError=$error['message']; 
                
            }
      }

      function obtenerbingosdesdecomprobante($id_comp){
       $query=$this->db->query("SELECT c.`id_comp`,c.`nro_tipo` AS id_bingo,b.* FROM comp_det c 
    JOIN bingos b ON c.`nro_tipo`=b.`id_bingo`
    WHERE c.id_tipo=7 AND c.`id_comp`=$id_comp ORDER BY b.`fecha_juego`");
       $rsbingos=$query->result_array();
       return $rsbingos;
    }

}

?>