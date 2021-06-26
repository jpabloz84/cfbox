<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mpedidos extends CI_model{
private $id;
private $numError=0;
private $descError="";
public $visitante=null;
function __construct()
{
  parent::__construct();
  
}


public function estados()
{$this->load->database();  
  $query = $this->db->query("select * from estado where presupuesto=1 order by orden asc");
  return $query->result_array();
}


function numError(){
  return $this->numError;
}

function descError(){
  return $this->descError;
}
//defino las consultas del front
function definiciones($id_empresa=0){
$definiciones=array();
$definiciones['selpedidos']="SELECT *,date_format(fecha,'%H:%i') as horario,DiaW(fecha,'es_ES') as diastr,Mes(fecha,'es_ES') as messtr,date_format(fecha,'%d') as dia,date_format(fecha,'%d-%m-%Y %H:%i') as fechastr,date_format(fecha,'%Y') as anio,path_comp FROM vercheckout_presupuesto WHERE id_empresa=$id_empresa {nombres} {fecha} {pedido} {estado} order by fecha desc";
$definiciones['selpedidosproductos']="SELECT * FROM vercheckout_detalle WHERE id_empresa=$id_empresa {idpedido} order by nro_detalle desc";

return $definiciones;
}


public function cambiarestado($id_pedido,$estado,$id_empresa)
{ 
$this->load->database();
  $exito=false;
  $query = $this->db->query("SELECT * FROM checkout_presupuesto WHERE id_pedido=$id_pedido AND id_empresa=$id_empresa");
  $row=$query->result_array();
  if(count($row)==0){
    $this->numError=1;
    $this->descError="no se encontro el pedido";
    return false;
  }
  $strcomp=($estado=='S' or $estado=='P')?",id_comp=null":""; //si los estados cambian a recibido o pendiente, se setea el comprobante

  $this->db->query("update checkout_presupuesto set estado='$estado' $strcomp where id_pedido=$id_pedido");
   $error=$this->db->error();
    if (isset($error['code']) && $error['code']!=0) {                
    $this->numError=$error["code"];
    $this->descError=$error['message'];    
    }else{
      $exito=true;
    }
  return $exito;

}//cambiarestado
//asocio el pedido al comprobante
public function facturar_pedido($id_pedido,$id_comp,$id_empresa){
  //no hace load db aca porq se usa para consulta transaccional
 //$ex=$this->cambiarestado($id_pedido,"S",$id_empresa);
  $exito=false;
  $query = $this->db->query("SELECT * FROM checkout_presupuesto WHERE id_pedido=$id_pedido AND id_empresa=$id_empresa");
  $row=$query->result_array();
  if(count($row)==0){
    $this->numError=1;
    $this->descError="no se encontro el pedido";
    return $exito;
  }
 //marco como recibido
 $this->db->query("update checkout_presupuesto set estado='S', id_comp=$id_comp where id_pedido=$id_pedido");
 $error=$this->db->error();
  if (isset($error['code']) && $error['code']!=0) {                
  $this->numError=$error["code"];
  $this->descError=$error['message'];    
  }else{
    $exito=true;
  }
  return $exito;
 
}

public function last_nro_pedido($id_empresa){


  $nro_pedido=0;
  $query = $this->db->query("SELECT max(id_pedido) as nro_pedido FROM checkout_presupuesto WHERE id_empresa=$id_empresa");
  $row=$query->result_array();
  if(count($row)>=0){
    $nro_pedido=$row[0]['nro_pedido'];
  }
  return $nro_pedido;
}


public function anular_pedido($id_comp,$id_empresa){
  //$this->load->database();//no poner esto por las transacciones
  $exito=false;
 $query = $this->db->query("SELECT * FROM checkout_presupuesto WHERE id_comp=$id_comp AND id_empresa=$id_empresa");
  $row=$query->result_array();
  if(count($row)==0){
    return $exito;
  }else{ 
   $this->db->query("update checkout_presupuesto set estado='A' where id_comp=$id_comp");
   $error=$this->db->error();
    if (isset($error['code']) && $error['code']!=0) {                
    $this->numError=$error["code"];
    $this->descError=$error['message'];    
    }else{
      $exito=true;
    }
 }
 return $exito;
}//anular_pedido


}
?>