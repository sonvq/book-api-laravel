<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\ApiResponse;
use Illuminate\Http\Request;
use App\Category;
use Auth;

class CategoryController extends BaseController
{
    public function index() {
        $query = $this->processInput();               

        $result = Category::getAll($query['where'], $query['sort'], $query['limit'], $query['offset']);

        
        if (count($result) > 0) {            
            // TODO: optimize
            foreach ($result as $id=>$object) {
                if(!empty($query['fields'])) {
                    foreach ($object as $key=>$value) {
                        if(in_array($key, $query['fields'])) {
                            continue;
                        } else {
                            unset($object->$key);
                        }
                    }
                }                
            }
                        
        }
        
        return ApiResponse::successResponse($result);       
    }
}
