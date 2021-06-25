<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$folderPrimario="application/libraries/uploadhandler/";
		//$folder=$folderPrimario."Zend/Io";
error_reporting(E_ALL | E_STRICT);
		require_once $folderPrimario."Zend/Io/Exception.php";
		require_once $folderPrimario."Zend/Io/Reader.php";
		require_once $folderPrimario."Zend/Io/FileReader.php";
		require_once $folderPrimario."Zend/Io/FileWriter.php";	
		require_once $folderPrimario."Zend/Io/StringReader.php";
		require_once $folderPrimario."Zend/Io/StringWriter.php";
	    
		//require_once all_php($folderPrimario."Zend/Io";
		require_once $folderPrimario."Zend/Media/id3v1.php";
		//require_once $folderPrimario."Zend/Media/id3/Exception.php";

//require('upload/UploadHandler.php');
//$upload_handler = new UploadHandler();

class Cmedia extends CI_Controller {
	private $visitante=null;
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other publick methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function  __construct()
	{  
		parent::__construct();
		$this->load->library('session');
		$this->load->library('User');
		$this->load->helper('url');
		$this->visitante=unserialize($this->session->visitante);
		if(!isset($this->visitante) || $this->visitante==null)
		{
		redirect(base_url()."index.php/Login");
		return;
		}

		if(!$this->visitante->logueado())
		{
		redirect(base_url()."index.php/Login");
		return;
		}
		
	}
	 function index()
	{
		
		
		$this->load->view('upload/vmedia');
		
	}

	 function upload()
	{
		//UploadHandler

		

		$this->load->library('uploadhandler/UploadHandler');
		$fileObjectUpload =$this->UploadHandler;
		//$upload_handler = new UploadHandler();
		//print_r($fileObjectUpload);
		
	}
}
