<?php

namespace N11;

/**
 * Class N11SoapClient
 * @package N11
 */
class N11SoapClient extends \SoapClient
{
    /**
     * N11SoapClient constructor.
     * @param $wsdl
     * @param array|null $options
     * @throws N11Exception
     */
    public function __construct($wsdl, array $options = null)
    {
        try {
            parent::__construct($wsdl, $options);
        }
        catch (\SoapFault $e) {
            throw new N11Exception('İstemci oluşturulurken bir hata oluştu: %s', $e->getMessage());
        }
    }

    /**
     * @param string $function_name
     * @param array $arguments
     * @param null $options
     * @param null $input_headers
     * @param null $output_headers
     * @return mixed
     * @throws N11Exception
     */
    public function __soapCall($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null)
    {
        try {
            return parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
        }
        catch (\SoapFault $e) {
            throw new N11Exception('Fonksiyon çağrılırken bir hata oluştu: %s', $e->getMessage());
        }
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param int $one_way
     * @return string
     * @throws N11Exception
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        try {
            return parent::__doRequest($request, $location, $action, $version, $one_way);
        }
        catch (\SoapFault $e) {
            throw new N11Exception('İstekte bulunulurken bir hata oluştu: %s', $e->getMessage());
        }
    }

    /**
     * @param string $function_name
     * @param array $arguments
     * @return mixed
     * @throws N11Exception
     */
    public function __call($function_name, $arguments)
    {
        try {
            return parent::__call($function_name, $arguments);
        }
        catch (\SoapFault $e) {
            if (\strcmp($e->getMessage(), 'Forbidden') === 0) {
                throw new N11Exception('Yetkisiz giriş.');
            }
            throw new N11Exception(\sprintf('Fonksiyon çağrılırken bir hata oluştu (%s): %s', $e->faultcode, $e->getMessage()));
        }
    }
}