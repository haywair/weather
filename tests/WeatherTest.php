<?php


namespace Haywari\Weather\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use Haywari\Weather\Exceptions\InvalidArgumentException;
use Haywari\Weather\Exceptions\HttpException;
use Haywari\Weather\Weather;
use PHPUnit\Framework\TestCase;

class WeatherTest extends TestCase
{

    public function testGetWeather()
    {
        $response = new Response(200, [], '{"success":true}');

        $client = \Mockery::mock(Client::class);

        $client->allows()->get("https://restapi.amap.com/v3/weather/weatherInfo", [
            'query' => [
                'key' => 'mock-key',
                'city' => '深圳',
                'output' => 'json',
                'extensions' => 'base'
            ]
        ])->andReturn($response);

        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client); // $client 为上面创建的模拟实例。

        // 然后调用 `getWeather` 方法，并断言返回值为模拟的返回值。
        $this->assertSame(['success' => true], $w->getWeather('深圳'));



    }
    public function testGetWeatherWithInvalidType()
    {
        $w = new Weather('mock-key');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid arguments type: foo');

        $w->getWeather('深圳', 'foo');

        $this->fail("FAILED TO ASSERT GETWEATHER  throw exception with invalid argument.");
    }

    public function testGetWeatherWithInvalidFormat()
    {
        $w = new Weather('mock-key');

        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage("Invalid response format: array");

        $w->getWeather("深圳", 'base', 'array');

        $this->fail("FAILED TO ASSERT GET weather throw invalid format");
    }
}