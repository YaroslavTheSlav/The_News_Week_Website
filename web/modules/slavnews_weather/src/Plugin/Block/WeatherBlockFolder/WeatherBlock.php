<?php

namespace Drupal\slavnews_weather\Plugin\Block\WeatherBlockFolder;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Provides a 'Weather' Block.
 *
 * @Block(
 *   id = "weather_block",
 *   admin_label = @Translation("Weather Block"),
 *   category = @Translation("My Weather"),
 * )
 */
class WeatherBlock extends BlockBase implements ContainerFactoryPluginInterface {


  /**
   * Provides user data.
   *
   * @var \GuzzleHttp\Client
   */
  private $client;
  /**
   * Provides request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  private $request;
  /**
   * CacheBackendInterface object.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * The config I want to get.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * Constructing requests and other using dependency injection.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $cache_backend, ConfigFactory $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->client = new Client();
    $this->request = new Request();
    $this->cacheBackend = $cache_backend;
    $this->config = $config_factory->get('AdminWeather.settings');
  }

  /**
   * Creating container function that gets default cache.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('cache.default'),
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   *
   * @todo use dependency injection instead of phpcs:ignore.
   * Building function.
   */
  public function build() {
    $data = $this->getWeather();

    return [
      '#theme' => 'slavnews__weather__block',
      '#image' => $data['current']['condition']['icon'],
      '#temp_c' => $data['current']['temp_c'],
      '#city' => $data['location']['name'],
    ];
  }

  /**
   * Receive all weather data to display.
   */
  public function getWeather() {
    // Function that request's user geological location by ip.
    $location = $this->getIplocation();
    $this->cidMachineName($location);
    // Setting cid for cache that is saved.
    // If we have data in cache we do following.
    if ($cache = $this->cacheBackend->get($this->cidMachineNameVar)) {
      // Get data from cache.
      return $cache->data;
    }
    // If we don't have data in cache we set it there.
    // phpcs:ignore
    elseif (\Drupal::config('AdminWeather.settings')->get('AdminWeather.settings.weatherapi_token') != NULL) {
      // Get token from config file.
      // phpcs:ignore
      $weatherToken = \Drupal::config('AdminWeather.settings')->get('AdminWeather.settings.weatherapi_token');
      // Full url.
      $weather_url = 'http://api.weatherapi.com/v1/current.json?key=' . $weatherToken . '&q=' . $location . '&aqi=no';
      // (pulling json data)
      $response = $this->client->get($weather_url);
      // Gettign all data from json.
      $fullWeatherdata = json_decode($response->getBody($this->method), TRUE);
      // Call function that caches.
      $this->myCachingfunction($fullWeatherdata);

      // After we get requested data we return it.
      return $fullWeatherdata;
    }
    return [];
  }

  /**
   * Validate and receive user IP.
   */
  public function getIplocation() {
    // phpcs:ignore
//    $user = $this->dbNuser();
    // Request user Ip.
    // phpcs:ignore
    $ip = \Drupal::request()->getClientIp();
    $this->method = 'GET';
    // If Ip is local we set default city.
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
      // (reverting to default if ip is local variable)
      // phpcs:ignore
      return \Drupal::config('AdminWeather.settings')->get('AdminWeather.settings.admin_city') ?? NULL;
    }
    // If Ip is not local do next if.
    else {
      // We request ipFind token from config, get ip and return...
      // ready to use user geological location.
      if ($this->config->get('AdminWeather.settings.ipfind_token') != NULL) {
        $ip_token = $this->config->get('AdminWeather.settings.ipfind_token');
        $ip_api_url = 'https://api.ipfind.com?ip=' . $ip . '&auth=' . $ip_token;
        $response = $this->client->get($ip_api_url);
        $ipData = json_decode($response->getBody($this->method), TRUE);
        // Return user geological location data.
        $location = $ipData['country'] . ', ' . $ipData['city'];
        return $location;
      }
      // If we dont have IpFind token we just return default city.
      // phpcs:ignore
      return \Drupal::config('AdminWeather.settings')->get('AdminWeather.settings.admin_city') ?? NULL;
    }
  }

  /**
   * Function that puts requested data into cache.
   */
  private function myCachingfunction($fullWeatherdata) {

    // Cid.
    $cacheId = $this->cidMachineNameVar;
    // Getting cache saved data into db.
    $this->cacheBackend->set($cacheId, $fullWeatherdata, time() + 3600);
  }

  /**
   * Function used to form machine name for cid.
   *
   * @param string $locationImport
   *   String to make the normal machine name from it.
   *
   *   Example - "Lutsk, Ukraine" => "weather.data.lutsk_ukraine".
   */
  private function cidMachineName($locationImport) {
    // Replace all that are not letter into machone like name.
    $this->cidMachineNameVar = 'slavwnews_weather_' . strtolower(preg_replace('/([^a-zA-Z]+)/', '_', $locationImport));
  }

}
