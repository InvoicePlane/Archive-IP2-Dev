/*  Customize quote / invoice groups #5 */
ALTER TABLE `ip_invoice_groups`
  ADD COLUMN `invoice_group_identifier_format` VARCHAR(255) NOT NULL
  AFTER `invoice_group_name`;

/* convert fields to format string
 * Schema: Prefix{{{YEAR}}}{{{MONTH}}}{{{ID}}}
 * (based on existing schema)
 */
UPDATE ip_invoice_groups
  SET invoice_group_identifier_format = CONCAT(
    invoice_group_prefix,
    CASE invoice_group_prefix_year
      WHEN 1 THEN '{{{year}}}'
      ELSE ''
    END,
    CASE invoice_group_prefix_month
      WHEN 1 THEN '{{{month}}}'
      ELSE ''
    END,
    '{{{id}}}'
);
ALTER TABLE `ip_invoice_groups`
  DROP invoice_group_prefix,
  DROP invoice_group_prefix_month,
  DROP invoice_group_prefix_year;

# Module "families"
CREATE TABLE IF NOT EXISTS `ip_families` (
  `family_id`   INT(11) NOT NULL AUTO_INCREMENT,
  `family_name` VARCHAR(50)      DEFAULT NULL,
  PRIMARY KEY (`family_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

# Module "products"
CREATE TABLE IF NOT EXISTS `ip_products` (
  `product_id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `family_id`           INT(11)      NOT NULL,
  `product_sku`         VARCHAR(15)  NOT NULL,
  `product_name`        VARCHAR(50)  NOT NULL,
  `product_description` LONGTEXT     NOT NULL,
  `product_price`       FLOAT(10, 2) NOT NULL,
  `purchase_price`      FLOAT(10, 2) NOT NULL,
  `tax_rate_id`         INT(11)      NOT NULL,
  PRIMARY KEY (`product_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

/* Add the Invoice Sign */
ALTER TABLE `ip_invoice_amounts`
  ADD `invoice_sign` ENUM('1', '-1') NOT NULL DEFAULT '1'
  AFTER `invoice_id`;

ALTER TABLE `ip_invoices`
  ADD `creditinvoice_parent_id` INT(11),
  ADD `is_read_only` TINYINT(1)
  AFTER `invoice_status_id`;