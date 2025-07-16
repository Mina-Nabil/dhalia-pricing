<?php

namespace App\Models\Clients;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'client';

    protected $fillable = [
        'name',
        'code',
        'phone',
        'address',
        'email',
        'notes',
        'created_by_id',
        'country_name',
    ];

    public function scopeSearch($query, $search)
    {
        $words = explode(' ', $search);
        $query->where(function ($query) use ($words) {
            foreach ($words as $word) {
                $query->orWhere('name', 'like', '%' . $word . '%');
                $query->orWhere('code', 'like', '%' . $word . '%');
                $query->orWhere('phone', 'like', '%' . $word . '%');
                $query->orWhere('email', 'like', '%' . $word . '%');
            }
        });
        return $query;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'client_users', 'client_id', 'user_id');
    }

    public function infos(): HasMany
    {
        return $this->hasMany(ClientInfo::class);
    }
}
