<?php
use Dompdf\Dompdf;
class Pdf{
  private $numError;
  private $descError;
 private $html='';
  function __construct()
  { $this->numError=0;
    $this->descError='';    
    require_once dirname(__FILE__).'/dompdf/autoload.inc.php';
    $pdf=new DOMPDF();
    $CI=& get_instance();
    $CI->dompdf=$pdf;
  }
  function __destruct()
  { 

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
      
      file_put_contents($pathfileret,$this->html);
    }catch (Exception $e) {
        $this->numError=98;
        $this->descError=$e->getMessage();
    }
    return $pathfileret;
  }

}
?>