<?php

namespace App\Stage2;

use DateInterval;
use DatePeriod;
use DateTime;
use Introvert\ApiClient;
use Introvert\Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller extends AbstractController
{


    public function __construct(
        private readonly ApiClient $apiClient
    ) {
        Configuration::getDefaultConfiguration()
            ->setHost('https://api.s1.yadrocrm.ru/tmp')
            ->setApiKey('key', '23bc075b710da43f0ffb50ff9e889aed');
    }

    public function index(Request $request): Response
    {
        return $this->render('stage2_feature/index.html.twig', [
        ]);
    }
    public function refreshDays(): JsonResponse
    {

        $dateFieldId = 968675;
        $threeStatuses = [9585813, 142, 143];

        // deals in one of the 3 statuses
        $deals = $this->getDeals($threeStatuses);

        //deals with price !=0
        // $deals = array_filter($deals, fn($deal) => $deal['price'] != 0);

        //custom date field id 
        $dateFieldId = 968675;

        //deals with the field !=0
        $deals = array_filter(
            $deals,
            fn($deal) => !empty($deal['custom_fields']) &&
                in_array(
                    $dateFieldId,
                    array_column($deal['custom_fields'], 'id'),
                    true
                )
        );
        // deals within the date range 
        $today = new DateTime();
        $endDate = (clone $today)->modify('+31 days');
        $deals = array_filter($deals, function ($deal) use ($today, $endDate, $dateFieldId) {
            foreach ($deal['custom_fields'] as $field) {
                if ($field['id'] === $dateFieldId) {
                    $dealDate = new DateTime($field['values'][0]['value']);
                    return $dealDate >= $today && $dealDate <= $endDate;
                }
            }
            return false;
        });
        //preparing available dates
        $result = [];
        $current = clone $today;
        //fill keys 
        while ($current <= $endDate) {
            $result[$current->format('Y-m-d')] = 0;
            $current->modify('+1 day');
        }

        foreach ($deals as $deal) {
            foreach ($deal['custom_fields'] as $field) {
                if ($field['id'] === $dateFieldId) {
                    $dealDate = new DateTime($field['values'][0]['value']);
                    $dayKey = $dealDate->format('Y-m-d');
                    if (isset($result[$dayKey])) {
                        $result[$dayKey]++;
                    }
                    break;
                }
            }
        }
        $result = array_filter($result, fn($count) => $count >= 5);
        //set values
        $result = array_fill_keys(array_keys($result), false);
        return new JsonResponse($result);
    }

    private function getDeals(array $status = [], int $fromTimestamp = 0, int $toTimestamp = 0): array
    {
        return $this->apiClient->lead->getAll(null, $status, null, null,)['result'] ?? [];
    }
}
