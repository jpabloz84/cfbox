<?php
defined('BASEPATH') OR exit('No direct script access allowed');


?>  
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content">     
    <input type="hidden"  id="id_empresa" value="<?php echo $visitante->get_id_empresa()?>">
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                        <div class="btn-group pull-left">
                        <button type="button" class="btn btn-success btn-xs" onclick="registrar_movimiento()">
                            <i class="fa fa-credit-card"></i>
                            REGISTRAR MOVIMIENTO
                        </button>
                        
                        </div>
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;ADMINISTRACIÓN DE CAJA</h4>
            </div>             
            <div class="panel-body" id="panel-body-saldos">
                    <div class="col-md-1">
                        <button  class="btn btn-warning btn btn-sm" id="btn-volver">
                            <i class="fa fa-reply"></i>
                            VOLVER</button>

                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-stats bg-black">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-money"></i></div>
                            <div class="stats-title">TOTAL CAJA</div>
                            <div class="stats-number">$<span id="saldo_caja"><?=number_format ($saldo_caja,2,".","")?></span></div>
                            <div class="stats-link">
                            <a href="javascript:;" ><i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        </div>
                    </div>                
                    <div class="col-md-4">
                        <div class="widget widget-stats bg-black">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-plus-square"></i></div>
                            <div class="stats-title">RECAUDACION HOY</div>
                            <div class="stats-number">$<span id="recaudacion"><?=number_format ($recaudacion,2,".","")?></span></div>
                            <div class="stats-link">
                            <a href="javascript:;" onclick=""><i class="fa fa-arrow-circle-o-right"></i></a>
                            </div>
                        </div>
                    </div>                   
                    <div class="col-md-4">
                        <div class="widget widget-stats bg-red">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-money"></i></div>
                            <div class="stats-title">DEBEN A LA FECHA</div>
                            <div class="stats-number">$<span id="recaudacion"><?=number_format ($deben,2,".","")?></span></div>
                            <div class="stats-link">
                            <a href="javascript:;" onclick="deben()"><i class="fa fa-arrow-circle-o-right"></i>&nbsp;CONSULTAR</a>
                            </div>
                        </div>
                    </div>                   
                </div>            
           </div>
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
    <!-- begin row -->
    <div class="row" id="row-pago-add" style="display: none">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">                        
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;CARGAR MOVIMIENTO</h4>
            </div>             
            <div class="panel-body" id="panel-body-cargar-movimiento">
                <form class="form-horizontal" action="/" method="POST" id='form-crea-pago'>                  
                <div class="form-group">                                        
                        <div class="col-md-2">
                            <label>TIPO DE MOVIMIENTO</label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                <select id="inp_movimiento" name="inp_movimiento"  class="form-control" onchange="cargar_items()">
                                <option value="ingreso">INGRESO DE DINERO</option>
                                <option value="egresos">SALIDA DE DINERO</option>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>MOVIMIENTOS</label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                <select id="inp_movimiento_det" name="inp_movimiento_det"  class="form-control" >
                                <option></option>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">                            
                            <label>IMPORTE</label>
                            <div class="input-group">
                                <div class="input-group-btn">
                                <input type="text" name="inp_monto_movimiento" id="inp_monto_movimiento" class="form-control" placeholder="ingrese monto..." />
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label>DETALLE</label>
                                 <div class="input-group">
                                <div class="input-group-btn">
                                <input type="text" name="inp_detalle" id="inp_detalle" class="form-control" placeholder="Especificar detalle..." class="form-control" />
                                </div>
                                </div>
                        </div>    
                        <div class="col-md-2">

                                <button type='button' id="save-pago" class='btn btn-success btn-lg btn-block'  class="form-control" onclick="agregar_movimiento()">
                                <i class='fa fa-save' data-loading-text="<i class='fa fa-spinner fa-spin '></i> procesando..." ></i>
                                AGREGAR</button>

                        
                            
                        </div>                        
                </div>              
            </form>           
           </div>
           <!-- end body -->
          </div> 
          <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
    <!-- begin row -->
    <div class="row" id="row-consulta-pagos">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="btn-group pull-left">
                    <?php
                    
                    if($visitante->permitir('prt_pagos',16) && $mercadopagoactivo==1):
                        ?>
                        <button type="button" class="btn btn-warning btn-xs" onclick="checkwebservicepagos()">
                            <i class="fa fa-wrench"></i>
                            CHECK PAGOS MERCADO PAGO
                        </button>                
                    <?php endif;?>
                </div>        
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;CONSULTAR CAJA</h4>
            </div>                    
            <div class="panel-body"  id="panel-body-consultar-caja">
                <form class="form-horizontal" action="/" method="POST" id='form-standard'>                  
                <div class="form-group">                                                            
                    <div class="col-md-3">
                            <input type="text" name="fecha" id="fecha" class="form-control" placeholder="ingrese fecha de la caja..." />
                    </div>
                    <div class="col-md-2">
                        <button type='button' class='btn btn-inverse btn btn-sm'  class="form-control" id="btn-consultar">
                            <i class='fa fa-search' ></i>
                            CONSULTAR</button>
                    </div>
                    <div class="col-md-2">                            
                            <input type="text" name="fecha_desde" id="fecha_desde" class="form-control" placeholder="fecha desde..." />
                    </div>
                    <div class="col-md-2">                            
                            <input type="text" name="fecha_hasta" id="fecha_hasta" class="form-control" placeholder="fecha hasta..." />
                    </div>

                    <div class="col-md-1">                    
                        <button type='button' class='btn btn-success btn btn-sm'  class="form-control" id="btn-exportar">
                            <i class='fa fa-file-excel-o' ></i>
                            EXPORTAR</button>                        
                    </div>
               </div>
               <div class="form-group" id="table-wrapper">
                    
               </div>
            </form>
            </div>
           </div>
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
    <!-- begin row -->
    <div class="row" id="row-det" style="display: none">
    <!-- reservado para mostrar listado de comprobantes adeudados de objeto elementos view -->          
    </div>
    <!-- begin row -->
    <div class="row" id="row-body">
    <!-- reservado para --> 
    </div>
    
    <!-- begin row -->
    <div class="row" id="row-caja">
    <!-- reservado para tpl-operaciones-list, oPagoView -->      
    </div>
    <div class="modal fade in" id="modal-parametros" style="display: none;">
    </div>

   
</div>
<!-- end #content -->
<script type="text/template" id="tpl-operaciones-list">
<div class="col-md-12" >  
    <div class="panel panel-inverse">
    <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>                       
                </div>
                <h4 class="panel-title">RESULTADO DE LA BUSQUEDA</h4>
    </div>             
    <div class="panel-body" id="panel-body-pagos">                
        <table id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Operacion</th>
                                <th>Monto</th>
                                <th>Fecha</th>                                        
                                <th>Tipo operación</th>
                                <th>Usuario</th>                                        
                                <th>Inicio caja</th>                                        
                                <th>ver</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        
                        <% _.each(ls, function(elemento) { 
                        
                        
                        var strpago='$ '+ elemento.get('monto');
                        var strdetalle=elemento.get('detalle')
                        var strfecha=elemento.get('fe_operacion')
                        var strtipo_operacion=(elemento.get('movimiento')=='E')?'SALIDA':'ENTRADA';
                       
                         %>
                        <tr>
                            <td><%=strdetalle%></td>        
                            <td><%=strpago%></td>        
                            <td><%=strfecha%></td>
                            <td><%=strtipo_operacion%></td>                            
                            <td><%=elemento.get('usuario')%></td>
                            <td><%=elemento.get('fecha_inicio')%></td>                             
                            
                            <td>
                                <% if(elemento.get('origen')=='pagos'){%>
                                <a href="javascript:;" class="btn btn-white btn-xs" id="pago-link-<%=elemento.cid%>"><i class="fa fa-search-plus"></i></a>
                                <%}%>                        
                            </td>
                        </tr>
                        <% }); %>
                        </tbody>
        </table>
    </div>
   </div>
</div>
</script>

<script type="text/template" id="tpl-table-list"> 
<div class="col-md-12" >  
    <div class="panel panel-inverse">
    <div class="panel-heading">                        
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove" data-original-title="" title="" data-init="true"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title">&nbsp;&nbsp;&nbsp;COMPROBANTES ADEUDADOS</h4>
    </div>             
    <div class="panel-body" id="panel-body-comprobantes">
        <table id="data-table-comp-deuda"  class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
        <thead>
            <tr>
                <th>COMPROBANTE</th>
                <th>CLIENTE</th>
                <th>FECHA</th>                                
                <th>IMPORTE</th>                                                        
                <th>PAGOS</th>                                        
                <th>DEUDA</th>                                        
                <th>VER</th>                                        
            </tr>
        </thead>
        <tbody>                                                               
            <% _.each(ls, function(elemento) { 
                var str_talonario=format_number(elemento.get('nro_talonario'),'0000')
                var str_nro_comp=format_number(elemento.get('nro_comp'),'00000000')
                var str_cliente=elemento.get('documento')+' - '+elemento.get('nro_docu')+' '+elemento.get('strnombrecompleto')

                var comprobante=(elemento.get('afip')!=1)?('orden de pedido N°:'+elemento.get('id_comp')):(elemento.get('tipo_comp')+' N° '+str_talonario+' '+str_nro_comp+'  ('+elemento.get('id_comp')+')')
                var str_pagos='' 

                if(parseFloat(elemento.get('importe_pago'))< parseFloat(elemento.get('importe_total')) && parseFloat(elemento.get('importe_pago')) > 0)
                {
                    str_pagos="<a href='javascript:;' class='btn btn-warning btn-xs'>$ "+format_number(elemento.get('importe_pago'),'#.00')+"</a>"
                }

                if(parseFloat(elemento.get('importe_pago'))>=parseFloat(elemento.get('importe_total')))
                {
                str_pagos="<a href='javascript:;' class='btn btn-success btn-xs'>$ "+format_number(elemento.get('importe_pago'),'#.00')+"</a>"
                }

                if(parseFloat(elemento.get('importe_pago'))<parseFloat(elemento.get('importe_total')) && parseFloat(elemento.get('importe_pago'))<=0)
                {
                str_pagos="<a href='javascript:;' class='btn btn-danger btn-xs'>$ "+format_number(elemento.get('importe_pago'),'#.00')+"</a>"
                }


             %>
            <tr>        
                <td><%=comprobante%></td>
                <td><%=str_cliente %></td>
                <td><%=elemento.get('fe_creacion')%></td>
                <td>$<%=elemento.get('importe_total')%></td>        
                <td><%=str_pagos%></td>     
                <td><a href='javascript:;' class='btn btn-danger btn-xs'>$<%=elemento.get('deuda')%></a></td>     
                <td><a href="javascript:;" class="btn btn-white btn-xs" id="view-link-<%=elemento.get('id_comp')%>"><i class="fa fa-eye"></i></a></td>
            </tr>
            <% }); %>
            </tbody>
        </table>        
    </div>
   </div>
</div>
</script>
<script>
App.restartGlobalFunction();
App.setPageTitle('Consulta de caja| Coffee APP');
var base_url='<?=base_url()?>index.php/'
var ofwlocal=new fw(base_url)
var ofwtlocal=new fwt(base_url+'operaciones/caja/listener')
var win= new fwmodal();
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
                                
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/caja/");
                             })
                          });
                        });
                    });
                });
            });
        });
    });
});
var oColecciones=null;
var oDetalles=null;
var olista=null;
var olsDetalles=null;
var ocampoView=null;
var ocampopagoView=null;
var oCajaView=null;
var oPagos=null;
var oOperaciones=null;
var eventos = _.extend({}, Backbone.Events);
var viendopago=true;

var Operacion=Backbone.Model.extend({    
     url:'',
    default:{
        id_operacion:0,
        id_usuario:0,
        id_operacion_tipo:'',
        monto:0,
        detalle:'',
        fecha:'',
        tiposoperaciones:null
    }
});//itempago

var Operaciones=Backbone.Collection.extend({     
    initialize:function(options){            
        this.options=options || {}        
        this.tiposoperaciones=globales['operaciones_tipo']
    },
    loadAsync:function(patrones){
        
        var that=this;
        that.reset();
        var cond=" id_caja=<?php echo $id_caja ?>";
        var id_pago=0;
       
        

        if(typeof patrones['fecha'] !="undefined"){
            if(patrones['fecha']!="")
            {var strdate=todate(patrones['fecha'],103);
                cond+=' and DATE(fe_inicio)="'+strdate+'"';
            }    
        }
        
      

        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_caja",this);
        }        
        var cmp=Array("id as id_operacion","id_caja_op","id_operacion_tipo","detalle","DATE_FORMAT(fecha,'%d/%m/%Y %r') AS fe_operacion","monto","movimiento","usuario"," DATE_FORMAT(fe_inicio,'%d/%m/%Y %r') as fecha_inicio","origen");

            ofwlocal.getAsync("vercaja_movimientos",cmp,cond,"fecha asc",function(rs){             
                
            that.cargar(rs,that) 
        })
    },    
    cargar:function(rs,that)
    {   
          for (c in rs)
         {
          that.add(rs[c])
          }
       if(typeof that.options.eventos !="undefined")
        {
        that.options.eventos.trigger("endload_caja",that);
        }
    },
    model:Operacion
});




var Pago=Backbone.Model.extend({    
     url:'<?=base_url()?>index.php/operaciones/pagos/listener',
    default:{
        id_tipo_pago:1,//tipo pago seleccionado por defecto        
        monto_abona:0,
        observacion:'',
        tipospagos:null,
        eliminable:true,
        fe_estado_pago:'',
        anulado:false,
        id_usuario_estado:0,
        id_pago:0,
        fe_pago:'',  
        strnombrecompleto:'',
        observacion:'',          
        usuario:'',
        sucursal:'',  
        usuario_estado:'',
        anulado:'',
        fe_estado_pago:''
    }
});//itempago
var Pagos=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
        this.tipospagos=globales['tipo_pagos']
    },
    load:function(patrones){
        var that=this;
        that.reset();
        var cond=" 1=1";
        var id_pago=0;
        if(typeof patrones['id_pago'] != "undefined")
        {         
            if(patrones['id_pago']!=""){
            id_pago=parseInt(patrones['id_pago']);
            cond+=" and id_pago="+patrones['id_pago'];         
            }
        }

        if(typeof patrones['anulados'] != "undefined")
        {         
            if(!patrones['anulados']){            
            cond+=" and estado<>'A'"
            }
        }

        if(typeof patrones['id_proceso'] != "undefined")
        {
        cond+=" and id_proceso="+patrones['id_proceso'];    
        }
        if(typeof patrones['fecha'] !="undefined"){
            if(patrones['fecha']!="" && !(id_pago>0))
            {var strdate=todate(patrones['fecha'],103);
                cond+=' and fe_pago>="'+strdate+'"';
            }    
        }
        
        if(typeof patrones['fecha_hasta'] != "undefined"){
            if(patrones['fecha_hasta']!="" && !(id_pago>0))
            {
                var strdate=todate(patrones['fecha_hasta'],103);
                cond+=" and fe_pago< ADDDATE('"+strdate+"',1)";
            }    
        }
        
              
        var cmp=Array("id_pago","pago","DATE_FORMAT(fe_pago,'%d/%m/%Y %r') AS fe_pago","id_cliente","observacion","id_tipo_pago","tipo_pago","id_usuario","usuario","estado as estado_pago","id_usuario_estado","usuario_estado","DATE_FORMAT(fe_estado,'%d/%m/%Y %r') AS fe_estado_pago","imputado","resta_imputar","id_proceso","btnclass_pago","estado_pago_desc");
        var rs= ofwlocal.get("verpagos_imputar",cmp,cond,"")
        this.cargar(rs,that) 
    },    
    cargar:function(rs,that)
    {   
     for (c in rs)
     {
        var tps=this.tipospagos.findWhere({id_tipo_pago:rs[c]['id_tipo_pago']});
        rs[c]['parametros']=tps.get('parametros');
      that.add(rs[c])
     }
       
    },
    model:Pago
});

var CajaView=Backbone.View.extend({   
    initialize:function(options)
    {
        this.options=options || {};
        this.onview=false;
    },
    render:function(patrones)
    {   
        var that=this;
        if(oOperaciones ==null)
        {
        oOperaciones=new Operaciones({eventos:eventos});    
        }
        eventos.on("initload_caja",this.loading,that)
        eventos.on("endload_caja",this.endload,that)                
        oOperaciones.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body-consultar-caja'));  
    },
    endload:function(ocol)
    {
      this.cargar(ocol)
    },

    events:{    
    "click a[id*='pago-link-']":'verpago'
    },
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models  
        var tpl=_.template($('#tpl-operaciones-list').html());                
        this.$el.html(tpl({ls:olist}));
         spinnerEnd($('#panel-body-consultar-caja'));
    },
    verpago:function(e,modo='V'){        
        e.stopPropagation();
        if(this.onview){
            return;
        }
        $("#row-caja").hide();
        var permite='VED'
        this.onview=true;
        var i=e.target.id.indexOf('pago-link-')
        var id_model=''
        if(i>=0)
        {
            id_model=e.target.id.replace('pago-link-','')        
        }else{
            id_model=e.target.parentNode.id.replace('pago-link-','')        
        }
        var modelo=oOperaciones.get(id_model)
        var id_pago=modelo.get('id_operacion');
        oPagos.load({id_pago:id_pago});
        var modeloPago=oPagos.findWhere({id_pago:id_pago})
        var oCampos=new Campos(); //coleccion
        cCampo=new Campo({valor:modeloPago.get('id_pago'),nombre:'id_pago',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);        
        cCampo=new Campo({valor:"$ "+modeloPago.get('pago'),nombre:'pago',tipo:'readonly',etiqueta:'Monto Abonado',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);        
        cCampo=new Campo({valor:modeloPago.get('fe_pago'),nombre:'fe_pago',tipo:'readonly',etiqueta:'Fecha de pago',esdescriptivo:false,obligatorio:false});
        var tipobs='text'
        if(modeloPago.get('estado_pago')=="A")
        {   permite='V'
            tipobs='readonly'
        }else{
            //si no tiene permisos para elmiinar, que solomuestre la visualizacion o edicion
            /*if(permitir(prt_pagos,8)){
                permite='VED'
            }else{
                
            }*/
            permite='VE'
        }
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modeloPago.get('observacion'),nombre:'observacion',tipo:tipobs,etiqueta:'Observación',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modeloPago.get('usuario'),nombre:'usuario',tipo:'readonly',etiqueta:'Cajero',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        var strestado=modeloPago.get('estado_pago_desc')+" - fecha: "+modeloPago.get('fe_estado_pago')+" - usuario: "+modeloPago.get('usuario_estado') 
        cCampo=new Campo({valor:strestado,nombre:'usuario_estado',tipo:'readonly',etiqueta:'Estado del pago',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);    
        

        var parametros=modeloPago.get('parametros')
        if(parametros!=null){
            if(parametros.length>0)
            {   //mparams=parametros.models
                for(p in parametros){
                    param=parametros[p]
                    cparams=new Campo({valor:param.get('parametro_valor'),nombre:param.get('parametro'),tipo:'text',etiqueta:param.get('descripcion'),esdescriptivo:false,obligatorio:false});
                    oCampos.add(cparams);
                }
                
            }

        }
        
        
       var that=this;
       viendopago=true;
       if(ocampopagoView ==null)
       {
           ocampopagoView=new campoView({campos:oCampos,modelo:modeloPago,permite:permite,base_url:'<?=base_url()?>',tplname:'form_multipart.html',el:$("#row-body")});
           ocampopagoView.options.volver=function(){

            if(!viendopago) 
            return
                        $("#row-caja").show();    
                        $("#row-body").hide();    
                        $("#row-body").html("");
                        if (!$("#panel-body-pagos").is(":visible")) { //si no se ve, que lo abra
                            $("#panel-body-pagos").slideToggle();            
                        }
                        if (!$("#panel-body-consultar-caja").is(":visible")) { //si no se ve, que lo abra
                            $("#panel-body-consultar-caja").slideToggle();            
                        } 
                        ocampopagoView=null;
             }//volver
             

            ocampopagoView.options.onafterrender=function(){
                event.stopPropagation()  
                
                var mo= ocampopagoView.options.modelo 
                if(mo.get('estado_pago')!="A" && permitir(prt_pagos,8)){
                 $("#botonera2").html("<button type='button' id='eliminarpago' class='btn btn-sm btn-danger'><i class='fa fa-eraser'></i>ANULAR PAGO</button>"); 
                $("#eliminarpago").click(function(){        

                   var id_pago=ocampopagoView.options.modelo.get('id_pago')
                   that.confirmarAnulaPago(id_pago)
                })
                }
            
            }//afterrenderd
        }
        else
        {
            ocampopagoView.options.campos=oCampos
            ocampopagoView.options.modelo=modelo
        }
         
        if ($("#panel-body-pagos").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body-pagos").slideToggle();            
        }
        if ($("#panel-body-consultar-caja").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body-consultar-caja").slideToggle();            
        } 
              
        ocampopagoView.render(modo)
        if(modo=='A' || modo=='E')
        {
            ocampopagoView.options.validar=function(e)
            {
                return true;
            }
        }

        ocampopagoView.options.success=function(e)
        {   spinnerEnd($("#panel-body-view"))          
            
            if (!$("#panel-body-pagos").is(":visible")) { //si estacerrado, que lo abra
                    $("#panel-body-pagos").slideToggle();            
            }


            if (!$("#panel-body-consultar-caja").is(":visible")) { //si estacerrado, que lo abra
                    $("#panel-body-consultar-caja").slideToggle();            
            }
            $("#row-body").hide();    
            $("#row-body").html("");
            consultar();
        }
        ocampopagoView.options.before=function()
        {
            spinnerStart($("#panel-body-view"))          
        }//antes
        ocampopagoView.options.error=function()
            {
                spinnerEnd($('#panel-body-view'));
                
             consultar();
            }

        
    this.onview=false;
    },//ver
    confirmarAnulaPago:function(id_pago){
        var that=this        
        win.dialog("Tenga en cuenta que este pago puede estar imputado a algún comprobante. Los cambios son irreversibles.",'¿Desea eliminar realmente este pago?',4, function(t){            
                that.eliminarPago(t);
         },id_pago);
    },
    eliminarPago:function(id_pago){
    
        $.ajax({url:'<?=base_url()?>index.php/operaciones/pagos/anular',
                    type: "post",
                    dataType: "json",
                    data: {id_pago:id_pago},                    
                    beforeSend: function(){
                        
                        spinnerStart($("#panel-body-consultar-caja"));
                    },
                    complete: function(){
                        spinnerEnd($('#panel-body-consultar-caja'));
                    },
                    error:function(){
                      spinnerEnd($('#panel-body-consultar-caja'));
                      console.log("Hubo error en ajax");  
                    }
                    })
                    .done(function(response){
                        
                        var numError=parseInt(response.numerror);
                        var descError=response.descerror;
                        if(numError == 0){
                             
                               swal({title:"Operación exitosa!",
                                            timer: 3000,
                                            text: "El pago se anuló correctamente.",
                                            type: "success"})

                            ocampopagoView.options.volver();
                            consultar();
                            }else{
                                
                                 swal({title:"Error!",
                                            timer: 3000,
                                            text: "Hubo una falla al consultar. Consulte con el administrador.",
                                            type: "error"})
                            }                          
                        
                    });


    },//eliminar pago
    verparams:function(e){
        event.stopPropagation();
        if(this.onview){
            return;
        }
        this.onview=true;
        var i=e.target.id.indexOf('id_tipo_pago_')
        var identificador=''
        if(i>=0)
        {
            identificador=e.target.id.replace('id_tipo_pago_','')        
        }else{
            identificador=e.target.parentNode.id.replace('id_tipo_pago_','')        
        }
        var ids=identificador.split("-")
        var id_pago=ids[0]
        var id_tipo_pago=ids[1]
        var cmp=Array("id_pago","nro_parametro","valor","id_tipo_pago","tipo_pago","parametro","tipo_dato","descripcion","parametro_valor")
        var rs=ofwlocal.get("verparametros_pago",cmp,"id_tipo_pago="+id_tipo_pago+" and id_pago="+id_pago," descripcion");
        
        var titulo= " PARAMETROS PAGO "+rs[0]['tipo_pago']
        var cuerpo="<ul>"
        for (i in rs)
        {
        cuerpo+="<li>"+rs[i]['descripcion']+": "+rs[i]['parametro_valor']+"</li>"
        }
        cuerpo+="</ul>"
        win.alert(cuerpo,titulo,3)    
        this.onview=false;
    }

})//pagos view

var Pagosparametrosview=Backbone.View.extend(
 {   el:$('#tbl-pagos'),
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(pago,tipopago,bancos,tarjetas)
    { 
        var that=this;
        this.pago=pago;
        this.tipopago=tipopago;
        var mbancos=bancos.models;

        var mtarjetas=tarjetas.models;
        var parametros=tipopago.get('parametros');
        $.get(this.options.base_url+'tpl/opera_pago_parametros.html', function (data) {
            tpl = _.template(data, {});
            
            htmlrender=tpl({campos:parametros,bancos:mbancos,tarjetas:mtarjetas,base_url:that.options.base_url})
            that.$el.html(htmlrender);
            
            for (i in parametros)
            {
                if(parametros[i].get('parametro') !='id_banco' && parametros[i].get('parametro') !='id_tarjeta')
                {
                   if(parametros[i].get('tipo_dato')=='int')
                   {    
                        $("#"+parametros[i].get('parametro')).keypress(function(e){ return teclaentero(e)})
                        
                   }
                   if(parametros[i].get('tipo_dato')=='float')
                   {
                    $("#"+parametros[i].get('parametro')).keypress(function(e){ return teclamoney(e)})
                   }
                }
            }
        })
        
    },
    events:{            
            "click #save-params":'validateparams'
    },
    validateparams:function(){
        
         var strhtml=""
         var parametros=this.tipopago.get('parametros');
        for (i in parametros)
        { var parametro=parametros[i].get('parametro')
          var tipo_dato=parametros[i].get('tipo_dato')
            var desc=parametros[i].get('descripcion')
            var valor=$("#"+parametro).val();
            if(valor=='')
            {
                strhtml+="<li>No ha completado "+desc+"</li>"
            }
            if(tipo_dato=="int")
            {
                if(!es_entero(valor))
                    {strhtml+="<li>"+desc+" no tiene un valor correcto</li>"}
                if(es_entero(valor) && parametro=="mes_vencimiento" && !(parseInt(valor)>=1 && parseInt(valor)<13))
                {
                    strhtml+="<li>"+desc+" debe estar comprendido entre 1 y 12</li>"
                }

            }
            if(tipo_dato=="float")
            {
                if(!es_numero(valor))
                    strhtml+="<li>"+desc+" no tiene un valor correcto</li>"
            }
        }
        if(strhtml!="")
        {   strhtml="<ul>"+strhtml+"</ul>"
            that=this;
            win.dialog(strhtml,'¿Desea continuar de todas formas?',4, function(t){            
                                        t.saveparams(parametros);
                },that); 
        }else{
            this.saveparams(parametros);
        }
    },
    saveparams:function(parametros){
        $("#modal-parametros").modal("hide");        
        for (i in parametros)
        { var parametro=parametros[i].get('parametro')            
          var valor=$("#"+parametro).val();          
          parametros[i].set({valor:valor})
        }
        this.tipopago.set({parametros:parametros})
        var tipospagos=this.pago.get("tipospagos");
        for (i in tipospagos.models)
        {   //busco el objeto tipo pago  seleccionado en this.pago para asignarle el nuevo tipo con sus parametros
            if(this.pago.get("id_tipo_pago")==tipospagos.models[i].get("id_tipo_pago"))
            {
                tipospagos.models[i]=this.tipopago;
                break;
            }
        }
        this.pago.set({tipospagos:tipospagos});        
        oOperacionadd=this.pago;
    }

})


var date = new Date()
var day = date.getDate()
var month = date.getMonth() + 1
var year = date.getFullYear()
var strdateactual=''
if(month < 10){
  strdateactual=day+"/"+"0"+month+"/"+year
}else{
  
  strdateactual=day+"/"+month+"/"+year
}


function inicializacion_contexto()
{
eventos.off();
$("#fecha").mask("99/99/9999");
                $("#fecha").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });

$("#fecha_desde").mask("99/99/9999");
                $("#fecha_desde").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });

    $("#fecha_hasta").mask("99/99/9999");
    $("#fecha_hasta").datepicker({
         todayHighlight: true,
         format: 'dd/mm/yyyy',
         language: 'es',
         autoclose: true
    });                



$("#btn-volver").click(function(e){    
    event.stopPropagation();
     handleCheckPageLoadUrl("<?php echo base_url();?>index.php/panel/")
})
cargar_items()


$("#inp_monto_movimiento").keypress(function(e){
    return teclamoney(e)
})


oCajaView=new CajaView({el:$('#row-caja')});                

$("#btn-consultar").click(function(e){    
    event.stopPropagation();
    
    consultar();
})

$("#btn-exportar").click(function(e){    
    event.stopPropagation();
     exportar();
})

oPagos=new Pagos();

display('btnconsultarpagos');
olista=new ElementosView({el:$('#row-det')});

consultar();
}//inicializacion contexto


function consultar()
{
 
if($("#fecha").val()==""){
     $("#fecha").val(strdateactual)
    }
 
 display('consultascaja');
var patrones={fecha:$("#fecha").val()} 
/*se dibuja la consulta de los pagos*/

 oCajaView.render(patrones);
}
function exportar(){
    var patrones={fecha_desde:$("#fecha_desde").val(),fecha_hasta:$("#fecha_hasta").val()}
    $.ajax({url:'<?=base_url()?>index.php/operaciones/caja/exportar',
                    type: "post",
                    dataType: "json",
                    data: patrones,                    
                    beforeSend: function(){
                       spinnerStart($('#panel-body-consultar-caja'));
                    },
                    complete: function(){
                        spinnerEnd($('#panel-body-consultar-caja'));
                    },
                    error:function(){
                      spinnerEnd($('#panel-body-consultar-caja'));  
                    }
                    })
                    .done(function(response){                        
                        var numError=parseInt(response.numerror);
                        var descError=response.descerror;                                                

                        if(numError == 0)
                        {                            
                          window.open("<?php echo base_url() ?>"+descError,'_blank');
                        }else{
                                win.alert(descError," La exportación falló ",4) 
                        }
                    });
}//exportar


function registrar_movimiento(){
  event.stopPropagation();
  /*$("#row-pago-add").show();*/
  display('btncargamovimiento');
  
  oOperacionadd=new Operacion({id_operacion:0,monto:0,detalle:'',id_operacion_tipo:0 });
  $("#inp_movimiento").val("entrada");
  $("#inp_monto_movimiento").val("");
  $("#inp_detalle").val("");

 
}


function verificar_params(){
         var id_tipo_pago=$("#inp_movimiento").val();
        //si lo anterior tiene parametros, es porque hay que completar mas info, sino, nada
        var tipopago=(oOperacionadd.get("tipospagos")).findWhere({id_tipo_pago:id_tipo_pago.toString()})
         params=tipopago.get("parametros");
         if(params.length>0)
         { $("#lbpago").show();
            $("#btn-info-pago").show();
            oOperacionesparametros.render(oOperacionadd,tipopago,globales['bancos'],globales['tarjetas'])
            $("#modal-parametros").modal("show");
         }else{
            $("#btn-info-pago").hide();
            $("#lbpago").hide();
         }

}

function mostrar_params(){
    var id_tipo_pago=$("#inp_movimiento").val();
        //si lo anterior tiene parametros, es porque hay que completar mas info, sino, nada
        var tipopago=(oOperacionadd.get("tipospagos")).findWhere({id_tipo_pago:id_tipo_pago.toString()})
    $("#btn-info-pago").show();
    oOperacionesparametros.render(oOperacionadd,tipopago,globales['bancos'],globales['tarjetas'])
    $("#modal-parametros").modal("show");
}
function agregar_movimiento(){
event.stopPropagation();
var monto_abona=$("#inp_monto_movimiento").val();
if(!es_numero(monto_abona))
{
    //win.alert("El monto ingresado, es invalido"," Atención",4)
    swal("Atención","El monto ingresado es invalido","error")
    return
}
monto_abona=parseFloat(monto_abona);
if(monto_abona<=0){
 //win.alert("El monto no puede ser menor a cero"," Atención",4)
 swal("Atención","El monto no puede ser menor a cero","error")
    return   
}
var movstr=($("#inp_movimiento").val()=="egresos")?"a SALIDA":" INGRESO"

    swal({
          title: "Atención",
          text: 'Usted va a registrar un'+movstr+' de $'+monto_abona+' pesos de la caja activa. ¿Desea continuar?',
          type: ($("#inp_movimiento").val()=="egresos")?"warning":"info",
          showCancelButton: true,
          closeOnConfirm: true,
          showLoaderOnConfirm: true,
          confirmButtonText: "Si, agregar!",
          cancelButtonText: "No, lo haré en otro momento!",
          closeOnCancel: true
            }, function (isConfirm) {
           if(isConfirm)
           {
             send_movimiento()
           }
            });

      


}

function send_movimiento(){

//spinnerStart($("#panel-body-cargar-movimiento"))
var id_tipo_pago=$("#inp_movimiento").val();
oOperacionadd.url= '<?=base_url();?>index.php/operaciones/caja/agregar_movimiento/'
oOperacionadd.set({"monto":$("#inp_monto_movimiento").val(),"detalle":$("#inp_detalle").val(),"id_operacion_tipo":$("#inp_movimiento_det").val()})
$("#save-pago").button("loading")
oOperacionadd.save(null,{
               type:'POST', 
                    success:function(e,params){                            
                        $("#save-pago").button("reset")
                                if(params.numerror!=0)
                                {   swal("Error al generar movimiento","Detalle: "+params.descerror+". Consulte con el administrador", "error")
                                    
                                    
                                }
                                else
                                {   
                                var recaudacion=params.data.recaudacion                                
                                var saldo_caja=params.data.saldo_caja;
                                var pagos=params.data.id_pago;
                                
                                $("#recaudacion").html(format_number(recaudacion,"#.00"))
                                $("#saldo_caja").html(format_number(saldo_caja,"#.00"))
                                $("#row-pago-add").hide();  
                                $("#inp_movimiento").val("entrada");
                                $("#inp_monto_movimiento").val("");
                                $("#inp_detalle").val("");                              
                                  swal({title:"Operación exitosa!",
                                            timer: 3000,
                                            text: "El movimiento se agregó correctamente",
                                            type: "success"})
                                
                                 consultar()
                                }
                            },
                    error: function(e,params) {                        
                        $("#save-pago").button("reset")
                                    win.alert(params.responseText,"Error",4)
                                    
                    },
                    wait:true
                    })//save


}

/*elemento que se encarga de recolectar los comprobantes adeudados para la vista que muestra detalles de la deuda*/
var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>',   
    idAttribute:'id_comp',
    defaults:{
        id_comp:0,
        id_talonario:0,
        nro_comp:0,
        nro_talonario:0,
        id_cliente:0,
        strnombrecompleto:'',
        domicilio:'',
        telefono:'',
        importe_total:0,
        importe_neto:0,
        importe_iva:0,
        importe_exento:0,
        importe_descuento:0,
        fe_creacion:'',
        fe_comp:'',
        fe_vencimiento:'',
        cuit:'',
        estado:'',
        usuario_estado:'',
        usuario:'',
        afip:'',
        sucursal:'',
        condicion:'',
        tipo_comp:'',
        tipo_comp_afip:''
    }
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){
        var that=this;
        that.reset();        
        var cond=" id_comp in(SELECT distinct id_comp FROM vercomp_pagados WHERE debe>0)"
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_comp",this);
        }        
        var cmp=Array("id_comp","id_talonario","IFNULL(nro_comp,0) as nro_comp","IFNULL(nro_talonario,0) as nro_talonario","cae","id_cliente","strnombrecompleto","domicilio","telefono","nro_docu","tipo_docu","documento","tipo_persona_desc","descripcion_loc","descripcion_pro","importe_total","importe_neto","importe_base","importe_iva","importe_exento","importe_descuento","DATE_FORMAT(fe_creacion,'%d/%m/%Y %r') as fe_creacion","DATE_FORMAT(fe_comp,'%d/%m/%Y %r') as fe_comp","DATE_FORMAT(fe_vencimiento,'%d/%m/%Y %r') as fe_vencimiento","cuit","estado","descripcion","estadoclass","usuario_estado","usuario","afip","id_sucursal","sucursal","id_cond_iva","condicion","id_tipo_comp","tipo_comp","tipo_comp_afip","importe_pago","path_comp","id_comp_anula","deuda");
        ofwlocal.getAsync("vercomprobantes",cmp,cond,"fe_creacion asc",function(rs){ 
            
            that.cargar(rs,that) 
        } )
    },    
    cargar:function(rs,that)
    {       
     for (c in rs)
     {
      that.add(rs[c])
      }
        if(typeof that.options.eventos !="undefined")
        {
         that.options.eventos.trigger("endload_comp",that);
        }
    },
    model:Elemento
});

/*Vista utilizada para mostrar los resultados del boton "ver detalles" de los comprobantes adeudadoes*/
var ElementosView=Backbone.View.extend({   
    defaults:{ocampoView:null},
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(patrones)
    {
        var that=this;
        
        if(oColecciones ==null)
        {
        oColecciones=new Coleccion({eventos:eventos});    
        }
        eventos.on("initload_comp",this.loading,this)
        eventos.on("endload_comp",this.endload,this)
        oColecciones.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body-saldos'));  
      //spinnerStart($('#panel-body-comprobantes'));  
    },
    endload:function(ocol)
    {
      this.cargar(ocol)
    },
    events:{
            "click a[id*='view-link-']":'ver'
    },
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist}));                  
          $('#data-table-comp-deuda').DataTable({responsive: true,searching:false,lengthChange:false,pageLength:17,
            "columns": [
            { "orderable": true },
            { "orderable": true },
            { "orderable": true},
            { "orderable": true},
            { "orderable": true},
            { "orderable": true},
            { "orderable": false}
            ]
         }); 
         spinnerEnd($('#panel-body-saldos'));  


         $('[data-click="panel-remove"]').off();
         $('[data-click="panel-remove"]').click(function(){
            display('btnconsultarpagos');
            return true;
         })
    },
    ver:function(e)
    {   
        var i=e.target.id.indexOf('view-link-')
        var id_model=''
        if(i>=0)
        {
            id_model=e.target.id.replace('view-link-','')        
        }else{
            id_model=e.target.parentNode.id.replace('view-link-','')        
        }
        
        if(id_model!="")
        {  var modelo=oColecciones.findWhere({'id_comp':id_model})
            this.mostrar(modelo)
        }
    },
    mostrar:function(modelo)
    {   //muestro los datos de los comprobantes adeudados
        var oCampos=new Campos(); //coleccion
        cCampo=new Campo({valor:modelo.get('id_comp'),nombre:'id_comp',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        var str_talonario=format_number(modelo.get('nro_talonario'),'0000')
        var str_nro_comp=format_number(modelo.get('nro_comp'),'00000000')
        var comprobante=(modelo.get('afip')!=1)?('orden de pedido N°:'+modelo.get('id_comp')):(modelo.get('tipo_comp')+' N° '+str_talonario+' '+str_nro_comp+'  ('+modelo.get('id_comp')+')')
        cCampo=new Campo({valor:comprobante,nombre:'comprobante',tipo:'readonly',etiqueta:'Comprobante',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);
        var datoscliente=modelo.get('strnombrecompleto')+' '+modelo.get('documento')+' '+modelo.get('nro_docu')+' Cuit: '+modelo.get('cuit')
        cCampo=new Campo({valor:datoscliente,nombre:'datoscliente',tipo:'readonly',etiqueta:'Cliente',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('condicion'),nombre:'condicion',tipo:'text',etiqueta:'Condición',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('importe_total'),nombre:'importe_total',tipo:'money',etiqueta:'Importe total',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('importe_neto'),nombre:'importe_neto',tipo:'money',etiqueta:'Importe neto',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        cCampo=new Campo({valor:modelo.get('importe_iva'),nombre:'importe_iva',tipo:'money',etiqueta:'Importe iva',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('importe_exento'),nombre:'importe_exento',tipo:'money',etiqueta:'Importe exento',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        
        cCampo=new Campo({valor:modelo.get('importe_descuento'),nombre:'importe_descuento',tipo:'money',etiqueta:'Importe descuento',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        
        cCampo=new Campo({valor:modelo.get('fe_creacion'),nombre:'fe_creacion',tipo:'text',etiqueta:'Fecha creación',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('usuario'),nombre:'usuario',tipo:'text',etiqueta:'Usuario',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('fe_comp'),nombre:'fe_comp',tipo:'text',etiqueta:'Fecha comprobante',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('cae'),nombre:'cae',tipo:'text',etiqueta:'C.A.E',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('fe_vencimiento'),nombre:'fe_vencimiento',tipo:'text',etiqueta:'Fecha vencimiento',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('descripcion'),nombre:'descripcion',tipo:'text',etiqueta:'Estado',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('usuario_estado'),nombre:'usuario_estado',tipo:'text',etiqueta:'Usuario estado',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        var afip=(modelo.get("afip")=="1")?"SI":"NO"
        cCampo=new Campo({valor:afip,nombre:'afip',tipo:'text',etiqueta:'Afip',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        cCampo=new Campo({valor:modelo.get('sucursal'),nombre:'sucursal',tipo:'text',etiqueta:'Sucursal',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        if(modelo.get('estado')=='A'){
            cCampo=new Campo({valor:'ID COMP:'+ modelo.get('id_comp_anula'),nombre:'id_comp_anula',tipo:'text',etiqueta:'Anulado por ',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        }

        if(oColecciones ==null)
        {
        oColecciones=new Coleccion();    
        }
        viendopago=false;
        var that=this;
        if(ocampoView ==null)
        {
            ocampoView=new campoView({campos:oCampos,modelo:modelo,permite:'V',base_url:'<?=base_url()?>',tplname:'form_multipart.html',el:$("#row-body")});
            ocampoView.options.volver=function(){ 
                /*es para atajar el evento de volver cuando se 
                        apreto el boton de volver de la vista pagos*/
                    if(viendopago) 
                        return
            
                        if (!$("#panel-body-comprobantes").is(":visible")) { //si esta abierto, que lo cierre
                        $("#panel-body-comprobantes").slideToggle();            
                        }
                        $("#row-body").hide();
                        $("#row-body").html("");
                        $("#row-caja").hide();    
                        $("#row-caja").html("");
                        ocampoView=null;
                       
           }
         
         ocampoView.options.onafterrender=function(){
            event.stopPropagation()            
            var mo=ocampoView.options.modelo
            var id_tipo_comp=mo.get("id_tipo_comp")
            var afip=mo.get("afip")
            var estado=mo.get("estado")
            var  arrTipocomp=["1","2","5"]//solo facturas A,B,C
            var  arrEstados=['D','E','F','P'] //solo estos estados se puede editar            
            
            if(mo.get('path_comp')!="null" && mo.get('path_comp')!=""){
                $("#botonera2").html("<button type='button' id='mostrarComprobante' class='btn btn-sm btn-info'><i class='fa fa-file-text'></i> Comprobante</button>"); 
            $("#mostrarComprobante").click(function(){                
                window.open('<?php echo base_url();?>'+mo.get('path_comp'),'_blank');
            })
            }
            
            }//afterrenderd
        }
        else
        {
            ocampoView.options.campos=oCampos
            ocampoView.options.modelo=modelo
        }
         var targetLi = $("#panel-body-comprobantes").closest('li');
        if ($("#panel-body-comprobantes").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body-comprobantes").slideToggle();            
        }        
        ocampoView.render('V')
        
    }
})//modelos view

var secciones={}
secciones['btnresumen']={}
secciones['btnresumen']['row']='';
secciones['btnresumen']['panel']='panel-body-saldos';
secciones['btncargamovimiento']={}
secciones['btncargamovimiento']['row']='row-pago-add';
secciones['btncargamovimiento']['panel']='panel-body-cargar-movimiento';
secciones['btnconsultarpagos']={};
secciones['btnconsultarpagos']['row']='row-consulta-pagos';
secciones['btnconsultarpagos']['panel']='panel-body-consultar-caja';
secciones['compadeudados']={};
secciones['compadeudados']['row']='row-det';
secciones['compadeudados']['panel']='panel-body-comprobantes';
secciones['compdet']={};
secciones['compdet']['row']='row-body';
secciones['compdet']['panel']='';
secciones['consultascaja']={};
secciones['consultascaja']['row']='row-caja';
secciones['consultascaja']['panel']='';
function display(seccion){
    
    switch (seccion){
        case 'btncargamovimiento':
        $("#"+secciones[seccion]['row']).show();
        $("#"+secciones['compdet']['row']).hide();
        $("#"+secciones['consultascaja']['row']).hide();
        $("#"+secciones['compadeudados']['row']).hide();
        break;
        case 'btnconsultarpagos':
        $("#"+secciones[seccion]['row']).show();
        $("#"+secciones['btncargamovimiento']['row']).hide();
        $("#"+secciones['compdet']['row']).hide();
        $("#"+secciones['btncargamovimiento']['row']).hide();
        $("#"+secciones['compadeudados']['row']).hide();
        break;
        case 'consultascaja':
        $("#"+secciones[seccion]['row']).show();
        $("#"+secciones['btnconsultarpagos']['row']).show();
        $("#"+secciones['btncargamovimiento']['row']).hide();
        $("#"+secciones['compdet']['row']).hide();
        $("#"+secciones['btncargamovimiento']['row']).hide();
        $("#"+secciones['compadeudados']['row']).hide();
        break;
        case 'compadeudados':
        $("#"+secciones[seccion]['row']).show();
        $("#"+secciones['btnconsultarpagos']['row']).hide();
        $("#"+secciones['btncargamovimiento']['row']).hide();
        $("#"+secciones['compdet']['row']).show();
        $("#"+secciones['consultascaja']['row']).hide();        
        break;
        default:
        break;
    }


}


function cargar_items(){
    
    var mov=$("#inp_movimiento").val()
    var op=null;
    var sel=null;
    if(mov=="egresos"){
       op= globales['operaciones_tipo'].where({movimiento:'E'})
    }
    if(mov=="ingreso"){
     op= globales['operaciones_tipo'].where({movimiento:'I'})
    }
    $("#inp_movimiento_det option").remove()
    for(i in op){
        $("#inp_movimiento_det").append('<option value="'+op[i].get('id')+'">'+op[i].get('descripcion')+'</option>');
    }
}


function deben(){
    display('compadeudados')
    olista.render();
}


function checkwebservicepagos(){
var id_empresa=$("#id_empresa").val()
  $.ajax({url:'<?php echo BASE_URL_FRONT ?>index.php/webservice/checkpagospresupuestos/',
                    type: "post",
                    dataType: "json",
                    data: {id_empresa:id_empresa},                    
                    beforeSend: function(){
                        
                        spinnerStart($("#panel-body-consultar-caja"));
                    },
                    complete: function(){
                        spinnerEnd($('#panel-body-consultar-caja'));
                    },
                    error:function(){
                      spinnerEnd($('#panel-body-consultar-caja'));
                      console.log("Hubo error en ajax");  
                    }
                    })
                    .done(function(response){
                        
                        var numError=parseInt(response.numerror);
                        var descError=response.descerror;
                        console.log(descError)
                        if(numError == 0){
                        var ocurrencias=response.data.ocurrencias;     
                        var registros=response.data.registros;     
                               swal({title:"Operación exitosa!",
                                            timer: 3000,
                                            text: "Se han actualizado "+ocurrencias+" pagos de "+registros+" analizados",
                                            type: "success"})

                            
                            }else{
                                
                                 swal({title:"Error!",
                                            timer: 3000,
                                            text: "Hubo un problema. Consulte con el administrador.",
                                            type: "error"})
                            }                          
                        
                    });


}

</script>
<!-- ================== END PAGE LEVEL JS ================== -->