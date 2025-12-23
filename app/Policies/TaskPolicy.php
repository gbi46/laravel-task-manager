<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $this->ownsTask($user, $task);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        return $this->ownsTask($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->ownsTask($user, $task);
    }

    public function restore(User $user, Task $task): bool
    {
        return $this->ownsTask($user, $task);
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return $this->ownsTask($user, $task);
    }

    private function ownsTask(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }
}
