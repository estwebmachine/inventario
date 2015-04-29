<?php

/* SVN FILE: $Id: subdere_config.php 542 2013-10-17 21:36:35Z javier.jara $ */
/**
 * Configuracion mds
 *
 * Este archivo contiene las configuraciones del sitio
 *
 * PHP versión 5
 *
 * WebMachine, Desarrollo Web <http://www.webmachine.cl/>
 * Copyright 2010-2012, WebMachine Ltda.
 * Dominica N° 165 - Recoleta
 * Santiago, Chile
 *
 * @filesource
 * @copyright    Copyright 2010-2012, WebMachine Ltda.
 * @link         http://www.webmachine.cl WebMachine
 * @package      mds_inventario
 * @subpackage   mds_inventario.app.config
 * @version      $Revision: 542 $
 * @modifiedby   $LastChangedBy: javier.jara $
 * @lastmodified $Date: 2013-10-17 18:36:35 -0300 (jue 17 de oct de 2013) $
 */
//app
$config['App'] = array(
    'version' => array('release' => 1, 'build' => 1),
    'default_theme' => 'Smoothness',
    'environment'=>0,//0:Desarrollo,1:Calidad,2:Produccion
    'aid'=>'37zrzVQeEbiFxvZZO5HPSA=='
);

$config['Soap']=array(
    'user'=>'consultor2',
    'pass'=>'hcchcc98',
    'wsdl'=>array(
            0=>array(//Desarrollo
                'PA'=>'http://yelchovmdev.mideplan.cl:8000/sap/bc/srt/wsdl/bndg_AAF3B0521551EE09E1000000C0A81F28/wsdl11/allinone/ws_policy/document?sap-client=100',
                'OM'=>'http://yelchovmdev.mideplan.cl:8000/sap/bc/srt/wsdl/bndg_111EB0520251F409E1000000C0A81F28/wsdl11/allinone/ws_policy/document?sap-client=100'
            ),
            1=>array(//Calidad
                'PA'=>'http://yelchovmqa.mideplan.cl:8000/sap/bc/srt/wsdl/bndg_0CD2AA5221132F66E1000000C0A81F29/wsdl11/allinone/ws_policy/document?sap-client=300',
                'OM'=>'http://yelchovmqa.mideplan.cl:8000/sap/bc/srt/wsdl/bndg_8FD2AA5221132F66E1000000C0A81F29/wsdl11/allinone/ws_policy/document?sap-client=300'
            ),
            2=>array(//Produccion
                'PA'=>'',
                'OM'=>''
            )
        )
);

$config['SSO']=array(
    0=>'http://sso.mideplan.cl',//Desarrollo
    1=>'http://sso.mds.cl',//QA
    2=>'https://sso.ministeriodesarrollosocial.gob.cl'//Produccion
);

//roles
$config['User']['roles'] = array(
    0 => __('Administrador de Sistema', true),
    1 => __('Ejecutivo', true),
    2 => __('Jefe', true),
    3 => __('Contabilidad',true),
    4 => __('Funcionario', true)
);

//permisos
$config['User']['perms'] = array(
    0 => '*', //Administrador de Sistema
    1 => array(
        'ajax' => '*',
        'assets' => array('search'),
        'purchase_orders' => '*',
        'purchase_order_details' => '*',
        'transactions' => '*',
        'transaction_details' => '*',
        'users' => array('dashboard', 'themechange'),
        'inventory_assets' => array('enter'),
        'providers' => '*'
    ),
    2 => array(),
    3 => array(
        'ajax' => '*',
        'assets' => array('search'),
        'users' => array('dashboard', 'themechange'),
        'inventory_assets' => array('index', 'indextable', 'indexedit'),
        'ipcs' => '*',
        'reports'=>array('generate','conta_report')
    ),
    4 => array()
);

//menu
$config['User']['menu'] = array(
    __('Compras', true) => array(//carga de ordenes de compra
        __('Ordenes de Compra y Otros', true) => array('controller' => 'purchase_orders', 'action' => 'index')
    ),
    __('Control de Bienes', true) => array(//sistema inventario
        __('Alta de Bienes', true) => array('controller' => 'inventory_assets', 'action' => 'enter'),
        __('Asignación de Bienes', true) => array('controller' => 'inventory_assets', 'action' => 'release'),
        __('Actas de Entrega', true) => array('controller' => 'actas', 'action' => 'index'),
        __('Actas de Devolución', true) => array('controller' => 'actas', 'action' => 'returns'),
	__('Baja de Bienes', true) => array('controller' => 'inventory_assets', 'action' => 'terminate')
    ),
    __('Informes y Consultas', true) => array(
        __('Hoja Mural', true) => array('controller' => 'reports', 'action' => 'leaf_mural'),
        __('Informe Total de Bienes del Sistema', true) => array('controller' => 'reports', 'action' => 'all_data'),
	__('Informe de Altas y Bajas Inventario', true) => array('controller' => 'reports', 'action' => 'inventory_movements'),
	__('Bitácora', true) => array('controller' => 'reports', 'action' => 'bitacora'),
	__('Informe de Contabilidad', true) => array('controller' => 'reports', 'action' => 'conta_report'),
    ),
    __('Parametrización', true) => array(
        __('Mantenedor Centros de Costo', true) => array('controller' => 'cost_centers', 'action' => 'index'),
        __('Mantenedor Proveedores', true) => array('controller' => 'providers', 'action' => 'index'),
        __('Maestra Bienes de Inventario', true) => array('controller' => 'assets', 'action' => 'define'),
        __('Mantenedor Estructura de Inventario', true) => array('controller' => 'assets', 'action' => 'structure'),
        __('Mantenedor Ubicaciones Inventario', true) => array('controller' => 'inventory_assets', 'action' => 'locations'),
        __('Mantenedor Bienes', true) => array('controller' => 'inventory_assets', 'action' => 'index'),
        __('Mantenedor IPC', true) => array('controller' => 'ipcs', 'action' => 'index'),	
    ),
    __('Usuarios', true) => array(
        __('Definir Usuarios', true) => array('controller' => 'users', 'action' => 'index')
    ),
    __('Cambiar Contraseña', true) => array(
        __('Cambiar Contraseña', true) => array('controller' => 'users', 'action' => 'passchange')
    )
);

$config['is_active'] = array(
    __('Deshabilitado', true),
    __('Habilitado', true)
);
//Centros de Costos
$config['level'] = array(
    1 => __('Sección', true),
    2 => __('Departamento', true),
    3 => __('Unidad', true)
);

$config['Acta']['type']=array(
    __('Devolución',true),
    __('Entrega',true)
);

$config['Acta']['status']=array(
    __('Nula',true),
    __('Generada',true)
);
//bienes de consumo
$config['Asset']['status'] = array(
    __('Disponible', true),
    __('No Disponible', true)
);

//Orden de Compra
$config['PurchaseOrder']['document_types'] = array(
    __('Factura', true),
    __('Guía Despacho', true)
);

//Transaccion
$config['Transaction']['status'] = array(
    __('No Enviada', true),
    __('Enviada', true)
);
//Tipo de Alta
$config['Transaction']['type'] = array(
    __('Donación', true),
    __('Compra', true),
    __('Tipo Alta3', true),
    __('Comodato', true)
);

$config['Subtitulos'] = array(
    '22-01-001-000'=>'PARA PERSONAS',
    '22-02-001-000'=>'TEXTILES Y ACABADOS TEXTILES',
    '22-02-002-000'=>'VESTUARIO ACCES, Y PRENDAS DIV',
    '22-02-003-000'=>'CALZADO',
    '22-03-001-000'=>'PARA VEHICULOS',
    '22-03-003-000'=>'PARA CALEFACCION',
    '22-03-999-000'=>'PARA OTROS',
    '22-04-001-000'=>'MATERIALES DE OFICINA',
    '22-04-002-000'=>'TEXTOS Y OTROS MATERIALES DE E',
    '22-04-006-000'=>'FERTILIZANTES, INSECTICIDAS',
    '22-04-007-000'=>'MATERIALES Y UTILES DE ASEO',
    '22-04-008-000'=>'MENAJE  PARA OFICINA CASINOS Y',
    '22-04-009-000'=>'INSUMOS REP Y ACC. COMPUTACION',
    '22-04-010-000'=>'MAT. PARA MANT Y REP. DE INMUE',
    '22-04-011-000'=>'PARA MANT Y REP. DE VEHICULO',
    '22-04-012-000'=>'OTOS MATER. PARA MANT. Y REP',
    '22-04-013-000'=>'EQUIPOS MENORES',
    '22-04-014-000'=>'PRODUCTOS ELABORADO DE CUERO',
    '22-04-999-000'=>'OTROS MAT. DE USO O CONSUMO',
    '22-05-001-000'=>'ELECTRICIDAD',
    '22-05-002-000'=>'AGUA',
    '22-05-003-000'=>'GAS',
    '22-05-004-000'=>'CORREO',
    '22-05-005-000'=>'TELEFONIA FIJA',
    '22-05-006-000'=>'TELEFONIA CELULAR',
    '22-05-007-000'=>'ACCESO A INTERNET',
    '22-05-008-000'=>'ENLACES DE TELECOMUNICACIONES',
    '22-05-999-000'=>'OTROS SERVICIOS BASICOS',
    '22-06-001-000'=>'MANT. Y REP. DE EDIFICACIONES',
    '22-06-002-000'=>'MANT. Y REP. DE VEHICULOS',
    '22-06-003-000'=>'MANT. Y REP. DE MOBILIARIOS Y',
    '22-06-004-000'=>'MANT. Y REP. DE MAQ Y EQUP OF',
    '22-06-006-000'=>'MANTENC,Y REPAR. DE OTROS MAQ',
    '22-06-007-000'=>'MANT REP EQUIPO INFORMÁTICOS',
    '22-06-999-000'=>'OTRAS MANT. Y REPARACIONES',
    '22-07-001-000'=>'SERVICIOS DE PUBLICIDAD',
    '22-07-002-000'=>'SERVICIOS DE IMPRESION',
    '22-07-003-000'=>'SERVICIOS DE ENCUADERNACION Y EMPASTES',
    '22-07-999-000'=>'OTROS GTOS DE PUBLICDAD Y DIFUSION',
    '22-08-001-000'=>'SERVICIOS DE ASEO',
    '22-08-002-000'=>'SERVICIO DE VIGILANCIA',
    '22-08-003-000'=>'SERV. DE MANTENCION DE JARDINES',
    '22-08-007-000'=>'PASAJES, FLETES Y BODEGAJES',
    '22-08-008-000'=>'SALAS CUNAS Y O JARDINES INFAN',
    '22-08-009-000'=>'SERVICIO DE PAGOS Y COBRANZA',
    '22-08-010-000'=>'SERVICIO DE SUSCRIPCION Y',
    '22-08-999-000'=>'OTROS SERVICIOS GENERALES',
    '22-09-001-000'=>'ARRIENDO DE TERRENOS',
    '22-09-002-000'=>'ARRIENDO DE EDIFICIO',
    '22-09-003-000'=>'ARRIENDO DE VEHICULOS',
    '22-09-004-000'=>'ARRIENDO DE MOBILIRIARIO Y OTR',
    '22-09-005-000'=>'ARRIENDOS DE MAQUINAS Y EQUIPO',
    '22-09-006-000'=>'ARRIENDO DE EQUIP. COMPUTACION',
    '22-09-999-000'=>'OTROS ARRIENDOS (SALONES)',
    '22-10-002-000'=>'PRIMAS Y GASTOS DE SEGUROS',
    '22-10-003-000'=>'SERV. DE GIROS Y REMESAS',
    '22-10-004-000'=>'GASTOS BANCARIOS',
    '22-10-999-000'=>'OTROS SS. FINANCIEROS Y SEGUROS',
    '22-11-001-000'=>'ESTUDIOS E INVESTIGACIONES',
    '22-11-002-000'=>'CURSOS DE CAPACITACION',
    '22-11-003-000'=>'SERVICIOS INFORMATICOS',
    '22-11-999-000'=>'OTROS SS. TECNICOS Y PROFESIONALES',
    '22-12-002-000'=>'GASTOS MENORES',
    '22-12-003-000'=>'GASTOS DE REPRESENTACION',
    '22-12-004-000'=>'INTERESES MULTAS Y RECARGOS',
    '22-12-005-000'=>'DERECHOS Y TASAS',
    '22-12-006-000'=>'CONTRIBUCIONES',
    '22-12-999-000'=>'OTROS GTOS. EN BS. SS. CONSUMO',
    '29-02-000-000'=>'EDIFICIOS',
    '29-03-000-000'=>'VEHICULOS',
    '29-04-000-000'=>'MOBILIARIOS Y OTROS',
    '29-05-001-000'=>'MAQUINAS Y EQUIPOS DE OFICINA',
    '29-05-999-000'=>'OTRAS MAQUINAS Y EQUIPOS',
    '29-06-001-000'=>'EQUIPOS COMPUTACIONALES Y PERI',
    '29-06-002-000'=>'EQUIPOS DE COMUNICACIONES PARA',
    '29-07-001-000'=>'PROGRAMAS COMPUTACIONALES',
    '29-07-002-000'=>'SISTEMA DE INFORMACION'
    
);

$config['Programas']=array(
    __('Noche Digna', true),
    __('Chile Crece Contigo', true),
    __('Ingreso Ético Familiar', true),
    __('Ficha de Protección Social', true)
);
//Orden de Compra
$config['PurchaseOrder']['status'] = array(
    0 => __('No Enviada', true),
    1 => __('Sin Recepcionar', true),
    2 => __('Recepcionada', true),
    3 => __('Nula', true)
);

//Bien de inventario
$config['InventoryAsset']['status'] = array(
    __('No asignado', true),
    __('Asignado', true),
    __('Dado de Baja', true),
);

$config['InventoryAsset']['is_depreciate'] = array(
    __('No', true),
    __('Sí', true),
);

//Historial Bien de inventario
$config['InventoryAssetHistory']['type'] = array(
    __('Ingreso', true),
    __('Asignación', true),
    __('Baja', true),
    __('Liberación', true)
);

//Bajas
$config['InventoryAssetDisposal']['type'] = array(
    __('Anajenación', true),
    __('Tipo Baja 2', true),
    __('Tipo Baja 3', true)
);
?>
