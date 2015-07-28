<?php

namespace Algatux\Repository\Managers;

use Illuminate\Http\Request;

/**
 * Class ParamsManager
 * @package Algatux\Repository\Managers
 */
class ParamsManager
{

    const PAGINATION_STRING = 'page';

    /** @var Request */
    protected $request;

    /** @var string */
    private $paginationParamString;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->init();
    }

    /**
     * @return string|0
     */
    public function getPaginationParams()
    {
        return $this->request->get($this->paginationParamString, false);
    }

    /**
     * @param $paginationParamString
     */
    public function setPaginationParamString($paginationParamString)
    {
        if (!is_string($paginationParamString) or strlen($paginationParamString) < 1) {
            throw new \InvalidArgumentException('Invalid paginationParamString, expected string');
        }

        $this->paginationParamString = $paginationParamString;
    }

    /**
     * Initialization
     */
    private function init()
    {
        $this->paginationParamString = self::PAGINATION_STRING;
    }

}
