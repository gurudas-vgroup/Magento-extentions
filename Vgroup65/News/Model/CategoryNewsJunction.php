<?php
namespace Vgroup65\News\Model;

use Magento\Framework\Model\AbstractModel;

class CategoryNewsJunction extends AbstractModel
{
    
    public function _construct()
    {
        $this->_init('Vgroup65\News\Model\ResourceModel\CategoryNewsJunction');
    }
}
