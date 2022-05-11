<?php 

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Mail;
use Hash;
use Log;
use Carbon\Carbon;
use App\User;
use App\UserSocialAccount;
use App\Rules\TikTokUsername;


use App\Jobs\DeleteAccount;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    private $delAccountCodeMaxLifeTime = 300;
    private $allowedLocales = ['ru', 'en'];
    
    public function __construct()
    {
        // $this->middleware('checkBan');
    }
    
    public function list(Request $request)
    {

        $users = User::with('socialAccounts')->orderBy('id', 'desc')->get();
        
        return response()->json($users);
    }
    
    /**
     *  @OA\Get(
     *      path="/api/user",
     *      summary="Метод возвращает информацию о текущем пользователе.",
     *      description="Требуется авторизация при помощи заголовка bearer.",
     *      operationId="user",
     *      tags={"Users"}, 
     *      security={ {"bearerAuth": {}} },
     *      @OA\Response(
     *          response=200,
     *          description="Найдено.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="id", type="int32", example=1,
     *              ),
     *              @OA\Property(
     *                  property="email", type="string", example="user@example.com",
     *              ),
     *              @OA\Property(
     *                  property="first_name", type="string", example="Artemii",
     *              ),
     *              @OA\Property(
     *                  property="last_name", type="string", example="Lemedev",
     *              ),
     *              @OA\Property(
     *                  property="username", type="string", example="art.lebedev",
     *              ),
     *              @OA\Property(
     *                  property="password_expired_at", type="int", example="1624769258",
     *              ),
     *              @OA\Property(
     *                  property="updated_at", type="int", example="1624778962",
     *              ),
     *              @OA\Property(
     *                  property="deleted", type="int", example="0",
     *              ),
     *          )
     *      ),
     *  ),
     *  @OAS\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer"
     *  ),
     */     
    public function read(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        
        if($user != null) {
            return response()->json($user);  
        }

        return response()->json([], 404);
    }
    
    /**
     *  @OA\Post(
     *      path="/api/logout",
     *      summary="Метод отключает токен авторизации пользователя.",
     *      description="Требуется авторизация при помощи заголовка bearer.",
     *      operationId="logout",
     *      tags={"Users"}, 
     *      security={ {"bearerAuth": {}} },
     *      @OA\Response(
     *          response=200,
     *          description="Сервер не возвращает в ответ никаких данных, просто 200 ОК.",
     *      ),
     *  ),
     *  @OAS\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer"
     *  ),
     */
    public function logout(Request $request)
    {
        $accessToken = Auth::user()->token();
        
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();
        return response()->json(null, 200);
    }
    

    public function changePassword(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        
        if($user != null) {
            
            // Проверка пароля:
            if(!Hash::check($request['password'], $user->password)) {
                
                return response()->json([
                    'status' => 422, 
                    'message' => 'Указан не верный пароль'
                ], 422);
                
            } 
            

            if($request['new_password'] != $request['new_password_confirmation']) {
                return response()->json([
                    'status' => 422, 
                    'message' => 'Пароли не совпадают.'
                ], 422);
            }
            
            
            $user->password = bcrypt($request->new_password);
            $user->save();
            
            return response()->json($user, 200);
            
        }
        
        return response()->json([
                    'status' => 404, 
                    'message' => 'Пользователь не найден.'
                ], 404);
    }
    
    /**
     *  @OA\Post(
     *      path="/api/user_name",
     *      summary="Метод для обновления полей first_name, last_name, username. Разрешено передавать любой набор из этих 3 полей.",
     *      description="Требуется авторизация при помощи заголовка bearer. 
Правила валидации полей:
- First и Last name могут содержать любые символы.
- Username должен быть уникальным, может содержать только латинские буквы lowercase и цифры, а так же символ подчеркивания и точку. Должен начинаться с буквы, и не может заканчиваться точкой или подчеркиванием. 
Пример: vlad.salabun_php_programmer
- Максимальная длина по дефолту 191 символ.
- Не может принимать null
- Пробелы или спецсимволы не принимает.
- Передавать можно в любой комбинации. Что передано - то и будет изменено. Успешный запрос всегда возвращает все три имени.
     ",
     *      operationId="update_user_name",
     *      tags={"Users"}, 
     *      security={ {"bearerAuth": {}} },
     *      @OA\Parameter(
     *          description="Имя",
     *          in="query",
     *          name="first_name",
     *          example="Artemy",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *          description="Фамилия",
     *          in="query",
     *          name="last_name",
     *          example="Lebedev",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *          description="Никнейм",
     *          in="query",
     *          name="username",
     *          example="art.lebedev",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Результат обработки запроса.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=200,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="User data updated.",
     *              ),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/UserNameSchema"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Указаны неверные данные.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=400,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Wrong username."
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Ошибка на стороне сервера.",
     *      ),
     *  ),
     *  @OAS\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer"
     *  ),
     */
    public function changeUserName(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        
        if($user == null) {
            return response()->json([
                'status' => 404, 
                'message' => 'User not found.'
            ], 404);
        }
        
        // Валидация входящих данных:
        if($request->has('first_name')) {
            
            $validateFirstName = Validator::make($request->all(), [
                'first_name' => 'max:191',
            ]);

            if ($validateFirstName->fails()) {
                return response()->json([
                    'status' => 400, 
                    'message' => 'Wrong first name.'
                ], 400);
            }
        }
        
        if($request->has('last_name')) {
            
            $validateLastName = Validator::make($request->all(), [
                'last_name' => 'max:191',
            ]);

            if ($validateLastName->fails()) {
                return response()->json([
                    'status' => 400, 
                    'message' => 'Wrong last name.'
                ], 400);
            } 
        }
        
        if($request->has('username')) {
            $validateUserName = Validator::make($request->all(), [
                'username' => ['max:191', new TikTokUsername],
            ]);

            if ($validateUserName->fails()) {
                return response()->json([
                    'status' => 400, 
                    'message' => 'Wrong username.'
                ], 400);
            }
            
            $obj = User::where('username', $request->username)->first();                        
            
            if($obj != null) {
                return response()->json([
                    'status' => 400, 
                    'message' => 'Username already exists.'
                ], 400);
            }
        }
        
        // Обработка запроса:
        if($request->has('first_name')) {
            $user->first_name = $request->first_name;
            $user->save();
        }
        
        if($request->has('last_name')) {
            $user->last_name = $request->last_name;
            $user->save();
        }
        
        if($request->has('username')) {
            $user->username = $request->username;
            $user->save();
        }
        
        return response()->json([
            'status' => 200, 
            'message' => 'User data updated.',
            'data' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'username' => $user->username,
            ]
        ], 200);
    }
    
    /**
     *  Этот метод сохраняет в поле username значение id для всех пользователей у которых username == null
     */
    public static function resetNullableUsernames()
    {
        // TODO:
    }
    
    /**
     *  Этот метод сохраняет в поле username значение id для указанного пользователя
     */
    public static function setDefaultUsername($userId)
    {
        // TODO:
    }
    
    
    /**
     *  @OA\GET(
     *      path="/api/delete_account",
     *      summary="Запрос кода для удаления аккаунта.",
     *      description="Требуется авторизация при помощи заголовка bearer.",
     *      operationId="delete_account",
     *      tags={"Users"}, 
     *      security={ {"bearerAuth": {}} },
     *      @OA\Parameter(
     *          description="Locale: ru, en",
     *          in="query",
     *          name="locale",
     *          required=true,
     *          example="ru",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Результат обработки запроса.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=200,
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неверный авторизационный токен.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=401,
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Ошибка на стороне сервера.",
     *      ),
     *  ),
     *  @OAS\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer"
     *  ),
     */
    public function createDeleteAccountCode(Request $request)
    {
        if (!Auth::check())
        {
            return response()->json([
                'status' => 401,            
            ], 401);     
        }

        $user = User::where('id', Auth::user()->id)->first();
        
        if($user == null) {
            return response()->json([
                'status' => 404,            
            ], 404); 
        }
        
        $uuid = Str::uuid();
        
        DB::table('delete_account_codes')->insert([
            'code_id' => $uuid,
            'created_by_user_id' => Auth::user()->id,
            'created_at' => Carbon::now()
        ]);
        
        // код на почту
        if($request->has('locale')) {
            if(in_array($request->locale, $this->allowedLocales)) {
                $template = 'delete_account_' . $request->locale;
            } else {
                $template = 'delete_account_en';
            }
        } else {
            $template = 'delete_account_en';
        }
        
        $data = array(
            'name'=> explode('@', $user->email)[0], 
            'code' => $uuid
        );
        
        $to_name = $user->email;
        $to_email = $user->email;
        
        Mail::send('emails.' . $template, $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('Trips');
            $message->from(env('MAIL_USERNAME', 'help@trips.im'), 'Trips');
        });

        return response()->json([
            'status' => 200,
            //'code' => $uuid,                        
        ], 200);
    }

    /**
     *  @OA\POST(
     *      path="/api/delete_account",
     *      summary="Отправка кода для подтверждения удаления аккаунта.",
     *      description="Требуется авторизация при помощи заголовка bearer.",
     *      operationId="confirm_delete_account",
     *      tags={"Users"}, 
     *      security={ {"bearerAuth": {}} },
     *      @OA\Parameter(
     *          description="code",
     *          in="query",
     *          name="code",
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Аккаунт удален.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=200,
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Не указан код или указан не верный.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=400,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Specify code.",
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неверный авторизационный токен.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=401,
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Ошибка на стороне сервера.",
     *      ),
     *  ),
     *  @OAS\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer"
     *  ),
     */
    public function deleteAccount(Request $request)
    {
        if (!Auth::check())
        {
            return response()->json([
                'status' => 401,            
            ], 401);     
        }
        
        if (!$request->has('code')) {
            return response()->json([
                'status' => 400, 
                'message' => "Specify code."                
            ], 400);     
        }
        
        
        $user = User::where('id', Auth::user()->id)->first();

        if($user == null) {
            return response()->json([
                'status' => 404,
                'message' => "User not found."
            ], 404);  
        }
        
        $this->destroyOldDeleteCodes();
        
        $record = DB::table('delete_account_codes')
            ->where('created_by_user_id', $user->id)
            ->where('code_id', $request->code)
            ->first();
            
        if($record == null) {
            return response()->json([
                'status' => 400,
                'message' => "Wrong code."
            ], 400);
        } 
        
        // Отложить задачу
        // DeleteAccount::dispatch($user->toArray());
        
        // Выполнить задачу:
        $deleteJob = new \App\Jobs\DeleteAccount($user->toArray());
        $deleteJob->handle();

        return response()->json([
            'status' => 200
        ]);

    }
    
    public function destroyOldDeleteCodes()
    {
        try { 
            DB::table('delete_account_codes')
                ->where('created_at', '<', Carbon::now()->subSeconds($this->delAccountCodeMaxLifeTime))
                ->delete();

        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [$e->getMessage()]);
            return $e;
            
        } 
    }
    
	
    public function getUserTokenForTesting($userID)
    {
		if(config('app.debug')) {

			$user = User::where('id', $userID)->first();
			
			if($user != null) {
				
				$user->bearer = $user->createToken('authToken')->accessToken;
				
				return response()->json([
					"status" => 200,
					"message" => "OK.",
					"data" => $user
				]);
			}
			
			return response()->json([
				"status" => 404,
				"message" => "User not found."
			], 404);
		}
		
		return response()->json([
			"status" => 403,
			"message" => "This method is not allowed in production server."
		], 403);
	}		
}