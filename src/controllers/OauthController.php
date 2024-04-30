<?php

namespace bubalubs\craftinstagram\controllers;

use craft\web\Controller;
use Craft;
use craft\helpers\UrlHelper;
use GuzzleHttp\Client;
use bubalubs\craftinstagram\CraftInstagram;
use GuzzleHttp\Exception\ClientException;

class OauthController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionHandle()
    {
        $code = $this->request->getParam('code');
        $settings = CraftInstagram::getInstance()->getSettings();
        $appId = $settings->appId;
        $appSecret = $settings->appSecret;
        $redirectUri = CraftInstagram::getInstance()->getRedirectUrl();

        $client = new Client();

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

            return $this->redirect(UrlHelper::cpUrl('settings/plugins/craft-instagram'));
        }

        $response = json_decode($response->getBody()->getContents());

        $settings->accessToken = $response->access_token;

        Craft::$app->getPlugins()->savePluginSettings(CraftInstagram::getInstance(), $settings->getAttributes());

        Craft::$app->getSession()->setNotice('Instagram successfully connected!');

        return $this->redirect(UrlHelper::cpUrl('settings/plugins/craft-instagram'));
    }

    public function actionGetMedia()
    {
        $accessToken = CraftInstagram::getInstance()->getSettings()->accessToken;

        $client = new Client();

        $response = $client->get("https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token={$accessToken}");

        if ($response->getStatusCode() !== 200) {
            Craft::$app->getSession()->setError('Failed to connect to Instagram');
            return $this->redirect('settings/plugins/craft-instagram');
        }

        $response = json_decode($response->getBody()->getContents());

        return $this->asJson($response);
    }
}
