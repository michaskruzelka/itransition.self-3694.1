<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Attempt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;

/**
 * Class AttemptController.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class AttemptController extends AbstractController
{
    /**
     * @param string  $id
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @Route("/attempt/suspend/{id}", name="attempt_suspend", requirements={"id"=Uuid::VALID_PATTERN}, methods={"GET"})
     */
    public function suspend($id, Request $request): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Attempt $attempt */
        $attempt = $em->find(Attempt::class, $id);

        if ($attempt && $attempt->getAuthor() === $this->getUser() && $attempt->inProgress()) {
            if ($attempt->getUpdatedAt() < $attempt->getQuiz()->getUpdatedAt()) {
                $em->remove($attempt);
                $this->addFlash('warning', 'The quiz has been updated since you were not here. Please start over.');
            } else {
                $attempt->setStatus($attempt::getStatuses()['SUSPENDED']);
                $attempt->calculateDuration();
                $em->persist($attempt);
                $this->addFlash('info', 'The quiz has been suspended.');
            }

            $em->flush();
        }

        return $this->redirectToRoute('quizzes_list');
    }
}
