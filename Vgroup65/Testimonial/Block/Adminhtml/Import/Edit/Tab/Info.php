<?php

namespace Vgroup65\Testimonial\Block\Adminhtml\Import\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Store\Model\System\Store;

class Info extends Generic implements TabInterface {

    protected $_wysiwygConfig;
    protected $_systemStore;

    public function __construct(
    Context $context, Registry $registry, FormFactory $formFactory, Config $wysiwygConfig, Store $systemStore, array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm() {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('testimonial_');
        $form->setFieldNameSuffix('testimonial');

        $fieldset = $form->addFieldset(
                'base_fieldset', ['legend' => __('Import Testimonials')]
        );



        $fieldset->addField(
                'file', 'file', [
            'title' => __('File'),
            'label' => __('File'),
            'name' => 'file',
            'required' => true,
            'note' => '<a href="' . $this->getUrl('testimonials/samplefile/index') . '">Click to download sample file</a>',
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
    public function getTabLabel() {
        return __('Testimonial Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __('Testimonial Info');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return false;
    }

}
