<?php
namespace Vgroup65\News\Controller\Adminhtml\Newslist;

use Vgroup65\News\Controller\Adminhtml\Newslist;

class NewAction extends Newslist
{
    /**
     * Create new newslist action
     *
     * @return void
     */
    public function execute()
    {
        
        $this->_forward('edit');
    }
}
