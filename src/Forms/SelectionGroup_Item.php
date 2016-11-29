<?php

namespace SilverStripe\Forms;

class SelectionGroup_Item extends CompositeField
{

    /**
     * @var String
     */
    protected $value;

    /**
     * @var String
     */
    protected $title;

    /**
     * @param String $value Form field identifier
     * @param FormField|array $fields Contents of the option
     * @param String $title Title to show for the radio button option
     */
    public function __construct($value, $fields = null, $title = null)
    {
        $this->setValue($value);
        if ($fields && !is_array($fields)) {
            $fields = array($fields);
        }

        parent::__construct($fields);

        $this->setTitle($title ?: $value);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($Value)
    {
        $this->value = $Value;
        return $this;
    }
}
