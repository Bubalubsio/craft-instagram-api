<?php

namespace bubalubs\craftinstagramapi\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use bubalubs\craftinstagramapi\InstagramAPI;
use GuzzleHttp\Client;

class ApiController extends Controller
{
    protected array|bool|int $allowAnonymous = true;
    
    private string $apiBaseUrl = 'https://graph.instagram.com/me';

    // URL: /actions/instagram-api/api/profile
    public function actionProfile(): Response
    {
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

        $response = json_decode($response->getBody()->getContents());

        return $this->asJson($response);
    }

    // URL: /actions/instagram-api/api/media
    public function actionMedia(): Response
    {
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

        $response = json_decode($response->getBody()->getContents());

        return $this->asJson($response);
    }
}
