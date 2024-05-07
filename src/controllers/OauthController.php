<?php

namespace bubalubs\craftinstagramapi\controllers;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use bubalubs\craftinstagramapi\InstagramAPI;
use GuzzleHttp\Client;

class OauthController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    // URL: /actions/instagram-api/oauth/handle
    public function actionHandle()
    {
        $code = $this->request->getParam('code');

        if (!$code) {
            return null;
        }

        $state = $this->request->getParam('state');

        if (!$state || $state !== InstagramAPI::getInstance()->getSettings()->securityToken) {
            return null;
        }

        $settings = InstagramAPI::getInstance()->getSettings();
        $appId = $settings->appId;
        $appSecret = $settings->appSecret;
        $redirectUri = InstagramAPI::getInstance()->getRedirectUrl();

        $client = new Client();

        // Exchange code for short-lived token
        $response = $client->post('https://api.instagram.com/oauth/access_token', [
            'form_params' => [
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            Craft::$app->getSession()->setError('Failed to connect to Instagram');

            return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
        }

        $response = json_decode($response->getBody()->getContents());

        $shortLivedToken = $response->access_token;

        // Exchange short-lived token for long-lived token
        $response = $client->get("https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret={$appSecret}&access_token={$shortLivedToken}");

        if ($response->getStatusCode() !== 200) {
            Craft::$app->getSession()->setError('Failed to connect to Instagram');

            return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
        }

        $response = json_decode($response->getBody()->getContents());

        $settings->accessToken = $response->access_token;
        $settings->accessTokenExpires = date('Y-m-d H:i:s', time() + $response->expires_in);

        Craft::$app->getPlugins()->savePluginSettings(InstagramAPI::getInstance(), $settings->getAttributes());

        Craft::$app->getSession()->setNotice('Instagram successfully connected!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/instagram-api'));
    }
}
