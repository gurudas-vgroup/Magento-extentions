<?php
namespace Vgroup65\Testimonial\Block\Adminhtml\Testimonial;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;

class Grid extends WidgetGrid
{
    protected function _prepareCollection()
    {
        $collection = $this->_objectManager
            ->create(\Vgroup65\Testimonial\Model\ResourceModel\Testimonial\Collection::class);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('testimonial_id', [
            'header' => __('ID'),
            'index'  => 'testimonial_id',
            'type'   => 'number'
        ]);

        $this->addColumn('first_name', [
            'header' => __('First Name'),
            'index'  => 'first_name',
        ]);

        $this->addColumn('last_name', [
            'header' => __('Last Name'),
            'index'  => 'last_name',
        ]);

        $this->addColumn('status', [
            'header'  => __('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'options' => [
                1 => __('Enabled'),
                0 => __('Disabled')
            ]
        ]);

        return parent::_prepareColumns();
    }
}
