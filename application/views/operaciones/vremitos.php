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
    
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                        <div class="btn-group pull-left">
                        <button type="button" class="btn btn-success btn-xs" onclick="crear_remito()">
                            <i class="fa fa-file-text-o"></i>
                            CREAR REMITO
                        </button>
                        </div>
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">&nbsp;&nbsp;&nbsp;CONSULTAR REMITOS DE ENTRADA DE ARTICULOS</h4>
            </div>             
            <div class="panel-body" id="panel-body">
                <form class="form-horizontal" action="/" method="POST" id='form-standard'>                  
                <div class="form-group">                                        
                    <div class="col-md-1">
                        <label>ID remito
                            <input type="text" name="id_remito" id="id_remito" class="form-control" placeholder="" />
                        </label>
                    </div>
                    <div class="col-md-2">                            
                        <label>fecha desde
                            <input type="text" name="fecha_desde" id="fecha_desde" class="form-control" placeholder="ingrese fecha..." />
                        </label>
                    </div>
                    <div class="col-md-2">
                        <label>fecha hasta
                        <input type="text" name="fecha_hasta" id="fecha_hasta" class="form-control" placeholder="ingrese fecha..." />
                    </label>
                    </div>    
                    <div class="col-md-3">
                    <label>Proveedor
                        <select class="form-control" id="id_proveedor">
                            <option value=''>TODOS</option>
                            <?php
                            foreach ($proveedores as  $proveedor) {
                                echo "<option value='".$proveedor['id_proveedor']."'>".$proveedor['proveedor']."</option> ";
                            }
                            ?>
                        </select>
                    </label>
                    </div>                
                    <div class="col-md-2">
                    <label>Estado
                        <select class="form-control" id="estado">
                            <option value=''>TODOS</option>
                            <?php
                            foreach ($estados as  $estado) {
                                echo "<option value='".$estado['estado']."'>".$estado['descripcion']."</option> ";
                            }
                            ?>
                        </select>
                    </label>
                    </div>                        
                    <div class="col-md-2">
                        <button type='button' class='btn btn-inverse btn btn-sm'  class="form-control" id="btn-consultar">
                            <i class='fa fa-search' ></i>
                            BUSCAR</button>
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
    <div class="row" id="row-body">
    <!-- tabla --> 
    </div>
    <!-- begin row -->
    <div class="row" id="row-det">      
    </div>
    
</div>
<!-- end #content -->

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script type="text/template" id="tpl-table-list"> 
<table id="data-table" id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>REMITO</th>
                                <th>FECHA</th>                                
                                <th>PROVEEDOR</th>                                        
                                <th>ESTADO</th>                                
                                <th>VER</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) {         
        var str_remito=format_number(elemento.get('id_remito'),'00000000')+' - '+elemento.get('remito')
     %>
    <tr>        
        <td><%=str_remito%></td>
        <td><%=elemento.get('fecha')%></td>
        <td><%=elemento.get('proveedor')%></td>        
        <td><a href="javascript:;" class="<%=elemento.get('estadoclass')%>"><%=elemento.get('estado_desc')%></a>
        </td>        
        <td><a href="javascript:;" class="btn btn-white btn-xs" id="view-link-<%=elemento.get('id_remito')%>"><i class="fa fa-eye"></i></a></td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>
<script type="text/template" id="tpl-details-list"> 
<%
var str_remito=format_number(remito.get('id_remito'),'00000000')+' - '+remito.get('remito') 

%>     
<div class="col-md-12" >  
    <div class="panel panel-inverse">
    <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>                       
                </div>
                <h4 class="panel-title">DETALLES DEL REMITO <%=str_remito%></h4>
    </div>             
    <div class="panel-body" id="panel-body-det">                
        <table id="data-table" id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>articulo</th>
                                <th>cantidad</th>                                
                                <th>precio compra</th>                                        
                                <th>precio venta</th>
                                <th>-</th>
                            </tr>
                        </thead>
                        <tbody>                                                               
                        <% _.each(ls, function(elemento) { 
                        var cant=(es_entero(elemento.get('cantidad')))?parseInt(elemento.get('cantidad')):parseFloat(elemento.get('cantidad'))
                         %>
                        <tr>        
                            <td><%=elemento.get('nro_item')%> - <%=elemento.get('articulo')%></td>
                            <td><%=cant%></td>
                            <td>$ <%=elemento.get('precio_costo')%></td>                            
                            <td>$ <%=elemento.get('precio_venta')%></td>
                            <td><a href="javascript:;" class="btn btn-white btn-xs" id="view-articulo-<%=elemento.get('id_articulo')%>"><i class="fa fa-eye"></i></a></td>
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
App.setPageTitle('Consulta de remitos de entrada de articulos| Coffee APP');
var base_url='<?=base_url()?>index.php/'
var ofwlocal=new fw(base_url)
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
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/remitos/");
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
var eventos = _.extend({}, Backbone.Events);


var Detalle=Backbone.Model.extend({});
var Detalles=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
    },
    loadAsync:function(mComp){
        var that=this;
        that.reset(); 

        var cond="1=1"            
        
        cond+=" and id_remito="+mComp.get("id_remito");
        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_det",this);
        }        
        var cmp=Array("id_remito","nro_item","remito","id_proveedor","proveedor","id_articulo","articulo","cantidad","precio_base","precio_costo","precio_venta","estado","estado_desc");
        ofwlocal.getAsync("verremitos_det",cmp,cond,"nro_item asc",function(rs){

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
    that.options.eventos.trigger("endload_det",that);
    }
    },
    model:Detalle
});

var DetallesView=Backbone.View.extend(
{   el:$('#row-det'),    
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(remito)
    {   
        this.remito=remito
        var that=this;
        if(oDetalles ==null)
        {
        oDetalles=new Detalles({eventos:eventos});    
        }
        eventos.on("initload_det",this.loading,this)
        eventos.on("endload_det",this.endload,this)                
        oDetalles.loadAsync(remito)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body-det'));  
    },
    endload:function(ocol)
    {
      this.cargar(ocol)
    },
    events:{},
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models  
        var tpl=_.template($('#tpl-details-list').html()); 
                       
        this.$el.html(tpl({ls:olist,remito:this.remito}));         
         spinnerEnd($('#panel-body-det'));
    }
})//detalles view



var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>',   
    idAttribute:'id_remito',
    defaults:{
        id_remito:0,        
        remito:'',        
        id_proveedor:0,
        proveedor:'',
        estado:'',
        estado_desc:'',
        fecha:'',
        usuario_estado:'',
        fe_estado:'',
        id_usuario:'',
        usuario:''
    }
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){
        var that=this;
        that.reset();
        var id_remito=0;
        var cond="1=1 ";
        //priorizo la busqueda por ID
        if(patrones['id_remito']!="")
        {   if(es_numero(patrones['id_remito']))
            {
            id_remito=parseInt(patrones['id_remito'])
            cond+=" and id_remito="+patrones['id_remito'];    
            }
        }

        if(patrones['fecha_desde']!="" && !(id_remito>0))
        {var strdate=todate(patrones['fecha_desde'],103);
            cond+=' and fecha>="'+strdate+'"';
        }
        if(patrones['fecha_hasta']!="" && !(id_remito>0))
        {
            var strdate=todate(patrones['fecha_hasta'],103);
            cond+=" and fecha< ADDDATE('"+strdate+"',1)";
        }
        if(patrones['estado']!="" && !(id_remito>0))
        {
            
            cond+=" and estado='"+patrones['estado']+"'";
        }

        if(patrones['id_proveedor']!="" && !(id_remito>0))
        {
            
            cond+=" and id_proveedor='"+patrones['id_proveedor']+"'";
        }
        

        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_remito",this);
        }        
        var cmp=Array("id_remito","remito","id_proveedor","proveedor","estado","estado_desc","estadoclass","DATE_FORMAT(fecha,'%d/%m/%Y %r') as fecha","tipo","id_usuario_estado","usuario_estado","DATE_FORMAT(fe_estado,'%d/%m/%Y %r') as fe_estado","id_usuario","usuario","path");
        ofwlocal.getAsync("verremitos",cmp,cond,"fecha desc",function(rs){ 
            
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
         that.options.eventos.trigger("endload_remito",that);
        }
    },
    model:Elemento
});
var ElementosView=Backbone.View.extend(
{   el:$('#panel-body'),
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
        eventos.on("initload_remito",this.loading,this)
        eventos.on("endload_remito",this.endload,this)
        oColecciones.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body'));  
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
         spinnerEnd($('#panel-body'));
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
        {  var modelo=oColecciones.findWhere({'id_remito':id_model})
            this.mostrar(modelo)
        }
    },
    mostrar:function(modelo)
    {
        oCampos=new Campos(); //coleccion
        cCampo=new Campo({valor:modelo.get('id_remito'),nombre:'id_remito',tipo:'hidden',identificador:true});
        oCampos.add(cCampo);
        
        cCampo=new Campo({valor:modelo.get('remito'),nombre:'remito',tipo:'readonly',etiqueta:'Remito',esdescriptivo:true,obligatorio:false});
        oCampos.add(cCampo);
        var proveedor=modelo.get('proveedor')
        cCampo=new Campo({valor:proveedor,nombre:'proveedor',tipo:'readonly',etiqueta:'Proveedor',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);
        var strfecha_ingreso=modelo.get('fecha')+' - '+modelo.get('usuario')
        cCampo=new Campo({valor:strfecha_ingreso,nombre:'fecha',tipo:'text',etiqueta:'Fecha ingreso',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        var estado=modelo.get('estado_desc')+' ('+modelo.get('fe_estado')+' - '+modelo.get('usuario_estado')+')'
        cCampo=new Campo({valor:estado,nombre:'estado_desc',tipo:'text',etiqueta:'Estado',esdescriptivo:false,obligatorio:false});
        oCampos.add(cCampo);

        
        if(oColecciones ==null)
        {
        oColecciones=new Coleccion();    
        }
        
        var that=this;
        if(ocampoView ==null)
        {
        ocampoView=new campoView({campos:oCampos,mod:modelo,permite:'V',base_url:'<?=base_url()?>',tplname:'form_multipart.html',el:$("#row-body")});
        ocampoView.options.volver=function(){                
                        if (!$("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
                        $("#panel-body").slideToggle();            
                        }
                        $("#row-body").hide();    
                        $("#row-body").html("");
                        $("#row-det").hide();    
                        $("#row-det").html("");
        }
        ocampoView.options.onafterrender=function(){
            event.stopPropagation()
            
            var mo=ocampoView.options.mod
            var estaddd=mo.attributes.estado
                
            if(estaddd=='P'){
            $("#botonera3").html("<button type='button' id='editarComprobante' class='btn btn-sm btn-info'><i class='fa fa-edit'></i> Editar</button>");
            $("#editarComprobante").click(function(){                
                var id_remito=ocampoView.options.mod.get("id_remito");
                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/remitos/editar/"+id_remito);
                })
            }

            if(mo.get('path')!="null" && mo.get('path')!=""){
                $("#botonera2").html("<button type='button' id='mostrarComprobante' class='btn btn-sm btn-info'><i class='fa fa-file-text'></i> remito</button>"); 
            $("#mostrarComprobante").click(function(){                
                window.open('<?php echo base_url();?>'+mo.get('path'),'_blank');
            })
            }


             that.mostrardetalles(mo);
             }//afterrenderd
        }
        else
        {
            ocampoView.options.campos=oCampos
            ocampoView.options.mod=modelo
        }

        
 
         var targetLi = $("#panel-body").closest('li');
        if ($("#panel-body").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body").slideToggle();            
        }        
        ocampoView.render('V')
        
    },//mostrar
    mostrardetalles:function(oRemito){
        $("#row-det").show();    
        $("#row-det").html("");        
        olsDetalles=new DetallesView({el:$('#row-det')});                
        olsDetalles.render(oRemito)
    }

})//modelos view





function inicializacion_contexto()
{

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

$("#id_remito").keypress(function(e){
    return teclaentero(e)
})

olista=new ElementosView({el:$('#table-wrapper')});


$("#btn-consultar").click(function(e){    
    event.stopPropagation();
     consultar();
})
}//inicializacion contexto

function consultar()
{
    
var patrones={fecha_desde:$("#fecha_desde").val(),fecha_hasta:$("#fecha_hasta").val(), id_proveedor:$("#id_proveedor").val(),estado:$("#estado").val(),id_remito:$("#id_remito").val()}
 olista.render(patrones);
}


function crear_remito(){
  event.stopPropagation();
     handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/remitos/alta")  
}
</script>
<!-- ================== END PAGE LEVEL JS ================== -->