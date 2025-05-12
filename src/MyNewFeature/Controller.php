<?php

declare(strict_types=1);

namespace App\MyNewFeature;

use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public function showTime(): Response
    {
        return new Response('<h1>Current Time: ' . date('Y-m-d H:i:s') . '</h1>');
    }
}
