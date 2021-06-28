<?php

namespace Emageshop\Twitch\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends \Magento\Catalog\Block\Product\View
{

    public function getLiveStream($query)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helperTwitch = $objectManager->get(\Emageshop\Twitch\Helper\Twitch::class);

        $liveStreem = $helperTwitch->getLiveStream($query);

        return $liveStreem;
    }

}
