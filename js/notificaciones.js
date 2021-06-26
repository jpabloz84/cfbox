var base_url_parent=$("#base_url_parent").val()
//var ofwlocalnotificaciones=new fwt(base_url_parent+'nn')
var last_nro_pedido=0
var oPedidoNotify=null;

//los audios deben estar en el front, porque el usuario debe tener interaccion con los audios a reproducir, sino no se reproduce nada
var PedidoNotify=Backbone.Model.extend({
    idAttribute:'id_pedido',
     defaults:{
        id_pedido:0,
        pathaudio:base_url_parent+"audios/notificaciones/piece-of-cake-611.m4r"

    },
    initialize:function(){
        var that=this
        
    },
  ingreso:function(id_pedido){
    var audio = new Audio(this.get("pathaudio"));
    audio.muted = false;
    audio.play(); 
    console.log("nuevo pedido"+id_pedido+" audio "+this.get("pathaudio"))
    $.gritter.options.time=10000;
    $.gritter.add({
            title: 'Nuevo pedido',
            text: 'Ha ingresado el encargo nro. '+id_pedido
        });  
  }
});
var oPedidoNotify=new PedidoNotify()
var arrCookies={}
var timeoutCookies=8000;
var arrCookies={};
var arrCookiesDet={};
/*setInterval(getlastinfo, 30000);
setInterval(evalstatususer, 25000);
setInterval(evalstatuscert, 25000);
setInterval(evalstatuspedidos, 5000);*/



var arrNoti=Array()
function start_notify(){
    last_nro_pedido=$("#last_nro_pedido").val()
    arrCookies['loginon']=1;
    arrCookies['last_notification']=0;
    arrCookies['crt_vencidos']=0;
    arrCookies['nro_pedido']= +last_nro_pedido;
  var n1=setInterval(getlastinfo, 5000);
  arrNoti.push(n1)
  var n2=setInterval(evalstatususer, 30000);
  arrNoti.push(n2)
  var n3=setInterval(evalstatuscert, 40000);
  arrNoti.push(n3)
  var n4=setInterval(evalstatuspedidos, 5000);
  arrNoti.push(n4)  

 

}
function stop_notify(){
  for(i in arrNoti){
  clearInterval(arrNoti[i]);  
  }
  
}


function evalstatususer()
{
    if(arrCookies['loginon']!=1)
    {  var fecha = new Date();
        fefin=fecha.getDate()+"-"+(fecha.getMonth()+1)+"-"+fecha.getFullYear()+" "+fecha.getHours()+":"+fecha.getMinutes()+": "+fecha.getSeconds();
        winparent.alert("La sesión se cerró por tiempo de inactividad"," Cierre de sesión automatica"+fefin,1)
        console.log("Se cerró automaticamente. Fecha inicio:"+feini+", Fecha fin:"+fefin);
        window.location.href=base_url_parent+'index.php/login/salir'
    }
}

var certnoti=false
function evalstatuscert()
{  
    //si no fue notificado y hay certificados vencidos, que muestre la notificacion
    if(arrCookies['crt_vencidos']==1 && !certnoti)
    {  certnoti=true;
        $.gritter.options.time=30000;
        var rs=arrCookiesDet['crt_vencidos'];
        for(i in rs){
            var id_cert=rs[i].id_certificado
            var nombre=rs[i].nombre 
            var fe_vencimiento=rs[i].fe_vencimiento 
            $.gritter.add({
                title: 'Certificado ID '+id_cert+' pronto a vencerse',
                text: nombre+' '+fe_vencimiento+'. Notifíquelo y actualicelo'
            });    
        }
        
    }
}


function evalstatuspedidos()
{  
    //si no fue notificado y hay certificados vencidos, que muestre la notificacion
    if(arrCookies['nro_pedido']>last_nro_pedido)
    {  last_nro_pedido= +arrCookies['nro_pedido']
        
        oPedidoNotify.ingreso(last_nro_pedido)
    }
}

function getlastinfo()
{

var strxml="<\?xml version='1.0' \?><body>";
$.each(arrCookies, function( key, value ) {
    strxml+="<variable name='"+key+"'>"+value+"</variable>";  
});
strxml+="</body>";


$.ajax({
    url: base_url_parent+"index.php/panel/lastinfo/",
    dataType: "json",type: 'POST',data: { strXml:strxml} ,
    timeout: timeoutCookies, // sets timeout to 6 seconds
    error: function(dataResponse){
        // will fire when timeout is reached
    },
    success: function(jsonResponse){         
           if(typeof jsonResponse.numerror=="undefined"){                
                return
                }
        
        var numError=parseInt(jsonResponse.numerror);
        var descError=jsonResponse.descerror;                
        if(numError == 0)
        { 
          var dataJson = jsonResponse.data;
            $.each(dataJson, function( key, val ) {                
                var variable=val.variable
                var valor=val.valor
                    if(arrCookies[variable]!= valor)
                    {
                        if(typeof val.det !="undefined"){
                            arrCookies[variable]=valor
                            arrCookiesDet[variable]=val.det

                        }else{
                            arrCookies[variable]=valor                      
                        }
                    
                    }           
                
                }); 
        }
    
    }});//ajax

}//getlastinfo