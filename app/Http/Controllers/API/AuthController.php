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
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        // $user = User::create($input);
        $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                 ]);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
        $success['email'] =  $user->email;

        return $this->sendResponse($success, 'User register successfully.');
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
                throw new \Exception('Invalid Credentials');
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
    // public function login(Request $request)
    // {
    //     if (!Auth::attempt($request->only('email', 'password')))
    //     {
    //         return response()
    //             ->json(['message' => 'Unauthorized'], 401);
    //     }

    //     $user = User::where('email', $request['email'])->firstOrFail();

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()
    //         ->json(['message' => 'Hi '.$user->name.', welcome to home','access_token' => $token, 'token_type' => 'Bearer', ]);
    // }
    public function logout(Request $request)
    {
        // auth()->user()->tokens()->delete();

        // return [
        //     'message' => 'You have successfully logged out and the token was successfully deleted'
        // ];
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
