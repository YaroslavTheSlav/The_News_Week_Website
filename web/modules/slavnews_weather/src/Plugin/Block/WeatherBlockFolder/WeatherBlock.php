<?php

namespace Drupal\slavnews_weather\Plugin\Block\WeatherBlockFolder;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

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
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructing requests and other using dependency injection.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $cache_backend, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->client = new Client();
    $this->request = new Request();
    $this->cacheBackend = $cache_backend;
    $this->configFactory = $config_factory;
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
    );
  }

  /**
   * Config zones configuration.
   */
  protected function getZonesFromConfig($requestedParameter) {
    $config = $this->configFactory->get('AdminWeather.settings');
    $returnedParameter = $requestedParameter . '.';
    return $config->get($returnedParameter);
  }

  /**
   * {@inheritdoc}
   *
   * @todo use dependency injection instead of phpcs:ignore.
   * Building function.
   */
  public function build() {
    $requestedWeatherData = $this->getWeather();
    return $requestedWeatherData;
  }

  /**
   * Receive all weather data to display.
   */
  public function getWeather() {
    // Setting cid for cache that is saved.
    $cacheId = "slavwnews_weather";
    // Function that request's user geological location by ip.
    $location = $this->getIplocation();

    // If we have data in cache we do following.
    if ($cache = $this->cacheBackend->get($cacheId)) {
      // Get data from cache.
      $data = $cache->data;
      // Return data.
      return [
        '#theme' => 'slavnews__weather__block',
        '#image' => $data['current']['condition']['icon'],
        '#temp_c' => $data['current']['temp_c'],
        '#city' => $data['location']['name'],
      ];
    }
    // If we dont have data in cache we set it there.
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
      $cachedData = $this->myCachingfunction($fullWeatherdata);

      // After we get requested data we return it.
      return $cachedData;
    }
    return [];
  }

  /**
   * Validate and receive user IP.
   */
  public function getIplocation() {
    $user = $this->dbNuser();
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
      if ($ip_token = $this->getZonesFromConfig('ipfind_token') != NULL) {
        $ip_api_url = 'https://api.ipfind.com?ip=' . $ip . '&auth=' . $ip_token;
        $response = $this->client->get($ip_api_url);
        $ipData = json_decode($response->getBody($this->method), TRUE);
        // Return user geological location data.
        return $ipData['country'] . ', ' . $ipData['city'];
      }
      // If we dont have IpFind token we just return default city.
      // phpcs:ignore
      return \Drupal::config('AdminWeather.settings')->get('AdminWeather.settings.admin_city') ?? NULL;
    }
  }

  /**
   * Function that inputs user id and hil location into data Base.
   */
  private function dbNuser() {
    // phpcs:ignore
    $userId = \Drupal::currentUser();
    // phpcs:ignore
    $connection = \Drupal::database();
    foreach ($userId as $entry) {
      $connection->insert('user_dbtng')->fields($entry)->execute();
    }

  }

  /**
   * Function that puts requested data into cache.
   */
  private function myCachingfunction($fullWeatherdata) {
    // Cid.
    $cacheId = "slavwnews_weather";
    // Getting cache saved data into db.
    $this->cacheBackend->set(
      $cacheId,
      $fullWeatherdata,
      time() + 3600
    );
    // Return requested data.
    return [
      '#theme' => 'slavnews__weather__block',
      '#image' => $fullWeatherdata['current']['condition']['icon'],
      '#temp_c' => $fullWeatherdata['current']['temp_c'],
      '#city' => $fullWeatherdata['location']['name'],
    ];
  }

}
