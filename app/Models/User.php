<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable //nah ini contoh klo nama class singlular dengan nama table plural
{
    use HasFactory, HasApiTokens, Notifiable; //AuthenticatableTrait;

    protected $primaryKey = "userid";

    protected $fillable = [
        'email', 'password', 'username'
        //Tambahkan atribut lainnya yang ingin Anda izinkan untuk dimasukkan secara massal
    ];

    public function post(): HasMany
    {
        return $this->hasMany(Post::class, 'userid');
    }
    public function friend(): HasMany
    {
        return $this->hasMany(Friend::class, 'userid');
    }
}
