<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class AdminContextBuilder.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
final class AdminContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @var SerializerContextBuilderInterface
     */
    private $decorated;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * AdminContextBuilder constructor.
     *
     * @param SerializerContextBuilderInterface $decorated
     * @param AuthorizationCheckerInterface     $authorizationChecker
     */
    public function __construct(SerializerContextBuilderInterface $decorated,
                                AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param Request    $request
     * @param bool       $normalization
     * @param array|null $extractedAttributes
     *
     * @return array
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if ($normalization && $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $context['groups'][] = 'admin:read';
            $context['groups'][] = 'admin:readItem';
        }

        return $context;
    }
}
