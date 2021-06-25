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
    <script type="text/javascript" src="<?php echo base_url(); ?>js/backbone-utiles.js?v=1"></script>
    <script src="<?php echo BASE_FW ?>js/utilesV1.11.js"></script>
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
    
	<!-- ================== END BASE JS ================== -->

	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="<?php echo BASE_FW ?>assets/js/apps.js"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->
    

	<script>
var arrCookies={}
var timeoutCookies=8000;
var arrCookies={};
var arrCookiesDet={};
var feini='';
var fefin='';
setInterval(getlastinfo, 30000);
setInterval(evalstatususer, 25000);
setInterval(evalstatuscert, 25000);
setInterval(evalstatuspedidos, 5000);

var timeoutCookies=8000;
/*variables de uso global*/


var globales=Array();
var winparent= new fwmodal();
var base_url='<?=base_url()?>index.php/'
var ofw=new fw(base_url)
$.getScript('<?=BASE_FW?>assets/plugins/gritter/js/jquery.gritter.js').done(function() {

})

	$(document).ready(function() {
        //handleCheckPageLoadUrl("<?php echo base_url();?>index.php/panel/presentacion");
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
var last_nro_pedido=0
function init()
{
    
 var last_nro_pedido=$("#last_nro_pedido").val()
arrCookies['loginon']=1;
arrCookies['last_notification']=0;
arrCookies['crt_vencidos']=0;
arrCookies['nro_pedido']=last_nro_pedido;
globales['bancos']=new Bancos();
globales['tarjetas']=new Tarjetas(null,{habilitadas:true});
globales['tipo_pagos']=new Tipopagos();
globales['tipo_items']=new Tiposproductos();
globales['operaciones_tipo']=new Tiposoperaciones();
var fecha = new Date();
feini=fecha.getDate()+"-"+(fecha.getMonth()+1)+"-"+fecha.getFullYear()+" "+fecha.getHours()+":"+fecha.getMinutes()+": "+fecha.getSeconds();

console.log("Inicio:"+feini);
}


function evalstatususer()
{
	if(arrCookies['loginon']!=1)
	{  var fecha = new Date();
        fefin=fecha.getDate()+"-"+(fecha.getMonth()+1)+"-"+fecha.getFullYear()+" "+fecha.getHours()+":"+fecha.getMinutes()+": "+fecha.getSeconds();
        winparent.alert("La sesión se cerró por tiempo de inactividad"," Cierre de sesión automatica"+fefin,1)
        console.log("Se cerró automaticamente. Fecha inicio:"+feini+", Fecha fin:"+fefin);
		window.location.href='<?php echo base_url(); ?>index.php/login/salir'
	}
}

var certnoti=false
function evalstatuscert()
{  
    //si no fue notificado y hay certificados vencidos, que muestre la notificacion
    if(arrCookies['crt_vencidos']==1 && !certnoti)
    {  certnoti=true;
        $.gritter.options.time=30000;
        var rs=arrCookiesDet['crt_vencidos'];
        for(i in rs){
            var id_cert=rs[i].id_certificado
            var nombre=rs[i].nombre 
            var fe_vencimiento=rs[i].fe_vencimiento 
            $.gritter.add({
                title: 'Certificado ID '+id_cert+' pronto a vencerse',
                text: nombre+' '+fe_vencimiento+'. Notifíquelo y actualicelo'
            });    
        }
        
    }
}


function evalstatuspedidos()
{  
    //si no fue notificado y hay certificados vencidos, que muestre la notificacion
    if(arrCookies['nro_pedido']>last_nro_pedido)
    {  last_nro_pedido= +arrCookies['nro_pedido']
        $.gritter.options.time=10000;
        $.gritter.add({
                title: 'Nuevo pedido',
                text: 'Ha ingresado el encargo nro. '+nro_pedido
            });    
        
    }
}

function getlastinfo()
{

var strxml="<\?xml version='1.0' \?><body>";
$.each(arrCookies, function( key, value ) {
	strxml+="<variable name='"+key+"'>"+value+"</variable>";  
});
strxml+="</body>";


$.ajax({
    url: "<?php echo base_url(); ?>index.php/panel/lastinfo/",
    dataType: "json",type: 'POST',data: { strXml:strxml} ,
    timeout: timeoutCookies, // sets timeout to 6 seconds
    error: function(dataResponse){
        // will fire when timeout is reached
    },
    success: function(jsonResponse){         
    	   if(typeof jsonResponse.numerror=="undefined"){                
                return
                }
        
        var numError=parseInt(jsonResponse.numerror);
        var descError=jsonResponse.descerror;                
        if(numError == 0)
        { 
          var dataJson = jsonResponse.data;
            $.each(dataJson, function( key, val ) {                
                var variable=val.variable
                var valor=val.valor
                    if(arrCookies[variable]!= valor)
                    {
                        if(typeof val.det !="undefined"){
                            arrCookies[variable]=valor
                            arrCookiesDet[variable]=val.det

                        }else{
                            arrCookies[variable]=valor                      
                        }
                    
                    }           
                
                }); 
        }
    
	}});//ajax

}//getlastinfo

</script>
</body>
</html>