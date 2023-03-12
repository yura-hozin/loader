<?php
/**
 * Created by PhpStorm.
 * User: Юрий&Елена
 * Date: 13.03.23
 * Time: 1:39
 */

namespace backend\modules\loader\models;


class MyMoveModel extends MoveModel{

    /**
     * Функция выполняется после перемещения каждого файла
     * @param $file
     */
    function afterStarting($file)
    {

    }
}