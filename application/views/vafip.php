<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>  
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/DataTables/media/css/responsive.bootstrap.min.css" rel="stylesheet" />
<link href="<?=BASE_FW?>assets/plugins/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
<link href="<?php echo BASE_FW; ?>assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />  
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content"> 
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">Servicio afip</h4>
            </div>             
            <div class="panel-body" id="panel-body1">
                <form class="form-horizontal" action="/" method="POST">
                <div class="form-group">
                    <div class="col-md-4">
                            <select class="form-control" id="talonario" onchange="cambia_talonario()">
                                    <?php 
                                    foreach ($talonarios as $tal) {
                                        echo "<option value='".$tal['id_talonario']."' >".$tal['tipo_comp']." - ".$tal['nro_talonario']."</option>";
                                    }
                                     ?>
                            </select>
                    </div>                    
                    <div class="col-md-2">
                            <button type='button' class='btn btn-inverse btn btn-sm' onclick="test_servicio()" class="form-control"><i class='fa fa-check-circle' ></i>
                            TESTEAR SERVICIO</button>
                    </div>
                    <div class="col-md-2">
                        <button type='button' class='btn btn-inverse btn btn-sm' onclick="generar_ta()" class="form-control"><i class='fa fa-check-circle' ></i>
                            GENERAR TICKET</button>
                    </div>                    
                    <div class="col-md-2">
                        <button type='button' class='btn btn-inverse btn btn-sm' onclick="ultimo_comprobante()" class="form-control">
                            <i class='fa fa-check-circle' ></i>
                            ULTIMO COMPROBANTE</button>
                          
                    </div>
                    <div class="col-md-2">
                        <button type='button' class='btn btn-inverse btn btn-sm' onclick="getptovta()" class="form-control">
                            <i class='fa fa-check-circle' ></i>
                            GET PTOS VTAS AFIP</button>
                          
                    </div>
               </div>
               <div class="form-group">
                    <div class="col-md-5">
                          <div class="note note-info">
                            <h4>Datos del talonario</h4>
                            <p id="datos-talonario"></p>
                        </div>
                    </div>
                    <div class="col-md-7">
                            <div class="note note-info">
                            <h4>Respuesta ws</h4>
                            <p id="datos-ws"></p>
                        </div>
                    </div>
               </div>
            </form>
            </div>
           </div>
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
    <!-- begin row -->
    <div class="row">       
        <!-- begin col-10 -->
        <div class="col-md-12" >  
            <div class="panel panel-inverse">
            <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">Logs</h4>
            </div>             
            <div class="panel-body" id="panel-body">
                <form class="form-horizontal" action="/" method="POST" id='form-standard'>                   
                <div class="form-group">                    
                    <div class="col-md-2">                            
                        <label>fecha desde
                            <input type="text" name="fecha_desde" id="fecha_desde" class="form-control" placeholder="ingrese fecha..." />
                        </label>
                    </div>
                    <div class="col-md-2">
                        <label>fecha hasta
                        <input type="text" name="fecha_hasta" id="fecha_hasta" class="form-control" placeholder="ingrese fecha..." />
                    </label>
                    </div>    
                    <div class="col-md-6">
                    <div class="note note-info">
                            <h4>información</h4>
                            <p id="datos-consulta">Haga doble click sobre las cajas de texto para ver con más detalle
                            </p>
                        </div>
                    </div>                    
                    
                    <div class="col-md-2">
                        <button type='button' class='btn btn-inverse btn btn-sm' onclick="consultar()" class="form-control">
                            <i class='fa fa-check-circle' ></i>
                            CONSULTAR</button>
                    </div>
               </div>
               <div class="form-group" id="table-wrapper">
                    
               </div>
            </form>
            </div>
           </div>
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row-12 -->
</div>
<!-- end #content -->

    
<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script type="text/template" id="tpl-table-list"> 
<table id="data-table" id="data-table" class="table-responsive table-striped" width="100%" role="grid" aria-describedby="data-table_info" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>evento</th>
                                <th>detalle</th>                                
                                <th>request</th>                                        
                                <th>response</th>                                        
                            </tr>
                        </thead>
                        <tbody>                                                               
    <% _.each(ls, function(elemento) { 
    var max=40;
    if(elemento.get('detalle')== null)
    {elemento.set({detalle:''})}

    if(elemento.get('request')== null)
    {elemento.set({request:''})}

    if(elemento.get('response')== null)
    {elemento.set({response:''})}
    
    var detalle=(((elemento.get('detalle')).length > max)? (elemento.get('detalle').substring(0,max))+"...":elemento.get('detalle')).trim()
    var request=formatXml((((elemento.get('request')).length>max)? (elemento.get('request').substring(0,max))+"...":elemento.get('request')).trim())
    var response=formatXml((((elemento.get('response')).length>max)? (elemento.get('response').substring(0,max))+"...":elemento.get('response')).trim())


     %>
    <tr>        
        <td>(<%=elemento.get('id')%> - <%=elemento.get('fecha') %>) - <%=elemento.get('evento') %></td>
        <td style="text-align:right"><span><%=detalle %><a href="javascript:;" class="btn btn-white btn-xs" id="detalle-link-<%=elemento.get('id')%>"><i class="fa fa-eye"></i></a></span>
                <textarea style="display:none" id="detalle-<%=elemento.get('id')%>"><%=detalle %></textarea>
    </td>
     <td style="text-align:right">
     <span><%=request %><a href="javascript:;" class="btn btn-white btn-xs" id="request-link-<%=elemento.get('id')%>"><i class="fa fa-eye"></i></a></span><textarea style="display:none" id="request-<%=elemento.get('id')%>"><%=elemento.get('request')%></textarea>
    </td>
    <td style="text-align:right"><span><%=response %><a href="javascript:;" class="btn btn-white btn-xs" id="response-link-<%=elemento.get('id')%>">
                <i class="fa fa-eye"></i></a></span><textarea style="display:none" id="response-<%=elemento.get('id')%>"><%=elemento.get('response')%></textarea>
    </td>
    </tr>
    <% }); %>
    </tbody>
</table>
</script>

<script>
App.restartGlobalFunction();
App.setPageTitle('Afip Service | Coffee APP');
var base_url='<?=base_url()?>index.php/'
var ofwlocal=new fw(base_url)
var win= new fwmodal();
ofwlocal.guardarCache=false;

$.getScript('<?=BASE_FW?>assets/plugins/bootstrap-combobox/js/bootstrap-combobox.js').done(function(){
    $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/jquery.dataTables.min.js').done(function() {
        $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js').done(function() {
            $.getScript('<?=BASE_FW?>assets/plugins/DataTables/media/js/dataTables.responsive.min.js').done(function() {
                $.getScript('<?=BASE_FW?>assets/js/table-manage-responsive.demo.min.js').done(function() { 
                    $.getScript('<?=BASE_FW?>assets/plugins/masked-input/masked-input.min.js').done(function(){
                        $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js').done(function() {
               $.getScript('<?=BASE_FW?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js').done(function() {
                            inicializacion_contexto();     
                            $('[data-click="panel-reload"]').click(function(){
                                handleCheckPageLoadUrl("<?php echo base_url();?>index.php/afip/");
                             })
                          });
                        });
                    });
                });
            });
        });
    });
});
var oColecciones=null;
var eventos = _.extend({}, Backbone.Events);


var Elemento=Backbone.Model.extend({
    url:'<?=base_url()?>',   
    idAttribute:'id',
    defaults:{
        id:0,
        evento:'',
        detalle:'',
        fecha:'',
        request:'',
        response:''
    }
});//elementomodel
var Coleccion=Backbone.Collection.extend({     
    initialize:function(options){        
        this.options=options || {}             
    },
    loadAsync:function(patrones){
        var that=this;
        that.reset();  
              
        var cond="1=1"
        
        if(patrones['fecha_desde']!="")
        {var strdate=todate(patrones['fecha_desde'],103);
            cond+=' and fecha>="'+strdate+'"';
        }
        if(patrones['fecha_hasta']!="")
        {
            var strdate=todate(patrones['fecha_hasta'],103);
            cond+=" and fecha< ADDDATE('"+strdate+"',1)";
        }        
        if(typeof this.options.eventos !="undefined")
        {
            this.options.eventos.trigger("initload",this);
        }        
        var cmp=Array("id","detalle","evento","DATE_FORMAT(fecha,'%d/%m/%Y %r') as fecha","request","response");
        ofwlocal.getAsync("afip_logs",cmp,cond,"fecha asc",function(rs){ 
            
            that.cargar(rs,that) 
        } )
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
    model:Elemento
});


var ElementosView=Backbone.View.extend(
{   el:$('#panel-body'),
    defaults:{ocampoView:null},
    initialize:function(options)
    {
        this.options=options || {};
    },
    render:function(patrones)
    {
        var that=this;
        
        if(oColecciones ==null)
        {
        oColecciones=new Coleccion({eventos:eventos});    
        }
        eventos.on("initload",this.loading,this)
        eventos.on("endload",this.endload,this)
        oColecciones.loadAsync(patrones)
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
            "click a[id*='link']":'ver'
    },
    cargar:function(oColecciones)
    {               
        olist=oColecciones.models  
        var tpl=_.template($('#tpl-table-list').html());                
        this.$el.html(tpl({ls:olist}));
         //$('#data-table').DataTable({responsive: true}); 
         spinnerEnd($('#panel-body'));
    },
    ver:function(e)
    {   
        var ar=e.target.id.split("-")
        if(ar.length==1)
        {
            ar=e.target.parentNode.id.split("-")
        }
        if(ar.length==3)
        {
            var id=ar[0]+'-'+ar[2]
            var contenido=$("#"+id).val()
            var win = window.open('','_blank');
            var scr="<script type='text/javascript'>function copyclipboard(){var copyText = document.getElementById('contenido');copyText.select();document.execCommand('copy');alert('copiado')}<\/script>"
            win.document.write(scr+"<input type='button' onclick='copyclipboard()' value='copiar al portapapeles' /><br/><textarea rows='1200' cols='300' style='border:none;' id='contenido' readonly>"+contenido+"</textarea>");
            win.document.close();
        }
     
    },
    mostrar:function(modelo,modo)
    {
        oCampos=new Campos(); //coleccion
    }

})//elementos view



function inicializacion_contexto()
{

$("#fecha_desde").mask("99/99/9999");
                $("#fecha_desde").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });

$("#fecha_hasta").mask("99/99/9999");
                $("#fecha_hasta").datepicker({
                     todayHighlight: true,
                     format: 'dd/mm/yyyy',
                     language: 'es',
                     autoclose: true
                });                
cambia_talonario()
olista=new ElementosView({el:$('#table-wrapper')});    
}//inicializacion contexto

function consultar()
{
    
var patrones={fecha_desde:$("#fecha_desde").val(),fecha_hasta:$("#fecha_hasta").val()}
 olista.render(patrones);
}


function test_servicio()
{
var id_talonario=$("#talonario").val();
$("#datos-ws").html("consultando ws...")                        
$.ajax({dataType: "json",type: 'POST',url:'<?php echo base_url(); ?>index.php/afip/testingsrv',data: {id_talonario:id_talonario} ,
            success: function(jsonResponse)            
            {   
                if(typeof jsonResponse.numerror=="undefined"){
                $("#datos-ws").html(jsonResponse)
                return
                }
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;
                  $("#page-loader").modal("hide")
                if(numError == 0)
                {   
                 $("#datos-ws").html(descError)                        
                }else
                {
                    $("#datos-ws").html("Error:"+descError)
                }
                
            },
            beforeSend: function(){
           spinnerStart($('#panel-body1'));
                },
            complete: function(){
                
            spinnerEnd($('#panel-body1'));
                },
                error: function (request, status, error) {                
                $("#datos-ws").html(request.responseText)
                }
           })

}

function getptovta(){
$("#datos-ws").html("consultando ws...")                        
var id_talonario=$("#talonario").val();

$.ajax({dataType: "json",type: 'POST',url:'<?php echo base_url(); ?>index.php/afip/getptovta',data: {id_talonario:id_talonario} ,
            success: function(jsonResponse)
            {   
                if(typeof jsonResponse.numerror=="undefined"){
                $("#datos-ws").html(jsonResponse)
                return
                }
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;
                  $("#page-loader").modal("hide")
                if(numError == 0)
                {   
                 $("#datos-ws").html('<code>'+jsonResponse.data+'</code>')                        
                }else
                {
                    $("#datos-ws").html("Error:"+descError)
                }
                
            },
            beforeSend: function(){
           spinnerStart($('#panel-body1'));
                },
            complete: function(){
            spinnerEnd($('#panel-body1'));
                },
                error: function (request, status, error) {                
                $("#datos-ws").html(request.responseText)
                }
           })
}    



function ultimo_comprobante()
{
$("#datos-ws").html("consultando ws...")                        
var id_talonario=$("#talonario").val();

$.ajax({dataType: "json",type: 'POST',url:'<?php echo base_url(); ?>index.php/afip/lastcmp',data: {id_talonario:id_talonario} ,
            success: function(jsonResponse)
            {   if(typeof jsonResponse.numerror=="undefined"){
                $("#datos-ws").html(jsonResponse)
                return
                }
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;
                  $("#page-loader").modal("hide")
                if(numError == 0)
                {   
                 $("#datos-ws").html(descError)                        
                }else
                {
                    $("#datos-ws").html("Error:"+descError)
                }
                
            },
            beforeSend: function(){
           spinnerStart($('#panel-body1'));
                },
            complete: function(){
            spinnerEnd($('#panel-body1'));
                },
                error: function (request, status, error) {                
                $("#datos-ws").html(request.responseText)
                }
           })
}

function generar_ta()
{
var id_talonario=$("#talonario").val();
$("#datos-ws").html("consultando ws...")                        
$.ajax({dataType: "json",type: 'POST',url:'<?php echo base_url(); ?>index.php/afip/generata',data: {id_talonario:id_talonario} ,
            success: function(jsonResponse)
            {  
                if(typeof jsonResponse.numerror=="undefined"){
                $("#datos-ws").html(jsonResponse)
                return
                }
                var numError=parseInt(jsonResponse.numerror);
                var descError=jsonResponse.descerror;
                  $("#page-loader").modal("hide")
                if(numError == 0)
                {   
                 $("#datos-ws").html(descError)                        
                }else
                {
                    $("#datos-ws").html("Error:"+descError)
                }
                
            },
            beforeSend: function(){
           spinnerStart($('#panel-body1'));
                },
            complete: function(){
            spinnerEnd($('#panel-body1'));
                },
               error: function (request, status, error) {                
                $("#datos-ws").html(request.responseText)
                }
           })
}//generar TA

function cambia_talonario()
{
    var id_talonario=$("#talonario").val()
    var cmp=Array('id_talonario','nro_talonario','habilitado','tipo_comp','sucursal',"DATE_FORMAT(fe_emision,'%d/%m/%Y %r') as fe_emision","DATE_FORMAT(fe_vencimiento,'%d/%m/%Y %r') as fe_vencimiento",'path','path_key','strnombre')
    spinnerStart($("#panel-body1"))
    ofw.getAsync('vertalonarios',cmp,'id_talonario='+id_talonario,'',talonario_loaded);  


}
function talonario_loaded(rs)
{
    var strhtml="";
    if(rs.length>0){
    strhtml+='Talonario: ID '+rs[0]['id_talonario']+ ' Nro.:'+rs[0]['nro_talonario']+'<br/>'
    strhtml+='Estado: '+((rs[0]['habilitado']=='1')?'habilitado':'inhabilitado')+'<br/>'
    strhtml+='Comprobante: '+rs[0]['tipo_comp']+' - sucursal: '+rs[0]['sucursal']+'<br/>'
    strhtml+='Certificador: '+rs[0]['strnombre']+'<br/>'
    strhtml+='Fecha emision: '+rs[0]['fe_emision']+'<br/>' 
    strhtml+='Fecha vencimiento: '+rs[0]['fe_vencimiento']+'<br/>'
    strhtml+='path: '+rs[0]['path']+'<br/>'
    strhtml+='path_key: '+rs[0]['path_key']+'<br/>'    
    }
    
$("#datos-talonario").html(strhtml)
spinnerEnd($("#panel-body1"))
}

function formatXml(input) {
   return input.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}


function reinicializar_variables()
{


return
}




</script>
<!-- ================== END PAGE LEVEL JS ================== -->