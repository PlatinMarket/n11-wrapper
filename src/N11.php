<?php

namespace N11;

/**
 * N11 SOAP API PHP Wrapper
 */
class N11
{
    /**
     * @var string N11 API key
     */
    protected $_appKey;

    /**
     * @var string N11 API secret
     */
    protected $_appSecret;

    /**
     * @var array Global options of the class
     */
    protected $_options = [];

    /**
     * @var string N11 SOAP API service location
     */
    private $_webServicesUri = 'https://api.n11.com/ws';

    /**
     * The N11 API credentials should be passed to the constructor.
     * Third parameter can be an options array optionally. If 'as_array'
     * option set as true, all received response elements will be array
     * recursively; in other case, the child elements will be stdClass.
     *
     * Example usage:
     *
     *      $n11 = new N11(
     *          '<APP_KEY>',
     *          '<APP_SECRET>',
     *          $options = [
     *              'as_array' => true,
     *          ]
     *      );
     *
     * @param string $appKey N11 API key
     * @param string $appSecret N11 API secret
     */
    public function __construct($appKey, $appSecret, array $options = [])
    {
        $defaultOptions = [
            'as_array' => false,
        ];

        $this->_appKey = $appKey;
        $this->_appSecret = $appSecret;

        $this->setOptions(\array_merge($defaultOptions, $options));
    }

    /**
     * API key getter
     *
     * @return string
     */
    public function getAppKey()
    {
        return $this->_appKey;
    }

    /**
     * API key setter
     *
     * @param string $appKey
     */
    public function setAppKey($appKey)
    {
        $this->_appKey = $appKey;
    }

    /**
     * API secret getter
     *
     * @return string
     */
    public function getAppSecret()
    {
        return $this->_appSecret;
    }

    /**
     * API secret setter
     *
     * @param string $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->_appSecret = $appSecret;
    }

    /**
     * Global class options getter
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Global class options setter
     *
     * @param $options
     */
    public function setOptions($options)
    {
        $this->_options = \array_merge($this->_options, $options);
    }

    /**
     * Get all top level N11 categories
     *
     * @return array
     * @throws N11Exception
     */
    public function fetchCategories()
    {
        return $this->_soapRequest('CategoryService', 'GetTopLevelCategories');
    }

    /**
     * Get all sub N11 categories by an Id of the parent category
     *
     * @param $categoryId
     * @return array
     * @throws N11Exception
     */
    public function fetchSubCategories($categoryId)
    {
        return $this->_soapRequest('CategoryService', 'GetSubCategories',
            \compact('categoryId'));
    }

    /**
     * Get detailed N11 category attributes by category Id
     *
     * @param $categoryId
     * @param int $currentPage
     * @param null $pageSize
     * @return array
     * @throws N11Exception
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

        return $this->_soapRequest('CategoryService', 'GetCategoryAttributes', $params);
    }

    /**
     * Get N11 category attribute values by category attribute Id
     *
     * @param $categoryProductAttributeId
     * @param int $currentPage
     * @param null $pageSize
     * @return array
     * @throws N11Exception
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

        return $this->_soapRequest('CategoryService', 'GetCategoryAttributeValue', $params);
    }

    /**
     * Get the parent N11 category by category Id
     *
     * @param $categoryId
     * @return array
     * @throws N11Exception
     */
    public function fetchParentCategory($categoryId)
    {
        return $this->_soapRequest('CategoryService', 'GetParentCategory',
            \compact('categoryId'));
    }

    /**
     * Get N11 category attribute list by category Id
     *
     * @param $categoryId
     * @return mixed
     * @throws N11Exception
     */
    public function fetchCategoryAttributeList($categoryId)
    {
        return $this->_soapRequest('CategoryService', 'GetCategoryAttributesId',
            \compact('categoryId'));
    }

    /**
     * Get all cities
     *
     * @return array
     * @throws N11Exception
     */
    public function fetchCities()
    {
        return $this->_soapRequest('CityService', 'GetCities',
            [],
            [ 'auth' => false ]);
    }

    /**
     * Get city data by city code
     *
     * @param $cityCode
     * @return array
     * @throws N11Exception
     */
    public function fetchCity($cityCode)
    {
        return $this->_soapRequest('CityService', 'GetCity',
            \compact('cityCode'),
            [ 'auth' => false ]
        );
    }

    /**
     * Get all districts by city code
     *
     * @param $cityCode
     * @return array
     * @throws N11Exception
     */
    public function fetchDistricts($cityCode)
    {
        return $this->_soapRequest('CityService', 'GetDistrict',
            \compact('cityCode'),
            [ 'auth' => false ]
        );
    }

    /**
     * Get neighbor districts by district Id
     *
     * @param $districtId
     * @return array
     * @throws N11Exception
     */
    public function fetchNeighborhoods($districtId)
    {
        return $this->_soapRequest('CityService', 'GetNeighborhoods',
            \compact('districtId'),
            [ 'auth' => false ]
        );
    }

    /**
     * Get the N11 product list of the account
     *
     * @param $currentPage
     * @param $pageSize
     * @return array
     * @throws N11Exception
     */
    public function fetchProductList($currentPage = 0, $pageSize = null)
    {
        $params = [
            'pagingData' => \compact('currentPage')
        ];

        if ($pageSize !== null) {
            $params['pagingData']['pageSize'] = $pageSize;
        }

        return $this->_soapRequest('ProductService', 'GetProductList', $params);
    }

    /**
     * Get product details by product Id
     * @param string $productId
     * @return array
     * @throws N11Exception
     */
    public function fetchProductById($productId)
    {
        return $this->_soapRequest('ProductService', 'GetProductByProductId',
            \compact('productId'));
    }

    /**
     * Get product details by its own merchant code
     *
     * @param string $sellerCode
     * @return array
     * @throws N11Exception
     */
    public function fetchProductBySeller($sellerCode)
    {
        return $this->_soapRequest('ProductService', 'GetProductBySellerCode',
            \compact('sellerCode'));
    }

    /**
     * Save product by passing an array data
     *
     * Example usage:
     *
     *      $n11->saveProduct([
     *           'product' => [
     *               'productSellerCode' => 'Test001',
     *               'title' => 'Örnek Başlık',
     *               'subtitle' => 'Örnek Altbaşlık',
     *               'description' => 'Örnek açıklama',
     *               'category' => [
     *                   'id' => 999999
     *               ],
     *               'price' => 99.00,
     *               'domestic' => true,
     *               'currencyType' => 1,
     *               'images' => [
     *                  'image' => [
     *                      [
     *                          'url' => 'https://picsum.photos/1024/1024',
     *                          'order' => 1
     *                      ]
     *                  ],
     *               ],
     *               'approvalStatus' => 'WaitingForApproval',
     *               'attributes' => [
     *                  'attribute' => [
     *                      [
     *                          'name' => 'Marka',
     *                          'value' => 'Diğer'
     *                      ],
     *                      [
     *                          'name' => 'Aroma',
     *                          'value' => 'Sade'
     *                      ],
     *                  ]
     *               ],
     *               'saleStartDate' => date('d/m/Y', strtotime('-1 year')),
     *               'saleEndDate' => date('d/m/Y', strtotime('+10 years')),
     *               'productionDate' => date('d/m/Y'),
     *               'expirationDate' => date('d/m/Y', strtotime('+1 years')),
     *               'productCondition' => 1,
     *               'preparingDay' => 3,
     *               'discount' => [
     *                  'startDate' => null,
     *                  'endDate' => null,
     *                  'type' => null,
     *                  'value' => null,
     *               ],
     *               'shipmentTemplate' => 'Örnek Kargo',
     *               'stockItems' => [
     *                      'stockItem' => [
     *                      'quantity' => 5,
     *                      'gtin' => 9999999999999,
     *                      'sellerStockCode' => 'OrnekStokKodu-1',
     *                      'n11CatalogId' => null,
     *                      'attributes' => [
     *                          'attribute' => [
     *                              [
     *                                  'name' => 'Marka',
     *                                  'value' => 'Diğer'
     *                              ],
     *                          ],
     *                      ],
     *                      'optionPrice' => null,
     *                  ],
     *               ],
     *               'groupAttribute' => null,
     *               'groupItemCode' => null,
     *               'itemName' => null,
     *               'unitInfo' => null
     *               'specialProductInfoList' => null,
     *           ],
     *       ]);
     *
     * @param array $data
     * @return array
     * @throws N11Exception
     */
    public function saveProduct(array $data)
    {
        return $this->_soapRequest('ProductService', 'SaveProduct', $data);
    }

    /**
     * A simple SOAP wrapper to send request to and receive response from N11 API
     *
     * Example usage:
     *
     *      $this->_soapRequest('CategoryService', 'GetSubCategories', [
     *          categoryId => '<CATEGORY_ID>'
     *      ]);
     *
     * @param string $serviceName N11 SOAP service name
     * @param string $methodName N11 SOAP method name
     * @param array $params
     * @return array
     * @throws N11Exception
     */
    protected function _soapRequest($serviceName, $methodName, array $params = [], array $options = []): array
    {
        $defaultOptions = [
            'auth' => true,
        ];

        $options = \array_merge($defaultOptions, $options);

        if ($options['auth']) {
            $params = \array_merge($params, $this->_authParams());
        }

        $uri = \sprintf('%s/%s.wsdl', $this->_webServicesUri, $serviceName);

        $client = new N11SoapClient($uri, [
            'cache_wsdl' => \WSDL_CACHE_NONE,
            'trace' => false,
        ]);

        $response = $client->$methodName($params);

        // Object to array conversion recursively
        $toArray = function($e) use(&$toArray)
        {
            return \is_scalar($e) ? $e : \array_map($toArray, (array) $e);
        };

        if (isset($this->_options['as_array']) && $this->_options['as_array']) {
            $response = $toArray($response);
        }

        return (array) $response;
    }

    /**
     * Get auth parameters dynamically in a special format to be able to send request to N11 API
     *
     * @return array
     */
    protected function _authParams()
    {
        return [
            'auth' => [
                'appKey' => $this->_appKey,
                'appSecret' => $this->_appSecret,
            ],
        ];
    }
}