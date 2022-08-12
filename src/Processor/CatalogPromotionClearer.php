<?php


namespace Brille24\SyliusTierPricePlugin\Processor;


use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CatalogPromotionClearer implements CatalogPromotionClearerInterface
{
    public function clearVariant(ProductVariantInterface $variant): void
    {

        foreach ($variant->getChannelPricings() as $channelPricing) {
            $this->clearChannelPricing($channelPricing);
        }
        foreach ($variant->getTierPrices() as $tierPrice){
            $this->clearChannelPricing($tierPrice);
        }
    }

    private function clearChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        if ($channelPricing->getAppliedPromotions()->isEmpty()) {
            return;
        }

        if ($channelPricing->getOriginalPrice() !== null) {
            $channelPricing->setPrice($channelPricing->getOriginalPrice());
        }
        $channelPricing->clearAppliedPromotions();
    }
}
