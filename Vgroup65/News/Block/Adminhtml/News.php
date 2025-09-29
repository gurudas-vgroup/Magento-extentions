<?php
 
namespace Vgroup65\News\Block\Adminhtml;
 
use Magento\Backend\Block\Widget\Grid\Container;
 
class News extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_news';
        $this->_blockGroup = 'Vgroup65_News';
        $this->_headerText = __('Manage Category');
        $this->_addButtonLabel = __('Add Category');
        $this->buttonList->add(
            'category',
            ['label' => __('Back') ,
            'onclick' => 'setLocation(\'' . $this->getUrl('news/newslist/index') . '\')']
        );
        parent::_construct();
    }
}
