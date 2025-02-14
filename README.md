# Instagram API Plugin - Craft CMS

Instagram API Integration for Craft CMS, allowing you to fetch media from your Instagram profile and display it on your website using the Instagram Basic Display API and Meta's OAuth 2.

## Features

- Craft 5 Support!
- Authenticate with Instagram Basic Display API using Meta's OAuth 2
- Fetch Instagram Media and Profile Data
- Twig, Javascript and PHP API
- Full caching support with preheating/clearing controls
- Cron job support for refreshing access tokens
- Manual token refreshing from the control panel or CLI

## Requirements

This plugin requires Craft CMS 5.0.0 or later, and PHP 8.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for "Instagram API". Then press "Install".

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require bubalubs/craft-instagram-api

# tell Craft to install the plugin
./craft plugin/install craft-instagram-api
```

## Setup

### Step 1

First you need to create an Instagram App. You can do this by visiting the Facebook Developers page. When creating the app select use case `Other`, then `Comsumer`. and then you will be able to Set Up the Instagram Basic Display.

Add your instagram account to `App Roles -> Roles` on your Instagram App settings.

Note: You can find your App ID, App Secret and Valid OAuth Redirect URIs at: `Facebook Developers -> App Settings -> Products -> Instagram Basic Display -> Basic Display`

### Step 2

Add the URI from the from Plugin's Settings to the Valid OAuth Redirect URIs field on your Instagram App settings.

Note: If you are in a development environment, remember to additionally add your live domain to the Valid OAuth Redirect URIs field.

### Step 3

Fill in the Plugin Settings your Instagram App ID and Instagram App Secret.

### Step 4

After saving your Instagram App ID and Secret, you need to authenticate with Instagram to get an access token. Click the button in the Plugin Settings start the authorization flow with Instagram.

## Usage

### Javascript API Examples

#### Get Instagram Media

Endpoint: `/actions/instagram-api/api/media`

```
fetch('/actions/instagram-api/api/media')
  .then(response => response.json())
  .then(data => {
      console.log(data);
  });
```

#### Get Instagram Profile

Endpoint: `/actions/instagram-api/api/profile`

```
fetch('/actions/instagram-api/api/profile')
  .then(response => response.json())
  .then(data => {
      console.log(data);
  });
```

### Twig Examples

#### Loop through Instagram Media

```
{% set instagramMedia = craft.instagram.getMedia() %}

{% if instagramMedia|length %}
    <div class="grid grid-cols-4 gap-4">
        {% for media in instagramMedia %}
            <div>
                <a href="{{ media.permalink }}" title="View on Instagram" target="_blank">
                    <img src="{{ media.media_url }}" alt="{{ media.caption ?? 'Instagram image' }}"/>
                </a>
            </div>
        {% endfor %}
    </div>
{% endif %}
```

#### Get Profile Data

```
{% set instagramProfile = craft.instagram.getProfile() %}

{% if instagramProfile %}
  <h2>Instagram Profile</h2>

  <p>
      <strong>ID:</strong> {{ instagramProfile.id }}<br>
      <strong>Username:</strong> {{ instagramProfile.username }}<br>
      <strong>Account Type:</strong> {{ instagramProfile.account_type }}<br>
      <strong>Media Count:</strong> {{ instagramProfile.media_count }}<br>
  </p>
{% endif %}
```

### PHP Examples

#### Loop through Instagram Media and save locally

```
use bubalubs\instagramapi\InstagramAPI;

// ...

$instagramMedia = InstagramAPI::getInstance()->instagram->getMedia();

foreach ($instagramMedia as $media) {
    $mediaUrl = $media['media_url'];
    $file = file_get_contents($mediaUrl);

    // Strip query string from filename
    $newFilename = pathinfo(explode('?', $mediaUrl)[0], PATHINFO_BASENAME);
    
    // Check if directory exists
    if (!file_exists(Craft::$app->path->getStoragePath() . '/instagram')) {
        mkdir(Craft::$app->path->getStoragePath() . '/instagram', 0775, true);
    }

    // Save file locally
    file_put_contents(Craft::$app->path->getStoragePath() . '/instagram/' . $newFilename, $file);
}
```

#### Get Profile Data

```
use bubalubs\instagramapi\InstagramAPI;

// ...

$instagramProfile = InstagramAPI::getInstance()->instagram->getProfile();
```

## CLI Commands

### Refresh Access Token

```
./craft instagram-api/refresh-token
```
