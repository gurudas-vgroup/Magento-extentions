<?php
namespace Vgroup65\News\Model\ResourceModel\News;
 
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Vgroup65\News\Model\News',
            'Vgroup65\News\Model\ResourceModel\News'
        );
    }
}
