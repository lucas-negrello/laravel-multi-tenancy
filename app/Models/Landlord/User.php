<?php

namespace App\Models\Landlord;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasPermissions;
use App\Traits\HasRoles;
use App\Traits\HasTenants;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class User extends Authenticatable
{
    use UsesLandlordConnection, Notifiable, HasApiTokens, HasRoles, HasPermissions, HasTenants, SoftDeletes;

    const
        INACTIVE = 0,
        ACTIVE = 1,
        PENDING_EMAIL_VERIFICATION = 2,
        PENDING_APPROVAL = 3,
        SUSPENDED = 4,
        BANNED = 5,
        PASSWORD_RESET_REQUIRED = 6,
        LOCKED = 7;

    const STATUSES = [
        self::INACTIVE,
        self::ACTIVE,
        self::PENDING_EMAIL_VERIFICATION,
        self::PENDING_APPROVAL,
        self::SUSPENDED,
        self::BANNED,
        self::PASSWORD_RESET_REQUIRED,
        self::LOCKED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'meta'
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'meta' => 'array',
        ];
    }

    protected $appends = [
        'is_root_user'
    ];

    protected static function booted(): void
    {
        static::deleting(function ($user) {
            $user->roles()->detach();
            $user->permissions()->detach();
        });
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => bcrypt($value),
        );
    }

    protected function isRootUser(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->hasRole($this->rootRoles()),
        );
    }
}
