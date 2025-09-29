<?php
namespace Vgroup65\Testimonial\Ui\Component;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Vgroup65\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}
