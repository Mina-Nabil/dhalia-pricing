<?php

namespace App\Policies;

use App\Models\Offers\Offer;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\App;

class OfferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewProfit(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function canExport(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Offer $offer): bool
    {
        return $user->is_admin || $user->id === $offer->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin || $user->id == 4 || App::environment('local');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Offer $offer): bool
    {
        return $user->is_admin;
    }

    public function updateNotes(User $user, Offer $offer): bool
    {
        return $user->is_admin || $user->id === $offer->user_id;
    }

    public function updateStatus(User $user, Offer $offer): bool
    {
        return $user->is_admin || $user->id === $offer->user_id;
    }

    public function addComment(User $user, Offer $offer): bool
    {
        return $user->is_admin || $user->id === $offer->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Offer $offer): bool
    {
        return $user->is_admin || $user->id === $offer->user_id;
    }
}
