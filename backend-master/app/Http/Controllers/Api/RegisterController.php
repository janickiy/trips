<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;
use File;
use Auth;
use Hash;
use Mail;
use DB;
use URL;
use Log;

use Carbon\Carbon;
use App\User;


class RegisterController extends Controller
{
    
    private $passwordLifeTime = 10; // в минутах
    private $allowedLocales = ['ru', 'en'];
    
    public function __construct()
    {
        
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191',
            'password' => 'required|max:191',
            'password_confirmation' => 'required|same:password|max:191',
        ]);

        if ($validator->fails()) {
            
            return response()->json([
                'status' => 422, 
                'message' => 'Wrong incoming data.'
            ], 422);
            
        }

        if(User::where('email', $request->email)->first()) {
            
            return response()->json([
                'status' => 422, 
                'message' => 'User with this e-mail is already registered.'
            ], 422);
            
        }
        
        try {
            
            $user = new User();

            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            
            $user->username = strval($user->id);
            $user->save();
            
            $user->bearer = $user->createToken('authToken')->accessToken;
            
            
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()]);
        }

        return response()->json(['status' => 200, 'data' => $user]); 
    }
    
    
    /**
     *  @OA\Post(
     *      path="/api/send_auth_email",
     *      summary="Метод для получения кода для входа в приложение (автоматическая регистрация).",
     *      description="В зависимости от того, существует ли указанный e-mail в нашей базе данных: 
* Если указанный e-mail не зарегистрирован в нашей базе данных, то произойдет автоматическая регистрация и код отправится на указанный емайл.
* А если e-mail уже зарегистрирован, то сразу будет отправлен код. 

Чтобы авторизоваться в системе, нужно в течение 10 минут передать код методом POST: /api/login",
     *      operationId="send_auth_email",
     *      tags={"Users"}, 
     *      security={},
     *      @OA\Parameter(
     *          description="e-mail",
     *          in="query",
     *          name="email",
     *          required=true,
     *          example="user@mail.com",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          description="Locale: ru, en",
     *          in="query",
     *          name="locale",
     *          required=true,
     *          example="ru",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Найдено.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=200,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Your confirmation code has been sent to email."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Не указан емайл, либо указан не верный.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=400,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Wrong email."
     *              )
     *          )
     *      ),
     *  )
     */
     // 
    public function sendAuthEmail(Request $request)
    {
        $random = Str::upper(Str::random(6));
        
        if(!$request->has('email')) {
            return response()->json([
                'status' => 400,
                'message' => 'Specify email.',
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191',
        ]);

        if ($validator->fails()) {
            
            return response()->json([
                'status' => 400, 
                'message' => 'Wrong email.'
            ], 400);
            
        }
        
        
        // Существует ли пользователь с указанный емайлом:
        $user = User::where('email', $request->email)->first();
        
        // Если нет:
        if($user == null) {
        
            // Регистрирую:
            $user = new User();
            $user->email = $request->email;
            $user->password = bcrypt($random);
            $user->save();
            
            $user->username = $user->id;
            $user->save();

        }

        // Устанавливаю временный пароль и время действия пароля:
        $user->password = bcrypt($random);
        $user->password_expired_at = Carbon::now('UTC')->addSeconds($this->passwordLifeTime * 60);
        $user->save();
        
        
        
        $to_name = $request->email;
        $to_email = $request->email;
        
        $data = array(
            'name'=> explode('@', $request->email)[0], 
            'code' => $random
        );
              
        if($request->has('locale')) {
            if(in_array($request->locale, $this->allowedLocales)) {
                $template = 'mail_' . $request->locale;
            } else {
                $template = 'mail_en';
            }
        } else {
            $template = 'mail_en';
        }
        
        Mail::send('emails.' . $template, $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('Trips');
            $message->from(env('MAIL_USERNAME', 'help@trips.im'), 'Trips');
        });
                
        
        return response()->json([
			"status" => 200,
			"message" => "Your confirmation code has been sent to email."
		]);
    }
    
    
    /**
     *  @OA\Post(
     *      path="/api/login",
     *      summary="Метод для входа в приложение с помощью e-mail. Успешный запрос возвращает bearer-токен.",
     *      description="",
     *      operationId="ogin",
     *      tags={"Users"}, 
     *      security={},
     *      @OA\Parameter(
     *          description="e-mail",
     *          in="query",
     *          name="email",
     *          required=true,
     *          example="user@mail.com",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          description="Пароль",
     *          in="query",
     *          name="password",
     *          required=true,
     *          example="123456",
     *          @OA\Schema(
     *              type="string",
     *              format="password"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Найдено.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=200,
     *              ),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/UserSchema"),

     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Пользователь не найден.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=404,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="User with this e-mail was not found."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Истекло время жизни временного пароля.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=403,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Password expired."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Указан не верный пароль",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=422,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Invalid password."
     *              ),
     *          )
     *      ),
     *  )
     */
    public function login(Request $request)
    {   
        
        // Существует ли пользователь с таким емайлом:
        $user = User::where('email', $request->email)->first();

        if($user == null) {
            return response()->json([
                'status' => 404, 
                'message' => 'User with this e-mail was not found.'
            ], 404);
        }

        // Проверяю не истек ли срок действия пароля:
        if(!Carbon::parse($user->password_expired_at)->gt(Carbon::now('UTC'))) {
            return response()->json([
                'status' => 403, 
                'message' => 'Password expired.'
            ], 403);
        } 
        
        // Проверяю правильно ли указан пароль:
        if(!Hash::check($request['password'], $user->password)) {
            return response()->json([
                'status' => 422, 
                'message' => 'Invalid password'
            ], 422);
        } 
        
        $user->bearer = $user->createToken('authToken')->accessToken;

        return response()->json([
            'status' => 200, 
            'data' => $user
        ]);

    }


}