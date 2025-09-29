<?php
 
namespace Vgroup65\News\Block\Adminhtml\Newsconfiguration\Edit;
 
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
 
class Tabs extends WidgetTabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('newsconfiguration_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('News Configuration Information'));
    }
    
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'newsconfiguration_info',
            [
                'label' => __('Configuration Info'),
                'title' => __('Configuration Info'),
                'content' => $this->getLayout()->createBlock(
                    'Vgroup65\News\Block\Adminhtml\Newsconfiguration\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );
 
        return parent::_beforeToHtml();
    }
}
