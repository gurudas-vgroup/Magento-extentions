<?php
namespace Vgroup65\News\Controller\Adminhtml\NewsConfiguration;

use Vgroup65\News\Controller\Adminhtml\NewsConfiguration;

class Index extends NewsConfiguration
{
    
    public function execute()
    {
        
        $model = $this->_newsConfigurationFactory->create();
        $newsConfigurationId = 1;
            $model->load($newsConfigurationId);
        if (!$model->getId()) {
            $this->_messageManager->addError('This id is no longer exist');
            $this->_redirect('*/*/');
            return;
        }

        
        // Restore previously entered form data from session
        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('news_config', $model);
        
        /**
*
         *
 * @var \Magento\Backend\Model\View\Result\Page $resultPage
*/
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Vgroup65_News::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('News Extension Configuration'));
 
        return $resultPage;
    }
}
