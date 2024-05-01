<?php

namespace bubalubs\craftinstagramapi\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use bubalubs\craftinstagramapi\InstagramAPI;
use craft\helpers\UrlHelper;
use GuzzleHttp\Client;

class AdminController extends Controller
{
    protected array|bool|int $allowAnonymous = false;

    // URL: /actions/instagram-api/admin/clear-access-token
    public function actionClearAccessToken(): Response
    {
        $settings = InstagramAPI::getInstance()->getSettings();

        $settings->accessToken = null;

        Craft::$app->getPlugins()->savePluginSettings(InstagramAPI::getInstance(), $settings->getAttributes());

        Craft::$app->getSession()->setNotice('Access token cleared!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
    }

    public function actionPreheatCache(): Response
    {
        $instagram = InstagramAPI::getInstance()->instagram;

        $instagram->getProfile();
        $instagram->getMedia();

        Craft::$app->getSession()->setNotice('Instagram API cache preheated!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
    }

    // URL: /actions/instagram-api/admin/clear-cache
    public function actionClearCache(): Response
    {
        $cache = Craft::$app->getCache();

        $cache->delete('instagram-api-profile');
        $cache->delete('instagram-api-media');

        Craft::$app->getSession()->setNotice('Instagram API cache cleared!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
    }

    public function actionRefreshAccessToken(): Response
    {
        $settings = InstagramAPI::getInstance()->getSettings();
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

        Craft::$app->getPlugins()->savePluginSettings(InstagramAPI::getInstance(), $settings->getAttributes());

        Craft::$app->getSession()->setNotice('Instagram token successfully refreshed!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
    }
}
