<?php
namespace Headzoo\Utilities;
use Exception;

/**
 * Performs simple validation on values.
 */
class Validator
    extends Core
    implements ValidatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function validateRequired(array $values, array $required, $allowEmpty = false)
    {
        if (!$allowEmpty) {
            $values = array_filter($values);
        }
        $missing = array_diff($required, array_keys($values));
        if (!empty($missing)) {
            $this->throwException(
                "ValidationFailedException",
                "Required values missing: {0}.",
                Arrays::conjunct($missing, 'Headzoo\Utilities\Strings::quote')
            );
        }
        
        return true;
    }
} 