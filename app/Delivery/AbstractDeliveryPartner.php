<?php

namespace App\Delivery;

use Illuminate\Support\Facades\Log;

class AbstractDeliveryPartner
{
    /**
     * @var array - результаты
     */
    public $result;

    /**
     * @var bool - существование ошибки в работе
     */
    public $error = false;

    /**
     * @var string - описание ошибки
     */
    public $error_description;

    /**
     * @var string - системное опсиание
     */
    public $error_system_message;

    /**
     * @var array - правила валдиации данных
     */
    public $validation_rules = [
        'address_from'    => 'req:string:min.10',
        'address_to'      => 'req:string:min.10',
        'elements'           => 'req:array:min.1'
    ];

    /**
     * Подключаем конфигурации партнера
     * AbstractDeliveryPartner constructor.
     */
    public function __construct(
        string  $address_from,
        string  $address_to,
        array   $elements
    )
    {
        $this->data = [
            'address_from' => $address_from,
            'address_to' => $address_to,
            'elements' => $elements,
        ];

        $this->config = config('delivery_partners')[$this->slug];
    }

    /**
     * Валидация полученных данных
     */
    public function validateData() :bool
    {
        foreach($this->validation_rules as $field => $rules) {
            if (!isset($this->data[$field])) {
                $this->setError(null, 'Ошибка валдиации данных для партнера ' . $this->config['name']);
                return false;
            }
            foreach (explode(':', $rules) as $rule) {
                switch ($rule) {
                    case 'req':
                        return $this->data[$field] == null ? false : true;
                        break;
                    case 'string':
                        return is_string($this->data[$field]);
                        break;
                    case 'array':
                        return is_array($this->data[$field]);
                        break;
                }
            }
        }
        return true;
    }

    /**
     * Устанавливаем ошибки. Пишем лог
     * @param string|null $error_description
     * @param string|null $error_system_message
     */
    public function setError(string $error_system_message = null, string $error_description = null)
    {
        $this->error = true;
        $this->error_description = $error_description;
        $this->error_system_message = $error_system_message;

        Log::critical('Произошла в работе с сервиса доставки: ' . $error_description . ' (' . $error_system_message . ')');
    }
}
