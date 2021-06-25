var oDescuentos=null;
var oDescuentosView=null;
var eventos = _.extend({}, Backbone.Events);
var base_url=$("#base_url").val()
var id_empresa=$("#id_empresa").val();
var stmtempresa=" and id_empresa="+id_empresa;
var ofwtlocal=new fwt(base_url+'index.php/operaciones/consultas/listener')
var Descuento=Backbone.Model.extend({
    initialize:function(options){        
        this.options=options || {}        
    },
    calcularSrv:function(parametros){
        var param=Array();        
        for(p in parametros){
            param.push(parametros[p]['valor']);
        }
        var rs=ofwtlocal.get("descuento-"+this.get('id_descuento'),param);

        var valor=round2dec(rs[0]['valor']);
        this.set({valor:valor})
        return valor;

    }
});//Descuento
var Descuento_parametro=Backbone.Model.extend({});//Descuento
var Descuentos_parametros=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
    },
    load:function(rs){
        for (c in rs)
        { 
        this.add(rs[c])
        }
    },
    model:Descuento_parametro
})//Descuentos_parametros
var Descuentos=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}        
    },
    loadAsync:function(base_url){
        var that=this;
        this.base_url=base_url;
        that.reset();    
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload_descuentos",this);
        }        
        ofwlocal.getAsync("descuentos",Array("*"),"(now()>=fe_desde or fe_desde is null) and (fe_hasta is null or fe_hasta>now()) and (id_empresa is null or id_empresa="+id_empresa+")","descripcion asc",function(rs){ that.cargar(rs,that) } )

    },    
    cargar:function(rs,that)
    {       
        for (c in rs)
        { rs[c]['base_url']=this.options.base_url
          that.add(rs[c])
          var id_descuento=rs[c]['id_descuento'];
          ofwlocal.getAsync("descuentos_parametros",Array("*"),"id_descuento="+id_descuento,"nro_parametro asc",function(rsp){ that.cargar_parametros(rsp) } )
        }
        if(typeof that.options.eventos !="undefined")
        {
         that.options.eventos.trigger("endload_descuentos",that);
        }
    },
    cargar_parametros:function(rs){
        
        if(rs.length>0){
        var id_descuento=rs[0]['id_descuento']
        var parametros=new Descuentos_parametros();
        parametros.load(rs);
            var descArr=this.findWhere({id_descuento:id_descuento})
            if(descArr !=null){
                descArr.set({parametros:parametros})
            }   
        }
        
        /*for(i=0;i<this.models.length;i++){
            if(this.models[i].get("id_descuento")==id_descuento){
                this.models[i].set({parametros:parametros})
                break;
            }
        }*/
    },
    model:Descuento
});

var DescuentosView=Backbone.View.extend(
{   el:$("#body-aplica"),
    descuentos:null,    
    initialize:function(options)
    {
        this.options=options || {};
        that=this;
        eventos.on("initload_descuentos",this.loading,this)
        eventos.on("endload_descuentos",this.endload,this)        
    },
    render:function()
    {
        var that=this;
        if(oDescuentos ==null)
        {
        oDescuentos=new Descuentos({eventos:eventos});    
        }        
        oDescuentos.loadAsync(this.options.base_url)
        return this;
    },//render        
    events:{        
            "click button[id='btn-add-desc']":'calcular'
    },
    endload:function(oCols)
    {
        this.cargar(oCols)
        
    },
    calcular:function(){        
         var parametros=Array();
        var id_descuento=$("#selec_descuentos").val()
        var oDesc=oDescuentos.findWhere({id_descuento:id_descuento})
        var oPara=oDesc.get("parametros");
        var scr="";
        
      for(i in oPara.models){
            var variable=oPara.models[i].get("nombre");
            var evaluacion=oPara.models[i].get("evaluacion");
            var nro_parametro=oPara.models[i].get("nro_parametro");
            var origen=oPara.models[i].get("origen");
            var tipo_dato=oPara.models[i].get("tipo_dato");
            var valor=eval(evaluacion);

            if(origen=="cliente"){
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
        //console.log(scr);
        for(i in oPara.models){
        var origen=oPara.models[i].get("origen");
        var descripcion=oPara.models[i].get("descripcion");
        var validacion=oPara.models[i].get("validacion");
         if(origen=="cliente"){
            if(validacion!=null && validacion!=""){                
                var evalue="("+validacion+")?true:false;";
                valido=eval(evalue);
                //console.log(evalue);
                if(!valido){
                    swal("Dato invalido","el dato "+descripcion+" es invalido para agregar un descuento","error")
                    return
                }

            }

         }
     }
     
     var descuento=oDesc.calcularSrv(parametros);
     
     if(descuento>0){
        var prod={};
        prod['id_producto']="5_"+oDesc.get("id_descuento");
        prod['nro_tipo']=oDesc.get("id_descuento");
        prod['cantidad']=1;
        prod['id_tipo']=5;
        prod['tipo']="DESCUENTO";
        prod['importe_base']=descuento*-1;
        prod['importe_iva']=0;
        prod['categoria']="Descuento";
        prod['fraccion']="unidad";
        prod['fraccion_plural']="unidades";
        prod['generico']="1";
        prod['mueve_stock']="0";
        prod['importe_tipo']=descuento*-1;
        prod['importe_total']=descuento*-1;
        prod['iva']=0;
        prod['readonly']=1;
        prod['producto']=oDesc.get("descripcion");
        oCarrito.add(prod);
        
        var importe_total=oCarrito.gettotal('importe_total');
        var modelocc=oPagos.actualizacc(importe_total);

     }
     $("#inp_valor_descuento").val("")
    },
    loading:function()
    {},
    cargar:function(oCols)
    {   $("#modal-aplica-title").html("INGRESO DE DESCUENTO")
        var that=this;
        olist=oCols.models  
        var tpl=_.template($('#tpl-select_descuentos').html());                
        this.$el.html(tpl({ls:olist}));   
        $("input[id='inp_valor_descuento']").keypress(function(e){ return teclamoney(e)})             
    }
});//DescuentosView


oDescuentosView=new DescuentosView({base_url:base_url});
