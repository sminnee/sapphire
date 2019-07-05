<?php

namespace SilverStripe\Forms;

use SilverStripe\Core\Convert;

/**
 * The readonly class for our {@link TimeField}.
 */
class TimeField_Readonly extends TimeField
{

    protected $readonly = true;


    private $valueObj = true;

    public function Field($properties = array())
    {
        $localised = $this->internalToFrontend($this->value);
        $val = $localised ? Convert::raw2xml($localised) : '<i>(not set)</i>';

        return "<span class=\"readonly\" id=\"" . $this->ID() . "\">$val</span>";
    }
}
