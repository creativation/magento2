<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Api\Data;

interface CategoryAttributeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Magento\Catalog\Api\Data\CategoryAttributeInterface[]
     */
    public function getItems();
}
