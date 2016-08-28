<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\ApiResponse;
use Illuminate\Http\Request;
use App\Book;
use App\Category;
use Auth;

class BookController extends BaseController
{
    public function store (Request $request) {

        $input = $request->all();
        $book = '';
        $user = Auth::user();
        
        $input['user_id'] = $user->id;
                       
        Validator::extend('existing_book_category', function($attribute, $value, $parameters)
        {
            $categoryObject = Category::find($value);
            if (empty($categoryObject)) {
                return false;
            }
            return true;            
        });
        
        Validator::replacer('existing_book_category', function($message, $attribute, $rule, $params) {
            return str_replace('_', ' ' , 'The '. $attribute .' does not exist');
        });
        
        
        $validator = Validator::make($input, Book::getCreateRules());

        if ($validator->passes()) {

            $book = new Book();
            $book->name = $input['name'];
            $book->cateogry_id = $input['cateogry_id'];
            
            $book->latitude = $request->has('latitude') ? $input['latitude'] : null;
            $book->longitude = $request->has('longitude') ? $input['longitude'] : null;
            
            if (!$book->save()) {
                return ApiResponse::errorInternal('An error occured. Please, try again.');
            }
            
        } else {
            return ApiResponse::errorValidation($validator);
        }               
        
        return ApiResponse::successResponse($book->toArray());
    }
}
