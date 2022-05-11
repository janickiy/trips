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
use Hash;
use Log;
use Carbon\Carbon;
use App\User;
use App\UserSocialAccount;
use App\Http\Controllers\Api\AppleController;


use Laravel\Socialite\Facades\Socialite;

/**
 *  Инструкция по установке:
 *  https://hivokas.com/api-authentication-via-social-networks-for-your-laravel-application
 */ 
class AuthController extends Controller
{
    public $debug = true;
    public $user = null;
    public $socialUser = null;
    public $providers = [
        'github',
        'facebook',
        'apple',
        'google',
    ];
    
    public function __construct()
    {
        $this->debug = false;
    }

    
    /**
     *  Запрос на веб-авторизацию через провайдера:
     *  http://trips.com.yy/login/github
     */ 
    public function redirectToProvider($provider)
    {
        if(in_array($provider, $this->providers)) {
            return \Socialite::driver($provider)->redirect();
        } else {
            return response()->json(['Unknown service provider. (1)'], 404);
        }
    }
    
    /**
     *  Обработка токена провайдера:
     */ 
    public function handleProviderCallback($provider)
    {
        if(in_array($provider, $this->providers)) {
        
            $this->socialUser = \Socialite::driver($provider)->stateless()->user();
        
            if($this->socialUser != null) {
                
                // Вход или регистрация пользователя через провайдера:
                if($provider == 'github') {
                    $this->GitHubAuth();
                }
                
                // Возвращаю пользователя вместе bearer:
                if($this->user != null) {
                    return response()->json([
                        'status' => 200, 
                        'data' => $this->user
                    ]);
                }

                return response()->json([
                    'status' => 500, 
                    'message' => 'Error while registering via social network.'
                ], 500); 
                
            } else {
                // Если пользователь не распознан, запрещаю доступ:
                return response()->json(['Access denied.'], 403);
            }

        } else {
            // Неизвестный сервис провайдер:
            return response()->json(['Unknown service provider. (2)'], 404);
        }
    }
    
    /**
     *  Вход/регистрация через github:
     */ 
    public function GitHubAuth()
    {
        if($this->debug == true) {
            
            Log::info(
                'Пользователь id: ' . $this->socialUser->id . ', nickname: ' . $this->socialUser->nickname . ', name: ' . $this->socialUser->name . ' ', 
                []
            );
        }
        
        $providerUser = Socialite::driver('github')->userFromToken($this->socialUser->token);
        $this->user = null;

    }
    
    // 'fe2ea894f5377fc421254fbfbd1f5bc6f1c88c8b'
    
    /**
     *  Вход через провайдера:
     */ 
    /**
     *  @OA\Post(
     *      path="/api/login/{provider}",
     *      summary="Метод для  входа/регистрации с помощью соц. сетей.",
     *      description="Авторизация произойдет автоматически. Успешный запрос возвращает bearer-токен.",
     *      operationId="social_register",
     *      tags={"Users"}, 
     *      security={},
     *      @OA\Parameter(
     *          description="token",
     *          in="query",
     *          name="token",
     *          required=true,
     *          example="ya29.A0AfH6SMDvtWet4WFGrkmC03E6R5y1zUNOA4rGY0oc61pHz4Mbm7ZoSHAG7V6vYRTH2VrxOSqleGSuiINu7H....",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *          description="provider: facebook, google, apple",
     *          in="query",
     *          name="provider",
     *          required=true,
     *          example="facebook",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешный вход/регистрация в приложении.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=200,
     *              ),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/UserSchema"),

     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Если бекенд не смог получит информацию о пользователе от провайдера, то доступ будет запрещен.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=403,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Access denied."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ошибка возникает в случае, если указан неверный провайдер, или по какой-то причине провайдер не предоставил e-mail пользователя.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=404,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Not found."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Произошла ошибка на стороне сервера. Подробности сохраняются в логах сервера, а в поле message будет текст ошибки.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=500,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Error message."
     *              ),
     *          )
     *      ),
     *  )
     */
    public function providerLogin($provider, Request $request)
    {
        if($this->debug == true) {
            Log::info($provider . ': request token:', [$request->token]);
        }
        
        // Если провайдер указан верно:
        if(in_array($provider, $this->providers)) {

            if($provider == "apple") {
                
                if(!$request->has('id_token')) {
                    return response()->json([
                        "status" => 400,
                        "message" => "Specify id_token."
                    ]);
                }
                
                //if($this->debug == true) {
                    Log::info('apple playload', $request->toArray());
                //}
                
                $tokenIsValid = AppleController::verify($request->id_token);
                
                if(!$tokenIsValid) {
                    return response()->json([
                        "status" => 400,
                        "message" => "Invalid id_token."
                    ]);
                }

                // email
                
                $providerUser = $this->decodeIdToken($request->id_token);
                
                //if($this->debug == true) {
                    Log::info('providerUser', [$providerUser]);
                //}
                
                $providerUser->id = $providerUser->sub;
                
                // TODO: а если емайл скрыт?
                if (property_exists($providerUser, 'email'))  {
                    if($providerUser->email != null) {
                        $providerUser->name = explode(",", $providerUser->email)[0];
                    }
                } else {
                    $providerUser->email = null;
                }

            } else {
                if(!$request->has('token')) {
                    return response()->json([
                        "status" => 400,
                        "message" => "Specify token."
                    ]);
                }
                
                $providerUser = Socialite::driver($provider)->userFromToken($request->token);
            }
        
            
            
            // Если пользователь авторизован:
            if($providerUser != null) {
                
                // Если емайл в соц. сети не указан:
                if($providerUser->email == null) {
                    
                    return response()->json(['Email not found.'], 404);
                    
                } else {
                    // Если емайл указан, проверяю зарегистрирован ли такой email в нашей базе данных:
                    $user = User::where('email', $providerUser->email)->first();

                    // Если юзер уже зареган:
                    if($user != null) {
                       
                        // Выдаю токен:
                        $user->bearer = $user->createToken('authToken')->accessToken;
                        
                        // Проверяю соц. сеть:
                        $userSocialAccount = UserSocialAccount::where('user_id', $user->id)->where('provider', $provider)->first();
                        
                        // Если еще нет соц сети, то добавляю:
                        if($userSocialAccount == null) {
                            $userSocialAccount = new UserSocialAccount;
                            $userSocialAccount->user_id = $user->id;
                            $userSocialAccount->provider = $provider;
                            $userSocialAccount->social_user_id = $providerUser->id;
                            $userSocialAccount->save();
                        }
                        
                    } else {
                        
                        // Проверяю привязан ли этот соц. аккаунт к другому емайлу:
                        $userSocialAccount = UserSocialAccount::where('social_user_id', $providerUser->id)->where('provider', $provider)->first();
                        
                        // Если такой соц. аккаунт еще не зарегистрирован:
                        if($userSocialAccount == null) {
                            // Регистририрую:
                            try {
                                
                                $user = new User();
                                
                                $user->last_name = $providerUser->name;
                                $user->email = $providerUser->email;
                                $user->password = bcrypt($providerUser->email);
                                $user->save();
                                
                                $user->username = $user->id;
                                $user->save();
                                
                                $user->bearer = $user->createToken('authToken')->accessToken;
                                
                                $userSocialAccount = new UserSocialAccount;
                                $userSocialAccount->user_id = $user->id;
                                $userSocialAccount->provider = $provider;
                                $userSocialAccount->social_user_id = $providerUser->id;
                                $userSocialAccount->save();
                                
                            } catch (\Throwable $th) {
                                return response()->json(['status' => 500, 'message' => $th->getMessage()]);
                            }
                        
                        } else {
                            // Меняю емайл на новый:
                            // Проверяю существует ли пользователь:
                            $user = User::where('id', $userSocialAccount->user_id)->first();
                            
                            // Если существует:
                            if($user != null) {
                                // Меняю ему емайл:
                                // $user->email = $providerUser->email;
                                // $user->save();
                                
                                // Выдаю токен:
                                $user->bearer = $user->createToken('authToken')->accessToken;
                                
                            } else {
                                return response()->json(['User not found.'], 404);
                            }

                        }

                    }
                    
                }
                
                // return response()->json(['status' => 200, 'data' => $user, 'providerUser' => $providerUser]);
                return response()->json(['status' => 200, 'data' => $user]);
            }
            
            // Если пользователь не распознан, запрещаю доступ:
            return response()->json(['Access denied.'], 403);
        }
        
        // Неизвестный сервис провайдер:
        return response()->json(['Unknown service provider. (api provider login)'], 404);

    }
    
    
    /**
     *  Декодирование токена эппла:
     */
    public function decodeIdToken($string)
    {
        // TODO: проверки
        $claims = explode('.', $string)[1];
        $claims = json_decode(base64_decode($claims));

        return $claims;
    }
    
    /**
     *  @OA\Schema(
     *      schema="UserSchema",
     *              @OA\Property(
     *                  property="id", type="int32", example=1,
     *              ),
     *              @OA\Property(
     *                  property="first_name", type="string", example="Artemy"
     *              ),
     *              @OA\Property(
     *                  property="last_name", type="string", example="Lebedev"
     *              ),
     *              @OA\Property(
     *                  property="username", type="string", example="art.lebedev"
     *              ),
     *              @OA\Property(
     *                  property="email", type="string", example="user@email.com"
     *              ),
     *              @OA\Property(
     *                  property="updated_at", type="int", example="1624778962", description="Дата последнего обновления."
     *              ),
     *              @OA\Property(
     *                  property="deleted", type="int", example="0"
     *              ),
     *      @OA\Property(
     *                  property="bearer", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNGMwN2Y1NWJiODQyNDE4ZWYyZGYxMTcxZGQzNmQyYTFkM2MwZjIwZDk2YTQxYWY0OTM3NDUxOTliMzFhYmI1OGY3ZGU4NjZjMmExOGEzZjYiLCJpYXQiOjE2MDQwNzg4ODgsIm5iZiI6MTYwNDA3ODg4OCwiZXhwIjoxNjM1NjExMjg4LCJzdWIiOiIyNCIsInNjb3BlcyI6W119.MjtpUiCmgowNZzIlGrtqzy9wyZ2BtX3e18MO3SLEN30tfwsLCoUyJGA1WgX5Q0Vn0BP1S_f27d1YriMgpIME6YrALEIonfaw22UHx7aDGUyK2z4CcKMspYdBYhDjIWEy6o_k_EozBjcbw2OXz92NTYVE05_E7VyYPFbZNL1xk6fyA0umkCDYz_h08qe6Y4zjGskBZThgq-GxRD5vcc4QP-3YK9y6dcZ6wE5T00uyULEu1dFYcQ0H77wFr1jNp91ByVc8IMhvYk_bIhK9BDJ5tPTvdwZec0DYCa57ZVDLfoKpu-F1mlD5VpVhN4jh7uenfhiiyHNUMIsL2fcF6zNLlZzcsYKh8WToPQ2v2eIPx9sL1KLa0MvcQ2nglDg61jRv2lN-mGoy3S5FLio6N57-ZONUUhi1Oxes4EOkV8dqKkWEBqKmcH3LQivHAdkZnRCJo-rKvR_1wDHqwXyDV8DOi8eMmpEsotxsfn5RTqyuqmm2RtYlhFTpwy7_2SgmtFNTt9QbcagjDDMxqcW_PdlVy4qohL7BKotHMub09htvFvMdbsmhW31dDr0X-sVKIQE_SIzyhnPllpSyQ-DtpHvScqYoOqDg25XrlID5uyT_VRbescFn3o0swIaWsNWqDrsScgAP4iQgykBt3W8Ylx560I8qll7y09cpSvHTJELpDh8"
     *              ),
     *      
     *  )
     */
    
    
    /**
     *  @OA\Schema(
     *      schema="UserNameSchema",
     *         @OA\Property(
     *              property="first_name", type="string", example="Artemy"
     *         ),
     *         @OA\Property(
     *              property="last_name", type="string", example="Lebedev"
     *         ),
     *         @OA\Property(
     *              property="username", type="string", example="art.lebedev"
     *         ),
     *  )
     */
}