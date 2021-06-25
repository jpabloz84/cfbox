
var Pago=Backbone.Model.extend({    
     url:'',
    default:{
        id_tipo_pago:1,//tipo pago seleccionado por defecto        
        monto_abona:0,
        observacion:'',
        tipospagos:null,
        eliminable:true,
        id_pago:0,
        id_comp_pago:{},
        monto_afectado:0,
        propio:true,
        estado:''
    },
    actualizarmonto_afectado:function(){
        //dados los id_comp_pago de la BD, obtengo los valores afactados de este item
        
        var acmp=this.get('id_comp_pago');
        var strids='';
        if(typeof acmp !="undefined"){
            for(i in acmp){
            if(strids!=''){
                strids+=','+acmp[i]
            }else{
                strids+=acmp[i]
            }            
            }    
        }        
        
        if(strids!=""){
             var cols=Array("id_comp_pago","id_comp","id_pago","monto")
            rs=ofwlocal.get("comp_pagos",cols,"id_comp_pago in("+strids+") and estado<>'A'","") 
            var total=0;
            for(i in rs){
                total+=parseFloat(rs[i]['monto']);
            }
            this.set({'monto_afectado':total})
        }
    }
});//itempago

var Pagos=Backbone.Collection.extend({
    model:Pago,
    load:function(comp){        
     //dado el parameotro (array del comprobante) ,agrego los pagos y cargo los pagos en funcion de lo imputado
     var tp=new Tipopagos();
     var tienepagocc=false;
     var tienepagosaldo=false;
     var i=0;
        var id_comp=comp['id_comp'];
      //si el listado de modelos , no tiene un item de pago agregado, lo agrego (pero que no sea eliminable)
       if(this.models.length==0)
       {
       var pago=new Pago({id_tipo_pago:1,monto_abona:0,observacion:'',tipospagos:tp,eliminable:false,disabled:false,monto_afectado:0,propio:true,estado:'',token:''});
       this.add(pago);
       }
        //dado un comprobante, se auto carga los pagos
        var rsp=ofwlocal.get("vercomp_pagos",Array("*"),"id_comp="+id_comp+" and estado<>'A'","propio desc");
      for(i in rsp){
        var arrcomp_pagos=Array();
        var monto_disabled=false;
        var id_tipo_pago=parseInt(rsp[i]['id_tipo_pago'])        
            arrcomp_pagos.push(parseInt(rsp[i]['id_comp_pago']));
        var monto_abona=parseFloat(rsp[i]['pago'])
        var monto_afectado=parseFloat(rsp[i]['monto'])
        var estado_pago=parseFloat(rsp[i]['estado_pago'])
        var token=rsp[i]['token']
        var propio=(rsp[i]['propio']==1)?true:false;
        if(id_tipo_pago==7)tienepagocc=true;

        if(!propio)
        {
         tienepagosaldo=true;
         monto_disabled=true;
        }        
       //si es el primero, tomo el primer item, sino lo agrego
       if(i==0){
        //en el caso de que ya exista, actualizo, sino, creo el primero               
        var pago=this.models[0];
        pago.set({id_tipo_pago:id_tipo_pago,monto_abona:monto_abona,observacion:rsp[i]['observacion'],id_pago:rsp[i]['id_pago'],propio:propio,disabled:monto_disabled,id_comp_pago:arrcomp_pagos,monto_afectado:monto_afectado,estado:estado_pago,token:token})
        this.actualizarModel(pago);        
        }else{
        var pago=new Pago({id_tipo_pago:id_tipo_pago,monto_abona:monto_abona,observacion:rsp[i]['observacion'],tipospagos:tp,eliminable:true,disabled:monto_disabled,id_pago:rsp[i]['id_pago'],propio:propio,id_comp_pago:arrcomp_pagos,monto_afectado:monto_afectado,estado:estado_pago,token:token});
        this.add(pago);
        }
      }//for
       var importe_total=0;
       var importe_pagado=0;
       var estado='';
       importe_total=parseFloat(comp['importe_total']);
       estado=comp['estado']
       importe_pagado=this.gettotal();
       
       //si el importe total es distinto al importe pago, que actualice el item de pago CC siempre y cuando 
       //asegurandose que el item de pago tipo 7 (cc) este en los pagos, sino lo agrego, como lo hace el siguiente if
       if((estado=="E" || estado=="F" ) && importe_pagado<importe_total){
         if(!tienepagocc){
          var pago=new Pago({id_tipo_pago:7,monto_abona:0,observacion:'',tipospagos:tp,eliminable:true,disabled:false,monto_afectado:0,propio:true,estado:''});        
          this.add(pago);
          }         
         var modelocc=this.actualizacc(importe_total);        
        }//if
    }, //fin load
    generarID:function(){
        //definicion: genera los IDS como atributo de cada pago para mantener referencia al enviarlo al server
        for (i in this.models) {            
            this.models[i].set("cid",this.models[i].cid)            
        }//for  

    }, 
    actualizarIDS:function(retPagos){
        //dato el array de pagos proveniente del server, actualizo los id pagos asignados por la BD
        
        for(p in this.models){
           for (r in retPagos){
            if(retPagos[r]['cid']==this.models[p]['cid']){
                this.models[p].set({'id_pago':retPagos[r]['id_pago'],'id_comp_pago':retPagos[r]['id_comp_pago']});
                this.models[p].actualizarmonto_afectado();
                break;
            }
           }
        }
    },
    gettotal:function(){
        
        //definicion:obtiene la sumatoria del valor campo
        var total=0;
        for (i in this.models) {
            var valor=0;
            if(this.models[i].get('propio'))
            {
                valor=this.models[i].get('monto_abona');
            }
            else{
                valor=this.models[i].get('monto_afectado');   
            }
            if(es_numero(valor))
            total+=parseFloat(valor);
            
        }
      return total;
    }, 
    //dado el importe total del carrito, obtiene el saldo para un item de CC, por eso el segundo parametros, es el id del modelo a ignorar
    getsaldoparacc:function(importe_total,id_model=''){        
        //definicion:importetotal-pagos  sin  que sea tipo pago acuenta
      var total=0;
        for (i in this.models) {
          
            if(this.models[i].get("id_tipo_pago")!=7 && this.models[i].cid!=id_model ){
                var valor=0;
                if(this.models[i].get('propio'))
                {
                    valor=this.models[i].get('monto_abona');
                }
                else{
                    valor=this.models[i].get('monto_afectado');   
                }
                if(es_numero(valor))
                total+=parseFloat(valor);

            }                        
        }//for  
        return importe_total-total;
    }, //getsaldoparacc
    //dado el importe total, obtiene el saldo sin aquellos items de tipos de pagos que no afectan a la caja
    getsaldoafectable:function(importe_total){        
        //definicion:importe_total-pagos que incidencaja
        var total=0;
        for (i in this.models) {
            var tipopago=(this.models[i].get("tipospagos")).findWhere({id_tipo_pago:(this.models[i].get("id_tipo_pago")).toString()});
            var incide_caja=parseInt(tipopago.get("incide_caja"));
            if(incide_caja==1)
            {             
             var valor=0;
                if(this.models[i].get('propio'))
                {
                    valor=this.models[i].get('monto_abona');
                }
                else{
                    valor=this.models[i].get('monto_afectado');   
                }

             if(es_numero(valor))
             total+=parseFloat(valor);
            }//si incide en caja
            
        }//for
        return importe_total-total;
    },//getsaldoafectable
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
    },//actualizarModel
    actualizacc:function(importe_total){
        //sean el importe total de la factura, se actualiza este item a cuenta en funcion de los demas siempre y cuando haya un pago de tipo a cuenta
        var totalsincc=0;
        var modelocc=null;
        for (i in this.models) {
            if(this.models[i].get("id_tipo_pago")==7){
                modelocc=this.models[i];
            }
            if(this.models[i].get("id_tipo_pago")!=7 && this.models[i].get("estado") !="A"){                
                var valor=0;
                if(this.models[i].get('propio'))
                {
                    valor=this.models[i].get('monto_abona');
                }
                else{
                    valor=this.models[i].get('monto_afectado');   
                }
                if(es_numero(valor))
                totalsincc+=parseFloat(valor);
            }                        
        }//for  
        var saldo=importe_total-totalsincc;
        if(saldo<0){
            saldo=0;
        }
        if(modelocc!=null){
        modelocc.set({monto_abona:saldo});
        this.actualizarModel(modelocc); 
        }
        return modelocc;
    }

})

var Pagosview=Backbone.View.extend(
{   el:$('#tbl-pagos'),
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(oPagos)
    { 
        var that=this;
        this.pagos=oPagos;

        $.get(this.options.base_url+'tpl/opera_pago_items.html', function (data) {

            tpl = _.template(data, {});
            var ctacte=false;
            if(typeof that.options.ctacte !="undefined"){
                ctacte=that.options.ctacte
            }

            var saldoafavor=false;
            if(typeof that.options.saldoafavor !="undefined"){
                saldoafavor=that.options.saldoafavor
            }
            
            htmlrender=tpl({campos:oPagos.models,base_url:that.options.base_url,ctacte:ctacte,saldoafavor:saldoafavor,onlyread:that.options.onlyread})
            that.$el.html(htmlrender);
            $("input[name='monto_abona']").keypress(function(e){ 
                //al presional enter, consulta en el servidor si posee algun tipo de recargo
                if(e.which==13)
                {
                    var id_model=e.target.id.replace("monto_abona_","")
                    var modelo=oPagos.get(id_model);
                    oRecargosView.calcular(modelo)
                }
                return teclamoney(e)
            })

            $("input[name='monto_abona']" ).focus(function(e) {
              //si se hace foco el monto es 0, se pone en vacio
              if($(this).val()==0){
                $(this).val("");
              }
            });

            $("a[id*='observacion_link_']").click(function(e){                
                var id_model=e.target.id.replace("observacion_link_","");
                $("#"+e.target.id).hide();
                $("#observacion_"+id_model).show();              
            })

             $("a[id*='recibo-link-']").click(function(e){                
              
              var id_node=(e.target.id.indexOf("recibo-link-")>=0)?e.target.id: e.target.parentNode.id 
              var id_model=id_node.replace("recibo-link-","");
              var op=oPagos.findWhere({id_pago:id_model})
                //window.open(that.options.base_url+'index.php/operaciones/pagos/pdfpago/'+id_model+'/','_blank');
                window.open(that.options.base_url+'index.php/operaciones/pagos/recibo/'+op.get("token")+'/','_blank');
            })

            $("input[name='observacion']").keypress(function(e){
                var id_model=e.target.id.replace("observacion_","")
                if(e.which==13)
                {
                    $("#"+e.target.id).hide();
                    $("#observacion_link_"+id_model).show();    
                    var modelo=oPagos.get(id_model);
                    var newobservacion=$("#"+e.target.id).val()
                    $("#observacion_link_"+id_model).html(newobservacion)
                    if(newobservacion=="")
                    {
                        $("#observacion_link_"+id_model).html("agregar observación")
                    }
                    
                    modelo.set({"observacion":newobservacion})
                   oPagos.actualizarModel(modelo)

                }
                
            })

            

        })
        
    },
    events:{
            "change select[name='id_tipo_pago']":'cambiar_tipo',
            "click button[name='btn-elimina-pago']":'elimina_pago',
            "click #btn-addpago":'agrega_pago',
            "keyup input[name='monto_abona']":'cambia_monto',
            "click button[id*='btn-info-pago-']":'editarparams'        
    },
    /*copylinkmp:function(e){
        debugger
        var id_node=(e.target.id.indexOf("btn-link-mp-")>=0)?e.target.id: e.target.parentNode.id 
        var id_model=id_node.replace("btn-link-mp-","");
        var modelo=oPagos.get(id_model);
        var linkmp=modelo.get("linkmp")
    },*/
    editarparams:function(e){
        
        var id_node=(e.target.id.indexOf("btn-info-pago-")>=0)?e.target.id: e.target.parentNode.id 
        var id_model=id_node.replace("btn-info-pago-","");

        var modelo=oPagos.get(id_model);
        //busco dentro del pago a modificar, las caracteristicas del tipo de pago seleccionado
        var tipopago=(modelo.get("tipospagos")).findWhere({id_tipo_pago:modelo.get("id_tipo_pago").toString()})
         //si lo anterior tiene parametros, es porque hay que completar mas info, sino, nada
         params=tipopago.get("parametros");
         if(params.length>0)
         {
            oPagosparametros.render(modelo,tipopago,globales['bancos'],globales['tarjetas'])
            $("#modal-parametros").modal("show");
         }

    },
    cambia_monto:function(e){
        
        var id_model=e.target.id.replace("monto_abona_",""); 
        var monto_abona=($("#"+e.target.id).val()).trim();
        if(es_numero(monto_abona))
        {
         var modelo=oPagos.get(id_model);
         modelo.set({monto_abona:tomoney(monto_abona)})
         oPagos.actualizarModel(modelo);

         //actualizo el campo de cuenta corriente en caso de existir
         var importe_total=oCarrito.gettotal('importe_total');
         var modelocc=oPagos.actualizacc(importe_total);         
          if(modelocc != null){            
            this.setcampocc(modelocc);
          }          
        oCarritoview.totalizar(); 
        //oRecargosView.calcular(modelo)
        }
     
    },    
    agrega_pago:function(e){
        var tp=new Tipopagos();
        var pago=new Pago({id_tipo_pago:1,monto_abona:0,observacion:'',tipospagos:tp,eliminable:true,disabled:false,propio:true,token:''});
        oPagos.add(pago);
        this.render(oPagos);
    },
    cambiar_tipo:function(e){
        
        var id_model=e.target.id.replace("id_tipo_pago_","");
        //obtengo el pago
        var modelo=oPagos.get(id_model);
        var id_tipo_pago_old=modelo.get("id_tipo_pago");
        var id_tipo_pago=$("#"+e.target.id).val();
        var importe_total=oCarrito.gettotal('importe_total');
        var modelocc=null; //modelo detipo a cuenta, que puede ser afectado si existe dentro de los items de pagos
        

        //si es a mercado pago, calculo la diferencia a pagar
         if(id_tipo_pago==8)
         { //busco si hay otro con el tipo de pago 8(cc) para no dejarlo seleccionar
            var haypagocc=false;
            for(p in oPagos.models) 
            {
                if(oPagos.models[p].get("id_tipo_pago")==8 && oPagos.models[p].id!=id_model){
                    
                    swal("Atención!", "Solo puede haber un pago por mercadopago", "error")
                    $("#"+e.target.id).val(id_tipo_pago_old);
                    return;                    
                }
            }
            var saldo=oPagos.getsaldoparacc(importe_total,id_model);
            if(saldo<0){
                saldo=0;
            }
            //aca le pongo el saldo total, despues se ve en el server
            modelo.set({monto_abona:saldo,id_tipo_pago:id_tipo_pago,disabled:true,propio:true})            
            modelocc=modelo;
         }else{
            if(id_tipo_pago!=6)
            { 
            modelo.set({disabled:false})            
            }
         }

        
         //si es a cuenta, calculo la diferencia a pagar
         if(id_tipo_pago==7)
         { //busco si hay otro con el tipo de pago 7(cc) para no dejarlo seleccionar
            var haypagocc=false;
            for(p in oPagos.models) 
            {
                if(oPagos.models[p].get("id_tipo_pago")==7 && oPagos.models[p].id!=id_model){
                    
                    swal("Atención!", "Solo puede haber un pago en cuenta corriente", "error")
                    $("#"+e.target.id).val(id_tipo_pago_old);
                    return;                    
                }
            }
            var saldo=oPagos.getsaldoparacc(importe_total,id_model);
            if(saldo<0){
                saldo=0;
            }
            //aca le pongo el saldo total, despues se ve en el server
            modelo.set({monto_abona:saldo,id_tipo_pago:id_tipo_pago,disabled:false,propio:true})            
            modelocc=modelo;
         }else{
            if(id_tipo_pago!=6)
            { 
            modelo.set({disabled:false})
            //$("#monto_abona_"+id_model).prop("disabled",false);
            }
         }

         //si es con saldo, calculo la diferencia a pagar
         if(id_tipo_pago==6)
         {  
            //busco si hay otro con el tipo de pago 6(con saldo) para no dejarlo seleccionar            
            for(p in oPagos.models) 
            {
                if(oPagos.models[p].get("id_tipo_pago")==6 && oPagos.models[p].cid!=id_model){                    
                    swal("Atención!", "Solo puede haber un pago de tipo saldo.", "error")
                    $("#"+e.target.id).val(id_tipo_pago_old);
                    return;                   
                }
            }
            var saldo=oCliente.saldo_afavor; //oPagos.getsaldoafectable(importe_total);
            if(saldo<0){
                saldo=0;
            }
            modelo.set({monto_abona:saldo})
            modelo.set({disabled:true})
                    
         }else{
            if(id_tipo_pago!=7)
             { 
                modelo.set({disabled:false})
             }
         }
         //seteo el modelo con el nuevo tipo de pago
         modelo.set({id_tipo_pago:id_tipo_pago})                  
         //guardo el pago, dentro de los pagos ingresados
         oPagos.actualizarModel(modelo);
         //siempre que el tipo de pago no sea a cuenta, se actualiza el valor del campo de a cuenta (siempre y cuando, exista)         
         if(id_tipo_pago!=7){
         //tambien se debe actualizar el pago tipo cc si existe, porq siempre es el resto de los demas
         modelocc=oPagos.actualizacc(importe_total);   
         }
         
         
         //busco dentro del pago a modificar, las caracteristicas del tipo de pago seleccionado
         var tipopago=(modelo.get("tipospagos")).findWhere({id_tipo_pago:id_tipo_pago.toString()})
         //si lo anterior tiene parametros, es porque hay que completar mas info, sino, nada
         params=tipopago.get("parametros");
         if(params.length>0)
         {
            oPagosparametros.render(modelo,tipopago,globales['bancos'],globales['tarjetas'])
            $("#modal-parametros").modal("show");
         }         
         oPagosView.render(oPagos);
         oCarritoview.totalizar(); 
         oRecargosView.calcular(modelo)
         
    },
    setcampocc:function(modelo){      
        //def:si hay un campo a cuenta, lo actualiza con el valor pasado por parametro
        try{
            var saldo=round2dec(parseFloat(modelo.get('monto_abona')));

        $("#monto_abona_"+modelo.cid).val(format_number((saldo).toString(),'#.00'));
        }catch (e) {

        }
    },
    elimina_pago:function(e){
        
        var str=(e.target.id.indexOf("btn-elimina-pago-")>=0)?e.target.id: e.target.parentNode.id
        var id_model=str.replace("btn-elimina-pago-","");
        var modelo=oPagos.get(id_model);
        oPagos.remove(modelo);
        var importe_total=oCarrito.gettotal('importe_total');
        var modelocc=oPagos.actualizacc(importe_total);
         
        this.render(oPagos);
        oCarritoview.totalizar();   
    },
    pagar_MP:function(id_pago){
       
        
        var that=this  
        that.options.linkmp="" 
          $.ajax({dataType: "json",type: 'POST',url:that.options.base_url+'index.php/operaciones/pagos/pagarMP/',data: {id_pago:id_pago},
            beforeSend: function(){
             spinnerStart($("#panel-body-pago"))
            },
            success: function(jsonResponse)
            {
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror; 
                $("#formmp").html("")               
                if(numError == 0)
                {    
                 

                  //agrego datos al form para mostrar boton de mp;
                  var rand=Math.floor((Math.random() * 1000)+1);
                  var urlscript="https://www.mercadopago.com.ar/integrations/v1/web-payment-checkout.js?v="+rand
                  var script= document.createElement('script');
                  formmp=document.getElementById("formmp"); 
                  formmp.appendChild(script)
                  $("#formmp script").attr("id","tagscript")
                  $("#formmp script").attr("data-public-key",jsonResponse.data['data-public-key'])
                  $("#formmp script").attr("data-preference-id",jsonResponse.data['data-preference-id'])
                  script.src=urlscript;
                  
                  that.options.linkmp="https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id="+jsonResponse.data['data-preference-id']
                  $("#linkmp").val(that.options.linkmp)
                 $(window).on("message", function(e) {

                    var origen=e.originalEvent.origin;
                    if(origen.indexOf("mercadopago")>=0){
                                          
                      
                    if(typeof e.originalEvent.data.type !="undefined"){
                        if(e.originalEvent.data.type=="submit"){
                            e.preventDefault();
                            e.stopPropagation();
                            console.log("Pagó...")
                            //var arrDatosPago=e.originalEvent.data.value                             
                            $("#formmp").html("")
                            that.verificarAcreditacion(id_pago)

                             $(window).off("message");
                         return
                        }
                        if(e.originalEvent.data.type=="close"){
                            console.log("cerro ventana mp...")
                             swal("error","El pago por mercadopago no se concretó.","error")
                            return
                        }
                    
                    }//origen                    
                    return;    
                    }
                  }); //oyente de mensajes windows
                }else
                {
                    //win.alert("Los cambios no se realizaron."+descError," Hubo un problema",1)
                    swal("error","Los cambios no se realizaron."+descError,"error")
                }
                
            },            
            complete: function(){
            spinnerEnd($("#panel-body-pago"))
            }
           })
       

    }, //pagarMP
    //manda a verificar si el comprobante esta pago
    verificarAcreditacion:function(id_pago){

       var that=this;
        console.log("verificamos acreditacion...")
          $.ajax({dataType: "json",type: 'POST',url:that.options.base_url+'index.php/operaciones/pagos/pagoverify/',data: {id_pago:id_pago},
            beforeSend: function(){
              spinnerStart($("#panel-body-pago"))
            },
            success: function(jsonResponse)
            {
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;                
                if(numError == 0)
                {
                  var data=jsonResponse.data
                  var montoacreditado=parseFloat(data.montoacreditado)
                  if(montoacreditado>0){
                    swal({
                           title: "Perfecto!!!",
                           text: descError,
                           type: "success",
                           showCancelButton: false,
                           confirmButtonColor: "rgb(71, 186, 193)",
                           confirmButtonText: "Aceptar",
                           closeOnConfirm: true
                        },
                        function () {
                         handleCheckPageLoadUrl(that.options.base_url+"index.php/operaciones/consultas/");  
                        });
                  }else{
                    swal({
                           title: "Atención",
                           text: "Por el momento no se ha acreditado el pago. Estamos esperando confirmación de mercadopago",
                           type: "info",
                           showCancelButton: false,
                           confirmButtonColor: "rgb(71, 186, 193)",
                           confirmButtonText: "Aceptar",
                           closeOnConfirm: true
                        },
                        function () {
                         handleCheckPageLoadUrl(that.options.base_url+"index.php/operaciones/consultas/");  
                        })
                setTimeout(function () {
                         handleCheckPageLoadUrl(that.options.base_url+"index.php/operaciones/consultas/"); 
                          }, 3000);
                }
              }else
              {
                swal("Error al verificar pago","Consulte con el administrador o intente luego.","error")
                console.log(descError)
              }
            },            
            complete: function(){
            spinnerEnd($("#panel-body-pago"))
            }
           }).fail( function( jqXHR, textStatus, errorThrown ) {
            console.log(textStatus)
            alert('Error!!');
            });


    }//verificarAcreditacion

})






var Pagosparametrosview=Backbone.View.extend(
{   el:$('#tbl-pagos'),
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(pago,tipopago,bancos,tarjetas)
    { //oPagosparametros.render(modelo,tipopago,globales['bancos'],globales['tarjetas'])
        var that=this;
        this.pago=pago;
        this.tipopago=tipopago;
        var mbancos=bancos.models;

        var mtarjetas=tarjetas.models;
        var parametros=tipopago.get('parametros');
        $.get(this.options.base_url+'tpl/opera_pago_parametros.html', function (data) {
            tpl = _.template(data, {});
            
            htmlrender=tpl({campos:parametros,bancos:mbancos,tarjetas:mtarjetas,base_url:that.options.base_url})
            that.$el.html(htmlrender);
            
            for (i in parametros)
            {
                if(parametros[i].get('parametro') !='id_banco' && parametros[i].get('parametro') !='id_tarjeta')
                {
                   if(parametros[i].get('tipo_dato')=='int')
                   {    
                        $("#"+parametros[i].get('parametro')).keypress(function(e){ return teclaentero(e)})
                        
                   }
                   if(parametros[i].get('tipo_dato')=='float')
                   {
                    $("#"+parametros[i].get('parametro')).keypress(function(e){ return teclamoney(e)})
                   }
                }
            }
        })
        
    },
    events:{            
            "click #save-params":'validateparams'
    },
    validateparams:function(){
        
         var strhtml=""
         var parametros=this.tipopago.get('parametros');
        for (i in parametros)
        { var parametro=parametros[i].get('parametro')
          var tipo_dato=parametros[i].get('tipo_dato')
            var desc=parametros[i].get('descripcion')
            var valor=$("#"+parametro).val();
            if(valor=='')
            {
                strhtml+="<li>No ha completado "+desc+"</li>"
            }
            if(tipo_dato=="int")
            {
                if(!es_entero(valor))
                    {strhtml+="<li>"+desc+" no tiene un valor correcto</li>"}
                if(es_entero(valor) && parametro=="mes_vencimiento" && !(parseInt(valor)>=1 && parseInt(valor)<13))
                {
                    strhtml+="<li>"+desc+" debe estar comprendido entre 1 y 12</li>"
                }

            }
            if(tipo_dato=="float")
            {
                if(!esNumeroReal(valor))
                    strhtml+="<li>"+desc+" no tiene un valor correcto</li>"
            }
        }
        if(strhtml!="")
        {   strhtml="<ul>"+strhtml+"</ul>"
            that=this;
            win.dialog(strhtml,'¿Desea continuar de todas formas?',4, function(t){            
                                        t.saveparams(parametros);
                },that); 
        }else{
            this.saveparams(parametros);
        }
    },
    saveparams:function(parametros){
        $("#modal-parametros").modal("hide");
        
        for (i in parametros)
        { var parametro=parametros[i].get('parametro')            
          var valor=$("#"+parametro).val();          
          parametros[i].set({valor:valor})
        }
        this.tipopago.set({parametros:parametros})
        var tipospagos=this.pago.get("tipospagos");
        for (i in tipospagos.models)
        {   //busco el objeto tipo pago  seleccionado en this.pago para asignarle el nuevo tipo con sus parametros
            if(this.pago.get("id_tipo_pago")==tipospagos.models[i].get("id_tipo_pago"))
            {
                tipospagos.models[i]=this.tipopago;
                break;
            }
        }
        this.pago.set({tipospagos:tipospagos});
        oPagos.actualizarModel(this.pago);        
        oPagosView.render(oPagos);
    }

})


function inputPagos_read(bandera)
{   

oPagosView.options.onlyread=bandera;
oPagosView.render(oPagos);

}


function save_edit_pagos()
{
    var strtipo=($("#chkorden").is(":checked"))?"esta orden de pedido":"este comprobante";
    win.dialog('Usted va modificar los pagos del comprobante y los mismos son irreversibles. ¿Desea continuar?',' Atención',3,send_pagos_modificados,null);
}
var strbtnold='';
function modificar_pagos()
{
    inputPagos_read(false);
    var dataloadspinner="data-loading-text=\"<i class='fa fa-spinner fa-spin '></i> procesando...\""
    oPagosView.options.onlyread=false;
    strbtnold=$("#btn-acciones").html();  
    var strbtn="<button type='button' class='btn btn-default btn btn-sm' id='btn-cancel' onclick='canceleditpagobtn()'><i class='fa fa-arrow-circle-left'></i> VOLVER</button>"
        strbtn+="<button "+dataloadspinner+" type='button' class='btn btn-success btn btn-sm' id='btn-save' onclick='save_edit_pagos()'><i class='fa fa-save'></i> IMPUTAR</button>"
   $("#btn-acciones").html(strbtn) 
    

}

function send_pagos_modificados()
{

var rs=ofwlocal.get("vercomprobantes",Array("*"),"id_comp="+$("#id_comp").val(),"");
var comp=rs[0];
var importe_total=parseFloat(comp['importe_total']);
var importe_carrito=oCarrito.gettotal('importe_total');
if(importe_total!=importe_carrito){
    win.alert("Se ha detectado que el comprobante cambió en su contenido. Intente guardar el comprobante"," No se pueden modificar los pagos",1)
    return
}

oPagos.generarID();
var strpagos=''
var pagos=JSON.stringify(oPagos.toJSON())
//spinnerStart($("#panel-body-pago"))

$.ajax({dataType: "json",type: 'POST',url:base_url+'index.php/operaciones/ventas/changepagos/',data: {pagos:pagos,id_comp:$("#id_comp").val()},
            beforeSend: function(){
            $("#btn-save").button('loading');
            },
            success: function(jsonResponse)
            {$("#btn-save").button('reset');
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;                
                if(numError == 0)
                {                    
                 win.alert("La modificacion de pagos para este comprobante fue correcta"," Exito",1)                 
                 //actualizo y muestro saldo si corresponde
                 oCliente.getsaldo();
                 mostrar_saldo();                 
                 inputPagos_read(true);
                 canceleditpagobtn();
                 oPagos=new Pagos();
                 oPagos.load(comp);
                 oPagosView.render(oPagos);
                }else
                {
                    win.alert("Los cambios no se realizaron."+descError," Hubo un problema",1)
                }
                
            },            
            complete: function(){
            $("#btn-pagar").button('reset');
            }
           })

}//send_pagos_modificados


function canceleditpagobtn()
{   inputPagos_read(true);
    strbtnold=$("#btn-acciones").html(strbtnold); 
}

function init_mod_pagos()
{

oPagos=new Pagos();
var oPago=new Pago({id_tipo_pago:1,monto_abona:0,observacion:'',tipospagos:globales['tipo_pagos'],eliminable:false,disabled:false,propio:true,estado:''});
oPagos.add(oPago);
var ctacte=(oCliente.get("cf")!=1)?true:false;
var saldoafavor=(oCliente.get("cf")!=1)?true:false;
oPagosView=new Pagosview({el:$('#tbl-pagos'),base_url:base_url,ctacte:ctacte,saldoafavor:saldoafavor})
oPagosView.render(oPagos);    
}

