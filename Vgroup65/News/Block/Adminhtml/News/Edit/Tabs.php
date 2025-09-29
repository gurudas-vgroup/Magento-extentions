<?php
 
namespace Vgroup65\News\Block\Adminhtml\News\Edit;
 
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
        $this->setId('news_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('News Category Information'));
    }
 
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'news_info',
            [
                'label' => __('Category Info'),
                'title' => __('Category Info'),
                'content' => $this->getLayout()->createBlock(
                    'Vgroup65\News\Block\Adminhtml\News\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );
 
        return parent::_beforeToHtml();
    }
}
