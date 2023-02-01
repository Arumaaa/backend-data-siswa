<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    
    public function register(Request $request)
        {
        $validator = Validator::make($request->all(), [
            'nama_user' => 'required|string|min:4|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    
        $data =[
                    'nama_user' => $request->input('nama_user'),
                    'email' => $request->input('email'),
                    'password' => app('hash')->make($request->input('password')),
                    'level' => 'siswa',
                    'api_token' => '123404',
                    'status' => '1',
                    
                ];
                
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Register Gagal",
                'Data' => $validator->errors()
            ], 400);
        }
        $user = User::create($data);

            return response()->json([
                'success' => true,
                'message' => "Register Berhasil",
                'data' => $user
            ]);
        }
        
    public function login(Request $request)
        {
            $email = $request->input('email');
            $password = $request->input('password');


            $user = User::where('email',$email)->first();
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required | min:6',
            ]);
            
             if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid Email or Password",
                    'Data' => $validator->errors()
                ], 400);
            }
            
            if (Hash::check($password, $user->password)) { // check hash password
                // time to live token
                $exp_time = time() + (60 * 60); // token will expire in 1 hour
            
                $payload = [
                    'sub' => $user->id,
                    'iat' => time(),
                    'exp' => time() + (60*60)
                    
                ];
                
                
                $secret = env('JWT_SECRET');
                $algorithm = 'HS256';
                $token = JWT::encode($payload, $secret, $algorithm);
                $user->update([
                    'api_token' => $token
                ]);
                $result = $user;
                unset($result['api_token']);
                return response()->json([
                'success' => true,
                'pesan' => 'login berhasil',
                'exp_time' => date('Y-m-d H:i:s', $exp_time),
                'data' => $result,
                'api_token'=>$token,
            ]);
            }else {
                return response()->json([
                    'success' => false,
                    'pesan' => 'login gagal',
                    'data' => ''
                ]);
                
            }      
              
        }



    public function logout(Request $request)
        {
            //validasi token
            $validator = Validator::make($request->all(), [
                'api_token' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'pesan' => 'Token tidak valid',
                    'data' => ''
                ], 400);
            }

            
            //ambil user dengan token yang sesuai
            $user = User::where('api_token', $request->api_token)->first();

            //jika user tidak ditemukan
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'pesan' => 'User tidak ditemukan',
                    'data' => ''
                ], 400);
            }
            // $user->update(['api_token' => '']);
            $exp_time = time() + (60 * 60); // token will expire in 1 hour
            $payload = [
                'sub' => '1234567890',
                'iat' => time(),
                'exp' => $exp_time
            ];
            $secret = env('JWT_SECRET');
            $algorithm = 'HS256';
            $new_token = JWT::encode($payload, $secret, $algorithm);
            $user_name = $user->nama_user;
            //update user dengan api_token terupdate
            $user->update([
            'api_token' => ''
            ]);

            return response()->json([
                'success' => true,
                'pesan' => 'Logout berhasil',
                'data' => "Data dengan Nama_user = $user_name berhasil logout"
            ]);
        }

        public function changepw (Request $request)
        {
            $token= request()->bearerToken();
            $credential = JWT::decode($token, new Key(env('JWT_SECRET'),'HS256'));
            $id=$credential->sub ;
            $bebas = DB::select("select password from users where id=$id");
            $bebas = $bebas[0]->password;
    
            $currentpw = $_POST['currentpw'];
          
            
            if (Hash::check($currentpw, $bebas)) { 
                dd("berhasil");
                return response()->json([
                    'success' => true,
                    'pesan' => 'valid',
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'pesan' => 'tidak valid',
                ], 400);
            }
    }
}
