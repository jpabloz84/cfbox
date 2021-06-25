<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Para controlar las versones - comentar para producción
$version =rand(0, 10000);
?>  
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  

<link href="<?=BASE_FW?>assets/plugins/bootstrap-eonasdan-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
<style type="text/css">
    


</style>
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content">     
    
    <input type="hidden" id="version" value="<?=$version?>" />
    <input type="hidden" id="base_fw" value="<?=BASE_FW?>" />    
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">                        
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;BUSCA Y AGREGA TUS CUENTAS AQUI</h4>
            </div>             
            <div class="panel-body">
                
                    <!-- begin row -->
                    <div class="row" id="row-acceso-add" >       
                        
                    </div>
                    <!-- end row-12 -->
                </div>            
           </div>
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
    
   
   
    
</div>





<script src="<?=BASE_FW?>assets/plugins/bootstrap-daterangepicker/moment.js"></script>
<script src="<?=BASE_FW?>assets/plugins/bootstrap-eonasdan-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script> 
<script src="<?=base_url()?>js/accesos.js?v=<?php echo $version ?>"></script>
<!-- end #content -->

<script>
App.restartGlobalFunction();
App.setPageTitle('accesos| dadadmin');


$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    
                    
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-show-password/bootstrap-show-password.js').done(function(){
                        
                        $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js').done(
                            function() {
               $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js').done(function() {                
                            inicializacion_contexto();     
                            $('[data-click="panel-reload"]').click(function(){
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/accesos/");
                             })
                            });

                          });
                        });
                    });
                });
            });
        });
    });
});


</script>
<!-- ================== END PAGE LEVEL JS ================== -->