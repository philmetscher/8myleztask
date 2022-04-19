<?php declare(strict_types=1);

namespace Shopware\Core\Content\Product\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * This route is an general route to get products of the sales channel
 */
abstract class AbstractProductListRoute
{
    abstract public function getDecorated(): AbstractProductListRoute;

    abstract public function load(Criteria $criteria, SalesChannelContext $context): ProductListResponse;
}
