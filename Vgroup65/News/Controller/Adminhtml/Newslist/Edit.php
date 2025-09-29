<?php
namespace Vgroup65\News\Controller\Adminhtml\Newslist;

use Vgroup65\News\Controller\Adminhtml\Newslist;

class Edit extends Newslist
{
    
    public function execute()
    {
        $newsId = $this->getRequest()->getParam('id');
        
        $model = $this->_newslistFactory->create();
        
        if ($newsId) {
            $model->load($newsId);
            if (!$model->getId()) {
                $this->_messageManager->addError('This id is no longer exist');
                $this->_redirect('*/*/');
                return;
            }
        }
        
        // Restore previously entered form data from session
        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('news_list', $model);
        
        /**
*
         *
 * @var \Magento\Backend\Model\View\Result\Page $resultPage
*/
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Vgroup65_News::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Add/Edit News'));
 
        return $resultPage;
    }
}
