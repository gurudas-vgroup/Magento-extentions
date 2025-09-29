<?php
 
namespace Vgroup65\News\Block\Adminhtml\Newslist\Edit;
 
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
 
class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('newslist_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('News List Information'));
    }
 
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'news_info',
            [
                'label' => __('News Info'),
                'title' => __('News Info'),
                'content' => $this->getLayout()->createBlock(
                    'Vgroup65\News\Block\Adminhtml\Newslist\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
