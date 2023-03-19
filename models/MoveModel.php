<?php
/**
 * Created by PhpStorm.
 * User: Юрий
 * Date: 13.03.23
 * Time: 1:17
 */

namespace backend\modules\loader\models;


use backend\lib\FileInfo;

abstract class MoveModel {

    /** @var array Массив загружаемых файлов */
    protected $files = array();
    /** @var string Псевдоним пути конечной директории */
    protected $alias = "";
    /** @var string Путь куда переместить файлы */
    protected $new_path = '';

    public function __construct($files, $alias)
    {
        $this->files = $files;
        $this->alias = $alias;
        $this->new_path = FileInfo::getPathByAlias($alias);

        // Если пути такого нет - создаем
        if (!is_dir($this->new_path))
            mkdir($this->new_path, 0755, true);
    }

    /**
     * Функция выполняется до перемещения всех файлов
     * @param $file
     */
    protected function beforeStarting()
    {
    }

    /**
     * Запускаем процесс переноса файлов
     */
    public function starting()
    {
        if (empty($this->new_path)) return "Указан не существующий псевдоним ссылки";

        // Предобработка, если требуется
        $this->beforeStarting();

        foreach ($this->files as $file)
        {
            if (!rename($file, $this->new_path))
                return "Файл '".$file."' не удалось переместить в конечную директорию!";
            $this->afterStarting($file);
        }
    }

    /**
     * Функция выполняется после перемещения каждого файла
     * @param $file
     */
    abstract function afterStarting($file);
}