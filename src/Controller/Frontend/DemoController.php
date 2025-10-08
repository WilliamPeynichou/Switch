<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends AbstractController
{
    #[Route('/demo/vintage-sport', name: 'app_demo_vintage_sport')]
    public function vintageSport(): Response
    {
        return $this->render('frontend/demo/vintage-sport.html.twig');
    }
}
