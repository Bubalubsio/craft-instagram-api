<?php

namespace bubalubs\craftinstagramapi\services;

use Craft;
use craft\helpers\UrlHelper;
use yii\base\Component;
use yii\caching\CacheInterface;
use GuzzleHttp\Client;

class Instagram extends Component
{
    private CacheInterface $cache;
    private int $cacheDuration = 60 * 60 * 24;

    public function init()
    {
        $this->cache = Craft::$app->getCache();
        $this->cacheDuration = Craft::$app->plugins->getPlugin('instagram-api')->getSettings()->cacheDuration;
    }

    public function getProfile($cache = true): array
    {
        $profile = $this->cache->get('instagram-api-profile');

        if ($profile && $cache) {
            return json_decode($profile, true);
        }

        $accessToken = Craft::$app->plugins->getPlugin('instagram-api')->getSettings()->accessToken;

        if (!$accessToken) {
            return [];
        }

        $client = new Client();

        $response = $client->get("https://graph.instagram.com/me?fields=id,username,account_type,media_count&access_token={$accessToken}");

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $contents = $response->getBody()->getContents();

        if ($cache) {
            $this->cache->set('instagram-api-profile', $contents, $this->cacheDuration);
        }

        return json_decode($contents, true);
    }

    public function getMedia($cache = true): array
    {
        $media = $this->cache->get('instagram-api-media');

        if ($media && $cache) {
            return json_decode($media, true)['data'];
        }

        $accessToken = Craft::$app->plugins->getPlugin('instagram-api')->getSettings()->accessToken;

        if (!$accessToken) {
            return [];
        }

        $client = new Client();

        $response = $client->get("https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token={$accessToken}");

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $contents = $response->getBody()->getContents();

        if ($cache) {
            $this->cache->set('instagram-api-media', $contents, $this->cacheDuration);
        }

        return json_decode($contents, true)['data'];
    }

    public function refreshToken()
    {
        $settings = Craft::$app->plugins->getPlugin('instagram-api')->getSettings();
        $accessToken = $settings->accessToken;

        $client = new Client();

        $response = $client->get('https://graph.instagram.com/refresh_access_token', [
            'query' => [
                'grant_type' => 'ig_refresh_token',
                'access_token' => $accessToken,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            Craft::$app->getSession()->setError('Failed to connect to Instagram');

            return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
        }

        $response = json_decode($response->getBody()->getContents());

        $settings->accessToken = $response->access_token;
        $settings->accessTokenExpires = date('Y-m-d H:i:s', time() + $response->expires_in);

        return Craft::$app->getPlugins()->savePluginSettings(Craft::$app->plugins->getPlugin('instagram-api'), $settings->getAttributes()); 
    }

    public function getMediaCacheStatus(): bool
    {
        return (bool) $this->cache->get('instagram-api-media');
    }

    public function getProfileCacheStatus(): bool
    {
        return (bool) $this->cache->get('instagram-api-profile');
    }
}
