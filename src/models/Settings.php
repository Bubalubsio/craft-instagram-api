<?php

namespace bubalubs\instagramapi\models;

use craft\base\Model;

class Settings extends Model
{
    public $appId = '';
    public $appSecret = '';
    public $accessToken = '';

    public function rules(): array
    {
        return [
            ['appId', 'string'],
            ['appId', 'required'],
            ['appSecret', 'string'],
            ['appSecret', 'required'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'appId' => 'Facebook App ID',
            'appSecret' => 'Facebook App Secret',
        ];
    }
}
