<?php


namespace N11;

use Throwable;

/**
 * Class N11Exception
 *
 * @package N11
 */
class N11Exception extends \Exception
{
    /**
     * @var \stdClass
     */
    public $response;

    /**
     * N11Exception constructor.
     * @param \Exception $e
     * @param \stdClass|null $response
     */
    public function __construct(\Exception $e, \stdClass $response = null)
    {
        parent::__construct($e->getMessage(), $e->getCode(), $e);
        $this->response = $response;
    }
}