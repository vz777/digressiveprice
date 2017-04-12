<?php

namespace DigressivePrice\Loop;

use DigressivePrice\Model\DigressivePrice;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;
use DigressivePrice\Model\DigressivePriceQuery;
use Thelia\Model\ProductQuery;

/**
 * Class DigressiveLoop
 * Definition of the Digressive loop of DigressivePrice module
 *
 * @package DigressivePrice\Loop
 * @author Etienne PERRIERE <eperriere@openstudio.fr> - Nexxpix - OpenStudio
 * @method getProductId()
 * @method getQuantity()
 */
class DigressiveLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    public $countable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('product_id'),
            Argument::createIntTypeArgument('quantity')
        );
    }

    public function buildModelCriteria()
    {
        $productId = $this->getProductId();
        $search = DigressivePriceQuery::create();

        if (!is_null($productId)) {
            $search->filterByProductId($productId);
        }

        if (null !== $quantity = $this->getQuantity()) {
            $search
                ->filterByQuantityFrom($quantity, Criteria::LESS_EQUAL)
                ->filterByQuantityTo($quantity, Criteria::GREATER_EQUAL)
            ;
        }

        return $search;
    }

    public function parseResults(LoopResult $loopResult)
    {
        /** @var DigressivePrice $digressivePrice */
        foreach ($loopResult->getResultDataCollection() as $digressivePrice) {
            $loopResultRow = new LoopResultRow($digressivePrice);

            // Get product
            $productId = $digressivePrice->getProductId();
            $product = ProductQuery::create()->findOneById($productId);

            // Get prices
            $price = $digressivePrice->getPrice();
            $promo = $digressivePrice->getPromoPrice();

            // Get country
            $taxCountry = $this->container->get('thelia.taxEngine')->getDeliveryCountry();

            // Get taxed prices
            $taxedPrice = $product->getTaxedPrice($taxCountry, $price);
            $taxedPromoPrice = $product->getTaxedPromoPrice($taxCountry, $promo);

            $loopResultRow
                ->set("ID", $digressivePrice->getId())
                ->set("PRODUCT_ID", $productId)
                ->set("QUANTITY_FROM", $digressivePrice->getQuantityFrom())
                ->set("QUANTITY_TO", $digressivePrice->getQuantityTo())
                ->set("PRICE", $price)
                ->set("PROMO_PRICE", $promo)
                ->set("TAXED_PRICE", $taxedPrice)
                ->set("TAXED_PROMO_PRICE", $taxedPromoPrice);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
