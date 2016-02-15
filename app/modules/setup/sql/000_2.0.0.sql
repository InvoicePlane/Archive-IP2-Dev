# ************************************************************
# InvoicePlane
# Version 2.0.0
# ************************************************************

# client_notes
# ------------------------------------------------------------

CREATE TABLE `client_notes` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`    INT(11) UNSIGNED NOT NULL,
  `note`         LONGTEXT         NOT NULL,
  `date_created` DATE             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# clients
# ------------------------------------------------------------

CREATE TABLE `clients` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(200)     NOT NULL DEFAULT '',
  `address_1`     VARCHAR(200)              DEFAULT '',
  `address_2`     VARCHAR(200)              DEFAULT '',
  `city`          VARCHAR(100)              DEFAULT '',
  `state`         VARCHAR(100)              DEFAULT '',
  `zip`           VARCHAR(100)              DEFAULT '',
  `country`       VARCHAR(100)              DEFAULT '',
  `phone`         VARCHAR(50)               DEFAULT '',
  `fax`           VARCHAR(50)               DEFAULT '',
  `mobile`        VARCHAR(50)               DEFAULT '',
  `email`         VARCHAR(200)              DEFAULT '',
  `web`           VARCHAR(200)              DEFAULT '',
  `vat_id`        VARCHAR(200)              DEFAULT '',
  `tax_code`      VARCHAR(200)              DEFAULT '',
  `is_active`     TINYINT(1)       NOT NULL DEFAULT '1',
  `date_created`  DATETIME         NOT NULL,
  `date_modified` DATETIME         NOT NULL,
  `date_deleted`  DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `is_active` (`is_active`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# custom_client
# ------------------------------------------------------------

CREATE TABLE `custom_client` (
  `id`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# custom_fields
# ------------------------------------------------------------

CREATE TABLE `custom_fields` (
  `id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `table`  VARCHAR(50)      NOT NULL DEFAULT '',
  `type`   VARCHAR(100)     NOT NULL DEFAULT '',
  `label`  VARCHAR(10)      NOT NULL DEFAULT '',
  `column` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# custom_invoice
# ------------------------------------------------------------

CREATE TABLE `custom_invoice` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# custom_payment
# ------------------------------------------------------------

CREATE TABLE `custom_payment` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# custom_quote
# ------------------------------------------------------------

CREATE TABLE `custom_quote` (
  `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# custom_user
# ------------------------------------------------------------

CREATE TABLE `custom_user` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# email_templates
# ------------------------------------------------------------

CREATE TABLE `email_templates` (
  `id`                 INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`              VARCHAR(300)     NOT NULL DEFAULT '',
  `type`               VARCHAR(200)              DEFAULT NULL,
  `subject`            VARCHAR(300)              DEFAULT NULL,
  `to_email`           VARCHAR(300)     NOT NULL DEFAULT '',
  `from_name`          VARCHAR(300)              DEFAULT NULL,
  `from_email`         VARCHAR(300)              DEFAULT NULL,
  `cc`                 VARCHAR(300)              DEFAULT NULL,
  `bcc`                VARCHAR(300)              DEFAULT NULL,
  `pdf_template`       VARCHAR(100)              DEFAULT NULL,
  `body_template_file` VARCHAR(100)     NOT NULL DEFAULT '',
  `send_attachments`   TINYINT(1)       NOT NULL,
  `send_pdf`           TINYINT(1)       NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# import_details
# ------------------------------------------------------------

CREATE TABLE `import_details` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `import_id`  INT(11) UNSIGNED NOT NULL,
  `lang_key`   VARCHAR(50)      NOT NULL DEFAULT '',
  `table_name` VARCHAR(50)      NOT NULL DEFAULT '',
  `record_id`  INT(11)          NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# imports
# ------------------------------------------------------------

CREATE TABLE `imports` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `import_date` DATETIME         NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# invoice_amounts
# ------------------------------------------------------------

CREATE TABLE `invoice_amounts` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`     INT(11) UNSIGNED NOT NULL,
  `sign`           ENUM('1', '-1')  NOT NULL DEFAULT '1',
  `item_subtotal`  DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `item_tax_total` DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `tax_total`      DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `total`          DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `paid`           DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `balance`        DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# invoice_groups
# ------------------------------------------------------------

CREATE TABLE `invoice_groups` (
  `id`                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(100)     NOT NULL DEFAULT '',
  `identifier_format` VARCHAR(300)     NOT NULL DEFAULT '',
  `next_id`           INT(11)          NOT NULL,
  `left_pad`          INT(2)           NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# invoice_item_amounts
# ------------------------------------------------------------

CREATE TABLE `invoice_item_amounts` (
  `id`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id`   INT(11) UNSIGNED NOT NULL,
  `subtotal`  DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `tax_total` DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `discount`  DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `total`     DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# invoice_items
# ------------------------------------------------------------

CREATE TABLE `invoice_items` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`      INT(11) UNSIGNED NOT NULL,
  `tax_rate_id`     INT(11) UNSIGNED          DEFAULT NULL,
  `task_id`         INT(11) UNSIGNED          DEFAULT NULL,
  `product_id`      INT(11) UNSIGNED          DEFAULT NULL,
  `name`            VARCHAR(200)     NOT NULL DEFAULT '',
  `description`     LONGTEXT,
  `quantity`        DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `price`           DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `discount_amount` DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `item_order`      INT(2)           NOT NULL DEFAULT '0',
  `date_created`    DATE             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# invoice_tax_rates
# ------------------------------------------------------------

CREATE TABLE `invoice_tax_rates` (
  `id`               INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`       INT(11) UNSIGNED NOT NULL,
  `tax_rate_id`      INT(11) UNSIGNED NOT NULL,
  `include_item_tax` TINYINT(1)       NOT NULL DEFAULT '0',
  `amount`           DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# invoices
# ------------------------------------------------------------

CREATE TABLE `invoices` (
  `id`                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`           INT(11) UNSIGNED NOT NULL,
  `client_id`         INT(11) UNSIGNED NOT NULL,
  `invoice_group_id`  INT(11) UNSIGNED NOT NULL,
  `status_id`         INT(11) UNSIGNED NOT NULL,
  `payment_method_id` INT(11) UNSIGNED          DEFAULT NULL,
  `credit_parent_id`  INT(11) UNSIGNED          DEFAULT NULL,
  `invoice_number`    VARCHAR(200)              DEFAULT NULL,
  `date_due`          DATE             NOT NULL,
  `discount_amount`   DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `discount_percent`  DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `terms`             LONGTEXT,
  `url_key`           VARCHAR(50)      NOT NULL DEFAULT '',
  `password`          VARCHAR(90)               DEFAULT NULL,
  `is_read_only`      TINYINT(1)                DEFAULT NULL,
  `date_created`      DATETIME         NOT NULL,
  `date_modified`     DATETIME         NOT NULL,
  `date_deleted`      DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_number` (`invoice_number`),
  KEY `url_key` (`url_key`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# invoices_recurring
# ------------------------------------------------------------

CREATE TABLE `invoices_recurring` (
  `id`                 INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`         INT(11) UNSIGNED NOT NULL,
  `email_template_id`  INT(11) UNSIGNED NOT NULL,
  `start_date`         DATE             NOT NULL,
  `end_date`           DATE             NOT NULL,
  `frequency`          VARCHAR(100)     NOT NULL DEFAULT '',
  `next_date`          DATE             NOT NULL,
  `invoices_due_after` INT(11)          NOT NULL DEFAULT '30',
  `date_deleted`       DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# online_payments
# ------------------------------------------------------------

CREATE TABLE `online_payments` (
  `id`               INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`       INT(11) UNSIGNED NOT NULL,
  `gateway_name`     VARCHAR(100)     NOT NULL DEFAULT '',
  `gateway_response` VARCHAR(500)     NOT NULL DEFAULT '',
  `reference`        VARCHAR(300)     NOT NULL DEFAULT '',
  `date_created`     DATE             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# payment_methods
# ------------------------------------------------------------

CREATE TABLE `payment_methods` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `method_name` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# payments
# ------------------------------------------------------------

CREATE TABLE `payments` (
  `id`                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`        INT(11) UNSIGNED NOT NULL,
  `payment_method_id` INT(11) UNSIGNED NOT NULL,
  `amount`            DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `note`              LONGTEXT,
  `payment_date`      DATE             NOT NULL,
  `date_deleted`      DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# product_families
# ------------------------------------------------------------

CREATE TABLE `product_families` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100)     NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# products
# ------------------------------------------------------------

CREATE TABLE `products` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `family_id`      INT(11) UNSIGNED NOT NULL,
  `tax_rate_id`    INT(11) UNSIGNED NOT NULL,
  `sku`            VARCHAR(50)      NOT NULL DEFAULT '',
  `name`           VARCHAR(200)     NOT NULL DEFAULT '',
  `description`    LONGTEXT,
  `price`          DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `purchase_price` DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `date_deleted`   DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# projects
# ------------------------------------------------------------

CREATE TABLE `projects` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`     INT(11) UNSIGNED NOT NULL,
  `user_id`       INT(11) UNSIGNED NOT NULL,
  `project_name`  VARCHAR(200)     NOT NULL DEFAULT '',
  `date_created`  DATETIME         NOT NULL,
  `date_modified` DATETIME         NOT NULL,
  `date_deleted`  DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# quote_amounts
# ------------------------------------------------------------

CREATE TABLE `quote_amounts` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id`       INT(11) UNSIGNED NOT NULL,
  `item_subtotal`  DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `item_tax_total` DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `tax_total`      DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `total`          DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# quote_item_amounts
# ------------------------------------------------------------

CREATE TABLE `quote_item_amounts` (
  `id`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id`   INT(11) UNSIGNED NOT NULL,
  `subtotal`  DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `tax_total` DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `discount`  DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `total`     DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# quote_items
# ------------------------------------------------------------

CREATE TABLE `quote_items` (
  `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id`        INT(11) UNSIGNED NOT NULL,
  `tax_rate_id`     INT(11) UNSIGNED          DEFAULT NULL,
  `product_id`      INT(11) UNSIGNED          DEFAULT NULL,
  `name`            VARCHAR(100)     NOT NULL,
  `description`     LONGTEXT,
  `quantity`        DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `price`           DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `discount_amount` DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `item_order`      INT(2)           NOT NULL DEFAULT '0',
  `date_created`    DATE             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# quote_tax_rates
# ------------------------------------------------------------

CREATE TABLE `quote_tax_rates` (
  `id`               INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id`         INT(11) UNSIGNED NOT NULL,
  `tax_rate_id`      INT(11) UNSIGNED NOT NULL,
  `include_item_tax` TINYINT(1)       NOT NULL DEFAULT '0',
  `amount`           DECIMAL(50, 10)  NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# quotes
# ------------------------------------------------------------

CREATE TABLE `quotes` (
  `id`               INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`       INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `user_id`          INT(11) UNSIGNED NOT NULL,
  `client_id`        INT(11) UNSIGNED NOT NULL,
  `invoice_group_id` INT(11) UNSIGNED NOT NULL,
  `status_id`        INT(11) UNSIGNED NOT NULL,
  `quote_number`     VARCHAR(200)              DEFAULT NULL,
  `date_expires`     DATE             NOT NULL,
  `discount_amount`  DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `discount_percent` DECIMAL(50, 10)  NOT NULL DEFAULT '0.0000000000',
  `url_key`          CHAR(50)         NOT NULL DEFAULT '',
  `password`         VARCHAR(90)               DEFAULT NULL,
  `notes`            LONGTEXT,
  `date_created`     DATETIME         NOT NULL,
  `date_modified`    DATETIME         NOT NULL,
  `date_deleted`     DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_number` (`quote_number`),
  KEY `url_key` (`url_key`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# settings
# ------------------------------------------------------------

CREATE TABLE `settings` (
  `id`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`   VARCHAR(100)     NOT NULL DEFAULT '',
  `value` LONGTEXT         NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# statuses
# ------------------------------------------------------------

CREATE TABLE `statuses` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status_name` VARCHAR(100)     NOT NULL DEFAULT '',
  `color`       VARCHAR(7)       NOT NULL DEFAULT '#111111',
  `type`        INT(11)          NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# tasks
# ------------------------------------------------------------

CREATE TABLE `tasks` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id`    INT(11) UNSIGNED NOT NULL,
  `tax_rate_id`   INT(11) UNSIGNED NOT NULL,
  `name`          VARCHAR(50)      NOT NULL,
  `description`   LONGTEXT,
  `price`         DECIMAL(50, 10)  NOT NULL,
  `finish_date`   DATE             NOT NULL,
  `status`        TINYINT(1)       NOT NULL,
  `date_created`  DATETIME         NOT NULL,
  `date_modified` DATETIME         NOT NULL,
  `date_deleted`  DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# tax_rates
# ------------------------------------------------------------

CREATE TABLE `tax_rates` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(100)     NOT NULL DEFAULT '',
  `percent` DECIMAL(5, 5)    NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# uploads
# ------------------------------------------------------------

CREATE TABLE `uploads` (
  `id`                 INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`          INT(11) UNSIGNED          DEFAULT NULL,
  `quote_id`           INT(11) UNSIGNED          DEFAULT NULL,
  `invoice_id`         INT(11) UNSIGNED          DEFAULT NULL,
  `payment_id`         INT(11) UNSIGNED          DEFAULT NULL,
  `project_id`         INT(11) UNSIGNED          DEFAULT NULL,
  `url_key`            VARCHAR(50)      NOT NULL DEFAULT '',
  `file_name_original` LONGTEXT         NOT NULL,
  `file_name_new`      LONGTEXT         NOT NULL,
  `date_uploaded`      DATE             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url_key` (`url_key`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# user_clients
# ------------------------------------------------------------

CREATE TABLE `user_clients` (
  `id`        INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`   INT(11) UNSIGNED NOT NULL,
  `client_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# user_roles
# ------------------------------------------------------------

CREATE TABLE `user_roles` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL DEFAULT '',
  `permissions` LONGTEXT         NOT NULL,
  `is_client`   TINYINT(1)                DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id`                  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_role_id`        INT(1) UNSIGNED  NOT NULL,
  `is_active`           TINYINT(1)                DEFAULT '1',
  `name`                VARCHAR(100)              DEFAULT '',
  `email`               VARCHAR(100)     NOT NULL,
  `company`             VARCHAR(100)              DEFAULT '',
  `address_1`           VARCHAR(100)              DEFAULT '',
  `address_2`           VARCHAR(100)              DEFAULT '',
  `city`                VARCHAR(45)               DEFAULT '',
  `state`               VARCHAR(35)               DEFAULT '',
  `zip`                 VARCHAR(15)               DEFAULT '',
  `country`             VARCHAR(35)               DEFAULT '',
  `phone`               VARCHAR(20)               DEFAULT '',
  `fax`                 VARCHAR(20)               DEFAULT '',
  `mobile`              VARCHAR(20)               DEFAULT '',
  `web`                 VARCHAR(100)              DEFAULT '',
  `vat_id`              VARCHAR(100)              DEFAULT '',
  `tax_code`            VARCHAR(100)              DEFAULT '',
  `password`            VARCHAR(60)      NOT NULL,
  `psalt`               CHAR(100)        NOT NULL DEFAULT '',
  `passwordreset_token` VARCHAR(100)              DEFAULT '',
  `date_created`        DATETIME         NOT NULL,
  `date_modified`       DATETIME         NOT NULL,
  `date_deleted`        DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  KEY `name` (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# versions
# ------------------------------------------------------------

CREATE TABLE `versions` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file`         VARCHAR(100)     NOT NULL DEFAULT '',
  `sql_errors`   INT(11)          NOT NULL,
  `date_applied` DATE             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

# ------------------------------------------------------------
# Foreign keys
# ------------------------------------------------------------

ALTER TABLE `client_notes`
ADD KEY `client_id` (`client_id`),
ADD FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

ALTER TABLE `custom_client`
ADD KEY `client_id` (`client_id`),
ADD FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

ALTER TABLE `custom_invoice`
ADD KEY `invoice_id` (`invoice_id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

ALTER TABLE `custom_payment`
ADD KEY `payment_id` (`payment_id`),
ADD FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`);

ALTER TABLE `custom_quote`
ADD KEY `quote_id` (`quote_id`),
ADD FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`);

ALTER TABLE `custom_user`
ADD KEY `user_id` (`user_id`),
ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `import_details`
ADD KEY `import_id` (`import_id`),
ADD FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`);

ALTER TABLE `invoice_amounts`
ADD KEY `invoice_id` (`invoice_id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

ALTER TABLE `invoice_item_amounts`
ADD KEY `item_id` (`item_id`),
ADD FOREIGN KEY (`item_id`) REFERENCES `invoice_items` (`id`);

ALTER TABLE `invoice_items`
ADD KEY `product_id` (`product_id`),
ADD KEY `invoice_id` (`invoice_id`),
ADD KEY `task_id` (`task_id`),
ADD KEY `tax_rate_id` (`tax_rate_id`),
ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
ADD FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
ADD FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates` (`id`);

ALTER TABLE `invoice_tax_rates`
ADD KEY `invoice_id` (`invoice_id`),
ADD KEY `tax_rate_id` (`tax_rate_id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
ADD FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates` (`id`);

ALTER TABLE `invoices`
ADD KEY `client_id` (`client_id`),
ADD KEY `credit_parent_id` (`credit_parent_id`),
ADD KEY `invoice_group_id` (`invoice_group_id`),
ADD KEY `payment_method_id` (`payment_method_id`),
ADD KEY `status_id` (`status_id`),
ADD KEY `user_id` (`user_id`),
ADD FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
ADD FOREIGN KEY (`credit_parent_id`) REFERENCES `invoices` (`id`),
ADD FOREIGN KEY (`invoice_group_id`) REFERENCES `invoice_groups` (`id`),
ADD FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
ADD FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `invoices_recurring`
ADD KEY `email_template_id` (`email_template_id`),
ADD KEY `invoice_id` (`invoice_id`),
ADD FOREIGN KEY (`email_template_id`) REFERENCES `email_templates` (`id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

ALTER TABLE `online_payments`
ADD KEY `invoice_id` (`invoice_id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

ALTER TABLE `payments`
ADD KEY `invoice_id` (`invoice_id`),
ADD KEY `payment_method_id` (`payment_method_id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
ADD FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`);

ALTER TABLE `products`
ADD KEY `family_id` (`family_id`),
ADD KEY `tax_rate_id` (`tax_rate_id`),
ADD FOREIGN KEY (`family_id`) REFERENCES `product_families` (`id`),
ADD FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates` (`id`);

ALTER TABLE `projects`
ADD KEY `client_id` (`client_id`),
ADD KEY `user_id` (`user_id`),
ADD FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `quote_amounts`
ADD KEY `quote_id` (`quote_id`),
ADD FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`);

ALTER TABLE `quote_item_amounts`
ADD KEY `item_id` (`item_id`),
ADD FOREIGN KEY (`item_id`) REFERENCES `quote_items` (`id`);

ALTER TABLE `quote_items`
ADD KEY `product_id` (`product_id`),
ADD KEY `quote_id` (`quote_id`),
ADD KEY `tax_rate_id` (`tax_rate_id`),
ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
ADD FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`),
ADD FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates` (`id`);

ALTER TABLE `quote_tax_rates`
ADD KEY `tax_rate_id` (`tax_rate_id`),
ADD KEY `quote_id` (`quote_id`),
ADD FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates` (`id`),
ADD FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`);

ALTER TABLE `quotes`
ADD KEY `client_id` (`client_id`),
ADD KEY `invoice_id` (`invoice_id`),
ADD KEY `invoice_group_id` (`invoice_group_id`),
ADD KEY `status_id` (`status_id`),
ADD KEY `user_id` (`user_id`),
ADD FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
ADD FOREIGN KEY (`invoice_group_id`) REFERENCES `invoice_groups` (`id`),
ADD FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `tasks`
ADD KEY `project_id` (`project_id`),
ADD KEY `tax_rate_id` (`tax_rate_id`),
ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
ADD FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates` (`id`);

ALTER TABLE `uploads`
ADD KEY `project_id` (`project_id`),
ADD KEY `client_id` (`client_id`),
ADD KEY `invoice_id` (`invoice_id`),
ADD KEY `payment_id` (`payment_id`),
ADD KEY `quote_id` (`quote_id`),
ADD FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
ADD FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
ADD FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
ADD FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`),
ADD FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`);

ALTER TABLE `user_clients`
ADD KEY `user_id` (`user_id`),
ADD KEY `client_id` (`client_id`),
ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
ADD FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

ALTER TABLE `users`
ADD KEY `user_role_id` (`user_role_id`),
ADD FOREIGN KEY (`user_role_id`) REFERENCES `user_roles` (`id`);
