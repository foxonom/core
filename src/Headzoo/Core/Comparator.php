<?php
namespace Headzoo\Core;

/**
 * Used to make comparisons between values.
 * 
 * Primarily used when a comparison needs to be made between two values
 * with a callback.
 */
class Comparator
{
    /**
     * Returns the order of the left and right hand values
     *
     * Compares its two arguments for order. Returns a negative integer, zero, or a positive integer as
     * the first argument is less than, equal to, or greater than the second.
     * 
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return int
     */
    public function compare($left, $right)
    {
        if ($left < $right) {
            $result = -1;
        } else if ($left > $right) {
            $result = 1;
        } else {
            $result = 0;
        }
        
        return $result;
    }
    
    /**
     * Returns whether two values are equal
     * 
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isEquals($left, $right)
    {
        return $left == $right;
    }

    /**
     * Returns whether two values are not equal
     *
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isNotEquals($left, $right)
    {
        return $left != $right;
    }

    /**
     * Returns whether two values are equal using strict operation
     *
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isStrictlyEquals($left, $right)
    {
        return $left === $right;
    }

    /**
     * Returns whether two values are not equal using strict operation
     *
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isStrictlyNotEquals($left, $right)
    {
        return $left !== $right;
    }

    /**
     * Returns whether the left hand value is less than the right hand value
     *
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isLessThan($left, $right)
    {
        return $left < $right;
    }

    /**
     * Returns whether the left hand value is less than or equal to the right hand value
     *
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isLessThanOrEquals($left, $right)
    {
        return $left <= $right;
    }

    /**
     * Returns whether the left hand value is greater than the right hand value
     *
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isGreaterThan($left, $right)
    {
        return $left > $right;
    }

    /**
     * Returns whether the left hand value is greater than or equal to the right hand value
     *
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isGreaterThanOrEquals($left, $right)
    {
        return $left >= $right;
    }

    /**
     * Returns whether the left hand value is an instance of the right hand value
     *
     * @param  mixed $left  The left value
     * @param  mixed $right The right value
     *
     * @return bool
     */
    public function isInstanceOf($left, $right)
    {
        return $left instanceof $right;
    }
} 