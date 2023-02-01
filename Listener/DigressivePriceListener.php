<?php

namespace DigressivePrice\Listener;

use DigressivePrice\Event\DigressivePriceEvent;
use DigressivePrice\Event\DigressivePriceFullEvent;
use DigressivePrice\Event\DigressivePriceIdEvent;
use DigressivePrice\Model\DigressivePrice;
use DigressivePrice\Model\DigressivePriceQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Cart\CartItemDuplicationItem;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\Product\ProductEvent;
use Thelia\Model\CartItem;
use Thelia\Model\ProductPriceQuery;


/**
 * Class CartAddListener
 * Manage actions when adding a product to a pack
 *
 * @package DigressivePrice\Listener
 * @author Nexxpix
 */
class DigressivePriceListener extends BaseAction implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::CART_ADDITEM        => [ "itemAddedToCart", 128 ],
            TheliaEvents::CART_UPDATEITEM     => [ "itemAddedToCart", 128 ],
            TheliaEvents::CART_ITEM_DUPLICATE => [ 'cartItemDuplication', 100],

            'action.createDigressivePrice' => [ "createDigressivePrice", 128 ],
            'action.updateDigressivePrice' => [ "updateDigressivePrice", 128 ],
            'action.deleteDigressivePrice' => [ "deleteDigressivePrice", 128 ]
        ];
    }
    
    /*I've added the itemAddedToCart function because I receveid "Attempted to call an undefined method named "itemAddedToCart" of class "DigressivePrice\Listener\DigressivePriceListener"
    I find it strange that this function is not already defined*/

    public function itemAddedToCart(CartEvent $event)
    {
        $this->updateCartItemPrice($event);
    }

    /**
     * Set the good item's price when added to cart
     *
     * @param CartEvent $event
     */
    public function updateCartItemPrice(CartEvent $event)
    {
        $this->processCartItem($event->getCartItem(), true);
    }

    /**
     * Update cart prices after duplication.
     *
     * @param CartItemDuplicationItem $event
     */
    public function cartItemDuplication(CartItemDuplicationItem $event)
    {
        $this->processCartItem($event->getNewItem(), false);
    }

    /**
     * Process a cart item to apply our price if required.
     *
     * @param CartItem $cartItem
     * @param bool $setDefaultPrice if true, the regular price is set if the quantity doesn't match any slice.
     */
    protected function processCartItem(CartItem $cartItem, $setDefaultPrice)
    {
        // Check if the quantity is into a range
        if (null !== $dpq = DigressivePriceQuery::create()
                ->filterByProductId($cartItem->getProductId())
                ->filterByQuantityFrom($cartItem->getQuantity(), Criteria::LESS_EQUAL)
                ->filterByQuantityTo($cartItem->getQuantity(), Criteria::GREATER_EQUAL)
                ->findOne()) {
            // Change cart item's prices with those from the corresponding range
            $cartItem
                ->setPrice($dpq->getPrice())
                ->setPromoPrice($dpq->getPromoPrice())
                ->save();
        } elseif ($setDefaultPrice) {
            // Change cart item's prices with the default one
            $prices = ProductPriceQuery::create()
                ->findOneByProductSaleElementsId($cartItem->getProductSaleElementsId());

            $cartItem
                ->setPrice($prices->getPrice())
                ->setPromoPrice($prices->getPromoPrice())
                ->save();
        }
    }

    /**
     * @param DigressivePriceEvent $event
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createDigressivePrice(DigressivePriceEvent $event)
    {
        $digressivePrice = new DigressivePrice();

        $digressivePrice
            ->setProductId($event->getProductId())
            ->setPrice($event->getPrice())
            ->setPromoPrice($event->getPromoPrice())
            ->setQuantityFrom($event->getQuantityFrom())
            ->setQuantityTo($event->getQuantityTo())
            ->save();
    }

    /**
     * @param DigressivePriceFullEvent $event
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateDigressivePrice(DigressivePriceFullEvent $event)
    {
        $digressivePrice = DigressivePriceQuery::create()->findOneById($event->getId());

        $digressivePrice
            ->setProductId($event->getProductId())
            ->setPrice($event->getPrice())
            ->setPromoPrice($event->getPromoPrice())
            ->setQuantityFrom($event->getQuantityFrom())
            ->setQuantityTo($event->getQuantityTo())
            ->save();
    }

    /**
     * @param DigressivePriceIdEvent $event
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function deleteDigressivePrice(DigressivePriceIdEvent $event)
    {
        DigressivePriceQuery::create()
            ->filterById($event->getId())
            ->delete();
    }
}
