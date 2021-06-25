<?php
//habilitar extensiones en php.ini de SOAP y OPENSSL
//Ademas, tener sincronizado el reloj con AFIP, para hacer esto ir (en windows) a 
//cambiar configuracion fecha y hora y habilitar para que el reloj de la PC sincronice con la hora de internet 
date_default_timezone_set('America/Argentina/Buenos_Aires');
class wsafip{
public $numError=0;
public $urlwsa;
public $urlwsfe;
public $service;
public $WSDL_wsaa;
public $WSDL_wsfe;
public $cuit;
public $certificado;
public $id_certificado=0;
public $clave_privada;
public $clave;
public $descError='';
public $uniqueId;
public $client_wsa;
public $client_wsfe;
public $reg_request=array();
public $reg_respuesta=array(); //array con los comprobates APROBADOS
public $token;
public $sing;
public $pathTRA;  //= realpath(".")."/TRA.xml";
public $pathTMP;  //=realpath(".")."/tmp.xml";
public $pathCarpeta;
public $logPedidoCAERegistro=array(); //guarda los problemas POR COMPROBANTES que devolvio afip en el momento de pedir cae
public $logPedidoCAE=array(); //guarda los problemas que devolvio afip en el momento de pedir cae
public $expirationTime;
public $ID_ENVIO;
private $db;
private $certDb;
private $limite_comp_c=5000; //limite establecido para hacer facturas C
//parametro:: path del archivo PHP que invoco la clase
function __construct($params){
  
$pathCarpeta="";
if($this->isWindows()){
    $pathCarpeta=str_replace("/", "\\",$params['pathcarpeta']);
  }else{
    $pathCarpeta=str_replace("\\", "/",$params['pathcarpeta']);
  }

$this->pathCarpeta=$pathCarpeta;
$this->WSDL_wsaa=$pathCarpeta."wsaa.wsdl";  
$this->pathTRA=$pathCarpeta."TRA.xml";
$this->pathTMP=$pathCarpeta."tmp.xml";
}


function init($id_certificado,$db){
    $this->id_certificado=(int)$id_certificado;
    $this->db=$db;
   try {

    $rs=$this->db->getwhere("certificados","id_certificado=".$this->id_certificado);  
    $this->certDb=$rs[0];

  

      if($this->certDb['testing']==1)
    {
    $this->service="TESTING";
    $this->urlwsa="https://wsaahomo.afip.gov.ar/ws/services/LoginCms";
    $this->urlwsfe="https://wswhomo.afip.gov.ar/wsfev1/service.asmx";
    $this->WSDL_wsfe=$this->pathCarpeta."wsfev1Homo.wsdl";
    }else
    {
    $this->service="PRODUCCION";
    $this->urlwsa="https://wsaa.afip.gov.ar/ws/services/LoginCms";
    $this->urlwsfe="https://servicios1.afip.gov.ar/wsfev1/service.asmx";
    $this->WSDL_wsfe=$this->pathCarpeta."wsfev1.wsdl"; 
    }
    
    $this->cuit=$this->certDb['cuit'];
    $pathcerts='';
    if($this->isWindows()){
    $pathcerts=str_replace("afip\\", "",$this->pathCarpeta);
    $this->certificado=$pathcerts.str_replace("/", "\\",$this->certDb['path']);
    $this->clave_privada=$pathcerts.str_replace("/", "\\",$this->certDb['path_key']);
    }else{
    $pathcerts=str_replace("afip/", "",$this->pathCarpeta);
    $this->certificado=$pathcerts.$this->certDb['path'];
    $this->clave_privada=$pathcerts.$this->certDb['path_key'];
    }
    
    
    $this->clave=$this->certDb['clave'];

   $this->client_wsa=new SoapClient($this->WSDL_wsaa, array(
          'soap_version'   => SOAP_1_2,
          'location'       => $this->urlwsa,
          'trace'          => 1,
          'exceptions'     => 0
          ));

   $this->client_wsfe=new SoapClient($this->WSDL_wsfe, array(
          'soap_version'   => SOAP_1_2,
          'location'       => $this->urlwsfe,
          'trace'          => 1,
          'exceptions'     => 0
          )); 
     
   } catch (Exception $e) {
    $evento['evento']='Error al inicializar';
    $evento['detalle']=$e->getMessage();    
    $this->regevent($evento);
   }

}//fin del constructor

//obtiene obtiene informacion del certificado
//en indices del array validFrom_time_t , validTo_time_t
function getInfocert()
{ 
  return openssl_x509_parse(file_get_contents($this->certificado));
}

function set_limite_fc($valor=0){
  $this->limite_comp_c=$valor;
}

function CreateTRA()
{
  $TRA = new SimpleXMLElement(
    '<?xml version="1.0" encoding="UTF-8"?>' .
    '<loginTicketRequest version="1.0">'.
    '</loginTicketRequest>');
  $TRA->addChild('header');
  $TRA->header->addChild('uniqueId',date('U'));
  $TRA->header->addChild('generationTime',date('c',date('U')-60));
  $TRA->header->addChild('expirationTime',date('c',date('U')+60));
  $TRA->addChild('service',"wsfe"); //selecciono el servicio de los tanto que hay en afip
  $TRA->asXML($this->pathTRA);
}
#==============================================================================

function SignTRA()
{
$this->numError=0;
$this->descError="";
$CMS="";
try{
 $tra=str_replace("\\","/",$this->pathTRA);
 $tmp=str_replace("\\","/",$this->pathTMP); 

 // $tra=$this->pathTRA;  
  //$tmp=$this->pathTMP; 
 //$cert="file://".str_replace("\\","/",$this->certificado);  
 //$pk="file://".str_replace("\\","/",$this->clave_privada); 
 $cert='file://'.realpath($this->certificado);  
 $pk='file://'.realpath($this->clave_privada); 
  $STATUS=openssl_pkcs7_sign($tra,$tmp,$cert,array($pk,$this->clave),array(),!PKCS7_DETACHED);
  if (!$STATUS) {
  $this->numError=1;
  $this->descError=openssl_error_string();
  $evento['evento']='SignTRA';
  $evento['detalle']=$this->descError;    
  $this->regevent($evento);
  return ;
  }
  $inf=fopen($this->pathTMP, "r");
  $i=0;
  
  while (!feof($inf)) 
    { 
      $buffer=fgets($inf);
      if ( $i++ >= 4 ) {$CMS.=$buffer;}
    }
  fclose($inf);
  unlink($this->pathTMP);
}catch (Exception $e) {
    $this->numError=-1;
    $this->descError=$e->getMessage();            
    $evento['evento']='SignTRA';
    $evento['detalle']=$this->descError;    
    $this->regevent($evento);
}

return $CMS;
}
#==============================================================================
function CallWSAA($CMS)
{
 $this->numError=0;
 $this->descError="";
 try{
  $results=$this->client_wsa->loginCms(array('in0'=>$CMS));
  if (is_soap_fault($results)) 
  {
    $this->numError=2;
    $this->descError="CallWSAA->SOAP Fault: ".$results->faultcode."\n".$results->faultstring."\n";
    $evento['evento']='CallWSAA error '.$this->numError;
    $evento['detalle']=$this->descError;    
    $evento['request']= $this->client_wsa->__getLastRequest();
    $evento['response']= $this->client_wsa->__getLastResponse();
    $this->regevent($evento);
    return ;
  }

   }catch (Exception $e) {
      $this->numError=-2;
      $this->descError=$e->getMessage();            
      $evento['evento']='CallWSAA exceptions .'.$this->numError;
      $evento['detalle']=$this->descError;    
      $this->regevent($evento);
      return;
  }
 
  
  
  return $results->loginCmsReturn;
}

function regevent($campos)
{ $campos['fecha']=date('Y-m-d H:i:s');
  $this->db->insertar('afip_logs',$campos);  
}



function obtener_TA()
{
date_default_timezone_set('America/Argentina/Buenos_Aires');    
$this->numError=0;
$this->descError="";
$exito=false;
$pathfoldertmp=str_replace("\\","/",$this->pathCarpeta);
  #==============================================================================
  ini_set("soap.wsdl_cache_enabled", "0");
if (!file_exists(str_replace("\\","/",$this->certificado))) {
  $this->numError=3;
  $this->descError="No se pudo encontrar o abrir el certificado";
  $evento['evento']='obtener_TA ->'.$this->numError;
  $evento['detalle']=$this->descError;    
  $this->regevent($evento);
return $exito;
}
if (!file_exists(str_replace("\\","/",$this->clave_privada))) {
  $this->numError=4;
  $this->descError="No se pudo encontrar el archivo de clave privada";
  $evento['evento']='obtener_TA ->'.$this->numError;
  $evento['detalle']=$this->descError;    
  $this->regevent($evento);
  return $exito;
}
if (!file_exists($this->WSDL_wsaa)) {
$this->numError=5;
$this->descError="No se pudo encontrar el archivo WSDL";
$evento['evento']='obtener_TA ->'.$this->numError;
  $evento['detalle']=$this->descError;    
  $this->regevent($evento);
  return $exito;
}

$this->CreateTRA();
if($this->numError!=0)
{  
return $exito;
}

$CMS=$this->SignTRA();
if($this->numError!=0)
{  
return $exito;
}
$TA=$this->CallWSAA($CMS);
if($this->numError!=0)
{ 
return $exito;
}
 try {
  $campos['ta']= $TA;
  $xml=$this->loadXML($TA);
  $header=$xml->header;
  $expirationTime=$header->expirationTime;
  $f=new DateTime($expirationTime);
  $fecha_expiracion=$f->format(DATE_ISO8601);
  $campos['login_vencimiento']=$fecha_expiracion;
  $this->db->actualizar_registro('certificados',$campos, array('id_certificado' =>$this->id_certificado));
  $rs=$this->db->getwhere("certificados","id_certificado=".$this->id_certificado);  
  $this->certDb=$rs[0];   
  $exito=true;
 } catch (Exception $e) {
  $this->numError==31;
  $this->descError=="Error al guardar TA".$e->getMessage();
 }

return $exito;
}

//verifica si el TA esta vigente o no, tomando los datos que lee desde el archivo
function TA_expirado()
{
date_default_timezone_set('America/Argentina/Buenos_Aires');    
$this->numError=0;
$this->descError="";
$exito=false;
try{
 $rs=$this->db->getwhere("certificados","id_certificado=".$this->id_certificado." and login_vencimiento>=now()");  

  if(!count($rs)>0){
    $exito=true;
  }
 }catch (Exception $e) {
    $this->numError= -2;
    $this->descError=$e->getMessage();
    $evento['evento']='TA_expirado . Exception. ->'.$this->numError;
    $evento['detalle']=$this->descError;    
    $this->regevent($evento);
    
  }

  return $exito;
}

//verifica si el TA esta vigente o no
function verificaTA()
{
date_default_timezone_set('America/Argentina/Buenos_Aires');    
$this->numError=0;
$this->descError="";
$exito=false;
try{
$expirado=$this->TA_expirado();

  if($expirado){
  $rs1=$this->db->query("select DATE_FORMAT( NOW(),'%d-%m-%Y %H:%i:%s') as login_vencimiento_str from certificados where id_certificado=".$this->id_certificado);  
    $evento['evento']='verificaTA ';
    $evento['detalle']='Expiró el '.$rs1[0]['login_vencimiento_str'];
    $this->regevent($evento);
  }
  $exito=!$expirado;
 }catch (Exception $e) {
    $this->numError= -10;
    $this->descError=$e->getMessage();
    $evento['evento']='verificaTA. Exepcion '.$this->numError;
   $evento['detalle']=$this->descError;    
   $this->regevent($evento);    
  }

  return $exito;
}



function cargar_TA()
{
$exito=false;
$this->numError=0;
$this->descError="";
$TA=null;
 try{
    
    $rs=$this->db->getwhere("certificados","id_certificado=".$this->id_certificado);  
    $this->certDb=$rs[0];
    $TA=simplexml_load_string($this->certDb['ta']); 
   if($TA==null){
    $this->numError=6;
      $this->descError=" El TA no fue encontrado ->.".$this->numError.".id_certificado ".$this->id_certificado;      
      $evento['evento']='cargar_TA';
      $evento['detalle']=$this->descError;    
      $this->regevent($evento);    
   }else{
    $this->token=strval($TA->credentials->token);
    $this->sign=strval($TA->credentials->sign);
    $this->uniqueId=strval($TA->credentials->uniqueId);
    $header=$TA->header;    
    $this->expirationTime=$header->expirationTime;
    $exito=true;
   }
    
  }
  catch (Exception $e) {
  $this->numError= -3;
  $this->descError=$e->getMessage();  
  $evento['evento']='cargar_TA. Exception.->'.$this->numError;
  $evento['detalle']=$this->descError;    
  $this->regevent($evento);
 }
return $exito;
}


//recupera el ultimo comprobante aprobado
function RecuperaLastCMP($ptovta, $tipocbte)
{
$this->numError=0;
$this->descError="";
 try{
  $results=$this->client_wsfe->FECompUltimoAutorizado(array('Auth' =>  array('Token'=> $this->token,'Sign'=> $this->sign,'Cuit'=> $this->cuit),'PtoVta' => $ptovta,'CbteTipo' => $tipocbte));
  $hayerror=$this->CheckErrors($results, 'FECompUltimoAutorizado');
  if ($hayerror)
  {
    return -1; 
  }
  return $results->FECompUltimoAutorizadoResult->CbteNro;

 }catch (Exception $e){
  $this->numError=-8;
  $this->descError=$e->getMessage();
  $evento['evento']='RecuperaLastCMP. Exception ->'.$this->numError;
  $evento['detalle']=$this->descError;    
  $this->regevent($evento);
 } 
  return -1; 
}//RecuperaLastCMP



function CheckErrors($results, $method)
{
$this->numError=0;
$this->descError="";
try{
    if (is_soap_fault($results))
    { $this->numError=7;
      $this->descError=" MENSAJE DEL WEBSERVICE AFIP: Fault:  ".$results->faultcode."\nFaultString: ".$results->faultstring."\n";
        $evento['evento']="CheckErrors. $method ->".$this->numError;
        $evento['detalle']= $this->descError; // (String)$results;    
        $evento['request']= $this->getLastRequestXml();
        $evento['response']= $this->getLastResponseXml();
        $this->regevent($evento);
      return true;
    }
    if ($method == 'FEDummy') {return false;}
    
    $XXX=$method.'Result';
    
    if (isset($results->$XXX->Errors->Err->Code))
    {if($results->$XXX->Errors->Err->Code!=0){
      $this->numError=$results->$XXX->Errors->Err->Code;
      $this->descError="MENSAJE DEL WEBSERVICE AFIP: Method=".$method."\n errcode=".$results->$XXX->Errors->Err->Code." errmsg=%s\n".$results->$XXX->Errors->Err->Msg;
        $evento['evento']="CheckErrors. $XXX  ->".$this->numError;
        $evento['detalle']=$this->descError; //(String)$results;
        $evento['request']= $this->getLastRequestXml();
        $evento['response']= $this->getLastResponseXml();
        $this->regevent($evento);
      return true;
      }
    }
  }catch(Exception $e)
  {
    $this->numError=-7;
    $this->descError=$e->getMessage();
    $evento['evento']="CheckErrors. Exception ->".$this->numError;
    $evento['detalle']=$this->descError;    
    $this->regevent($evento);
    return true;
  }
    return false;
}





function enviar($cmp,$tipo_cbte,$pto_vta)
{
$exito=false;
$this->numError=0;
$this->descError="";
$this->reg_respuesta=array();  
$this->logPedidoCAERegistro=array();
$this->logPedidoCAE=array();
$imp_total=(float)$cmp['ImpTotal'];
$monto_comprobante_c=0;

$rs=$this->db->getwhere("configuracion","variable='total_max_comp_c'");  
$total_max_comp_c=(float)$rs[0]['valor'];
  //si es factura C o recibo C
  if($tipo_cbte==11 || $tipo_cbte==13 || $tipo_cbte==15)
  {
    if($imp_total<$total_max_comp_c){
      $cmp['DocTipo']=99;
      $cmp['DocNro']=0;
    }
    $cmp['ImpOpEx']=0;
    $cmp['ImpTotConc']=0;
    $cmp['ImpIVA']=0;
  }

/* para cargar tributos para regimnes especiales, completar el array, y poner su suma de importes en $cmp['ImpTrib']
$tributo=array();
  $tributo['Id']=99; //99->otros impuestos
  $tributo['Desc']='Impuesto provincial';
  $tributo['BaseImp']=3000;
  $tributo['Alic']=2;
  $tributo['Importe']=$impALICUOTA;
  $arrTributos['Tributo'][]=$tributo;
 $cmp['Tributos']=$arrTributos; */


/*if(!$this->cargar_TA()) //se debe cargar por afuera
{
return false;
}*/


try {
  $results=$this->client_wsfe->FECAESolicitar(
  array('Auth' => array('Token' => $this->token,'Sign'  => $this->sign,'Cuit'  => $this->cuit),
        'FeCAEReq' => array('FeCabReq' => array('CantReg' =>1,'PtoVta' => $pto_vta,'CbteTipo'=>$tipo_cbte),
           'FeDetReq' => array('FECAEDetRequest' =>$cmp))
       ));
  
$evento['evento']="Envio de comprobante";
$evento['detalle']="ptovta $pto_vta , tipocbte $tipo_cbte (nro_comp:".$cmp['CbteDesde'].")";
$evento['request']= $this->getLastRequestXml();
$evento['response']= $this->getLastResponseXml();
$this->regevent($evento);


  
   if($this->CheckErrors($results,'FECAESolicitar'))
   {    
    return false;
   }
 
    if(isset($results->FECAESolicitarResult->Errors->Err->Code))
    {   
      if($results->FECAESolicitarResult->Errors->Err->Code!="0")
      {
      $this->numError=$results->FECAESolicitarResult->Errors->Err->Code;
        $this->descError=" MENSAJE DEL WEBSERVICE AFIP: Percode: ".$results->FECAESolicitarResult->Errors->Err->Code."\nPerrmsg: ".$results->FECAESolicitarResult->Errors->Err->Msg."\n";
        $evento['evento']="enviar ->".$this->numError;
        $evento['detalle']=$this->descError; //(string)$results;
        $evento['request']= $this->getLastRequestXml();
        $evento['response']= $this->getLastResponseXml();
        $this->regevent($evento);
     return false;  
      }
      
    }
    //var_dump($results->FECAESolicitarResult);
    //consulto por la cabecera
    if($results->FECAESolicitarResult->FeCabResp->Resultado =="R")
    {
      $arrComp=array();
      $arrComp['cuit']=$results->FECAESolicitarResult->FeCabResp->Cuit;
      $arrComp['PtoVta']=$results->FECAESolicitarResult->FeCabResp->PtoVta;
      $arrComp['CbteTipo']=$results->FECAESolicitarResult->FeCabResp->CbteTipo;
      $arrComp['FchProceso']=$results->FECAESolicitarResult->FeCabResp->FchProceso;
      $arrComp['CantReg']=$results->FECAESolicitarResult->FeCabResp->CantReg;
      $arrComp['Resultado']=$results->FECAESolicitarResult->FeCabResp->Resultado;
      $arrComp['Reproceso']=$results->FECAESolicitarResult->FeCabResp->Reproceso;
      $this->logPedidoCAE[]=$arrComp;
      $comp=$results->FECAESolicitarResult->FeDetResp->FECAEDetResponse;
      $arrResp=array();        
      $arrResp['Concepto']=$comp->Concepto;
      $arrResp['FchProceso']=$results->FECAESolicitarResult->FeCabResp->FchProceso;
      $arrResp['DocTipo']=$comp->DocTipo;
      $arrResp['DocNro']=$comp->DocNro;
      $arrResp['CbteDesde']=$comp->CbteDesde;
      $arrResp['CbteHasta']=$comp->CbteHasta;
      $arrResp['Resultado']=$comp->Resultado;
      $arrResp['CAE']=$comp->CAE;
      $arrResp['CbteFch']=$comp->CbteFch;
      $arrResp['CAEFchVto']=$comp->CAEFchVto;
      $arrResp['Obs']=$comp->Observaciones;
      $evento['evento']="enviar. Rechazo de envio";
      $evento['detalle']="rechazado"; //(string)$results;
      $evento['request']= $this->getLastRequestXml();
      $evento['response']= $this->getLastResponseXml();
      $this->regevent($evento);
      $this->logPedidoCAERegistro=$arrResp;
      return false;
    }//fin del if que analiza errores cuando el resultado del envio arrojo R
else   
    {      //print_r($results->FECAESolicitarResult);
        $comp=$results->FECAESolicitarResult->FeDetResp->FECAEDetResponse;
        $arrResp=array();
        $arrResp['Concepto']=$comp->Concepto;
        $arrResp['FchProceso']=$results->FECAESolicitarResult->FeCabResp->FchProceso;
        $arrResp['DocTipo']=$comp->DocTipo;
        $arrResp['DocNro']=$comp->DocNro;
        $arrResp['CbteDesde']=$comp->CbteDesde;
        $arrResp['CbteHasta']=$comp->CbteHasta;
        $arrResp['Resultado']=$comp->Resultado;
        $arrResp['CAE']=$comp->CAE;
        $arrResp['CbteFch']=$comp->CbteFch;
        $arrResp['CAEFchVto']=$comp->CAEFchVto;
        $this->reg_respuesta[]=$arrResp;    
        $exito=true;
    }//fin del if  
}catch (Exception $e) {
  $this->numError=-15;
  $this->descError=$e->getMessage();  
  $evento['evento']="enviar. Exception ->".$this->numError;
  $evento['detalle']=$this->descError;    
  $this->regevent($evento);
}
return $exito;
} //enviar

public function getLastRequestXml()
{
    return $this->client_wsfe->__getLastRequest();
}
    /**
     * Get most recent XML Response returned from SOAP server
     *
     * @return string
     */
public function getLastResponseXml()
{
    return $this->client_wsfe->__getLastResponse();
}


//prepara un comprobante con los valores iniciales
function prepare()
{

$cmp=array();
$cmp['Concepto']=3; //1 producto 2 servicios 3 productos y servicios
$cmp['DocTipo']=80;
$cmp['DocNro']='';
$cmp['CbteDesde']=0;
$cmp['CbteHasta']=0;
$cmp['CbteFch']='';
$cmp['ImpTotal']=0;
$cmp['ImpTotConc']=0;
$cmp['ImpNeto']=0;
$cmp['ImpOpEx']=0;
$cmp['ImpTrib']=0;
$cmp['ImpIVA']=0;
$cmp['FchServDesde']='';   //Fecha de inicio  del abono para el  servicio a facturar. Dato  obligatorio  para concepto  2 o 3 (Servicios / Productos y Servicios). Formato  yyyymmdd
$cmp['FchServHasta']='';
$cmp['FchVtoPago']=''; //Fecha de vencimiento  del  pago servicio a facturar. Dato  obligatorio  para concepto  2 o 3 (Servicios / Productos y Servicios). Formato  yyyymmdd. Debe ser igual o posterior a la fecha delcomprobante
$cmp['FchVtoPago']='';
$cmp['MonId']='PES';
$cmp['MonCotiz']=1;
/*$cmp['Tributos']=array();
$cmp['Iva']=array();*/

  
return $cmp;

}


//dado SOLO UN comprobante, obtiene su CAE, ademas es optimizado para facturaciones al instante
function obtener_CAE($cuit,$tipo_cbte,$pto_vta,$cbte,$imp_total,$imp_tot_conc,$imp_neto,$neto_21,$neto_105,$ImpTrib,$imp_op_ex,$fecha_cbte,$FchVencPago,$impIVA,$impALICUOTA,$FchServDesde,$FchServHasta,$Concepto)
{

$cmp=array();

$cmp['Concepto']=$Concepto; //1 producto 2 servicios 3 productos y servicios
$cmp['DocTipo']=80;
$cmp['DocNro']=$cuit;
$cmp['CbteDesde']=$cbte;
$cmp['CbteHasta']=$cbte;
$cmp['CbteFch']=$fecha_cbte;
$cmp['ImpTotal']=$imp_total;
$cmp['ImpTotConc']=$imp_tot_conc;
$cmp['ImpNeto']=$imp_neto;
$cmp['ImpOpEx']=$imp_op_ex;
$cmp['ImpTrib']=$impALICUOTA;
$cmp['ImpIVA']=($tipo_cbte==11)?0:$impIVA;
$cmp['FchServDesde']=$FchServDesde;   //Fecha de inicio  del abono para el  servicio a facturar. Dato  obligatorio  para concepto  2 o 3 (Servicios / Productos y Servicios). Formato  yyyymmdd
$cmp['FchServHasta']=$FchServHasta;
$cmp['FchVtoPago']=$FchVencPago; //Fecha de vencimiento  del  pago servicio a facturar. Dato  obligatorio  para concepto  2 o 3 (Servicios / Productos y Servicios). Formato  yyyymmdd. Debe ser igual o posterior a la fecha delcomprobante
$cmp['FchVtoPago']='';
$cmp['MonId']='PES';
$cmp['MonCotiz']=1;


if($impIVA>0 && !($tipo_cbte==2  && $tipo_cbte==3 && $tipo_cbte==7 && $tipo_cbte==8 && $tipo_cbte==11 && $tipo_cbte==12 && $tipo_cbte==13 && $tipo_cbte==15)){
/*Aca entran facturas A y B*/
$arrAli=array();
$arrAli['AlicIva']=array();
$arrTributos['Tributo']=array();
if($impALICUOTA>0)
{
/*<IvaTipo><Id>3</Id><Desc>0%</Desc><FchDesde>20090220</FchDesde><FchHasta>NULL</FchHasta></IvaTipo><IvaTipo><Id>4</Id><Desc>10.5%</Desc><FchDesde>20090220</FchDesde><FchHasta>NULL</FchHasta></IvaTipo><IvaTipo><Id>5</Id><Desc>21%</Desc><FchDesde>20090220</FchDesde><FchHasta>NULL</FchHasta></IvaTipo><IvaTipo><Id>6</Id><Desc>27%</Desc><FchDesde>20090220</FchDesde><FchHasta>NULL</FchHasta></IvaTipo><IvaTipo><Id>8</Id><Desc>5%</Desc><FchDesde>20141020</FchDesde><FchHasta>NULL</FchHasta></IvaTipo><IvaTipo><Id>9</Id><Desc>2.5%</Desc><FchDesde>20141020</FchDesde><FchHasta>NULL</FchHasta></IvaTipo>*/

  $tributo=array();

  $tributo['Id']=99; //99->otros impuestos
  $tributo['Desc']='Impuesto provincial';
  $tributo['BaseImp']=3000;
  $tributo['Alic']=2;
  $tributo['Importe']=$impALICUOTA;
  $arrTributos['Tributo'][]=$tributo;

 $cmp['Tributos']=$arrTributos; 
}

  // echo $imp_neto." = ".$imp105."+".$imp21;



  if($neto_21>0)
  {   
    $arr21=array();
    $arr21['Id']=5; //->21%
    $arr21['BaseImp']=$neto_21;
    $arr21['Importe']=round($neto_21*0.21,2);
    $arrAli['AlicIva'][]=$arr21;
    
  }
  if($neto_105>0)
  {    
    $arr105=array();
    $arr105['Id']=4; //->10.5%
    $arr105['BaseImp']=$neto_105;
    $arr105['Importe']=round($neto_105*0.105,2);
    $arrAli['AlicIva'][]=$arr105;    
  }

if($tipo_cbte!=6){ $cmp['Iva']=$arrAli; }  #Solo Si es Factura A

/*<AlicIva>
 <Id>5</Id> --> 21%
 <BaseImp>100</BaseImp>
<Importe>21</Importe>
 </AlicIva>
<AlicIva>
 <Id>4</Id> --> 10.5%
 <BaseImp>50</BaseImp>
 <Importe>5.25</Importe>
 </AlicIva>*/
 
//$cmp['Iva']=$arrAli;

/*
  if($impALICUOTA>0){

      // <Tributos>
      // <Tributo>
      //     <Id>99</Id>
      //     <Desc>Impuesto Municipal Matanza</Desc>
      //     <BaseImp>150</BaseImp>
      //     <Alic>5.2</Alic>
      //     <Importe>7.8</Importe>
      // </Tributo>
      // </Tributos>

      $idALIC='';
      $descALIC='';
      $BaseImpALIC='';
      $alicALIC='';
      $impALIC='';
      

      $arrTrib['ImpTrib']=array();

      $arrTrib['ImpTrib']['Id']=$idALIC;
      $arrTrib['ImpTrib']['Desc']=$descALIC;
      $arrTrib['ImpTrib']['BaseImp']=$BaseImpALIC;
      $arrTrib['ImpTrib']['Alic']=$alicALIC; 
      $arrTrib['ImpTrib']['Importe']=$impALIC;

      $cmp['Tributos']['Tributo']=$arrTrib; 
  }*/


}

//si es factura B no discrimina IVA
  if($tipo_cbte==6 || $tipo_cbte==8)
  {
  $cmp['ImpNeto']=0;
  $cmp['ImpTotConc']=$imp_neto;
  $cmp['ImpIVA']=0;
  }
  //si es factura C o recibo C
  if($tipo_cbte==11 || $tipo_cbte==13 || $tipo_cbte==15)
  {
    if($imp_total<1000){
      $cmp['DocTipo']=99;
      $cmp['DocNro']=0;
    }
    $cmp['ImpOpEx']=0;
    $cmp['ImpTotConc']=0;
    $cmp['ImpIVA']=0;
  }
  
//$cmp['CbtesAsoc']=array();
//$cmp['Tributos']='';
//if($tipo_cbte==6 || $tipo_cbte==8) $cmp['Iva']=''; 
/*
Array para informar las alícuotas y sus importes asociados a un comprobante <AlicIva>. 
Para comprobantes tipo C y Bienes Usados – Emisor Monotributista no debe informar el array.N 
*/
//$cmp['Opcionales']=array();


  //echo "fecha pago:".$cmp['FchVtoPago'];

  // echo "ImpTotal".$cmp['ImpTotal'].", debe ser igual a la suma de ImpTotConc".$cmp['ImpTotConc']."+ ImpNeto".$cmp['ImpNeto']." + ImpOpEx".$cmp['ImpOpEx']." + ImpTrib".$cmp['ImpTrib']." + ImpIVA.".$cmp['ImpIVA'];
  // var_dump($cmp);
  // die();

 $this->reg_respuesta=array();  
 $this->logPedidoCAERegistro=array();
 $this->logPedidoCAE=array();
 

  if(!$this->cargar_TA())
    {
      return false;
    }
  
  if($this->TA_expirado())
    {if(!$this->obtener_TA())
        {
          return false;
        }
        //en caso de que haya traido el TA, q lo vuelva a cargar a las clase para poder tomar sus datos
      if(!$this->cargar_TA())
      {
        return false;
      }
      
    }

  $cant_reg=1;
  $results=$this->client_wsfe->FECAESolicitar(
    array('Auth' => array('Token' => $this->token,'Sign'  => $this->sign,'Cuit'  => $this->cuit),
          'FeCAEReq' => array('FeCabReq' => array('CantReg' => $cant_reg,'PtoVta' => $pto_vta,'CbteTipo'=>$tipo_cbte),
             'FeDetReq' => array('FECAEDetRequest' =>$cmp))
         ));
   //print_r($results);
   $this->CheckErrors($results,'FECAESolicitar');
 
  if ( $results->FECAESolicitarResult->Errors->Err->Code != 0 )
    {

     $this->descError=" MENSAJE DEL WEBSERVICE AFIP: Percode: ".$results->FECAESolicitarResult->Errors->Err->Code."\nPerrmsg: ".$results->FECAESolicitarResult->Errors->Err->Msg."\n";
          return false;
    }
    
//consulto por la cabecera
    if($results->FECAESolicitarResult->FeCabResp->Resultado =="R")
    {
      $arrComp=array();
      $arrComp['cuit']=$results->FECAESolicitarResult->FeCabResp->Cuit;
      $arrComp['PtoVta']=$results->FECAESolicitarResult->FeCabResp->PtoVta;
      $arrComp['CbteTipo']=$results->FECAESolicitarResult->FeCabResp->CbteTipo;
      $arrComp['FchProceso']=$results->FECAESolicitarResult->FeCabResp->FchProceso;
      $arrComp['CantReg']=$results->FECAESolicitarResult->FeCabResp->CantReg;
      $arrComp['Resultado']=$results->FECAESolicitarResult->FeCabResp->Resultado;
      $arrComp['Reproceso']=$results->FECAESolicitarResult->FeCabResp->Reproceso;
      $this->logPedidoCAE[]=$arrComp;

        $comp=$results->FECAESolicitarResult->FeDetResp->FEDetResponse;
        $arrResp=array();
        
        $arrResp['Concepto']=$comp->Concepto;
        $arrResp['FchProceso']=$results->FECAESolicitarResult->FeCabResp->FchProceso;
        $arrResp['DocTipo']=$comp->DocTipo;
        $arrResp['DocNro']=$comp->DocNro;
        $arrResp['CbteDesde']=$comp->CbteDesde;
        $arrResp['CbteHasta']=$comp->CbteHasta;
        $arrResp['Resultado']=$comp->Resultado;
        $arrResp['CAE']=$comp->CAE;
        $arrResp['CbteFch']=$comp->CbteFch;
        $arrResp['CAEFchVto']=$comp->CAEFchVto;
        $arrResp['Obs']=$comp->Obs->Observaciones;
        $this->logPedidoCAERegistro=$arrResp;
        
      
      return false;
    }//fin del if que analiza errores cuando el resultado del envio arrojo R
else   
    {         
        $comp=$results->FECAESolicitarResult->FeDetResp->FECAEDetResponse;
        $arrResp=array();
        $arrResp['Concepto']=$comp->Concepto;
        $arrResp['FchProceso']=$results->FECAESolicitarResult->FeCabResp->FchProceso;
        $arrResp['DocTipo']=$comp->DocTipo;
        $arrResp['DocNro']=$comp->DocNro;
        $arrResp['CbteDesde']=$comp->CbteDesde;
        $arrResp['CbteHasta']=$comp->CbteHasta;
        $arrResp['Resultado']=$comp->Resultado;
        $arrResp['CAE']=$comp->CAE;
        $arrResp['CbteFch']=$comp->CbteFch;
        $arrResp['CAEFchVto']=$comp->CAEFchVto;
        
        $this->reg_respuesta[]=$arrResp;
    
      return true;
    }//fin del if
   
    return false;


}//fin de obetener 1 CAE


function loadXML($data) {
  $xml = @simplexml_load_string($data);
  if (!is_object($xml))
    throw new Exception('Error en la lectura del XML',1001);
  return $xml;
}
//servidor de aplicacion
function getStatusSrv()
{
  $this->numError=0;
  $this->descError="";
  try {
    $results=$this->client_wsfe->FEDummy();
    return $results->FEDummyResult->AppServer;    
    } catch (Exception $e) {
    $this->numError=-10;
    $this->descError=$e->getMessage();
    }  
    return ;
}
//servidor de base de datos
function getStatusDb()
{
  $this->numError=0;
  $this->descError="";
  try {
    $results=$this->client_wsfe->FEDummy();
    return $results->FEDummyResult->DbServer;    
    } catch (Exception $e) {
    $this->numError=-11;
    $this->descError=$e->getMessage();;
    }  
    return ;
}
//servidor de autenticidad
function getStatusAuth()
{
  $this->numError=0;
  $this->descError="";
  try {
    $results=$this->client_wsfe->FEDummy();
    return $results->FEDummyResult->AuthServer;    
    } catch (Exception $e) {
    $this->numError=-15;
    $this->descError=$e->getMessage();
    }
    return ;
}

function FEParamGetTiposTributos()
{
  $this->numError=0;
  $this->descError="";
  try {
  $results=$this->client_wsfe->FEParamGetTiposTributos(array('Auth' =>  array('Token'    => $this->token,'Sign'=> $this->sign,'Cuit'=> $this->cuit)));

  if($this->CheckErrors($results, 'FEParamGetTiposTributos'))
  {
    return;
  }
  if ($results->FEParamGetTiposTributos->Errors->Err->Code != 0 )
  {
    $this->numError=$results->FEParamGetTiposTributos->Errors->Err->Code;
    $this->descError="Percode:".$results->FEParamGetTiposTributosResult->Errors->Err->Code."\nPerrmsg:".$results->FEParamGetTiposTributosResult->Errors->Err->Msg."\n"; 
    return; 
  }  

   return $results->FEParamGetTiposTributosResult->ResultGet;
  } catch (Exception $e) {
    $this->numError=-11;
    $this->descError=$e->getMessage();
  }
  return; 
}

function FEParamGetTiposIva()
{
  $this->numError=0;
  $this->descError="";
  try {
  $results=$this->client_wsfe->FEParamGetTiposIva(array('Auth' =>  array('Token'    => $this->token,'Sign'=> $this->sign,'Cuit'=> $this->cuit)));

  if($this->CheckErrors($results, 'FEParamGetTiposTributos'))
  {
    return;
  }
  if ($results->FEParamGetTiposIva->Errors->Err->Code != 0 )
  { $this->numError=$results->FEParamGetTiposIva->Errors->Err->Code;
    $this->descError="Percode:".$results->FEParamGetTiposIvaResult->Errors->Err->Code."\nPerrmsg:".$results->FEParamGetTiposIvaResult->Errors->Err->Msg."\n"; 
    return; 
  }
  return $results->FEParamGetTiposIvaResult->ResultGet;    
  } catch (Exception $e) {
   $this->numError= -12;
    $this->descError=$e->getMessage(); 
  }
  return;
  
}

function FEParamGetPtosVenta()
{
  $this->numError=0;
  $this->descError="";
  try {
  $results=$this->client_wsfe->FEParamGetPtosVenta(array('Auth' =>  array('Token'    => $this->token,'Sign'=> $this->sign,'Cuit'=> $this->cuit)));

  if($this->CheckErrors($results, 'FEParamGetPtosVenta'))
  {
    return;
  }
  if(isset($results->FEParamGetPtosVenta)){
    if ($results->FEParamGetPtosVenta->Errors->Err->Code != 0 )
    { $this->numError=$results->FEParamGetPtosVenta->Errors->Err->Code;
      $this->descError="Percode:".$results->FEParamGetPtosVentaResult->Errors->Err->Code."\nPerrmsg:".$results->FEParamGetPtosVentaResult->Errors->Err->Msg."\n"; 
      return; 
    }  
  }
  
  return  $results->FEParamGetPtosVentaResult->ResultGet;    
  } catch (Exception $e) {
   $this->numError= -12;
    $this->descError=$e->getMessage(); 
  }
  return;
  
}

//indica si el servidor es windows
private function isWindows(){
  return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}


} //fin de la clase

?>