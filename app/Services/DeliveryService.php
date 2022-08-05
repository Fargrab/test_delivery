<?php

namespace App\Services;

use App\Delivery\AbstractDeliveryPartner;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use phpDocumentor\Reflection\Types\Mixed_;

class DeliveryService
{
    /**
     * Конфигурационные данные
     * @var
     */
    private $config;

    /**
     * @var string - адрес доставки
     */
    private $address_to;

    /**
     * @var string - адрес отправителя
     */
    private $address_from;

    /**
     * @var array - товары для отправки
     */
    private $items;

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

    public function __construct()
    {
        try {
            $this->config = config('delivery_partners');
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), 'Ошибка при загрузке конфигурационного файла');
        }
    }

    /**
     * Метод установки данных по отправке
     * @param string $address_from
     * @param string $address_to
     * @param array $items
     */
    public function setDataForCalculate(
        string  $address_from,
        string  $address_to,
        array   $items
    )
    {
        $this->address_from = $this->formatAddress($address_from);
        $this->address_to = $this->formatAddress($address_to);
        $this->items = $items;
    }

    /**
     * Работа с указанным адресом
     * @param string $address
     * @return string
     */
    public function formatAddress(string $address) :string
    {
        // Произоводим магию с адресом (проверяем форматируем либо получаем координаты)
        return $address;
    }

    /**
     * @param string $address_from
     * @param string $address_to
     * @param array $items
     * @param array|null $delivery_partners - необходимые партнеры для расчета (если null считаем по всем)
     * @return array|null
     */
    public function calculate(
        string  $address_from,
        string  $address_to,
        array   $items,
        array   $delivery_partners = null
    )
    {
        try {
            $this->setDataForCalculate(
                $address_from,
                $address_to,
                $items
            );
        } catch (\Exception $e) {
            $this->setError(null, 'Данные для доставки указаны не верно, пожалуйста, проверьте и попробуйте еще раз');
        }

        if ($this->validationData()) {
            if ($delivery_partners == null || empty($delivery_partners))
            {
                $this->calculateAllPartners();
            } else {
                foreach($items as $partner_slug) {
                    $this->calculateByPartnerSlug($partner_slug);
                }
            }
        }

        return $this->result;
    }

    /**
     * Метод получения расчета доставки по всем павртнерам
     */
    public function calculateAllPartners()
    {
        if ($this->validationData()) {
            foreach ($this->config as $partner_slug => $partner) {
                $this->calculateByPartnerSlug($partner_slug);
            }
        }
    }

    /**
     * Расчет по конкретному партнеру
     * @param string $partner_slug
     */
    private function calculateByPartnerSlug(string $partner_slug)
    {
        try {
            $delivery_partner = new $this->config[$partner_slug]['class']($this->address_from, $this->address_to, $this->items);
            if ($delivery_partner instanceof AbstractDeliveryPartner) {
                $this->result[$partner_slug] = $delivery_partner->calculate($this->address_from, $this->address_to, $this->items);
            } else {
                $this->result[$partner_slug] = 'Ошибка при попытке расчета доставки';
            }
        } catch (\Exception $e) {
            $this->result[$partner_slug] = 'Ошибка при попытке расчета доставки';
            $this->setError($e->getMessage(), 'Ошибка при попытка расчета доставки у ' . $partner_slug);
        }
    }

    /**
     * Валидирование данных, необходимо расширить
     * @return bool
     */
    private function validationData() :bool
    {
        // Валидация явно должна иметь более сложную логику, но пока так=)
        if ($this->address_to && $this->address_from && $this->items && !$this->error) {
            return true;
        }

        $this->setError(null, 'Данные для доставки указаны не верно, пожалуйста, проверьте и попробуйте еще раз');
        return false;
    }

    /**
     * Устанавливаем ошибки. Пишем лог
     * @param string|null $error_description
     * @param string|null $error_system_message
     */
    private function setError(string $error_system_message = null, string $error_description = null)
    {
        $this->error = true;
        $this->error_description = $error_description;
        $this->error_system_message = $error_system_message;

        Log::critical('Произошла ошибка при работе с сервисом доставки: ' . $error_description . ' (' . $error_system_message . ')');
    }
}
