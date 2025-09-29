<?php
 
namespace Vgroup65\News\Controller\Adminhtml\News;
 
use Vgroup65\News\Controller\Adminhtml\News;
 
class Grid extends News
{
    /**
     * @return void
     */
    public function execute()
    {

        return $this->_resultPageFactory->create();
    }
}
