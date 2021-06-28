<?php
namespace Emageshop\Twitch\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Custom Module Email helper
 */
class Twitch extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TOKEN_URL = 'https://id.twitch.tv/oauth2/token';
    const CHANNELS_URL = 'https://api.twitch.tv/helix/search/channels';

    protected $_curl;

    protected $_scopeConfig;

    public function __construct(
        Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_curl = $curl;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function getToken()
    {
        $params = [
            'client_id' => $this->getConfig('twitch/general/client_id'),
            'client_secret' => $this->getConfig('twitch/general/client_secret'),
            'grant_type' => 'client_credentials',
        ];

        $this->_curl->post(self::TOKEN_URL, $params);


        $result = json_decode($this->_curl->getBody(), true);

        return $result;
    }

    public function getLiveStream($query)
    {
        $token = $this->getToken();
        $headers = [
            "client-id" => $this->getConfig('twitch/general/client_id'),
            "Authorization" => "Bearer " . $token['access_token']
        ];
        $this->_curl->setHeaders($headers);
        $this->_curl->get(self::CHANNELS_URL . '?query=' . $query);

        $result = json_decode($this->_curl->getBody(), true);

        if (isset($result['data'])){
            $key = array_search(true, array_column($result['data'], 'is_live'));
            if ($key){
                return $result['data'][$key];
            }
        }

        return false;
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
