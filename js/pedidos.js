var base_url=$("#base_url").val()
var ofwlocal=new fwt(base_url+'index.php/operaciones/pedidos/listener')
var eventos = _.extend({}, Backbone.Events);
var oCheckoutClientes=null;
var olista=null;
var oProductosview =null;


var Producto=Backbone.Model.extend();
var CheckoutDetalles=Backbone.Collection.extend({
    initialize:function(options){        
        this.options=options || {}             
    },
    cargarproductos:function(pedido){
        this.options.pedido=pedido;
        var that=this;
        that.reset();               
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_productos",this);
        }
        ofwlocal.getAsync("selpedidosproductos",{id_pedido:this.options.pedido.get("id_pedido")},function(rs){ that.cargar(rs,that) } )

    },
    cargar:function(rs,that){
       for (c in rs)
       {
        that.add(rs[c])
       }
        if(typeof that.options.eventos !="undefined")
        {
         that.options.eventos.trigger("endload_productos",that);
        }
    },
    model:Producto
});//CheckoutDetallemodel


var CheckoutItem=Backbone.Model.extend();
var Checkout=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){

        var that=this;
        that.reset();               
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }
        ofwlocal.getAsync("selpedidos",patrones,function(rs){ that.cargar(rs,that) } )
    },    
    cargar:function(rs,that)
    {       
     for (c in rs)
     {
      that.add(rs[c])
      }
     
    if(typeof that.options.eventos !="undefined")
    {
     that.options.eventos.trigger("endload",that);
    }
    },
    model:CheckoutItem
});//Checkout

var CheckoutclientesView=Backbone.View.extend(
{   el:$('#panel-body'),
    defaults:{ocampoView:null},
    initialize:function(options)
    {
        this.options=options || {};
        eventos.on("initload",this.loading,this)
        eventos.on("endload",this.endload,this)
        this.options.checkoutclientes=null
        this.options.itemselected=null
    },
    render:function(patrones)
    {
        var that=this;
        
        if(oCheckoutClientes ==null)
        {
        oCheckoutClientes=new Checkout({eventos:eventos});    
        }
        
        oCheckoutClientes.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    { 
        
     $("#bntBuscar").button("loading");   
      spinnerStart($('#panel-body'));  
    },
    endload:function(ocol)
    {
      $("#bntBuscar").button("reset");
      this.cargar(ocol)
    },
    events:{
            "click button[name='ver']":'ver',
            "click button[name='estado']":"cambiarestado"
    },
    cambiarestado:function(e) {
        e.preventDefault();        
        var id_pedido=$(e.target).attr("id").replace("estado-","")
           this.options.itemselected= this.options.checkoutclientes.findWhere({id_pedido:id_pedido})
        $("#estado-selected").val(this.options.itemselected.get("estado"))
        setestadopill()
        $("#modal-estado-pedidos").modal("show")
    },
    cargar:function(oCheckoutClientes)
    {   this.options.checkoutclientes=  oCheckoutClientes          
        olist=oCheckoutClientes.models  
        var that=this
        $.get(base_url+'tpl/pedidos.html', function (data) {
            tpl = _.template(data, {});
            htmlrender=tpl({ls:olist,base_url:base_url,fecha:$("#patron_fecha").val(),nro_pedido:$("#patron_nro_pedido").val()})
            that.$el.html(htmlrender);
        })
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {   
        e.preventDefault()
        var id_pedido=(e.target.id.indexOf("view-")>=0)?(e.target.id).replace("view-",""):(e.target.ParentNode.id).replace("view-","");
        var res=oCheckoutClientes.where({"id_pedido":id_pedido})
        var modelo=null;
        if(res.length==0)
        {
            return        
        }
        modelo=res[0]
        this.mostrar(modelo)
    },
    mostrar:function(pedido)
    {   
        oProductosview.mostrarcarrito(pedido)
    }//mostrar
});//checkoutclientesView


var CarritoView=Backbone.View.extend(
{   el:$('#row-body-pedido'),    
    initialize:function(options)
    {
        this.options=options || {};
        eventos.on("initload_productos",this.loading,this)
        eventos.on("endload_productos",this.endload,this)
        this.options.productos=null
        this.options.pedido=null
    },
    mostrarcarrito:function (pedido) {
        var productos=new CheckoutDetalles({eventos:eventos})
        productos.cargarproductos(pedido)
        this.options.productos=productos
        return this;
    }
    ,
    render:function(productos)
    {
        this.options.productos= productos          
        this.options.pedido= productos.options.pedido          
        olist=productos.models  

        var that=this
        $.get(base_url+'tpl/carritoadmin.html', function (data) {
            tpl = _.template(data, {});
            htmlrender=tpl({ls:olist,pedido:that.options.pedido,base_url:base_url})
            that.$el.html(htmlrender);
            $(".panelquery").fadeOut("1000")
            $("#row-body-pedido").fadeIn("1000")
        })
    },//render    
    loading:function()
    { 
    },
    endload:function()
    {
        this.render(this.options.productos)
    },
    events:{"click #btn-facturar":"facturar","click .btn-cerrar":"cerrar"
    },
    cerrar:function()
    { 
     $(".panelquery").fadeIn("1000")
     $("#row-body-pedido").fadeOut("1000")      
    },//cerrar
    facturar:function () {
        var that=this
        var id_comp= +that.options.pedido.get("id_comp")
        var id_pedido=that.options.pedido.get("id_pedido")
        
        if(id_comp>0){
            swal({
          title: "Pedido ya facturado",
          text: '¿Tiene un comprobante asociado, desea continuar y editar el mismo?',
          type: "warning",
          showCancelButton: true,
          closeOnConfirm: true
            }, function () {
                redirect(base_url+"index.php/operaciones/consultas/checkout/"+id_pedido)              
                window.localStorage.setItem("origin",base_url+"index.php/operaciones/pedidos/")
            })
        }else{
             redirect(base_url+"index.php/operaciones/consultas/checkout/"+id_pedido)              
             window.localStorage.setItem("origin",base_url+"index.php/operaciones/pedidos/")
        }
        
    }
});//checkoutclientesView





$("#bntBuscar").on("click",function(e){ 
    e.stopPropagation();
consultar();    
})




$("#patronnombres").keypress(function(e){
    if(e.which==13){
    consultar();
    }
})



$("#patron_nro_pedido").bind('paste',function(e){
var nro_pedido=0
var text=e.originalEvent.clipboardData.getData('text');
if(!text)
 text=e.originalEvent.clipboardData.getData('text/plain');
if(!text)
 text=e.originalEvent.clipboardData.getData('application/whatsapp');
 /*console.log("text1")
 console.log(text1)
 console.log("text2")
 console.log(text2)
 console.log("text3")
 console.log(text3)*/
 if(text && text!=""){
    var regnro_pedido =/\[([0-9]+)]/ 
    var match = text.match(regnro_pedido); 
    if(match){
        var cad=match[0].replace(/(\[)|(\])/g,"") //elimino los corchetes en caso que los tenga
        if(cad.length==8) {
           nro_pedido= +cad
           $("#patron_nro_pedido").val(nro_pedido)
           consultar()
        }
    }
 }

return false
})


function consultar(){
var patrones={nro_pedido:($("#patron_nro_pedido").val()).trim(),fecha:($("#patron_fecha").val()).trim(),nombres:($("#patronnombres").val()).trim(),estado:($("#estado").val()).trim()}
olista.render(patrones);    
}


$("a[name='estado-select']").click(function(e){  

var estado=($(e.target).attr("item"))?$(e.target).attr("item"):$(e.target.parentNode).attr("item")    
    $("#estado-selected").val(estado)
  setestadopill()  
})
function setestadopill(){
$("a[name='estado-select']").each(function(es){
    
    var estado=$("#estado-selected").val()
        if($(this).attr("item")==estado){
            $(this.parentNode).attr("class","active")
        }else{
            $(this.parentNode).removeAttr("class")
        }
    })
}


$("#btnconfirmar").click(function(e){
    e.preventDefault();
    var itemselected=olista.options.itemselected
    var id_pedido=itemselected.get("id_pedido")
    var estado=$("#estado-selected").val()
    $.ajax({dataType: "json",type: 'POST',url:base_url+'index.php/operaciones/pedidos/cambiarestado/',data: {id_pedido:id_pedido,estado:estado},
            beforeSend: function(){
             $("#btnconfirmar").button("loading")
            },
            success: function(jsonResponse)
            { $("#btnconfirmar").button("reset")  
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;                 
                if(numError == 0)
                {    
                 var patrones={nro_pedido:id_pedido,fecha:($("#patron_fecha").val()).trim(),nombres:($("#patronnombres").val()).trim(),estado:($("#estado").val()).trim()}
                olista.render(patrones);    
                }else
                {
                    swal("error","Los cambios no se realizaron."+descError,"error")
                }                
                $("#modal-estado-pedidos").modal("hide");
            },            
            complete: function(){}
           })
    
})

oProductosview= new CarritoView();