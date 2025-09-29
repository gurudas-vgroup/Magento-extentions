<?php
namespace Vgroup65\Testimonial\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const ENABLED = 1;
    const DISABLED = 0;

    public function toOptionArray()
    {
        return [
            ['value' => self::ENABLED, 'label' => __('Enabled')],
            ['value' => self::DISABLED, 'label' => __('Disabled')],
        ];
    }
}
