<?php
namespace Headzoo\Core;

/**
 * Performs simple validation on values.
 */
class Validator
    extends Obj
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
            $this->toss(
                "ValidationFailedException",
                "Required values missing: {0}.",
                Arrays::conjunct($missing, 'Headzoo\Core\Strings::quote')
            );
        }
        
        return true;
    }
} 