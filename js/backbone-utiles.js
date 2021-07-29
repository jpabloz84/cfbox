var Campo=Backbone.Model.extend({    
    defaults:{        
        identificador:false,//es para indicar que este campo se usa como identificador del modelo
        esdescriptivo:false, //se usa para mensajes de alerta y hacer alusion a este campo
        valor:'',
        nombre:'',
        tipo:'text', //text/checkbox/datetime/int/money/select/multiselect/file/longtext/image
        etiqueta:'', //descripcion del campo a mostrar
        obligatorio:false, //si el campo es o no obligatorio
        coleccion:null, //solo para casos tipo select y deben ser coleeciones con atributos id/descripcion
        accept:'*.*', //para tipo file, archivos que acepta
        download:true //para tipo file, permite descargar el archivo
    }
})//model campo

var Campos=Backbone.Collection.extend({model:Campo});

var campoView=Backbone.View.extend({
        el:$('#row-body'),
        campos:null,
        tplname:'form.html',
        defaults:{
            tplname:'form.html'
        },
        initialize:function(options){            
            this.options=options || {};
            if(typeof this.options.tplname!="undefined")
            {
                this.tplname=this.options.tplname;
            }

              
            this.uploadCrop = null
        },
        render:function(modo)
        {            
        var that=this;        
        that.onbeforerender();
        this.modo=modo
        this.$el.show();            
        $.get(this.options.base_url+'/tpl/'+this.tplname, function (data) {                        
            tpl = _.template(data, {});//Option to pass any dynamic values to template
            var camposInputs=that.options.campos.models
            //permite es 'AVDE' -alta, vista, eliminar,editar
            htmlrender=tpl({campos:camposInputs,modo:modo,permite:that.options.permite})
            that.$el.html(htmlrender);//adding this.camposthe template content to the main template.
            
            for(c in camposInputs)
            {
                cp=camposInputs[c];
                if(cp.get('tipo')=='int')
                {
                  $("#"+cp.get('nombre')).keypress(function(e){return teclaentero(e)});  
                }
                if(cp.get('tipo')=='money')
                {
                  $("#"+cp.get('nombre')).keypress(function(e){return teclamoney(e)});  
                }
                if(cp.get('tipo')=='datetime') 
                {                  
                    /*
                necesita:masked-input.min.js/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js/bootstrap-datepicker.min.js
                    */
                $("#"+cp.get('nombre')).mask("99/99/9999");
                $("#"+cp.get('nombre')).datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });
                }//datetime

               if(cp.get('tipo')=='select'){
                  $("#"+cp.get('nombre')).combobox();
               }
               if(cp.get('tipo')=='multiselect'){ //select2/dist/js/select2.min.js
                  $("#"+cp.get('nombre')).select2({ placeholder: "Seleccione..." });
               }
             
            }//for
              that.onafterrender();
        }, 'html');    
        },//render
        events:{
            'submit #form-view':'guardar',            
            "click #eliminarElemento":'eliminar',
            "click #editarElemento":'mostrarEditar',
            "click #volverElemento":'volver',
            "click  [id*='btndownload']":'descargar',
            "click .img-fw":"clickimg",
            "change [name=img]":"changeimg",
            "click #cutAndSave":"cutimg"
        },
        cutimg:function(){ //corto la imagen segun el viewport
            var that=this
            if(this.uploadCrop){
                this.uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function (resp) {                
                $("img[idimg="+that.idimgEdit+"]").prop("src",resp)
                $("img[idimg="+that.idimgEdit+"]").attr("newimg",1)
                $("#myimgmodaledit").modal("hide")
            });
            }
        },
        clickimg:function(e){           
                e.preventDefault()
                if(this.modo=="V") return
                var id_element=$(e.target).attr("idimg");
                //(e.target.id.indexOf("seleccionar-")>=0)?e.target.id: e.target.parentNode.id
                if(id_element==null){
                    id_element=$(e.target.parentNode).attr("idimg");
                }
                if(id_element){
                $("#"+id_element).trigger("click")
                this.idimgEdit=id_element
                console.log("#"+id_element+" hizo click")    
                }else{
                    swal("Disculpe!. Haga click de nuevo por favor");
                }
                return 1;
        },//cclickimg
        changeimg:function(evt){         
        if(this.modo=="V") return   
          if(this.uploadCrop!=null){
            this.uploadCrop.croppie('destroy')
          }
          this.uploadCrop=$('.upload-img').croppie({
                viewport: {
                    width: 350,
                    height: 350,
                    type: 'square'
                },
                enableExif: true,
                boundary: {
                    width: 400,
                    height: 400
                },
                minZoom:0.1,
                maxZoom:1.6
            });
          var that=this
            input=$(evt.target).context
            if (input.files && input.files[0]) {
                var reader = new FileReader();                
                reader.onload = function (e) {                    
                    that.uploadCrop.croppie('bind', {
                        url: e.target.result
                    }).then(function(){                        
                        //console.log('jQuery bind complete');
                        $('.cr-slider').attr({'min':0.1000, 'max':1.5000});
                        $("#myimgmodaledit").modal("show")                        
                        //hago esto porque sino, la imagen no se mueve desde un principio
                        img = new Image();
                            img.onload = function() {                              
                              $(".cr-overlay").css({top:0,left:0,height:this.height,width:this.width})
                            }
                        img.src = $(".cr-image").prop("src");
                        
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
            else {
                swal("Sorry - you're browser doesn't support the FileReader API");
            }
            
        },
        descargar:function(e)
        { var idbtnname=$(e.target.parentElement).prop("id");
        var idhiddenname=idbtnname.replace("btndownload","file");
        var urldownload=$("#"+idhiddenname).val()
             window.open(urldownload, 'Download');
        },


        onafterrender:function()
        { console.log("afterrender")
              if(typeof this.options.onafterrender=="function")
            {
                this.options.onafterrender();
            }
        },
        onbeforerender:function()
        { console.log("onbeforerender")
              if(typeof this.options.onbeforerender=="function")
            {
                this.options.onbeforerender();
            }
        },
        mostrarEditar:function()
        {
            /*var mEdit=null;
            cpID=this.campos.findwhere({identificador:true});
            mEdit=this.modelos.findwhere({cpID.nombre:cpID.valor})*/
            /*var id_model=$('#id_servicio').val();                        
             mEdit=oColecciones.get(id_model);*/
             
            if(typeof this.options.modelo!="undefined")
            {
                this.render("E");
            }
            else
            {
              console.log('no se encontro modelo');  
            }
            //
            
        },
        getdescripcion:function(cmpcoleccion,modelo){
            
            var cmp=cmpcoleccion.findwhere({esdescriptivo:true});
            var res=''
            if(cmp != null)
            {
               res=modelo.get(cmp.nombre) 
            }
            return res;
        },
        getidentificador:function(cmpcoleccion,modelo){
            
            var cmp=cmpcoleccion.findwhere({identificador:true});
            var res=''
            if(cmp != null)
            {
               res=modelo.get(cmp.nombre) 
            }
            return res;
        },
        eliminar:function()
        {   
                var cp=this.options.campos.findWhere({esdescriptivo:true});
                var cpID=this.options.campos.findWhere({identificador:true});
                var col=cpID.get('nombre');
                mRemove=this.options.modelo;
                if(this.verificar(mRemove)){
                    if(typeof mRemove!="undefined" && mRemove!=null)
                    {   urldestroy=mRemove.url+'/'+cpID.get('valor');
                        that =this

                        swal({
                        title: "Atención",
                        text: "¿Usted está seguro que desea eliminar "+cp.get('nombre')+": "+cp.get('valor')+"?",
                        type: "info",
                        showCancelButton: true,
                        closeOnConfirm: true          
                        }, function () {
                            mRemove.url=urldestroy
                            that.remove(mRemove)
                        })
                       
                    }
                    else
                    {
                      console.log('no se ingreso modelo');  
                    }
                  }//verificar previo a eliminar                
             
            //
            
        },
       remove:function(mRemove)
        {
            
        this.before();
        that=this;
        mRemove.destroy({success:function (e,params){
                            if(params.numerror==0)
                            {                            
                            that.success()
                            }else{                                
                                swal("Atención","Error: "+params.descerror,"error")
                                that.error()
                            }

                    }
                    ,wait: true
                });        
        },//remove
        before:function(){
            if(typeof this.options.before=="function")
            {
             this.options.before();             
            }
        },//accion a ejecutar antes de enviar datos al servidor
        success:function(){
            if(typeof this.options.success=="function")
            {
             this.options.success();             
            }
        },//accion a ejecutar luego de que la accion se haya ejecutado bien
        error:function(){
            if(typeof this.options.error=="function")
            {
             this.options.error();             
            }
        },//accion a ejecutar luego de que la accion se haya ejecutado mal
        volver:function()
        {
            if(typeof this.options.volver=="function")
            {
             this.options.volver();             
            }

        },
        verificar:function(modelo) //verificacion previa a eliminar
        { var datosvalidos=false;
            if(typeof this.options.verificar=="function")
            {
            datosvalidos=this.options.verificar(modelo);             
            }else{
                datosvalidos=true;
            } 
            return datosvalidos           
        },
        validar:function(inputs)
        { var datosvalidos=false;
            if(typeof this.options.validar=="function")
            {
            datosvalidos=this.options.validar(inputs);             
            }else{
                datosvalidos=true;
            } 
            return datosvalidos           
        },
        guardar:function(ev){
            
            var tienefile=false;
            for(f in this.options.campos.models)
            {
                if(this.options.campos.models[f].get("tipo")=="file")
                {
                 tienefile=true;
                 break;   
                }
            }
            
            var detailsInputs=$(ev.currentTarget).serializeObject();            
            var strhtml=""
            var metodo=(detailsInputs['modo']=='A')?'POST':'PUT'
            //recorro aquellos campos que sean checkbox para setear correctamente el valor (0 o 1)
             var camposbits=this.options.campos.where({tipo:'checkbox'})
            
             for (e in camposbits)
             {
                var tagname=camposbits[e].get('nombre');
                detailsInputs[tagname]=($("#"+tagname).is(":checked"))?1:0
             }

             //recorro aquellos campos de texto para limpiar de espacios en blancos
             var campostextos=this.options.campos.where({tipo:'text'})
            
             for (e in campostextos)
             {
                var tagname=campostextos[e].get('nombre');
                detailsInputs[tagname]=($("#"+tagname).val()).trim()
             }

             //recorro aquellos campos de texto largo para limpiar de espacios en blancos
             var campostextoslargos=this.options.campos.where({tipo:'longtext'})
            
             for (e in campostextoslargos)
             {
                var tagname=campostextoslargos[e].get('nombre');
                detailsInputs[tagname]=($("#"+tagname).val()).trim()
             }

             //recorro aquellos campos de imagenes
             
             var camposimgs=this.options.campos.where({tipo:'image'})
            
             for (e in camposimgs)
             {
                   var tagname=camposimgs[e].get('nombre');
                if($("img[idimg="+tagname+"]").attr("newimg")==1){
                detailsInputs[tagname]=$("img[idimg="+tagname+"]").prop("src")   
                }else{
                    detailsInputs[tagname]=""; //no hay cambio de iamgen
                }
                
             }
            
            var that=this;
            if(this.validar(detailsInputs))
            {  
                /*saco los elementos de los inputs que no me sirven para el modelo*/                
               delete detailsInputs['modo'];
               delete detailsInputs['identificador']
               
                this.before();
                if(this.options.modelo!=null)
                {   this.options.modelo.set(detailsInputs)
                    if(!tienefile){
                        this.options.modelo.save(detailsInputs,{
                        type: metodo, //esto debo setearlo manualmente porque el modelo tiene como idatribute personalizado ()
                        success:function(e,params){                            
                                if(params.numerror!=0)
                                {
                                    //win.alert("Detalle: "+params.descerror+". Consulte con el administrador","Error al crear",4)
                                    swal("Error al crear",params.descerror+". Consulte con el administrador","error")
                                    if(that.error!=null){
                                        that.error();
                                    }
                                }
                                else
                                {that.options.modelo.set(params.data)
                                    if(that.success!=null){
                                        that.success();
                                    }
                                    
                                }
                            }
                        })//save    
                    }else{

                        //seteo los campos bits porque sino , no me los toma
                        var camposbits=this.options.campos.where({tipo:'checkbox'})            
                         for (e in camposbits)
                         {
                            var tagname=camposbits[e].get('nombre');
                            if($("#"+tagname).is(":checked"))
                             {$("#"+tagname).val(1)}   
                            else
                            {$("#"+tagname).val(0)}   
                            
                         }

                        var formData = new FormData($('#form-view')[0]);                        
                        this.options.modelo.save(null,{
                        type: "POST", //esto debo setearlo manualmente porque el modelo tiene como idatribute personalizado ()
                        data: formData,      // Must put data here.
                        processData: false,  // Don't let Backbone process the data.
                        contentType: false,
                        success:function(e,params){                            
                                if(params.numerror!=0)
                                {
                                    //win.alert("Detalle: "+params.descerror+". Consulte con el administrador","Error al crear",4)
                                    swal("Error al crear","Detalle: "+params.descerror+". Consulte con el administrador","error")
                                    if(that.error!=null){
                                        that.error();
                                    }
                                }
                                else
                                {that.options.modelo.set(params.data)
                                    if(that.success!=null){
                                        that.success();
                                    }
                                    
                                }
                            }
                        })//save    

                    }
                    
                    
                }
    
            }//validar
                        
            return false;
        }
});//campoView



var Categoria=Backbone.Model.extend();//provincias
var Categorias=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('categorias',Array('id_categoria as id','categoria as descripcion'),'','categoria asc');
        for(r in rs)
        {
            var elem=new Categoria(rs[r]);        
            this.add(elem);
        }        
    },
model:Categoria
});//provincias

var Fraccion=Backbone.Model.extend();//provincias
var Fracciones=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('fraccion',Array('id_fraccion as id','fraccion as descripcion'),'','fraccion asc');
        for(r in rs)
        {var elem=new Fraccion(rs[r]);        
         this.add(elem);
        }        
    },
model:Fraccion
});//provincias


var Tipocomprobante=Backbone.Model.extend();
var Tipocomprobantes=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('tipo_comprobante',Array('id_tipo_comp as id','tipo_comp as descripcion'),'','tipo_comp asc');
        for(r in rs)
        {var elem=new Tipocomprobante(rs[r]);        
         this.add(elem);
        }        
    },
model:Tipocomprobante
});//Tipocomprobantes


var Sucursal=Backbone.Model.extend();
var Sucursales=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('versucursales',Array('*'),'id_empresa='+window.localStorage.getItem("id_empresa")+' and id_tipo_sucursal=1','sucursal asc');
        for(r in rs)
        {var elem=new Sucursal(rs[r]);        
         this.add(elem);
        }        
    },
model:Sucursal
});//Tipocomprobantes

var Certificado=Backbone.Model.extend();
var Certificados=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('vercertificados',Array('id_certificado as id','detalle as descripcion'),'','habilitado desc,testing asc');
        for(r in rs)
        {var elem=new Certificado(rs[r]);        
         this.add(elem);
        }        
    },
model:Certificado
});//Tipocomprobantes


var Alicuota=Backbone.Model.extend();
var Alicuotas=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('alicuota',Array('id_alicuota as id','alicuota as descripcion','valor'),'','id_alicuota asc');
        for(r in rs)
        {var elem=new Certificado(rs[r]);        
         this.add(elem);
        }        
    },
model:Certificado
});//Tipocomprobantes

var Tipopago=Backbone.Model.extend({    
     url:'',
    default:{
        id_tipo_pago:0,
        tipo_pago:'',
        parametros:null,
        incide_caja:1
    }
});//Tipo_pago

var Tipopagos=Backbone.Collection.extend({
    initialize:function(options){    
        
        var oParamtipo=new Parametrostipopago();
        var rs=ofw.get('tipo_pagos',Array('id_tipo_pago','tipo_pago','incide_caja'),'habilitado=1','orden asc');
        for(r in rs)
        {   
            var p=oParamtipo.where({id_tipo_pago:rs[r].id_tipo_pago.toString()})
            rs[r]['parametros']=p
            var elem=new Tipopago(rs[r]);        
            this.add(elem);
        }        
    },
model:Tipopago
});//Tipopagos
var Parametrotipopago=Backbone.Model.extend({    
     url:'',
    default:{
        id_tipo_pago:0,
        nro_parametro:0,
        parametro:'',
        tipo_dato:'',
        descripcion:'',
        valor:''
    }
});//Parametrotipopago
var Parametrostipopago=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('tipo_pagos_parametros',Array('id_tipo_pago','nro_parametro','parametro','tipo_dato','descripcion'),'','');
        for(r in rs)
        {   rs[r]['valor']='';
            var elem=new Parametrotipopago(rs[r]);                 
         this.add(elem);
        }        
    },
model:Parametrotipopago
});//Parametrostipopago
var Banco=Backbone.Model.extend({    
     url:'',
    default:{
        id_banco:0,
        banco:''
    }
});//banco
var Bancos=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('bancos',Array('id_banco as id','banco as descripcion'),'','orden asc');
        for(r in rs)
        {   
         var elem=new Banco(rs[r]);                 
         this.add(elem);
        }        
    },
model:Banco
});//Bancos
var Tarjeta=Backbone.Model.extend({    
     url:'',
    default:{
        id_tarjeta:0,
        tarjeta:'',
        icono:'',
        habilitada:0
    }
});//tarjeta

var Tarjetas=Backbone.Collection.extend({
    initialize:function(models,options){ 
        var loaded=false;
        var rs=null;
        
        if(typeof options !="undefined")
        {
            if(typeof options.habilitadas !="undefined")
            { if(options.habilitadas)
              rs=ofw.get('tarjetas',Array('id_tarjeta as id','tarjeta as descripcion', 'icono','habilitada'),'habilitada=1','tarjeta asc');
              loaded=true;

            }
        }

        if(!loaded)
        {
        rs=ofw.get('tarjetas',Array('id_tarjeta as id','tarjeta as descripcion', 'icono','habilitada'),'','tarjeta asc');    
        }
        
        
        for(r in rs)
        {   
         var elem=new Tarjeta(rs[r]);                 
         this.add(elem);
        }        
    },
model:Tarjeta
});//tarjetas


var Tipoproducto=Backbone.Model.extend();
var Tiposproductos=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('tipo_item',Array('id_tipo as id','tipo as descripcion'),'','id_tipo asc');
        for(r in rs)
        {var elem=new Tipoproducto(rs[r]);        
         this.add(elem);
        }        
    },
model:Tipoproducto
});//Tipocomprobantes



var Tiposoperacion=Backbone.Model.extend();
var Tiposoperaciones=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('operaciones_tipo',Array('id_operacion_tipo as id','operacion_tipo as descripcion','movimiento'),'','movimiento,operacion_tipo');
        for(r in rs)
        {var elem=new Tiposoperacion(rs[r]);        
         this.add(elem);
        }        
    },
model:Tiposoperacion
});//TipoTiposoperacions





var Localidad=Backbone.Model.extend({
    initialize: function (options) {
        this.selected=false;

    }
});
var Localidades=Backbone.Collection.extend({model:Localidad,
cargar:function(oprovincia){
  if(this.models.length>0){
    return
  }
  
var id_provincia=oprovincia.get('id_pro')
var rs=ofw.get('sellocalidades',Array(id_provincia),true);
        for(r in rs)
        {
          var elem=new Localidad(rs[r]);        
         this.add(elem);
        }
},//cargar
cargarAsync:function(id_provincia){
  this.reset();
  var that=this
  ofw.getAsync('localidades',Array("*"),'id_pro='+id_provincia,'',function(rs){
    
    for(r in rs)
        {
          var elem=new Localidad(rs[r]);        
         that.add(elem);
        }
  });
        
}//cargarAsync
})


var Provincia=Backbone.Model.extend({
    initialize: function (options) {
        this.localidades= new Localidades()
        this.selected=false;
        this.loaded=false; //bandera que indica que la provincia esta cargada (con sus respectivas localidades)
    },
    cargarlocalidades:function(){
        this.localidades.reset();
        var rs=ofw.get('localidades',Array('id_loc','descripcion_loc'),'id_pro='+this.get('id_pro'),'descripcion_loc asc');
        for(r in rs)
        {
            var elem=new Localidad(rs[r]);        
            this.localidades.add(elem);
        }
        this.loaded=true;
    },
    selectLocalidad:function(id_loc){         
        var  locs=this.localidades.models;
        var exito=false;
          for (i in this.localidades.models)
         { 
            if(this.localidades.models[i].get('id_loc')==id_loc)
            {
             this.localidades.models[i].set({selected:true})
             exito=true;             
            }else{
             this.localidades.models[i].set({selected:false})
            }

         }        
        //en el caso de no existir lo busco en la base de datos
        if(!exito && !this.loaded){
            this.cargarlocalidades();
            this.selectLocalidad(id_loc);
        }
        return exito;
    }
});//provincia
var Provincias=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('provincias',Array('id_pro','descripcion_pro'),'','descripcion_pro asc');
        for(r in rs)
        {
            var elem=new Provincia(rs[r]);        
            this.add(elem);
        }        
    },
model:Provincia
});//provincias


var Condicion_iva=Backbone.Model.extend({
    initialize: function (options) {
        

    }
});

var Condiciones_iva=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('cond_iva',Array('id_cond_iva','condicion','comp_tipo'),'','condicion asc,comp_tipo');
        for(r in rs)
        {
            var elem=new Condicion_iva(rs[r]);        
            this.add(elem);
        }        
    },
model:Condicion_iva
});//provincias



var Sucursal_tipo=Backbone.Model.extend({
    initialize: function (options) {
    }
});

var Sucursales_tipo=Backbone.Collection.extend({
    initialize:function(models,options){    
        var rs=ofw.get('sucursal_tipo',Array('id_tipo_sucursal','tipo'),'','tipo asc');
        for(r in rs)
        {
            var elem=new Sucursal_tipo(rs[r]);        
            this.add(elem);
        }        
    },
model:Sucursal_tipo
});//provincias