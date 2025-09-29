<?php

namespace Vgroup65\News\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;

class Topmenu implements ObserverInterface
{
    protected $newsConfiguration;
    
    protected $_url;
    public function __construct(
        \Magento\Cms\Block\Block $cmsBlock,
        \Magento\Framework\UrlInterface $url,
        \Vgroup65\News\Model\NewsConfigurationFactory $newsConfiguration
    ) {
        $this->_url = $url;
        $this->newsConfiguration = $newsConfiguration;
    }
    /**
     * @param  EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $newsConfiguration = $this->newsConfiguration->create();
        $newsConfigurationCollection = $newsConfiguration->getCollection();
        $newsConfigurationCollection->addFieldToFilter('news_config', '1');
        foreach ($newsConfigurationCollection as $values):
            $title = $values['top_menu_text'];
            $display_top_menu = $values['display_top_menu'];
        endforeach;
        
        /**
*
         *
 * @var \Magento\Framework\Data\Tree\Node $menu
*/
        $menu = $observer->getMenu();
        
        if ($display_top_menu == 1):
            $tree = $menu->getTree();
            $data = [
                'name'      => __($title),
                'id'        => 'vgroupinc-news-menu-id',
                'url'       => $this->_url->getUrl('news'),
                'is_active' => false
            ];
            $node = new Node($data, 'id', $tree, $menu);
            $menu->addChild($node);
        endif;
        return $this;
    }
}
