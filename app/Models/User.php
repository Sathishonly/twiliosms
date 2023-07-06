<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $connection = 'mongodb';
	protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'companyName',
        'country',
        'email',
        'message'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
       
    ];

   
}