# Thumbnail Generator Yii2 Wrapper
Requirement:
imagick,
jpegoptim
php extensions

Usage:
```php
// Configure
'thumbnail' => [
    'class' => 'spayn\ImageHelpers\Yii2\ThumbnailGeneratorWrapper',
    'save_path' => '@backend/web/thumbs',
    'url' => '/backend/thumbs',
    'thumbs' => [
        'common\modules\blog\models\Post' => [
            'picture_property' => 'picture',
            'resolutions' => [
                'small' => '75x75',
                'medium' => '730x410'
            ]
        ],
        'common\modules\blog\models\SiteMeta' => [
            'picture_property' => 'value',
            'resolutions' => [
                'small' => '80x80'
            ]
        ]
    ]
]

// Generate
Yii::$app->thumbnail->generate($model, $path_to_imagefile);

// Get url
Yii::$app->thumbnail->getFileUrl($model, 'medium');
