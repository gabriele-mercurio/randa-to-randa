<?php

namespace App\Formatter;

use App\Entity\User;

class UserFormatter
{
    /**
     * UserFormatter constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * @param User $user
     *
     * @return array
     */
    private function format(User $user): array
    {
        $userDetails = [
            'email'    => $user->getEmail(),
            'fullName' => $user->getFullName(),
            'id'       => $user->getId()
        ];

        return $userDetails;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function formatBasic(User $user): array
    {
        return $this->format($user);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function formatForAutocomplete(User $user): array
    {
        return [
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'label' => $user->getFullName() . " (" . $user->getEmail() . ")",
            'value' => $user->getEmail()
        ];
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function formatForSelectFields(User $user): array
    {
        $ret = $this->format($user);
        unset($ret['email']);

        return $ret;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function formatFull(User $user): array
    {
        $userDetails = array_merge($this->format($user), [
            'isAdmin' => $user->isAdmin()
        ]);

        return $userDetails;
    }
}
