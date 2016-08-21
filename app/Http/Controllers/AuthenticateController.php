<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helper\Common;
use App\Http\Helper\ApiResponse;
use App\Components\SimpleImage;
use JWTAuth;
use App\User;

class AuthenticateController extends Controller {

    public function login(Request $request) {

        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return ApiResponse::errorUnauthorized('Wrong email or password');
            }
        } catch (JWTException $e) {
            // something went wrong
            return ApiResponse::errorInternal('Could not create token');
        }
        // if no errors are encountered we can return a JWT
        $user = User::where('email', '=', $credentials['email'])->first();
        
        $returnObject = new \stdClass();
        $returnObject->user = $user;
        $returnObject->token = compact('token');
        
        return ApiResponse::successResponse($returnObject);
    }

    public function register(Request $request) {

        $input = $request->all();
        $user = '';

        $validator = Validator::make($input, User::getCreateRules());

        if ($validator->passes()) {

            $user = new User();
            $user->email = $input['email'];
            $user->name = $input['name'];
            $user->password = \Hash::make($input['password']);
            $user->latitude = $request->has('latitude') ? $input['latitude'] : null;
            $user->longitude = $request->has('longitude') ? $input['longitude'] : null;
            $user->phone = $request->has('phone') ? $input['phone'] : null;
            $user->address = $request->has('address') ? $input['address'] : null;
            $user->birthday = $request->has('birthday') ? $input['birthday'] : null;

            if (!$user->save()) {
                return ApiResponse::errorInternal('An error occured. Please, try again.');
            }

            if ($request->hasFile('avatar')) {

                $avatarArray = $this->getAndCropImageAvatar($user, $request->file('avatar'), true);

                $user->avatar_image = $avatarArray['avatar_image'];
                $user->avatar_thumb = $avatarArray['avatar_thumb'];

                if (!$user->save()) {
                    return ApiResponse::errorInternal('An error occured. Please, try again.');
                }
            }
        } else {
            return ApiResponse::errorValidation($validator);
        }
        
        $returnObject = new \stdClass();
        $returnObject->user = $user;
        $returnObject->token = JWTAuth::fromUser($user);
        
        return ApiResponse::successResponse($returnObject);
    }

    // somewhere in your controller
    public function getAuthenticatedUser() {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

    protected function getAndCropImageAvatar($user, $imageFile, $removeOld) {
        $avatarArray = array();
        $uploadPath = '/uploads/';
        $avatarPath = '/uploads/avatars/';

        if (!is_dir(public_path() . $uploadPath)) {
            mkdir(public_path() . $uploadPath, 0777, true);
        }

        if (!is_dir(public_path() . $avatarPath)) {
            mkdir(public_path() . $avatarPath, 0777, true);
        }

        $userAvatarPath = $avatarPath . 'user_' . $user->id . '/';
        if (!is_dir(public_path() . $userAvatarPath)) {
            mkdir(public_path() . $userAvatarPath, 0777, true);
        }

        if ($removeOld) {
            // Remove old files                
            $files = glob(public_path() . $userAvatarPath . '*'); // get all file names                
            foreach ($files as $file) { // iterate files
                if (is_file($file))
                    unlink($file); // delete file
            }
        }

        $extension = $imageFile->getClientOriginalExtension();


        $imgName = 'image_' . uniqid() . '_' . time() . '.' . $extension;
        $imgThumbName = 'thumb_' . uniqid() . '_' . time() . '.' . $extension;

        $imgNamePublicPath = public_path() . $userAvatarPath . $imgName;
        $imgThumbNamePublicPath = public_path() . $userAvatarPath . $imgThumbName;

        $imageSize = getimagesize($imageFile);

        $imageFile->move(public_path() . $userAvatarPath, $imgName);

        if ($imageSize[0] > 1200 || $imageSize[1] > 1200) {
            try {
                $img = new SimpleImage($imgNamePublicPath);
                $img->best_fit(1200, 1200)->save($imgNamePublicPath);
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }


        // Save thumbnail image
        try {
            $img = new SimpleImage($imgNamePublicPath);
            $img->best_fit(300, 300)->save($imgThumbNamePublicPath);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

        $avatarArray['avatar_image'] = url('/') . $userAvatarPath . $imgName;
        $avatarArray['avatar_thumb'] = url('/') . $userAvatarPath . $imgThumbName;

        return $avatarArray;
    }

}
