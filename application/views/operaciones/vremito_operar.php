<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$default='';
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/template" id="tpl-table-list">     
    <table id="data-table" class="table table-striped table-bordered nowrap" width="100%" style="color:#707478">
                            <thead>
                                <tr>
                                    <th>ARTICULO</th>                                        
                                    <th>STOCK</th>
                                    <th>PRECIO COMPRA</th>
                                    <th>PRECIO VENTA</th>
                                    <th>CANTIDAD</th>
                                    <th>-</th>                                        
                                </tr>
                            </thead>
                            <tbody>                                                               
        <% _.each(ls, function(elemento) {  
        
        var strproducto="("+elemento.get('id_articulo') + ") -" + elemento.get('articulo')
        if(elemento.get('categoria')!="" && elemento.get('categoria')!=null)
        {
            strproducto=strproducto + " " + elemento.get('categoria')
        }
        var strstock='no mueve stock'
        if(elemento.get('mueve_stock')==1)
        {   var stock=0;
            if(elemento.get('tipo_dato')=='int')
            {
             stock=parseInt(elemento.get('stock'));   
            }
            strstock='restan '+(stock).toString()+' '+elemento.get('fraccion_plural')
        }

        var disabledprecios=(modificarprecio)?"":"disabled='disabled'";
        var costo=(modificarprecio)?elemento.get('precio_venta'):"0";

        %>
        <tr>        
            <td><%=strproducto%></td>
            <td><%=strstock%></td>            
            <td><input type='text' value='<%=costo %>' class="form-control" name='inp-costo' id='inp-costo-<%=elemento.cid %>' <%=disabledprecios %> />
            </td>
            <td><input type='text' value='<%=elemento.get('precio_venta') %>' class="form-control" name='inp-venta' id='inp-venta-<%=elemento.cid %>' <%=disabledprecios %> />
            </td>
            <td><input type='text' value='1' class="form-control" name='inp-cant' id='inp-cant-<%=elemento.cid %>' />
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-primary" name='selecItem' id="seleccionar-<%=elemento.cid %>">
                    <i class="fa fa-arrow-right"></i>
                agregar</button>            
            </td>
        </tr>
        <% }); %>
        </tbody>
    </table>    
</script>

<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <input type="hidden" id="id_remito" value="<?=$id_remito?>"/>        
    <!-- begin row -->    
    <div class="row">
        <div class="col-md-12" >  
          <div class="panel panel-success">
           <div class="panel-heading">
            <div class="panel-heading-btn">                 
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            </div>            
                <h4 class="panel-title">SELECCIÓN DE ARTICULOS PARA EL REMITO</h4>
                
           </div>             
            <div class="panel-body bg-green text-white" id="panel-body-inputs">             
            <form class="form-horizontal" action="/" method="POST" id="form-avanzada" >
                <div class="form-group">
                    <div class="col-md-1">
                        <button type='button' class='btn btn-warning btn btn-sm'  class="form-control" id="btn-volver">
                            <i class='fa fa-reply' ></i>
                            VOLVER</button>
                    </div>
                    <div class="col-md-1">
                        <label>ID</label>
                        <input type="text" class="form-control" id="inp_id_articulo" placeholder="identificador del producto">
                    </div>
                    <div class="col-md-3">
                        <label>Nombre</label>
                        <input type="text" class="form-control" id="inp_nombre" placeholder="nombre del producto">
                    </div>
                    <div class="col-md-3">
                        <label>Categoria</label>
                        <select class="form-control" id="inp_id_categoria">
                                                <option value="">TODAS</option>
                                                <?php foreach ($categorias as  $cat) {
                                                    $id_categoria=$cat['id_categoria'];
                                                    $categoria=$cat['categoria'];
                                                  echo "<option value='$id_categoria'>$categoria</option>";
                                                }
                                                ?>
                         </select> 
                    </div>
                    <div class="col-md-3">
                           <label>Codigo de barras</label>
                            <input type="text" class="form-control" id="inp_codbarras" placeholder="ingrese codigo">                               
                    </div>
                    <div class="col-md-1">                     
                        <button type="button" class="btn btn-sm btn-primary" id="btn-buscar">
                            <i class="fa fa-search"></i>
                        Buscar</button>
                    </div>
                </div>                
                 <div class="form-group" id="tpl-table-query">
                 </div>
            </form>
            </div>
        </div>
    </div>
</div>
<!-- end row -->    
<!-- begin row -->    
<div class="row" id="tpl_opera_carrito">
</div>
<!-- end row -->    
<!-- begin row -->    
<div class="row" >
    <div class="col-md-12" >  
  <div class="panel panel-info">
   <div class="panel-heading">
    <div class="panel-heading-btn">
        <div class="btn-group pull-right" id="btn-acciones">
            <button type="button" class="btn btn-success btn btn-sm" id="btn-pendiente" onclick="valida_remito()">
             <i class="fa fa-save"></i>GUARDAR
           </button>
        </div>
    </div>
        <h4 class="panel-title">
            <span class="label label-warning m-r-4" style="font-size: 14px" > 
                DATOS DEL REMITO
            </span>             
        </h4>
   </div>             
        <div class="panel-body bg-aqua text-white" id="panel-body-remito">
            <form class="form-horizontal"  action="/" method="POST">            
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>DETALLE DEL REMITO
                                     <input type="text" id="remito" name="remito" class="form-control" />
                                     </label>
                                </div>
                                <div class="col-md-4">
                                    <label>PROVEEDOR
                                    <select id="id_proveedor" class="form-control" >
                                            <option name="" value="">SELECCIONE</option>
                                            <?php                                            
                                foreach ($proveedores as $tipo_comp) {
                                echo "<option value='".$tipo_comp['id_proveedor']."'>".$tipo_comp['proveedor']."</option>";
                                            }
                                            ?>
                                     </select>
                                     </label>
                                </div>
                                <div class="col-md-2">                                    
                                    <label>MODIFICAR PRECIOS
                                    <input type="checkbox" id="chkprecios" name="chkprecios" class="form-control" />
                                    </label>
                                    
                                </div>
                                <div class="col-md-2">                                    
                                    <label>ESTADO
                                    <select id="estado" class="form-control" >
                                            <option name="" value="">SELECCIONE</option>
                                            <?php                                            
                                foreach ($estados as $es) {
                                echo "<option value='".$es['estado']."'>".$es['descripcion']."</option>";
                                            }
                                            ?>
                                     </select>
                                    </label>
                                    
                                </div>
                            </div>
        </form>
        </div>
    </div>
</div>
</div>
<!-- end row -->    

</div>
<!-- end #content -->

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
var localurlx=window.location.href.split("#")[1];

App.restartGlobalFunction();
App.setPageTitle('Operar remito | CoffeBox APP');
var win= new fwmodal();
var ofwlocal=new fw('<?=base_url()?>index.php/')
ofwlocal.guardarCache=false;
var remitogenerado=false;
var ocampoView=null;    
var oCarrito=null;
var oCarritoview=null;
var oCampos=null;
var oClienteView=null;
var oArticuloView=null;
var oArticulos=null;
var oCategorias=null;
var oElementView=null;
var oRemito=null;



$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        inicializacion_contexto();
                    })
                });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);

var Remito=Backbone.Model.extend({    
     url:'',
    default:{
        cliente:null,
        carrito:null,
        id_tipo_comp:0,
        pagos:null,
        afip:0,
        estado:'',
        ctacte:0,
        id_comp:0
            }
});//Pago



var Articulo=Backbone.Model.extend({    
    idAttribute:'id_articulo'
});//Productomodel

var Carrito=Backbone.Collection.extend({
    model:Articulo
})

var Articulos=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
    },
    loadAsync:function(patrones){

        var that=this;
        that.reset();        
        var cond="1=1 "
        
        if(typeof patrones['articulo'] !="undefined")
        {patrones['articulo']=patrones['articulo'].trim();
            if(patrones['articulo']!="")
            {
            cond+=' and articulo like "'+patrones['articulo']+'%"'    
            }
            
        }
        if(typeof patrones['codbarras'] !="undefined")
        {patrones['codbarras']=patrones['codbarras'].trim();
            if(patrones['codbarras']!="")
            {
            cond+=' and codbarras="'+patrones['codbarras']+'"'
            }   
        }
        //si ingresa ID que aplaste los filtros anteriores
        if(typeof patrones['id_articulo']!="undefined")
        {   if(patrones['id_articulo']!="")
            {
            patrones['id_articulo']=patrones['id_articulo'].trim();
            cond=' and id_articulo='+patrones['id_articulo']    
            }
            
        }
        if(typeof patrones['id_categoria']!="undefined")
        {   if(patrones['id_categoria']!="")
            {
            patrones['id_categoria']=patrones['id_categoria'].trim();
            cond+=' and id_categoria='+patrones['id_categoria']
            }
        }

                        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }        
        ofwlocal.getAsync("verarticulos",Array("*"),cond,"articulo asc,categoria asc",function(rs){ that.cargar(rs,that) } )


    },    
    cargar:function(rs,that)
    {       
     for (c in rs)
     { rs[c].importe_tipo=parseFloat(rs[c].importe_tipo)        
      that.add(rs[c])
      }
        if(typeof that.options.eventos !="undefined")
        {
         that.options.eventos.trigger("endload",that);
        }
    },
    model:Articulo
});

var Carritoview=Backbone.View.extend(
{   el:$('#tpl_opera_carrito'),
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function()
    {
        var that=this;
        
        if(remitogenerado)
        {win.alert("El remito ya se realizó"," No se puede agregar remitos",4)
            return;
        }

        $.get(this.options.base_url+'tpl/opera_remito.html', function (data) {
            tpl = _.template(data, {});
            modificarprecio=$("#chkprecios").is(":checked");              
            htmlrender=tpl({campos:oCarrito.models,base_url:that.options.base_url,modificarprecio:modificarprecio})
            that.$el.html(htmlrender);
            $("input[name='car_cantidad']").keypress(function(e){ return teclaentero(e)})
            $("input[name='car_cantidad_float']").keypress(function(e){ return teclamoney(e)})
            $("input[name='car_venta']").keypress(function(e){ return teclamoney(e)})
            $("input[name='car_costo']").keypress(function(e){ return teclamoney(e)})
             //that.totalizar()
            if(typeof that.options.onlyread !="undefined"){
                if(that.options.onlyread){
                inputCarrito_read()    
                }
             }
        })

    },
    events:{
         "keyup input[name='car_costo']":'cambiaprecio',
         "keyup input[name='car_cantidad']":'cambiacantidad',
         "keyup input[name='car_cantidad_float']":'cambiacantidad',
         "click span[id*='car_remove']":'eliminar',

    },//events
    eliminar:function(e){
        
    var id_button=(e.target.id.indexOf("car_remove")>=0)?e.target.id: e.target.parentNode.id 
    var id_producto=id_button.replace("car_remove_","");
    var modelo=oCarrito.get(id_producto);
    oCarrito.remove(modelo);
    },
    cambiaprecio:function(e)
    {   
        var id_producto=e.target.id.replace("car_costo_","");        
        var modelo=oCarrito.get(id_producto);
        var new_costo=(!es_numero($("#"+e.target.id).val()))? 0:parseFloat($("#"+e.target.id).val());
        var new_importe_total=new_costo*parseFloat(modelo.get("cantidad"));            
        modelo.set({"precio_costo":new_costo});
        modelo.set({"costo_total":new_importe_total});
        new_importe_total=tomoney(new_importe_total);
        $("#car_total_"+id_producto).html("$ "+new_importe_total.toString());            
       
        for (i in oCarrito.models)
       {
        if(oCarrito.models[i].id==modelo.id)
        {
         oCarrito.models[i]=modelo;   
         break
        }
       }
            //this.totalizar()
        
    },    
    cambiacantidad:function(e)
    {   
        var name=e.target.name
        var id_producto=e.target.id.replace("car_cantidad_","");        
        var modelo=oCarrito.get(id_producto);
        var new_cantidad=0;
         if(name.indexOf('float'))
         {
            new_cantidad=(!es_numero($("#"+e.target.id).val()))? 0:parseFloat($("#"+e.target.id).val());
         }else{
            new_cantidad=(!es_entero($("#"+e.target.id).val()))? 0:parseInt($("#"+e.target.id).val());
         }             
             
        var precio_costo=parseFloat(modelo.get("precio_costo"));            
        var new_importe_total=new_cantidad*precio_costo;
        new_importe_total=tomoney(new_importe_total);
        modelo.set({"cantidad":new_cantidad})
        modelo.set({"costo_total":new_importe_total})
       $("#car_total_"+id_producto).html("$ "+new_importe_total.toString())
       for (i in oCarrito.models)
       {
        if(oCarrito.models[i].id==modelo.id)
        {
         oCarrito.models[i]=modelo;   
         break
        }

       }
        //this.totalizar()
        
    },
    totalizar:function()
    {   
        var total_abona=0;
        for(i in oPagos.models)
        {
         var p=oPagos.models[i]         
         total_abona+=parseFloat(p.get("monto_abona"));
        }
        var total=0;
        var total_iva=0;
        for(p in oCarrito.models) 
        {
           total+=parseFloat(oCarrito.models[p].get("importe_total"));
           total_iva+=parseFloat(oCarrito.models[p].get("importe_iva"));
        }
        
        $("#total_parcial").html(format_number((total).toString(),'#.00'));
        $("#total_iva").html(format_number((total_iva).toString(),'#.00'));
        var cambio=0;        
        
        var ta=parseFloat(format_number((total_abona).toString(),'#.00'))
        var t=parseFloat(format_number((total).toString(),'#.00'))
         cambio=round2dec(ta-t);

        $("#cambio").html(format_number((cambio).toString(),'#.00'));
        
    }
})

var Comprobanteview=Backbone.View.extend(
{   el:$('#tpl_opera_carrito'),
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(oProd)
    {
        var that=this;
        this.productos=oProd;
        $.get(this.options.base_url+'tpl/opera_pago.html', function (data) {
            tpl = _.template(data, {});
            htmlrender=tpl({campos:oProd.models,base_url:that.options.base_url})
            that.$el.html(htmlrender);
        })
        
    }
})

function desc_producto(modelo)
{
     var strhtml="<ul>"
                 strhtml+="<li>Identificativo: "+modelo.get("id_articulo")+"</li>"
                 strhtml+="<li>Nombre: "+modelo.get("producto")+"</li>"
                 strhtml+="<li>Categoria: "+modelo.get("categoria")+"</li>"                 
                 strhtml+="<li>Precio Base:$ "+format_number(modelo.get("importe_base"),'#.00')+"</li>"
                 strhtml+="<li>Importe IVA:$ "+format_number(modelo.get("importe_iva"),'#.00')+" IVA:"+modelo.get("iva")+"</li>"
                 strhtml+="<li>Precio de venta por  "+modelo.get("fraccion_plural")+":$ "+format_number(modelo.get("importe_tipo"),'#.00')+"</li>"
                 strhtml+="</ul>"
                 return strhtml;
}


var ArticuloView=Backbone.View.extend(
{   el:$('#tpl-table-query'),
    productos:null,    
    initialize:function(options)
    {
        this.options=options || {};
        that=this;
        eventos.on("initload",this.loading,this)
        eventos.on("endload",this.endload,this)
        this.listenTo(oCarrito, 'add',function(){            
          oCarritoview.render()  
          //oRemitorobanteview.render(oCarrito)  
        });
        this.listenTo(oCarrito, 'remove',function(){            
          oCarritoview.render()  
          //oRemitorobanteview.render(oCarrito)  
        });
    },
    render:function(patrones)
    {
        var that=this;
        if(oArticulos ==null)
        {
        oArticulos=new Articulos({eventos:eventos});    
        }
        
        oArticulos.loadAsync(patrones)
        $('#data-table').DataTable({responsive: true}); 
        return this;
    },//render        
    events:{        
            "click button[name='selecItem']":'seleccionar'
    },
    endload:function(oCols)
    {
        this.cargar(oCols)
        
    },
    loading:function()
    {        
    spinnerStart($('#panel-body-inputs'));
    },
    cargar:function(oCols)
    {
        var that=this;
        olist=oCols.models  
        var tpl=_.template($('#tpl-table-list').html());  
        modificarprecio=$("#chkprecios").is(":checked");              
        this.$el.html(tpl({ls:olist, modificarprecio:modificarprecio}));        
        $("input[name='inp-cant']").keypress(function(e){return teclaentero(e)});
        $("input[name='inp-costo']").keypress(function(e){return teclamoney(e)});
        $("input[name='inp-venta']").keypress(function(e){return teclamoney(e)});
        spinnerEnd($('#panel-body-inputs'));
        $("#tpl-table-query").show();
    },
    seleccionar:function(e)
    {   
           var id_button=(e.target.id.indexOf("seleccionar-")>=0)?e.target.id: e.target.parentNode.id //hago esto porque es depende de donde hago click (si en el boton o en el icoono)
        if(id_button=="")
        return
            
        
        id_model=id_button.replace("seleccionar-","");
        var precio_venta=$("#inp-venta-"+id_model).val()

        if(!es_numero(precio_venta))
        {
            win.alert("El precio de venta no es valido."," Atención",4)
            return
        }


        var precio_costo=$("#inp-costo-"+id_model).val()

        if(!es_numero(precio_costo))
        {
            win.alert("El precio de costo no es valido."," Atención",4)
            return
        }
        var modelo=oArticulos.get(id_model)

        if(typeof modelo=="undefined")        
            return        
        
        
        var cantidad=$("#inp-cant-"+id_model).val();
        if(!es_entero(cantidad))
        {
            win.alert("El numero ingresado no es valido, el mismo debe ser entero mayo a cero."," Atención",4)
            return
        }
        modelo.set({precio_costo:precio_costo})
        modelo.set({precio_venta:precio_venta})

        var modeloOld=oCarrito.findWhere({id_articulo:modelo.id})
        var cantTotal=parseInt(cantidad)
        if(typeof modeloOld!="undefined")
        {   cantTotal+=parseInt(modeloOld.get("cantidad"));
            oCarrito.remove(modeloOld,{silent:true}) 
        }
        var costo_total=cantTotal*modelo.get("precio_costo")
        var total=cantTotal*modelo.get("precio_venta")
        //var importe_total=tomoney(total)
        modelo.set({cantidad:cantTotal,costo_total:costo_total},{silent:true})        
        oCarrito.add(modelo);
        //if(!$("#seachvisible").is(":checked"))
        $("#tpl-table-query").html("");
        $("#inp_nombre").val("");
        $("#inp_codbarras").val("");
        $("#inp_id_articulo").val("");
        $("#inp_id_categoria").val("");
        
         /*$("#form-avanzada").hide();
         $("#panel-body-inputs").show();
         $("#tpl-table-query").html("");         
         $("#chkavanzada").prop("checked",false)*/
    },
    editar:function()
    {   
    }
});//ElementosView

function inicializacion_contexto()
{   
    
    oCarrito=new Carrito();    
    oArticuloView=new ArticuloView({el:$('#tpl-table-query')})
    oCarritoview=new Carritoview({el:$('#tpl_opera_carrito'),base_url:'<?=base_url()?>'});
    
    $("#btn-volver").click(function(e){    
    event.stopPropagation();
     handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/remitos/")
    })
    $("#chkprecios").click(function(){
     oCarritoview.render();
    })


    $("#cantidad").keypress(function(e){return teclaentero(e)});
    $("#inp_id_articulo").keypress(function(e){return teclaentero(e)});
    $("#inp_codbarras").keypress(function(e){return teclaentero(e)});
    
    $("#btn-buscar").click(function(){        
        var patron={id_articulo:$("#inp_id_articulo").val(),codbarras:$("#inp_codbarras").val(),articulo:$("#inp_nombre").val(),id_categoria:$("#inp_id_categoria").val()};
        oArticuloView.render(patron);
    })
    

if(parseInt($("#id_remito").val())>0)
{
cargar_remito();    
}


}//inicializacion contexto

function valida_remito(){
event.stopPropagation()
        var strerror=""        
        if(remitogenerado)
        {
         win.alert("Este remito ya se realizó"," ATENCIÓN",4)  
         return   
        }
        var estado=$("#estado").val()
        var estado_desc=$("#estado option[value='"+estado+"']").text();
        if(estado==""){
         strerror+="<li>No ha seleccionado un estado a guardar el remito</li>"   
        }
        if($("#id_proveedor").val()==""){
         strerror+="<li>No ha seleccionado proveedor del remito</li>"      
        }
        var id_remito=parseInt($("#id_remito").val())
        if(id_remito>0)
        {
        var rs=ofwlocal.get("remito",Array("estado"),"id_remito="+id_remito)
            if(rs[0]['estado']==estado)
            {
             strerror+="<li>No se puede guardar el remito en el estado actual</li>"         
            }    
        }
        
                
        if(strerror!="")
        {
         strerror="<ul>"+strerror+"</ul>"
         win.alert(strerror," ATENCIÓN",4)  
         return
        }

        if(oCarrito.models.length!=0)
        {
        
            var strhtml="Usted esta a punto de generar un remito "+estado_desc+". ";
            
            if($("#chkprecios").is(":checked") && estado=="E")
            { 
                strhtml+="Tenga en cuenta que ha elegido cambiar el precio de estos productos.";
            }
            if(estado=="E"){
                strhtml+="El stock de los articulos ingresados se va a actualizar.";
            }
            strhtml+="¿Desea continuar?";
            swal({
          title: "Atención",
          text: strhtml,
          type: "info",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
            }, function () {
             guardar_remito()
            });

            
        }else{
            swal("Atención", "No ha ingresado un articulo para hacer el remito", "error")
            
        }
}//valida comp



function guardar_remito()
{
var remito=$("#remito").val()
var id_proveedor=$("#id_proveedor").val()
var estado=$("#estado").val()
var modificarprecio=($("#chkprecios").is(":checked"))?1:0;

var id_remito=parseInt($("#id_remito").val())
oRemito=new Remito({carrito:oCarrito,modificarprecio:modificarprecio,remito:remito,estado:estado,id_proveedor:id_proveedor,id_remito:id_remito})
oRemito.url= '<?=base_url();?>index.php/operaciones/remitos/guardar/'    


spinnerStart($("#panel-body-remito"))
oRemito.save(null,{
               type:'POST', 
                    success:function(e,params){                            
                        spinnerEnd($("#panel-body-remito"))
                                if(params.numerror!=0 )
                                {
                                    
                                    swal("Error al guardar remito", "Detalle: "+params.descerror+". Consulte con el administrador", "error")
                                    
                                }
                                else
                                {   //cuando llegue a estado F, es su estado final
                                    var estadonuevo=params.data.estado
                                    if(estadonuevo=="E" || estadonuevo=="A"){
                                    remitogenerado=true;
                                    inputCarrito_read();
                                    }
                                    
                                    
                                    oRemito.set({id_remito:params.data.id_remito});
                                    oRemito.set({estado:params.data.estado});
                                    $("#id_remito").val(params.data.id_remito);
                                   
                                        if(params.data.estado=="P")
                                        {
                                            swal("Perfecto", "Remito quedó guardado con el ID "+params.data.id_remito, "success")
                                                
                                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/operaciones/remitos/")
                                        }else{
                                            
                                            swal("Perfecto", "El remito se guardó correctamente", "success")
                                        }
                                    
                                    if(estadonuevo=='E')
                                    {
                                    window.open('<?php echo base_url();?>'+params.data.path ,'_blank');
                                    }
                                     
                                }
                            },
                    error: function(e,params) {
                        spinnerEnd($("#panel-body-remito"))
                        win.alert(params.responseText,"Error",4)
                                    
                    },
                    wait:true
                    })//save


}//guardar_remito

function cargar_remito()
{
spinnerStart($('#panel-body-inputs'));
var rs=ofwlocal.get("verremitos",Array("*"),"id_remito="+$("#id_remito").val(),"");
var remito=rs[0];
$("#id_proveedor").val(remito['id_proveedor'])
$("#estado").val(remito['estado'])
$("#remito").val(remito['remito'])

if(remito['modificaprecio']=="1"){
    $("#chkprecios").prop("checked",true)
}
if(remito['estado']=="A" || remito['estado']=="E")
{
    remitogenerado=true;
}
var htmlmsn="";
var rs=ofwlocal.get("verremitos_det",Array("*"),"id_remito="+$("#id_remito").val(),"");
for(i in rs){
    var id_articulo=rs[i]['id_articulo'];
    var rsprod=ofwlocal.get("verarticulos",Array("*"),"id_articulo='"+id_articulo+"'","");
    if(rsprod.length>0)
    {
    prod=rsprod[0];
    var cantTotal= (es_numero(rs[i]['cantidad']))? parseFloat(rs[i]['cantidad']):0;
    var precio_costo= (es_numero(rs[i]['precio_costo']))? parseFloat(rs[i]['precio_costo']):0;
    var precio_venta= (es_numero(rs[i]['precio_venta']))? parseFloat(rs[i]['precio_venta']):0;
    var costo_total=cantTotal*precio_costo;
    prod['cantidad']=cantTotal;
    prod['precio_costo']=precio_costo;
    prod['costo_total']=costo_total;
    oCarrito.add(prod);
    }else{
        htmlmsn +="<li>El producto "+rs[i]['detalle']+" ("+id_remito+") no esta disponible</li>"
    }
}
if(htmlmsn!="")
{
    win.alert("<ul>"+htmlmsn+"</ul>"," EL REMITO YA NO SERÁ EL MISMO",4)
}

spinnerEnd($('#panel-body-inputs'));
}//cargar remito


function inputCarrito_read()
{ 
    $("#panel-body-inputs input[type='text']").each(function(){  $(this).prop("disabled",true)})
    $("#panel-body-inputs select").each(function(){  $(this).prop("disabled",true)})
    $("#panel-body-carrito input[type='text']").each(function(){  $(this).prop("disabled",true)})
    
     
 }

</script>
<!-- ================== END PAGE LEVEL JS ================== -->
