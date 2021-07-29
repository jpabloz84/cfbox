<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mpresupuesto extends CI_model{

private $numError=0;
private $descError="";
public $mproductos=null;
  function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  function generar_presupuesto($data,$productos,$id_empresa,$fecha,&$errores){
    $this->load->database();
    /*errores de 1 a 100, errores tecnicos de bd, codigo etc, los errores mayores a 1000 , son personalizados o de negocio(no graves)*/
   $id_pedido=(!isset($data['id_pedido']))?0:$data['id_pedido'];
   $errores=array();
   $this->begin_trans();
   $data['pagado']=0;
   //si es envio a domicilio, elimino lo q es sucursal
   if($data['id_tipo_envio']==1){
    unset($data['id_sucursal']);
    //cargo el servicio de cadeteria
    $qs = $this->db->query("select * from servicios WHERE id_empresa=".$id_empresa."  AND habilitado=1 AND cadeteria=1 AND id_servicio IN(SELECT id_servicio FROM servicios WHERE habilitado=1 AND id_empresa=".$id_empresa." AND cadeteria=1 HAVING MAX(fe_actualizacion))");
    $rs=$qs->result_array();
    if(count($rs)>0){
     $productos[]=array('id_tipo' =>2 ,'nro_tipo'=>$rs[0]['id_servicio'],'cantidad'=>1);
    }
   }//envio a domicilio

   //si retira en sucursal, saco lo q es localidad
   if($data['id_tipo_envio']==2){
    unset($data['id_localidad']);
    unset($data['calle']);
    unset($data['nro']);
    unset($data['piso']);
    unset($data['depto']);
    unset($data['resto']);
   }

   $data['estado']='P';
   $this->db->set($data);
   if($id_pedido>0){
    $this->db->where('id_pedido',$id_pedido);
    $this->db->update('checkout_presupuesto');
    $this->db->delete('checkout_detalle', array('id_pedido' => $id_pedido));
   }else{
    if(isset($data['id_pedido'])){
    unset($data['id_pedido']);    
    }
    $this->db->insert("checkout_presupuesto");
   }
    
    $error=$this->db->error();
    if (isset($error['code']) && $error['code']!=0){
      $this->numError=1;
      $this->descError='Error de base de datos al insertar checkout_presupuesto:'.$error['message'];
      goto salir;
    }else{
        if($id_pedido==0){
        $id_pedido=$this->db->insert_id();          
        }
    }
    $importe_total=0;
    $importe_item=0;
    $importe_unitario=0;
    $nro_detalle=1;
    $producto=array();
   foreach ($productos as $prod) {
     $id_tipo =$prod['id_tipo'];
     $nro_tipo =$prod['nro_tipo'];
     $cantidad =$prod['cantidad'];
     
     
     $disponible=$this->mproductos->verificar_disponibilidad($id_tipo,$nro_tipo,$id_empresa,$cantidad,$errores,$producto);   
 
     if($disponible){
      $importe_unitario=(float)$producto['importe_tipo'];
      $importe_item=$importe_unitario*$cantidad;
      $importe_total+=$importe_item;
      $cmp=array('id_pedido' =>$id_pedido,'nro_detalle' =>$nro_detalle ,'id_tipo' =>$id_tipo,'nro_tipo'=>$nro_tipo,'cantidad'=>$cantidad,'importe_unitario'=>$importe_unitario,'importe_item'=>$importe_item);
      $this->db->set($cmp);
      $this->db->insert("checkout_detalle");
      $error=$this->db->error();
      if (isset($error['code']) && $error['code']!=0){
        $this->numError=2;
        $this->descError='Error de base de datos al insertar checkout_detalle:'.$error['message'];
        goto salir;
      }
      $nro_detalle++;  
     }
   }

   if(count($errores)>0){
    $this->numError=1000;
    $this->descError="Error al solicitar productos";
    goto salir;
   }else{
    $cmp=array('importe_total' => $importe_total);
    $this->db->set($cmp);
    $where=array('id_pedido' =>$id_pedido);
    $this->db->where($where);
    $this->db->update("checkout_presupuesto");
    if (isset($error['code']) && $error['code']!=0){
        $this->numError=2;
        $this->descError='Error de base de datos al actulizar checkout_presupuesto:'.$error['message'];
        goto salir;
      }
   }
    salir:
    if($this->numError!=0){
     $this->rollback_trans();
     $id_pedido=0;
    }else{
     $this->commit_trans();
    }
    return  $id_pedido;
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


      function getmessage($id_pedido){
        if(!$id_pedido>0) return;
        $query = $this->db->query("select * from vercheckout_presupuesto where id_pedido=$id_pedido");
        $strpedido= sprintf("%'.08d", $id_pedido);
        $message="Hola, envio pedido [$strpedido]\r\n";
            foreach ($query->result_array() as $row)
            {
                $q2 = $this->db->query("select * from vercheckout_detalle where id_pedido=$id_pedido and id_tipo=1");
                foreach ($q2->result_array() as $det)
                {   $cantidad=$det['cantidad'];
                    $producto=$det['producto'];                    
                    if($cantidad>1){
                        $message.="-$producto x $cantidad unidades\r\n";
                    }else{
                        $message.="-$producto x $cantidad unidad\r\n";
                    }
                }
                
                $message.="\r\na nombre de ".$row['nombres']." ".$row['apellido']."\r\n";
                if($row['telefono']!=""){
                  $message.="Telefono: ".$row['telefono']."\r\n";
                }
                $mensajecostoenvio="";
                //envio a domicilio
                if($row['id_tipo_envio']==1){
                    $mensajecostoenvio="(costo de envio incluido)";
                    $message.="Enviar a calle ".$row['calle']." ".$row['nro']." ";
                  if($row['piso']!="" || $row['depto']!=""){
                    $message.="piso ".$row['piso'].", depto ".$row['depto'];
                  }                  
                  if($row['resto']!=""){
                    $message.=", ".$row['resto'];
                  }                  
                 $message.=" (".$row['descripcion_loc'].")\r\n";
                }   
                //retira en sucursal   
                if($row['id_tipo_envio']==2){
                    $message.="retiro en sucursal ".$row['sucursal'].": ".$row['sucursal_direccion']." (".$row['localidad_sucursal'].")\r\n";
                }

                $message.="total a pagar:$ ".$row['importe_total']." ".$row['tipo_pago']." $mensajecostoenvio\r\n";
            }
            return $message;
      }

}

?>