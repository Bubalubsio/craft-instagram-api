<?php

namespace bubalubs\craftinstagramapi\models;

use craft\base\Model;

class Settings extends Model
{
    public $appId = '';
    public $appSecret = '';
    public $accessToken = '';
    public $accessTokenExpires = 0;
    public $cacheDuration = 60 * 60 * 24;

    public function rules(): array
    {
        return [
            ['appId', 'string'],
            ['appId', 'required'],
            ['appSecret', 'string'],
            ['appSecret', 'required'],
            ['cacheDuration', 'integer'],
            ['cacheDuration', 'required'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'appId' => 'Facebook App ID',
            'appSecret' => 'Facebook App Secret',
            'accessToken' => 'Access Token',
            'accessTokenExpires' => 'Date Access Token Expires',
            'cacheDuration' => 'Cache Duration',
        ];
    }
}
