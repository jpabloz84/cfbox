<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Marticulos extends CI_model{
private $id;
private $numError=0;
private $descError="";
	function __construct()
	{
		parent::__construct();
	}
	
	function actualizar_stock_precio($id_articulo,$cantidad,$precio_venta,$modificarprecio)
  {
  		$this->load->database();		
			$this->numError=0;
  		$this->descError="";
  		$exito=false;
		try {
			if($modificarprecio==1){
				if(!$this->actualizar_precio_venta($id_articulo,$precio_venta))
				{
					return $exito;
				}
			}	
	 		$this->db->query("update articulos set stock=stock+$cantidad where id_articulo=".$id_articulo);
	 		$error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {            	
      	$this->numError=$error["code"];
        $this->descError=$error['message'];
        return $exito;	
      }
			$exito=true;				
		} catch (Exception $e) {
			$this->numError=1;
	  	$this->descError=$e->getMessage();
		}			
			
		return $exito;
  }//actualizar_stock_precio

 function actualizar_precio_venta($id_articulo,$precio_venta){
 	$this->load->database();		
			$this->numError=0;
  		$this->descError="";
  	$exito=false;
		try {
		  		
	 		$query=$this->db->get_where('verarticulos','id_articulo='.$id_articulo);
	 		$error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {            	
      	$this->numError=$error["code"];
        $this->descError=$error['message'];
        return $exito;	
      }
      $rows=$query->result_array();
			$numrows=$query->num_rows();
			if($numrows>0){
				$iva=(float)$rows[0]['iva'];
				$pbase=($precio_venta/(1+$iva));
				$piva=$pbase*$iva;
				$this->db->query("update articulos set precio_base=$pbase,precio_iva=$piva,precio_venta=$precio_venta where id_articulo=".$id_articulo);
		 		$error=$this->db->error();
        if (isset($error['code']) && $error['code']>0) {            	
        	$this->numError=$error["code"];
          $this->descError=$error['message'];
          return $exito;	
        }
        $exito=true;
			}
			
		} catch (Exception $e) {
			$this->numError=1;
	  	$this->descError=$e->getMessage();
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

}

?>
