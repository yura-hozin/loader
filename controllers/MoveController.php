<?php
/**
 * Created by PhpStorm.
 * User: Юрий&Елена
 * Date: 11.03.23
 * Time: 18:55
 */

namespace backend\modules\loader\controllers;

use backend\lib\FileInfo;
use backend\modules\loader\models\MyMoveModel;
use yii\web\Controller;

/**
 * Класс отвечающий за перемещение файлов до конечной точки
 * Этот класс настраивается под конкретный проект
 * Class MoveController
 * @package backend\modules\loader\controllers
 */
class MoveController extends Controller{

    public function actionIndex()
    {
        $error = $success = "";

        header('content-type: application/json');

        $files = \Yii::$app->request->post('files', []);
        $alias = \Yii::$app->request->post('alias', '');

        if (count($files) < 1)
            $error = "Не переданы ссылки для переноса файлов";
        if (empty($alias))
            $error = "Не указана конечная папка для переноса файлов";

        if (empty($error))
        {
            // Перенос файлов в конечную директорию
            $move_model = new MyMoveModel($files, $alias);
            $error = $move_model->starting();

            if (empty($error))
                $success = "ok";
        }

        echo json_encode(["error" => $error, "success" => $success], JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function beforeAction($action)
    {
        if ($action->id == 'index') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }
}