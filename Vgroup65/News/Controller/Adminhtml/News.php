<?php
 
namespace Vgroup65\News\Controller\Adminhtml;
 
// use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Vgroup65\News\Model\NewsFactory;
 
abstract class News extends \Magento\Backend\App\Action
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
     * News model factory
     *
     * @var \Tutorial\SimpleNews\Model\NewsFactory
     */
    protected $_newsFactory;
    /**
     * Newslist model factory
     *
     * @var Vgroup65\News\Model\NewslistFactory
     */
    protected $_newslistFactory;
    
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
        NewsFactory $newsFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
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
