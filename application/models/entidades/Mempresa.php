<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mempresa extends CI_model{
private $id;
private $numError=0;
private $descError="";
	function __construct()
	{
		parent::__construct();
	}
	
	function guardar($campos)
  { 

  		$this->load->database();		
			$this->numError=0;
  		$this->descError="";
  		
      $id_empresa=(isset($campos['id_empresa']))?$campos['id_empresa']:0;
      $id_empresa=($id_empresa=="")?0:$id_empresa;
      
		try {
			
      //modificacion
      if($id_empresa>0){
        
        $data=array('empresa'=>$campos['empresa'],'cuil'=>$campos['cuil'],'direccion'=>$campos['direccion'],'telefono'=>$campos['telefono'],'id_loc'=>$campos['id_loc'],'habilitado'=>$campos['habilitado'],'ingresos_brutos'=>$campos['ingresos_brutos'],'inicio_actividades'=>$campos['inicio_actividades'],'id_cond_iva','page'=>$campos['page']);
      $where= array('id_empresa' => $id_empresa);       
      if(isset($campos['logo'])){
        $data['logo']=$campos['logo'];
      }
      $query=$this->db->update('empresa', $campos,$where);
      $error=$this->db->error();
        if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message'];        
        }
      }else{

        $data=array('empresa'=>$campos['empresa'],'cuil'=>$campos['cuil'],'direccion'=>$campos['direccion'],'telefono'=>$campos['telefono'],'id_loc'=>$campos['id_loc'],'habilitado'=>$campos['habilitado'],'ingresos_brutos'=>$campos['ingresos_brutos'],'inicio_actividades'=>$campos['inicio_actividades'],'id_cond_iva'=>$campos['id_cond_iva'],'page'=>$campos['page']);
      
      if(isset($campos['logo'])){
        $data['logo']=$campos['logo'];
      }
      $this->db->insert('empresa',$data);
      $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message'];        
        }else{          
          $id_empresa=$this->db->insert_id();
        } 
      
      }//else
      $this->crear_consumidor_final($id_empresa);

		} catch (Exception $e) {
			$this->numError=1;
	  	$this->descError=$e->getMessage();
		}			
			
		return $id_empresa;
  }//actualizar_stock_precio

 function guardar_datos_pagina($id_empresa,$datos){
  $this->numError=0;
  $this->descError="";
 	$this->load->database();
  if(!$id_empresa>0){
    return false;
  }
  //elimino los datos previos
  $this->db->query("delete from empresa_datos_pagina where id_empresa=$id_empresa");
  //inserto los datos nuevos
  foreach ($datos as $variable => $valor) {
    $fila=array('variable'=>$variable,'valor'=>$valor,'id_empresa'=>$id_empresa);
    $this->db->insert('empresa_datos_pagina',$fila);
    $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message'];        
          return false;
        } 
   }
			
		return true;
 } 

 function guardar_sucursal($sucursalrs){

  $this->numError=0;
  $this->descError="";
  $this->load->database();
  
  $id_empresa=$sucursalrs['id_empresa'];
  $id_sucursal=$sucursalrs['id_sucursal'];
  $sucursal=$sucursalrs['sucursal'];
  $direccion=$sucursalrs['direccion'];
  $telefono=$sucursalrs['telefono'];
  $responsable_legal=$sucursalrs['responsable_legal'];
  $id_loc=$sucursalrs['id_loc'];
  $email=$sucursalrs['email'];
  $mapa=$sucursalrs['mapa'];
  $id_tipo_sucursal=$sucursalrs['id_tipo_sucursal'];
  
  if($id_sucursal>0){
    $fila=array('sucursal'=>$sucursal,'direccion'=>$direccion,'telefono'=>$telefono,'responsable_legal'=>$responsable_legal,'id_loc'=>$id_loc,'email'=>$email,'mapa'=>$mapa,'id_tipo_sucursal'=>$id_tipo_sucursal);
    $where=array('id_sucursal'=>$id_sucursal,'id_empresa'=>$id_empresa);
    $query=$this->db->update('sucursal', $fila,$where);
    $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message'];        
          return false;
        } 

  }else{
    $fila=array('sucursal'=>$sucursal,'direccion'=>$direccion,'telefono'=>$telefono,'responsable_legal'=>$responsable_legal,'id_loc'=>$id_loc,'email'=>$email,'mapa'=>$mapa,'id_tipo_sucursal'=>$id_tipo_sucursal,'id_empresa'=>$id_empresa);
    $this->db->insert('sucursal',$fila);
    $id_sucursal=$this->db->insert_id();
    
    $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message'];        
          return false;
        } 
  }

  $query= $this->db->get_where('cajas','id_sucursal='.$id_sucursal);
  $rs=$query->result_array();
  //si no existe la caja, la creo
  if(count($rs)==0){
  $cajars=array('caja'=>'CAJA '.$sucursal,'id_sucursal'=>$id_sucursal);
  $this->db->insert('cajas',$cajars);
  }
  
      
    return true;

 }

 function eliminar_sucursal($id_sucursal){
  $this->load->database();
   $this->db->query("delete from sucursal where id_sucursal=$id_sucursal");
   $error=$this->db->error();
    if (isset($error['code']) && $error['code']>0) {              
        $this->numError=$error["code"];
        $this->descError=$error['message'];        
        return false;
      } 
      return true;
 }

//crea cliente consumidor final, siempre y cuando no exista para la empresa (solo debe haber un solo consumidor final por empresa)
 function crear_consumidor_final($id_empresa){  

$this->db->query("INSERT INTO  clientes (id_persona,fe_alta,id_cond_iva,observaciones,habilitado, id_empresa) 
  SELECT p.id_persona,NOW() AS fe_alta, 4 AS  id_cond_iva,CONCAT('CONS. FINAL. EMPRESA ',e.`empresa`) AS observaciones, 1 AS habilitado,e.id_empresa FROM personas p JOIN empresa e 
LEFT OUTER JOIN clientes cl ON cl.id_persona=p.`id_persona` AND cl.id_empresa=e.`id_empresa`
WHERE e.id_empresa=$id_empresa AND p.cf=1 AND cl.id_cliente IS NULL");
   $error=$this->db->error();
    if (isset($error['code']) && $error['code']>0) {              
        $this->numError=$error["code"];
        $this->descError=$error['message'];        
        return false;
      } 
      return true;
 }

 function guardar_items($id_empresa,$items){
  $this->load->database();
  $this->db->query("delete from tipo_item_empresa where id_empresa=$id_empresa");
  foreach ($items as $item) {
    $id_tipo=$item['id_tipo'];
    $vigente=$item['vigente'];
    $default=$item['default'];
    $this->db->query("insert into tipo_item_empresa (`id_tipo`,`id_empresa`,`vigente`,`default`) values ($id_tipo,$id_empresa,$vigente,$default)");
   $error=$this->db->error();
    if (isset($error['code']) && $error['code']>0) {              
        $this->numError=$error["code"];
        $this->descError=$error['message'];        
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
