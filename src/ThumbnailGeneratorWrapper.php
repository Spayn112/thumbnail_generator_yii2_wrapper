<?php

namespace spayn\ImageHelpers\Yii2;

use Yii;
use spayn\ImageHelpers\ThumbnailGenerator;
use yii\base\InvalidParamException;
use yii\base\Object;
use yii\db\ActiveRecord;


class ThumbnailGeneratorWrapper extends Object
{

    /**
     * Path to thumbs
     */
    public $save_path;

    /**
     * Url to thumbs path
     */
    public $url;

    /**
     * Array Class => parameters
     * Example:
     * [
     *     'NameSpace\SomeClass' => [
     *         'picture_property' => 'picture',
     *         'resolutions' => [
     *             'small' => '75x75',
     *             'medium' => '730x410'
     *         ]
     *     ]
     * ]
     */
    public $thumbs = [];

    /**
     * @var ThumbnailGenerator[]
     */
    protected $thumbnail_generators;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->validateParameters();
        foreach ($this->thumbs as $class => $array) {
            $this->thumbnail_generators[$class] = new ThumbnailGenerator(Yii::getAlias($this->save_path), $this->url, $array['resolutions']);
        }
    }


    /**
     * Return url to thumbnail
     *
     * @param ActiveRecord $model
     * @param $prefix
     * @return string
     */
    public function getFileUrl(ActiveRecord $model, $prefix)
    {
        return $this->getThumbnailGenerator($model)->getFileUrl($model->{$this->getPictureProperty($model)}, $this->getThumbDir($model), $prefix);
    }


    /**
     * Generate thumbnails
     *
     * @param ActiveRecord $model
     * @param $filepath
     */
    public function generate(ActiveRecord $model, $filepath)
    {
        $this->getThumbnailGenerator($model)->generate($filepath, $this->getThumbDir($model));
    }


    /**
     * Generate thumbnails
     *
     * @param ActiveRecord $model
     * @return bool
     */
    public function deleteThumbDir(ActiveRecord $model)
    {
        return $this->getThumbnailGenerator($model)->deleteThumbnailsDirectory($this->getThumbDir($model));
    }


    /**
     * Generates a unique directory for thumbnails
     *
     * @param ActiveRecord $model
     * @return string
     */
    protected function getThumbDir(ActiveRecord $model)
    {
        return md5($model::className() . $model->id . $model->{$this->getPictureProperty($model)});
    }


    /**
     * Returns ThumbnailGenerator object with transmitted status
     *
     * @param ActiveRecord $model
     * @return ThumbnailGenerator
     */
    protected function getThumbnailGenerator(ActiveRecord $model)
    {
        return $this->thumbnail_generators[$model::className()];
    }


    /**
     * Returns settings with transmitted status
     *
     * @param ActiveRecord $model
     * @return mixed
     */
    protected function getSettings(ActiveRecord $model)
    {
        return $this->thumbs[$model::className()];
    }


    /**
     * Returns picture_property with transmitted object
     *
     * @param ActiveRecord $model
     * @return mixed
     */
    protected function getPictureProperty(ActiveRecord $model)
    {
        return $this->getSettings($model)['picture_property'];
    }


    /**
     * Validates passed parameters
     * @throws InvalidParamException
     */
    private function validateParameters()
    {
        if (empty($this->save_path)) {
            throw new InvalidParamException('save_path parameter cannot be empty');
        }
        if (empty($this->url)) {
            throw new InvalidParamException('url parameter cannot be empty');
        }
        if (empty($this->thumbs)) {
            throw new InvalidParamException('thumbs parameter cannot be empty');
        } else {
            foreach ($this->thumbs as $class => $parameters) {
                if (empty($class)) {
                    throw new InvalidParamException('thumb[\'class\'] parameter cannot be empty');
                }
                if (!isset($parameters['picture_property']) || empty($parameters['picture_property'])) {
                    throw new InvalidParamException('thumb[\'picture_property\'] parameter cannot be empty');
                }
                if (!isset($parameters['resolutions']) || empty($parameters['resolutions'])) {
                    throw new InvalidParamException('thumb[\'resolutions\'] parameter cannot be empty');
                } else {
                    if (!is_array($parameters['resolutions'])) {
                        throw new InvalidParamException('thumb[\'resolutions\'] parameter must be array');
                    }
                }
            }
        }
    }


}
