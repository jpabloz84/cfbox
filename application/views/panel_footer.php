<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Para controlar las versones - comentar para producción
$version = rand(0, 10000);
?>
    </div>

   

	<!-- end page container -->
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/underscore-min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/backbone-min.js"></script>
    <script src="<?php echo BASE_FW ?>assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>/js/croppie/croppie.min.js"></script>        
    <script type="text/javascript" src="<?php echo base_url(); ?>js/backbone-utiles.js?v=2"></script>
    <script src="<?php echo BASE_FW ?>js/utilesV1.12.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
        <script src="<?php echo BASE_FW ?>assets/crossbrowserjs/html5shiv.js"></script>
        <script src="<?php echo BASE_FW ?>assets/crossbrowserjs/respond.min.js"></script>
        <script src="<?php echo BASE_FW ?>assets/crossbrowserjs/excanvas.min.js"></script>
	<![endif]-->
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery-hashchange/jquery.hashchange.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script src="<?php echo BASE_FW ?>assets/plugins/jquery-cookie/jquery.cookie.js"></script>
    <script src="<?php echo BASE_FW ?>assets/plugins/gritter/js/jquery.gritter.js"></script>
    
	<!-- ================== END BASE JS ================== -->

	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="<?php echo base_url() ?>assets/js/apps.js?v=1"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->
    <script src="<?php echo base_url(); ?>js/notificaciones.js?v=1"></script>
	<script>

var feini='';
var fefin='';

/*variables de uso global*/
window.localStorage.setItem("id_sucursal",<?=$visitante->get_id_sucursal()?>);
window.localStorage.setItem("id_empresa",<?=$visitante->get_id_empresa()?>);
window.localStorage.setItem("id_prov_default",<?=$id_prov_default?>);

var globales=Array();
var winparent= new fwmodal();
var base_url='<?=base_url()?>index.php/'
var ofw=new fw(base_url)
$(document).ready(function() {        
		App.init();
        init();

});

$(document).on('click', '[data-click="deslogearse"]', function() {
        $('#modal-dialog').modal('show');
});        
$(document).on('click', '[data-click="deslogearse2"]', function() {
        $('#modal-dialog').modal('show');
});
$(document).on('click', '[data-click="salir"]', function() {
        window.location.href='<?php echo base_url(); ?>index.php/login/salir'
});
$(document).on('click', '[data-click="btn-actuar-como"]', function() {
       $("#modal-actuar-como").modal("show")
});    
 <?php  if($visitante->get_id_rol()==1){?>
$(document).on('click', '#btn-cerrar-actuar-como', function() {
    var id_empresa=$("#id_empresa_actuar_como").val()
     $.ajax({
    url: "<?php echo base_url(); ?>index.php/panel/setempresa/",
    dataType: "json",type: 'POST',data: { id_empresa:id_empresa} ,
    error: function(dataResponse){
        // will fire when timeout is reached
    },
    success: function(jsonResponse){  
        if(jsonResponse.numerror==0){
            window.location.href="./panel"
        }
    }
    })//ajax
});//clcik actuar como   
<?php } ?> 

function init()
{

globales['bancos']=new Bancos();
globales['tarjetas']=new Tarjetas(null,{habilitadas:true});
globales['tipo_pagos']=new Tipopagos();
globales['tipo_items']=new Tiposproductos();
globales['operaciones_tipo']=new Tiposoperaciones();
globales['sucursales']=new Sucursales();
var oLocalidades=new Localidades();

oLocalidades.cargarAsync(window.localStorage.getItem("id_prov_default"));
globales['localidades']=oLocalidades


var fecha = new Date();
feini=fecha.getDate()+"-"+(fecha.getMonth()+1)+"-"+fecha.getFullYear()+" "+fecha.getHours()+":"+fecha.getMinutes()+": "+fecha.getSeconds();
console.log("Inicio:"+feini);
start_notify()
}




</script>
</body>
</html>