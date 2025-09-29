<?php
 
namespace Vgroup65\News\Model\ResourceModel;
 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class CategoryNewsJunction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('category_news_junction', 'category_news_id');
    }
}
