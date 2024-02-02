<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function homepage(): Response
    {
        $t= 1;
        $test = 2;
        $r = 3;
        return $this->render('main/homepage.html.twig');
    }
}
