<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\ApiResponse;
use Illuminate\Http\Request;
use App\Components\SimpleImage;
use App\Book;
use App\Photo;
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
            $book->category_id = $input['category_id'];
            $book->user_id = $input['user_id'];
            $book->address = $input['address'];
            $book->description = $input['description'];
            $book->publisher = $input['publisher'];
            
            $book->latitude = $request->has('latitude') ? $input['latitude'] : null;
            $book->longitude = $request->has('longitude') ? $input['longitude'] : null;
            $book->purpose = $request->has('purpose') ? $input['purpose'] : null;
            $book->sale_off = $request->has('sale_off') ? $input['sale_off'] : null;
            $book->price = $request->has('price') ? $input['price'] : null;
            
            // getting all of the post data
            $images = $request->file('images');
                                           
            $validateImage = true;
            
            foreach ($images as $image) {
                $rules = array('images' => 'mimes:jpeg,bmp,png,gif');
                $validator = Validator::make(array('images' => $image), $rules);
                if (!$validator->passes()) {
                    $validateImage = false;
                    return ApiResponse::errorValidation($validator);
                } 
            }
            
            if ($validateImage == true) {
                // Save book first to get it id
                if (!$book->save()) {
                    return ApiResponse::errorInternal('An error occured. Please, try again.');
                }
                
                // Remove old images if exists
                $this->removeBookPhoto($book);
                
                // Save photo images
                foreach ($images as $image) {
                    $this->saveBookPhoto($book, $image);
                }
                
            }
            
        } else {
            return ApiResponse::errorValidation($validator);
        } 
                        
        $returnBook = Book::find($book->id);
        $returnBook->photos;       
        
        return ApiResponse::successResponse($returnBook->toArray());
    }
    
    public function index() {
        $query = $this->processInput();               

        $result = Book::getAll($query['where'], $query['sort'], $query['limit'], $query['offset']);
        
        if (count($result) > 0) {    
            // Add User info to event list
            foreach ($result as $id=>$object) {                 
                $object->photos = Photo::where('book_id', '=', $object->id)->get()->toArray();
            }
            
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
    
    protected function removeBookPhoto($book) {
        $uploadPath = '/uploads/';
        $bookPath = '/uploads/books/';

        if (!is_dir(public_path() . $uploadPath)) {
            mkdir(public_path() . $uploadPath, 0777, true);
        }

        if (!is_dir(public_path() . $bookPath)) {
            mkdir(public_path() . $bookPath, 0777, true);
        }

        $bookPathWithId = $bookPath . 'book_' . $book->id . '/';
        if (!is_dir(public_path() . $bookPathWithId)) {
            mkdir(public_path() . $bookPathWithId, 0777, true);
        }

        // Remove old files                
        $files = glob(public_path() . $bookPathWithId . '*'); // get all file names                
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
      
        return true;
    }
    
    protected function saveBookPhoto($book, $imageFile) {
        $uploadPath = '/uploads/';
        $bookPath = '/uploads/books/';

        if (!is_dir(public_path() . $uploadPath)) {
            mkdir(public_path() . $uploadPath, 0777, true);
        }

        if (!is_dir(public_path() . $bookPath)) {
            mkdir(public_path() . $bookPath, 0777, true);
        }

        $bookPathWithId = $bookPath . 'book_' . $book->id . '/';
        if (!is_dir(public_path() . $bookPathWithId)) {
            mkdir(public_path() . $bookPathWithId, 0777, true);
        }

        $extension = $imageFile->getClientOriginalExtension();

        $imgName = 'image_' . uniqid() . '_' . time() . '.' . $extension;

        $imgNamePublicPath = public_path() . $bookPathWithId . $imgName;

        $imageSize = getimagesize($imageFile);

        $imageFile->move(public_path() . $bookPathWithId, $imgName);

        if ($imageSize[0] > 960 || $imageSize[1] > 960) {
            try {
                $img = new SimpleImage($imgNamePublicPath);
                $img->best_fit(960, 960)->save($imgNamePublicPath);
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }

        $bookPhoto = new Photo();
        $bookPhoto->book_id = $book->id;
        $bookPhoto->link = url('/') . $bookPathWithId . $imgName;
                
        if (!$bookPhoto->save()) {
            return ApiResponse::errorInternal('An error occured. Please, try again.');
        }
        
        return true;
    }
}
