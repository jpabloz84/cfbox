<?php

class Xlslib{
  private $numError;
  private $descError;
  
  function __construct()
  { $this->numError=0;
    $this->descError='';    
    require_once dirname(__FILE__).'/excel/excel_reader2.php';
    //$objeto= new Spreadsheet_Excel_Reader($path_archivo_excel,false);
    $objeto= new Spreadsheet_Excel_Reader();
    $CI=& get_instance();
    $CI->xlslib=$objeto;
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

  function read($pathfilexls)
  {
    $this->numError=0;
    $this->descError='';
    $exito=false;
    $data=null;
    try{
      $errorInfo="";
      $data =  new Spreadsheet_Excel_Reader($pathfilexls,false,"UTF-8");
    }catch (Exception $e) {
        $this->numError=98;
        $this->descError=$e->getMessage();
    }
    return $data;
  }

}
?>