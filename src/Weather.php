<?php


namespace Haywari\Weather;


use GuzzleHttp\Client;
use Haywari\Weather\Exceptions\Exception;
use Haywari\Weather\Exceptions\HttpException;

class Weather
{
    protected $key;
    protected $guzzleOptions = [];
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleHttpClient(array $guzzleOptions)
    {
        $this->guzzleOptions = $guzzleOptions;
    }

    public function getWeather($city, $type="base", $format="json")
    {
        $url = "https://restapi.amap.com/v3/weather/weaterInfo";

        if (!in_array($format, ['json', 'xml'])) {
            throw new \Haywari\Weather\Exceptions\InvalidArgumentException('Invalid argument format');
        }

        if (!in_array(strtolower($type), ['base', 'all'])) {
            throw new \Haywari\Weather\Exceptions\InvalidArgumentException('Invalid argument type');
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => $format,
            'extensions' => $type
        ]);
        try {
            $response = $this->getHttpClient()->get($url, [
                'query'=>$query
            ])->getBody()->getContents();
            return 'json' === $format ? \json_decode($response, true):$response;
        }catch(Exception $e) {
           throw new HttpException($e->getMessage(), $e->getCode());
        }


    }


}