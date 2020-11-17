<?php


namespace N11;

/**
 * Class N11Exception
 *
 * @package N11
 */
class N11Exception extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'Bilinmeyen hata.';

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s: [%s]: %s\n", __CLASS__, $this->code, $this->message);
    }
}