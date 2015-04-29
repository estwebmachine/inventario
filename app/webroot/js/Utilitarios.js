/*Armar division*/
function armardivision( id )
{
	var fila = '';
		fila += '<tr id="division_'+id+'">';
		fila += '<td class="unidadDemandante">';
		fila += '<select id="selectDivision_'+id+'" class="selectDivisionClick" rel="'+id+'" name="data[Subdivision]['+id+'][division_id]" style="float: left;"><option value="">Sel..</option></select>';
		fila += '<select id="selectSubDivision_'+id+'" class="selectSubDivisionClick" rel="'+id+'" name="data[Subdivision]['+id+'][subdivision_id]" style="display: none;"><option value="">Sel..</option></select>';
		fila += '<img src="'+wroot+'img/icons/menos.png" alt="Quitar" class="quitarDivision" rel="'+id+'" style="float: right;" />';
		fila += '</td>';
		fila += '</tr>';

	return fila;
}

/* ORDEN DE COMPRA */
function existeOrdenDeCompraEnLista( ordenCompraId )
{
	var existe = -1;
	if( $('.ordenCompra').length ){
		$('.ordenCompra').each(function(){
			var valorAtributo = $(this).val();
			if( ordenCompraId == valorAtributo ){
				existe = ordenCompraId;
			}
		});
		if( existe != -1 ){
			return true;
		}
	}
	return false;
}

function agregarOrdenDeCompra(ordenCompraId, numeroOrden, proveedor, monto, cuotas )
{
	var fila = '';
		fila += '<tr id="orden_'+ordenCompraId+'" >';
			fila += '<td>';
			fila += '<input type="hidden" class="ordenCompra" id="ordenCompra_'+ordenCompraId+'" name="data[PurchaseOrder][][id]" value="'+ordenCompraId+'">';
			fila +=  ''+numeroOrden;
			fila += '</td>';
			fila += '<td>'+proveedor+'</td>';
			fila += '<td class="oc_amount">'+monto+'</td>';
			fila += '<td><input type="hidden" name="data[ForcePayment][' + ordenCompraId + ']" value="0"><input type="checkbox" name="data[ForcePayment][' + ordenCompraId + ']"></td>';
			fila += '<td><img src="'+wroot+'img/icons/menos.png" class="eliminarFilaOrdenCompra" rel="'+ordenCompraId+'" /></td>';
		fila += '</tr>';
	$('#datosOrdenCompra').last().append( fila );
}

function totalOrdenDeCompras() {
	var oc_amount_total = 0;
	$('.oc_amount').each(function(){
		oc_amount_total += parseInt($(this).html().replace(/[A-Za-z$-]/g, ""));
	});
	return oc_amount_total;
}

function agregarCuotas(ordenCompraId, ordenPagoId)
{
	$.ajax({
		url: webroot + '/payment_schedules/get_schedule/' + ordenCompraId,
		type: 'POST',
		dataType: 'json',
		success: function(response) {
			var schedule_count = $('.schedule').length;
			
			$.each(response, function(index, value) {
				var id = response[index]['PaymentSchedule']['id'];
				var date = formatDate( response[index]['PaymentSchedule']['date'] );
				var status = response[index]['PaymentSchedule']['status'];
				var purchase_order_number = response[index]['PurchaseOrder']['order_number'];
				var purchase_order_id = response[index]['PurchaseOrder']['id'];
				var payment_order_id = response[index]['PaymentSchedule']['payment_order_id'];
				var payment_order_number = (response[index]['PaymentOrder']['id'] == null)? '-' : response[index]['PaymentOrder']['id'] + '-' + response[index]['PaymentOrder']['month_year'];
				
				var status_names = ['No Pagada', 'En Proceso', 'Pagada'];
				var fila = '';
				fila += '<tr id="cuota_' + id + '" class="schedule schedule_for_' + purchase_order_id + '">';
				fila += '<td>' + date +'</td>';
				fila += '<td>' + purchase_order_number + '</td>';
				fila += '<td>' + payment_order_number + '</td>';
				fila += '<td>' + status_names[status] + '</td>';
				fila += '<td>';
				fila += '<input type="hidden" name="data[PaymentSchedules][' + schedule_count + '][id]" value="' + id + '" />';
				fila += '<input type="hidden" name="data[PaymentSchedules][' + schedule_count + '][payment_order_id]" value="' + payment_order_id + '" />';
				fila += '<input type="hidden" name="data[PaymentSchedules][' + schedule_count + '][status]" value="' + status + '" />';
				fila += (status == 0)? '<input type="checkbox" name="data[PaymentSchedules][' + schedule_count + '][pay]" />' : '';
				fila += (status == 1 && ordenPagoId == payment_order_id)? '<input type="checkbox" name="data[PaymentSchedules][' + schedule_count + '][pay]" checked="checked" />' : '';
				fila += '</td>';
				fila += '</tr>';
				$('#datosCuotas').last().append( fila );
				schedule_count++;
			});
		}
	});
}

function inicializaCuotas(ordenPagoId)
{
	if( $('.ordenCompra').length ) {
		$('.ordenCompra').each(function() {
			var ordenCompraId = $(this).val();
			agregarCuotas(ordenCompraId, ordenPagoId);
		});
	}
}

function formatDate(date)
{
	var p = date.split(/[-]/);
	return p[2] + '-' + p[1] + '-' + p[0];
}

/* DOCUMENTO PAGO */
function existeDocumentoPagoEnLista( documentoPagoId )
{
	var existe = -1;
	if( $('.documentoPago').length ){
		$('.documentoPago').each(function(){
			var valorAtributo = $(this).attr('rel');
			if( documentoPagoId == valorAtributo ){
				existe = documentoPagoId;
			}
		});
		if( existe != -1 ){
			return true;
		}
	}
	return false;	
}

function agregarDocumentoPago( documentoPagoId, doc_number, tipo_doc, monto, emision, admision )
{
	if(!existeDocumentoPagoEnLista( documentoPagoId )){
		var doc_count = $('.documentoPago').length;
		var fila = '';
		fila += '<tr id="documentoPago_'+documentoPagoId+'" class="documentoPago" rel='+documentoPagoId+' >';
			fila += '<td>';
			fila += '<input type="hidden" name="data[PaymentDocuments][' + doc_count + '][id]" value="'+documentoPagoId+'">';
			fila += '<input type="hidden" name="data[PaymentDocuments][' + doc_count + '][amount]" id="valorMontoDocumentoPago_' + documentoPagoId + '" value="' + monto + '">';
			fila += '<input type="hidden" name="doc_type" id="doc_type_' + documentoPagoId + '" value="' + tipo_doc + '">';
			fila +=  tipo_doc
			fila += '</td>';
			fila += '<td>'+doc_number+'</td>';
			fila += '<td class="doc_amount">'+monto+'</td>';
			fila += '<td>'+emision+'</td>';
			fila += '<td>'+admision+'</td>';
			fila += '<td><img src="'+wroot+'img/icons/menos.png" class="eliminarDocumentoPago" rel="'+documentoPagoId+'" /></td>';
		fila += '</tr>';
		$('#documentoPagos').last().append( fila );
	}
}

function totalDocumentos() {
	var total = 0;
	$('.documentoPago').each(function(){
		var rel = $(this).attr("rel");
		doc_type = $('#doc_type_' + rel).val();
		var amount = parseInt( $('#valorMontoDocumentoPago_'+rel).val() );
		if(doc_type == 'NOTA DE CREDITO') total -= amount;
		else total += amount;
	});
	return total;
}

/* CREAR FILA PRESUPUESTO */
function crearFilaPresupuesto(num, cant){
	var fila = '';
	fila += '<tr id="pres_'+num+'" class="cantBudgetCount">';
	fila += '<td>';
	fila += '<input type="text" id="numero_budget_'+num+'" name="data[Budget]['+cant+'][number]" value="" size="13" maxlength="10" onkeypress="return permite(event, \'num\');" />';
	fila += '</td>';
	fila += '<td>';
	fila += '<input type="text" id="cod_budget_'+num+'" name="data[Budget]['+cant+'][code]" value="" size="13" maxlength="10" onkeypress="return permite(event, \'num\');" />';
	fila += '</td>';
	fila += '<td><img src="'+wroot+'img/icons/menos.png" class="eliminarTrDatosPresupuesto" rel="'+num+'" /></td>';
	fila += '</tr>';
	
	return fila;
}

/*CREAR FILA ITEM*/
function crearFilaItemAsig( num, cant ){
	var fila = '';
	fila += '<tr id="filaItemAsig_'+num+'" class="cantFilaItemAsig">';
	fila += '<input type="hidden" name="data[PaymentOrderDetail]['+cant+'][id]" value="" >';
	fila += '<td colspan="3">';
	fila += '<select id="subtitles_'+num+'" name="data[PaymentOrderDetail]['+cant+'][subtitle_id]" class="subtitles" rel="'+num+'">';
	fila += '</select>';
	fila += '<select id="items_'+num+'" name="data[PaymentOrderDetail]['+cant+'][item_id]" class="items" rel="'+num+'" style="display: none;"></select>';
	fila += '<select id="assignments_'+num+'" name="data[PaymentOrderDetail]['+cant+'][assignment_id]" class="assignments" rel="'+num+'" style="display: none;"></select>';
	fila += '</td>';
	fila += '<td>';
	fila += '<input class="paymentOrderMount" id="filaItemAsigValue_'+num+'" onkeyup="calculaTotal()" name="data[PaymentOrderDetail]['+cant+'][amount]" size="9" onkeypress="return permite(event, \'num\');" />';
	fila += '</td>';
	fila += '<td><img src="'+wroot+'img/icons/menos.png" class="eliminarFilaItemAsig" rel="'+num+'" /></td>';
	fila += '</tr>';
	
	return fila;
}


function permite(elEvento, permitidos){
	  // Variables que definen los caracteres permitidos
	  var numeros = "0123456789";
	  var caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  var caracteres_especiales = "\u00e1\u00e9\u00ed\u00f3\u00fa\u00c1\u00c9\u00cd\u00d3\u00da\u00f1\u00d1";
	  var numeros_caracteres = numeros + caracteres;
	  var teclas_especiales = [8, 9, 16, 37, 39];
	  // 8 = BackSpace, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha
	 
	 
	  // Seleccionar los caracteres a partir del parámetro de la función
	  switch(permitidos) {
	    case 'num':
	      permitidos = numeros;
	      break;
		case 'num_guion':
	      permitidos = numeros + '-';
	      break;
	    case 'car':
	      permitidos = caracteres + caracteres_especiales + ' ';
	      break;
	    case 'num_car':
	      permitidos = numeros_caracteres;
	      break;
		 case 'num_car_guion':
			permitidos = numeros + caracteres + caracteres_especiales + '-';
			break;
		 default:
		 	permitidos = permitidos;
	  }
	 
	  // Obtener la tecla pulsada 
	  var evento = elEvento || window.event;
	  var codigoCaracter = evento.charCode || evento.keyCode;
	  var caracter = String.fromCharCode(codigoCaracter);
	 
	  // Comprobar si la tecla pulsada es alguna de las teclas especiales
	  // (teclas de borrado y flechas horizontales)
	  var tecla_especial = false;
	  for(var i in teclas_especiales) {
	    if(codigoCaracter == teclas_especiales[i]) {
	      tecla_especial = true;
	      break;
	    }
	  }
	 
	  // Comprobar si la tecla pulsada se encuentra en los caracteres permitidos
	  // o si es una tecla especial
	  return permitidos.indexOf(caracter) != -1 || tecla_especial;
}

function dar_formato(num){  
	var cadena = "";var aux;  
	var cont = 1,m,k;  
	if(num<0) aux=1; else aux=0;  
	num=num.toString();  
	for(m=num.length-1; m>=0; m--){  
	 cadena = num.charAt(m) + cadena;  
	  	 if(cont%3 == 0 && m >aux)  cadena = "." + cadena; else cadena = cadena;  
	  	 if(cont== 3) cont = 1; else cont++;  
	  	}  
	  
	cadena = cadena.replace(/.,/,",");  
	  
	return cadena;  
	  
}

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function num2letras(objTextNum, objTextLetras){
	var n;
	if($(objTextNum).val() == "")
		n = 0;
	else
		n = String($(objTextNum).val() );
	uni = new Array("","UN","DOS","TRES","CUATRO","CINCO","SEIS","SIETE","OCHO","NUEVE","DIEZ","ONCE","DOCE","TRECE","CATORCE","QUINCE");
	dec = new Array("","","","TREINTA","CUARENTA","CINCUENTA","SESENTA","SETENTA","OCHENTA","NOVENTA");

	if(n > 0){
	largo = n.length;

	var Num = "                  ";
	var a = Num.substr(0,18-largo);
	a = a + n;
	largo = a.length;
	var i = 1;
	var z = "";
	while (i) {
	var k = a.substr(17 - (i * 3 - 1), 3);
	if (k == "   "){
	break;
	}
	var c = Number(k.substr(0,1));
	var d = Number(k.substr(1,1));
	var u = Number(k.substr(2,1));
	var v = Number(k.substr(1,2));

	 if (i > 1){
	      if (((i == 2) || (i == 4)) && (k > 0)){
	         z = " MIL " + z;
	      }
	      if ((i == 3) && (a.substr(6,6) > 0)){
	         if (k == 1){
	            z = " MILLON " + z
	         }else{
	            z = " MILLONES " + z
	         }
	      }
	      if ((i == 5) && (k > 0)){
	         if (k == 1) {
	            z = " BILLON " + z
	         }else{
	            z = " BILLONES " + z
	         }
	      }
	   }

	   if (v > 0) {
			if ((v > 0) && (v <= 15)){
		        z = uni[v] + z;
			}else if ((v >= 16) && (v <= 19)){
			    z = " DIECI" + uni[u] + z;
			}else if (v == 20){			
			    z = " VEINTE " + z;
			}else if ((v >= 21) && (v <= 29)){
			    z = " VEINTI" + uni[u] + z;		
			}else{
	              if (u == 0) {
	                z = dec[d] + z
	              }else{
	                z = dec[d] + " Y " + uni[u] + z
	              }		
			
			}		
	    }

	   if (c > 0){
	      if (c == 1){
	         if (v == 0){
	            z = " CIEN " + z
	         }else{
	            z = " CIENTO " + z
	         }
	      }
	      if ((c == 2) || (c == 3) || (c == 4) || (c == 6) || (c == 8)) {
	         z = uni[c] + "CIENTOS " + z
	      }
	      if (c == 5) {
	         z = " QUINIENTOS " + z
	      }
	      if (c == 7) {
	         z = " SETECIENTOS " + z
	      }
	      if (c == 9) {
	         z = " NOVECIENTOS " + z
	      }
	   }
	i++;
	}
		var xj = trim(z.toUpperCase());
		$(objTextLetras).val( xj );
	}else
		$(objTextLetras).val();
}

function calculaTotal()
{
	var cantItems = $('.cantFilaItemAsig').size();
	var suma = 0;
	var valorActual = 0;
	var resultado = 0;
	
		if( cantItems > 0 ){	
			for( var i = 0 ; i < cantItems; i++ ){				
				if( $('#filaItemAsigValue_'+i).val() != undefined ){
					if( $('#filaItemAsigValue_'+i).val() != '' ){
						suma = suma + parseInt( $('#filaItemAsigValue_'+i).val() );
					}						
				}
			}
		}
		else{
			if( $('#filaItemAsigValue_0').val() != undefined ){
				if( $('#filaItemAsigValue_0').val() != '' ){
					suma = suma + parseInt( $('#filaItemAsigValue_0').val() );
				}						
			}			
		}


		resultado = suma;
		resultado = parseInt( resultado, 10 );
		
	$('#PaymentOrderAmount').val( resultado );
	num2letras( '#PaymentOrderAmount' , '#PaymentOrderLaCantidadDe' );
}


function isEmpty(obj) {
	  for(var i in obj) {return false;}
	  return true;
}

function colocarFormatoRUT(entrada){    
	var tmpstr = '';
	var texto = entrada;
  
	if(texto.length == 0){
		return;	//false
	}
  
  for(i = 0; i < texto.length; i++)  
    if( texto.charAt(i) != ' ' && texto.charAt(i) != '.' && texto.charAt(i) != '-' )
        tmpstr = tmpstr + texto.charAt(i);    
    texto = tmpstr;    
    largo = texto.length;    

	 if(largo == 0 || largo == 1){
        alert("R.U.T. incompleto.");
  		  entrada = "";
        return;	//false
    }    

    for (i = 0; i < largo; i++){            
        if ( texto.charAt(i) != "0" && texto.charAt(i) != "1" && texto.charAt(i) !="2" && texto.charAt(i) != "3" && texto.charAt(i) != "4" && texto.charAt(i) != "5" && texto.charAt(i) != "6" && texto.charAt(i) != "7" && texto.charAt(i) != "8" && texto.charAt(i) != "9" && texto.charAt(i) != "k" && texto.charAt(i) != "K" ){            
            alert("El valor ingresado no corresponde a un R.U.T válido");                 
            return;	//false
        }    
    }    

	var invertido = "";    
	for(i = (largo-1), j = 0; i >= 0; i--, j++)        
		invertido = invertido + texto.charAt(i);
	
	var dtexto = "";    
	dtexto = dtexto + invertido.charAt(0);
	dtexto = dtexto + '-';
	cnt = 0;
	for(i = 1, j = 2; i < largo; i++, j++){
		if(cnt == 3){
			dtexto = dtexto + '.';
			j++;
			dtexto = dtexto + invertido.charAt(i);
			cnt = 1;
		}else{
		  dtexto = dtexto + invertido.charAt(i);
		  cnt++;
		}
	}

    invertido = "";
    for (i = (dtexto.length-1), j = 0; i >= 0; i--, j++ )        
        invertido = invertido + dtexto.charAt(i);    

    entrada = invertido.toUpperCase();
}

function compare_dates(fecha, fecha2)  
  {  
    var xMonth=fecha.substring(3, 5);  
    var xDay=fecha.substring(0, 2);  
    var xYear=fecha.substring(6,10);  
    var yMonth=fecha2.substring(3, 5);  
    var yDay=fecha2.substring(0, 2);  
    var yYear=fecha2.substring(6,10);  
    if (xYear> yYear)  
    {  
        return(true)  
    }  
    else  
    {  
      if (xYear == yYear)  
      {   
        if (xMonth> yMonth)  
        {  
            return(true)  
        }  
        else  
        {   
          if (xMonth == yMonth)  
          {  
            if (xDay> yDay)  
              return(true);  
            else  
              return(false);  
          }  
          else  
            return(false);  
        }  
      }  
      else  
        return(false);  
    }  
}
