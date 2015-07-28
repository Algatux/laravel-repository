<?php

namespace Algatux\Repository\Exceptions;

/**
 * Class ModelClassNameException
 * @package Algatux\Repository\Exceptions
 */
class ModelClassNameException extends RepositoryException
{

    const MESSAGE = "Class %s does not exist";

    /**
     * @param string $model
     */
    public function __construct($model)
    {
        $message = sprintf(self::MESSAGE, $model);

        parent::__construct($message, 0, null);
    }

}
