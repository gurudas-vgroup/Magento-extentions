<?php
 
namespace Vgroup65\News\Block\Adminhtml;
 
use Magento\Backend\Block\Widget\Grid\Container;
 
class Newslist extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_newslist';
        $this->_blockGroup = 'Vgroup65_News';
        $this->_headerText = __('News List');
        $this->_addButtonLabel = __('Add News');
        $this->buttonList->add(
            'category',
            ['label' => __('News Category') ,
            'onclick' => 'setLocation(\'' . $this->getUrl('news/news/index') . '\')']
        );
        $this->buttonList->add(
            'export',
            ['label' => __('Export') ,
            'onclick' => 'setLocation(\'' . $this->getUrl('news/fileexport/index') . '\')']
        );
        $this->buttonList->add(
            'import',
            ['label' => __('Import') ,
            'onclick' => 'setLocation(\'' . $this->getUrl('news/import/index') . '\')']
        );
        parent::_construct();
    }
}
