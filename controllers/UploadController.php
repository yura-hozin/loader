<?php
/**
 * Created by PhpStorm.
 * User: Юрий&Елена
 * Date: 08.03.23
 * Time: 11:42
 */

namespace backend\modules\loader\controllers;


use yii\web\Controller;

/**
 * Класс для приёма загружаемых файлов
 * Class UploadController
 * @package backend\modules\loader\controllers
 */
class UploadController extends Controller{

    /** @var string Название переменной в источнике данных */
    private $input_name = 'file';
    /** @var  Путь куда размещать временные файлы. Формируется в before */
    private $path;

    // Запрещенные расширения файлов.
    private $deny = array(
        'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl', 'asp',
        'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh', 'js', 'html',
        'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi', 'exe'
    );

    public function actionIndex()
    {
        $res = array();

        // Директория куда будут загружаться файлы.
        $path = \Yii::getAlias("@backend"). '/runtime' . '/uploads/';

        // Создать папку, если она не существует
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $error = $success = '';

        if (!isset($_FILES[$this->input_name])) {
            $error = 'Файл не загружен. Не отправлен на сервер.';
            $res[] = [
                'error' => $error,
                'success' => '',
                'path' => '',
            ];
        } else {

            // Преобразуем массив $_FILES в удобный вид для перебора в foreach.
            $files = array();
            $diff = count($_FILES[$this->input_name]) - count($_FILES[$this->input_name], COUNT_RECURSIVE);
            if ($diff == 0) {
                $files = array($_FILES[$this->input_name]);
            } else {
                foreach($_FILES[$this->input_name] as $k => $l) {
                    foreach($l as $i => $v) {
                        $files[$i][$k] = $v;
                    }
                }
            }

            foreach ($files as $file) {
                $res[] = $this->prepareFile($file);
            }
        }

        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Загрузка одного файла
     */
    public function actionOne()
    {
        $res = array();

        $error = $success = '';
        if (!isset($_FILES[$this->input_name])) {
            $error = 'Файл не загружен.';
        } else {
            $file = $_FILES[$this->input_name];
            $res = $this->prepareFile($file);
        }

        // Вывод сообщения о результате загрузки.
        if (!empty($error)) {
            $error = '<p style="color: red">' . $error . '</p>';
            $res[] = [
                'error' => $error,
                'success' => '',
                'path' => '',
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Проверка и сохранение файла во временное хранилище
     * @param $file - информация о файле
     */
    private function prepareFile($file)
    {
        $error = "";
        $success = "";

        // Проверим на ошибки загрузки.
        if (!empty($file['error']) || empty($file['tmp_name'])) {
            switch (@$file['error']) {
                case 1:
                case 2: $error = 'Превышен размер загружаемого файла.'; break;
                case 3: $error = 'Файл был получен только частично.'; break;
                case 4: $error = 'Файл не был загружен.'; break;
                case 6: $error = 'Файл не загружен - отсутствует временная директория.'; break;
                case 7: $error = 'Не удалось записать файл на диск.'; break;
                case 8: $error = 'PHP-расширение остановило загрузку файла.'; break;
                case 9: $error = 'Файл не был загружен - директория не существует.'; break;
                case 10: $error = 'Превышен максимально допустимый размер файла.'; break;
                case 11: $error = 'Данный тип файла запрещен.'; break;
                case 12: $error = 'Ошибка при копировании файла.'; break;
                default: $error = 'Файл не был загружен - неизвестная ошибка.'; break;
            }
        } elseif ($file['tmp_name'] == 'none' || !is_uploaded_file($file['tmp_name'])) {
            $error = 'Не удалось загрузить файл.';
        } else {
            // Оставляем в имени файла только буквы, цифры и некоторые символы.
            $pattern = "[^a-zа-яё0-9,~!@#%^-_\$\?\(\)\{\}\[\]\.]";
            $name = mb_eregi_replace($pattern, '-', $file['name']);
            $name = mb_ereg_replace('[-]+', '-', $name);

            // Т.к. есть проблема с кириллицей в названиях файлов (файлы становятся недоступны).
            // Сделаем их транслит:
            $converter = array(
                'а' => 'a',   'б' => 'b',   'в' => 'v',    'г' => 'g',   'д' => 'd',   'е' => 'e',
                'ё' => 'e',   'ж' => 'zh',  'з' => 'z',    'и' => 'i',   'й' => 'y',   'к' => 'k',
                'л' => 'l',   'м' => 'm',   'н' => 'n',    'о' => 'o',   'п' => 'p',   'р' => 'r',
                'с' => 's',   'т' => 't',   'у' => 'u',    'ф' => 'f',   'х' => 'h',   'ц' => 'c',
                'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',  'ь' => '',    'ы' => 'y',   'ъ' => '',
                'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

                'А' => 'A',   'Б' => 'B',   'В' => 'V',    'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
                'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',    'И' => 'I',   'Й' => 'Y',   'К' => 'K',
                'Л' => 'L',   'М' => 'M',   'Н' => 'N',    'О' => 'O',   'П' => 'P',   'Р' => 'R',
                'С' => 'S',   'Т' => 'T',   'У' => 'U',    'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
                'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',  'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
                'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
            );

            $name = strtr($name, $converter);
            $parts = pathinfo($name);

            if (empty($name) || empty($parts['extension'])) {
                $error = 'Недопустимый тип файла';
            } elseif (!empty($allow) && !in_array(strtolower($parts['extension']), $allow)) {
                $error = 'Недопустимый тип файла';
            } elseif (!empty($this->deny) && in_array(strtolower($parts['extension']), $this->deny)) {
                $error = 'Недопустимый тип файла';
            } else {
                // Перемещаем файл в директорию.
                if (move_uploaded_file($file['tmp_name'], $this->path . $name)) {
                    // Далее можно сохранить название файла в БД и т.п.
                    $success = '<p style="color: green">Файл «' . $name . '» успешно загружен.</p>';
                } else {
                    $error = 'Не удалось загрузить файл.';
                }
            }
        }
        // Вывод сообщения о результате загрузки.
        if (!empty($error)) {
            $error = '<p style="color: red">' . $error . '</p>';
        }

        return array(
            'error' => $error,
            'success' => $success,
            'path' => $this->path.$name,
        );
    }

    public function beforeAction($action)
    {
        $this->path = \Yii::getAlias("@backend"). '/runtime' . '/uploads/';

        if ($action->id == 'index' or $action->id == 'one') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }
}