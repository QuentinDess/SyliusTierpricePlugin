<?php


namespace Brille24\SyliusTierPricePlugin\Applicator;


use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibilityInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

class CatalogPromotionApplicator implements CatalogPromotionApplicatorInterface
{
    public function __construct(
        private ActionBasedDiscountApplicatorInterface $actionBasedDiscountApplicator,
        private ProductVariantForCatalogPromotionEligibilityInterface $checker,
        private CatalogPromotionEligibilityCheckerInterface $catalogPromotionEligibilityChecker
    ) {}

    public function applyOnVariant(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        if (!$this->catalogPromotionEligibilityChecker->isCatalogPromotionEligible($catalogPromotion)) {
            return;
        }

        if (!$this->checker->isApplicableOnVariant($catalogPromotion, $variant)) {
            return;
        }
        foreach ($catalogPromotion->getActions() as $action) {
            $this->applyDiscountFromAction($catalogPromotion, $action, $variant);
        }
    }

    private function applyDiscountFromAction(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ProductVariantInterface $variant
    ): void {
        foreach ($catalogPromotion->getChannels() as $channel) {
            $channelPricing = $variant->getChannelPricingForChannel($channel);
            if ($channelPricing === null) {
                continue;
            }

            /* @var \Brille24\SyliusTierPricePlugin\Entity\ProductVariantInterface $variant */

            $tierPrices = $variant->getTierPrices();
            if(!is_null($tierPrices)){

                foreach ($tierPrices as $tierPrice){
                    $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $tierPrice);
                }

            }
            $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
        }
    }
}
