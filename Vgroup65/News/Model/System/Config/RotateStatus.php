<?php
 
namespace Vgroup65\News\Model\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
 
class RotateStatus implements ArrayInterface
{
    const YES  = 1;
    const NO = 0;
 
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::YES => __('Yes'),
            self::NO => __('No')
        ];
 
        return $options;
    }
}
