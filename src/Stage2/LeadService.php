<?php

namespace App\Stage2\Service;

use DateInterval;
use DatePeriod;
use DateTime;
use Introvert\ApiClient;

class DealService
{
    private const DATE_FIELD_ID = 968675;
    private const THREE_STATUSES = [9585813, 142, 143];
    public function __construct(
        private readonly ApiClient $apiClient
    ) {}
    public function getAvailableDays(): array
    {
        $deals = $this->getDealsWithDateField();
        $dateRange = $this->generateDateRange();
        $daysCount = $this->countDealsPerDay($deals, $dateRange);

        return array_fill_keys(
            array_keys(
                array_filter($daysCount, fn($count) => $count >= 5)
            ),
            false
        );
    }
    private function getDealsWithDateField(): array
    {
        $deals = $this->getDeals(self::THREE_STATUSES);

        return array_filter(
            $deals,
            fn($deal) => !empty($deal['custom_fields']) &&
                in_array(
                    self::DATE_FIELD_ID,
                    array_column($deal['custom_fields'], 'id'),
                    true
                )
        );
    }
    private function generateDateRange(): array
    {
        $today = new DateTime();
        $endDate = (clone $today)->modify('+31 days');
        $result = [];
        $current = clone $today;

        while ($current <= $endDate) {
            $result[$current->format('Y-m-d')] = 0;
            $current->modify('+1 day');
        }

        return $result;
    }
    private function countDealsPerDay(array $deals, array $dateRange): array
    {
        $result = $dateRange;
        $today = new DateTime();
        $endDate = (clone $today)->modify('+31 days');
        foreach ($deals as $deal) {
            foreach ($deal['custom_fields'] as $field) {
                if ($field['id'] === self::DATE_FIELD_ID) {
                    $dealDate = new DateTime($field['values'][0]['value']);
                    if ($dealDate >= $today && $dealDate <= $endDate) {
                        $dayKey = $dealDate->format('Y-m-d');
                        if (isset($result[$dayKey])) {
                            $result[$dayKey]++;
                        }
                    }
                    break;
                }
            }
        }
        return $result;
    }
    private function getDeals(array $status = []): array
    {
        return $this->apiClient->lead->getAll(null, $status, null, null,)['result'] ?? [];
    }
}
