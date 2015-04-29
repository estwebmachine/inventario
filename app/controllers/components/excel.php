<?php

class ExcelComponent extends Object {

    var $controller;

    function import_users($filename) {
        $start = (float) array_sum(explode(' ', microtime())); //registro de tiempo de ejecución

        ini_set('memory_limit', '-1');
        //para no obtener error "maximum execution time of 30 seconds exceeded"
        set_time_limit(0);
        error_reporting(E_ALL);

        $save = true;

        //datos
        $data = array();
        //debug
        $debug = array();

        //importo archivo excel
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($extension == 'xls') {
            App::import('Vendor', 'PHPExcelReader', array('file' => 'PHPExcel/Reader/Excel5.php'));
            $objReader = new PHPExcel_Reader_Excel5('Excel5');
        } else if ($extension == 'xlsx') {
            App::import('Vendor', 'PHPExcelReader', array('file' => 'PHPExcel/Reader/Excel2007.php'));
            $objReader = new PHPExcel_Reader_Excel2007('Excel2007');
        }
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load(WWW_ROOT . 'files' . DS . $filename);

        //importo modelos
        //user
        App::import('Model', 'User');
        $this->User = new User();

        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

        for ($row = 1; $row <= $highestRow; $row++) {
            $name = trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue());
            $username = trim($objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
            $email = trim($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());

            $user_data = array(
                'User' => array(
                    'name' => $name,
                    'username' => strtolower($username),
                    'email' => $email,
                    'role' => 10,
                    'status' => 0,
                    'password' => '*'
                )
            );
            $debug[] = $user_data;
            
            $this->User->create();
            $this->User->save($user_data);
        }
        
        print_r($debug);
        $end = (float) array_sum(explode(' ', microtime())); //registro de tiempo de ejecución
        echo "Tiempo de ejecucion: " . sprintf("%.4f", ($end - $start)) . " segundos";
    }

    function processInventory($filename) {
        $start = (float) array_sum(explode(' ', microtime())); //registro de tiempo de ejecución

        ini_set('memory_limit', '-1');
        //para no obtener error "maximum execution time of 30 seconds exceeded"
        set_time_limit(0);
        error_reporting(E_ALL);

        $save = true;

        //datos
        $data = array();
        //debug
        $debug = array();

        //importo archivo excel
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel.php'));
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($extension == 'xls') {
            App::import('Vendor', 'PHPExcelReader', array('file' => 'PHPExcel/Reader/Excel5.php'));
            $objReader = new PHPExcel_Reader_Excel5('Excel5');
        } else if ($extension == 'xlsx') {
            App::import('Vendor', 'PHPExcelReader', array('file' => 'PHPExcel/Reader/Excel2007.php'));
            $objReader = new PHPExcel_Reader_Excel2007('Excel2007');
        }
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load(WWW_ROOT . 'files' . DS . $filename);

        //importo modelos
        //group
        App::import('Model', 'Group');
        $this->Group = new Group();
        //family
        App::import('Model', 'Family');
        $this->Family = new Family();
        //asset
        App::import('Model', 'Asset');
        $this->Asset = new Asset();
        //provider
        App::import('Model', 'Provider');
        $this->Provider = new Provider();
        //purchaseorder
        App::import('Model', 'PurchaseOrder');
        $this->PurchaseOrder = new PurchaseOrder();
        //purchaseorderdetail
        App::import('Model', 'PurchaseOrderDetail');
        $this->PurchaseOrderDetail = new PurchaseOrderDetail();
        //inventoryasset
        App::import('Model', 'InventoryAsset');
        $this->InventoryAsset = new InventoryAsset();
        //inventoryassethistory
        App::import('Model', 'InventoryAssetHistory');
        $this->InventoryAssetHistory = new InventoryAssetHistory();
        //inventoryassetdisposal
        App::import('Model', 'InventoryAssetDisposal');
        $this->InventoryAssetDisposal = new InventoryAssetDisposal();

        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

        $i = 2;
        while ($i <= $highestRow) {
            $nombre_grupo = trim($objWorksheet->getCellByColumnAndRow(0, $i)->getValue());
            $nombre_familia = trim($objWorksheet->getCellByColumnAndRow(1, $i)->getValue());
            $nombre_bien = trim($objWorksheet->getCellByColumnAndRow(2, $i)->getValue());
            $inventory_asset_desc = trim($objWorksheet->getCellByColumnAndRow(3, $i)->getValue());
            $provider_rut = trim($objWorksheet->getCellByColumnAndRow(4, $i)->getValue());
            $provider_socialreason = trim($objWorksheet->getCellByColumnAndRow(5, $i)->getValue());
            $purchase_order_number = trim($objWorksheet->getCellByColumnAndRow(6, $i)->getValue());
            $purchase_order_date = $this->_dateConv(trim($objWorksheet->getCellByColumnAndRow(7, $i)->getValue())); //covertir fecha a SQL desde 18-08-06
            $inventory_asset_price = trim($objWorksheet->getCellByColumnAndRow(11, $i)->getValue());
            //$inventory_asset_code = trim($objWorksheet->getCellByColumnAndRow(13, $i)->getValue());
            $inventory_asset_code = trim($objWorksheet->getCellByColumnAndRow(27, $i)->getValue());
            $inventory_asset_code = str_replace(array("'"), "", $inventory_asset_code);

            $inventory_asset_serie = trim($objWorksheet->getCellByColumnAndRow(16, $i)->getValue());
            $inventory_asset_status = trim($objWorksheet->getCellByColumnAndRow(19, $i)->getValue());
            $inventory_asset_ing_date = $this->_dateConv(trim($objWorksheet->getCellByColumnAndRow(21, $i)->getValue())); //covertir fecha a SQL desde 18-08-06
            $inventory_asset_estado_date = $this->_dateConv(trim($objWorksheet->getCellByColumnAndRow(23, $i)->getValue())); //covertir fecha a SQL desde 18-08-06

            /*             * *** GRUPO **** */
            //busco grupo por nombre, si no existe lo guardo
            $group_id = 0;
            $group = $this->Group->find('first', array('conditions' => array('Group.name' => $nombre_grupo), 'recursive' => -1));
            if (empty($group)) {
                if ($save) {
                    $this->Group->create();
                    $this->Group->save(array('Group' => array('name' => $nombre_grupo, 'isinventory' => 1)));
                    $group_id = $this->Group->id;
                }
            } else {
                $group_id = $group['Group']['id'];
            }

            /*             * *** FAMILIA **** */
            //busco familia por nombre, si no existe la guardo
            $family_id = 0;
            $family = $this->Family->find('first', array('conditions' => array('Family.name' => $nombre_familia), 'recursive' => -1));
            if (empty($family)) {
                if ($save) {
                    $this->Family->create();
                    $this->Family->save(array('Family' => array('name' => $nombre_familia, 'group_id' => $group_id, 'isinventory' => 1)));
                    $family_id = $this->Family->id;
                }
            } else {
                $family_id = $family['Family']['id'];
            }

            /*             * *** BIEN **** */
            //busco bien por nombre, si no existe lo guardo
            $asset_id = 0;
            $asset = $this->Asset->find('first', array('conditions' => array('Asset.name' => $nombre_bien), 'recursive' => -1));
            if (empty($asset)) {
                if ($save) {
                    $this->Asset->create();
                    $this->Asset->save(array('Asset' => array('name' => $nombre_bien, 'group_id' => $group_id, 'family_id' => $family_id, 'is_inventory' => 1)));
                    $asset_id = $this->Asset->id;
                }
            } else {
                $asset_id = $asset['Asset']['id'];
            }

            /*             * *** PROVEEDOR **** */
            //busco proveedor por rut, si no existe lo guardo
            $provider_id = 0;
            $provider = $this->Provider->find('first', array('conditions' => array('Provider.rut' => $provider_rut), 'recursive' => -1));
            if (empty($provider)) {
                if ($save) {
                    $this->Provider->create();
                    $this->Provider->save(array('Provider' => array('rut' => $provider_rut, 'socialreason' => $provider_socialreason, 'fantasyname' => $provider_socialreason, 'region_id' => 19, 'city_id' => 5, 'commune_id' => 7, 'is_active' => 1)));
                    $provider_id = $this->Provider->id;
                }
            } else {
                $provider_id = $provider['Provider']['id'];
            }

            /*             * *** ORDEN DE COMPRA **** */
            //busco orden de compra por numero, si no existe la guardo
            $purchase_order_id = 0;
            $purchase_order = $this->PurchaseOrder->find('first', array('conditions' => array('PurchaseOrder.order_number' => $purchase_order_number), 'recursive' => -1));
            if (empty($purchase_order)) {
                if ($save) {
                    $this->PurchaseOrder->create();
                    $this->PurchaseOrder->save(array('PurchaseOrder' => array('destination' => 1, 'name' => 'Sin Nombre', 'description' => 'Sin Descripcion', 'provider_id' => $provider_id, 'date' => $purchase_order_date, 'order_number' => $purchase_order_number, 'currency' => 'CLP', 'status' => 2, 'payment_status' => 1)));
                    $purchase_order_id = $this->PurchaseOrder->id;
                }
            } else {
                $purchase_order_id = $purchase_order['PurchaseOrder']['id'];
            }

            /*             * *** DETALLE ORDEN DE COMPRA **** */
            //busco detalle orden de compra por id padre orden de compra e id de producto, si no existe la guardo, si existe le aumento en 1 la cantidad al detalle
            $purchase_order_detail_id = 0;
            $purchase_order_detail = $this->PurchaseOrderDetail->find('first', array('conditions' => array('PurchaseOrderDetail.purchase_order_id' => $purchase_order_id, 'PurchaseOrderDetail.asset_id' => $asset_id), 'recursive' => -1));
            if (empty($purchase_order_detail)) {
                if ($save) {
                    $this->PurchaseOrderDetail->create();
                    $this->PurchaseOrderDetail->save(array('PurchaseOrderDetail' => array('destination' => 1, 'asset_id' => $asset_id, 'purchase_order_id' => $purchase_order_id, 'name' => $nombre_bien, 'description' => $inventory_asset_desc, 'amount' => 1, 'amount_trans' => 1, 'currency' => 'CLP', 'price' => $inventory_asset_price, 'value' => $inventory_asset_price)));
                    $purchase_order_detail_id = $this->PurchaseOrderDetail->id;
                }
            } else { //ya existe un detalle en esta orden de compra para este bien, lo actualizo aumentando en 1 la cantidad y corrigiendo el valor total
                $purchase_order_detail_id = $purchase_order_detail['PurchaseOrderDetail']['id'];
                $amount = $purchase_order_detail['PurchaseOrderDetail']['amount'];
                $amount++;
                $value = $inventory_asset_price * $amount;
                $this->PurchaseOrderDetail->save(array('PurchaseOrderDetail' => array('id' => $purchase_order_detail_id, 'amount' => $amount, 'amount_trans' => $amount, 'value' => $value)));
            }

            /*             * *** BIEN INVENTARIO **** */
            //busco bien inventario por codigo, si no existe lo guardo
            $inventory_asset_id = 0;
            $inventory_asset = $this->InventoryAsset->find('first', array('conditions' => array('InventoryAsset.code' => $inventory_asset_code), 'recursive' => -1));
            if (empty($inventory_asset)) {
                $inventory_asset_status = ($inventory_asset_status == 'BSenajenacion') ? 2 : 0;

                if ($save) {
                    $this->InventoryAsset->create();
                    $this->InventoryAsset->save(array('InventoryAsset' => array('asset_id' => $asset_id, 'purchase_order_id' => $purchase_order_id, 'code' => $inventory_asset_code, 'status' => $inventory_asset_status, 'original_price' => $inventory_asset_price, 'description' => $inventory_asset_desc, 'created' => $inventory_asset_ing_date, 'modified' => $inventory_asset_estado_date)));
                    $Inventory_asset_id = $this->InventoryAsset->id;
                }

                /*                 * *** HISTORIAL BIEN INVENTARIO **** */
                //creo historial de inventario para el bien inventario recien creado
                //ingreso
                if ($save) {
                    $this->InventoryAssetHistory->create();
                    $this->InventoryAssetHistory->save(array('InventoryAssetHistory' => array('inventory_asset_id' => $Inventory_asset_id, 'type' => 0, 'comment' => 'Ingreso de bien a inventario', 'created' => $inventory_asset_ing_date, 'modified' => $inventory_asset_ing_date)));
                }
                //baja, si corresponde
                if ($inventory_asset_status == 2) {
                    if ($save) {
                        //Almaceno InventoryAssetDisposal
                        $this->InventoryAssetDisposal->create();
                        $this->InventoryAssetDisposal->save(array('InventoryAssetDisposal' => array('inventory_asset_id' => $Inventory_asset_id, 'type' => 0, 'comment' => 'Baja de bien', 'created' => $inventory_asset_estado_date, 'modified' => $inventory_asset_estado_date, 'resolution_date' => $inventory_asset_estado_date, 'resolution_number' => 'S/N')));
                        $inventory_asset_disposal_id = $this->InventoryAssetDisposal->id;

                        $this->InventoryAssetHistory->create();
                        $this->InventoryAssetHistory->save(array('InventoryAssetHistory' => array('inventory_asset_id' => $Inventory_asset_id, 'type' => 2, 'comment' => 'Baja de bien', 'created' => $inventory_asset_estado_date, 'modified' => $inventory_asset_estado_date, 'inventory_asset_disposal_id' => $inventory_asset_disposal_id)));
                    }
                }
            } else {
                $inventory_asset_id = $inventory_asset['InventoryAsset']['id'];
            }

            $debug[] = compact('nombre_grupo', 'nombre_familia', 'nombre_bien', 'inventory_asset_code', 'purchase_order_date', 'inventory_asset_ing_date', 'inventory_asset_estado_date');

            $i += 1;
        }

        //printf('<pre>%s</pre>', print_r($debug, true));
        $end = (float) array_sum(explode(' ', microtime())); //registro de tiempo de ejecución
        echo "Tiempo de ejecucion: " . sprintf("%.4f", ($end - $start)) . " segundos";
    }

    /**
     * Convierte una fecha a formato SQL desde formato 18-08-06 (dia, mes, año)
     * 
     * @param string $date la fecha a convertir, ejemplo: 18-08-06
     * @return string la fecha convertida, ejemplo: 2006-08-18
     */
    function _dateConv($date) {
        //$date = explode('-', $date);
        //return sprintf('20%s-%s-%s', $date[2], $date[1], $date[0]);
        $date = PHPExcel_Shared_Date::ExcelToPHP($date);
        return gmdate('Y-m-d', $date);
    }

}

?>