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

    // URL: /actions/instagram-api/admin/clear-access-token
    public function actionClearAccessToken(): Response
    {
        $settings = InstagramAPI::getInstance()->getSettings();

        $settings->accessToken = null;

        Craft::$app->getPlugins()->savePluginSettings(InstagramAPI::getInstance(), $settings->getAttributes());

        Craft::$app->getSession()->setNotice('Access token cleared!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
    }
}
