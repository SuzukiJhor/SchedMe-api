<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determina se o usuário pode atualizar o evento.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }

    /**
     * Determina se o usuário pode deletar o evento.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }

    /**
     * Determina se o usuário pode visualizar o evento.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }
}
