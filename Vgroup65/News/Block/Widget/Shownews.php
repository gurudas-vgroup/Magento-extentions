<?php

namespace Vgroup65\News\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;

//use Vgroup65\News\Model\NewslistFactory;

class Shownews extends \Magento\Framework\View\Element\Template implements BlockInterface
{

    protected $_template = 'widget/shownews.phtml';
    protected $_newsListy;
    protected $_newsConfigurationFactory;
    protected $newsFactory;
    protected $helper;

    public function __construct(Context $context, \Vgroup65\News\Model\NewslistFactory $newslistFactory, \Vgroup65\News\Model\NewsConfigurationFactory $newsConfigurationFactory, \Vgroup65\News\Model\NewsFactory $newsFactory, \Vgroup65\News\Helper\Data $helper)
    {
        $this->_newsListy = $newslistFactory;
        $this->_newsConfigurationFactory = $newsConfigurationFactory;
        $this->newsFactory = $newsFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }
    
    public function getNewsCategory()
    {
        return $this->newsFactory->create();
    }
    
    public function getHelper()
    {
        return $this->helper;
    }
    
    public function getNewsCategoryTitle($categoryId)
    {
        $newslist = $this->getNewsCategory();
        $newsCollection = $newslist->getCollection();
        $newsCollection->addFieldToFilter('status', 1);
        $newsCollection->addFieldToFilter('category_id', $categoryId);
        $newsCollection->addFieldToSelect(['title', 'category_url']);
        foreach ($newsCollection as $title):
              return ['title' => $title['title'] , 'category_url' => $title['category_url']] ;
        endforeach;
        return $newsCollection;
    }
    
    public function getConfigurationNewsCount()
    {
        $newsConfiguration = $this->_newsConfigurationFactory->create();
        $configCollection = $newsConfiguration->getCollection();
        $configCollection->addFieldToFilter('news_config', '1');
        foreach ($configCollection as $values):
                $noOfNews = $values->getNoOfNews();
        endforeach;
        return $noOfNews;
    }

    public function getAutoRotateStatus()
    {
        $newsConfiguration = $this->_newsConfigurationFactory->create();
        $configCollection = $newsConfiguration->getCollection();
        $configCollection->addFieldToFilter('news_config', '1');
        foreach ($configCollection as $values):
            return $autoRotate = $values->getAutoRotate();
        endforeach;
    }

    public function getNewsCollection()
    {
        $newsCollection = $this->_newsListy->create();
        $newslistCollection = $newsCollection->getCollection();
        $currentStoreId = $this->getStoreId();

        //filter collection as per store and status
        $newslistCollection->addFieldToFilter('status', 1);
        $newslistCollection->addFieldToFilter('store_view', ['like' => '%'.$currentStoreId.'%']);
        $newslistCollection->addFieldToFilter('publish_date', ['lteq' => date('Y-m-d H:i:s')]);
        $newslistCollection->setOrder('news_id', 'DESC');

        //get news configuration count
        $newsCount = $this->getConfigurationNewsCount();
        if ($newsCount == 0):
            return ['status' => '0'];
        endif;
        
        $newslistCollection->setPageSize($newsCount);
        return ['status' => '1' , 'news' =>$newslistCollection];
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    //    public function getCmsFilterContent($value=''){
    //        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //        $filterProvider = $objectManager->get('Magento\Cms\Model\Template\FilterProvider');
    //        $html = $filterProvider->getPageFilter()->filter($value);
    //        return $html;
    //    }
}
