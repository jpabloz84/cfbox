<?php
use \MercadoPago\SDK;
class Mpwrapper{
 private $numError;
 private $descError;
 private $html='';
 private $config;
 private $accesstoken;
 private $sandbox_mode;
 public $prefijo_refexterna="";
 public $successurl='';// url de donde mercadopago va a contestar cuando el pago se haya realizado
 public $failureurl='';// url de donde mercadopago va a contestar cuando el pago haya fallado
 public $pendingurl=''; // url de donde mercadopago va a contestar cuando el pago este pendiente
 public $notificacionesurl='';// url de donde mercadopago va a notificar cambios (devoluciones por ejemplo)
 public $aprobados=true; //notifica solo pagos aprobados, sino poner false para que notifique todos
 private $error=null;
 private $cuotas=1; //cantidad de cuotas a ofreces () 0 cualquier cantidad de cuotas,)
 /*aqui excluyo tipos de pago rapipago, transf bancaria, atm*/
 private $pagos_tipos_excluidos=array(array( "id"=>"ticket"), array("id"=>"bank_transfer"), array("id"=>"atm"));
  function __construct()
  { $this->numError=0;
    $this->descError='';    
    require_once dirname(__FILE__).'/vendor/autoload.php';
    \MercadoPago\SDK::initialize();
      $this->config = \MercadoPago\SDK::config();
    $CI=& get_instance();
    $CI->mp=$this;
    /*$CI=& get_instance();
    $CI->config=$this->config;*/
  }
  function __destruct()
  { 

  }
  public function init($accesstoken,$sandbox_mode=true){
    $this->accesstoken=$accesstoken;
    $this->sandbox_mode=$sandbox_mode;
  }

  
   function numError()
   {
    return $this->numError;
   }

  function descError()
  {
    return $this->descError;
  }
  /*
  id_operacion: identificativo de la operacion de pago
  items:articulos a pagar con lso montos (array)
  pagador:array de los datos del que paga
  
  */
  //devuelte data-preference-id que inidica el init point a generar
  function getinitpoint($id_operacion,$items,$pagador) 
  { $exito=false;
    $this->numError=0;
    $this->descError='';
    $initpoint='';
    $payment_methods= array();
    date_default_timezone_set('America/Argentina/Buenos_Aires');    
    try{
     $this->config->set('ACCESS_TOKEN',$this->accesstoken);
     $this->config->set('sandbox_mode',$this->sandbox_mode);        
     $preference =new MercadoPago\Preference(); 
    
     if($this->cuotas>0){
      $payment_methods=array("installments" =>$this->cuotas);
      //$preference->payment_methods = 
     }
     //excluyo algunos tipos de pagos
     if(count($this->pagos_tipos_excluidos)>0){
      $payment_methods["excluded_payment_types"]=$this->pagos_tipos_excluidos;
         //$preference->payment_methods["excluded_payment_types"]=$this->pagos_tipos_excluidos;
     }
     if(count($payment_methods)>0){
      $preference->payment_methods = $payment_methods;
     }

     //agrego los articulos a pagar
     $articulos = array();               
     foreach ($items as  $item) {
       $articulo=new MercadoPago\Item();
       $articulo->id=$item['id'];
       $articulo->title=$item['titulo'];
       $articulo->quantity=$item['cantidad'];
       $articulo->currency_id=$item['divisa'];
       $articulo->unit_price=$item['precio_unitario'];
       array_push($articulos, $articulo);
     }
     //agrego los datos del pagante
     $payer = new MercadoPago\Payer();

     $objDateTime=new DateTime();
     $payer->date_created = $objDateTime->format('c');
     if(isset($pagador['nombres'])){
      $payer->name =$pagador['nombres']; 
      $payer->first_name=$pagador['nombres']; 
     }
     if(isset($pagador['apellido'])){
      $payer->surname =$pagador['apellido']; 
      $payer->last_name=$pagador['apellido']; 
     }
     if(isset($pagador['email'])){
      $payer->email =$pagador['email']; 
     }

     $phone=array('area_code'=>'','number'=>'');
     if(isset($pagador['tel_caracteristica'])){
      $phone['area_code']=$pagador['tel_caracteristica']; 
     }
     if(isset($pagador['tel_numero'])){
      $phone['number']=$pagador['tel_numero']; 
     }
      $payer->phone =  $phone;


     $identificacion=array("type" => "","number" => "");
      if(isset($pagador['tipo_docu'])){
      $identificacion['type']=$pagador['tipo_docu']; 
     }
     if(isset($pagador['nro_docu'])){
      $identificacion['number']=(int)$pagador['nro_docu']; 
     }
       $payer->identification = $identificacion;

      $direccion=array(
      "street_name" => "",
      "street_number" => 0,
      "zip_code" => ""
      );
      if(isset($pagador['calle'])){
      $direccion['street_name']=$pagador['calle']; 
      }
     if(isset($pagador['calle_nro'])){
      $direccion['street_number']=$pagador['calle_nro']; 
     }
     if(isset($pagador['cp'])){
      $direccion['zip_code']=(int)$pagador['cp']; 
     }
      $payer->address=$direccion;
      
      $preference->payer = $payer;
      $preference->items = $articulos;
      if($this->notificacionesurl!=""){

      $preference->notification_url  =str_replace("localhost","www.asdasdgdsd.com",$this->notificacionesurl) ; //remplazo esto para que me ande en el local host
      
      }     
      
      //agrego el identificativo de la operacion
      $preference->external_reference=$this->prefijo_refexterna.((string)$id_operacion);
      $backurls=array("success" => "","failure" =>"","pending" => "");  
      if($this->successurl!=""){
        $backurls['success']=$this->successurl;
      }
      if($this->aprobados){
      $preference->auto_return = "approved";//solo pagos aprobados, sino poner all para ver todos  
      }else
      {
        $preference->auto_return = "all";
        if($this->failureurl!=""){
          $backurls['failure']=$this->failureurl;
        }

        if($this->pendingurl!=""){
          $backurls['pending']=$this->pendingurl;
        }
      }
      

      $preference->back_urls=$backurls;  
      //var_dump($preference);
      //genero el initpoint       
      $preference->save();
        $error=$preference->error;
        if(isset($error)){
        $this->numError=$error->status;
        $this->descError='error:'.$error->error.'. message: '.$error->message;
        $this->error=$error;        
        }else{
          $initpoint=$preference->id;      
        }
        

    }catch (Exception $e) {
        $this->numError=98;
        $this->descError=$e->getMessage();
    }
    return $initpoint;
  }

  function Error()
  {
    return $this->error;
  }

  function montoAcreditado($referencia_externa,&$pagos=null,&$orden=null){

    $exito=false;
    $this->numError=0;
    $this->descError='';
    $suma=0;
    try {

     $this->config->set('ACCESS_TOKEN',$this->accesstoken);
     $this->config->set('sandbox_mode',$this->sandbox_mode);
     $pago_obj = new MercadoPago\Payment(); //busco el pago
     /* Al pago lo busco por la external_reference, que es el UUID que generé en la compra y que me guardé en la db junto con los datos del comprador y del vendedor */
     $pagos = $pago_obj->search(array("external_reference" => $referencia_externa));
     $this->objpagos=$pagos;
     if($pagos !=null){
      foreach ($pagos as  $pago) {
        if(isset($pago->status_detail)){
          if($pago->status_detail=="accredited"){
          $monto=(float)(isset($pago->transaction_details->total_paid_amount))?$pago->transaction_details->total_paid_amount:0;
          $suma+=$monto;
          }
        }
      }
     }


     
    } catch (Exception $e) {
       $this->numError=80;
        $this->descError=$e->getMessage();
    }
    return $suma;
  }

  function getOrden($objpago){

    $this->numError=0;
    $this->descError='';
    $orden=null;
    try {

     $this->config->set('ACCESS_TOKEN',$this->accesstoken);
     $this->config->set('sandbox_mode',$this->sandbox_mode);
     
     $merchant_order_obj = new MercadoPago\MerchantOrder();      
     if(isset($objpago->order->id)){
     $orden = $merchant_order_obj->get($objpago->order->id); 
   }     
    } catch (Exception $e) {
       $this->numError=80;
       $this->descError=$e->getMessage();
    }
    return $orden;
  }


   function getOrdenbyID($id){

    $this->numError=0;
    $this->descError='';
    $orden=null;
    try {

     $this->config->set('ACCESS_TOKEN',$this->accesstoken);
     $this->config->set('sandbox_mode',$this->sandbox_mode);
     
     $merchant_order_obj = new MercadoPago\MerchantOrder();      
     
     $orden = $merchant_order_obj->get($id); 
       
    } catch (Exception $e) {
       $this->numError=80;
       $this->descError=$e->getMessage();
    }
    return $orden;
  }

  function getPagos($id_operacion){

    $this->numError=0;
    $this->descError='';
    $pagos=null;
    try {

     $this->config->set('ACCESS_TOKEN',$this->accesstoken);
     $this->config->set('sandbox_mode',$this->sandbox_mode);
      $pago_obj = new MercadoPago\Payment(); //busco el pago
      $pagos = $pago_obj->search(array("external_reference" => $id_operacion));
     
       
    } catch (Exception $e) {
       $this->numError=80;
       $this->descError=$e->getMessage();
    }
    return $pagos;
  }

  function estadoPago($pagos){


    $this->numError=0;
    $this->descError='';
    $status_detail="";
    $estado="X"; //inhabilitado
    try {
     if($pagos !=null){
      //el pago no existe
      if($pagos->total==0){
        $estado="P"; //pendiente el pago puede ser q sea por pago facil o directamente no existe
        goto salir;
      }
      
      $approved=false;
      $canceled=false;
      $rechazado=false;

      foreach ($pagos as  $pago) {
        if(isset($pago->status)){
          if($pago->status=="approved"){ //aprobado
          $approved=true;
          }
          if($pago->status=="cancelled"){
          $canceled=true;
          }
          if($pago->status=="rejected"){ //rechazado
          $rechazado=true;
          }
        }
      }

      if($approved && $canceled){
        $estado="L"; //parcialmente pagado
      }
      if($approved && $rechazado){
        $estado="L"; //parcialmente pagado
      }

      if($approved && !$rechazado && !$canceled){
        $estado="C"; //cobrado
      }

      if(!$approved &&  ($rechazado || $canceled)){
        $estado="A"; //cobrado
      }

     }//pagos null
     
      
    } catch (Exception $e) {
       $this->numError=80;
        $this->descError=$e->getMessage();
    }
    salir:
    return $estado;
  }


}
?>