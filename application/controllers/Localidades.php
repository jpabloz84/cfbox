<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Localidades extends CI_Controller {
	
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
            $this->fwxml->arrResponse=$this->mservice->get($tabla,$arrcol,$condicion,$orden);
            
        }else
        {
        $this->fwxml->numError=2;
        $this->fwxml->descError="No inició sesión";
        }
    }
        $this->fwxml->ResponseJson();

   }//get
        
}//service class

?>