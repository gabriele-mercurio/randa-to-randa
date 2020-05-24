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
    public function format(User $user): array
    {
        $userDetails = [
            'id'    => $user->getId(),
            'username' => $user->getUsername()
        ];

        return $userDetails;
    }

    /**
     * @param User      $user
     *
     * @return array
     */
    public function formatFull(User $user): array
    {
        $userDetails = $this->format($user);

        return $userDetails;
    }
}
