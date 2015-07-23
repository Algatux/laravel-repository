<?php 

namespace Algatux\Repository\Exceptions;

/**
 * Class CriteriaNameNotStringException
 * @package Algatux\Repository\Exceptions
 */
class CriteriaNameNotStringException extends RepositoryException
{

    const MESSAGE = "%s method criteriaName must return a string";

    /**
     * @param string $criteriaClassName
     */
    public function __construct($criteriaClassName)
    {
        $message = sprintf(self::MESSAGE, $criteriaClassName);

        parent::__construct($message, 0, null);
    }

}
