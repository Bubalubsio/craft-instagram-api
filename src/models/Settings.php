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
    public $securityToken = '';

    public function rules(): array
    {
        return [
            ['appId', 'string'],
            ['appSecret', 'string'],
            ['cacheDuration', 'integer'],
            ['cacheDuration', 'required'],
            ['securityToken', 'string'],
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
            'securityToken' => 'Security Token',
        ];
    }
}
