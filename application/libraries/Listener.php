<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Listener {
	private $m;
	private $permisos=array();	
	private $userconect;
  private $transportencode=false; //el resultado de la query, se envia decodificada
  private $definiciones=array();
	 function __construct(){     
		$this->m =& get_instance();    //--------------
    $this->m->load->model('mservice');
    
    }
  
function get($definicionsql,$params=null,$encode=null){
  date_default_timezone_set('America/Argentina/Buenos_Aires');    
    $this->m->fwxml->numError=0;
    $this->m->fwxml->descError="";
    $this->m->fwxml->arrResponse=array();
    $codificarRs=$this->transportencode;
    if(isset($encode)){
      $codificarRs=(bool)$encode;
    }
    
    try {    
      $rs=$this->m->mservice->getprepared($definicionsql,$params);  
      
      $this->m->fwxml->arrResponse['enc']=(int)$codificarRs;
      $this->m->fwxml->arrResponse['rs']=($codificarRs)?base64_encode(json_encode($rs)):$rs;

      if($this->m->mservice->numError()!=0){
         $this->m->fwxml->numError=2;
         $this->m->fwxml->descError=$this->m->mservice->descError();      
      }
    } catch (Exception $e) {
       $this->m->fwxml->numError=1;
       $this->m->fwxml->descError=$e->getMessage();     
    }
     $this->m->fwxml->ResponseJson();
  }
  //cargo las queris
  public function load_defs($definiciones)
  {
    $this->definiciones=$definiciones;
  }

  public function get_transform($defname,$where)
  {
    $definicionsql=$this->transform($defname,$where);
    if($definicionsql!=""){
      $this->get($definicionsql);
    }
  }
  //los campos en las querys  solo deben contener llaves , letras y numeros
  public function transform($defname,$where){
    //busco todas las subcadenas que aparezcan con llaves y las reemplazo por los indice que haya
    //aquellos indices que no existan, quedaran vacios
    //ejemplo select * from where {campo1} and {campo2}->select * from where A=1 and B like '%algo%'
    $query=$this->definiciones[$defname];
    if($query=="") return "";
  //var_dump($where);
    preg_match_all('/{[a-zA-Z1-9]+}/', $query, $matches, PREG_OFFSET_CAPTURE); 

    /*var_dump( $matches);
    die();*/
    if(count($matches)>0){
      $campos=$matches[0]; //aqui se guardar todos los patrones de encerradas por llaves encontradas
      for($c=0;$c<count($campos);$c++){
        $campo=$campos[$c][0];
          //elimino las llaves
           $indexname=preg_replace('/({)|(})/','',$campo);
          //reemplazo el campo por la expresion que viene en el where, sino la dejo vacia
          if(isset($where[$indexname])){
            $query= str_replace($campo,$where[$indexname],$query);
            //var_dump($query);
          }else{
            $query= str_replace($campo,"",$query);
            //var_dump($query);
          }
       //   echo $query;
      }
    }
    //var_dump($query);
    return $query;
  }

	

}
?>