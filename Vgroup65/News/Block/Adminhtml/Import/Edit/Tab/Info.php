<?php
namespace Vgroup65\News\Block\Adminhtml\Import\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Vgroup65\News\Model\System\Config\Status;
use Magento\Store\Model\System\Store;
use Vgroup65\News\Model\ResourceModel\News\Collection;
 
class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
 
    /**
     * @var \Tutorial\SimpleNews\Model\Config\Status
     */
    protected $_newsStatus;
    
    protected $_systemStore;
    
    protected $_newsCategoryCollection;
 
    /**
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     * @param Config      $wysiwygConfig
     * @param Status      $newsStatus
     * @param array       $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $newsStatus,
        Store $systemStore,
        Collection $Collection,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_newsStatus = $newsStatus;
        $this->_systemStore = $systemStore;
        $this->_newsCategoryCollection = $Collection;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /**
*
         *
 * @var \Magento\Framework\Data\Form $form
*/
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('news_');
        $form->setFieldNameSuffix('news');
       
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('News Import')]
        );
        
        $fieldset->addField(
            'file',
            'file',
            [
                'title'     =>__('File'),
                'label'     =>__('File'),
                'name'      => 'file',
                'required'  => true,
                'note'      => 'Note: <br><br>a) Allowed file type: CSV  <br> b) While Importing Fresh news, leave the News ID column blank in the CSV file.',
            ]
        );

        //$form->setValues($data);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
 
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('News Info');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('News Info');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
