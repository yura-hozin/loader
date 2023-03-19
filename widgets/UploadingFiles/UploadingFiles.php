<?php
/**
 * Created by PhpStorm.
 * User: Юрий&Елена
 * Date: 08.03.23
 * Time: 1:01
 */

namespace backend\widgets\UploadingFiles;

use yii\base\Widget;
use yii\helpers\Html;

class UploadingFiles extends Widget{

    /** @var bool Разрешить мультизагрузку */
    public $multiple = false;
    /** @var string Аналог пути в системе, например 'image-product' */
    public $alias_path = '';

    public function init()
    {
    }

    public function run()
    {

        if ($this->multiple)
            return $this->render('multiple', ['alias' => $this->alias_path]);
        else
            return $this->render('one_file', ['alias' => $this->alias_path]);
    }
} 