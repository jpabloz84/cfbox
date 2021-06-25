<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Service extends CI_Controller {
	private $numError=0;
    private $visitante=null;    
    private $descError="";
	function __construct(){
		parent::__construct();
        $this->load->model("mservice");
    }
    
    

  
   function get()
    {
    $this->visitante=unserialize($this->session->userdata('visitante'));
    
    $this->fwxml->numError=0;
    $this->fwxml->descError="";
    $this->fwxml->arrResponse=array();
        


    if(!isset($this->visitante) || $this->visitante==null)
    {
    $this->fwxml->numError=1;
    $this->fwxml->descError="Sin sesion";
    }else
    {           
        if($this->visitante->logueado())
        {
            
            $xmldata=$this->input->post('xmldata');
            $strxml=loadXML($xmldata);
            $res=$strxml->data;
            $tabla=$res['tbl'];
            $condicion=$res->condicion;
            $orden=$res->orden;
            $arrcol=array();
            foreach ($res->col as $col)            
            {
                $arrcol[]=$col;
            }
            $ret=$this->mservice->get($tabla,$arrcol,$condicion,$orden);
            if($this->mservice->numError()!=0)
            {
                $ret=array();
                 $this->fwxml->numError=$this->mservice->numError();
                 $this->fwxml->descError=$this->mservice->descError();
            }
            $this->fwxml->arrResponse=$ret;
            
        }else
        {
        $this->fwxml->numError=2;
        $this->fwxml->descError="No inició sesión";
        }
    }
        $this->fwxml->ResponseJson();

   }//get

    function numError()
     {
        return $this->numError;
     }

    function descError()
    {
        return $this->descError;
    }
        
}//service class

?>