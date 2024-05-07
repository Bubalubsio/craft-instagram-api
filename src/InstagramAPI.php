<?php

namespace bubalubs\craftinstagramapi;

use Craft;
use craft\web\twig\variables\CraftVariable;
use craft\base\Model;
use craft\base\Plugin;
use craft\helpers\UrlHelper;
use yii\base\Event;
use bubalubs\craftinstagramapi\models\Settings;
use bubalubs\craftinstagramapi\services\Instagram;

/**
 * Instagram API plugin
 *
 * @method static InstagramAPI getInstance()
 * @method Settings getSettings()
 * @author Bubalubs <adam@bubalubs.io>
 * @copyright Bubalubs
 * @license https://craftcms.github.io/license/ Craft License
 * @property-read InstagramApi $instagramApi
 */
class InstagramAPI extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => ['instagram' => Instagram::class],
        ];
    }

    public function init(): void
    {
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'bubalubs\\craftinstagramapi\\console\\controllers';
        } else {
            $this->controllerNamespace = 'bubalubs\\craftinstagramapi\\controllers';
        }

        parent::init();

        Craft::$app->onInit(function () {
            $this->attachEventHandlers();

            if (!$this->getSettings()->securityToken) {
                $settings = $this->getSettings();

                $settings->securityToken = Craft::$app->getSecurity()->generateRandomString();

                Craft::$app->plugins->savePluginSettings($this, $settings->getAttributes());
            }
        });
    }

    public function getAuthUrl(): string
    {
        $appId = $this->getSettings()->appId;
        $redirectUri = $this->getRedirectUrl();
        $securityToken = $this->getSettings()->securityToken;

        return "https://api.instagram.com/oauth/authorize?client_id={$appId}&redirect_uri={$redirectUri}&response_type=code&scope=user_profile,user_media&state={$securityToken}";
    }

    public function getRedirectUrl(): string
    {
        return UrlHelper::siteUrl('actions/instagram-api/oauth/handle');
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('instagram-api/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $e) {
                /** @var CraftVariable $variable */
                $variable = $e->sender;

                $variable->set('instagram', Instagram::class);
            }
        );
    }
}
