<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // 'password' => $this->passwordRules()
        ]);

        if($validator->fails()){
            return $this->error('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        // $user = User::create($input);
        $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'address' => $request->address,
                        'houseNumber' => $request->houseNumber,
                        'phoneNumber' => $request->phoneNumber,
                        'city' => $request->city,
                        'password' => Hash::make($request->password),
                 ]);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
        $success['email'] =  $user->email;

        return $this->success($success, 'User register successfully.');
    }
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(),[
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8'
    //     ]);

    //     if($validator->fails()){
    //         return response()->json($validator->errors());
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password)
    //      ]);

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return $this->sendResponse($token,  $user, 'User register successfully.');
    // }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    // public function login(Request $request)
    // {
    //     if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
    //         $user = Auth::user();
    //         $success['name'] =  $user->name;
    //         $success['email'] =  $user->email;
    //         $success['token'] =  $user->createToken('MyApp')->plainTextToken;

    //         return $this->sendResponse($success, 'User login successfully.');
    //     }
    //     else{
    //         return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
    //     }
    // }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return  $this->error([
                    'message' => 'Unauthorized'
                ],'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();
            if ( ! Hash::check($request->password, $user->password, [])) {
                throw new Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return  $this->success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'Authenticated');
        } catch (Exception $error) {
            return  $this->error([
                'message' => 'Something went wrong',
                'error' => $error,
            ],'Authentication Failed', 500);
        }
    }
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return $this->success($token,'Token Revoked');
    }

    // public function updatePhoto(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|image|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->error(['error'=>$validator->errors()], 'Update Photo Fails', 401);
    //     }

    //     if ($request->file('file')) {

    //         $file = $request->file->store('assets/user', 'public');

    //         //store your file into database
    //         $user = Auth::user();
    //         $user->profile_photo_path = $file;
    //         $user->update();

    //         return $this->success([$file],'File successfully uploaded');
    //     }
    // }
}
