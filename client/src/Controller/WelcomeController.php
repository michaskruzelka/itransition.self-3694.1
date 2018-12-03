<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class WelcomeController.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     *
     * @return Response
     */
    public function home(): Response
    {
        return $this->render('welcome/home.html.twig');
    }
}
