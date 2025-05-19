<?php

namespace App\Stage1Feature;

use Introvert\ApiClient;
use Introvert\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        $form = $this->createFormBuilder()
            ->add('from', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'date'
                ]
            ])
            ->add('to', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'date'
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Calculate Statistics'])
            ->getForm();

        $form->handleRequest($request);

        $stats = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $stats = $this->calculateStatisticsData(
                $data['from']->getTimestamp(),
                $data['to']->getTimestamp()
            );
        }
        return $this->render('stage1_feature/index.html.twig', [
            'form' => $form->createView(),
            'stats' => $stats
        ]);
    }
    private function getClients(): array
    {
        // $clients = $this->apiClient->account->users()['result'];
        $clients = [
            [
                'id' => 9585804,
                'name' => 'test_introvert',
                'api' => '23bc075b710da43f0ffb50ff9e889aed',
            ],
            [
                'id' => 2,
                'name' => 'artedegrass0',
                'api' => '23bc075b710da43f0ffb50ff9e889aee',
            ],
        ];

        foreach ($clients as &$client) {
            try {
                Configuration::getDefaultConfiguration()
                    ->setApiKey('key', $client['api']);
                $client['valid'] = boolval($this->apiClient->account->info()['result']);
            } catch (\Exception $e) {
                $client['valid'] = false;
            }
        }
        Configuration::getDefaultConfiguration()
            ->setApiKey('key', '23bc075b710da43f0ffb50ff9e889aed');
        return $clients;
    }

    private function calculateStatisticsData(int $fromTimestamp, int $toTimestamp): array
    {
        $clients = $this->getClients();

        $deals = $this->getDeals([142]);
        //filter deals with 0 price
        $deals = array_filter($deals, fn($deal) => ($deal['price'] ?? 0) != 0);
        if ($fromTimestamp && $toTimestamp) {
            $deals = array_filter($deals, function ($deal) use ($fromTimestamp, $toTimestamp) {
                return isset($deal['date_close']) &&
                    $deal['date_close'] >= $fromTimestamp &&
                    $deal['date_close'] <= $toTimestamp;
            });
        }

        usort($deals, function ($a, $b) {
            return $b['date_close'] - $a['date_close'];
        });

        $totalSum = array_reduce($deals, function ($carry, $deal) {
            return $carry + ($deal['price'] ?? 0);
        }, 0);

        foreach ($clients as &$client) {
            $client['deals'] = [];
            $client['total'] = 0;
            if ($client['valid']) {
                $client['deals'] = array_filter($deals, function ($deal) use ($client) {
                    return $deal['account_id'] == $client['id'];
                });
                $client['total'] = array_reduce($client['deals'], function ($sum, $deal) {
                    return $sum + ($deal['price'] ?? 0);
                }, 0);
            }
        }

        $stats = [
            'from' => $fromTimestamp,
            'to' => $toTimestamp,
            'data' => [
                'total_sum' => $totalSum,
                'total_deals' => count($deals),
                'clients' => $clients,
                'deals' => $deals
            ]
        ];
        // dd($stats);
        return $stats;
    }
    private function getDeals(array $status = []): array
    {
        return $this->apiClient->lead->getAll(null, $status)['result'] ?? [];
    }
}
