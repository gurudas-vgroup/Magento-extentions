<?php
 
namespace Vgroup65\News\Controller\Adminhtml\News;
 
use Vgroup65\News\Controller\Adminhtml\News;
 
class NewAction extends News
{
    /**
     * Create new news action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
