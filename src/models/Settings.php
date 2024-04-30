<?php

namespace bubalubs\craftinstagram\models;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    public $appId = '';

    public function rules(): array
    {
        return [
            ['appId', 'string'],
            ['appId', 'required'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'appId' => 'Facebook App ID',
        ];
    }
}
