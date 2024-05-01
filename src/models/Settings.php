<?php

namespace bubalubs\craftinstagramapi\models;

use craft\base\Model;

class Settings extends Model
{
    public $appId = '';
    public $appSecret = '';
    public $accessToken = '';
    public $cacheDuration = 60 * 60 * 24;

    public function rules(): array
    {
        return [
            ['appId', 'string'],
            ['appId', 'required'],
            ['appSecret', 'string'],
            ['appSecret', 'required'],
            ['accessToken', 'string'],
            ['cacheDuration', 'integer'],
            ['cacheDuration', 'required'],
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
