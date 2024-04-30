<?php

namespace bubalubs\craftinstagram;

use Craft;
use bubalubs\craftinstagram\models\Settings;
use craft\base\Model;
use craft\base\Plugin;
use craft\helpers\UrlHelper;

/**
 * craft-instagram plugin
 *
 * @method static CraftInstagram getInstance()
 * @method Settings getSettings()
 * @author Bubalubs <adam@bubalubs.io>
 * @copyright Bubalubs
 * @license https://craftcms.github.io/license/ Craft License
 */
class CraftInstagram extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
        });
    }

    public function getAuthUrl(): string
    {
        $appId = $this->getSettings()->appId;
        $redirectUri = $this->getRedirectUrl();

        return "https://api.instagram.com/oauth/authorize?client_id={$appId}&redirect_uri={$redirectUri}&response_type=code&scope=user_profile,user_media";
    }

    public function getRedirectUrl(): string
    {
        return UrlHelper::siteUrl('actions/craft-instagram/oauth/handle');
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('craft-instagram/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        //
    }
}
