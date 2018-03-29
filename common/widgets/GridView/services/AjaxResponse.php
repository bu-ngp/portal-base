<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 27.07.2017
 * Time: 10:50
 */

namespace common\widgets\GridView\services;

/**
 * Класс ajax ответа http запроса.
 *
 * Пример использования:
 *
 * ```php
 *     // Controller
 *     public function actionDelete($id)
 *     {
 *         try {
 *             $this->service->delete($id);
 *         } catch (\Exception $e) {
 *             // Ответ в случае ошибки
 *             return AjaxResponse::init(AjaxResponse::ERROR, $e->getMessage());
 *         }
 *         // В случае отсутствия ошибок, оспешный ответ
 *         return AjaxResponse::init(AjaxResponse::SUCCESS);
 *     }
 * ```
 *
 * Ответ запроса:
 *
 * ```json
 *   [
 *      "result": "error",
 *      "message": "Unknown error!"
 *   ]
 * ```
 */
class AjaxResponse
{
    /** Результат запроса успешен */
    const SUCCESS = 'success';
    /** В результате запроса проихошла ошибка */
    const ERROR = 'error';

    /**
     * @var string Результат ответа запроса, одно из двух значений (`AjaxResponse::SUCCESS`, `AjaxResponse::ERROR`)
     */
    public $result;
    /**
     * @var string Строка ответа
     */
    public $message;

    /** Конструктор класса
     *
     * @param string $result Результат ответа запроса, одно из двух значений (`AjaxResponse::SUCCESS`, `AjaxResponse::ERROR`)
     * @param string $message Строка ответа
     */
    public function __construct($result, $message = '')
    {
        $this->result = $result;
        $this->message = $message;
    }

    /**
     * Создать экземпляр текущего класса
     *
     * @param string $result Результат ответа запроса, одно из двух значений (`AjaxResponse::SUCCESS`, `AjaxResponse::ERROR`)
     * @param string $message Строка ответа
     * @return $this
     */
    public static function init($result, $message = '')
    {
        return new self($result, $message);
    }
}