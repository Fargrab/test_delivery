<?php

return [
    /*
     * Настройки подключаемых служб доставки
     * Каждый партнер должен наследовать абстрактный класс AbstractDeliveryPartner
     * и выполнять контракт DeliveryContract
     */

    'bird' => [
        'name'  => 'Птичка',
        'class' => App\Delivery\Bird::class
    ],

    'turtle' => [
        'name'  => 'Черепашка',
        'class' => App\Delivery\Turtle::class,
        'coefficient' => 150
    ],

];
