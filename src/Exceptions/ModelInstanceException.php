<?php 

namespace Algatux\Repository\Exceptions;

/**
 * Class ModelInstanceException
 * @package Algatux\Repository\Exceptions
 */
class ModelInstanceException extends RepositoryException
{

    const MESSAGE = "Class %s is not an instance of Model";

    /**
     * @param string $model
     */
    public function __construct($model)
    {
        $message = sprintf(self::MESSAGE, $model);

        parent::__construct($message, 0, null);
    }

}
