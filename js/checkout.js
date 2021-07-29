
var eventchkout = _.extend({}, Backbone.Events);
var base_url_checkout=$("#base_url").val()
var ofwchk=new fwt(base_url_checkout+"index.php/operaciones/pedidos/listener")
var oTipo_envios=null;
var oCheckout=null;
var oTipo_pagos=globales['tipo_pagos'];






var Tipo_envio =Backbone.Model.extend();//tipo_envio
var Tipo_envios=Backbone.Collection.extend({
 initialize:function(options){
    this.options=options || {} 
},
load:function(){
    var that=this
    that.reset()
 var rs=ofwchk.get('sel_tipo_envios',null);
 for(i=0;i<rs.length;i++){
                that.add(rs[i])
  }

},
model:Tipo_envio
})
oTipo_envios=new Tipo_envios()



var Checkout =Backbone.Model.extend({
    defaults:{
        id_pedido:0,
        carrito:null,
        apellido:"",
        nombres:"",
        calle:"",
        nro:"",
        piso:"",
        depto:"",
        resto:"",
        telefono:"",
        id_localidad:$("#id_localidad").val(),              
        id_sucursal:$("#id_sucursal").val(),
        id_tipo_envio:1, //1 envio con cadete, 2 retiro en sucursal
        id_tipo_pago:1, //efectivo
        nota:"",
        id_cliente:$("#id_cliente").val()
        
    }
});//Cliente



var oCheckoutView=null;
var CheckoutView=Backbone.View.extend({
     url:'',
    el:$("#panel-body-checkout"),
    initialize:function(options)
    {
        this.options=options || {};        
        this.options.step=0;
        eventchkout.on("endload_checkout",this.endload_checkout,this)        

    },    
    endload_checkout:function(ochk){        
    },
    render:function(checkout){
        if(this.options.checkout!=null && checkout==null){
            checkout=this.options.checkout
        }
        this.options.checkout=checkout
        var that=this

        $.get(this.options.base_url+'tpl/checkout.html', function (data) {                        
                tpl = _.template(data, {});                  
                htmlrender=tpl({checkout:that.options.checkout,base_url:that.options.base_url,tipos_envios:oTipo_envios,sucursales:globales['sucursales'],localidades:globales['localidades'],tipos_pagos:oTipo_pagos})
                that.$el.html(htmlrender); 
                $("#telefono").keypress(function(e){
                             return teclaentero(e)
               })                 
                var id_tipo_envio=that.options.checkout.get("id_tipo_envio")
                $("[tipo_envio]").each(function(){
                    
                    if($(this).attr("tipo_envio")==id_tipo_envio){
                    $(this).show();        
                    }else{
                    $(this).hide();                                
                    }
                })
                
        });        
        
    },
    events:{
        "click input[name='tipo_envio']":'tipo_envio_click',
        "click input[name='sucursal']":'click_sucursal',
        "click .btn-cancelar-pedido":"click_btn_cancelar"
    },
    click_btn_cancelar:function(e) {
        e.preventDefault()
    }
    ,   
    finalizar_pedido:function () {
        
       
        
    },
    seguir_comprando:function() {
    },
    tipo_envio_click:function(e){
        
        var id_tipo_envio=$(e.target).val()
         $("[tipo_envio]").each(function(){
                    if($(this).attr("tipo_envio")==id_tipo_envio){
                    $(this).fadeIn(500);        
                    }else{
                    $(this).fadeOut(500);                                
                    }
                })
    },
    click_sucursal:function(e){
    }
})//CheckoutView


function  valida_orden() {
    var modo="A"
    event.preventDefault()
    if(oCarrito.models.length==0){
     swal("Atención","para realizar el pedido, debe seleccionar al menos un producto","warning")
     return
    }

var id_pedido = +oCheckoutView.options.checkout.get("id_pedido")
if(id_pedido>0){
    modo="M"
}
var nombres=$("#nombres").val()
var apellido=$("#apellido").val()
var telefono=$("#telefono").val()        
var tipo_envio=$("input[name='tipo_envio']:checked").val()
if(!tipo_envio){
    swal("Atención","Debe seleccionar un tipo de envio por favor","warning")
    return
}

var calle=$("#calle").val()
var numero=$("#nro").val()
var piso=$("#piso").val()
var depto=$("#depto").val()
var resto=$("#resto").val()
//tipo envio a domicilio
if(tipo_envio==1 && !calle && !numero){
    swal("Atención","Debe ingresar calle y numero por favor","warning")
    return
}
var id_localidad=$("#id_localidad").val()
if(tipo_envio==1 && !id_localidad){
    swal("Atención","Debe ingresar una localidad por favor","warning")
    return
}
var id_sucursal=null
if(tipo_envio==2){
    id_sucursal=$("input[name='sucursal']:checked").val()
}

var nota=$("#nota").val()
        
if(!id_sucursal) id_sucursal=0;
        
oCheckout.set({apellido:apellido,nombres:nombres,telefono:telefono,calle:calle
            ,nro:numero,piso:piso,depto:depto,resto:resto,id_tipo_envio:tipo_envio
            ,id_localidad:id_localidad,id_tipo_pago:$("#id_tipo_pago").val(),id_sucursal:id_sucursal,carrito:oCarrito,nota:nota})

oCheckoutView.options.checkout=oCheckout
$("#btn-pendiente").button("loading")

        $.ajax({
                url: base_url_checkout+"index.php/operaciones/pedidos/generar_pedido/",
                type:'post',dataType: "json",data:{chk:JSON.stringify(oCheckout)}, 
                error: function(dataResponse){                         
                    $("#btn-pendiente").button("reset")
                    swal("Disculpe","En este momento no es posible realizar el pedido. Intente luego","error")
                },
                success: function(jsonResponse){    
                    $("#btn-pendiente").button("reset")
                     if(typeof jsonResponse.numerror=="undefined"){                
                            return
                    }  

                    var numError=parseInt(jsonResponse.numerror);
                    var descError=jsonResponse.descerror;                
                    if(numError == 0)
                    { 
                      var id_pedido= +jsonResponse.data['id_pedido']
                      var mensaje= jsonResponse.data['mensaje']
                      oCheckoutView.options.checkout.set({id_pedido:id_pedido})
                        swal({
                                    title: "Perfecto!", 
                                     text: "¿Desea continuar  cargando pedidos?",                                   
                                      type: "success",
                                      showCancelButton: true,
                                      closeOnConfirm: true
                                    }, function (inputValue) {

                                            if(inputValue){
                                            oCarrito.reset()
                                            oCheckout= new Checkout({carrito:oCarrito,id_tipo_envio:1})
                                            oCheckoutView.render(oCheckout); 
                                            oCarritoview.render()                                                                      
                                                }else{
                                                redirect(base_url+"index.php/operaciones/pedidos/index/"+id_pedido)            
                                                }
                                            
                                    });

                     
                      
                    }                
              }});//ajax




}//valida_orden

oTipo_envios.load()
var id_pedido= +$("#id_pedido").val()   
if(id_pedido==0){
oCheckout= new Checkout({carrito:oCarrito,id_tipo_envio:1})
oCheckoutView=new CheckoutView({base_url:base_url_checkout})    
oCheckoutView.render(oCheckout)    
}




function  cargar_pedido() {    
    
 var id_pedido=$("#id_pedido").val()   
 var params={id_pedido:id_pedido}
 var rs=ofwchk.get("selpedidosproductos",params)
 oCarrito.load(rs)
 params={nro_pedido:id_pedido,fecha:'',nombres:'',estado:''}
 var rsp=ofwchk.get("selpedidos",params)
 var pedido=rsp[0]
 var id_loc=(pedido['id_loc'])?pedido['id_loc']:$("#id_localidad").val()
 var id_sucursal=(pedido['id_sucursal'])?pedido['id_sucursal']:$("#id_sucursal").val()
 var id_tipo_envio=(pedido['id_tipo_envio'])?pedido['id_tipo_envio']:1
 var id_tipo_pago=(pedido['id_tipo_pago'])?pedido['id_tipo_pago']:1
 oCheckout= new Checkout({carrito:oCarrito,id_pedido:id_pedido,id_tipo_envio:id_tipo_envio,apellido:pedido['apellido'],nombres:pedido['nombres']
    ,calle:pedido['calle'],nro:pedido['nro'],piso:pedido['piso'],depto:pedido['depto'],resto:pedido['resto'],telefono:pedido['telefono'],id_localidad:pedido['id_loc'],id_tipo_pago:id_tipo_pago,id_cliente:pedido['id_cliente'],nota:pedido['nota'],id_sucursal:id_sucursal})
    oCheckoutView=new CheckoutView({base_url:base_url_checkout})    
    oCheckoutView.render(oCheckout)
}