<?php
namespace Vgroup65\News\Controller\Adminhtml\Export;

class Index extends \Magento\Backend\App\Action
{
    
    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
          
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Load the page defined in view/adminhtml/layout/news_export_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        //$resultPage->setActiveMenu('Vgroup65_News::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('News Export'));
        return $resultPage;
    }
}
