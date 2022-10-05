<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'age',
        'gender',
        'interest',
        'bio'
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
    ];
         /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    // return picture
    public function picture(){

        return $this->hasOne(Picture::class,'user_id');
    }
    // return location
    public function location(){

        return $this->hasOne(Location::class,'user_id');
    }

    public function favourite()
    {
        return $this->belongsToMany(User::class, 'user_favourites', 'user_id', 'favourite_id');
    }

    public function block()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'user_id', 'blocked_id');
    }

    public function blockedBy()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_id', 'user_id');
    }

    public function chat()
    {
        return $this->belongsToMany(User::class, 'chats', 'user1_id', 'user2_id')->withPivot('id');
    }

    public function chatwith()
    {
        return $this->belongsToMany(User::class, 'chats', 'user2_id', 'user1_id')->withPivot('id');
    }

    public function message()
    {
        return $this->belongsToMany(Chat::class, 'messages', 'user_id', 'chat_id')->withPivot('message');
    }
}
