<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Test_fpdf extends CI_Controller {

	function  __construct()
	{  
		parent::__construct();
		//$this->load->library('session');		
		//$this->load->helper('url');
		//$this->load->library('Fwxml');		
		$this->load->model("mservice");

	}
	 function index()
	{
		

/*
# Create the image 
 $img = imagecreate("2480", "3508"); 
 imagecolorallocate($img,0,0,0); 
 $c = imagecolorallocate($img,70,70,70); 
 imageline($img,0,0,2480 ,3508,$c2); 
 imageline($img,2480,0,0,3508,$c2); 

$white = imagecolorallocate($img, 255, 255, 255); 
imagettftext($img, 9, 0, 1, 1, $white, "VERDANA.TTF", $html); 

# Display the image 
header("Content-type: image/jpeg"); 
imagejpeg($img); 
*/

		require_once("TCPDF/tcpdf_include.php");

// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		//$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('Coffee APP');
		$pdf->SetTitle('comprobantes');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(0, 0, 0);

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// add a page
$pdf->AddPage();


$pdf->writeHTML($html, true, false, true, false, '');
//$pdf->writeHTML($ht, true, false, true, false, '');
//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
// reset pointer to the last page
$pdf->lastPage();
//Close and output PDF document
$pdf->Output('example_1.pdf', 'I');

}

}
