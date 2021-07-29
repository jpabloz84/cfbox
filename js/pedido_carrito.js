

var tipos_productos_default=$("#tipos_productos_default").val();
var base_url=$("#base_url").val();
var base_fw=$("#base_fw").val();
App.setPageTitle('pedido | CoffeBox APP');

var ofwlocal=new fw(base_url+'index.php/')
ofwlocal.guardarCache=false;
var ofwt=new fwt(base_url+'index.php/operaciones/ventas/listener')
var oCliente=null;
var oCarrito=null;
var oCarritoview=null;
var oClienteView=null;
var oProductoView=null;
var oProductos=null;
var oCategorias=null;



var id_empresa=$("#id_empresa").val();
var id_cliente=$("#id_cliente").val();
var stmtempresa=" and id_empresa="+id_empresa;

    $.getScript(base_fw+'assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
                $.getScript(base_fw+'assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript(base_fw+'assets/plugins/masked-input/masked-input.min.js').done(function(){                        
                            inicializacion_contexto();                        
                        
                    });
        });
    });


var eventos = _.extend({}, Backbone.Events);



var Producto=Backbone.Model.extend({    
    idAttribute:'id_producto'
});//Productomodel

var Carrito=Backbone.Collection.extend({
    model:Producto,
    gettotal:function(campo){
        var total=0;
        for (i in this.models) {
            var valor=this.models[i].get(campo);
            if(esNumeroReal(valor))
            {
                total+=parseFloat(valor)
            }
        }
        return total;
    },
    actualizarModel:function(modelo){
        //definicion:dado un modelo, lo reemplazo en el arreglo
        var i=-1;
        for (i in this.models)
         { 
            if(this.models[i].cid==modelo.cid)
            {
             this.models[i]=modelo;               
             i=i;
             break;
            }

         }
         return i;
     },//actualizarmodel
     load:function (rs) {
        this.reset();        
         for(r=0;r<rs.length;r++){
            var prod=new Producto(rs[r])
            this.add(prod)
         }
     }
})

var Productos=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
    },
    loadAsync:function(patrones){

        var that=this;
        that.reset();        
        var cond="habilitado=1 "
        
        if(typeof patrones['producto'] !="undefined")
        {patrones['producto']=patrones['producto'].trim();
            if(patrones['producto']!="")
            {
            cond+=' and producto like "'+patrones['producto']+'%"'    
            }
            
        }
        if(typeof patrones['codbarras'] !="undefined")
        {patrones['codbarras']=patrones['codbarras'].trim();
            if(patrones['codbarras']!="")
            {
            cond+=' and codbarras="'+patrones['codbarras']+'"'
            }   
        }
        if(typeof patrones['nro_tipo']!="undefined")
        {   if(patrones['nro_tipo']!="")
            {
            patrones['nro_tipo']=patrones['nro_tipo'].trim();
            cond+=' and nro_tipo='+patrones['nro_tipo']    
            }
            
        }

        if(typeof patrones['id_fraccion']!="undefined")
        {   if(patrones['id_fraccion']!="")
            {
            patrones['id_fraccion']=patrones['id_fraccion'].trim();
            cond+=' and id_fraccion='+patrones['id_fraccion']
            }
        }

        if(typeof patrones['id_categoria']!="undefined")
        {   if(patrones['id_categoria']!="")
            {
            patrones['id_categoria']=patrones['id_categoria'].trim();
            cond+=' and id_categoria='+patrones['id_categoria']
            }
        }

        if(typeof patrones['id_tipo']!="undefined")
        {   if(patrones['id_tipo']!="")
            {
            patrones['id_tipo']=patrones['id_tipo'].trim();
            cond+=' and id_tipo='+patrones['id_tipo']
            }
        }
         
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }        
        ofwlocal.getAsync("verproductos",Array("*"),cond+stmtempresa,"producto asc,tipo asc",function(rs){ that.cargar(rs,that) } )


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
    model:Producto
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
        
        
        $.get(this.options.base_url+'tpl/opera_carrito.html', function (data) {
            tpl = _.template(data, {});
            htmlrender=tpl({campos:oCarrito.models,base_url:that.options.base_url})
            that.$el.html(htmlrender);
            $("#panel-body-carrito table").DataTable({paging:false,responsive: true,searching:false,lengthChange:false,pageLength:10,
            "columns": [
            { "orderable": false },
            { "orderable": false },
            { "orderable": false},
            { "orderable": false},
            { "orderable": false},
            { "orderable": false},
            { "orderable": false}
            ]
         }); 
            
             $('[data-toggle="popover"]').popover('hide');
             $('[data-toggle="popover"]').popover({html:true, container: 'body'})
              $('[data-toggle="popover"]').on('shown.bs.popover', function (e,b) {
                var that=this;
                setTimeout(function() {
                    
                    $('[data-toggle="popover"]').popover('hide'); 

                },4000);
            });
             //setTimeout(function(){$('[data-toggle="popover"]').popover('hide')}, 5000);

            $("input[name='car_cantidad']").keypress(function(e){ return teclaentero(e)})
            $("input[name='car_cantidad_float']").keypress(function(e){ return teclamoney(e)})
            $("input[name='car_precio']").keypress(function(e){ return teclamoney(e)})
            $("input[name='car_precio']" ).focus(function(e) {
              //si se hace foco el monto es 0, se pone en vacio
              if($(this).val()==0){
                $(this).val("");
              }
            });
            $("a[id*='car_generico']").click(function(e){
                var id_producto=e.target.id.replace("car_generico_","");
                 var modelo=oCarrito.get(id_producto);
                 var strhtml=desc_producto(modelo)
              win.alert(strhtml," Detalle del "+modelo.get("tipo"),2)
                
            })

           
            $("a[id*='car_detalle_link']").click(function(e){                
                var tagid=(e.target.id.indexOf("car_detalle_link_")>=0)?e.target.id: e.target.parentNode.id
                var id_producto=tagid.replace("car_detalle_link_","");
                $("#"+tagid).hide();
                $("#car_detalle_"+id_producto).show();
                $("#car_detalle_"+id_producto).focus();
             
            })


            $("a[id*='car_detalle_label']").click(function(e){
               
            })

            $("input[name='car_detalle']").keypress(function(e){                
                var id_producto=e.target.id.replace("car_detalle_","")
                if(e.which==13)
                {
                    $("#"+e.target.id).hide();
                    $("#car_detalle_link_"+id_producto).show();    
                    var modelo=oCarrito.get(id_producto);
                    var newnombre=$("#"+e.target.id).val()
                    $("#car_detalle_link_"+id_producto).html(newnombre.toUpperCase())
                    modelo.set({"producto":newnombre.toUpperCase()})             
                   oCarrito.actualizarModel(modelo);
                }
                return true;
                
            })
            
             that.totalizar()
            if(typeof that.options.onlyread !="undefined"){
                if(that.options.onlyread){
                inputCarrito_read()    
                }
             }
        })

    },
    events:{
         "keyup input[name='car_precio']":'cambiaprecio',
         "keyup input[name='car_cantidad']":'cambiacantidad',
         "keyup input[name='car_cantidad_float']":'cambiacantidad',
         "click button[id*='car_remove']":'eliminar',

    },//events
    eliminar:function(e){
        
    var id_button=(e.target.id.indexOf("car_remove")>=0)?e.target.id: e.target.parentNode.id 
    var id_producto=id_button.replace("car_remove_","");
    var modelo=oCarrito.get(id_producto);
    oCarrito.remove(modelo);
    
    },
    cambiaprecio:function(e)
    {   
        var id_producto=e.target.id.replace("car_precio_","");
        
         var modelo=oCarrito.get(id_producto);
            var new_importe_tipo=(!es_numero($("#"+e.target.id).val()))? 0:parseFloat($("#"+e.target.id).val());
            var new_importe_total=new_importe_tipo*parseFloat(modelo.get("cantidad"));
            
            var iva=parseFloat(modelo.get("iva"));
            var new_importe_base=tomoney(new_importe_tipo/(iva+1)); //calculo de importe base en funcion del importe tipo
            modelo.set({"importe_base":new_importe_base});
            var new_importe_iva=tomoney(new_importe_base*iva); //calculo de importe base en funcion del importe tipo
            modelo.set({"importe_iva":new_importe_iva})
            $("#car_iva_"+id_producto).html("$ "+new_importe_iva);
            $("#car_base_"+id_producto).html("$ "+new_importe_base);
            
            new_importe_total=tomoney(new_importe_total);
            modelo.set({"importe_tipo":new_importe_tipo})
            modelo.set({"importe_total":new_importe_total})
           $("#car_total_"+id_producto).html("$ "+new_importe_total.toString())
           
           oCarrito.actualizarModel(modelo);
            this.totalizar()
        
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
             
            var importe_tipo=parseFloat(modelo.get("importe_tipo"));
            var iva=parseFloat(modelo.get("iva"));
            var new_importe_base=tomoney(importe_tipo/(iva+1)); //calculo de importe base en funcion del importe tipo
            modelo.set({"importe_base":new_importe_base});
            var new_importe_iva=tomoney(new_importe_base*iva); //calculo de importe base en funcion del importe tipo
            modelo.set({"importe_iva":new_importe_iva})
            $("#car_iva_"+id_producto).html("$ "+new_importe_iva.toString());
            $("#car_base_"+id_producto).val("$ "+new_importe_base.toString());

            var new_importe_total=new_cantidad*importe_tipo;
            new_importe_total=tomoney(new_importe_total);
            modelo.set({"cantidad":new_cantidad})
            modelo.set({"importe_total":new_importe_total})
           $("#car_total_"+id_producto).html("$ "+new_importe_total.toString())
                      
           oCarrito.actualizarModel(modelo);

            this.totalizar()
        
    },
    totalizar:function()
    {   
        
       /* var total=oCarrito.gettotal('importe_total');
        //var total_iva=oCarrito.gettotal('importe_iva');
        
        $("span[name='total_parcial']").html(format_number((total).toString(),'#.00'));
        //$("#total_iva").html(format_number((total_iva).toString(),'#.00'));
        var total_resta=round2dec(total-total_abona);
        if(total_resta<0)
        {
          total_resta=0;  
        }
        $("#total_resta").html(format_number((total_resta).toString(),'#.00'));
        var cambio=0;

         cambio=round2dec(total_abona-total);
         if(cambio<0)
            cambio=0;

        $("#cambio").html(format_number((cambio).toString(),'#.00'));*/
        
    }
})


function desc_producto(modelo)
{
     var strhtml="<ul>"
                 strhtml+="<li>Identificativo: "+modelo.get("nro_tipo")+"</li>"
                 strhtml+="<li>Nombre: "+modelo.get("producto")+"</li>"
                 strhtml+="<li>Categoria: "+modelo.get("categoria")+"</li>"                 
                 strhtml+="<li>Precio Base:$ "+format_number(modelo.get("importe_base"),'#.00')+"</li>"
                 strhtml+="<li>Importe IVA:$ "+format_number(modelo.get("importe_iva"),'#.00')+" IVA:"+modelo.get("iva")+"</li>"
                 strhtml+="<li>Precio de venta por  "+modelo.get("fraccion_plural")+":$ "+format_number(modelo.get("importe_tipo"),'#.00')+"</li>"
                 strhtml+="</ul>"
                 return strhtml;
}


var ProductoView=Backbone.View.extend(
{   el:$('#tpl_opera_cliente'),
    productos:null,    
    initialize:function(options)
    {
        this.options=options || {};
        that=this;
        eventos.on("initload",this.loading,this)
        eventos.on("endload",this.endload,this)
        this.listenTo(oCarrito, 'add',function(){            
          oCarritoview.render()            
        });
        this.listenTo(oCarrito, 'remove',function(){            
          oCarritoview.render()  
          //oComprobanteview.render(oCarrito)  
        });
    },
    render:function(patrones,elementEvent=null)
    {   this.elementEvent=elementEvent
        var that=this;

        if(oProductos ==null)
        {
        oProductos=new Productos({eventos:eventos});    
        }  

        oProductos.loadAsync(patrones)        
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
     //no poner spinner porq cuando es busqueda avanzada, no se oculta
    //spinnerStart($('#panel-body-inputs'));
    if(this.elementEvent!=null){
        if(this.elementEvent[0].id=="btn-buscar"){
            this.elementEvent.button("loading")
        }
    }
    },
    cargar:function(oCols)
    {
        var that=this;
        olist=oCols.models  
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist}));        
        $("input[name='inp-cant']").keypress(function(e){return teclaentero(e)});
        if(this.elementEvent!=null){
        if(this.elementEvent[0].id=="btn-buscar"){
            this.elementEvent.button("reset")
        }
    }
       // spinnerEnd($('#panel-body-inputs'));
        $("#tpl-table-query").show();
    },
    seleccionar:function(e)
    {   
           var id_button=(e.target.id.indexOf("seleccionar-")>=0)?e.target.id: e.target.parentNode.id //hago esto porque es depende de donde hago click (si en el boton o en el icoono)
        if(id_button=="")
        return
            
            //si contiene la leyenda de agregado , no se agrega
        if($("#"+id_button).html().indexOf("agregado")>=0){
            return
        }

        id_model=id_button.replace("seleccionar-","");
        var modelo=oProductos.get(id_model)
        
        if(typeof modelo=="undefined")        
            return        
        
        
        var cantidad=$("#inp-cant-"+id_model).val();
        if(!es_entero(cantidad))
        {
            
            swal("Atención!", "El numero ingresado no es valido, el mismo debe ser entero y mayor a cero.", "error")
            return
        }
        
        if(modelo.get("mueve_stock")=="1"){
            var stock=parseFloat(modelo.get("stock"));    
            var c=parseFloat(cantidad);
            if(stock<c){
                var pr=modelo.get("producto");                
                swal("Atención!", "No hay stock suficiente para "+pr+" (disponible: "+stock.toString()+", solicitado: "+c.toString()+")", "error")
                return
            }
        }


        var modeloOld=oCarrito.findWhere({id_producto:modelo.id})
        var cantTotal=parseInt(cantidad)
        if(typeof modeloOld!="undefined")
        {   cantTotal+=parseInt(modeloOld.get("cantidad"));
            oCarrito.remove(modeloOld,{silent:true}) 
        }
        
        var total=cantTotal*modelo.get("importe_tipo")
        var importe_total=tomoney(total)
        modelo.set({cantidad:cantTotal,importe_total:importe_total},{silent:true});
        oCarrito.add(modelo);
        
       
        if(!$("#seachvisible").is(":checked"))
        $("#tpl-table-query").html("");

        if(!$("#modalvisible").is(":checked"))
            $("#modal-manual-busqueda").modal("hide");
        
        var htmlOld=$("#"+id_button).html()
        var classOld=$("#"+id_button).prop("class")
        $("#"+id_button).prop("class","btn btn-xs btn-success")
        
        $("#"+id_button).html("agregado");
        setTimeout(function(){

            $("#"+id_button).html(htmlOld)
            $("#"+id_button).prop("class",classOld)
            }, 1000);
        
        
    },
    editar:function()
    {   
        if(permitir(prt_clientes,4))
        {
            that=this;
        win.dialog('Usted va a abandonar esta pantalla y va perder la información cargada aqui. ¿Desea continuar?',' Atención',4, function(t){
            
                    window.location.href="#"+t.options.base_url+'index.php/entidades/clientes/modificar/'+t.cliente.get("id_cliente")
                },that);    
        }else{
            
            swal("Atención!", 'Usted no tiene permisos para realizar esta accion', "error")
        }
        
        
    }
});//ElementosView








function inicializacion_contexto()
{   
    
    oCarrito=new Carrito();
    oProductoView=new ProductoView({el:$('#tpl-table-query')})
    oCarritoview=new Carritoview({el:$('#tpl_opera_carrito'),base_url:base_url});
    $("#tipo").val(tipos_productos_default)
    $("#cantidad").keypress(function(e){return teclaentero(e)});
    $("#inp_nro_tipo").keypress(function(e){
        if(e.which==13)
        {
            buscar_avanzada();
        }
        return teclaentero(e)
    });
    $("#inp_codbarras").keypress(function(e){
        if(e.which==13)
        {
            buscar_avanzada();
        }
        return teclaentero(e)
    });
    $("#inp_nombre").keypress(function(e){
        if(e.which==13)
        {
            buscar_avanzada();
        }else{
            return true
        }

    })
    
    $("#buscador").keypress(function(e){   
        
        if(e.which==13)
        {
            var patron=($("#buscador").val()).trim();
            if(patron!="")
            {
                buscar(patron)
                $("#buscador").val("")
            }    
        }
        
        return true;        
    })
    $("#btn-buscar").click(function(){  
        buscar_avanzada($(this));              
    })
    $("#seachvisible").click(function(){
        if($("#seachvisible").is(":checked"))
        {
            $("#tpl-table-query").show();
        }else{
            $("#tpl-table-query").hide();
        }
    })

           




if($("#tipo").val()==7){
buscar_avanzada($("#btn-buscar"))    
}
if(+$("#id_pedido").val()>0){
    cargar_pedido()
}

}//inicializacion contexto





function buscar_avanzada(elementEvent=null){

   var patron={nro_tipo:$("#inp_nro_tipo").val(),codbarras:$("#inp_codbarras").val(),producto:$("#inp_nombre").val(),id_categoria:$("#inp_id_categoria").val(),id_tipo:$("#tipo").val()};
   oProductoView.render(patron,elementEvent);
}

//buscador de front (no busqueda avanzada)
function buscar(patron)
{

var txtbuscar=$("#text-buscador").html()
$("#text-buscador").html("BUSCANDO...")
event.preventDefault()   

var tipo=$("#tipo").val()
var exito=false;
var rsOK=null;
var cantidad=1;
var precio_manual=0
if(es_entero(patron) && patron.length>6)
{ // probale cod codbarras
    var rs=ofw.get("verproductos",Array("*"),"codbarras='"+patron+"'"+stmtempresa,"producto asc,tipo asc")
    if(rs.length>0)
    {
        rsOK=rs[0];
        exito=true;
    }
}

if(es_entero(patron) && !exito && tipo>0)
{ // probar con id
    var rs=ofw.get("verproductos",Array("*"),"nro_tipo="+patron+" and id_tipo="+tipo+stmtempresa,"producto asc,tipo asc")
    if(rs.length>0)
    {
        rsOK=rs[0]
        exito=true;
    }
}

if(es_entero(patron) && !exito)
{ // probar con id
    var rs=ofw.get("verproductos",Array("*"),"nro_tipo="+patron+stmtempresa,"producto asc,tipo asc")
    if(rs.length>0)
    {
        rsOK=rs[0]
        exito=true;
    }
}
var partes=patron.split("*")
if(partes.length==2 && !exito)
{ if(es_entero(partes[0]) && es_entero(partes[1]) && tipo!="" && !exito)
    {console.log("es cantidad y codbarras o ID de tipo")
        var rs=ofw.get("verproductos",Array("*"),"nro_tipo="+partes[1]+" and id_tipo="+tipo+stmtempresa,"producto asc,tipo asc")
        if(rs.length>0)
        {
            rsOK=rs[0];
            exito=true;
            cantidad=parseInt(partes[0])
        }
        if(!exito)
        {console.log("intento por codigo de barras con tipo")
            var rs=ofw.get("verproductos",Array("*"),"codbarras="+partes[1]+" and id_tipo="+tipo+stmtempresa,"producto asc,tipo asc")
            if(rs.length>0)
            {
                rsOK=rs[0];
                exito=true;
                cantidad=parseInt(partes[0])
            }    
        }
        
        
    }
   if(es_entero(partes[0]) && es_numero(partes[1]) && tipo!="" && !exito)
    {   console.log("es cantidad y precio (generico) de tipo")
        var rs=ofw.get("verproductos",Array("*"),"generico=1 and id_tipo="+tipo+stmtempresa,"producto asc,tipo asc")
        if(rs.length>0)
        {
            rsOK=rs[0];
            exito=true;
            cantidad=parseInt(partes[0])
            precio_manual=parseFloat(partes[1])
        }
    } 

    if(!es_entero(partes[0]) && es_numero(partes[1]) && tipo!="" && !exito)
    { var abreviacion=partes[0].replace(/[0-9]*$/i,"")
        var cant=(es_entero(partes[0].replace(abreviacion,"")))?partes[0].replace(abreviacion,""):"";
         if(cant!="")   
         {
            var rs=ofw.get("verproductos",Array("*"),"generico=1 and id_tipo="+tipo+" and cat_abreviada='"+abreviacion+"'"+stmtempresa,"producto asc,tipo asc")
            if(rs.length>0)
            {
                rsOK=rs[0];
                exito=true;
                cantidad=parseInt(cant)
                precio_manual=parseFloat(partes[1])
            }    
         }
        
    } 

}//con asterisco
$("#text-buscador").html(txtbuscar)
if(exito)
{var modeloExiste=null;
    rsOK['cantidad']=cantidad
    if(precio_manual!=0)
    {
     rsOK['importe_tipo']=precio_manual     
    }
    
    modeloExiste=oCarrito.findWhere({id_producto:rsOK['id_producto']});

    if(modeloExiste !=null)
    {   rsOK['cantidad']+=modeloExiste.get("cantidad");
        
        rsOK['cantidad']+=modeloExiste.get("cantidad");        
        if(modeloExiste.get("mueve_stock")=="1"){
            var stock=parseFloat(modeloExiste.get("stock"));    
            var c=parseFloat(rsOK['cantidad']);
            if(stock<c){
                //win.alert("No hay stock suficiente para "+rsOK['producto']+" (disponible: "+stock.toString()+", solicitado: "+c.toString()+")"," Atención",4)
                swal("Sin stock suficiente de "+rsOK['producto'],"disponible: "+stock.toString()+", solicitado: "+c.toString(),"error")
                return
            }
        }

        rsOK['importe_total']=tomoney(rsOK['importe_tipo']*rsOK['cantidad']);        
        var modelupdate=new Producto(rsOK);
        
        oCarrito.remove(modeloExiste);
        oCarrito.add(modelupdate);
    }else{
        if(rsOK["mueve_stock"]=="1"){
            var stock=parseFloat(rsOK["stock"]);    
            var c=parseFloat(rsOK['cantidad']);
            if(stock<c){
                //win.alert("No hay stock suficiente para "+rsOK['producto']+" (disponible: "+stock.toString()+", solicitado: "+c.toString()+")"," Atención",4)
                swal("Sin stock suficiente de "+rsOK['producto'],"disponible: "+stock.toString()+", solicitado: "+c.toString(),"error")
                return
            }
        }
        rsOK['importe_total']=tomoney(rsOK['importe_tipo']*rsOK['cantidad']);    
        oCarrito.add(rsOK);
    }
    
    
}else{
    
    $.gritter.options.time=2000;
    $.gritter.add({
            title: 'Disculpe!',
            text: 'No se pudo encontrar el articulo.'
        });
}

}



function click_btnmanual(){
    var id_tipo=$("#tipo").val()
    if(id_tipo!="1" && id_tipo!=""){
        $("#inp_id_categoria").parent().hide()
    }else{
         $("#inp_id_categoria").parent().show()
         $("#tpl-table-query").html("")
    }
    $('#modal-manual-busqueda').modal('show')
}




 $(document).keydown(function(e) {    
      switch (e.which){
        case 117: //f6        
        click_btnmanual()
        break;
        case 118: //f7
        ActivarTab("default-tab-carrito")        
        break;
        case 119: //f8
        ActivarTab("default-tab-envio")
        break;
      }
   });

 function ActivarTab(tabname){
    switch (tabname){
        case "default-tab-carrito":        
        $('a[href="#default-tab-carrito"]').tab('show');         
        break;
        case "default-tab-envio":
        $('[data-toggle="popover"]').popover('hide');
        $('a[href="#default-tab-envio"]').tab('show'); 
        break;
    }
}


