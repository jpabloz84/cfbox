<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mentregas extends CI_model{
private $id;
private $numError=0;
private $descError="";
public $visitante=null;
function __construct()
{
  parent::__construct();
  $this->load->database();  
}
function marcar_entrega($entregado,$id_usuario,$id_carton,$nro_premio){
  date_default_timezone_set('America/Argentina/Buenos_Aires');
  $cmp=array('entregado' => $entregado,'fe_entregado'=>date("Y-m-d H:i:s"),'id_usuario_entrega'=>$id_usuario);
  $this->db->set($cmp);
  $where=array('id_carton' =>$id_carton,'nro_premio' => $nro_premio);
  $this->db->where($where);
  $this->db->update('premios');
   $error=$this->db->error();
  if (isset($error['code']) && $error['code']!=0){
    $this->numError=$error["code"];
    $this->descError='Error al guardar bingo:'.$error['message'];
   
  }

}


function numError(){
  return $this->numError;
}

function descError(){
  return $this->descError;
}

}
?>