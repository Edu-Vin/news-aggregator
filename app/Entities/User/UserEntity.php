<?php

namespace App\Entities\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserEntity extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function preference(): HasOne {
        return $this->hasOne(UserPreferenceEntity::class, 'user_id', 'id');
    }

    /**
     * Get or create user preferences
     */
    public function getOrCreatePreference(): UserPreferenceEntity
    {
        return $this->preference()->firstOrCreate([
            'user_id' => $this->id,
        ]);
    }

    /**
     * Check if user has any preferences set
     */
    public function hasPreferences(): bool
    {
        if (!$this->preference) {
            return false;
        }

        return $this->preference->hasAnyPreferences();
    }
}
