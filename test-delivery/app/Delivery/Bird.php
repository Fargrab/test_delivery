<?php

namespace App\Delivery;

use App\Delivery\Contracts\DeliveryContract;

class Bird extends AbstractDeliveryPartner implements DeliveryContract
{
    public $slug = 'bird';

    public $config;

    public $data;

    public $custom_validation_rules = [
        'weight',
        'width',
        'height',
        'deep'
    ];

    /**
     * Метод расчета данных
     * @return array|string
     */
    public function calculate()
    {
        if ($this->validateData()) {
            // Получаем данные от службы доставки и приводим их к необходимому виду
            return $this->formatData(100500, 3);
        }

        return $this->error_description;
    }

    /**
     * Приведение данных к нужному формату
     * @param float $price
     * @param string|int $dateOrDay
     * @return array
     */
    public function formatData(float $price, $dateOrDay) :array
    {
        return [
            'price' => $price,
            'date'  => date('d.m.Y',strtotime(date('d.m.Y') . ' +' . $dateOrDay . ' days')),
        ];
    }

    /**
     * Валидация полученных данных (добавляем валидацию по птичке)
     * @return bool
     */
    public function validateData() :bool
    {
        if (parent::validateData()) {
            foreach ($this->data['elements'] as $element) {
                foreach ($this->custom_validation_rules as $need_field) {
                    if (!isset($element[$need_field])) {
                        $this->setError(null, 'Данные для доставки указаны не верно, пожалуйста, проверьте и попробуйте еще раз');
                        return false;
                    }
                }
            }
            return true;
        }

        return false;
    }
}
