<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends CI_Controller {
  private $numError=0;
  private $visitante=null;    
  private $descError="";
function __construct(){
  parent::__construct();
  $this->load->model("mservice");
  $this->visitante=unserialize($this->session->visitante);
  }

   function index()
  { 
    echo "ok";
   
  }

  function clientes(){
    echo "NO SE PERMITE ACTUALIZAR/INSERTAR";
    return;
    date_default_timezone_set('America/Argentina/Buenos_Aires');    
    ini_set('post_max_size','100M');
    ini_set('max_execution_time','10000');
    ini_set('max_input_time','10000');
  $this->load->library("xlslib");
  $this->load->model("mpersona");
  $this->load->model("entidades/mclientes");
  $data=$this->xlslib->read("files/importacion1.xls");
  $id_caja_op=$this->visitante->get_id_caja_opera();
  $this->mservice->begin_trans();
  $id_usuario=1; //administrador
  $id_sucursal=1; //casa central
  $fila=2;
  $filas=$data->rowcount(0);
  if($this->xlslib->numError()==0){
      $numfilas=$data->rowcount(0);
    for($fila;$fila<=$numfilas;$fila++){
      $saldo=0;
      $localidad=trim($data->val($fila,'B'));
      $apenom=trim($data->val($fila,'C'));
      $cuit=trim($data->val($fila,'D'));
      $strsaldo=trim($data->val($fila,'E'));
      $direccion=trim($data->val($fila,'F'));
      $id_loc=13208; //santa fe
      $id_persona=0;
      $nombre="";
      $apellido="";
      $nombres=explode(" ",strtoupper($apenom));
      if(count($nombres)>1){
        $nombre=$nombres[0];      
        $apellido=$nombres[1];  
      }else{
        $nombre=$apenom;
      }
      
      if($localidad!=""){
        //echo "localidad $localidad <br/>";
        $rs=$this->mservice->get("localidades",array("*"),"descripcion_loc LIKE '".$localidad."%'"); 
         if($this->mservice->numError()!=0)
        {
        $this->numError=$this->mservice->numError();
        $this->descError="Error buscar localidad $localidad:".$this->mservice->descError();
         goto salir;
        } 


        if(count($rs)>0){
          $id_loc=$rs[0]['id_loc'];
          //echo "encontre localidad $localidad <br />";
        }
      }
      if($cuit!=""){
        $cuit=str_replace("-","",$cuit);
        echo "tiene cuit $cuit <br />";
      }
      if($strsaldo!=""){
        $saldo=(float)$strsaldo;
      }
      
      //agrego persona
      $campos= array("apellido"=>$apellido,"nombres"=>$nombre,"id_loc"=>$id_loc,"id_loc_nacimiento"=>$id_loc,"cuit"=>$cuit,"tipo_persona"=>"F","cf"=>0,"calle"=>$direccion,"nro_docu"=>$fila,"tipo_docu"=>1,"sexo"=>"M");
      
      $id_persona=$this->mpersona->agrega($campos);
      //agrego cliente
      if($this->mpersona->numError()!=0){
        $this->numError=$this->mpersona->numError();
        $this->descError="Error al cargar persona:".$this->mpersona->descError();
        goto salir;
      }
      $id_cliente=$this->mclientes->agregar(4,"cargado via migracion",$id_persona);
      if($this->mclientes->numError()!=0){
        $this->numError=$this->mpersona->numError();
        $this->descError="Error al cargar cliente:".$this->mpersona->descError();
        goto salir;
      }
      if($saldo>0){
        //genero deuda
        $comp=array("id_cliente"=>$id_cliente,"importe_total"=>$saldo,"importe_neto"=>$saldo,"importe_base"=>$saldo,"fe_creacion"=>date("Y-m-d H:i:s"),"fe_estado"=>date("Y-m-d H:i:s"),"cuit"=>$cuit,"estado"=>"E","id_usuario"=>$id_usuario,"id_usuario_estado"=>$id_usuario,"afip"=>0,"id_sucursal"=>$id_sucursal);
        $id_comp=$this->mservice->insertar('comp',$comp);     
        if($this->mservice->numError()!=0)
        {
        $this->numError=$this->mservice->numError();
        $this->descError="Error al crear comprobante $id_cliente $apenom:".$this->mservice->descError();
         goto salir;
        }
        $comp_det=array("id_comp"=>$id_comp,"nro_item"=>1,"id_tipo"=>1,"nro_tipo"=>1,"precio_base"=>$saldo,"precio_venta"=>$saldo,"precio_venta"=>$saldo,"importe_item"=>$saldo,"detalle"=>"saldo por migracion","cantidad"=>1);
        $this->mservice->insertar('comp_det',$comp_det); 
         if($this->mservice->numError()!=0)
        {
        $this->numError=$this->mservice->numError();
        $this->descError="Error al crear item del comprobante para $id_cliente $apenom:".$this->mservice->descError();
         goto salir;
        }    
      }else{
        //creo saldo a favor
        if($saldo<0){
          $pago=array();
          $pago['pago']=$saldo*-1;
          $pago['id_tipo_pago']=1;
          $pago['observacion']="saldo por migracion";
          $pago['fe_pago']=date('Y-m-d H:i:s');
          $pago['id_cliente']=$id_cliente;        
          $pago['estado']='C';        
          $pago['fe_estado']=date('Y-m-d H:i:s');
          $pago['id_usuario']=$id_usuario;
          $pago['id_usuario_estado']=$id_usuario;
          $pago['id_caja_op']=$id_caja_op;
          $id_pago=$this->mservice->insertar('pagos',$pago);
          echo "pago agregado cliente $id_cliente";
          if($this->mservice->numError()!=0 )
          {
            $this->numError=$this->mservice->numError();
            $this->descError=="Error insertar pago $id_cliente $apenom::".$this->mservice->descError();
            goto salir;
          }
        }//si el saldo es negativo (saldo a favor)
      }
      //echo "$fila - $localidad, $apenom , $cuit, $saldo, $direccion ";
      //echo "<br/>";
      
    } //for
  //$this->mservice->commit_trans();
  
  }else{
    //$this->mservice-> rollback_trans();
    $this->numError=1;
    $this->descError="Se deshicieron los cambios. Error .".$this->descError;
  }
  salir:
  if($this->numError!=0){
    echo "error ".$this->numError.". desc.:".$this->descError;
    $this->mservice-> rollback_trans();
  }else{
    echo "TODO BIEN";
    //$this->mservice-> rollback_trans();
    $this->mservice->commit_trans();  
  }
}//clinetes
        
}//service class

?>