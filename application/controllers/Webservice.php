<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webservice extends CI_Controller {
  private $numError=0;
  private $visitante=null;    
  private $descError="";
  private $log_activo=false;
function __construct(){
  parent::__construct();
  $this->load->model("mservice");
  $this->load->model('operaciones/meventos');
  $this->load->model('seguridad/mconfiguracion');

  $this->load->library('Mpwrapper');
  $this->load->library('Venta');
  $this->load->library('Mailer');
  $this->load->model('operaciones/mpagos');
  $this->load->model('operaciones/mcomprobantes');
  $this->load->model('muser');
  $log_activo=1;//(int)$this->mconfiguracion->getvalor("log_activo");
    $this->log_activo=($log_activo==1)?true:false;
  }

   function index()
  { 
   
  }


//mp informa que un pedido fue pagado , puede que informe por celu con la url 
  /*https://vivobingo.com.ar/index.php/webservice/pedido_pagado?collection_id=28115882&collection_status=approved
  &external_reference=17&payment_type=credit_card&merchant_order_id=1607691479
  &preference_id=42435928-d53635d7-1f46-46f9-a5e9-b32edae7782a&site_id=MLA&processing_mode=aggregator&merchant_account_id=null*/
  /*
  o por servicio web de mercado pago
  */
  /*tengo la sospecha q esto se va a ejecutar cuando se acredite*/
function pedido_pagado(){
date_default_timezone_set("America/Argentina/Buenos_Aires");
$this->fwxml->numError=0;
$this->fwxml->descError="";
$this->fwxml->arrResponse=array();

$registrosActualizados=0;
$reportado=false;
$topic="";
$dispositivomovil=false;
$id_empresa_redirect=0;
$processing_mode="unknow";
$page="";
//pago vino por webservice de mercado pago
if (isset($_GET["topic"])) {    
    $json=json_encode($_GET);
    $this->meventos->registrar('MP:recepcion en pedido_pagado(via webservice mp)',$json);
    if($this->meventos->numError()!=0){
        echo $this->descError();
      }
  }

//pago vino por webservice de mercado pago (por lo general cuando se hace pago por celu)
if (isset($_GET["merchant_order_id"]) &&  $_GET["collection_status"]) {    
    $json=json_encode($_GET);
    $dispositivomovil=true;
    $processing_mode=$_GET['processing_mode']; //aggregator 
    $this->meventos->registrar('MP:recepcion en pedido_pagado(via redireccionamiento)',$json);
    if($this->meventos->numError()!=0){
        echo $this->descError();
      }
  }  
  
try {
     


  if($topic=="merchant_order"){
    $reportado=$this->verificar_pago_orden($_GET["id"],$registrosActualizados);
    
   }//topic merchant order

   if(isset($_GET['external_reference']) && $dispositivomovil ){
     $external_reference=$_GET["external_reference"];         
     //$external_reference="VIVOBINGOPR001175";
     //verifico pago para el formato nuevo y tambien para el formato viejo
     if(strlen($external_reference)>11){
      $prefix_mpsistema=$this->mconfiguracion->getvalor("prefix_mpsistema");
      $prefix_mod=$this->mconfiguracion->getvalor("prefix_mpmodpresupuesto");
      $parte=str_replace($prefix_mpsistema.$prefix_mod, "", $external_reference);
      //$parte=str_replace("VIVOBINGOPR", "", $external_reference);
      $id_empresa=(int)substr($parte,0,3);
      $id_pedido=(int)substr($parte,3,strlen($parte)-3);
      //$id_pedido=0;
      $this->meventos->registrar('MP verificacion pago'.$external_reference,"Pedido $id_pedido");
      $ex=$this->verificar_pago($id_pedido,true);
      $rspedido=$this->mservice->query("SELECT * FROM vercheckout_presupuesto WHERE  id_pedido=$id_pedido");
      $pedido=$rspedido[0];
      //$id_empresa=$pedido['id_empresa'];
      $rs=$this->mservice->query("SELECT * FROM empresa WHERE  id_empresa=$id_empresa");
      $page=BASE_URL.$rs[0]['page'];
     }else{
      $ex=$this->verificar_pago($external_reference,true);
      $rspedido=$this->mservice->query("SELECT * FROM vercheckout_presupuesto WHERE  id_pedido=$id_pedido");
      $pedido=$rspedido[0];
      $id_empresa=$pedido['id_empresa'];
      $rs=$this->mservice->query("SELECT * FROM empresa WHERE  id_empresa=$id_empresa");
      $page=BASE_URL.$rs[0]['page'];
      $this->meventos->registrar('MP verificacion pago'.$external_reference,"Pedido $id_pedido");
     }
      

   }
    

    if($registrosActualizados>0){
      $this->meventos->registrar('MP actualizaciones',"Se han registrado y actualizado $registrosActualizados pagos");
    }
      
  } catch (Exception $e) {
    $this->fwxml->numError=1;
    $this->fwxml->descError=$e->getMessage();
  }

if($this->fwxml->numError!=0){
  $this->meventos->registrar('MP error WEBSERVICE',"numError ".$this->fwxml->numError." descError ".$this->fwxml->descError);
}
if($reportado && !$dispositivomovil){
http_response_code(200);  
} 
//si viene dispositivo movil, redireccionamos a la pagina principal   
if($dispositivomovil){?>
  <script type="text/javascript">
    window.location.replace("<?=$page?>");
  </script>
 <?php
}
return;  


}
//mp informa que un pedido esta pendiente de pago
function pedido_pendiente(){
//$json=json_encode($_GET);
$this->meventos->registrar('MP:recepcion en pedido_pendiente()',var_export($_REQUEST, true));

}
//mp informa que un pedido fallo el pago
function pedido_fallo(){
//$json=json_encode($_GET);
$this->meventos->registrar('MP:recepcion en pedido_fallo()',var_export($_REQUEST, true));

}
//para implementar a futuro
function notificaciones(){  
    $this->meventos->registrar("MP:fx notificaciones()",var_export($_REQUEST, true));    
    http_response_code(200);
return;
}
    //oyente de mensajes de mercado pago
function cronmp(){
  $numError=0;
  $descError="";
  $registrosActualizados=0;
  date_default_timezone_set("America/Argentina/Buenos_Aires");
  
  $this->load->model('mpagos');
  if (isset($_GET["topic"])) {    
     $json=json_encode($_GET);
    $this->meventos->registrar('MP:fx cronmp()',$json);
    if($this->meventos->numError()!=0){
        echo $this->descError();
      }
  }
  
  try {
    $this->load->library('Mpwrapper');  
    $token_access='';
    $sandbox_mode=1;
    $prefix_mpsistema=$this->mconfiguracion->getvalor("prefix_mpsistema");
    $prefix_mod=$this->mconfiguracion->getvalor("prefix_mpmodpago");  
    //reviso todos los pagos pendientes o parciales de mercado pago, y hago foco sobre esos pagos
    $rs=$this->mservice->query("SELECT * FROM verpagos WHERE estado in('P','L') AND id_tipo_pago=8");
    
    foreach ($rs as  $pago) {
    $token_access=$this->mconfiguracion->getvalor("mp_token_access",$pago['id_empresa']);
    $sandbox_mode=($this->mconfiguracion->getvalor("mp_modo_produccion",$pago['id_empresa'])==1)?false:true;
    /*echo "mp_modo_produccion:".$this->mconfiguracion->getvalor("mp_modo_produccion",$pago['id_empresa']);
    echo "token_access $token_access";
    echo "sandbox_mode ".(($sandbox_mode)?"SI":"NO");
    die();*/
    $this->mp->init($token_access,$sandbox_mode); 
      $suma=(float)$pago['pago']; 
      $id_pago=$pago['id_pago'];  
      $id_caja=$pago['id_caja'];  
      $id_usuario_estado=$pago['id_usuario_estado']; 
      $montoacreditado=0; 
      $objpagos=null;
      $estadopagoMP="";
      $prefijo_referencia=$prefix_mpsistema.$prefix_mod.str_pad($pago['id_empresa'],3,"0",STR_PAD_LEFT).$id_pago;
      //hay pago de tipo mercado pago pendiente, verifico en mp si el mismo se incremento
      if($suma>0){        
        $montoacreditado=(float)$this->mp->montoAcreditado($prefijo_referencia,$objpagos);        
        $estadopagoMP=$this->mp->estadoPago($objpagos);
        }
        if($this->log_activo){
          $this->meventos->registrar("MODULO WEBSERVICE:monto acreditado $montoacreditado para pago $id_pago",var_export($objpagos, true));
        }
        $infoArr['montoacreditado']=$montoacreditado;
        //si se ha pagado el total o parcial mente, o bien son montos mayores a cero pero distintos, hay que actualizar comp_pagos
        if(($estadopagoMP=="P"|| $estadopagoMP=="L" ||  $estadopagoMP=="C") && $montoacreditado>0){
          $infoArr['actualiza']=1;
          $imputado=$this->mpagos->imputarPagoPendiente($id_pago,$montoacreditado,$id_usuario_estado,$id_caja,$estadopagoMP);
          if(!$imputado){
            $numError=$this->mpagos->numError();
            $descError=$this->mpagos->descError();
          }else{
            $registrosActualizados++;
          }
        }//si hay montoacreditado      
    }//for

    if($registrosActualizados>0){
      $this->meventos->registrar('MP actualizaciones',"Se han registrado y actualizado $registrosActualizados pagos");
    }
      
  } catch (Exception $e) {
    $numError=1;
    $descError=$e->getMessage();
  }

if($numError!=0){
  $this->meventos->registrar('MP error WEBSERVICE',"numError $numError. descError $descError");
}
    
http_response_code(200);
return;    
}





function verificar_pago($id_pedido=0,$return=false,&$ocurrencias=0){
date_default_timezone_set("America/Argentina/Buenos_Aires");
$this->fwxml->numError=0;
$this->fwxml->descError="";
$this->fwxml->arrResponse=array();
$pedidopagado=false;
$prefijo_referencia="";
if($id_pedido>0){
  $rspagado=$this->mservice->query("SELECT * FROM vercheckout_presupuesto WHERE  id_pedido=$id_pedido AND pagado=1");
  if(count($rspagado)>0){
    $this->fwxml->numError=100;
    $this->fwxml->descError="El pago por el que consulta, ya esta cobrado";
    $this->fwxml->ResponseJson['id_pedido']=$id_pedido;
    goto salir;
  }  
   $rspedido=$this->mservice->query("SELECT * FROM vercheckout_presupuesto WHERE  id_pedido=$id_pedido AND pagado=0");
  
  if(count($rspedido)>0){
    $id_empresa=$rspedido[0]['id_empresa'];
    $token_access=$this->mconfiguracion->getvalor_variable("mp_token_access",$id_empresa);
    $sandbox_mode=($this->mconfiguracion->getvalor_variable("mp_modo_produccion",$id_empresa)==1)?false:true;
    $prefix_mpsistema=$this->mconfiguracion->getvalor("prefix_mpsistema");
    $prefix_mod=$this->mconfiguracion->getvalor("prefix_mpmodpresupuesto");
    $prefijo_referencia=$prefix_mpsistema.$prefix_mod.str_pad($id_empresa,3,"0",STR_PAD_LEFT).$id_pedido;
    $this->mpwrapper->init($token_access,$sandbox_mode); 
    $importe_pagado=$this->mpwrapper->montoAcreditado($prefijo_referencia);
    $this->meventos->registrar("MP:fx verificar_pago- checkeo referencia ".$prefijo_referencia,"importe_pagado $importe_pagado");  
    $this->venta->load("mpwrapper",$this->mpwrapper);
    $this->venta->load("muser",$this->muser);
    $this->venta->load("mailer",$this->mailer);
    $this->venta->load("mpagos",$this->mpagos);
    $this->venta->load("mcomprobantes",$this->mcomprobantes);
    $this->venta->load("mservice",$this->mservice);
    $this->venta->load("mconfiguracion",$this->mconfiguracion);  
    if($importe_pagado>0){
      $pedidopagado=$this->venta->reportar_pago_pedido($rspedido[0],$importe_pagado); 
      if(!$pedidopagado){
        $this->fwxml->numError=99;
        $this->fwxml->descError="error al reportar pago ".$this->venta->descError();
      }else{
        $ocurrencias++;
      }   
    }else{
      $this->fwxml->numError=100;
      $this->fwxml->descError="Aún no hemos recibido el pago. Cuando el mismo esté acreditado, le avisaremos";
    }
    
          
  }else{
    $this->fwxml->numError=101;
      $this->fwxml->descError="Aún no hemos podido verificar el pago. Cuando el mismo esté acreditado, le avisaremos";
  }
}else{
$this->fwxml->numError=1;
$this->fwxml->descError="error";
}

salir:

if(!$return){
$this->fwxml->ResponseJson();   
}else{
  return $this->fwxml->numError==0;
}

}

function failure(){ //para recibir pagos fallados
$this->meventos->registrar("MP: fx failure",var_export($_REQUEST, true));
http_response_code(200);
return;
}

function pending(){ //para recibir pagos pendientes
$this->meventos->registrar("MP: fx pending",var_export($_REQUEST, true));
http_response_code(200);
return;
}


//para implementar a futuro
function notificaciones(){ 
  $exito=false; 
    $this->meventos->registrar("MP:fx notificaciones()",var_export($_REQUEST, true));  
    if(isset($_REQUEST['merchant_order'])&& isset($_REQUEST['id'])){
      $id_order=$_REQUEST['id'];
      $exito=$this->verificar_pago_orden($id_order);
    }
    if($exito){
    http_response_code(200);
    }
    
return;
}

function verificar_pago_orden($id_order,$registrosActualizados=0){
  $token_access='';
$sandbox_mode=1;
$reportado=false;
$this->venta->load("mpwrapper",$this->mpwrapper);
$this->venta->load("muser",$this->muser);
$this->venta->load("mailer",$this->mailer);
$this->venta->load("mpagos",$this->mpagos);
$this->venta->load("mcomprobantes",$this->mcomprobantes);
$this->venta->load("mservice",$this->mservice);
$this->venta->load("mconfiguracion",$this->mconfiguracion);
   //$id_order=$_GET["id"];
   $importe_pagado=0;

   $orden=$this->mpwrapper->getOrdenbyID($id_order);
      if($orden!=null){        
        $reportado=true;
        $prefix_mpsistema=$this->mconfiguracion->getvalor("prefix_mpsistema");
        $prefix_mod=$this->mconfiguracion->getvalor("prefix_mpmodpresupuesto");
        $external_reference=$orden->external_reference; 
        if(strlen($external_reference)>11){
        $parte=str_replace($prefix_mpsistema.$prefix_mod, "", $external_reference);
        $id_empresa=(int)substr($parte,0,3);
        $id_pedido=(int)substr($parte,3,strlen($parte)-3);
        //$prefijo_referencia=$prefix_mpsistema.$prefix_mod.str_pad($id_empresa,3,"0",STR_PAD_LEFT).$id_pedido;

        //si existe la orden para este token , busco si existe el pedido         
         $importe_pagado=$this->mpwrapper->montoAcreditado($id_pedido);
         $rspedido=$this->mservice->query("SELECT * FROM vercheckout_presupuesto WHERE  id_pedido=$id_pedido AND pagado=0");
          if(count($rspedido)>0 && $importe_pagado>0){
          $pedidopagado=$this->venta->reportar_pago_pedido($rspedido[0],$importe_pagado);
            if($pedidopagado){
              $registrosActualizados++;  
            }else{
              $this->meventos->registrar('MP error grave a reportar pedido',"numError ".$this->venta->numError().". descError ".$this->venta->descError());
            }

          }
        }else{
           $this->meventos->registrar('Se recibio pago desconocido',var_dump($orden));
        }// external_reference LEN>11        
      }

   //busco todas las empresas que tengan pagos pendientes
    /*$rs=$this->mservice->query("SELECT distinct id_empresa FROM vercheckout_presupuesto WHERE  fecha>ADDDATE(NOW(), INTERVAL -4 MONTH) AND pagado=0");
    $pedidopagado=false;
    foreach ($rs as $em) {
      $id_empresa=$em['id_empresa'];
      $token_access=$this->mconfiguracion->getvalor_variable("mp_token_access",$id_empresa);
      $sandbox_mode=($this->mconfiguracion->getvalor_variable("mp_modo_produccion",$id_empresa)==1)?false:true;
      $this->mpwrapper->init($token_access,$sandbox_mode); 
      //busco si la orden, pertenece a la configuracion de MP de la empresa
      $orden=$this->mpwrapper->getOrdenbyID($id_order);
      if($orden!=null){        
        $reportado=true;
        //si existe la orden para este token , busco si existe el pedido
         $id_pedido=$orden->external_reference;         
         $importe_pagado=$this->mpwrapper->montoAcreditado($id_pedido);
         $rspedido=$this->mservice->query("SELECT * FROM vercheckout_presupuesto WHERE  id_pedido=$id_pedido AND pagado=0");
        if(count($rspedido)>0 && $importe_pagado>0){
        $pedidopagado=$this->venta->reportar_pago_pedido($rspedido[0],$importe_pagado);
          if($pedidopagado){
            $registrosActualizados++;  
          }else{
            $this->meventos->registrar('MP error grave a reportar pedido',"numError ".$this->venta->numError().". descError ".$this->venta->descError());
          }
        
        }         
      }
     
    }//for */
return $reportado;

}//verificar_pago_orden

function checkpagospresupuestos(){
  $this->fwxml->numError=0;
  $this->fwxml->descError="";
  $where_id_empresa="";
  
  if(isset($_REQUEST['id_empresa'])){
    $where_id_empresa=" and id_empresa=".$_REQUEST['id_empresa'];
  }
  $pedidopagado=false;
  $ocurrencias=0;
  $registros=0;
  $stmt="SELECT distinct id_pedido FROM vercheckout_presupuesto WHERE  fecha>ADDDATE(NOW(), INTERVAL -4 MONTH) AND pagado=0 $where_id_empresa";
   //busco todas las empresas que tengan pagos pendientes en los ultimos 4 meses
    $rs=$this->mservice->query($stmt);
    if($this->mservice->numError()!=0){
       $this->fwxml->numError=$this->mservice->numError();
       $this->fwxml->descError=$this->mservice->descError();
    }else{
      foreach ($rs as $em) {
        $registros++;
        $id_pedido=$em['id_pedido'];
        $this->verificar_pago($id_pedido,true,$ocurrencias);
        if($this->fwxml->numError==100){//arroja 100 cuando todavia no se ha pagado
          $this->fwxml->numError=0;
        }
      }  
    }
    

  //$this->fwxml->numError=0;
  $this->fwxml->arrResponse['ocurrencias']=$ocurrencias;
  $this->fwxml->arrResponse['registros']=$registros;
  $this->fwxml->ResponseJson();
}

    
function numError()
{
  return $this->numError;
}

function descError()
{
  return $this->descError;
}


function getpagomp($id_pago){


  try {
    $this->load->library('Mpwrapper');  
    $token_access='';
    $sandbox_mode=1;
    $prefix_mpsistema=$this->mconfiguracion->getvalor("prefix_mpsistema");
    $prefix_mod=$this->mconfiguracion->getvalor("prefix_mpmodpago"); 
       
    //reviso todos los pagos pendientes o parciales de mercado pago, y hago foco sobre esos pagos
    $rs=$this->mservice->query("SELECT * FROM verpagos WHERE estado in('P','L') AND id_tipo_pago=8 and id_pago=$id_pago");
    
    foreach ($rs as  $pago) {
    $token_access=$this->mconfiguracion->getvalor("mp_token_access",$pago['id_empresa']);
    $sandbox_mode=($this->mconfiguracion->getvalor("mp_modo_produccion",$pago['id_empresa'])==1)?false:true;
     $prefijo_referencia=$prefix_mpsistema.$prefix_mod.str_pad($pago['id_empresa'],3,"0",STR_PAD_LEFT).$id_pago;
    $this->mp->init($token_access,$sandbox_mode); 
      $suma=(float)$pago['pago']; 
      $id_pago=$pago['id_pago'];  
      $id_caja=$pago['id_caja'];  
      $id_usuario_estado=$pago['id_usuario_estado']; 
      $montoacreditado=0; 
      $objpagos=null;
      $estadopagoMP="";
      //hay pago de tipo mercado pago pendiente, verifico en mp si el mismo se incremento
      if($suma>0){        
        $montoacreditado=(float)$this->mp->montoAcreditado($prefijo_referencia,$objpagos);        
        $estadopagoMP=$this->mp->estadoPago($objpagos);
        }
        echo "montoacreditado $montoacreditado ,estadopagoMP:$estadopagoMP<br/>" ;
        echo "objpagos:" ;
        var_dump($objpagos);
    }//for

    
      
  } catch (Exception $e) {    
    echo $e->getMessage();
  }
}//getpagomp
        
}//service class

?>