<?php

declare(strict_types=1);

namespace App\Exception;

use ApiPlatform\Core\Exception\InvalidArgumentException as ApiPlatformInvalidArgumentException;
use GraphQL\Error\ClientAware;

/**
 * Class InvalidArgumentException.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class InvalidArgumentException extends ApiPlatformInvalidArgumentException implements ClientAware
{
    /**
     * @return bool
     */
    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return 'arguments';
    }
}
