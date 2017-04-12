<?php

namespace DigressivePrice\Hook;

use DigressivePrice\DigressivePrice;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Translation\Translator;

/**
 * Class DigressivePriceHook
 * @package DigressivePrice\Hook
 * @author Etienne PERRIERE <eperriere@openstudio.fr> - Nexxpix - OpenStudio
 */
class DigressivePriceHook extends BaseHook
{

    public function onProductTab(HookRenderBlockEvent $event)
    {
        $event->add([
            'id' => 'digressive-prices',
            'title' => Translator::getInstance()->trans(
                'Digressive prices',
                [],
                DigressivePrice::DOMAIN
            ),
            'content' => $this->render('product-tab-content-hook.html')
        ]);
    }


    public function onProductJavascriptInitialization(HookRenderEvent $event)
    {
        $event->add(
            $this->render('digressive-price-update-js.html')
        );
    }

}
