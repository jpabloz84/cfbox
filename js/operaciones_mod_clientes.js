var Cliente=Backbone.Model.extend({idAttribute:'id_cliente',
defaults:{
"id_cliente":0,
"id_vendedor_opera":0
},
initialize:function(options){
    
    this.options=options || {}
    that=this;
    if(typeof this.options.id_cliente!="undefined")
    {   

        var rs=null;
        if($("#id_pedido").val()!="" && +$("#id_pedido").val()>0){
        rs=ofwt.get("selcliente_presupuesto",{id_pedido:$("#id_pedido").val()})
        }else{
        rs=ofwt.get("selcliente",{id_cliente:this.options.id_cliente})
        }    
        
        
        var datos=null;
        if(rs.length>0)
        {   this.options.puedefacturar=true;
            datos=rs[0];
            if(datos['cuit']!=null){
                if(datos['cuit'].length!=11){
                this.options.puedefacturar=false;
                }

            }
            
            this.set(datos)
        }
        //mientras no sea consumidor final, consulto saldo
        if(this.get("cf") != 1){
        this.saldo_afavor=this.getsaldo();    
        }
        
    }
},
getsaldo:function(){
    var saldo=0;
    //definicion: consulta en tiempo real si tiene saldo a favor
    if(typeof this.options.id_cliente!="undefined")
    {   if(this.get("cf")!=1){
        cond="id_cliente="+this.options.id_cliente;
        var cols=Array("saldo_afavor")
        rs=ofwlocal.get("versaldo_afavor",cols,cond,"")    
        var datos=null;
        if(rs.length>0)
        saldo=parseFloat(rs[0].saldo_afavor);
        this.saldo_afavor=saldo;
        }else{
            this.saldo_afavor=0;
        }
        
    }
return saldo;
}
});//clientemodel

var ClienteView=Backbone.View.extend(
{   el:$('#tpl_opera_cliente'),
    cliente:null,       
    initialize:function(options)
    {
        this.options=options || {};
        
    },
    render:function(oCliente)
    {
        
        var that=this;
        this.cliente=oCliente;
        $.get(this.options.base_url+'tpl/opera_clienteV1.html', function (data) {
            tpl = _.template(data, {});            
            htmlrender=tpl({campo:oCliente,base_url:that.options.base_url,base_fw:that.options.base_fw,urlcomprobante:that.options.urlcomprobante,urlnuevaventa:that.options.urlnuevaventa})
            that.$el.html(htmlrender);
            $('[data-click="panel-reload"]').click(function(){                
        handleCheckPageLoadUrl(base_url+"index.php/operaciones/consultas/operar/"+that.cliente.id);
            })
        })
        return this;        
    },//render        
    events:{
            "click #btn-editar":'editar',
            "click #btn-regresar":'regresar',
            "click #btn-nuevaventa":'nueva',
            "click #chkvendedor":"vervendedores"
    },
    vervendedores:function(e){
        
        if($("#chkvendedor").is(":checked")){
        $("#modal-vendedor-seleccion").modal("show")    
        }else{
            $("#id_usuario_vende").val(0)
            $("#lblvendedor").html("Operar para vendedor")
            oCliente.set("id_vendedor_opera",0);
        }
        
    },    
    nueva:function()
    {   
        $('[data-toggle="popover"]').popover('hide');
        if(typeof this.options.urlnuevaventa !="undefined")
        {
            
                    handleCheckPageLoadUrl(this.options.urlnuevaventa);            
            
        
        }
    },
    regresar:function()
    {   if(typeof this.options.urlregresar !="undefined")
        {$('[data-toggle="popover"]').popover('hide');
            var that=this
          swal({
                title: "Atención",
                text: 'Usted va a abandonar esta pantalla y va perder la información cargada aqui. ¿Desea continuar?',
                type: "info",
                showCancelButton: true,
                closeOnConfirm: true          
                }, function () {                    
            redirect(that.options.urlregresar)
          })
            
        
        }
    },
    editar:function()
    {   $('[data-toggle="popover"]').popover('hide');
        if(permitir(prt_clientes,4))
        {
            that=this;

            swal({
                title: "Atención",
                text: 'Usted va a abandonar esta pantalla y va perder la información cargada aqui. ¿Desea continuar?',
                type: "info",
                showCancelButton: true,
                closeOnConfirm: true          
                }, function () {
            redirect(base_url+'index.php/entidades/clientes/modificar/'+that.cliente.get("id_cliente"))
          })
        /*win.dialog('Usted va a abandonar esta pantalla y va perder la información cargada aqui. ¿Desea continuar?',' Atención',4, function(t){
            
                    window.location.href="#"+t.options.base_url+'index.php/entidades/clientes/modificar/'+t.cliente.get("id_cliente")
                },that);    */
        }else{
            //win.alert('Usted no tiene permisos para realizar esta accion',' Atención',4);
            swal("Atención","Usted no tiene permisos para realizar esta acción","warning")
        }
        
        
    }
});//ClienteView




function init_mod_clientes()
{   
    
    oCliente=new Cliente({id_cliente:$("#id_cliente").val()})
    
    oClienteView=new ClienteView({el:$('#tpl_opera_cliente'),base_url:base_url,base_fw:base_fw,urlregresar:window.localStorage.getItem("origin"),urlnuevaventa:'',urlcomprobante:''});
    oClienteView.render(oCliente);
    $("input[name='radiobusqueda']").change(function(e){
        
        if($("#avanzada").is(":checked"))
        {
         $("#form-avanzada").show()
         $("#form-standard").hide()   
        }
        if($("#fija").is(":checked"))
        {
         $("#form-avanzada").hide()
         $("#form-standard").show({complete:function(){
            
            $("#buscador").get(0).focus()
         }})
         
         
          event.preventDefault();
        }
    })


}


$("#seleccionarvendedor").click(function(){

    oCliente.set("id_vendedor_opera",$("#inp_vendedor_opera").val());
        $("#modal-vendedor-seleccion").modal("hide");
        $("#lblvendedor").html("Operando para "+$("#inp_vendedor_opera option:selected" ).text()) 
})
        

