<?php
 
namespace Vgroup65\News\Controller\Adminhtml\Newslist;
 
use Vgroup65\News\Controller\Adminhtml\Newslist;
 
class Grid extends Newslist
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}
