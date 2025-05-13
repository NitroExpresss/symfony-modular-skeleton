<?php

namespace App\Stage1Feature;

use Introvert\ApiClient;
use Introvert\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends AbstractController
{
    public function __construct(
        private readonly ApiClient $apiClient
    ) {
    }

    public function index()
    {
        //todo:refactor configuration
        Configuration::getDefaultConfiguration()->setHost('https://api.s1.yadrocrm.ru/tmp')->setApiKey('key', '%env(INTROVERT_YADRO_API_KEY)%');
        
        return new Response(dd($this->apiClient));
        
        // ...
    }
}