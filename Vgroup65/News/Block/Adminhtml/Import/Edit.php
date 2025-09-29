<?php
namespace Vgroup65\News\Block\Adminhtml\Import;
 
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
 
    /**
     * @param Context  $context
     * @param Registry $registry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_controller = 'adminhtml_import';
        $this->_blockGroup = 'Vgroup65_News';
 
        parent::_construct();
 
        $this->buttonList->update('save', 'label', __('Import'));
        $this->buttonList->update('back', 'onclick', "setLocation('" . $this->getUrl('news/newslist/index') . "')");
        $this->buttonList->add(
            'sample',
            ['label' => __('Sample File') ,
            'onclick' => 'setLocation(\'' . $this->getUrl('news/samplefile/index') . '\')']
        );
    }
 
    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    public function getHeaderText()
    {
        $newsRegistry = $this->_coreRegistry->registry('news_list');
        if ($newsRegistry->getId()) {
            $newsTitle = $this->escapeHtml($newsRegistry->getTitle());
            return __("Edit News '%1'", $newsTitle);
        } else {
            return __('Add News');
        }
    }
 
    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('post_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
                }
            };
        ";
 
        return parent::_prepareLayout();
    }
}
