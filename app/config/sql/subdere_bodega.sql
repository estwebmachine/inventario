SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `subdere_bodega` DEFAULT CHARACTER SET utf8 ;
USE `subdere_bodega` ;

-- -----------------------------------------------------
-- Table `subdere_bodega`.`regions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`regions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Regiones PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`cities`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`cities` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `region_id` INT(11) NOT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Ciudades PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`communes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`communes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `city_id` INT(11) NOT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Comunas PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`providers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`providers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `region_id` INT(11) NOT NULL ,
  `city_id` INT(11) NOT NULL ,
  `commune_id` INT(11) NOT NULL ,
  `rut` VARCHAR(255) NULL DEFAULT NULL ,
  `socialreason` VARCHAR(255) NULL DEFAULT NULL ,
  `fantasyname` VARCHAR(255) NULL DEFAULT NULL ,
  `address` VARCHAR(255) NULL DEFAULT NULL ,
  `phone` VARCHAR(255) NULL DEFAULT NULL ,
  `fax` VARCHAR(255) NULL DEFAULT NULL ,
  `website` VARCHAR(255) NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `contact_name` VARCHAR(255) NULL DEFAULT NULL ,
  `contact_phone` VARCHAR(255) NULL DEFAULT NULL ,
  `contact_cell` VARCHAR(255) NULL DEFAULT NULL ,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'está activo, 0:NO, 1:SI' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Proveedores (PARAMETRIZABLE)';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`positions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`positions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Cargos de usuarios PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`warehouses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`warehouses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `address` VARCHAR(255) NULL DEFAULT NULL ,
  `status` INT(11) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Bodegas PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`cost_centers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`cost_centers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `code` INT(11) NULL ,
  `user_id` INT(11) NULL ,
  `warehouse_id` INT(11) NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Centro de Costos (Unidades de la Empresa)';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `position_id` INT(11) NOT NULL ,
  `cost_center_id` INT(11) NOT NULL DEFAULT 1 ,
  `username` VARCHAR(255) NULL DEFAULT NULL ,
  `password` VARCHAR(255) NULL DEFAULT NULL ,
  `role` INT(11) NULL DEFAULT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `status` INT(11) NULL DEFAULT NULL ,
  `theme` VARCHAR(255) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Usuarios PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`purchase_orders`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`purchase_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `location` INT NULL COMMENT 'si la orden de compra es regional o edificio' ,
  `destination` INT(11) NULL DEFAULT NULL ,
  `name` VARCHAR(255) NULL COMMENT 'Nombre de la orden de compra' ,
  `description` TEXT NULL COMMENT 'descripción de la orden de compra' ,
  `provider_id` INT(11) NOT NULL ,
  `date` DATETIME NULL DEFAULT NULL ,
  `order_number` VARCHAR(255) NULL DEFAULT NULL ,
  `currency` VARCHAR(255) NULL COMMENT 'moneda orden de compra, UF, CLP.' ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado:\n0=No enviada;\n1=Enviada;\n2=Recepcionada;\n3=Nula;' ,
  `comment` TEXT NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Ordenes de compra'
ROW_FORMAT = DYNAMIC;


-- -----------------------------------------------------
-- Table `subdere_bodega`.`purchase_order_details`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`purchase_order_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `destination` INT NULL COMMENT 'si el ítem de orden de compra es para bodega, inventario, servicios' ,
  `asset_id` INT(11) NULL ,
  `purchase_order_id` INT(11) NOT NULL ,
  `code` VARCHAR(255) NULL ,
  `name` VARCHAR(255) NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `amount` INT(11) NULL DEFAULT NULL ,
  `amount_trans` INT(11) NULL DEFAULT NULL ,
  `currency` VARCHAR(255) NULL COMMENT 'moneda detalle orden de compra, UF, CLP.' ,
  `price` INT(11) NULL DEFAULT NULL ,
  `value` INT(11) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Detalles de las Ordenes de Compra'
ROW_FORMAT = DYNAMIC;


-- -----------------------------------------------------
-- Table `subdere_bodega`.`measure_units`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`measure_units` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `multiplier` INT(11) NULL DEFAULT 1 COMMENT 'Numero que multiplica el ingreso de bienes para bodega' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Unidades de Medida PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(255) NULL DEFAULT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `isinventory` TINYINT(1) NULL DEFAULT 0 ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Grupos (En el sistema actual Familias) PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`families`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`families` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `group_id` INT(11) NOT NULL ,
  `code` VARCHAR(255) NULL DEFAULT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `isinventory` TINYINT(1) NULL DEFAULT 0 ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Familia (En el sistema actual sub-familia ) (sub-categoria d' /* comment truncated */;


-- -----------------------------------------------------
-- Table `subdere_bodega`.`types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `family_id` INT(11) NOT NULL ,
  `code` VARCHAR(255) NULL DEFAULT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  `isinventory` TINYINT(1) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Tipo (Categoria hija de tabla \'families\')';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`assets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`assets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `measure_unit_id` INT(11) NOT NULL COMMENT 'Unidad de medida' ,
  `type_id` INT(11) NOT NULL COMMENT 'Categoria a la que pertenece\n' ,
  `family_id` INT(11) NOT NULL COMMENT 'Sub-familia a la que pertenece' ,
  `group_id` INT(11) NOT NULL COMMENT 'Familia a la que pertenece' ,
  `code` VARCHAR(255) NULL DEFAULT NULL ,
  `barcode` VARCHAR(255) NULL DEFAULT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `criticstock` INT(11) NULL DEFAULT NULL COMMENT 'Stock critico del bien (segunda aviso)' ,
  `minimumstock` INT(11) NULL DEFAULT NULL COMMENT 'Stock minimo (primer aviso)' ,
  `description` TEXT NULL DEFAULT NULL ,
  `is_inventory` TINYINT(1) ZEROFILL NULL COMMENT 'es inventario, 0:NO, 1:SI' ,
  `life` INT(11) NULL DEFAULT 1 COMMENT 'Vida util en meses' ,
  `residual_value` INT(11) NULL DEFAULT 0 COMMENT 'Valor residual' ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado disponibilidad:\n0=Disponible;\n1=No disponible;' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_assets_families` (`family_id` ASC) ,
  INDEX `fk_assets_groups` (`group_id` ASC) ,
  INDEX `fk_assets_measure_units` (`measure_unit_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Bienes de Bodega e Inventario';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`assets_warehouses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`assets_warehouses` (
  `asset_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NOT NULL ,
  PRIMARY KEY (`asset_id`, `warehouse_id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Relacion entre Bienes y Bodegas';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`asset_requests`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`asset_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `cost_center_id` INT(11) NULL DEFAULT NULL ,
  `user_id` INT(11) NOT NULL ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado solicitud:\n0=No enviada;\n1=Pendiente de Aprobacion;\n2=Aprobada;\n3=No Aprobada;\n4=Despachada;\n5=Objetada;' ,
  `send_date` DATETIME NULL DEFAULT NULL COMMENT 'Fecha envio solicitud' ,
  `aproval_date` DATETIME NULL DEFAULT NULL COMMENT 'Fecha aprobacion solicitud' ,
  `dispatch_date` DATETIME NULL DEFAULT NULL COMMENT 'Fecha de despacho' ,
  `return_date` DATETIME NULL DEFAULT NULL COMMENT 'Fecha de devolucion' ,
  `comment` TEXT NULL DEFAULT NULL ,
  `extra` INT(11) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Solicitud de bienes de Bodega';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`asset_request_details`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`asset_request_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `cost_center_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `asset_request_id` INT(11) NOT NULL ,
  `asset_id` INT(11) NOT NULL ,
  `amount` INT(11) NULL DEFAULT NULL COMMENT 'Monto solicitado' ,
  `amount_approved` INT(11) NULL DEFAULT NULL COMMENT 'Monto aprobado de la solicitud' ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado solicitud:\n0=No enviada;\n1=Pendiente de Aprobacion;\n2=Aprobada;\n3=No Aprobada;\n4=Despachada;\n5=Objetada;\n' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Detalles de Solicitud de Bienes de Bodega';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`users_warehouses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`users_warehouses` (
  `user_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NOT NULL )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Relacion entre Bodegas e Inventario';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`stocks`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`stocks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `asset_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NOT NULL ,
  `amount` INT(11) NULL DEFAULT NULL ,
  `physical` INT(11) NULL DEFAULT NULL COMMENT 'Stock de bienes DE Bodega e Inventario' ,
  `logical` INT(11) NULL DEFAULT NULL ,
  `price` INT(11) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Stock de Bodega';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`transactions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `purchase_order_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NULL ,
  `destination` INT(11) NULL DEFAULT NULL COMMENT 'Este campo indica si la transaccion es para bodega=0, o inventario=1' ,
  `date` DATETIME NULL DEFAULT NULL ,
  `document_type` INT(11) NULL DEFAULT NULL ,
  `document_number` INT(11) NULL DEFAULT NULL ,
  `document_date` DATETIME NULL DEFAULT NULL ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado:\n0=No enviada;\n1=Enviada;' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Recepcion de bienes Bodega e Inventario'
ROW_FORMAT = FIXED;


-- -----------------------------------------------------
-- Table `subdere_bodega`.`transaction_details`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`transaction_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `transaction_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NULL ,
  `asset_id` INT(11) NULL ,
  `purchase_order_detail_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `amount` INT(11) NULL DEFAULT NULL ,
  `amount_trans` INT(11) NULL DEFAULT NULL ,
  `price` INT(11) NULL DEFAULT NULL ,
  `value` INT(11) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Detalles de Recepcion de Bienes Bodega e Inventario'
ROW_FORMAT = DYNAMIC;


-- -----------------------------------------------------
-- Table `subdere_bodega`.`adjustments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`adjustments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `type` INT(11) NOT NULL COMMENT 'Tipo de ajuste:\n0=positvo;\n1=negativo;' ,
  `warehouse_id` INT(11) NOT NULL COMMENT 'Bodega' ,
  `user_id` INT(11) NOT NULL ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado de ajuste:\n0=No enviado;\n1=Pendiente de aprobacion;\n2=Aprobado;\n3=No aprobado;' ,
  `aproval_date` DATETIME NULL DEFAULT NULL COMMENT 'Fecha de aprobacion' ,
  `comment` TEXT NULL DEFAULT NULL ,
  `admcomment` TEXT NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_cost_centers_adjustments` (`warehouse_id` ASC) ,
  INDEX `fk_users_adjustments` (`user_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Ajustes de Bodega';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`adjustment_details`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`adjustment_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL COMMENT 'Ususario que realiza el ajuste' ,
  `adjustment_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NOT NULL COMMENT 'Bodega' ,
  `asset_id` INT(11) NOT NULL COMMENT 'Bien al que se realiza el ajuste' ,
  `amount` INT(11) NULL DEFAULT NULL COMMENT 'Monto a ajustar' ,
  `amount_approved` INT(11) NULL DEFAULT NULL COMMENT 'Monto aprobado del ajuste' ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado de ajuste:\n0=No enviado;\n1=Pendiente de aprobacion;\n2=Aprobado;\n3=No aprobado;' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_adjustments_adjustment_details` (`adjustment_id` ASC) ,
  INDEX `fk_assets_adjustment_details` (`asset_id` ASC) ,
  INDEX `fk_cost_centers_adjustments_details` (`warehouse_id` ASC) ,
  INDEX `fk_users_adjustment_details` (`user_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Detalles de Ajustes de Bodeja';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`asset_returns`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`asset_returns` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NOT NULL ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado devolucion:\n0=No enviada;\n1=Pendiente de aporbacion;\n2=Aprobada;\n3=No aprobada;\n4=Pendoente recepcion;\n' ,
  `aproval_date` DATETIME NULL DEFAULT NULL COMMENT 'Fecha aprobacion' ,
  `comment` TEXT NULL DEFAULT NULL ,
  `admcomment` TEXT NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Devolucion Bienes de Bodega';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`asset_return_details`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`asset_return_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `asset_return_id` INT(11) NOT NULL ,
  `asset_id` INT(11) NOT NULL COMMENT 'Bien a devolver' ,
  `warehouse_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `amount` INT(11) NULL DEFAULT NULL COMMENT 'Cantidad a devolver' ,
  `amount_approved` INT(11) NULL DEFAULT NULL COMMENT 'Cantidad aprobada para devolucion' ,
  `status` INT(11) NULL DEFAULT NULL COMMENT 'Estado devolucion:\n0=No enviada;\n1=Pendiente de aporbacion;\n2=Aprobada;\n3=No aprobada;\n4=Pendoente recepcion;\n' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Detalles Devolucion Bienes de Bodega';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`notifications`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`notifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `asset_return_id` INT(11) NOT NULL ,
  `asset_request_id` INT(11) NOT NULL ,
  `cost_center_id` INT(11) NOT NULL ,
  `adjustment_id` INT(11) NOT NULL ,
  `admin_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `warehouse_id` INT(11) NOT NULL ,
  `type` INT(11) NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Notificaciones en el sistema (PARAMETRIZABLE)';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`inventory_assets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`inventory_assets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'id bien de inventario' ,
  `asset_id` INT(11) NOT NULL COMMENT 'id del bien padre' ,
  `purchase_order_id` INT(11) NULL COMMENT 'Orden de compra' ,
  `code` VARCHAR(255) NULL COMMENT 'código del bien de inventario' ,
  `index` INT(11) NULL ,
  `status` INT(11) NULL COMMENT 'estado del bien de inventario:\n0=Ingresado;\n1=Asignado;\n2=Dado de baja;' ,
  `appreciation` TINYINT(1) NULL COMMENT 'el bien se aprecia, 0:NO, 1:SI' ,
  `original_price` INT(11) NULL COMMENT 'precio original del bien de inventario' ,
  `description` TEXT NULL ,
  `created` DATETIME NULL DEFAULT NULL COMMENT 'fecha de creación' ,
  `modified` DATETIME NULL DEFAULT NULL COMMENT 'fecha de modificación' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Bienes de inventario';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`inventory_asset_disposals`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`inventory_asset_disposals` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'id de la baja' ,
  `inventory_asset_id` INT(11) NOT NULL COMMENT 'id del bien de inventario' ,
  `type` INT(11) NULL COMMENT 'tipo de la baja:\n0=Tipo Baja 1;\n1=Tipo Baja 2;\n2=Tipo Baja 3;' ,
  `comment` TEXT NULL COMMENT 'comentario de la baja' ,
  `resolution_date` DATE NULL DEFAULT NULL COMMENT 'fecha de la resolución de baja' ,
  `resolution_number` VARCHAR(255) NULL COMMENT 'número de la resolución de baja' ,
  `created` DATETIME NULL DEFAULT NULL COMMENT 'fecha de creación' ,
  `modified` DATETIME NULL DEFAULT NULL COMMENT 'fecha de modificación' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Bajas de bienes de inventario';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`seremis`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`seremis` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL COMMENT 'nombre seremi' ,
  `created` DATETIME NULL DEFAULT NULL COMMENT 'fecha creación' ,
  `modified` DATETIME NULL DEFAULT NULL COMMENT 'fecha modificación' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Seremis (En el sistema actual URS)(PARAMETRIZABLE)';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`acities`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`acities` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  `seremi_id` INT(11) NOT NULL COMMENT 'Clave foranea a tabla seremis (URS)' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Ciudades para ubicaciones de inventario PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`addresses`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`addresses` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  `acity_id` INT NOT NULL COMMENT 'Clave foranea a tabla acities' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Direcciones para ubicaciones de invetantario PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`floors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`floors` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `number` VARCHAR(255) NULL COMMENT 'número del piso' ,
  `created` DATETIME NULL DEFAULT NULL COMMENT 'fecha de creación' ,
  `modified` DATETIME NULL DEFAULT NULL COMMENT 'fecha de modificación' ,
  `address_id` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Pisos para ubicaciones de inventario PARAMETRIZABLE';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`rooms`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`rooms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL COMMENT 'nombre sala' ,
  `type` INT NULL COMMENT 'Tipo:\n1=Sala;\n2=Oficina;\n3=Modulo;' ,
  `user_id` INT(11) NULL COMMENT 'Responsable de la sala' ,
  `floor_id` INT NOT NULL COMMENT 'id piso' ,
  `created` DATETIME NULL DEFAULT NULL COMMENT 'fecha creación' ,
  `modified` DATETIME NULL DEFAULT NULL COMMENT 'fecha modificación' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Salas para ubicaciones de inventario(PARAMETRIZABLE)';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`inventory_asset_allocations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`inventory_asset_allocations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'id de la asignación' ,
  `inventory_asset_id` INT(11) NOT NULL COMMENT 'id del bien de inventario asignado' ,
  `user_id` INT(11) NULL COMMENT 'id del usuario al que fue asignado el bien de inventario' ,
  `cost_center_id` INT(11) NULL COMMENT 'id del centro de costo' ,
  `seremi_id` INT(11) NULL COMMENT 'URS a la que pertenece' ,
  `acity_id` INT NULL COMMENT 'Ciudad a la que pertenece' ,
  `address_id` INT NULL COMMENT 'Direccion a la que pertenece' ,
  `floor_id` INT NULL COMMENT 'Piso al que pertenece' ,
  `room_id` INT(11) NULL COMMENT 'Sala a la que pertenece' ,
  `is_current` TINYINT(1) NULL COMMENT 'si la asignación es actual, 0:NO, 1:SI' ,
  `created` DATETIME NULL DEFAULT NULL COMMENT 'fecha de creación' ,
  `modified` DATETIME NULL DEFAULT NULL COMMENT 'fecha de modificación' ,
  `resolution_number` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Asignación de bienes de inventario';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`inventory_asset_histories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`inventory_asset_histories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `inventory_asset_id` INT(11) NOT NULL COMMENT 'Bien de inventario' ,
  `inventory_asset_disposal_id` INT(11) NULL COMMENT 'id de la baja del bien' ,
  `inventory_asset_allocation_id` INT(11) NULL COMMENT 'id de la asignación del bien' ,
  `type` INT(11) NULL COMMENT 'tipo del registro de historial:\n0=Ingreso;\n1=Asignacion;\n2=Baja;' ,
  `comment` TEXT NULL COMMENT 'comentario del registro de historial' ,
  `created` DATETIME NULL DEFAULT NULL COMMENT 'fecha de creación' ,
  `modified` DATETIME NULL DEFAULT NULL COMMENT 'fecha de modificación' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Historial del bien de inventario';


-- -----------------------------------------------------
-- Table `subdere_bodega`.`logs`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `subdere_bodega`.`logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `type` INT(11) NULL ,
  `comment` TEXT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Historial de acceso de usuarios al sistema';



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
