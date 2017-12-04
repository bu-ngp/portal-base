<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 12.11.2017
 * Time: 8:35
 */

namespace ngp\services\validators;


use Imagine\Image\Box;
use Imagine\Image\Point;
use yii\imagine\Image;
use yii\validators\Validator;

class ThumbnailFilterValidator extends Validator
{
    public $widthAttribute;
    public $heightAttribute;
    public $xAttribute;
    public $yAttribute;
    public $to = [];
    public $path;
    public $web;
    public $thumbnailAttribute;

    public function validateAttribute($model, $attribute)
    {
        $this->filterAttribute($model, $attribute);
    }

    protected function filterAttribute($model, $attribute)
    {
        $fileName = time();
        $tmpOrigFileName = $this->path . '/tmp/' . $fileName . '.' . $model->$attribute->extension;
        $model->$attribute->saveAs($tmpOrigFileName);
        $imagine = Image::getImagine();
        $image = $imagine->open($tmpOrigFileName);
        $oldThumb = $model->{$this->thumbnailAttribute};

        $image->crop(new Point($model->{$this->xAttribute}, $model->{$this->yAttribute}), new Box($model->{$this->widthAttribute}, $model->{$this->heightAttribute}));
        foreach ($this->to as $key => $resize) {
            $image->resize(new Box($resize[0], $resize[1]));
            $image->save($this->path . '/' . $fileName . '-' . $resize[0] . 'x' . $resize[1] . '.' . $model->$attribute->extension);
            if ($key === 0) {
                $model->{$this->thumbnailAttribute} = $this->web . '/' . $fileName . '-' . $resize[0] . 'x' . $resize[1] . '.' . $model->$attribute->extension;
            }
        }

        unlink($tmpOrigFileName);
        $this->removeOldThumbs($oldThumb);
    }

    protected function removeOldThumbs($pathThumb)
    {
        if ($pathThumb) {
            preg_match('/\/(\d+-)\d+x\d+(\.\w+)$/', $pathThumb, $matches);

            $resolutions = ['363x209', '165x95'];
            $path = $this->path;

            array_walk($resolutions, function ($resolution) use ($matches, $path) {
                if ($matches[1] && $matches[2]) {
                    if (file_exists($path . '/' . $matches[1] . $resolution . $matches[2])) {
                        unlink($path . '/' . $matches[1] . $resolution . $matches[2]);
                    }
                }
            });
        }
    }
}