<?php
namespace Vgroup65\News\Controller\Adminhtml\NewsConfiguration;

use Vgroup65\News\Controller\Adminhtml\NewsConfiguration;

class Save extends NewsConfiguration
{
    
    
    public function execute()
    {
        $formData = $this->getRequest()->getParam('news');
         
        if (isset($formData)):
            try {
                $configModel = $this->_newsConfigurationFactory->create();
                $configModel->load($formData['news_config']);
                $configModel->setData($formData);
                $configModel->save();
            
                $this->messageManager->addSuccess(__('Configuration updated successfully'));
                $this->_redirect('*/*/index');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/index');
                return;
            }
        endif;
    }
}
