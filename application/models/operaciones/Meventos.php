<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Meventos extends CI_model{

private $numError=0;
private $descError="";
  function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  function registrar($evento,$detalle){
    date_default_timezone_set('America/Argentina/Buenos_Aires');    
    $exito=false;
    $sql = "insert into eventos (evento,detalle,fecha) values (?,?,now())";
    $this->db->query($sql, array($evento, $detalle));
    $error=$this->db->error();
    if (isset($error['code']) && $error['code']>0) {                
        $this->numError=$error["code"];
        $this->descError=$error['message'];         
    }else{
      $exito=true;
    }
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