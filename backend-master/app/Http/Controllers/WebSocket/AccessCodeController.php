<?php 

namespace App\Http\Controllers\WebSocket;

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
use App\User;
use App\UserSocialAccount;
use Carbon\Carbon;

use Laravel\Socialite\Facades\Socialite;

class AccessCodeController extends Controller
{
    private $codeMaxLifeTime = 30;
    
    
    public function __construct()
    {

    }
    
    /**
     *  @OA\GET(
     *      path="/api/get_wss_link",
     *      summary="Возвращает ссылку для подключения по WSS. Ссылка действует в течении 30 секунд, и уничтожается сразу после успешного входа.",
     *      description="Требуется авторизация при помощи заголовка bearer.",
     *      operationId="get_wss_link",
     *      tags={"WSS"}, 
     *      security={ {"bearerAuth": {}} },
     *      @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=200,
     *              ),
     *              @OA\Property(
     *                  property="url", type="string", example="wss://data.dev.trips.im?code=abcdefgh",
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неверный авторизационный токен",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=401,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Неверный авторизационный токен."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Доступ запрещен. Аккаунт удален.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status", type="int32", example=403,
     *              ),
     *              @OA\Property(
     *                  property="message", type="string", example="Account deleted."
     *              )
     *          )
     *      ),
     *  ),
     *  @OAS\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer"
     *  ),
     */
    public function getWssLink(Request $request)
    {
        if (Auth::check())
        {
            $uuid = Str::uuid();
            
            $user = User::where('id', Auth::user()->id)->first();
            
            if($user == null) {
                return response()->json([
                    'status' => 401,            
                ], 401);
            }
            
            if($user->deleted == 1) {
                return response()->json([
                    "status" => 403,    
                    "message" => "Account deleted."                    
                ], 403);
            }

            DB::table('wss_codes')->insert([
                'code_id' => $uuid,
                'created_by_user_id' => Auth::user()->id,
                'created_at' => Carbon::now()
            ]);
            
            $this->destroyExpiredWssCodes();

            return response()->json([
                'status' => 200,
                'url' => config('websockets.endpoint') . '?code=' . $uuid,            
                'len' => strlen($uuid),            
            ], 200);
        }
        
        return response()->json([
            'status' => 401,            
        ], 401);

    }
    
    public function checkWssCode(Request $request)
    {

        $code = $this->getUserByCode($request->code);
        
        if($code != null) {
            
            $left = $this->codeMaxLifeTime - Carbon::parse($code->created_at)->diffInSeconds(Carbon::now());
            
            return response()->json([
                'status' => 200,
                'code' => $code,            
                'active_time' => $left > 0 ? $left : 0,            
            ], 200);
        }
        
        return response()->json([
            'status' => 404,
            'code' => null,                      
        ], 404);
    }
    
    public function getUserByCode($code)
    {
        $this->destroyExpiredWssCodes();
        
        try { 
            $code = DB::table('wss_codes')->where('code_id', $code)->first();

        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [$e->getMessage()]);
            return $e;
            
        }
        
        return $code;
    }
    
    /**
     *  Проверить действителен ли код:
     */
    public function isActive($code)
    {
        $code = DB::table('wss_codes')->where('code_id', $code)->first();
        
        if($code != null) {
            return false;
        } 
        
        return true;
    }

    /**
     *  Уничтожить указанный код:
     */
    public function destroyCode($code)
    {
        try { 
            DB::table('wss_codes')->where('code_id', $code)->delete();

        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [$e->getMessage()]);
            return $e;
            
        }
    }
    
    /**
     *  Уничтожить все коды у которых вышел срок действия:
     */
    public function destroyExpiredWssCodes()
    { 
        try { 
            DB::table('wss_codes')->where('created_at', '<', Carbon::now()->subSeconds($this->codeMaxLifeTime))->delete();

        } catch(\Exception $e) { 
            Log::error('Произошла ошибка.', [$e->getMessage()]);
            return $e;
            
        }
    }
    
}