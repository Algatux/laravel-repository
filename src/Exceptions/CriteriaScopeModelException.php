<?php 

namespace Algatux\Repository\Exceptions;

/**
 * Class CriteriaScopeModelException
 * @package Algatux\Repository\Exceptions
 */
class CriteriaScopeModelException extends RepositoryException
{

    const MESSAGE = "%s model scope is different to che model you are applying it";

    /**
     * @param string $criteriaClassName
     */
    public function __construct($criteriaClassName)
    {
        $message = sprintf(self::MESSAGE, $criteriaClassName);

        parent::__construct($message, 0, null);
    }

}
