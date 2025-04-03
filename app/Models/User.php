<?php

namespace App\Models;

use App\Models\Traits\HasEncrypt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasEncrypt;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'google_token',
        'birthday',
        'cpf',
        "cpf_index",
        "first_name_index",
        "surname_tokens",
    ];

    protected $casts = [
        "surname_tokens" => "array",
    ];

    protected $hidden = [
        "google_token",
    ];

    protected function cpf(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('cpf'));
    }   

    protected function name(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('name'));
    }

    protected function google_token(): Attribute
    {
        return Attribute::make(...$this->makeEncryptedAttributeCallables('google_token'));
    }


    protected static function booted()
    {
        static::created(function (User $user) {
            Cache::tags("filter_search")->flush();
            
        });

        static::saving(function (User $user) {
            $user->encryptCpfIndex();
            $user->encryptFirstNameIndex();
            $user->encryptSurnameTokens();
        });
    }
}
