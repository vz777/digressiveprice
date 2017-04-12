-- ---------------------------------------------------------------------
-- Update the foreign key to provide automatic delete
-- ---------------------------------------------------------------------

ALTER TABLE `digressive_price` DROP FOREIGN KEY `fk_product_digressive`;

ALTER TABLE `digressive_price` ADD CONSTRAINT `fk_product_digressive`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE;
