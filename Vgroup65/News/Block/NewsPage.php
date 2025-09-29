<?php

namespace Vgroup65\News\Block;

class NewsPage extends \Magento\Framework\View\Element\Template
{

    protected $_pageSize = 10;
    protected $_newsCategory;
    protected $_newsList;
    protected $_categoryNewsJunction;
    protected $helper;
    protected $filterProvider;
    protected $newsConfiguration;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Vgroup65\News\Model\NewsFactory $newsFactory, \Vgroup65\News\Model\NewslistFactory $newsListFactory, \Vgroup65\News\Model\CategoryNewsJunctionFactory $categoryNewsJunction, \Vgroup65\News\Helper\Data $helper, \Magento\Cms\Model\Template\FilterProvider $filterProvider, \Vgroup65\News\Model\NewsConfigurationFactory $newsConfiguration)
    {
        parent::__construct($context);
        $this->_categoryNewsJunction = $categoryNewsJunction;
        $this->_newsCategory = $newsFactory;
        $this->_newsList = $newsListFactory;
        $this->helper = $helper;
        $this->filterProvider = $filterProvider;
        $this->newsConfiguration = $newsConfiguration;
    }

    protected function _prepareLayout()
    {
        $configurationModel = $this->newsConfiguration->create();
        $configCollection = $configurationModel->getCollection();
        $configCollection->addFieldToFilter('news_config', '1');
        foreach ($configCollection as $configColl):
            $topMenuText = $configColl['top_menu_text'];
        endforeach;

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', ['label' => 'Home', 'title' => 'Home', 'link' => $this->getUrl()]);
        $breadcrumbs->addCrumb($topMenuText, ['label' => $topMenuText, 'title' => $topMenuText, 'link' => $this->getUrl('news')]);
        $this->getLayout()->getBlock('breadcrumbs')->toHtml();
        $this->pageConfig->getTitle()->set(__($topMenuText));

        parent::_prepareLayout();

        //get news count
        $newsListCount = $this->getNewsListCount()->count();
        $defaultLimit = [5 => 5];

        if ($newsListCount > 5 && $newsListCount <= 10) {
            $defaultLimit = [5 => 5, 10 => 10];
        }
        if ($newsListCount > 10 && $newsListCount <= 15) {
            $defaultLimit = [5 => 5, 10 => 10, 15 => 15];
        }
        if ($newsListCount > 15 && $newsListCount <= 25) {
            $defaultLimit = [5 => 5, 10 => 10, 15 => 15, 20 => 20];
        }
        if ($newsListCount > 25 && $newsListCount <= 50) {
            $defaultLimit = [5 => 5, 10 => 10, 15 => 15, 20 => 20, 50 => 50];
        }
        if ($newsListCount > 50 && $newsListCount <= 100) {
            $defaultLimit = [5 => 5, 10 => 10, 15 => 15, 20 => 20, 50 => 50, 100 => 100];
        }

        if ($this->getNewsList()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'fme.news.pager'
            )->setAvailableLimit($defaultLimit)->setShowPerPage(true)->setCollection($this->getNewsList());
            $this->setChild('pager', $pager);
            $this->getNewsList()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getHelper()
    {
        return $this->helper;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getNewsCategory()
    {
        $newslist = $this->_newsCategory->create();
        $newsCollection = $newslist->getCollection();
        $newsCollection->addFieldToFilter('status', 1);
        return $newsCollection;
    }

    public function getNewsCategoryTitle($categoryId)
    {
        $newslist = $this->_newsCategory->create();
        $newsCollection = $newslist->getCollection();
        $newsCollection->addFieldToFilter('status', 1);
        $newsCollection->addFieldToFilter('category_id', $categoryId);
        $newsCollection->addFieldToSelect(['title', 'category_url']);
        foreach ($newsCollection as $title):
            return ['title' => $title['title'], 'category_url' => $title['category_url']];
        endforeach;
        return $newsCollection;
    }

    public function getCategoryNewsJunction()
    {
        $categoryNewsJunction = $this->_categoryNewsJunction->create();
        $junctionColection = $categoryNewsJunction->getCollection();
        return $junctionColection;
    }

    public function getNewsList()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        $newsList = $this->_newsList->create();
        $newsCollection = $newsList->getCollection();
        //get params

        $categoryUrl = $this->getRequest()->getParam('category');

        $categoryId = '';
        if (isset($categoryUrl) && $categoryUrl != 'allNews'):
            $categoryCollection = $this->getNewsCategory();
            $categoryCollection->addFieldToFilter('category_url', $categoryUrl);

            foreach ($categoryCollection as $category):
                $categoryId = $category->getCategoryId();
            endforeach;


            //get getCategoryNewsJunction instance
            //                $categoryNewsJunctionCollection = $this->getCategoryNewsJunction();
            //
            //                $categoryNewsJunctionCollection->addFieldToFilter('category_id' , $categoryId);
            //
            //
            //                $newsIdsArray = array_column($categoryNewsJunctionCollection->getData(), 'news_id');
            //                $newsIds = @implode(',' , $newsIdsArray);
            //
            //                $newsCollection->addFieldToFilter('news_id' , array( 'in' => $newsIdsArray));
            $newsCollection->addFieldToFilter('category_id', ['like' => '%' . $categoryId . '%']);
        endif;


        $storeId = $this->getStoreId();
        $newsCollection->addFieldToFilter('status', '1');

        $newsCollection->addFieldToFilter('store_view', ['like' => '%' . $storeId . '%']);
        $newsCollection->setOrder('publish_date', 'DESC');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $date = $objDate->date()->format('Y-m-d H:i:s');

        $newsCollection->addFieldToFilter('publish_date', ['lteq' => $date]);

        $newsCollection->setPageSize($pageSize);
        $newsCollection->setCurPage($page);

        return $newsCollection;
    }

    public function getNewsListCount()
    {
        $newsList = $this->_newsList->create();
        $newsCollection = $newsList->getCollection();
        //get params
        $categoryUrl = $this->getRequest()->getParam('category');

        $categoryId = '';
        if (isset($categoryUrl) && $categoryUrl != 'allNews'):
            $categoryCollection = $this->getNewsCategory();
            $categoryCollection->addFieldToFilter('category_url', $categoryUrl);

            foreach ($categoryCollection as $category):
                $categoryId = $category->getCategoryId();
            endforeach;
            $newsCollection->addFieldToFilter('category_id', ['like' => '%' . $categoryId . '%']);
        endif;

        $storeId = $this->getStoreId();
        $newsCollection->addFieldToFilter('status', '1');

        $newsCollection->addFieldToFilter('store_view', ['like' => '%' . $storeId . '%']);
        $newsCollection->setOrder('publish_date', 'DESC');
        $newsCollection->addFieldToFilter('publish_date', ['lteq' => date('Y-m-d H:i:s')]);
        return $newsCollection;
    }

    public function getCmsFilterContent($value = '')
    {
        $filterProvider = $this->filterProvider;
        $html = $filterProvider->getPageFilter()->filter($value);
        return $html;
    }
}
