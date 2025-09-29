<?php
namespace Vgroup65\News\Model;

use Magento\Framework\Model\AbstractModel;
 
class Newslist extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Vgroup65\News\Model\ResourceModel\Newslist');
    }
}
