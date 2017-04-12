
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- digressive_price
-- ---------------------------------------------------------------------

CREATE TABLE `digressive_price`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `product_id` INTEGER NOT NULL,
    `price` DECIMAL(16,6) DEFAULT 0.000000 NOT NULL,
    `promo_price` DECIMAL(16,6) DEFAULT 0.000000 NOT NULL,
    `quantity_from` INTEGER NOT NULL,
    `quantity_to` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `FI_product_digressive` (`product_id`),
    CONSTRAINT `fk_product_digressive`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
