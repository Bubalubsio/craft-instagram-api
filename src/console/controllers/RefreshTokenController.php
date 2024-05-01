<?php

namespace bubalubs\craftinstagramapi\console\controllers;

use Craft;
use craft\console\Controller;
use craft\helpers\Console;
use yii\console\ExitCode;

class RefreshTokenController extends Controller
{
    public $defaultAction = 'handle';

    public function actionHandle(): int
    {
        $response = Craft::$app->plugins->getPlugin('instagram-api')->instagram->refreshToken();
        
        if (!$response) {
            $this->stderr("Failed to refresh token!", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Successfully refreshed token!", Console::FG_GREEN);

        return ExitCode::OK;
    }
}
