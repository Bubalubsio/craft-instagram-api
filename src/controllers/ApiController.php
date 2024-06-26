<?php

namespace bubalubs\craftinstagramapi\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use yii\caching\CacheInterface;
use bubalubs\craftinstagramapi\InstagramAPI;
use GuzzleHttp\Client;

class ApiController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    private string $apiBaseUrl = 'https://graph.instagram.com/me';
    private int $cacheDuration = 60 * 60 * 24;
    private CacheInterface $cache;
    private Client $client;

    public function beforeAction($action): bool
    {
        $this->cache = Craft::$app->getCache();
        $this->cacheDuration = InstagramAPI::getInstance()->getSettings()->cacheDuration;

        $this->client = Craft::createGuzzleClient([
            'base_uri' => $this->apiBaseUrl,
        ]);

        return parent::beforeAction($action);
    }

    // URL: /actions/instagram-api/api/profile
    public function actionProfile(): Response
    {
        $profile = $this->cache->get('instagram-api-profile');

        if ($profile) {
            $profile = json_decode($profile);

            return $this->asJson($profile);
        }
        
        $accessToken = Craft::$app->plugins->getPlugin('instagram-api')->getSettings()->accessToken;

        if (!$accessToken) {
            return $this->asJson([]);
        }

        $response = $this->client->get('?fields=id,username,account_type,media_count&access_token=' . $accessToken);

        if ($response->getStatusCode() !== 200) {
            return $this->asJson([
                'status' => 'error',
                'code' => $response->getStatusCode(),
                'error' => 'Failed to connect to Instagram'
            ]);
        }
        
        $contents = $response->getBody()->getContents();

        $this->cache->set('instagram-api-profile', $contents, $this->cacheDuration);
        
        return $this->asJson($contents);
    }

    // URL: /actions/instagram-api/api/media
    public function actionMedia(): Response
    {
        $media = $this->cache->get('instagram-api-media');

        if ($media) {
            $media = json_decode($media);

            return $this->asJson($media);
        }

        $accessToken = InstagramAPI::getInstance()->getSettings()->accessToken;

        $response = $this->client->get('/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token=' . $accessToken);

        if ($response->getStatusCode() !== 200) {
            return $this->asJson([
                'status' => 'error',
                'code' => $response->getStatusCode(),
                'error' => 'Failed to connect to Instagram'
            ]);
        }

        $contents = $response->getBody()->getContents();

        $this->cache->set('instagram-api-media', $contents, $this->cacheDuration);

        return $this->asJson($contents);
    }
}
