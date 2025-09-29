<?php
namespace Vgroup65\News\Block;

class NewsDescription extends \Magento\Framework\View\Element\Template
{
    
    protected $_newsCategory;
    protected $_newsList;
    protected $helper;
    protected $filterProvider;
    protected $newsConfiguration;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vgroup65\News\Model\NewsFactory $newsFactory,
        \Vgroup65\News\Model\NewslistFactory $newsListFactory,
        \Vgroup65\News\Helper\Data $helper,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Vgroup65\News\Model\NewsConfigurationFactory $newsConfiguration
    ) {
        parent::__construct($context);
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
        $breadcrumbs->addCrumb('home', ['label'=>'Home', 'title'=>'Home', 'link'=> $this->getUrl()]);
        $breadcrumbs->addCrumb($topMenuText, ['label'=>$topMenuText, 'title'=> $topMenuText, 'link'=>$this->getUrl('news')]);
        
        //get Currnet news
        $getCurrentNews = $this->getNewsDetail();
        if ($getCurrentNews->count() > 0):
            foreach ($getCurrentNews as $currentNews):
                $newsTitle = $currentNews['title'];
                $newsUrlIdentifier = $currentNews['url_identifier'];
            endforeach;
            $breadcrumbs->addCrumb($topMenuText, ['label'=>$newsTitle, 'title'=> $newsTitle, 'link'=>$this->getUrl('news/view/detail/', ['news'=> $newsUrlIdentifier])]);
        endif;
        $this->getLayout()->getBlock('breadcrumbs')->toHtml();
        $this->pageConfig->getTitle()->set(__($topMenuText));
        
        
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addCss('docs.theme.min.css');
        }
        return parent::_prepareLayout();
    }
        
    public function getNewsCategory()
    {
        $newslist = $this->_newsCategory->create();
        $newsColection = $newslist->getCollection();
        $newsColection->addFieldToFilter('status', '1');
        return $newsColection;
    }
    
    public function getHelper()
    {
        return $this->helper;
    }
    
    public function getNewsCategoryTitle($categoryId)
    {
        $newslist = $this->_newsCategory->create();
        $newsCollection = $newslist->getCollection();
        $newsCollection->addFieldToFilter('status', 1);
        $newsCollection->addFieldToFilter('category_id', $categoryId);
        $newsCollection->addFieldToSelect(['title', 'category_url']);
        foreach ($newsCollection as $title):
              return ['title' => $title['title'] , 'category_url' => $title['category_url']] ;
        endforeach;
        return $newsCollection;
    }
    
    public function getNewsDetail()
    {
        $newsList = $this->_newsList->create();
        $newsCollection= $newsList->getCollection();
        
        //get params
        $getNewsParam = $this->getRequest()->getParam('news');
        $newsCollection->addFieldToFilter('url_identifier', $getNewsParam);
        $newsCollection->addFieldToFilter('status', '1');
        return $newsCollection;
    }

    public function getCmsFilterContent($value = '')
    {
        $filterProvider = $this->filterProvider;
        $html = $filterProvider->getPageFilter()->filter($value);
        return $html;
    }
    
    public function getNextNews($currentNews = [])
    {
        //return $currentNews;
        $newsList = $this->_newsList->create();


        $nextNewsCollection = $newsList->getCollection();

        $nextNewsCollection->addFieldToSelect(['url_identifier']);
        $nextNewsCollection->addFieldToFilter('status', '1');
        $nextNewsCollection->addFieldToFilter('news_id', ['gt' => $currentNews['id']]);
        $nextNewsCollection->getSelect()->order('news_id', \Magento\Framework\DB\Select::SQL_ASC);
        $nextNewsCollection->getSelect()->limit(1);

        //echo $nextNewsCollection->getSelect();exit;
        if ($nextNewsCollection->getSize() > 0) {
            foreach ($nextNewsCollection as $nextNews) {
                return $nextNews;
            }
        }
    }

    public function getPreviousNews($currentNews = [])
    {

        $newsList = $this->_newsList->create();
        $preNewsCollection = $newsList->getCollection();
        $preNewsCollection->addFieldToSelect(['url_identifier']);
        $preNewsCollection->addFieldToFilter('status', '1');
        $preNewsCollection->addFieldToFilter('news_id', ['lt' => $currentNews['id']]);
        //$preNewsCollection->getSelect()->order('news_id', \Magento\Framework\DB\Select::SQL_DESC);
        $preNewsCollection->setOrder('news_id', 'DESC');
        $preNewsCollection->getSelect()->limit(1);

        if ($preNewsCollection->getSize() > 0) {
            foreach ($preNewsCollection as $preNews) {
                return $preNews;
            }
        }
    }
}
