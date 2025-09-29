<?php
namespace Vgroup65\News\Block\Adminhtml\Newsconfiguration\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Vgroup65\News\Model\System\Config\RotateStatus;
use Vgroup65\News\Model\ResourceModel\Newslist\Collection;
 
class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
 
    /**
     * @var \Tutorial\SimpleNews\Model\Config\Status
     */
    protected $_rotateStatus;
    
    protected $_newsListFactory;
 
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
        RotateStatus $rotateStatus,
        Collection $collection,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_rotateStatus = $rotateStatus;
        $this->_newsListFactory = $collection;
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
 * @var $model \Tutorial\SimpleNews\Model\News
*/
        $model = $this->_coreRegistry->registry('news_config');
 
        /**
*
         *
 * @var \Magento\Framework\Data\Form $form
*/
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('news_');
        $form->setFieldNameSuffix('news');
        
        //count of news
        $newsCount = $this->_newsListFactory->count();
 
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );
 
        if ($model->getId()) {
            $fieldset->addField(
                'news_config',
                'hidden',
                ['name' => 'news_config']
            );
        }
        // validate-digits-range digits-range-0-'.$newsCount
        $fieldset->addField(
            'no_of_news',
            'text',
            [
                'name'        => 'no_of_news',
                'label'    => __('No. of News to Display in Widget'),
                'required'   => false,
                'class' => 'required-entry validate-number'
            ]
        );
        $fieldset->addField(
            'auto_rotate',
            'select',
            [
                'name'      => 'auto_rotate',
                'label'     => __('Auto Rotation in Widget'),
                'options'   => $this->_rotateStatus->toOptionArray()
            ]
        );
        $fieldset->addField(
            'top_menu_text',
            'text',
            [
                'name'      => 'top_menu_text',
                'label'     => __('Top Navigation Menu Text'),
                'class' => 'required-entry'
            ]
        );
        $fieldset->addField(
            'display_top_menu',
            'select',
            [
                'name'      => 'display_top_menu',
                'label'     => __('Display News Link in Top Navigation'),
                'options'   => $this->_rotateStatus->toOptionArray()
            ]
        );
        
        
        $data = $model->getData();
        $form->setValues($data);
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
