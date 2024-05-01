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
    private CacheInterface $cache = Craft::$app->getCache();

    public function beforeAction($action): bool
    {
        $this->cache = Craft::$app->getCache();

        return parent::beforeAction($action);
    }

    // URL: /actions/instagram-api/api/profile
    public function actionProfile(): Response
    {
        $profile = $this->cache->get('instagram-api-profile');

        if ($profile) {
            return $this->asJson($profile);
        }
        
        $accessToken = Craft::$app->plugins->getPlugin('instagram-api')->getSettings()->accessToken;

        if (!$accessToken) {
            return $this->asJson([]);
        }

        $client = new Client();

        $response = $client->get($this->apiBaseUrl . '?fields=id,username,account_type,media_count&access_token=' . $accessToken);

        if ($response->getStatusCode() !== 200) {
            return $this->asJson([
                'status' => 'error',
                'code' => $response->getStatusCode(),
                'error' => 'Failed to connect to Instagram'
            ]);
        }
        
        $contents = $response->getBody()->getContents();

        $this->cache->set('instagram-api-profile', $contents, 60 * 60 * 24);
        
        return $this->asJson($contents);
    }

    // URL: /actions/instagram-api/api/media
    public function actionMedia(): Response
    {
        $media = $this->cache->get('instagram-api-media');

        if ($media) {
            return $this->asJson($media);
        }

        $accessToken = InstagramAPI::getInstance()->getSettings()->accessToken;

        $client = new Client();

        $response = $client->get($this->apiBaseUrl . '/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token=' . $accessToken);

        if ($response->getStatusCode() !== 200) {
            return $this->asJson([
                'status' => 'error',
                'code' => $response->getStatusCode(),
                'error' => 'Failed to connect to Instagram'
            ]);
        }

        $contents = $response->getBody()->getContents();

        $this->cache->set('instagram-api-media', $contents, 60 * 60 * 24);

        return $this->asJson($contents);
    }
}
