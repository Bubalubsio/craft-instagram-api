<?php

namespace bubalubs\craftinstagramapi\services;

use Craft;
use yii\base\Component;
use GuzzleHttp\Client;

class Instagram extends Component
{
    public function getProfile(): array
    {
        $accessToken = Craft::$app->plugins->getPlugin('instagram-api')->getSettings()->accessToken;

        if (!$accessToken) {
            return [];
        }

        $client = new Client();

        $response = $client->get("https://graph.instagram.com/me?fields=id,username,account_type,media_count&access_token={$accessToken}");

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getMedia(): array
    {
        $accessToken = Craft::$app->plugins->getPlugin('instagram-api')->getSettings()->accessToken;

        if (!$accessToken) {
            return [];
        }

        $client = new Client();

        $response = $client->get("https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token={$accessToken}");

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        return json_decode($response->getBody()->getContents(), true)['data'];
    }
}
