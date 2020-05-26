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
            'id'       => $user->getId(),
            'email'    => $user->getEmail(),
            'fullName' => $user->getFullName()
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
    public function formatFull(User $user): array
    {
        $userDetails = array_merge($this->format($user), [
            'isAdmin' => $user->isAdmin()
        ]);

        return $userDetails;
    }
}
