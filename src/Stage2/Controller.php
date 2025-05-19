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
        return $this->render('stage2_feature/index.html.twig');
    }
    public function refreshDays(): JsonResponse
    {
        //reserve date range
        $today = new DateTime();
        $endDate = (clone $today)->modify('+31 days');
        // date_reservation
        //custom date field id
        $dateFieldId = 968675;
        // statuses
        $statuses = [42883918, 42883915, 42883912];
        // PIPELINE = Carrot;
        // "42883912": {
        // "name": "Первичный контакт",
        // "pipeline_id": 4679716,
        // "id": 42883912
        // },
        // "42883915": {
        //     "name": "Переговоры",
        //     "pipeline_id": 4679716,
        //     "id": 42883915
        // },
        // "42883918": {
        //     "name": "Принимают решение",
        //     "pipeline_id": 4679716,
        //     "id": 42883918
        // }

        // deals in one of the 3 statuses
        $deals = $this->getDeals($statuses);

        //filter price !=0
        // $deals = array_filter($deals, fn($deal) => $deal['price'] != 0);

        // filter deals with empty custom field and out of range date
        $deals = array_filter($deals, function ($deal) use ($today, $endDate, $dateFieldId) {
            if (empty($deal['custom_fields'])) {
                return false;
            }

            foreach ($deal['custom_fields'] as $field) {
                if ($field['id'] === $dateFieldId && !empty($field['values'][0]['value'])) {
                    $dealDate = new DateTime($field['values'][0]['value']);
                    return $dealDate >= $today && $dealDate <= $endDate;
                }
            }

            return false;
        });
        //preparing available dates
        $result = [];
        $current = clone $today;
        while ($current <= $endDate) {
            $result[] = $current->format('Y-m-d');
            $current->modify('+1 day');
        }
        $dateCounts = [];
        foreach ($deals as $deal) {
            foreach ($deal['custom_fields'] as $field) {
                if ($field['id'] === $dateFieldId) {
                    $dealDate = new DateTime($field['values'][0]['value']);
                    $dayKey = $dealDate->format('Y-m-d');
                    if (!isset($dateCounts[$dayKey])) {
                        $dateCounts[$dayKey] = 0;
                    }
                    $dateCounts[$dayKey]++;
                    break;
                }
            }
        }

        // Filter the result to only include dates that have 5 or more deals
        $result = array_filter($result, function ($date) use ($dateCounts) {
            return isset($dateCounts[$date]) && $dateCounts[$date] >= 5;
        });

        // Re-index the array (optional)
        $result = array_values($result);
        
        return new JsonResponse($result);
    }

    private function getDeals(array $statuses = []): array
    {
        return $this->apiClient->lead->getAll(null, $statuses)['result'] ?? [];
    }
}
