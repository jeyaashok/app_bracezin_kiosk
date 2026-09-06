<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tji\Traits\TjiTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AuthModel extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, SoftDeletes, TjiTrait;

    protected $guard = 'admin';

    protected $guard_name = 'admin';

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected function password(): Attribute
    {
        return new Attribute(set: fn ($password) => bcrypt($password));
    }
}
