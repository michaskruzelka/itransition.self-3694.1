<?php

namespace App\Utils;

use App\Exception\InvalidArgumentException;

/**
 * Class Validator.
 *
 * @author Michael Marchanka <m.marchenko@itransition.com>
 */
class Validator
{
    /**
     * @param null|string $username
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function validateUsername(?string $username): string
    {
        if (empty($username)) {
            throw new InvalidArgumentException('The username can not be empty.');
        }

        if (1 !== preg_match('/^[0-9a-z_]+$/', $username)) {
            throw new InvalidArgumentException('The username must contain only lowercase latin characters, numbers and underscores.');
        }

        return $username;
    }

    /**
     * @param null|string $plainPassword
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function validatePassword(?string $plainPassword): string
    {
        if (empty($plainPassword)) {
            throw new InvalidArgumentException('The password can not be empty.');
        }

        if (mb_strlen(trim($plainPassword)) < 6) {
            throw new InvalidArgumentException('The password must be at least 6 characters long.');
        }

        return $plainPassword;
    }

    /**
     * @param null|string $email
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function validateEmail(?string $email): string
    {
        if (empty($email)) {
            throw new InvalidArgumentException('The email can not be empty.');
        }

        if (false === mb_strpos($email, '@')) {
            throw new InvalidArgumentException('The email should look like a real email.');
        }

        return $email;
    }

    /**
     * @param null|string $fullName
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function validateFullName(?string $fullName): string
    {
        if (empty($fullName)) {
            throw new InvalidArgumentException('The full name can not be empty.');
        }

        return $fullName;
    }
}
