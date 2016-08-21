<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected static $createRules = array(
        'name' => 'required|min:3|max:255',
        'password' => 'required|min:6|max:255|confirmed',
        'password_confirmation' => 'required|min:6|max:255',
        'email' => 'required|email|unique:users,email',
        'latitude' => ['regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        'longitude' => ['regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        'birthday' => 'before:now|date_format:"Y-m-d"',
        'avatar' => 'mimes:jpeg,bmp,png'
    );

    public static function getCreateRules() {
        return self::$createRules;
    }

}
