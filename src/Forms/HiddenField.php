<?php

namespace SilverStripe\Forms;

/**
 * Hidden field.
 */
class HiddenField extends FormField
{

    protected $schemaDataType = FormField::SCHEMA_DATA_TYPE_HIDDEN;

    /**
     * @param array $properties
     * @return string
     */
    public function FieldHolder($properties = array())
    {
        return $this->Field($properties);
    }

    /**
     * @return static
     */
    public function performReadonlyTransformation()
    {
        $clone = clone $this;

        $clone->setReadonly(true);

        return $clone;
    }

    /**
     * @return bool
     */
    public function IsHidden()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return array_merge(
            parent::getAttributes(),
            array(
                'type' => 'hidden',
            )
        );
    }

    public function SmallFieldHolder($properties = array())
    {
        return $this->FieldHolder($properties);
    }
}
