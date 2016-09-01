<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'books';
    
     /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
    
    protected static $createRules = array(
        'name' => 'required|min:3|max:255',                
        'latitude' => ['regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        'longitude' => ['regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        'category_id' => 'required|existing_book_category',
        'user_id' => 'required',
        'purpose' => 'in:"away","trade","sell"',
        'address' => 'required',
        'sale_off' => 'numeric',
        'price' => 'numeric',
        'description' => 'required',
        'publisher' => 'required',
        'images'    => 'required'
    );

    public static function getCreateRules() {
        return self::$createRules;
    }
    
    public function photos() {
    	return $this->hasMany('App\Photo');
    }

}
