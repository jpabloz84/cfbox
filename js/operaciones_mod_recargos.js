var oRecargos=null;
var oRecargosView=null;
var eventos = _.extend({}, Backbone.Events);
var base_url=$("#base_url").val()
var id_empresa=$("#id_empresa").val();
var ofwtlocalcacheable=new fwt(base_url+'index.php/operaciones/consultas/listener')
ofwtlocalcacheable.guardarcache=true;
var Recargo=Backbone.Model.extend({
    initialize:function(options){        
        this.options=options || {}        
    },
    calcularSrv:function(parametros,fdxresponse,modelo_pago){

        var param=Array();    
        
        for(p in parametros){
            param.push(parametros[p]['valor']);
        }
        var that=this;
        var rs=ofwtlocalcacheable.getAsync("recargo-"+this.get('id_recargo'),param,function(rs){
            
            var valor=0;            
            if(rs.length>0){ //si hay recargo a este tipo de pago, actualizo/agrego
                valor=round2dec(rs[0]['valor']);
                if(valor>0){
                that.set({valor:valor})
                if(that.get("modulo_aplica")=="pagos"){
                that.set({cidpago:modelo_pago.cid}) //indico con que pago se realizo el recargo    
                }
                
                fdxresponse(that)    
                }
            }
            //aqui pregunto por el modelo de pago que genero el pedido de calculo, que si esta agregado, borro su recargo
            if(valor==0){
             var cidpago=that.get("cidpago")   
             if(cidpago !=null){
                var modeloexiste=oCarrito.findWhere({id_producto:"6_"+that.get("id_recargo")})
                if(modeloexiste!=null){
                    oCarrito.remove(modeloexiste)
                }
             }
            }
            
            
        },true);

    }
});//Descuento
var Recargos_parametro=Backbone.Model.extend({});//Descuento
var Recargos_parametros=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
    },
    load:function(rs){
        for (idx=0;idx<rs.length;idx++)
        { 
        this.add(rs[idx])
        }
    },
    model:Recargos_parametro
})//Recargos_parametros
var Recargos=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
    },
    loadAsync:function(base_url){
        this.reset();    
        var that=this;
        this.base_url=base_url;
        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_recargos",this);
        }        
        ofwlocal.getAsync("recargos",Array("*"),"(now()>=fe_desde or fe_desde is null) and (fe_hasta is null or fe_hasta>now()) and (id_empresa is null or id_empresa="+id_empresa+")","descripcion asc",function(rs){ 
            
            that.cargar(rs) 
        } )

    },    
    cargar:function(rs)
    {       
        var that=this
        for (ic=0;ic<rs.length;ic++)
        { rs[ic]['base_url']=that.base_url
            
          this.add(rs[ic])
          var id_recargo=rs[ic]['id_recargo'];
          ofwlocal.getAsync("recargos_parametros",Array("*"),"id_recargo="+id_recargo,"nro_parametro asc",function(rsp){ 
            if(rsp.length>0){
            that.cargar_parametros(rsp,rsp[0]['id_recargo'])     
            }
            
        })
        }
        if(typeof this.options.eventos !="undefined")
        {
         this.options.eventos.trigger("endload_recargos",this);
        }
    },
    cargar_parametros:function(rs,id_recargo){
         
        var parametros=new Recargos_parametros();
        parametros.load(rs);
        for(ix=0;ix<this.models.length;ix++){
            
            if(this.models[ix].get("id_recargo")==id_recargo){

                this.models[ix].set({parametros:parametros})
                break;
            }
        }
       
       /* var recargo=this.findWhere({id_recargo:id_recargo})
        recargo.set({parametros:parametros})*/
    },
    model:Recargo
});

var RecargosView=Backbone.View.extend(
{   el:$("#body-aplica"),
    descuentos:null,    
    initialize:function(options)
    {
        this.options=options || {};
        that=this;
        eventos.on("initload_recargos",this.loading,this)
        eventos.on("endload_recargos",this.endload,this)        
    },
    render:function()
    {
        var that=this;
        if(oRecargos ==null)
        {
        oRecargos=new Recargos({eventos:eventos});    
        oRecargos.loadAsync(this.options.base_url)
        }        
        
        return this;
    },//render        
    events:{        
           
    },
    endload:function(oCols)
    {
        this.cargar(oCols)
        
    },
    calcular:function(modelo){
        
        var parametros=Array();
        var aplicarecargos=false;
        for(r=0;r<oRecargos.models.length;r++){
        
            var scr="";
            var oRec=oRecargos.models[r]
            var modulo_aplica=oRec.get("modulo_aplica");
            var oPara=oRec.get("parametros")
             var oPara=oRec.get("parametros");
              for(i=0;i<oPara.models.length;i++){
                    var variable=oPara.models[i].get("nombre");
                    var evaluacion=oPara.models[i].get("evaluacion");
                    var nro_parametro=oPara.models[i].get("nro_parametro");
                    var origen=oPara.models[i].get("origen");
                    var tipo_dato=oPara.models[i].get("tipo_dato");                    
                    var valor=eval(evaluacion);

                    if(origen=="cliente" && modulo_aplica=="pagos"){
                        scr+="var "+variable+"=";
                        switch (tipo_dato) {
                        case 'float':
                        scr+="parseFloat("+valor+");"
                        break;
                        case 'int':
                        scr+="parseInt("+valor+");"
                        break;
                        default:
                        scr+="'"+valor+"';"
                        break;
                        }
                    }
                 parametros.push({variable:variable,valor:valor,nro_parametro:nro_parametro})
                }
            eval(scr);
            
            for(i=0;i<oPara.models.length;i++){
            var origen=oPara.models[i].get("origen");
            var descripcion=oPara.models[i].get("descripcion");
            var validacion=oPara.models[i].get("validacion");
            
             if(origen=="cliente" && modulo_aplica=="pagos"){
                if(validacion!=null && validacion!=""){                
                    var evalue="("+validacion+")?true:false;";
                    valido=eval(evalue);
                    //console.log(evalue);
                    if(!valido){
                        swal("Dato invalido","el dato "+descripcion+" es invalido para agregar un descuento","error")
                        return
                    }//if valido
                }//if 
             }//if
           }//for
         oRec.calcularSrv(parametros,this.responseSrv,modelo);

        }//r recagos
     
       return aplicarecargos;
     
    },
    responseSrv:function(oRec){
            debugger
            var prod={};
            var id_producto="6_"+oRec.get("id_recargo");
            modeloExiste=oCarrito.findWhere({id_producto:id_producto});
            prod['id_producto']=id_producto
            prod['nro_tipo']=oRec.get("id_recargo");
            prod['cantidad']=1;
            prod['id_tipo']=5;
            prod['tipo']="RECARGO";
            prod['importe_base']=oRec.get("valor");
            prod['importe_iva']=0;
            prod['categoria']="Recargo";
            prod['fraccion']="unidad";
            prod['fraccion_plural']="unidades";
            prod['generico']="1";
            prod['mueve_stock']="0";
            prod['importe_tipo']=oRec.get("valor");
            prod['importe_total']=oRec.get("valor");
            prod['iva']=0;
            prod['producto']=oRec.get("descripcion");
            prod['readonly']=1;
            //si existe, lo actualizo, sino, lo agrego
            if(modeloExiste !=null)
            {
             oCarrito.remove(modeloExiste);        
            }
            oCarrito.add(prod);
            var importe_total=oCarrito.gettotal('importe_total');
            var modelocc=oPagos.actualizacc(importe_total);

            $.gritter.options.time=6000;
            $.gritter.add({
            title: 'Se genero un recargo a la venta!',
            text: oRec.get("descripcion")+'. ($ '+prod['importe_total'].toString()+")"
            });


    },
    loading:function()
    {},
    cargar:function(oCols)
    {   

        $("#modal-aplica-title").html("INGRESO DE RECARGO")
        var that=this;
        olist=oCols.models  
        var tpl=_.template($('#tpl-select_recargos').html());                
        this.$el.html(tpl({ls:olist}));   
        $("input[id='inp_valor_recargo']").keypress(function(e){ return teclamoney(e)})     
    }
});//DescuentosView


oRecargosView=new RecargosView({base_url:base_url});
oRecargos=new Recargos({eventos:eventos});

oRecargos.loadAsync(base_url)