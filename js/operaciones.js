
var localurlx=window.location.href.split("#")[1];
var tipos_productos_default=$("#tipos_productos_default").val();
var base_url=$("#base_url").val();
var base_fw=$("#base_fw").val();
App.restartGlobalFunction();
App.setPageTitle('Operar | CoffeBox APP');
var win= new fwmodal();
var ofwlocal=new fw(base_url+'index.php/')
ofwlocal.guardarCache=false;
var ofwt=new fwt(base_url+'index.php/operaciones/ventas/listener')
var ventagenerada=false;
var ocampoView=null;    
var oCliente=null;
var oCarrito=null;

var oCarritoview=null;
var oCampos=null;
var oClienteView=null;
var oRecibosPrintView=null;
var oProductoView=null;
var oProductos=null;
var oPagos=null;
var oCategorias=null;
var oElementView=null;
var oPagosView=null;
var oComp=null;
var id_empresa=$("#id_empresa").val();
var id_cliente=$("#id_cliente").val();
var stmtempresa=" and id_empresa="+id_empresa;
$.getScript(base_fw+'assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript(base_fw+'assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        
        
                $.getScript(base_fw+'assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript(base_fw+'assets/plugins/masked-input/masked-input.min.js').done(function(){                        
                            inicializacion_contexto();                        
                        
                    })
        
        
        });
    });
});

var eventos = _.extend({}, Backbone.Events);




var Comprobante=Backbone.Model.extend({    
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
});//Comprobante

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
     }//actualizarmodel
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
        
        if(ventagenerada)
        {win.alert("La venta ya se realizó"," No se puede agregar productos",4)
            return;
        }

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
                /* var tagid=(e.target.id.indexOf("car_detalle_label_")>=0)?e.target.id: e.target.parentNode.id
                var id_producto=tagid.replace("car_detalle_label_","");
                 var modelo=oCarrito.get(id_producto);
                 var strhtml=desc_producto(modelo)
                win.alert(strhtml," Detalle del "+modelo.get("tipo"),2)*/
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
    var importe_total=oCarrito.gettotal('importe_total');
    var modelocc=oPagos.actualizacc(importe_total);
  

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

            var importe_total=oCarrito.gettotal('importe_total');
            var modelocc=oPagos.actualizacc(importe_total);
            if(modelocc!=null){
                oPagosView.setcampocc(modelocc);
            }
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

           var importe_total=oCarrito.gettotal('importe_total');
           var modelocc=oPagos.actualizacc(importe_total);
            if(modelocc!=null){
                oPagosView.setcampocc(modelocc);
            }
            this.totalizar()
        
    },
    totalizar:function()
    {   
        var total_abona=oPagos.gettotal();        
        var total=oCarrito.gettotal('importe_total');
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
          //oComprobanteview.render(oCarrito)  
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
        var importe_total=oCarrito.gettotal('importe_total');
        var modelocc=oPagos.actualizacc(importe_total);
       
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



var RecibosPrintView=Backbone.View.extend(
{   el:$('#tpl-table-print'),    
    initialize:function(options)
    {
        this.options=options || {};
        that=this;
    },
    render:function(oPagos)
    {
        var that=this;
        this.cargar(oPagos)
        return this;
    },//render        
    events:{        
            "click button[name='selecItempagopdf']":'imprimirpdf',
            "click button[name='selecItempagomail']":'enviarmail',
            "click button[name='selecItempagocopy']":'copiarlink',
            "click button[name='selecItempagophone']":'enviarwp'
            
    },
    endload:function(oCols)
    {
        this.cargar(oCols)
        
    },
    loading:function()
    {        
    
    },
    cargar:function(oCols)
    {
        var that=this;
        this.modelo=oCols
        var tpl=_.template($('#tpl-table-list-recibos').html());                
        this.$el.html(tpl({ls:oCols.models,id_comp:$("#id_comp").val(),base_url:base_url}));       
        
    },
    imprimirpdf:function(e)
    {   
           var id_button=(e.target.id.indexOf("print-recibo-")>=0)?e.target.id: e.target.parentNode.id //hago esto porque es depende de donde hago click (si en el boton o en el icoono)
        if(id_button=="")
        return
        var id_pago=$("#"+id_button).attr("idpago")
        var pg=this.modelo.findWhere({id_pago:id_pago})
        var token=pg.get("token")
         window.open(this.options.base_url+"index.php/operaciones/pagos/recibo/"+token,'_blank');
       
        
    },
    enviarmail:function(e)
    {   
         var id_button=(e.target.id.indexOf("mail-recibo-")>=0)?e.target.id: e.target.parentNode.id //hago esto porque es depende de donde hago click (si en el boton o en el icoono)
        if(id_button=="")
        return
    
      var id_pago=$("#"+id_button).attr("idpago")
      swal({
          title: "Email",
          text: "¿es correcto este mail? sino, ingrese otro",
          type: "input",
          showCancelButton: true,
          closeOnConfirm: false,
          inputPlaceholder: "ingrese un correo",
          showLoaderOnConfirm: true
        }, function (inputValue) {
            
          if (inputValue === false) return false;
          
         if(es_email(inputValue)){            
            enviarcomp_mail(id_pago,inputValue)
         }

        });
      $(".sweet-alert input").val(oComp.get("cliente").get("email"))
        
    },
     copiarlink:function(e)
    {   
           var id_button=(e.target.id.indexOf("copy-recibo-")>=0)?e.target.id: e.target.parentNode.id //hago esto porque es depende de donde hago click (si en el boton o en el icoono)
        if(id_button=="")
        return

        var id_pago=$("#"+id_button).attr("idpago")
        var id=id_button.replace("copy-recibo-","")
         var copyText = document.getElementById('portapapeles-'+id);
            copyText.select();
            document.execCommand('copy');            
             swal({title:"Copiado!",
                timer: 2000,
                text: "",
                type: "success"})
       
        
    },
    enviarwp:function(e){
        var that=this
         var id_button=(e.target.id.indexOf("phone-recibo-")>=0)?e.target.id: e.target.parentNode.id //hago esto porque es depende de donde hago click (si en el boton o en el icoono)
        if(id_button=="")
        return
         var id_pago=$("#"+id_button).attr("idpago")
      swal({
          title: "Enviar a WhatsApp",
          text: "¿es correcto este telefono? sino, ingrese otro",
          type: "input",
          showCancelButton: true,
          closeOnConfirm: true,
          inputPlaceholder: "ingrese un correo",
          showLoaderOnConfirm: true
        }, function (inputValue) {
            
          if (inputValue === false) return false;
          
          var pg=that.modelo.findWhere({id_pago:id_pago})
            var token=pg.get("token")
         
         
         var cliente= oComp.get("cliente")
         var mensaje="Hola "+cliente.get("strnombrecompleto")+", desde "+$("#empresa_nombre").val()+" te dejamos el  recibo de pago N° "+id_pago+"\n"
         mensaje+=that.options.base_url+"index.php/operaciones/pagos/recibo/"+token+"\n"
         mensaje+="muchas gracias!!!"
            enviarcomp_whatsapp(inputValue,mensaje,id_pago)
         

        });
      $(".sweet-alert input").val(oComp.get("cliente").get("telefono").trim().replace(" ",""))
         
    }
});//recibosView


function enviarcomp_whatsapp(telefono,mensaje,id_pago){
    var referencia="https://wa.me/54"+telefono+"?text="+encodeURI(mensaje)
   window.open(referencia,'_blank');
    /*$("#whatsapp-send").attr("href",referencia)
    $("#whatsapp-send").trigger("click")*/
}

function enviarcomp_mail(id_pago,email){
 var that=this  
          $.ajax({dataType: "json",type: 'POST',url:base_url+'index.php/operaciones/pagos/send_mail/',data: {id_pago:id_pago,email:email},
            beforeSend: function(){
             spinnerStart($("#panel-body-pago"))
            },
            success: function(jsonResponse)
            {   
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;                 
                if(numError == 0)
                {    
                 swal("Perfecto","Hemos enviado el mail","success")
                }else
                {
                    swal("error","Los cambios no se realizaron."+descError,"error")
                }                
            },            
            complete: function(){
            spinnerEnd($("#panel-body-pago"))
            }
           })
}//enviarcomp_mail





function inicializacion_contexto()
{   
    
    oCarrito=new Carrito();
    oProductoView=new ProductoView({el:$('#tpl-table-query')})
    oCarritoview=new Carritoview({el:$('#tpl_opera_carrito'),base_url:base_url});
    oRecibosPrintView=new RecibosPrintView({el:$('#tpl-table-print'),base_url:base_url});
    oPagosparametros= new Pagosparametrosview({el:$('#modal-parametros'),base_url:base_url})
    init_mod_clientes();
    
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

    $("#id_tipo_comp option").each(function(e) {        

        if($(this).attr("name")!=oCliente.get("comp_tipo"))
        {
            $(this).remove();
        }    
        });

    $("#chkorden").click(function(){
        eval_check_orden();
    })        
init_mod_pagos();
if(parseInt($("#id_comp").val())>0)
{
cargar_comprobante();   
if(permitir(prt_operaciones,8)){
$("#btnactualizarvendedor").show();
}
        

}else{
botonera('')
eval_check_orden();
//para esta implementacion que cobra cuotas, abre busqueda avanzada siempre y cuando no sea modificaiocn de comprobante
//if(isMobile()){
//$('#modal-manual-busqueda').modal('show')
//click_btnmanual()
//siempre que sea de tipo cuota social, busca al principio, todas las cuotas sociales sin pagar
if($("#tipo").val()==7){
buscar_avanzada($("#btn-buscar"))    
}
if(+$("#id_pedido").val()>0){
    cargar_pedido()
}

//}

}
mostrar_saldo()

}//inicializacion contexto

function mostrar_saldo(){
//muestra el saldo siempre y cuando tenga saldo mayor a cero
var saldo=oCliente.saldo_afavor;
if(saldo>0){
    $("#spansaldo").show()
    $("#saldoafavor").html(saldo)
}else{
    $("#spansaldo").hide()
    $("#saldoafavor").html(0)
}

}

//al hacer click en btn comprobante a facturar, realiza esta validacion
function valida_comp(){
event.stopPropagation()
var monto_abona=0;        
      
        //actualizo el item cc para ver si hay al menos un item de pago en cc, lo cual me da la pauta de que el usuario tiene intenciones de  plasmar pagos a cuenta

        if(!oCliente.options.puedefacturar && !$("#chkorden").is(":checked")){
        win.alert("El cliente no puede facturar. Falta validar el cuit","Atención",4)
        return;
        }
        var importe_total=oCarrito.gettotal('importe_total');
        var modelocc=oPagos.actualizacc(importe_total);
        monto_abona=oPagos.gettotal();
        
        var strerror=""
        var total=$("#total_parcial").html();
        if(ventagenerada)
        {         
         swal("Atención!", "Esta venta ya se realizó", "error")
         return   
        }

        if(!es_numero(monto_abona) && modelocc == null)
        {
            strerror+="<li>No ha ingresado un monto a pagar</li>"
        }

        if(!parseFloat(monto_abona)>0  && modelocc == null)
        {
            strerror+="<li>El monto a pagar no puede ser menor o igual a cero</li>"
        }
        if(strerror!="")
        {
         strerror="<ul>"+strerror+"</ul>"
         win.alert(strerror," ATENCIÓN",4)  
         return
        }

        if(oCarrito.models.length!=0)
        {
        
        var strhtml="Usted esta a punto de generar una"
        
        if($("#chkorden").is(":checked"))
        { 
            strhtml+=" orden de pedido";
        }else{
            var id_tipo_comp= $("#id_tipo_comp").val()
            var tipo_comp_desc=$("#id_tipo_comp option[value='"+id_tipo_comp+"']").text();
        strhtml+=" "+tipo_comp_desc+" ante afip ";
            
        }
         strhtml+=" por $"+total+" pesos.¿Desea continuar?";
        
          swal({
          title: "Atención",
          text: strhtml,
          type: "info",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
            }, function () {
            generar_venta('E')
            });

        }else{            
            swal("Atención!","No ha ingresado un producto para hacer la compra", "error")
        }
}//valida comp


function eval_check_orden(){
    if($("#chkorden").is(":checked"))
        {
            $("#btn-pagar").html("<i class='fa fa-credit-card'></i> GENERAR ORDEN");            
            $("#id_tipo_comp").prop('disabled', true);
        }else{
            $("#btn-pagar").html("<i class='fa fa-credit-card'></i> GENERAR COMPROBANTE");
            $("#id_tipo_comp").prop('disabled', false);
        }
}


//al hace click en btn comprobante pendiente, realiza esta validacion
function valida_orden()
{
    event.stopPropagation()
if(oCarrito.models.length!=0)
    {
      var total=$("#total_parcial").html();
      var strhtml="Usted va a GUARDAR una"        
        if($("#chkorden").is(":checked"))
        { 
            strhtml+=" orden de pedido en PENDIENTE";
        }else{
            var id_tipo_comp= $("#id_tipo_comp").val()
            var tipo_comp_desc=$("#id_tipo_comp option[value='"+id_tipo_comp+"']").text();
            strhtml+=" "+tipo_comp_desc+" en PENDIENTE ";            
        }
         strhtml+=" por $"+total+" pesos. ¿Desea continuar?";
            swal({
          title: "Atención",
          text: strhtml,
          type: "info",
          showCancelButton: true,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
            }, function () {
            generar_venta('P')
            });
        
        }else{
            //win.alert("No ha ingresado un producto para hacer la venta"," ATENCIÓN",4)    
            swal("Atención!", "No ha ingresado un producto para hacer la venta", "warning")
        }
}//valida orden



function generar_venta(estado)
{
$("#linkmp").val("")
var id_tipo_comp=(typeof $("#id_tipo_comp").val()!="undefined")?$("#id_tipo_comp").val():""
var afip=($("#chkorden").is(":checked"))?0:1;
var representa=0;
id_cliente_representa=0;
if(oCliente.get("id_cliente_representante")>0){
    representa=($("#chk-id-representa").is(":checked")?1:0)
}
//var ctacte=($("#chkcc").is(":checked"))?1:0;
var id_comp= +$("#id_comp").val()
var modeloscc=oPagos.where({id_tipo_pago:7})
var ctacte=(modeloscc.length>0)?1:0; //hay al menos un pago  a cuenta
var modelossaldo=oPagos.where({id_tipo_pago:6})

var modelosmercadopago=oPagos.where({id_tipo_pago:"8"})
//verifico si dentros de los pagos, hay un item que paga con saldo
//, si es asi, advierto si el monto ha cambiado
if(modelossaldo.length>0){
    var saldoactual=oCliente.getsaldo();
    if(saldoactual!= modelossaldo[0].get('monto_abona')){

        swal("Atención!", "El saldo a favor del cliente ha cambiado. El nuevo saldo es "+saldoactual, "warning")
    }
}



//genera los atributos IDS para cada pago, asi mantengo referencias para cuando se inserten en la BD
oPagos.generarID();
if(id_comp==0){
oComp=new Comprobante({cliente:oCliente,pagos:oPagos,carrito:oCarrito,id_tipo_comp:id_tipo_comp,afip:afip,estado:estado,ctacte:ctacte,id_comp:0,representa:representa,id_pedido:$("#id_pedido").val()})
oComp.url= base_url+'index.php/operaciones/ventas/generar/'    
}else{
//significa que existe el comprobante y se desea modificar
var strpagos=''
    var pagos=JSON.stringify(oPagos.toJSON())
    for (p in oPagos.pagosdb)
    {
        if(strpagos=="")
        {
            strpagos+=oPagos.pagosdb[p];
        }else{
            strpagos+=","+oPagos.pagosdb[p];
        }
    }

oComp.set({pagos:oPagos,carrito:oCarrito,id_tipo_comp:id_tipo_comp,afip:afip,estado:estado,ctacte:ctacte})
}

oComp.save(null,{wait: true,
               type:'POST',
                    beforeSend :function(){                        
                        $("#btn-pagar").button('loading');                        
                    },
                    success:function(e,params){                                                    
                        $("#btn-pagar").button('reset');
                        
                                if(params.numerror!=0 && params.numerror!=30)
                                {   //problemas con el stock
                                    if(params.numerror==10){
                                        swal("No se puede generar la venta",params.descerror, "error")
                                    }else{
                                    swal("Error al generar venta","Detalle: "+params.descerror+". Consulte con el administrador", "error")    
                                    }
                                    
                                    
                                }
                                else
                                {   

                                //cuando llegue a estado F, es su estado final
                                    var estadonuevo=params.data.estado
                                    if(estadonuevo=="F"){
                                    ventagenerada=true;
                                    }
                                    if(estadonuevo=="F" || estadonuevo=="E" || estadonuevo=="A")
                                    {
                                        oPagosView.options.onlyread=true;
                                    }
                                    if(estadonuevo=="F"){
                                    inputCarrito_read();
                                    inputComp_read(true);
                                    //inputPagos_read(true);
                                    }
                                    if(estadonuevo=="E"){
                                        $("#chkorden").prop("disabled",true)   
                                        $("#chkorden").prop("checked",false)
                                    }


                                    oComp.set({id_comp:params.data.id_comp});
                                    oComp.set({estado:params.data.estado});
                                    $("#id_comp").val(params.data.id_comp);
                                    botonera(params.data.estado)
                                    //oPagos.pagosdb=params.data.pagos;
                                    
                                    
                                    //actualizo y muestro saldo (si corresponde)
                                     oCliente.getsaldo();
                                     mostrar_saldo();
                                     debugger
                                    afipcmp=params.data.afipcmp;
                                    var strcompafip=""
                                    if(typeof afipcmp.nro_comp !="undefined")
                                    {
                                        if(es_numero(afipcmp.nro_comp))
                                        {
                                            strcompafip=afipcmp.tipo_comp+" "+format_number(afipcmp.nro_talonario,"0000")+" "+format_number(afipcmp.nro_comp,"00000000");
                                        }    
                                    }

                                    if(params.data.afip!="1")
                                    {
                                    $("#legend-comp").html("Orden de pedido N° "+format_number(params.data.id_comp.toString(),"00000000"));    
                                    }else{
                                        $("#legend-comp").html("Comprobante N° "+format_number(params.data.id_comp.toString(),"00000000")+" Fact.: "+strcompafip);    
                                    }
                                    
                                    if(params.numerror==30)
                                    {
                                        
                                        swal("Operación parcial!", "La venta se realizó correctamente pero hubo un inconveniente al enviar a afip. Solo se genera la orden de pedido. Intente enviar este comprobante despues.", "warning")

                                    }else{
                                        if(params.data.estado=="P")
                                        {
                                            
                                            swal({title:"Operación exitosa!",
                                            timer: 2000,
                                            text: "La venta quedó guardada con el ID "+params.data.id_comp,
                                            type: "success"})
                                            handleCheckPageLoadUrl(base_url+"index.php/operaciones/consultas/operar/"+oCliente.id)
                                        }else{
                                            
                                            swal({title:"Operación exitosa!",
                                            timer: 2000,
                                            text: "La venta se realizó correctamente",
                                            type: "success"})
                                        }
                                    
                                    }

                                    
                                        
                                    


                                        oClienteView.options.urlcomprobante=params.data.path
                                        oClienteView.options.anulacomp=true;
                                        oClienteView.options.urlnuevaventa=base_url+"index.php/operaciones/consultas/operar/"+oCliente.id
                                        oClienteView.render(oCliente);
                                        if(estadonuevo=='E' || estadonuevo=='F')
                                        {
                                        window.open(base_url+params.data.path ,'_blank');
                                        }
                                        //actualizo los id_pago de las variablees locales
                                        //actualizo esto a lo ultimo, asi no se nota el retardo
                                        //var arrpagos=JSON.parse(params.data.pagos);
                                        //oPagos.actualizarIDS(arrpagos);
                                        
                                        //siempre que el comprobante no esté en pago pendiente, pone en solo lectura los inputs pagos, 
                                        //como asi tambien consulta los pagos del comprobante
                                        if(params.data.estado!="P"){                                            
                                            
                                            oPagos=new Pagos();                                        
                                            var rs=ofwlocal.get("vercomprobantes",Array("*"),"id_comp="+params.data.id_comp,"");
                                            var comp=rs[0];
                                            oPagos.load(comp);
                                            oPagosView.render(oPagos);
                                            oRecibosPrintView.render(oPagos);
                                            inputPagos_read(true)
                                        }
                                        
                                     if((params.data.estado=="F" || params.data.estado=="E")  && modelosmercadopago.length>0){
                                         var rs=ofwlocal.get("pagos",Array("*"),"id_comp_ref="+params.data.id_comp+" and estado='P' and id_tipo_pago=8","");
                                            if(rs.length>0){
                                            oPagosView.pagar_MP(rs[0]['id_pago'])    

                                            }
                                        
                                    }
                                     
                                    
                               }
                            },
                    error: function(e,params) {                                                
                        swal("Error!", params.responseText, "error")
                    },
                    wait:true
                    })//save


}//generar_venta
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
    //actualizo el item de pago de cuenta corriente (si existe)
    var importe_total=oCarrito.gettotal('importe_total');
    var modelocc=oPagos.actualizacc(importe_total);
    
    
}else{
    
    $.gritter.options.time=2000;
    $.gritter.add({
            title: 'Disculpe!',
            text: 'No se pudo encontrar el articulo.'
        });
}

}


function inputComp_read(bandera)
{  
$("#id_tipo_comp").prop("disabled",bandera)    
$("input[name='chkorden']").prop("disabled",bandera)    
}
function inputCarrito_read()
{ 
    $("#panel-body-inputs input[type='text']").each(function(){  $(this).prop("disabled",true)})
    $("#panel-body-inputs select").each(function(){  $(this).prop("disabled",true)})


     $("#panel-body-carrito").find("input[type='text']").each(function(e){
        $(this).prop("disabled",true)
     })
 }

function anular_comprobante()
{
    var strtipo=($("#chkorden").is(":checked"))?"esta orden de pedido":"este comprobante";

    swal({title: "Atención",
                  text: 'Usted va anular '+strtipo+'.Los productos que se stokean se van a reponer. Los cambios que va a realizar, son irreversibles. ¿Desea continuar?',
                  type: "info",
                  showCancelButton: true,
                  closeOnConfirm: true
                    }, function () {
                       anular();
                    });


    /*win.dialog('Usted va anular '+strtipo+'.Los productos que se stokean se van a reponer. Los cambios que va a realizar, son irreversibles. ¿Desea continuar?',' Atención',4, function(param){
                    anular();
                },null);*/
}
var anulaciongenerada=false;
function anular()
{
if(anulaciongenerada && ventagenerada)
    return
var id_comp=$("#id_comp").val()
var oCompAnular=new Comprobante({cliente:null,pagos:null,carrito:null,id_tipo_comp:0,afip:null,estado:null,ctacte:null,id_comp:id_comp})

oCompAnular.url= base_url+'index.php/operaciones/ventas/anular/'

oCompAnular.save(null,{type:'POST', 
                    beforeSend:function(){
                        spinnerStart($("#panel-body-pago"))
                    },    
                    success:function(e,params){                            
                        spinnerEnd($("#panel-body-pago"))
                                if(params.numerror!=0)
                                {
                                   // win.alert("No se realizaron los cambios: "+params.descerror+". Consulte con el administrador"," Error al anular comprobante.",4)
                                   swal("No se realizaron los cambios",params.descerror+". Consulte con el administrador","error")
                                    
                                }
                                else
                                {   ventagenerada=true;
                                    anulaciongenerada=true;
                                    inputCarrito_read();                                    
                                    inputComp_read(true);
                                    oComp.set({id_comp:params.data.id_comp});
                                    if(params.data.afip==1){
                                        afipcmp=params.data.afipcmp;
                                        var strcompafip=""
                                        if(typeof params.data.afipcmp !="undefined")
                                        {
                                            if(typeof afipcmp.nro_comp !="undefined")
                                            {
                                                if(es_numero(afipcmp.nro_comp))
                                                {
                                                    strcompafip=afipcmp.tipo_comp+" "+format_number(afipcmp.nro_talonario,"0000")+" "+format_number(afipcmp.nro_comp,"00000000");
                                                }    
                                            }    
                                        }    
                                    }
                                    
                                    

                                    if(params.data.afip==0)
                                    {
                                    $("#legend-comp").html("Orden de pedido anulada N° "+format_number(params.data.id_comp.toString(),"00000000"));    
                                    }else{
                                    $("#legend-comp").html("Anulado por N° "+format_number(params.data.id_comp.toString(),"00000000")+" NC.: "+strcompafip);    
                                    oClienteView.options.urlcomprobante=params.data.path
                                    window.open(base_url+params.data.path ,'_blank');
                                    }                                    
                                    
                                    
                                    swal({title:"Anulado!",timer: 2000,text: "",type: "success"})
                                    inputPagos_read(true);
                                    botonera('A')
                                    oClienteView.options.urlnuevaventa=base_url+"index.php/operaciones/consultas/operar/"+oCliente.id
                                    oClienteView.render(oCliente);
                                    
                                     
                                }
                            },
                    error: function(e,params) {
                        
                        spinnerEnd($("#panel-body-pago"))
                        win.alert(params.responseText," Error",4)
                                    
                    },
                    wait:true
                    })//save


}


function cargar_comprobante()
{

spinnerStart($('#panel-body-pago'));
spinnerStart($('#panel-body-inputs'));
var rs=ofwlocal.get("vercomprobantes",Array("*"),"id_comp="+$("#id_comp").val(),"");
var comp=rs[0];
botonera(comp['estado'])

if(comp['estado']=="F" || comp['estado']=="A")
{
oCarritoview.options.onlyread=true;
inputComp_read(true)
}
//esto estado, pagos read por defecto luego el boton de modificar pagos podra o no editar el pago
if(comp['estado']=='A' || comp['estado']=='F' || comp['estado']=='E'){
    oPagosView.options.onlyread=true;
}

//seteo los datos del comprobante
if(comp['afip']!="1")
{
    $("#chkorden").prop("checked",true)
}

$("#id_tipo_comp").val(comp['id_tipo_comp'])


if(comp['estado']=="E" || comp['estado']=="F")
{
oClienteView.options.urlcomprobante=comp['path_comp'];
oClienteView.options.anulacomp=true;
oClienteView.options.urlnuevaventa=base_url+"index.php/operaciones/consultas/operar/"+oCliente.id
oClienteView.render(oCliente);
}

//siempre que sea este estado, lo obligo a facturar o anular
if(comp['estado']=="E"){
$("#chkorden").prop("disabled",true)   
$("#chkorden").prop("checked",false)
}
eval_check_orden();

var strcompafip=""
if(comp['estado']=="F")
{
    if(es_numero(comp['nro_comp']))
    {
        strcompafip=comp['tipo_comp']+" "+format_number(comp['nro_talonario'],"0000")+" "+format_number(comp['nro_comp'],"00000000");
        $("#legend-comp").html("Comprobante N° "+format_number(comp['id_comp'].toString(),"00000000")+" Fact.: "+strcompafip);    
    }
}else{
$("#legend-comp").html("Orden de pedido N° "+format_number(comp['id_comp'].toString(),"00000000"));        
}

//agrego los articulos
var htmlmsn=""    
var rs=ofwlocal.get("vercomprobantes_det",Array("*"),"id_comp="+$("#id_comp").val(),"");
for(i in rs){
    var id_tipo=rs[i]['id_tipo'];
    var id_producto=rs[i]['nro_tipo']+"_"+rs[i]['id_tipo'];
    var rsprod=ofwlocal.get("verproductos",Array("*"),"id_producto='"+id_producto+"'"+stmtempresa,"");
    if(rsprod.length>0)
    {
    prod=rsprod[0];
    prod['cantidad']=rs[i]['cantidad'];
    prod['importe_base']=rs[i]['precio_base'];
    prod['importe_iva']=rs[i]['precio_iva'];
    prod['importe_tipo']=rs[i]['precio_venta'];
    prod['importe_total']=rs[i]['importe_item'];
    prod['iva']=rs[i]['iva'];
    prod['producto']=rs[i]['detalle'];
    oCarrito.add(prod);
    }else{
        //contempla falta de stock o productos para los items contemplados
        var tipos_contemplados=Array(1,3,4);
        if(tipos_contemplados.indexOf(id_tipo)){
        htmlmsn +="El producto "+rs[i]['detalle']+" ("+id_producto+") no esta disponible\n"    
        }
        
    }
}
if(htmlmsn!="")
{
    swal("Atencion","Si modifica el comprobante, puede que ya no sea el mismo: "+htmlmsn,"warning")
}
oPagos.load(comp);
oPagosView.render(oPagos);
$("#id_tipo_comp").val(comp['id_tipo_comp']);
var afip=($("#chkorden").is(":checked"))?0:1;
oComp=new Comprobante({cliente:oCliente,pagos:oPagos,carrito:oCarrito,id_tipo_comp:comp['id_tipo_comp'],afip:afip,estado:estado,ctacte:comp['ctacte'],id_comp:parseInt($("#id_comp").val())})
oComp.url= base_url+'index.php/operaciones/ventas/generar/'+$("#id_comp").val()
oRecibosPrintView.render(oPagos)
spinnerEnd($('#panel-body-pago'));
spinnerEnd($('#panel-body-inputs'));
}//cargar_comprobante


function cargar_pedido() {
    
    //agrego los articulos
var htmlmsn=""    
var rs=ofwt.get("pedido_detalle",{id_pedido:$("#id_pedido").val()});
for(i in rs){
    var id_tipo=rs[i]['id_tipo'];
    var id_producto=rs[i]['nro_tipo']+"_"+rs[i]['id_tipo'];    
    var rsprod=ofwt.get("productos",{id_producto:id_producto});
    if(rsprod.length>0)
    {
    prod=rsprod[0];
    prod['cantidad']=rs[i]['cantidad'];        
    prod['importe_tipo']=rs[i]['importe_unitario'];
    prod['importe_total']=rs[i]['importe_item'];    
    prod['producto']=rs[i]['producto'];
    oCarrito.add(prod);
    }else{
        //contempla falta de stock o productos para los items contemplados
        var tipos_contemplados=Array(1,3,4);
        if(tipos_contemplados.indexOf(id_tipo)){
        htmlmsn +="El producto "+rs[i]['producto']+" ("+id_producto+") no esta disponible\n"    
        }
        
    }
}
if(htmlmsn!="")
{
    swal("Atencion","Si modifica el comprobante, puede que ya no sea el mismo: "+htmlmsn,"warning")
}
}


function botonera(estadoActual)
{
$("#btn-acciones").html("");
var strhtml="";
var btnguardar=""
var conf_comprobantes=$("#conf_comprobantes").val()
var conf_guardar_compra=$("#conf_guardar_compra").val()
var conf_orden_pedido=$("#conf_orden_pedido").val()
if(conf_guardar_compra=="1"){
btnguardar="&nbsp;<button type='button' class='btn btn-sm btn-success ' id='btn-pendiente' onclick='valida_orden()'>'<i class='fa fa-save'></i> SOLO GUARDAR</button>"    
}

var dataloadspinner="data-loading-text=\"<i class='fa fa-spinner fa-spin '></i> procesando...\""

var btnpostventa=""
//var btnanular="&nbsp;<button "+dataloadspinner+" class='btn btn-danger btn btn-sm' id='btn-anular' onclick='anular_comprobante()'  ><i class='fa fa-eraser'></i> ANULAR</a>";
var btnanular="<li><a href='javascript:;' "+dataloadspinner+" class='btn btn-sm' id='btn-anular' onclick='anular_comprobante()'  ><i class='fa fa-eraser'></i> ANULAR</a></li>"
//var btneditarpagos="&nbsp;<button "+dataloadspinner+" type='button' class='btn btn-sm btn-inverse ' id='btn-modificar-pago' onclick='modificar_pagos()'><i class='fa fa-edit' ></i> EDITAR PAGOS</button>"
var btneditarpagos="<li><a href='javascript:;' "+dataloadspinner+" class='btn btn-sm' id='btn-modificar-pago' onclick='modificar_pagos()'><i class='fa fa-edit' ></i> EDITAR PAGOS</li>"                       

var btngenerar="&nbsp;<button  "+dataloadspinner+" type='button' class='btn btn-sm btn-primary' id='btn-pagar' onclick='valida_comp()'><i class='fa fa-credit-card'></i> GENERAR COMPROBANTE</button>"

if (!((estadoActual=='E' || estadoActual=='F') && permitir(prt_comprobantes,16)))
    btneditarpagos=""

//si tiene permisos de anular y esta facturado o emitido
if (!( (estadoActual=='F' || estadoActual=='E') && permitir(prt_comprobantes,8)))    
    btnanular=""
//strhtml+=btnanular

//strhtml+=btneditarpagos
if(btneditarpagos!="" ||btnanular!=""){
btnpostventa+="<div class='dropup'><button type='button' class='btn btn-sm btn-primary dropdown-toggle' data-toggle='dropdown'>opciones&nbsp;<span class='caret'></span></button>"
btnpostventa+="<ul class='dropdown-menu' role='menu'>"+btnanular+btneditarpagos+"</ul></div>"    
strhtml+=btnpostventa
}

if(estadoActual=='')
strhtml+=btnguardar;


if(((estadoActual=='E' && conf_comprobantes==1) || (estadoActual=='P' && conf_comprobantes=="1")|| (estadoActual=='P' && conf_orden_pedido=="1") || estadoActual=='') )
strhtml+=btngenerar


$("#btn-acciones").html(strhtml);
}


$("#copylinkmp").click(function(e){
    e.preventDefault();
var copyText = document.getElementById('linkmp');
copyText.select();
document.execCommand('copy');
swal("copiado","","success")
})

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


