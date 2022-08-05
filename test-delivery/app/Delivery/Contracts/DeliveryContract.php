<?php

namespace App\Delivery\Contracts;

interface DeliveryContract
{
    /**
     * Метод расчета данных
     * @return array|string
     */
    public function calculate();

    /**
     * Валидация полученных данных
     * @return bool
     */
    public function validateData() :bool;

    /**
     * Приведение данных к нужному формату
     * @param float $price
     * @param string|int $dateOrDay
     * @return array
     */
    public function formatData(float $price, $dateOrDay) :array;
}
