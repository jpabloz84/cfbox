<?php
class documento{
  private $numError;
  private $descError;
 private $html='';
  function __construct()
  { $this->numError=0;
    $this->descError='';    
    
  }
  function __destruct()
  { 

  }

  function parseXsl($pathxsl,$xmldata)
  {
    $this->numError=0;
    $this->descError='';
    $this->html='';
    try{
      $xml=new DOMDocument;
      $xml->loadXML($xmldata);
      //echo $xmldata;
      $xsl=new DOMDocument();
      $xsl->load($pathxsl);
    // Configuración del procesador
      $proc = new XSLTProcessor();
      $proc->importStyleSheet($xsl); // adjunta las reglas xsl
      $this->html=$proc->transformToXML($xml);
    }catch (Exception $e) {
        $this->numError=99;
        $this->descError=$e->getMessage();
    }
    return $this->html;
  }

  
   function numError()
   {
    return $this->numError;
   }

  function descError()
  {
    return $this->descError;
  }

  function save($pathfile='')
  {
    $this->numError=0;
    $this->descError='';
    $pathfileret='';
    try{
        if($pathfile=='')
        {
          $folder=getcwd()."/files/";
          $pathfileret=tempnam($folder, "tmp");
        }else
        {
          $pathfileret=$pathfile;
        }
        //si existe, lo borro
        if(file_exists($pathfileret)){
        unlink($pathfileret);    
        }
      
      file_put_contents($pathfileret,$this->html);
    }catch (Exception $e) {
        $this->numError=98;
        $this->descError=$e->getMessage();
    }
    return $pathfileret;
  }

}
?>