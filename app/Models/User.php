<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Validator;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function leiloes()
    {
        return $this->belongsToMany(Leilao::class);
    }

    public function lances()
    {
        return $this->hasMany(Lance::class);
    }

    public function isValid()
    {
        $validator = Validator::make($this->attributes, [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:75|unique:users',
            'password' => 'required|string|min:8|max:16',
        ]);

        return !$validator->fails();
    }
}
