<?php

/**
 * This file is part of the Brille24 tierprice plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Brille24\SyliusTierPricePlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class TierPrice implements TierPriceInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $price;

    /**
     * @var int
     */
    private $qty;

    /**
     * @var ChannelInterface|null
     */
    private $channel;

    /**
     * @var string|null
     */
    private $channelCode;
    /**
     * @var ProductVariantInterface
     */
    private $productVariant;

    /**
     * @var CustomerGroupInterface|null
     */
    private $customerGroup;
    /**
     * @var int|null
     */
    private $originalPrice ;


    /**
     * @var int
     */
    protected $minimumPrice = 0;


    /**
     * @var ArrayCollection
     * @psalm-var ArrayCollection<array-key, CatalogPromotionInterface>
     */
    protected $appliedPromotions;


    public function __construct(int $quantity = 0, int $price = 0)
    {
        $this->appliedPromotions = new ArrayCollection();
        $this->qty   = $quantity;
        $this->price = $price;
    }

    public function __toString(): string
    {
        return (string) $this->getPrice();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function setQty(int $qty): void
    {
        $this->qty = max($qty, 0);
    }

    public function getProductVariant(): ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function setProductVariant(ProductVariantInterface $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->setChannelCode($channel->getCode());
        $this->channel = $channel;
    }

    public function getCustomerGroup(): ?CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function setCustomerGroup(?CustomerGroupInterface $customerGroup): void
    {
        $this->customerGroup = $customerGroup;
    }
    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }

    public function getOriginalPrice(): ?int
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(?int $originalPrice): void
    {
        $this->originalPrice = $originalPrice;
    }

    public function getMinimumPrice(): int
    {
        return $this->minimumPrice;
    }

    public function setMinimumPrice(int $minimumPrice): void
    {
        $this->minimumPrice = $minimumPrice ?: 0;
    }

    public function isPriceReduced(): bool
    {
        return $this->originalPrice > $this->getPrice();
    }

    public function addAppliedPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        if($this->appliedPromotions->contains($catalogPromotion)) {
            return;
        }

        $this->appliedPromotions->add($catalogPromotion);
    }

    public function removeAppliedPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->appliedPromotions->removeElement($catalogPromotion);
    }

    public function getAppliedPromotions():ArrayCollection
    {
        if(is_null($this->appliedPromotions)){
            $this->appliedPromotions = new ArrayCollection();
        }
        return $this->appliedPromotions;
    }

    public function hasPromotionApplied(CatalogPromotionInterface $catalogPromotion): bool
    {
        return $this->appliedPromotions->contains($catalogPromotion);
    }

    public function clearAppliedPromotions(): void
    {
        $this->appliedPromotions->clear();
    }

    public function hasExclusiveCatalogPromotionApplied(): bool
    {

        foreach ($this->getAppliedPromotions() as $appliedPromotion) {
            if($appliedPromotion->isExclusive()) {
                return true;
            }
        }

        return false;
    }
}
