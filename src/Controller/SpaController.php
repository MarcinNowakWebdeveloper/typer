<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SpaController extends AbstractController
{
    #[Route('/{route}', name: 'spa', requirements: ['route' => '^(?!api|_profiler).+'], defaults: ['route' => null])]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}
