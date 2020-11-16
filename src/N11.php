<?php

namespace N11;

use GuzzleHttp;

/**
 * Class N11
 * @package N11
 */
class N11
{
    protected $appKey;
    protected $appSecret;
    protected $options = [];

    private $webServicesUri = 'https://api.n11.com/ws';

    /**
     * N11 constructor.
     * @param $appKey
     * @param $appSecret
     */
    public function __construct($appKey, $appSecret, array $options = []) {
        $defaultOptions = [
            'as_array' => false
        ];

        $this->appKey = $appKey;
        $this->appSecret = $appSecret;

        $this->setOptions(\array_merge($defaultOptions, $options));
    }

    /**
     * @return mixed
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @param $appKey
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }

    /**
     * @return mixed
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * @param $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = \array_merge($this->options, $options);
    }

    /**
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchCategories()
    {
        return $this->getSoap('CategoryService', 'GetTopLevelCategories');
    }

    /**
     * @param $categoryId
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchSubCategories($categoryId)
    {
        return $this->getSoap('CategoryService', 'GetSubCategories',
            \compact('categoryId'));
    }

    /**
     * @param $categoryId
     * @param int $currentPage
     * @param null $pageSize
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchCategoryAttributesWithValues($categoryId, $currentPage = 0, $pageSize = null)
    {
        $params = \array_merge(
            \compact('categoryId'),
            [ 'pagingData' => \compact('currentPage') ]
        );

        if ($pageSize !== null) {
            $params['pagingData']['pageSize'] = $pageSize;
        }

        return $this->getSoap('CategoryService', 'GetCategoryAttributes', $params);
    }

    /**
     * @param $categoryProductAttributeId
     * @param int $currentPage
     * @param null $pageSize
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchCategoryAttributeValue($categoryProductAttributeId, $currentPage = 0, $pageSize = null)
    {
        $params = \array_merge(
            \compact('categoryProductAttributeId'),
            [ 'pagingData' => \compact('currentPage') ]
        );

        if ($pageSize !== null) {
            $params['pagingData']['pageSize'] = $pageSize;
        }

        return $this->getSoap('CategoryService', 'GetCategoryAttributeValue', $params);
    }

    /**
     * @param $categoryId
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchParentCategory($categoryId)
    {
        return $this->getSoap('CategoryService', 'GetParentCategory',
            \compact('categoryId'));
    }

    /**
     * @param $categoryId
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchCategoryAttributeList($categoryId)
    {
        return $this->getSoap('CategoryService', 'GetCategoryAttributesId',
            \compact('categoryId'));
    }

    /**
     * @return bool|float|int|mixed|string
     * @throws \SoapFault
     */
    public function fetchCities()
    {
        return $this->getSoap('CityService', 'GetCities',
            [],
            [ 'auth' => false ]);
    }

    /**
     * @param $cityCode
     * @return bool|float|int|mixed|string
     * @throws \SoapFault
     */
    public function fetchCity($cityCode)
    {
        return $this->getSoap('CityService', 'GetCity',
            \compact('cityCode'),
            [ 'auth' => false ]
        );
    }

    /**
     * @param $cityCode
     * @return bool|float|int|mixed|string
     * @throws \SoapFault
     */
    public function fetchDistrict($cityCode)
    {
        return $this->getSoap('CityService', 'GetDistrict',
            \compact('cityCode'),
            [ 'auth' => false ]
        );
    }

    /**
     * @param $districtId
     * @return bool|float|int|mixed|string
     * @throws \SoapFault
     */
    public function fetchNeighborhoods($districtId)
    {
        return $this->getSoap('CityService', 'GetNeighborhoods',
            \compact('districtId'),
            [ 'auth' => false ]
        );
    }

    /**
     * @param $currentPage
     * @param $pageSize
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchProductList($currentPage = 0, $pageSize = null)
    {
        $params = [
            'pagingData' => \compact('currentPage')
        ];

        if ($pageSize !== null) {
            $params['pagingData']['pageSize'] = $pageSize;
        }

        return $this->getSoap('ProductService', 'GetProductList', $params);
    }

    /**
     * @param $productId
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchProductById($productId)
    {
        return $this->getSoap('ProductService', 'GetProductByProductId',
            \compact('productId'));
    }

    /**
     * @param $sellerCode
     * @return mixed
     * @throws \SoapFault
     */
    public function fetchProductBySeller($sellerCode)
    {
        return $this->getSoap('ProductService', 'GetProductBySellerCode',
            \compact('sellerCode'));
    }

    /**
     * @param $service
     * @param $method
     * @param array $params
     * @return mixed
     * @throws \SoapFault
     */
    protected function getSoap($service, $method, array $params = [], array $options = [])
    {
        $defaultOptions = [
            'auth' => true
        ];

        $options = \array_merge($defaultOptions, $options);

        $uri = \sprintf('%s/%s.wsdl', $this->webServicesUri, $service);

        $client = new \SoapClient($uri, [
            'cache_wsdl' => \WSDL_CACHE_NONE,
            'trace' => false,
        ]);

        if ($options['auth']) {
            $params = \array_merge($params, $this->authParams());
        }

        $response = $client->$method($params);

        // Object to array conversion recursively
        $toArray = function($e) use(&$toArray)
        {
            return \is_scalar($e) ? $e : \array_map($toArray, (array) $e);
        };

        if (isset($this->options['as_array']) && $this->options['as_array']) {
            $response = $toArray($response);
        }

        return $response;
    }

    /**
     * @return array[]
     */
    protected function authParams()
    {
        return [
            'auth' => [
                'appKey' => $this->appKey,
                'appSecret' => $this->appSecret,
            ]
        ];
    }
}