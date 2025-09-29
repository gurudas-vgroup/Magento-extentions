<?php

namespace Vgroup65\News\Block\Adminhtml\Newslist\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Vgroup65\News\Model\ResourceModel\News\Collection;
use Vgroup65\News\Model\System\Config\Status;

class Info extends Generic implements TabInterface {

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
    protected function _prepareForm() {
        /**
         *
         *
         * @var $model \Tutorial\SimpleNews\Model\News
         */
        $model = $this->_coreRegistry->registry('news_list');

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
                ['legend' => __('General')]
        );

        //get news category list @var Vgroup65\News\Model\ResourceModel\News\Collection
        $newsCategory = $this->_newsCategoryCollection;
        $newsCategory->addFieldToFilter('status', '1');
        $newsCategoryCollection = [];
        foreach ($newsCategory as $key => $category):
            $newsCategoryCollection[$key]['value'] = $category->getCategoryId();
            $newsCategoryCollection[$key]['label'] = $category->getTitle();
        endforeach;

        if ($model->getId()) {
            $fieldset->addField(
                    'news_id',
                    'hidden',
                    ['name' => 'news_id']
            );
        }

        $fieldset->addField(
                'title',
                'text',
                [
                    'name' => 'title',
                    'label' => __('Title'),
                    'required' => true,
                ]
        );
 $fieldset->addField(
                'publish_date',
                'date',
                [
                    'name' => 'publish_date',
                    'label' => __('Publish Date'),
                    'required' => false,
                    'date_format' => 'MM/dd/yyyy',
                    'time_format' => 'HH:mm:ss',
                    'image' => $this->getViewFileUrl('Magento_Theme::calendar.png'),
                    'class' => 'admin__control-text',
                    'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                     'container_classes' => 'admin__field-date'
                ]
        );
        $fieldset->addField(
                'category_id',
                'multiselect',
                [
                    'name' => 'category_id',
                    'label' => __('Select Category'),
                    'title' => __('Category URL'),
                    'values' => $newsCategoryCollection,
                    'required' => true,
                ]
        );

        $fieldset->addField(
                'url_identifier',
                'text',
                [
                    'name' => 'url_identifier',
                    'label' => __('Url Identifier'),
                    'required' => true,
                ]
        );
        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $fieldset->addField(
                'description',
                'editor',
                [
                    'name' => 'description',
                    'label' => __('Description'),
                    'required' => true,
                    'config' => $wysiwygConfig,
                ]
        );
        $fieldset->addType('image', 'Vgroup65\News\Block\Adminhtml\Helper\Image\Required');
        $fieldset->addField(
                'image',
                'image',
                [
                    'title' => __('Image'),
                    'label' => __('Image'),
                    'id' => 'image',
                    'name' => 'image',
                    'note' => '<b>Allow image type</b>: jpg, jpeg, gif, png <br/>'
                    . '<b>Maximum Size</b> : 8MB',
                ]
        );

       

        $fieldset->addField(
                'store_view',
                'multiselect',
                [
                    'name' => 'store_view',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(),
                ]
        );
        $fieldset->addField(
                'status',
                'select',
                [
                    'name' => 'status',
                    'label' => __('Status'),
                    'options' => $this->_newsStatus->toOptionArray(),
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
    public function getTabLabel() {
        return __('News Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __('News Info');
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
