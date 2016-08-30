<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';
    
    protected static $_table = 'categories';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

}
