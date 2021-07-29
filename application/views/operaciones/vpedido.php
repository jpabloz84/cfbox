<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$default="";
$conf=array();
foreach ($visitante->get_conf() as $var) {
$conf[$var['variable']]=$var['valor'];
} 
$id_empresa=$visitante->get_id_empresa();
// Para controlar las versones - comentar para producción
$version = rand(0, 10000);


?>


<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/jquery.dataTables.min.css" rel="stylesheet" />

<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/template" id="tpl-table-list">     
    <div class="table-responsive">
    <table id="data-table" class="table table-condensed table-hover" style="color:#707478">
                            <thead>
                                <tr>
                                    <th>PRODUCTO</th>                                        
                                    <th>STOCK</th>                                        
                                    <th>PRECIO</th>
                                    <th>CANTIDAD</th>
                                    <th>-</th>                                        
                                </tr>
                            </thead>
                            <tbody>                                                               
        <% _.each(ls, function(elemento) {  
        
        var strproducto="("+elemento.get('nro_tipo') + ") -" + elemento.get('producto')
        if(elemento.get('categoria')!="" && elemento.get('categoria')!=null)
        {
            strproducto=strproducto + " " + elemento.get('categoria')
        }
        var strstock='no mueve stock este '+elemento.get('tipo')
        if(elemento.get('mueve_stock')==1)
        {
            strstock=elemento.get('stock')+' '+elemento.get('fraccion_plural')+' de este '+elemento.get('tipo')
        }

        %>
        <tr>        
            <td><%=strproducto%></td>
            <td><%=strstock%></td>            
            <td>$ <%=elemento.get('importe_tipo').toFixed("2") %></td>        
            <td>
                <%
                if(elemento.get('mueve_stock')==1)
                {%>
                    <input type='text' value='1' class="form-control" name='inp-cant' id='inp-cant-<%=elemento.cid %>'  style='width:68px'/>
                <%}else
                {%>
                <input type='hidden' value='1' name='inp-cant' id='inp-cant-<%=elemento.cid %>' />
                <label>1</label>
                <%}%>
            </td>        
            <td>
                <button type="button" class="btn btn-xs btn-primary" name='selecItem' id="seleccionar-<%=elemento.cid %>">
                    agregar
                    <i class="fa fa-arrow-right"></i>
                </button>            
            </td>
        </tr>
        <% }); %>
        </tbody>
    </table>  
    </div>  
</script>


<style type="text/css">
    .popover{
    max-width:100%;
}
</style>

<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <input type="hidden" id="id_localidad" value="<?=$id_loc_default?>"/>
    <input type="hidden" id="id_cliente" value="<?=$id_cliente?>"/>
    <input type="hidden" id="base_url" value="<?=base_url()?>" />
    <input type="hidden" id="base_fw" value="<?=BASE_FW?>" />
    <input type="hidden" id="id_empresa" value="<?=$visitante->get_id_empresa()?>"/>
    <input type="hidden" id="id_usuario_vende" value="0"/>
    <input type="hidden" id="id_pedido" value="<?=$id_pedido?>"/>
    <input type="hidden" id="id_sucursal" value="<?=$visitante->get_id_sucursal()?>"/>
<!-- begin de tabs row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="active" id="li-default-tab-carrito"><a href="#default-tab-carrito" data-toggle="tab">CARRO DE COMPRA (F7)</a></li>
                <li class="" id="li-default-tab-envio"><a href="#default-tab-envio" data-toggle="tab">CLIENTE Y ENVIO (F8)</a></li>
                
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="default-tab-carrito">
                    <div class="row">
                    <div class="panel panel-success">
                       <div class="panel-heading">                        
                        <div class="btn-group pull-right" data-toggle="buttons" id="btn-accion">
                               <a href="javascript:;" class="btn btn-primary btn-sm"  id="btn-manual-busqueda">
                                    <i class="fa fa-file-archive-o"></i> BUSQUEDA MANUAL (F6)
                                </a>
                                <a href="javascript:;" class="btn btn-primary btn-sm"  id="btn-info">
                                    <i class="fa fa-info"></i> 
                                </a>
                         </div>
                         <h4 class="panel-title" id="text-buscador" >SELECCIÓN DE PRODUCTOS</h4>
                       </div>             
                        <div class="panel-body bg-green text-white" id="panel-body-inputs">
                             <form class="form-horizontal" action="/" method="POST"  id='form-standard' autocomplete="off">
                                <div class="form-group">
                                    <div class="col-md-6">
                                            <input type="text" class="form-control" id="buscador"  placeholder="buscador...">
                                    </div>
                                    <div class="col-md-3">
                                            <select class="form-control" id="tipo" >
                                                <option value="">TODOS</option>
                                                    <?php 
                                                    $selected="selected='selected'";
                                                    foreach ($tipos_productos as $tipo) {
                                                        if($tipo['default']==1)
                                                        $default=$tipo['id_tipo'];

                                                        if($tipo['id_tipo']==$default){
                                                            echo "<option value='".$tipo['id_tipo']."' $selected >".$tipo['tipo']."</option>";
                                                        }else{
                                                            echo "<option value='".$tipo['id_tipo']."' >".$tipo['tipo']."</option>";
                                                        }

                                                        
                                                    }
                                                     ?>
                                            </select>
                                    </div>                    
                                  
                               </div>               
                            </form>                            
                        </div>
                </div>
                <!--panel succes end-->
                 </div>   
                 <!-- end row productos seleccion -->    
                    <!-- begin row carrito-->    
                    <div class="row" id="tpl_opera_carrito">
                    </div>
                    <!-- end row carrito -->
                    <!-- begin row pagar-->    
                    <div class="row">
                        <div class="col-md-2 col-sm-2">
                            <h5><i class="fa fa-shopping-cart"></i> TOTAL $<span name="total_parcial">0</span> </h5>
                        </div>
                      
                       
                        <div class="col-md-7 col-sm-7" >
                        </div>
                        <div class="col-md-3 col-sm-3">                        
                            <p class="text-right m-b-0">
                            <a href="#default-tab-envio" data-toggle="tab" class="btn btn-sm btn-primary m-r-5 "  onclick="ActivarTab('default-tab-envio')">                                
                            DATOS DEL CLIENTE Y ENVIO (F8)
                            <i class="fa fa-chevron-circle-right"></i>
                            </a>
                            </p>
                        </div>
                        
                    </div>
                    <!-- end row pagar -->     
                </div>
                <div class="tab-pane fade" id="default-tab-envio">               
                    <!-- begin row -->    
                        <div class="row" >
                            <div class="col-md-12" >  
                          <div class="panel panel-info">
                           <div class="panel-heading">
                            <div class="panel-heading-btn">                 
                            </div>
                                <h4 class="panel-title">
                                  DATOS DEL CLIENTE Y ENVIO
                                  <span id="spansaldo" style="display: none">- SALDO A FAVOR $<span id="saldoafavor"></span></span>               
                                </h4>
                           </div>             
                                <div class="panel-body bg-aqua text-white" id="panel-body-checkout">
                                    
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- end row -->
                    <!-- begin row carrito return-->    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="btn-group btn-group-justified" >
                                <a href="#default-tab-carrito" data-toggle="tab" class="btn btn-sm btn-primary m-r-5 "  onclick="ActivarTab('default-tab-carrito')">
                                    <i class="fa fa-chevron-circle-left"></i>
                                    CARRO DE COMPRAS (F7)
                                </a>                                                
                             </div>
                        </div>
                        <div class="col-md-5" ></div>
                        <div class="col-md-3" >
                            <div class="btn-group btn-group-sm" id="btn-acciones" >                            
                            <button type="button" class="btn btn-sm btn-success" id="btn-pendiente" onclick="valida_orden()" data-loading-text="<i class='fa fa-spinner fa-spin '></i> procesando..."><i class="fa fa-save"></i>GUARDAR PEDIDO</button>
                             </div>
                        </div>
                    </div>
                </div>
                <!--tab default-tab-envio end -->

            </div>
        </div>
    </div>
<!-- end row tabs  -->

<input type="hidden" id="tipos_productos_default" value="<?=$default?>">
</div>
<!-- end #content -->
<div id="modal-manual-busqueda" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
            <h4 class="modal-title" id="classModalLabel">
              Buscar  productos
            </h4>
      </div>
      <div class="modal-body">
            <form class="form-horizontal" action="/" method="POST" id="form-avanzada" autocomplete="off" >
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label>ID</label>
                                        <input type="text" class="form-control" id="inp_nro_tipo" placeholder="identificador del producto">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="inp_nombre" placeholder="nombre del producto">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Categoria</label>
                                        <select class="form-control" id="inp_id_categoria">
                                                                <option value=''>TODAS</option>
                                                                <?php foreach ($categorias as  $cat) {
                                                                    $id_categoria=$cat['id_categoria'];
                                                                    $categoria=$cat['categoria'];
                                                                  echo "<option value='$id_categoria'>$categoria</option>";
                                                                }
                                                                ?>
                                         </select> 
                                    </div>                    
                                </div>
                                <div class="form-group">
                                    <div class="col-md-8">
                                           <label>Codigo de barras</label>
                                            <input type="text" class="form-control" id="inp_codbarras" placeholder="ingrese codigo">                               
                                    </div>
                                    <div class="col-md-4"> 
                                    
                                        <button type="button" class="btn btn-primary btn-block" id="btn-buscar" data-loading-text="<i class='fa fa-spinner fa-spin '></i> buscando...">
                                            <i class="fa fa-search"></i>
                                        Buscar</button>
                                    </div>
                                </div>
                                 <div class="form-group" id="tpl-table-query">
                                 </div>
                            </form>
        
      </div>
      <div class="modal-footer">
        <div class="row">
            <div class="col-md-6">
                <div class="form-check form-check-inline text-left">
                    <input class="form-check-input" type="checkbox" id="modalvisible" value="1">
                    <label class="form-check-label" for="modalvisible">mantener ventana</label>
                    <input class="form-check-input" type="checkbox" id="seachvisible" value="1">
                    <label class="form-check-label" for="seachvisible">mantener resultados</label>
                </div>                
            </div>            
            <div class="col-md-3">                
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    Cerrar
                </button>                
            </div>            
        </div>        
      </div>
    </div>
  </div>
</div>


    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="<?=base_url()?>js/pedido_carrito.js?v=<?php echo $version ?>"></script>
<script src="<?=base_url()?>js/checkout.js?v=<?php echo $version ?>"></script>
<script src="<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js"></script>


<!-- ================== END PAGE LEVEL JS ================== -->
<script type="text/javascript">


$(document).ready(function(){   
if(!isMobile()){
    $("#buscador").focus();
}  
 
})

$('#modal-manual-busqueda').on('hidden.bs.modal', function (e) { 
    if(!isMobile()){   
  $("#buscador").focus();
    }
})
$("#btn-manual-busqueda").click(function(){
    
    click_btnmanual()
})

</script>
