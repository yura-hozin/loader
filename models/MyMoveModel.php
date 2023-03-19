<?php
/**
 * Created by PhpStorm.
 * User: Юрий&Елена
 * Date: 13.03.23
 * Time: 1:39
 */

namespace backend\modules\loader\models;


class MyMoveModel extends MoveModel{

    private $dop_function = [
        'photo' => 'funcPhoto'
    ];
    /**
     * Функция выполняется после перемещения каждого файла
     * @param $file
     */
    function afterStarting($file)
    {
        // Загрузка конфига по параметрам файлов

        // Если требуется запустить доп функцию, то запускаем
        if (isset($this->dop_function[$this->alias]))
        {
            //if(method_exists($this, $this->dop_function[$this->alias]))
              //  $this->$this->dop_function[$this->alias];
        }
    }

    private function funcPhoto()
    {
        die("00000000000");
    }
}