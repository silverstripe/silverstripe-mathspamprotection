<?php

/**
 * @package mathspamprotection
 */

class MathSpamProtector implements SpamProtector
{
    /**
     * Returns the {@link MathSpamProtectorField} associated with this protector
     *
     * @return MathSpamProtectorField
     */
    public function getFormField($name = null, $title = null, $value = null)
    {
        return new MathSpamProtectorField($name, $title, $value);
    }

    /**
     * Not used by MathSpamProtector
     */
    public function setFieldMapping($fieldMapping)
    {
    }
}
