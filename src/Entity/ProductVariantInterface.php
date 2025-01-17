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

use Brille24\SyliusTierPricePlugin\Traits\TierPriceableInterface;

interface ProductVariantInterface extends TierPriceableInterface, \Sylius\Component\Core\Model\ProductVariantInterface
{
    public function removeTierPrice(TierPriceInterface $tierPrice): void;

    public function addTierPrice(TierPriceInterface $tierPrice): void;
}
