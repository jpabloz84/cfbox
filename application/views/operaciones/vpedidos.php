<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 date_default_timezone_set('America/Argentina/Buenos_Aires');    
$fecha_actual=date("d/m/Y")
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<style type="text/css">
    .bg-danger {
        background-color:#ca2b2b !important;
    }
    .bg-warning{
        background-color:#9f903c !important;
        
    }
    .bg-success{
        background-color: #457730 !important;
    }

    a{
            color: antiquewhite;
    }
    
    .panel-body {
    background:black;
    }
    .panel-body h5{
        color: #9d9d9d;
    }
</style>
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <input type="hidden" id="base_url"  value="<?=base_url()?>" />    
    
    <!-- begin row -->
    <div class="row panelquery">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-panel1">  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">                    
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">CONSULTA DE PEDIDOS</h4></div>             
                            <div class="panel-body" id="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">   
                                <div class="col-md-2">                                    
                                    <input type="text" class="form-control" id="patron_nro_pedido" placeholder="nro. pedido" />
                                 </div>
                                 <div class="col-md-4">                                    
                                    <input type="text" class="form-control" id="patronnombres" placeholder="apellido y/o nombres"  />
                                 </div>          
                                 <div class="col-md-2">                                    
                                    <input type="text" class="form-control" id="patron_fecha" placeholder="fecha" value="<?=$fecha_actual?>" />
                                 </div>
                                 <div class="col-md-2 col-xs-7">
                                     <select class="form-control" id="estado">
                                        <option value="">TODOS</option>
                                        <?php foreach ($estados as  $estado) {
                                            $e=$estado['estado'];
                                            $descestado=$estado['descripcion'];
                                          echo "<option value='$e'>$descestado</option>";
                                        }
                                        ?>
                                    </select>
                                 </div>
                                 <div class="col-md-2 col-xs-5">
                                    <div class="btn-group">
                                         <button type="button" class="btn btn-sm btn-primary btn-block"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> buscando..." id="bntBuscar">BUSCAR&nbsp;<i class="fa fa-search"></i>                                    
                                        </button>
                                    </div>
                                </div>
                                                              
                            </div>                                                        
                            <div class="form-group" id="tpl-table-query">
                            </div>                            
                            </form>
                            </div>
           </div>
        </div>
        <!-- end col-12 -->
    <!-- tabla --> 
    </div>
    <!-- begin row -->
    <div class="row" id="row-body-pedido">
    <!-- tabla --> 
    </div>
  

</div>
<!-- end #content -->

<div id="modal-estado-pedidos" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <input type="hidden" id="estado-selected" value="">
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:white">
                <h4 class="modal-title">Seleccione un nuevo estado</h4>
            </div>
            <div class="box-body">                
                    <div class="form-group col-md-12">                        
                        <ul class="nav nav-pills nav-stacked nav-sm m-b-0">
                            <?php 
                            foreach ($estados as $est):
                                            $e=$est['estado'];
                                            $descestado=$est['descripcion'];
                                            $btnclass=$est['btnclass'];?>
                                            <li><a href="javascript:;" name="estado-select" item="<?=$e?>"><i class="fa fa-fw m-r-5 fa-circle text-<?=$btnclass?>"></i> <?=$descestado?></a></li>
                            
                            <?php endforeach;?>

                        </ul>
                    </div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-default pull-left" id="btnsalir_estado" data-dismiss="modal">Salir</button>
                <button type="button" class="btn btn-success pull-right" id="btnconfirmar" data-loading-text="<i class='fa fa-spinner fa-spin '></i> guardando...">Confirmar</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade in" id="modalValidarCuponCUC_QRCode" >
   <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">ESCANEAR QR</h4>
            </div>
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <div id="mainbody">

                        <div id="outdiv" style="text-align: center;">
                        </div>
                        <div id="result"></div>
                        <canvas id="qr-canvas" width="800" height="600"></canvas>
                        <!--<input class="form-control input-lg" type="text" id="txtCuc_QRCode">-->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Salir</a>
                
            </div>
        </div>
    </div>
</div>

 

  
  <script src="<?=base_url()?>js/pedidos.js" type="text/javascript"></script>  
  <script src="<?=BASE_FW?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
  <script src="<?=BASE_FW?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js" type="text/javascript"></script>

  
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>

App.restartGlobalFunction();
App.setPageTitle('Consulta de pedidos | Encargame.ar');

$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/pedidos");
                         })    
                        
                        
                    })
                });
            });
        });
    });
});


function inicializacion_contexto()
{
      
olista=new CheckoutclientesView({el:$('#tpl-table-query'),base_url:"<?=base_url()?>"}); 
     
 $("#patron_nro_pedido").keypress(function(e){
    if(e.which==13){
    consultar();
    }
    return teclaentero(e)
});     
 
}//inicializacion contexto



$("#patron_fecha").datepicker({
     todayHighlight: false,
     format: 'dd/mm/yyyy',
     language: 'es',
     autoclose: true
    });







</script>
<!-- ================== END PAGE LEVEL JS ================== -->