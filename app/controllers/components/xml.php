<?php
	class XmlComponent extends Object {
		var $controller;
		var $components = array('LdapAuth');
		
		function processPurchaseOrder($file = null, $description = null) {
			
			$data = array();
			
			$input_file = new File(WWW_ROOT . 'files'.DS.'orden_compra.xml', true);
			$input = $input_file->read();
			$input_file->close();
			
			App::import('Xml');
			$xml = new Xml($input);
			$xmlAsArray = $xml->toArray();
			
			//ingreso en el arbol
			$xmlAsArray = $xmlAsArray['OrdersResults']['OrdersList']['Order'];

			//importo modelos
			//ordenes de compra
			App::import('Model','PurchaseOrder');
			$this->PurchaseOrder = new PurchaseOrder();
			//detalles orden de compra
			App::import('Model','PurchaseOrderDetail');
			$this->PurchaseOrderDetail = new PurchaseOrderDetail();
			//proveedor
			App::import('Model','Provider');
			$this->Provider = new Provider();
			
			
			/** PROVEEDOR **/
			$data['Provider'] = array(
				'rut' => $xmlAsArray['OrderHeader']['OrderParty']['SellerParty']['PartyID']['Ident'],
				'socialreason' => $xmlAsArray['OrderHeader']['OrderParty']['SellerParty']['NameAddress']['Name1'],
				'fantasyname' => $xmlAsArray['OrderHeader']['OrderParty']['SellerParty']['NameAddress']['Name1'],
                                'address'=>$xmlAsArray['OrderHeader']['OrderParty']['SellerParty']['NameAddress']['District'] . '-' . $xmlAsArray['OrderHeader']['OrderParty']['SellerParty']['NameAddress']['City'],
                                'contact_phone'=>$xmlAsArray['OrderHeader']['OrderParty']['SellerParty']['PrimaryContact']['ListOfContactNumber']['ContactNumber'][1]['ContactNumberValue'],
                                'contact_email'=>$xmlAsArray['OrderHeader']['OrderParty']['SellerParty']['PrimaryContact']['ListOfContactNumber']['ContactNumber'][0]['ContactNumberValue'],
                                'contact_name'=>$xmlAsArray['OrderHeader']['OrderParty']['SellerParty']['PrimaryContact']['ContactName'],
				'is_active' => 1,
                                'is_ses' => $this->LdapAuth->user('is_ses')
			);
                        
			//rut de proveedor sin puntos ni guión
//			$data['Provider']['rut'] = str_replace( array('.', '-'), array('', ''), $data['Provider']['rut'] );
			$data['Provider']['rut']=$data['Provider']['rut'];
			//busco el proveedor
			$provider_id = 0;
			$provider = $this->Provider->find('first', array('recursive' => -1, 'conditions' => array( 'Provider.rut' => $data['Provider']['rut'] ) ) );
			//si no lo encuentro lo guardo y obtengo su nueva id
			if( empty ($provider) ) {
				$this->Provider->save( $data['Provider'] );
				$provider_id = $this->Provider->id;
			} else { //si lo encuentro obtengo su id
				$provider_id = $provider['Provider']['id'];
			}
			
			
			/** ORDEN DE COMPRA **/
			$data['PurchaseOrder'] = array(
				'name' => $xmlAsArray['OrderHeader']['OrderReferences']['QuoteReference']['RefNum']['value'],
				'description' => $xmlAsArray['OrderHeader']['OrderReferences']['OtherOrderReferences']['ReferenceCoded']['ReferenceDescription'],
				'user_id' => $this->LdapAuth->user('id'),
				'provider_id' => $provider_id,
				'date' => $xmlAsArray['OrderHeader']['OrderDates']['PromiseDate'],
				'order_number' => $xmlAsArray['OrderHeader']['OrderNumber']['BuyerOrderNumber'],
				'currency' => $xmlAsArray['OrderHeader']['OrderCurrency']['CurrencyCoded']['value'],
				'status' => 1,//enviada
                                'is_ses' => $this->LdapAuth->user('is_ses'),
			);
			
			//guardo orden de compra solo si no existe una con el mismo número o existe una anulada con el mismo número
			$purchase_order = $this->PurchaseOrder->find('first', array('recursive' => -1, 'conditions' => array( 'PurchaseOrder.order_number' => $data['PurchaseOrder']['order_number'] ,'PurchaseOrder.provider_id'=>$provider_id ), 'order' => 'PurchaseOrder.created DESC' ) );
			$message = array('text' => '', 'class' => '');
			//si no existe una orden con el mismo número guardo
			if( empty ($purchase_order) || ( !empty ($purchase_order) && $purchase_order['PurchaseOrder']['status'] == 3 ) ) {
				$this->PurchaseOrder->save( $data['PurchaseOrder'] );
				$purchase_order_id = $this->PurchaseOrder->id;
				$message = array('text' => 'Orden de compra agregada.', 'class' => 'success');
			} else { //si existe obtengo su id
				$purchase_order_id = $purchase_order['PurchaseOrder']['id'];
				$message = array('text' => 'Orden de compra duplicada, no fue agregada.', 'class' => 'error');
			}
			
			/** DETALLES ORDEN DE COMPRA **/
			//parchar el caso en que es solo un detalle, el parseo del xml no le asigna un index al detalle
			//y deja sus datos directamente en $xmlAsArray['OrderDetail']['ListOfItemDetail']['ItemDetail']
			if( !isset( $xmlAsArray['OrderDetail']['ListOfItemDetail']['ItemDetail'][0] ) ) {
				$tmp = $xmlAsArray['OrderDetail']['ListOfItemDetail']['ItemDetail'];
				unset($xmlAsArray['OrderDetail']['ListOfItemDetail']['ItemDetail']);
				$xmlAsArray['OrderDetail']['ListOfItemDetail']['ItemDetail'][0] = $tmp;
			}
//                        if(isset($xmlAsArray['OrderSummary']['AllowOrChargeSummary']['TotalAllowOrCharge']['SummaryAllowOrCharge']['MonetaryAmount'])){
//                            $dctoTotal = (int) $xmlAsArray['OrderSummary']['AllowOrChargeSummary']['TotalAllowOrCharge']['SummaryAllowOrCharge']['MonetaryAmount'];
//                        }else{
//                            $dctoTotal = 0;
//                        }
//			$dctoTotal = $xmlAsArray['OrderSummary']['AllowOrChargeSummary']['TotalAllowOrCharge']['SummaryAllowOrCharge']['MonetaryAmount'] * -1;
//                        $neto =  $xmlAsArray['OrderSummary']['OrderSubTotal']['MonetaryAmount']['value'];
//                        $subTotal = $neto - $dctoTotal;
//                        $total =  $xmlAsArray['OrderSummary']['OrderTotal']['MonetaryAmount']['value'];
//                        $iva_amount = $total - $subTotal;
//                        $iva_porc = round(($iva_amount * 100) / $subTotal);
//                        $dcto_porc = round(($dctoTotal * 100) / $neto);
//                        $iva_porc = ($iva_porc / 100) + 1;
//                        $dcto_porc = 1 - ($dcto_porc / 100);
//                        $msg = array(
//                            'Dcto total' => $dctoTotal,
//                            'neto' => $neto,
//                            'Sub Total' => $subTotal,
//                            'Total' => $total,
//                            'iva_amount' => $iva_amount,
//                            'iva_porc' => $iva_porc,
//                            'dcto _porc' => $dcto_porc
//                        );
			foreach( $xmlAsArray['OrderDetail']['ListOfItemDetail']['ItemDetail'] as $detail ) {
				$description = isset($detail['BaseItemDetail']['ItemIdentifiers']['ItemDescription']['value'])? $detail['BaseItemDetail']['ItemIdentifiers']['ItemDescription']['value'] : 'Sin descripción';
				$amount = $amount_trans = isset($detail['BaseItemDetail']['TotalQuantity']['QuantityValue']['value'])? $detail['BaseItemDetail']['TotalQuantity']['QuantityValue']['value'] : 0;
				$currency = isset($detail['PricingDetail']['LineItemTotal']['Currency']['CurrencyCoded'])? $detail['PricingDetail']['LineItemTotal']['Currency']['CurrencyCoded'] : '';
				$price =  isset($detail['PricingDetail']['LineItemSubTotal']['MonetaryAmount'], $detail['BaseItemDetail']['TotalQuantity']['QuantityValue']['value'])? $detail['PricingDetail']['LineItemSubTotal']['MonetaryAmount'] / $detail['BaseItemDetail']['TotalQuantity']['QuantityValue']['value'] : 0;
				$value =  isset($detail['PricingDetail']['LineItemTotal']['MonetaryAmount'])? $detail['PricingDetail']['LineItemTotal']['MonetaryAmount'] : 0;
                                
				$data['PurchaseOrderDetail'][] = array(
					'purchase_order_id' => $purchase_order_id,
					'description' => $description,
					'amount' => $amount,
					'amount_trans' => $amount_trans,
					'currency' => $currency,
					'price' => $price,
					'value' => $value,
				);
			}
			
			//si la orden no existia, guardo sus detalles
			if( empty ($purchase_order) || ( !empty ($purchase_order) && $purchase_order['PurchaseOrder']['status'] == 3 ) ) {
				$this->PurchaseOrderDetail->saveAll( $data['PurchaseOrderDetail'] );
			}
			
			return $message;
		}
		
		/**
		 * Convierte una fecha en formato normal 16/08/2011 a formato sql 2011/08/16
		 * 
		 * @param string $date la fecha a transformar
		 * @param bool $time si el tiempo (horas, minutos, segundos) será utilizado
		 * @param bool $return si la fecha resultante es retornada
		 * @return string la fecha convertida a formato sql
		 */
		function dateToSql(&$date, $time = true, $return = false) {
			$date_str = 'Y-m-d';
			if($time) $date_str .= ' H:i:s';

			$date = explode('/', $date);
			$date = $date[2] . '-' . $date[1] . '-' . $date[0];
			$date = date($date_str, strtotime($date));
			if($return) return $date;
		}
		
	}
?>
