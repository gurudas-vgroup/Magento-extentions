<?php
 
namespace Vgroup65\News\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Vgroup65\News\Model\NewslistFactory;
use Vgroup65\News\Model\NewsFactory;

abstract class Import extends \Magento\Backend\App\Action
//class News extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
 
    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Newslist model factory
     *
     * @var Vgroup65\News\Model\NewslistFactory
     */
    protected $_newslistFactory;
    protected $_newsFactory;
    
    //    protected $_helper;
    /**
     * @param Context     $context
     * @param Registry    $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param NewsFactory $newsFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        NewslistFactory $newslistFactory,
        NewsFactory $newsFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_newslistFactory = $newslistFactory;
        $this->_newsFactory = $newsFactory;
    }
 
    /**
     * News access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vgroup65_News::manage_news');
    }
}
