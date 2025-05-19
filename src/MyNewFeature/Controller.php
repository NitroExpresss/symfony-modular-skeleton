<?php

declare(strict_types=1);

namespace App\MyNewFeature;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends AbstractController
{
    public function showTime(): Response
    {
        return $this->render('index.html.twig');
    }
}
