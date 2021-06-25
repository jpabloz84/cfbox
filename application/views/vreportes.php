<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-panel1">  
            <div class="panel-group" id="accordion">
                <div class="panel panel-inverse overflow-hidden">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                <i class="fa fa-plus-circle pull-right"></i> 
                                VENTAS - CONSULTA DE COMPROBANTES
                            </a>
                        </h3>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                
                                 <div class="col-md-3">
                                    <label>
                                        FECHA DESDE
                                    <input type="text" class="form-control" id="patronfe_desde" placeholder="fecha desde"  style="text-align:right"/>
                                    </label>
                                 </div>
                                 <div class="col-md-3">
                                    <label>
                                        FECHA HASTA
                                    <input type="text" class="form-control" id="patronfe_hasta" placeholder="fecha hasta"  style="text-align:right"/>
                                </label>
                                 </div>
                                 <div class="col-md-3" style="text-align:center">
                                     <label>TIPO DE COMPROBANTE                                    
                                    <select class="form-control" id="comprobante">    
                                        <option value="0">TODOS</option>
                                        <option value="-1">ORDENES DE PEDIDO</option>
                                        <?php  
                                            foreach ($tipo_comprobantes   as $value) {
                                                echo "<option value='".$value['id_tipo_comp']."'>".$value['tipo_comp']."</option>";
                                            }
                                        ?>
                                    </select>
                                    </label>
                                 </div>
                                 <div class="col-md-3" style="text-align:center">
                                     <label>ESTADOS
                                    <select class="form-control" id="estado">    
                                        <option value="">Todos</option>                                      
                                        <?php  
                                            foreach ($estados   as $value) {
                                                echo "<option value='".$value['estado']."'>".$value['descripcion']."</option>";
                                            }
                                        ?>
                                    </select>
                                    </label>
                                 </div>
                            </div>
                            <div class="form-group">                              
                                <div class="col-md-2">
                                    <label>
                                    ID comprobante
                                    <input type="text" class="form-control" id="patronid_comp" placeholder="numero"  style="text-align:right"/>
                                    </label>
                                </div>
                                <div class="col-md-2">
                                <label>
                                    Numero de comprobante
                                    <input type="text" class="form-control" id="patronnro_comp" placeholder="numero"  style="text-align:right"/>
                                </label>
                                </div>
                                <div class="col-md-4">
                                    <label>Talonario
                                    <select class="form-control" id="id_talonario">    
                                        <option value="">Seleccione...</option>                                      
                                        <?php  
                                            foreach ($talonarios   as $value) {
                                                $strtalonario=sprintf("%'.04d\n", $value['nro_talonario']);
                                                echo "<option value='".$value['id_talonario']."'>".$strtalonario." - ".$value['tipo_comp']."</option>";
                                            }
                                        ?>
                                    </select>
                                    </label>
                                </div>

                                <div class="col-md-2">
                                    <label>
                                    Ver Productos
                                    <input type="checkbox" id="patron_productos" name="patron_productos" class="form-control" >
                                    </label>
                                </div>                            
                                <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-success" id="btnExportar" onclick="exportar('comprobantes')">Generar reporte</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="panel panel-inverse overflow-hidden">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                <i class="fa fa-plus-circle pull-right"></i> 
                                CONSULTA DE PAGOS
                            </a>
                        </h3>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse">
                        <div class="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                
                                 <div class="col-md-3">
                                    <label>
                                        FECHA DESDE
                                    <input type="text" class="form-control" id="patronpagofe_desde" placeholder="fecha desde"  style="text-align:right"/>
                                    </label>
                                 </div>
                                 <div class="col-md-3">
                                    <label>
                                        FECHA HASTA
                                    <input type="text" class="form-control" id="patronpagofe_hasta" placeholder="fecha hasta"  style="text-align:right"/>
                                </label>
                                 </div>
                                 <div class="col-md-3" style="text-align:center">
                                     <label>TIPO PAGO
                                    <select class="form-control" id="patronpago_tipo">    
                                        <option value="0">TODOS</option>        
                                        <?php  
                                            foreach ($tipo_pagos   as $value) {
                                                echo "<option value='".$value['id_tipo_pago']."'>".$value['tipo_pago']."</option>";
                                            }
                                        ?>
                                    </select>
                                    </label>
                                 </div>
                                 <div class="col-md-3" style="text-align:center">
                                     <label>CAJA
                                    <select class="form-control" id="patronpago_caja">    
                                        <option value="0">TODAS</option>                                      
                                        <?php  
                                            foreach ($cajas   as $value) {
                                                echo "<option value='".$value['id_caja']."'>".$value['caja']." - ".$value['sucursal']."</option>";
                                            }
                                        ?>
                                    </select>
                                    </label>
                                 </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label>
                                    ID CLIENTE
                                    <input type="text" class="form-control" id="patronpago_id_cliente" placeholder="numero"  style="text-align:right"/>
                                    </label>
                                </div>
                                <div class="col-md-3">
                                <label>
                                    DOCUMENTO
                                    <input type="text" class="form-control" id="patronpago_nro_docu" placeholder="numero"  style="text-align:right"/>
                                </label>
                                </div>
                                <div class="col-md-3">
                                <label>
                                    CUIT
                                    <input type="text" class="form-control" id="patronpago_cuit" placeholder="numero"  style="text-align:right"/>
                                </label>
                                </div>
                                <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success"  onclick="exportar('pagos')">Generar reporte</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                 <div class="panel panel-inverse overflow-hidden">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                <i class="fa fa-plus-circle pull-right"></i> 
                                CONSULTA SORTEADOS
                            </a>
                        </h3>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse">
                        <div class="panel-body">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                
                                 <div class="col-md-3">
                                    <label>
                                        
                                    <input type="number" class="form-control" id="patronid_bingo" placeholder="id bingo"  style="text-align:right"/>
                                    </label>
                                 </div>
                                 <div class="col-md-3">
                                    <label>
                                    ganadores    
                                    <input type="radio" class="form-control" name="tipo_sorteado" value="ganadores" checked="checked" />
                                    </label>
                                 </div>
                                 <div class="col-md-3">
                                    <label>
                                    empates    
                                    <input type="radio" class="form-control" name="tipo_sorteado" value="empates" />
                                    </label>
                                 </div>
                                  <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success"  onclick="exportar('sorteados')">Generar reporte</button>
                                </div>                                
                            </div>
                           
                            </form>
                        </div>
                    </div>
                </div>
               
                
            </div>
        </div>
        <!-- end col-12 -->
    <!-- tabla --> 
    </div>
    <!-- begin row -->


</div>
<!-- end #content -->

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
var localurlx=window.location.href.split("#")[1];

App.restartGlobalFunction();
App.setPageTitle('Generación de reportes | CoffeBox APP');
var win= new fwmodal();
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;


$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js').done(function() {
               $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js').done(function() {
                            inicializacion_contexto();     
                            $('[data-click="panel-reload"]').click(function(){
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/reportes/");
                             })
                          });
                        });
                    });
                });
            });
        });
    });
});


function inicializacion_contexto()
{

$("#patronfe_desde").mask("99/99/9999");
                $("#patronfe_desde").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });

$("#patronfe_hasta").mask("99/99/9999");
                $("#patronfe_hasta").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });                
 $("#patronid_comp").keypress(function(e){return teclaentero(e)});   
 $("#patronnro_comp").keypress(function(e){return teclaentero(e)});   

/*reporte de pagos*/
$("#patronpagofe_desde").mask("99/99/9999");
                $("#patronpagofe_desde").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });

$("#patronpagofe_hasta").mask("99/99/9999");
                $("#patronpagofe_hasta").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });                
$("#patronpago_id_cliente").keypress(function(e){return teclaentero(e)});   
 $("#patronpago_nro_docu").keypress(function(e){return teclaentero(e)});                   
 $("#patronpago_cuit").keypress(function(e){return teclaentero(e)});                   


}//inicializacion contexto
function getparam_comprobantes(){

strXmlMod="";



if($("#patronfe_desde").val()!="")
strXmlMod+="<parametro name='fe_desde'>"+$("#patronfe_desde").val()+"</parametro>"

if($("#patronfe_hasta").val() !="")
strXmlMod+="<parametro name='fe_hasta'>"+$("#patronfe_hasta").val()+"</parametro>"

if($("#patronid_comp").val()!="")
strXmlMod+="<parametro name='id_comp'>"+$("#patronid_comp").val()+"</parametro>"

if($("#id_talonario").val()!="")
strXmlMod+="<parametro name='id_talonario'>"+$("#id_talonario").val()+"</parametro>"

if($("#estado").val()!="")
strXmlMod+="<parametro name='estado'>"+$("#estado").val()+"</parametro>"


if($("#comprobante").val()!="" && parseInt($("#comprobante").val())>0)
strXmlMod+="<parametro name='afip'>1</parametro><parametro name='id_tipo_comp'>"+$("#comprobante").val()+"</parametro>"

if($("#comprobante").val()!="" && parseInt($("#comprobante").val())<0)
strXmlMod+="<parametro name='afip'>0</parametro>"

return strXmlMod;
}//getparam_comprobantes

function getparam_pagos(){

strXmlMod="";
if($("#patronpagofe_desde").val()!="")
strXmlMod+="<parametro name='fe_desde'>"+$("#patronpagofe_desde").val()+"</parametro>"

if($("#patronpagofe_hasta").val() !="")
strXmlMod+="<parametro name='fe_hasta'>"+$("#patronpagofe_hasta").val()+"</parametro>"


if($("#patronpago_tipo").val()!="")
strXmlMod+="<parametro name='id_tipo_pago'>"+$("#patronpago_tipo").val()+"</parametro>"


if($("#patronpago_caja").val()!="")
strXmlMod+="<parametro name='caja'>"+$("#patronpago_caja").val()+"</parametro>"

if($("#patronpago_cuit").val()!="")
strXmlMod+="<parametro name='cuit'>"+$("#patronpago_cuit").val()+"</parametro>"

if($("#patronpago_id_cliente").val()!="")
strXmlMod+="<parametro name='id_cliente'>"+$("#patronpago_id_cliente").val()+"</parametro>"


if($("#patronpago_nro_docu").val()!="" && parseInt($("#patronpago_nro_docu").val())>0)
strXmlMod+="<parametro name='nro_docu'>"+$("#patronpago_nro_docu").val()+"</parametro>"


return strXmlMod;
}//getparam_pagos



function exportar(reporte)
{   
var strXmlMod="";
var strXml="<\?xml version='1.0' \?><body><reporte name='"+reporte+"'>"
if(reporte=="comprobantes")
strXmlMod=getparam_comprobantes();

if(reporte=="pagos")
strXmlMod=getparam_pagos();


strXml+=strXmlMod+'</reporte></body>'


 $.ajax({dataType: "xml",type: 'POST',url:'<?=base_url()?>index.php/reportes/exportar/',data: { strxml: strXml },success: function(xmlResponse)
            {
                var $xml = $(xmlResponse)             
                var numError=$xml.find('numError').text();
                var descError=$xml.find('descError').text();
                
                if(numError != 0)
                {
                    alert(descError)
                }else{
                 window.open("<?php echo base_url() ?>"+descError,'_blank');
                }
                
            },
            beforeSend: function(){
            spinnerStart($("panel-body"))
                },
            complete: function(){
            spinnerEnd($("panel-body"))
                }
           })
    return

}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->