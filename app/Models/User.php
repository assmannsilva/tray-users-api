<?php

namespace App\Models;

use App\Helpers\SodiumCrypto;
use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    protected function encryptedAttribute(string $column): Attribute
    {
        $crypt_key = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.$column");
        return Attribute::make(
            get: fn($value) => $value ? SodiumCrypto::decrypt($value, $crypt_key) : null,
            set: fn($value) => $value ? SodiumCrypto::encrypt($value, $crypt_key) : null,
        );
    }

    protected function encryptCpfIndex(): void
    {
        if($this->cpf === \null) return;
        $crypt_index = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.cpf_index");
        $this->attributes["cpf_index"] = SodiumCrypto::getIndex($this->cpf, $crypt_index);
    }

    protected function encryptFirstNameIndex(): void
    {
        if($this->name === \null) return;
        $crypt_index = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.name_index");
        
        $names = explode(" ", $this->name);
        $first_name = $names[0];

        $this->attributes["first_name_index"] = SodiumCrypto::getIndex($first_name, $crypt_index);
    }

    protected function encryptSurnameTokens(): void
    {
        if($this->name === \null) return;
        $crypt_index = SodiumCrypto::getCryptKey("app.crypted_user_columns_keys.name_index");

        $surnames = explode(" ", $this->name);
        \array_shift($surnames);
        $surnames_tokens = \array_map(fn($surname) => SodiumCrypto::getIndex($surname, $crypt_index), $surnames);
        
        $this->attributes["surname_tokens"] = \json_encode($surnames_tokens);
    }

    protected function cpf(): Attribute
    {
        return $this->encryptedAttribute('cpf');
    }   

    protected function name(): Attribute
    {
        return $this->encryptedAttribute('name');
    }

    protected function birthday(): Attribute
    {
        return $this->encryptedAttribute('birthday');
    }

    protected function google_token(): Attribute
    {
        return $this->encryptedAttribute('google_token');
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
