<?php

namespace App\Events;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    public const BEFORE_CREATE = 'user.before_create';
    public const CREATED = 'user.created';
    public const ACTIVATED = 'user.activated';
    public const REMOVED = 'user.removed';
    public const RECOVER_PASSWORD = 'user.recover_password';
    
    /** @var User */
    private $user;

    /**
     * UserEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
