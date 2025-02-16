<?php

namespace bubalubs\craftinstagramapi\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use bubalubs\craftinstagramapi\InstagramAPI;
use craft\helpers\UrlHelper;

class AdminController extends Controller
{
    protected array|bool|int $allowAnonymous = false;

    // URL: /actions/instagram-api/api/clear-app-id-secret
    public function actionClearAppIdSecret(): Response
    {
        $settings = InstagramAPI::getInstance()->getSettings();

        $settings->appId = null;
        $settings->appSecret = null;

        Craft::$app->getPlugins()->savePluginSettings(InstagramAPI::getInstance(), $settings->getAttributes());

        Craft::$app->getSession()->setNotice('App ID and secret cleared!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
    }

    // URL: /actions/instagram-api/admin/clear-access-token
    public function actionClearAccessToken(): Response
    {
        $settings = InstagramAPI::getInstance()->getSettings();

        $settings->accessToken = null;

        Craft::$app->getPlugins()->savePluginSettings(InstagramAPI::getInstance(), $settings->getAttributes());

        Craft::$app->getSession()->setNotice('Access token cleared!');

        // Clear the cache
        $cache = Craft::$app->getCache();

        $cache->delete('instagram-api-profile');
        $cache->delete('instagram-api-media');

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
        $response = InstagramAPI::getInstance()->instagram->refreshToken();

        if (!$response) {
            Craft::$app->getSession()->setError('Failed to refresh token!');

            return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
        }

        Craft::$app->getSession()->setNotice('Instagram token successfully refreshed!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
    }
}
