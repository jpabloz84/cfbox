<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mproductos extends CI_model{

private $numError=0;
private $descError="";
  function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  function verificar_disponibilidad($id_tipo,$nro_tipo,$id_empresa,$cantidad_solicitada,&$errores=null,&$producto=null){
    
    $exito=false;
    $errores=array();
    
    $cantidad =(int)$cantidad_solicitada;
     $where=array('id_tipo'=>$id_tipo,'nro_tipo'=>$nro_tipo,'id_empresa'=>$id_empresa);
     $this->db->from('verproductos');
     $this->db->where($where);
     $q=$this->db->get();
     $arr=$q->result_array(); 
     if(count($arr)<=0){      
      $errores[]="Un producto solicitado no existe o no esta a la venta";
     }

    $producto=$arr[0];
    if($producto['mueve_stock']==1 && $producto['stock']<$cantidad){
      $errores[]="No hay suficientes cantidades para el producto (".$producto['nro_tipo'].") ".$producto['producto'].": solicitado $cantidad, en stock ".$producto['stock'];
    }
    $exito=(count($errores)==0);
    return  $exito;
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