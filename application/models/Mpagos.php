<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 900);  //900 segundos
class Mpagos extends CI_model{
private $id;
private $numError=0;
private $descError="";
private $service;
private $mtoken;
function __construct()
{
	parent::__construct();  
  $pagos =& get_instance();    //--------------
  $pagos->load->model('seguridad/mtoken');
  $this->mtoken=$pagos->mtoken;
}

//elimina todo lo que haya en el primer parametro, e inserta el nuevo pago
//sin uso
function changepagos($pagosold,$pagosnew,$id_comp)
{ 	
$this->load->database();
$this->db->trans_begin();
$this->numError=0;
$this->descError="";
	$exito=false;
	$error=$this->db->error();
	$pagosinserted=array();
	foreach ($pagosnew as $pago) {
      try {
          $parametros=$pago['parametros']; //lo elimino para que no lo inserte
          unset($pago['parametros']);
          $estado_inicial='P';
          if(isset($pago['id_tipo_pago'])){
            $estado_inicial=$this->getestado_inicial($pago['id_tipo_pago']);
            $pago['estado']=$estado_inicial;
            $pago['fe_estado']=date("Y-m-d H:i:s");
          }
          //inserto los pagos
          $this->db->set($pago);
          $this->db->insert("pagos");
          $error=$this->db->error();
          if (isset($error['code']) && $error['code']!=0) {                
          $this->numError=$error["code"];
          $this->descError=$error['message'];
          $this->numError=4;            
          $this->db->trans_rollback();
          return $exito;
          }
          $pago['id_pago']=$this->db->insert_id();            
          $pagosinserted[]=$pago;
          //inserto los parametros del pago
          foreach ($parametros as $pa){
          	$pa['id_pago']=$pago['id_pago'];
          	$this->db->set($pa);
          	$this->db->insert("pagos_parametros");
            if (isset($error['code']) && $error['code']!=0) {                
            $this->numError=$error["code"];
            $this->descError=$error['message']; 
            $this->numError=3;            
            $this->db->trans_rollback();
          	return $exito;
           }

          }
       }catch (Exception $e) {
          $this->numError=1;
          $this->descError=$e->getMessage();
          $this->db->trans_rollback();
          return $exito;
      }
  
	}//isnerto los pagos nuevos
$comppagosdeleted=array();
	foreach ($pagosold as $pago) {
      try {   
        $compago=array();
       	$stmt.="select * from comp_pagos where where id_pago=".$pago['id_pago'];
	  		$query=$this->db->query($stmt);			
	  		$error=$this->db->error();
          if (isset($error['code']) && $error['code']>0) {            	
          	$this->numError=$error["code"];
            $this->descError=$error['message'];	
            $this->db->trans_rollback();
          	return $exito;                
          }else
          {            
              $compago=$query->result_array();                       
      		}               
       		$comppagosdeleted[]=$compago;
       		$this->db->delete("comp_pagos", array("id_pago",$pago['id_pago']));
       		$this->db->delete("pagos_parametros", array("id_pago",$pago['id_pago']));
       		$this->db->delete("pagos", array("id_pago",$pago['id_pago']));
          $error=$this->db->error();
          if (isset($error['code']) && $error['code']!=0) {                
          $this->numError=$error["code"];
          $this->descError=$error['message'];             
          $this->db->trans_rollback();
          return $exito;
          }

       }catch (Exception $e) {
          $this->numError=2;
          $this->descError=$e->getMessage();
          $this->db->trans_rollback();
          return $exito;
      }
      
	}
$stmt.="select importe_total from comp where where id_comp=".$id_comp;
$query=$this->db->query($stmt);			
$comp=$query->result_array();
$importe_total=(float)$comp[0]['importe_total'];
$stmt.="select sum(monto) as montopagado from comp_pagos where where id_comp=".$id_comp;
$query=$this->db->query($stmt);			
$compsaldo=$query->result_array();
$montopagado=(float)$compsaldo[0]['montopagado'];
$saldo=$importe_total-$montopagado;
	foreach ($pagosinserted as  $pi) {
		$totalpago=(float)$pi['pago'];
		$monto=0;
		if($saldo<=0)
		{				
			break;
		}
		if($saldo>=$totalpago)
		{
			$monto=$totalpago;
			$saldo=$saldo-$totalpago;
		}else
		{
			$monto=$saldo;
			$saldo=0;
		}
		$cmppagos=array('id_comp'=>$id_comp,'id_pago'=>$pi['id_pago'],$monto);
		$this->db->set($cmppagos);
    $this->db->insert("comp_pagos");
    $error=$this->db->error();
    if (isset($error['code']) && $error['code']!=0) {                
    $this->numError=$error["code"];
    $this->descError="Error al insertar comp_pagos ".$error['message'];             
    $this->db->trans_rollback();
    return $exito;
    }

	}//pagos comp_pagos imputacion

if ($this->db->trans_status() === FALSE)
{
$error=$this->db->error();
$this->numError=$error['code'];
$this->descError="error al hacer commit ".$error['message'];
$this->db->trans_rollback();
}else{
$this->db->trans_commit();
$exito=true;
} 

return $exito;
}//change pago


function desimputar_pagos_comp($id_comp){
  $this->load->database();    
  $this->numError=0;
  $this->descError="";
  $exito=false;
    try {
          $this->db->query("delete from comp_pagos where id_comp=$id_comp");          
          $error=$this->db->error();
          if (isset($error['code']) && $error['code']>0) {              
            $this->numError=$error["code"];
            $this->descError=$error['message']; 
          }else{
            $exito=true;
          }
      
    } catch (Exception $e) {
      $this->numError=-1;
      $this->descError=$e->getMessage();
    }
    return $exito;

}

function anular($id_pago,$id_usuario,$trans=true){ //el tercer parametro es para hacer una transaccion 
  $this->load->database();    

    $this->numError=0;
    $this->descError="";
    $exito=false;
    $pago=null;
    try {
          if($trans){
            $ex=$this->begin_trans();  
            if(!$ex){
            return $ex;  
            }
          }
          $q=$this->db->get_where('pagos', array('id_pago' => $id_pago));  
          $pago=($q->result_array())[0];
          $this->db->query("delete from comp_pagos where id_pago=$id_pago");
          //$this->db->query("delete from pagos_parametros where id_pago=$id_pago");
          $this->db->query("update pagos set id_usuario_estado=$id_usuario,estado='A',fe_estado=now() where id_pago=$id_pago");
          //anulo el token para que no se acceda mas
          if($pago['token']!=""){
          $this->mtoken->anular($pago['token']);  
          }
          
          if($trans){
          $ex=$this->commit_trans();
          if(!$ex){
            return $ex;  
            }
          }
       $exito=true;
      
    } catch (Exception $e) {
      $this->numError=-1;
      $this->descError=$e->getMessage();
    }
    if($trans && !$exito){
      $this->rollback_trans();
    }
    return $exito;
}

function anular_pagos_comp($id_comp,$id_usuario,$trans=true){
   $this->numError=0;
    $this->descError="";
    $exito=false;
    try {
        if($trans){
            $this->db->trans_start();  
          }
        $q=$this->db->query("select * from pagos where id_comp_ref=$id_comp");
        $pagos=$q->result_array();
          //anulo los token
         foreach ($pagos as $pago) {
            if($pago['token']!=""){
            $this->mtoken->anular($pago['token']);  
            }   
         }
        
      $this->db->query("delete from comp_pagos where id_pago in(select id_pago from pagos where id_comp_ref=$id_comp)");
      $error=$this->db->error();
          if (isset($error['code']) && $error['code']>0) {              
            $this->numError=$error["code"];
            $this->descError=$error['message']; 
            goto salir;
          }
      //anulo los pagos que vinieron con el comprobante
      $this->db->query("update pagos set id_usuario_estado=$id_usuario,estado='A',fe_estado=now() where id_comp_ref=$id_comp");
      $error=$this->db->error();
          if (isset($error['code']) && $error['code']>0) {              
            $this->numError=$error["code"];
            $this->descError=$error['message']; 
            goto salir;
          }
       if($trans){
        $this->db->trans_complete();
        }
      $exito=true;
      //echo "ok";
    } catch (Exception $e) {
       $this->numError=-1;
      $this->descError=$e->getMessage();
    }
    salir:
     if($trans && !$exito){
    $this->db->trans_rollback();
    }
    return $exito;

}

//dado un comprobante, si tiene deuda, buscar el saldo y se lo agrega
function imputar_con_saldo($id_comp,$id_usuario){
$this->load->database();  
$this->service =& get_instance();    //--------------
$this->service->load->model('mservice');  
$this->numError=0;
$this->descError="";
$exito=false;
$stmt="select * from vercomprobantes v where v.id_comp=$id_comp and v.deuda>0 and v.id_cliente<>3 and ((v.afip=0 AND v.estado='E') OR (v.estado IN('E','F') AND v.id_tipo_comp IN(1,2,5)))";
$query=$this->db->query($stmt);     
$arComp=$query->result_array();
//si el comprobante , tiene deuda por saldar
  if(count($arComp)>0){
  $id_cliente=$arComp[0]['id_cliente'];
  $id_empresa=$arComp[0]['id_empresa'];
  $id_proceso=$this->service->mservice->registrar_proceso($id_usuario,1,"proceso de imputación con saldo a comprobante $id_comp");
  //$exito=$this->pagar_consaldo($id_comp,$id_cliente,$id_proceso); 
  $comppago=array(); 
  $exito=$this->pagar_comp_saldo($id_comp,$id_cliente,$id_empresa,$comppago,$id_usuario,$id_proceso);

  }//arcomp>0
return $exito;
}//imputar_con_saldo


//dado el cliente, si tiene saldo a favor, imputa los pagos a los comprobantes que esten con deuda
function imputar($id_cliente,$id_usuario,&$id_proceso)
{   if($id_cliente<=0 || $id_cliente==""){
    $this->numError=7;
    $this->descError="No ingreso cliente";
    }

    $this->service =& get_instance();    //--------------
    $this->service->load->model('mservice');

    if($id_usuario<=0 || $id_usuario==""){
    $this->numError=8;
    $this->descError="No se ingreso usuario";
    }
    $this->load->database();    
    $this->numError=0;
    $this->descError="";
    $exito=false;
    $comppago=array();
    
    try {
          $ex=$this->begin_trans();
          if(!$ex)
          return $exito;

          $stmt="select * from verimputaciones_pagos where id_cliente=$id_cliente and resta_imputar>0 order by fe_pago asc";
          $query=$this->db->query($stmt);     
          $error=$this->db->error();
          if (isset($error['code']) && $error['code']>0) {              
            $this->numError=$error["code"];
            $this->descError=$error['message']; 
            goto salir;
          }else
          {     

              $arrSaldos=$query->result_array();
              if(!count($arrSaldos)>0)
              {
                    $this->numError=1;
                    $this->descError="no hay saldo a favor del cliente";  
                    goto salir;
              }
              
              //recorro cuales son los comprobantes con deuda en orden ascendientes de la creacion
              $stmt="select * from vercomprobantes v where v.id_cliente=$id_cliente  AND  ((v.afip=0 AND v.estado='E') OR (v.estado IN('E','F') AND v.id_tipo_comp IN(1,2,5))) and v.deuda>0 order by fe_creacion asc";
              $query=$this->db->query($stmt);     
              $arrComp=$query->result_array();

              if(!count($arrComp)>0)
              {
                  $this->numError=2;
                  $this->descError="no hay comprobantes con saldo"; 
                  goto salir;
              }
              $haysaldo=true;
              $id_proceso=$this->service->mservice->registrar_proceso($id_usuario,1,"proceso de imputacion con saldo");
              foreach ($arrComp as $cmp) {
                $id_comp=$cmp['id_comp'];
                $id_empresa=$cmp['id_empresa'];
                //$ex=$this->pagar_consaldo($id_comp,$id_cliente,$id_proceso);
                $comppago=array();
                $ex=$this->pagar_comp_saldo($id_comp,$id_cliente,$id_empresa,$comppago,$id_usuario,$id_proceso);
                if(!$ex && $this->numError!=0){
                goto salir; 
                }

                $saldo=$this->obtenersaldo_afavor($id_cliente,$id_empresa);

                if($saldo<=0){
                  break;
                }
              }
              if($this->commit_trans())
              $exito=true;

          }
    }catch (Exception $e) {
      $this->numError=-1;
      $this->descError=$e->getMessage();
    }
    salir:
    if($this->numError!=0)
    {
      $this->rollback_trans();        
    }
    
    return $exito;
  }


  function imputar_pago($id_pago,$id_usuario){
    if($id_pago==""){
    $this->numError=7;
    $this->descError="No ingreso pago";
    }

    if($id_usuario<=0 || $id_usuario==""){
    $this->numError=8;
    $this->descError="No se ingreso usuario";
    }
    $this->load->database();    
    $this->numError=0;
    $this->descError="";
    $exito=false;
    $comppago=array();
    $id_cliente=0;
    try {
      $ex=$this->begin_trans();
      if(!$ex)
      return $exito;

        $query=$this->db->query("select * from pagos where id_pago=$id_pago");     
        $rs=$query->result_array();
        $pago=$rs[0];

        $id_cliente=$pago['id_cliente'];
      //recorro cuales son los comprobantes con deuda en orden ascendientes de la creacion
       $stmt="select * from vercomprobantes v where v.id_cliente=$id_cliente  AND  ((v.afip=0 AND v.estado='E') OR (v.estado IN('E','F') AND v.id_tipo_comp IN(1,2,5))) and v.deuda>0 order by fe_creacion asc";
        $query=$this->db->query($stmt);     
        $arrComp=$query->result_array();

        if(!count($arrComp)>0)
        {
            $this->numError=2;
            $this->descError="no hay comprobantes con saldo"; 
            goto salir;
        }
        $haysaldo=true;
        
        foreach ($arrComp as $cmp) {
          $id_comp=$cmp['id_comp'];  
          $id_empresa=$cmp['id_empresa'];        
          $comppago=array();
          $ex=$this->pagar_comp_saldo($id_comp,$id_cliente,$id_empresa,$comppago,$id_usuario);
          if(!$ex && $this->numError!=0){
          goto salir; 
          }

          $saldo=$this->obtenersaldo_afavor($id_cliente,$id_empresa);
          if($saldo<=0){
            break;
          }
        }// for 
        if($this->commit_trans())
        $exito=true;
    }catch (Exception $e) {
      $this->numError=-1;
      $this->descError=$e->getMessage();
    }
    salir:
    if($this->numError!=0)
    {
      $this->rollback_trans();        
    }
    
    return $exito;

  }//imputar pago

  function begin_trans()
  { $this->load->database();
    $this->db->trans_begin();
    $exito=true;
        $error=$this->db->error();
        if (isset($error['code']) && $error['code']>0) {                
            $this->numError=$error["code"];
            $this->descError=$error['message']; 
            $exito=false;
        }
        return $exito;
  }

    function commit_trans()
    {$exito=true;
          
           if ($this->db->trans_status() === FALSE)
              {
              $error=$this->db->error();
              $this->numError=$error['code'];
              $this->descError=$error['message'];
              $this->db->trans_rollback();
              $exito=false;
              }else{
                  $this->db->trans_commit();
              } 
    return $exito;
          
    }

//dado un comprobante con deuda, toma el saldo del cliente para pagarlo
function pagar_comp_saldo($id_comp,$id_cliente,$id_empresa,&$comppago,$id_usuario,$id_proceso=0){
$exito=false;
$this->numError=0;
$this->descError="";  
/*obtengo todos aquellos pagos en los cuales todavia no se haya imputado su monto total*/
$stmt="select * from verimputaciones_pagos where id_cliente=$id_cliente and resta_imputar>0 and id_empresa=$id_empresa order by fe_pago asc";
$query=$this->db->query($stmt);     
$error=$this->db->error();
 if (isset($error['code']) && $error['code']>0) {
  $this->numError=$error["code"];
  $this->descError=$error['message']; 
  return $exito;
 }
$arrSaldos=$query->result_array();

//no hay saldo
if(!count($arrSaldos)>0){
  $this->numError=-50;
  $this->descError="No hay saldo disponible";
    return $exito;
}

$stmt="select * from vercomprobantes where id_comp=$id_comp and deuda>0";
$query=$this->db->query($stmt);     
$error=$this->db->error();
if (isset($error['code']) && $error['code']>0) {
  $this->numError=$error["code"];
  $this->descError=$error['message'];  
 return $exito;
}
$arrComp=$query->result_array();
 /*echo "arrComp 1 <br/>";
            print_r($arrComp);*/
//el comprobante esta pagado
if(!count($arrComp)>0){
return $exito;
}
$cmp=$arrComp[0];
$strproceso=$id_proceso;
if($id_proceso==0){
  $strproceso='NULL';
}
$deuda=(float)$cmp['deuda'];

foreach ($arrSaldos as $pagocredito) {  
  $credito=(float)$pagocredito['resta_imputar'];
  
  $diff=0;
  while ($credito>0 && $deuda>0) {    
    //si hay mas deuda que credito, el credito se puede hacer cargo de la deuda en forma parcial
    $diff=$deuda-$credito;
    if($diff>=0 && $deuda>0 && $credito>0){                   
    //ocupo credito

      $id_pago=$pagocredito['id_pago'];
      $stmt="insert into comp_pagos (id_comp,id_pago,monto,id_proceso,fecha,id_usuario) values ($id_comp,$id_pago,$credito,$strproceso,now(),$id_usuario)";
      $this->db->query($stmt);
      $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {             
      $this->numError=$error["code"];
      $this->descError=$error['message']; 
      return $exito;
      }
      $deuda=$deuda-$credito;
      $credito=0; //importante para que no entre en el siguiente IF , y no impute de nuevo      
      $comppago[]=$this->db->insert_id();
    }
    
    //si hay mas credito que deuda, el credito , salda la deuda
    if($diff<0 && $deuda>0 && $credito>0){
       $stmt="insert into comp_pagos (id_comp,id_pago,monto,id_proceso,fecha,id_usuario) values(".$id_comp.",".$pagocredito['id_pago'].",".$deuda.",$strproceso,now(),$id_usuario)";
      $this->db->query($stmt);
      $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {             
        $this->numError=$error["code"];
        $this->descError=$error['message']; 
        return $exito;
      }
      $comppago[]=$this->db->insert_id();
      $credito=$credito-$deuda;  
      $deuda=0;   
    }//mas credito que deuda
  
    if($deuda<=0)
      break; //salgo del while
  }//while credito

if($deuda<=0)
break; //salgo del for
            
}//for
$exito=true;
return $exito;  
}





function obtenersaldo_afavor($id_cliente,$id_empresa){
$numError=0;
$descError="";
$saldo=0;
//$this->load->database();
$rows=array();
 try{

    $query=$this->db->query("select * from versaldo_afavor where id_cliente=$id_cliente and id_empresa=$id_empresa");         
    $error=$this->db->error();
    if (isset($error['code']) && $error['code']>0) {
        
        $this->numError=$error["code"];
        $this->descError=$error['message']; 
        
    }else
    {
     $rows=$query->result_array();                       
     if(count($rows)>0){
      $saldo=(float)$rows[0]['saldo_afavor'];
     }
    }    
} 
catch (Exception $e) {
$this->numError=1;
$this->descError=$e->getMessage();
}

return $saldo;
}

//dado el comprobante, actualizo los pagos pendientes imputados con el nuevo monto 
//estadoMP es el estado del pago que devuelve mercado pago
function imputarPagoPendiente($id_pago,$montoacreditado,$id_usuario,$id_caja,$estadoMP="C"){
  $this->load->database();
  //echo "id_pago: $id_pago,montoacreditado: $montoacreditado,id_usuario: $id_usuario,id_caja: $id_caja,estadoMP: $estadoMP";

  $pago=null;
  $id_caja_opera=0;
  $this->numError=0;
  $this->descError="";
  //obtengo la caja abierta
  $q1=$this->db->query("SELECT * FROM cajas_operaciones WHERE id_caja=$id_caja AND fe_fin IS NULL ORDER BY fe_inicio DESC LIMIT 0,1");         
   $rs1=$q1->result_array(); 
   if(count($rs1)>0){
    $id_caja_opera=(int)$rs1[0]['id_caja_op'];
    $query=$this->db->query("select * from pagos where id_pago=$id_pago and estado in('P','L')");         
     $rs=$query->result_array(); 
    if(count($rs)>0){

      $pago=$rs[0];
      //si el monto acreditado supera o igual a lo lo que tengo q pagar, cambio de estado el pago y lo imputo
      if($montoacreditado>0 && (($estadoMP=="C" && $estadoMP!=$pago["estado"]) 
        || ($estadoMP=="L" && $montoacreditado>=$pago['pago']))){
        $this->db->query("delete from comp_pagos where id_pago=$id_pago"); //si el pago esta pendiente, ignoro las imputaciones
        //fuerzo a que se ponga como cobrado cuando supera el monto pagado
        if($estadoMP=="L" && $montoacreditado>=$pago['pago']){
          $estadoMP="C";
        }
        $error=$this->db->error();
        if (isset($error['code']) && $error['code']>0) {              
            $this->numError=$error["code"];
            $this->descError=$error['message'];         
        }
          $this->db->query("update pagos set estado='$estadoMP',id_usuario_estado=$id_usuario,fe_estado=now(),id_caja_op=$id_caja_opera  where id_pago=$id_pago");
          $this->imputar_pago($id_pago,$id_usuario);

          $error=$this->db->error();
        if (isset($error['code']) && $error['code']>0) {              
            $this->numError=$error["code"];
            $this->descError=$error['message'];         
        }          
      }
    }else{
      $this->numError=2;
      $this->descError="pago confirmado con anterioridad";  
    }
   }else{
    $this->numError=1;
    $this->descError="no hay caja abierta";
   }

  return ($this->numError==0);
}


function rollback_trans()
{$exito=true;    
      $this->db->trans_rollback();
      $error=$this->db->error();
      if (isset($error['code']) && $error['code']>0) {              
          $this->numError=$error["code"];
          $this->descError=$error['message']; 
      $exito=false;    
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

function getestado_inicial($id_tipo_pago){

    $query=$this->db->query("select * from tipo_pagos where id_tipo_pago=$id_tipo_pago ");         
     $rs=$query->result_array();
     return $rs[0]['estado_inicial'];
}


function getstrpagos($id_comp){
    $str="";
    $q=$this->db->query("SELECT SUM(pago) AS monto,tipo_pago,id_tipo_pago FROM verpagos WHERE id_comp_ref =$id_comp
GROUP BY id_tipo_pago,tipo_pago");
    $res=$q->result_array();
    if(count($res)>0){
      foreach ($res as $r) {
        //para cuenta corriente no se muestra el monto que va dirigido
        $strpago="";
        if($r['id_tipo_pago']==7){
          $strpago=$r['tipo_pago'];  
        }else{
          $strpago=$r['tipo_pago'].": $".$r['monto'];  
        }
        
        if($str==""){
        $str.=$strpago;  
        }else{
          $str.=" - ".$strpago;  
        }        
      }
    }

    return $str;
  }//getstrpagos



}//clase mpagos

?>
