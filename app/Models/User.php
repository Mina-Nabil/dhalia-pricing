<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const MORPH_TYPE = 'user';

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'role',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    //attributes
    public function getIsAdminAttribute()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getIsUserAttribute()
    {
        return $this->role === self::ROLE_USER;
    }

    //scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('is_active', $status);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeBySearch($query, $search)
    {
        $strings = explode(' ', $search);
        return $query->where(function ($query) use ($strings) {
            foreach ($strings as $string) {
                $query->where('name', 'like', '%' . $string . '%')
                    ->orWhere('id', '=', $string)
                    ->orWhere('username', 'like', '%' . $string . '%');
            }
        });
    }
}
