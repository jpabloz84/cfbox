<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<script type="text/template" id="tpl-table-list"> 
<table id="data-table" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>EMPRESA</th>                                        
                                <th>CUIL</th>                                        
                                <th>TELEFONO</th>
                                <th>DOMICILIO</th>           
                                <th>-</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) {  %>
    <tr class="gradeA">        
        <td>(<%=elemento.get('id_empresa') %>) - <%=elemento.get('empresa') %></td>
        <td><%=elemento.get('cuil') %></td>
        <td><%=elemento.get('telefono') %></td>
        <td><%=elemento.get('direccion') %> </td>
        <td><button type="button" class="btn btn-sm btn-success" name='sucursal' id="sucursal-<%=elemento.get('id_empresa')%>">SUCURSALES</button>
            <button type="button" class="btn btn-sm btn-primary" name='ver' id="view-<%=elemento.get('id_empresa')%>">VER</button>            
        </td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" id="data-table-empresas">  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                 <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <h4 class="panel-title">Empresas Coffee Box</h4></div>             
                            <div class="panel-body" id="panel-body-empresas">
                             <form class="form-horizontal form-bordered" action="/" method="POST">
                            <div class="form-group">                                                                
                                 <div class="col-md-4">
                                    <input type="text" class="form-control" id="patronnombres" placeholder="nombre empresa"  />
                                 </div>                                 
                            </div>
                            <div class="form-group">
                                <div class="col-md-3">
                                <?php if($visitante->get_id_rol()==1){?>
                                    <button type="button" class="btn btn-sm btn-success" id="btnRealizarAlta" onclick="realizaralta()">CREAR EMPRESA</button>
                                <?php }?>                                 
                                </div>
                                <div class="col-md-6">                            
                                </div>
                                <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-primary m-r-5" id="bntBuscar" onclick="buscar(event)">BUSCAR</button>
                                </div>                            
                            </div>                            
                            <div class="form-group" id="tpl-table-query">
                            </div>                            
                            </form>
                            </div>
           </div>
        </div>
        <!-- end col-12 -->
    <!-- tabla --> 
    </div>
    <!-- begin row -->
    <div class="row" id="row-body">
    <!-- tabla --> 
    </div>

     <div class="row" id="row-empresa" style="display: none">
        
    </div>
    <!-- end row-empresa -->

</div>
<!-- end #content -->

<div id="modal-empresa-abm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
         <div class="modal-dialog modal-lg">
           <div class="modal-content" id="content-empresa-abm" >
                 
               
          </div>
        </div>
</div>

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script>
var localurlx=window.location.href.split("#")[1];

App.restartGlobalFunction();
App.setPageTitle('Empresas | CoffeBox APP');
var win= new fwmodal();
//var ofwlocal=new fw('<?=base_url()?>index.php/')
//ofwlocal.guardarCache=false;
var ofwtlocal=new fwt('<?=base_url()?>index.php/entidades/empresas/listenfwt')



var ocampoView=null;    
var oEmpresas=null;
var oCampos=null;
var olista=null;

var oLocalidades=null;
var oEmpresaview=null;
var oSucursalesview=null;
var oSucursales_tipo=new Sucursales_tipo()
var oProvincias=new Provincias();


$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        
                        inicializacion_contexto();     
                        $('[data-click="panel-reload"]').click(function(){
                            handleCheckPageLoadUrl("<?php echo base_url();?>index.php/entidades/empresas/");
                         })
                    })
                });
            });
        });
    });
});

var eventos = _.extend({}, Backbone.Events);

var Tipo_item=Backbone.Model.extend({
    defaults:{        
        id_tipo:0,
        tipo:'',
        selected:0,
        default:0,
        vigente:0
    }
});
var Tipos_items=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    load:function(){
        var that=this
        var rs=ofwtlocal.get("items",Array())
        for(r=0;r<rs.length;r++){
            that.add(rs[r]);
        }
        
    },
    model:Tipo_item
});

var oTipos_items=new Tipos_items();


var Sucursal=Backbone.Model.extend({
    defaults:{        
        id_sucursal:0,
        sucursal:'',
        direccion:'',
        telefono:'',
        responsable_legal:'',
        id_loc:"13208",
        id_pro:"12",
        email:'',
        mapa:'',
        id_tipo_sucursal:1
    }

});

var Sucursales=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    model:Sucursal
});
var SucursalesView=Backbone.View.extend(
{   el:$('#row-empresa'),
    defaults:{},
    initialize:function(options)
    {
        this.options=options || {};
        eventos.on("initload_sucursales",this.loading,this)
        eventos.on("endload_sucursales",this.endload,this)
        
        
        
    },
    render:function(sucursales,empresa=null)
    {   
        this.options.sucursales=sucursales
        if(empresa!=null){
        this.options.empresa=empresa    
        }
        
         var that=this
       $.get(this.options.base_url+'tpl/tpl_abm_sucursal.html', function (data) {        
        tpl = _.template(data, {});
        htmlrender=tpl({sucursales:that.options.sucursales,oprovincias:oProvincias,osucursales_tipo:oSucursales_tipo,base_url:that.options.base_url})
        that.$el.html(htmlrender);
         $("#row-empresa").show();
        if ($("#panel-body-empresas").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body-empresas").slideToggle();            
        }
        })

    },//render    
    loading:function(ocol)
    {
      
    },
    endload:function(ocol)
    {
      
    },
    events:{
            "click .btn-save-sucursales":'guardar',
            "change select[id*='provincia-']":'changeprovincia',
            "click .addsucursal":"sucursaladd",
            "click .savesucursal":"savesucursal",
            "click button[name='remove']":"removesucursal"
    },
    changeprovincia:function(e){
        
        var cid=e.target.id.replace("provincia-","")
        var sucursal=this.options.sucursales.get(cid)
        var id_pro=$(e.target).val()
        pro=(oProvincias.where({id_pro:id_pro}))[0]
        pro.cargarlocalidades()
        var localidades=pro.localidades
        $("#localidad-"+cid).html("")
        for(l in localidades.models){
            $("#localidad-"+cid).append("<option value='"+localidades.models[l].get("id_loc")+"'>"+localidades.models[l].get('descripcion_loc')+"</option>"); 
        }
    },
    sucursaladd:function(){
        
        var newsucursal=new Sucursal()
        this.options.sucursales.add(newsucursal)
        this.render(this.options.sucursales)
    },
    savesucursal:function(){
       var id_empresa= this.options.empresa.get("id_empresa")
       if(!id_empresa>0){
        swal("Atención","Antes de crear sucursales, debe crear la empresa","error")
        return;
       }
       var tienesucursalweb=false
        for(s in this.options.sucursales.models){
            var cid=this.options.sucursales.models[s].cid
            var sucursal=$("#sucursal-"+cid).val()
            if(sucursal==""){
                swal("Atención","no ha ingresado un nombre de sucursal","error")
                return;
            }
            var direccion=$("#direccion-"+cid).val()
            var telefono=$("#telefono-"+cid).val()
            var id_pro=$("#provincia-"+cid).val()
            var id_loc=$("#localidad-"+cid).val()
            var responsable_legal=$("#responsable_legal-"+cid).val()
            
            var mapa=$("#mapa-"+cid).val()
            var email=$("#email-"+cid).val()
            var id_tipo_sucursal=$("#id_tipo_sucursal-"+cid).val()
            if(id_tipo_sucursal==2){
                tienesucursalweb=true
            }
            this.options.sucursales.models[s].set({sucursal:sucursal,direccion:direccion,telefono:telefono,responsable_legal:responsable_legal,id_loc:id_loc,id_pro:id_pro,email:email,mapa:mapa,id_tipo_sucursal:id_tipo_sucursal,id_empresa:id_empresa})
        }
        if(!tienesucursalweb){
            swal("Atención","debe tener definida una sucursal web","error")
                return
        }

        var that=this

          swal({
          title: "Atención",
          text: '¿Desea guardar estas sucursales para esta empresa?',
          type: "info",
          showCancelButton: true,
          closeOnConfirm: true
            }, function () {
                $(".savesucursal").button('loading')
                var datasucursales=JSON.stringify(that.options.sucursales.models)
                $.ajax({dataType: "json",type: 'POST',url:that.options.base_url+'index.php/entidades/empresas/guardar_sucursales/' 
                    ,data: { datasucursales: datasucursales }
                    ,success: function(json)
                        {
                           $(".savesucursal").button("reset")
                            
                            var numError=json.numerror;
                            var descError=json.descerror;
                            
                            if(numError != 0)
                            {
                               swal("Error al guardar sucursales","Detalle: "+params.descerror+". Consulte con el administrador", "error")
                            }
                             that.options.empresa.cargar()
                            $("#row-empresa").hide();
                            $("#panel-body-empresas").slideToggle();
                            
                        },
                        beforeSend: function(){
                        
                            },
                        complete: function(){
                        
                            }
            })//ajax
            })



           
    },
    removesucursal:function(e){
        
        var cid=e.target.id.replace("remove-","")
        var sucremove=this.options.sucursales.get(cid)
        var that=this
                 swal({
          title: "Atención",
          text: '¿Desea eliminar esta sucursal?',
          type: "info",
          showCancelButton: true,
          closeOnConfirm: true
            }, function () {
                
                $.ajax({dataType: "json",type: 'POST',url:that.options.base_url+'index.php/entidades/empresas/eliminar_sucursal/' 
                    ,data: { id_sucursal: sucremove.get("id_sucursal") }
                    ,success: function(json)
                    {   
                        var numError=json.numerror;
                        var descError=json.descerror;
                        
                        if(numError != 0)
                        {
                           swal("Error al eliminar sucursal","Detalle: "+params.descerror+". Consulte con el administrador", "error")
                        }else{
                         that.options.sucursales.remove(sucremove)
                         that.render(that.options.sucursales)
                         
                        }
                        
                    }
            })//ajax



           
            });

    }
})//SucursalesView






var Configuracion=Backbone.Model.extend({
    defaults:{        
        total_max_comp_c:5000,
        mp_token_access:'',
        mp_modo_produccion:0,
        log_activo:1,
        orden_pedido:1,
        comprobantes:0,
        guardar_compra:0
    }

});

var Datos_Pagina=Backbone.Model.extend({
    defaults:{        
        meta_autor:'',
        meta_keywords:'',
        google_ads_cliente:'',
        fb_page:'',
        title_page:'',
        titulo:'',
        slogan:'',
        fb_page_id:'',
        instagram_page:'',
        twiter_page:'',
        id_prov_default:12,
        id_loc_default:13208,
        whatsapp1:'',
        whatsapp2:'',
        localidad:'',
        provincia:'',
        codigo_postal:'',
        direccion:'',
        email_host:'',
        email_pto:'',
        email_ssl:0,
        email:'',
        email_pwd:'',
        video_live:0,
        video_live_code:'',
        atencion_publico:''
    }

});
var Empresa=Backbone.Model.extend({
    url:'<?=base_url()?>index.php/entidades/empresas/abm',   
    idAttribute:'id_empresa',
     defaults:{        
        id_empresa:0,
        empresa:'',
        cuitl:'',
        telefono:'',
        direccion:'',
        id_loc:13208,
        logo:'',
        habilitado:1,
        ingresos_brutos:'',
        inicio_actividades:'',
        id_cond_iva:1,
        page:'',
        datos_pagina:new Datos_Pagina(),
        configuracion:new Configuracion(),
        sucursales:new Sucursales(),
        tipos_items:new Tipos_items()
    },
    cargar:function(){
        
        var rs=ofwtlocal.get("empresadata",Array(this.get("id_empresa")))
        var emp=rs[0]
        this.set({empresa:emp['empresa'],cuitl:emp['cuil'],telefono:emp['telefono'],direccion:emp['direccion'],id_loc:emp['id_loc'],id_pro:emp['id_pro'],logo:emp['logo'],habilitado:(emp['habilitado']=="1")?1:0,ingresos_brutos:emp['ingresos_brutos'],inicio_actividades:emp['ini_actividades'],id_cond_iva:emp['id_cond_iva'],page:emp['page']})
        var rs=ofwtlocal.get("empresa_datos_pagina",Array(this.get("id_empresa")))        
        if(rs.length >0){
        
        var arr={}
        for(r in rs){            
            arr[rs[r]['variable']]=rs[r]['valor']
        }
        var datos_pagina=new Datos_Pagina(arr);
        this.set({datos_pagina:datos_pagina})    
        }        
        var rs=ofwtlocal.get("empresa_configuracion",Array(this.get("id_empresa")))
        if(rs.length>0){
        arr={}
        for(r in rs){
            arr[rs[r]['variable']]=rs[r]['valor']            
        }
        var configuracion=new Configuracion(arr)
        this.set({configuracion:configuracion})    
        }
        
        var rs=ofwtlocal.get("empresa_sucursales",Array(this.get("id_empresa")))
        if(rs.length>0){
            var sucursales=new Sucursales()
        for(s in rs){
            sucursales.add(rs[s])
        }
        this.set({sucursales:sucursales}) 
        }
        var rs=ofwtlocal.get("empresa_items",Array(this.get("id_empresa")))
        if(rs.length>0){
            var items=new Tipos_items();
            for(s in rs){
            items.add(rs[s])
            }
            this.set({tipos_items:items})
        }
        
    }
});//Empresamodel
var Empresas=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){
        
        var that=this;
        that.reset();                
        ofwtlocal.getAsync("selempresa",patrones,function(rs){
         that.cargar(rs,that) 
     })
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
    model:Empresa
});


var EmpresasView=Backbone.View.extend(
{   el:$('#panel-body'),
    defaults:{ocampoView:null},
    initialize:function(options)
    {
        this.options=options || {};
        eventos.on("initload",this.loading,this)
        eventos.on("endload",this.endload,this)
    },
    render:function(patrones)
    {
        var that=this;
        
        if(oEmpresas ==null)
        {
        oEmpresas=new Empresas({eventos:eventos});    
        }
        
        oEmpresas.loadAsync(patrones)
        return this;
        
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body'));  
    },
    endload:function(ocol)
    {
      this.cargar(ocol)
    },
    events:{
            "click button[name='ver']":'ver',
            "click button[name='sucursal']":'versucursales'
    }
    ,
    cargar:function(oEmpresas)
    {               
        olist=oEmpresas.models  
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist}));
         $('#data-table').DataTable({responsive: true}); 
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {
        var id_model=(e.target.id).replace("view-","");
        var res=oEmpresas.where({"id_empresa":id_model})
        var modelo=null;
        if(res.length==0)
        {
            return        
        }
        modelo=res[0]
        modelo.cargar()
        this.mostrar(modelo)

    },
    versucursales:function(e){
        
        var id_model=(e.target.id).replace("sucursal-","");
        var res=oEmpresas.where({"id_empresa":id_model})
        var modelo=null;
        if(res.length==0)
        {
            return        
        }
        modelo=res[0]
        modelo.cargar()
        this.mostrarsucursales(modelo)

    },
    mostrar:function(modelo)
    {    

        this.options.empresa=modelo
        oEmpresaview.cargar(modelo)
        
    },
    mostrarsucursales:function(modelo){
        
        oSucursalesview.render(modelo.get("sucursales"),modelo)
    }
    


});//EmpresasView


var EmpresaView=Backbone.View.extend(
{   el:$('#row-empresa'),
    defaults:{},
    initialize:function(options)
    {
        this.options=options || {};
        eventos.on("initload_empresa",this.loading,this)
        eventos.on("endload_empresa",this.endload,this)
        this.options.provincias=new Provincias()
        this.options.condiciones_iva=new Condiciones_iva()
        
    },
    render:function(patrones)
    {   
    },//render    
    loading:function(ocol)
    {
      spinnerStart($('#panel-body'));  
    },
    endload:function(ocol)
    {
      
    },
    events:{
            "click .btn-save-empresa":'guardar',
            "change input[id*='inplogo']":"cambiaimg",
            "change #inpprovincia_empresa":"provinciachange",
            "change #inpprovincia_default":"provinciachange"
            
    },
    provinciachange:function(e){
        
        e.preventDefault()
        var id=e.target.id

        var id_pro=$(e.target).val()
        var pro=(this.options.provincias.where({id_pro:id_pro}))[0]
            pro.cargarlocalidades()            
        if(id.indexOf("empresa")>=0){            
            this.loadlocalidades($("#inplocalidades"),pro.localidades)
        }else{
        this.loadlocalidades($("#inplocalidad_default"),pro.localidades)    
        }
        
    }
    ,
    cambiaimg:function(e){
        
        e.preventDefault();    
        var input=e.target
        if (input.files && input.files[0]) {
            var reader = new FileReader();        
            reader.onload = function (e) {
                $("#inpimglogo").attr('src', e.target.result);
                $("#inpimglogo").attr('data-img', e.target.result);
                $('#is-new-img').prop('checked',true)
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    ,
    cargar:function(modelo)
    {    

        this.options.empresa=modelo
        var that=this
       $.get(this.options.base_url+'tpl/tpl_abm_empresa.html', function (data) {        
        tpl = _.template(data, {});
        htmlrender=tpl({empresa:that.options.empresa,oprovincias:that.options.provincias,ocondiciones_iva:that.options.condiciones_iva,base_url:that.options.base_url,tipos_items:oTipos_items})
        that.$el.html(htmlrender);
        if(typeof that.options.empresa.get("id_pro")!="undefined"){
            $("#inpprovincia_empresa").val(that.options.empresa.get("id_pro"))
        }
        var id_prov_empresa=$("#inpprovincia_empresa").val()
        var proempresa=(that.options.provincias.where({id_pro:id_prov_empresa}))[0]
        proempresa.cargarlocalidades()
        var locempresa=proempresa.localidades
        that.loadlocalidades($("#inplocalidades"),locempresa,that.options.empresa.get("id_loc"))


        var datos_pagina=that.options.empresa.get("datos_pagina")
        if(typeof datos_pagina.get("id_prov_default")!="undefined"){
            $("#inpprovincia_default").val(datos_pagina.get("id_prov_default"))
        }
        var id_prov_default=$("#inpprovincia_default").val()
        var profault=(that.options.provincias.where({id_pro:id_prov_default}))[0]
        profault.cargarlocalidades()
        var locfaults=profault.localidades
        that.loadlocalidades($("#inplocalidad_default"),locfaults,datos_pagina.get("id_loc_default"))
        $("#inpinicioactividades").mask("99/99/9999");
        })

       $("#row-empresa").show();
      if ($("#panel-body-empresas").is(":visible")) { //si esta abierto, que lo cierre
            $("#panel-body-empresas").slideToggle();            
        }
    },
    loadlocalidades:function(target,coleccion,id_loc=""){
        
        target.html("")
        for(l in coleccion.models){
            target.append("<option value='"+coleccion.models[l].get("id_loc")+"'>"+coleccion.models[l].get('descripcion_loc')+"</option>"); 
        }
        if(id_loc!=""){
        target.val(id_loc)    
        }
        
    },
    guardar:function (){
     //variables datos de la empresa
     var id_empresa=$(".btn-save-empresa").attr("idempresa")
     var nombreempresa=$("#inpempresa").val()
     var inpcuil=$("#inpcuil").val()
     var inptelefono=$("#inptelefono").val()
     var inpprovincia_empresa=$("#inpprovincia_empresa").val()     
     var inplocalidades=$("#inplocalidades").val()
     var inpdireccion=$("#inpdireccion").val()
     var inphabilitado=($("#inphabilitado").attr("checked"))?1:0
     var inpingresosbrutos=$("#inpingresosbrutos").val()
     var inpinicioactividades=$("#inpinicioactividades").val()
     var inpcondiva=$("#inpcondiva").val()
     var inppage=$("#inppage").val()
     var inpimglogo=($("#is-new-img").prop("checked"))?$("#inpimglogo").attr("src"):""
     
     //variables de la pagina
     var inpmetaautor=$("#inpmetaautor").val()
     var inpmeta_keywords=$("#inpmeta_keywords").val()
     var inpgoogle_ads_cliente=$("#inpgoogle_ads_cliente").val()
     
     var inptitle_page=$("#inptitle_page").val()
     var inptitulo=$("#inptitulo").val()
     var inpfb_page=$("#inpfb_page").val()
     var inptwiter_page=$("#inptwiter_page").val()
     var inpinstagram_page=$("#inpinstagram_page").val()     
     var inpgoogle_ads_cliente=$("#inpgoogle_ads_cliente").val()
     var inpfb_page_id=$("#inpfb_page_id").val()
     var inpslogan=$("#inpslogan").val()
     
     /*datos que acompañana al formulario de contacto*/
     var inplocalidadcontacto=$("#inplocalidadcontacto").val()
     var inpprovinciacontacto=$("#inpprovinciacontacto").val()
     var inpcodigo_postal_contacto=$("#inpcodigo_postal_contacto").val()
     var inpdireccion_contacto=$("#inpdireccion_contacto").val()
     /*datos de configuracion para mails de recepcion de formulario d econtacto y notificaciones*/
     var inpemail=$("#inpemail").val()
     var inpemail_host=$("#inpemail_host").val()
     var inpemail_pto=$("#inpemail_pto").val()
     var inpssl=($("#inpssl").attr("checked"))?1:0
     var inpemail_pwd=$("#inpemail_pwd").val()
     var inpprovincia_default=$("#inpprovincia_default").val()
     var inplocalidad_default=$("#inplocalidad_default").val()
     var inpwhatsapp1=$("#inpwhatsapp1").val()
     var inpwhatsapp2=$("#inpwhatsapp2").val()
     var inpquienessomos=$("#inpquienessomos").val()

     //variables de configuracion general
      var inptotal_max_comp_c=$("#inptotal_max_comp_c").val()
      var inpmp_token_access=$("#inpmp_token_access").val()
      var inpmp_modo_produccion=($("#inpmp_modo_produccion").attr("checked"))?1:0
      var inplog_activo=($("#inplog_activo").attr("checked"))?1:0
      var inporden_pedido=($("#inporden_pedido").attr("checked"))?1:0
      var inpcomprobantes=($("#inpcomprobantes").attr("checked"))?1:0
      var inpguardar_compra=($("#inpguardar_compra").attr("checked"))?1:0
      var inpvideo_live=($("#inpvideo_live").attr("checked"))?1:0
      var inpvideo_live_code=($("#inpvideo_live_code").val()).trim()
      var inputs_vigentes_checked=$("input[name='vigente']:checked").map(function(){ return $(this).val()})
      var inputs_defaults_checked=$("input[name='item_default']:checked").map(function(){ return $(this).val()})
      var inpatencion_publico=($("#inpatencion_publico").val()).trim()

      var aItems=Array()
      $("input[name='item']:checked").each(function(){
        aItems.push($(this).val())
      })


      for(i=0;i<oTipos_items.models.length;i++){
        var id_tipo=oTipos_items.models[i].get("id_tipo")

        if(aItems.indexOf(id_tipo)>=0){
            var vigente=0
            for(v=0; v<inputs_vigentes_checked.length;v++){
             if(inputs_vigentes_checked[v]==id_tipo){
                vigente=1
                break;
             }   
            }
            var default1=0
            if(inputs_defaults_checked.length>0 && inputs_defaults_checked[0]==id_tipo){
                default1=1
            }
            oTipos_items.models[i].set({selected:1,default:default1,vigente:vigente})
        }else{
            oTipos_items.models[i].set({selected:0})
        }
      }

      var items_selected=oTipos_items.where({selected:1})

      if(nombreempresa==""){
        swal("ATENCIÓN","debe completar el nombre de la empresa","error")
        return
      }
      if(inptelefono==""){
        swal("ATENCIÓN","debe completar el telefono de la empresa","error")
        return
      }
      if(inplocalidades==""){
        swal("ATENCIÓN","debe completar la localidad de la empresa","error")
        return
      }
      if(inpdireccion==""){
        swal("ATENCIÓN","debe completar la direccion de la empresa","error")
        return
      }
      if(inppage==""){
        swal("ATENCIÓN","debe completar el pagina a redirigir (subdominio) de la empresa","error")
        return
      }
      if(inpinicioactividades!=""){
        if(!esFechaF2(inpinicioactividades)){
        swal("ATENCIÓN","La fecha ingresada no tiene un formato valido","error")
        return     
        }
      }
      var datos_pagina=new Datos_Pagina();
      var configuracion=new Configuracion({total_max_comp_c:inptotal_max_comp_c,mp_token_access:inpmp_token_access,mp_modo_produccion:inpmp_modo_produccion,log_activo:inplog_activo,orden_pedido:inporden_pedido,comprobantes:inpcomprobantes,guardar_compra:inpguardar_compra,fb_page_id:inpfb_page_id});

      datos_pagina.set({page:inppage,meta_autor:inpmetaautor,meta_keywords:inpmeta_keywords,google_ads_cliente:inpgoogle_ads_cliente,fb_page_id:inpfb_page_id,title_page:inptitle_page,titulo:inptitulo,fb_page:inpfb_page,twiter_page:inptwiter_page,instagram_page:inpinstagram_page,slogan:inpslogan,localidad_contacto:inplocalidadcontacto,provincia_contacto:inpprovinciacontacto,codigo_postal:inpcodigo_postal_contacto,direccion_contacto:inpdireccion_contacto,email:inpemail,email_host:inpemail_host,email_pto:inpemail_pto,email_ssl:inpssl,email_pwd:inpemail_pwd,id_prov_default:inpprovincia_default,id_loc_default:inplocalidad_default,whatsapp1:inpwhatsapp1,whatsapp2:inpwhatsapp2,quienes_somos:inpquienessomos,video_live:inpvideo_live,video_live_code:inpvideo_live_code,atencion_publico:inpatencion_publico})

      this.options.empresa.url= this.options.base_url+'index.php/entidades/empresas/guardar/'    
        this.options.empresa.set({id_empresa:id_empresa,nombreempresa:nombreempresa,cuil:inpcuil,telefono:inptelefono,id_pro:inpprovincia_empresa,id_loc:inplocalidades,direccion:inpdireccion,habilitado:inphabilitado,ingresos_brutos:inpingresosbrutos,inicio_actividades:inpinicioactividades,id_cond_iva:inpcondiva,logo:inpimglogo,page:inppage,datos_pagina:datos_pagina,configuracion:configuracion,tipos_items:items_selected})
         //JSON.stringify(this.options.empresa)
         var that=this
         this.options.empresa.save(null,{wait: true,
               type:'POST',
                    beforeSend :function(){                        
                        $(".btn-save-empresa").button('loading');                        
                    },
                    success:function(e,params){ 
                        $(".btn-save-empresa").button("reset")
                         if(params.numerror!=0 )
                            {
                            
                            swal("Error al guardar empresa","Detalle: "+params.descerror+". Consulte con el administrador", "error")
                            
                            }else{
                                
                                that.options.empresa.set({id_empresa:params.data.id_empresa})
                                $(".btn-save-empresa").attr("idempresa",params.data.id_empresa)
                            }

                            
            }
        });//save empresa


    }
    


});//EmpresaView





function inicializacion_contexto()
{
olista=new EmpresasView({el:$('#tpl-table-query'),base_url:"<?=base_url()?>"}); 
oEmpresaview= new EmpresaView({el:$("#row-empresa"),base_url:"<?=base_url()?>"})
oSucursalesview= new SucursalesView({el:$("#row-empresa"),base_url:"<?=base_url()?>"})
oTipos_items.load();
//oProvincias.cargarlocalidades()
// $("#patron_cuit").keypress(function(e){return teclaentero(e)});     
 //$("#patron_nro_docu").keypress(function(e){return teclaentero(e)});     
}//inicializacion contexto

function buscar(evt)
{
evt.preventDefault();
var patrones={nombres:$("#patronnombres").val()}
 olista.render(patrones);
}

function realizaralta()
{   
     if(permitir(prt_configuracion,64))
     {
        var empresa=new Empresa();
        oEmpresaview.cargar(empresa)
      
     }else {
        //win.alert("<ul>No tiene permiso para realizar esta acción</ul>","ATENCIÓN",4)
        swal("ATENCIÓN","No tiene permiso para realizar esta acción","error")
     }
   
}
function resetformempresa(){

}


</script>
<!-- ================== END PAGE LEVEL JS ================== -->