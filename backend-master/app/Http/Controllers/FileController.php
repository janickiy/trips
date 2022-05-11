<?php
 
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Response;
use Auth;
use DB;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class FileController extends Controller
{
    public function __construct()
    {
    }

    /**
     *  @OA\Post(
     *      path="/api/file_upload",
     *      summary="Загрузка файла на сервер.",
     *      description="Требуется авторизация при помощи заголовка bearer. В поле metadata протокола TUS нужно указать artifact_id.
* Смотрите документацию для фронтенда:
* [Описание протокола для дозагрузки файлов на сервер](https://tus.io/protocols/resumable-upload.html)
* [JS client](https://github.com/tus/tus-js-client)
* [Java client](https://github.com/tus/tus-java-client)
* [Swift client](https://github.com/tus/TUSKit)
",
     *      operationId="file_upload",
     *      tags={"Files"}, 
     *      security={ {"bearerAuth": {}} }, 

     *      @OA\Response(
     *          response=200,
     *          description="Успешный запрос отдаст запрошенный кусочек файла.",
     *          @OA\Property(type="string", format="binary"),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неверный авторизационный токен",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status_code", type="int32", example=401,
     *              ),
     *              @OA\Property(
     *                  property="status_message", type="string", example="Неверный авторизационный токен."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Доступ запрещен.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status_code", type="int32", example=403,
     *              ),
     *              @OA\Property(
     *                  property="status_message", type="string", example="Доступ запрещен."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Такого файла не существует.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status_code", type="int32", example=404,
     *              ),
     *              @OA\Property(
     *                  property="status_message", type="string", example="Такого файла не существует."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Произошла ошибка",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status_code", type="int32", example=500,
     *              ),
     *              @OA\Property(
     *                  property="status_message", type="string", example="Произошла ошибка."
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
    
    
    /**
     *  @OA\GET(
     *      path="/api/file_download",
     *      summary="Скачивание файла с сервера.",
     *      description="Требуется авторизация при помощи заголовка bearer. В заголовках нужно указать с какого по какой байт передать файл. Пример: Range: bytes=200-1000",
     *      operationId="file_download",
     *      tags={"Files"}, 
     *      security={ {"bearerAuth": {}} },
     *      @OA\Parameter(
     *          name="artifact_id",
     *          description="Идентификатор артефакта",
     *          in="query",
     *          example=1,
     *      ), 
     *      @OA\Response(
     *          response=200,
     *          description="Успешный запрос отдаст запрошенный кусочек файла.",
     *          @OA\Property(type="string", format="binary"),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неверный авторизационный токен",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status_code", type="int32", example=401,
     *              ),
     *              @OA\Property(
     *                  property="status_message", type="string", example="Неверный авторизационный токен."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Доступ запрещен.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status_code", type="int32", example=403,
     *              ),
     *              @OA\Property(
     *                  property="status_message", type="string", example="Доступ запрещен."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Такого файла не существует.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status_code", type="int32", example=404,
     *              ),
     *              @OA\Property(
     *                  property="status_message", type="string", example="Такого файла не существует."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Произошла ошибка",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status_code", type="int32", example=500,
     *              ),
     *              @OA\Property(
     *                  property="status_message", type="string", example="Произошла ошибка."
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
    public function download(Request $request) 
    {
        
        if(!$request->has('artifact_id')) {
            return response()->json([
                "status_code" => 400,
                "status_message" => "Specify artifact_id.",
            ], 400);
        }
        
        $fileInfo = DB::table('files_attributes')->where('artifact_id', $request->artifact_id)->first();

        if($fileInfo == null) {
            return response()->json([
                "status_code" => 404,
                "status_message" => "Artifact not found.",
            ], 404);
        }
        
        $user = Auth::user();
        
        if($user == null) {
            return response()->json([
                "status_code" => 401,
                "status_message" => "Unauthorized.",
            ], 401);
        }
        
        if($user->id != $fileInfo->created_by_user_id) {
            return response()->json([
                "status_code" => 403,
                "status_message" => "Forbidden.",
            ], 403);
        }

        if($fileInfo->upload_is_complete != 1) {
            return response()->json([
                "status_code" => 400,
                "status_message" => "File is not uploaded.",
            ], 400);
        }

        if($fileInfo->link == null) {
            return response()->json([
                "status_code" => 400,
                "status_message" => "File is not uploaded.",
            ], 400);
        }

        $currentPath = $fileInfo->created_by_user_id . '/' . $fileInfo->artifact_id . '/' . $fileInfo->link;
        
        // существует ли файл в облаке?
        if(!Storage::disk('s3')->exists($currentPath)) {
        
            return response()->json([
                "status_code" => 200,
            ], 200, $headers);
            
        }
        
        // Если файл в облаке существует:
        $info = Storage::disk('s3')->getMetadata($currentPath, ['size', 'mimetype']);
        
        
        // TODO: а если это запрос HEAD?
        if ($request->isMethod('HEAD')) {
            
            // Устанавливаю заголовки:
            $headers = array(
               'Content-Type: ' . $info['mimetype'],
               'Content-Length: '. $info['size'],
            );
            
                
            // Передаю клиенту:
            return  Response::stream(function() {
              echo '';
            }, 200, $headers);

        };
        

        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');
        } else {
            $range = "bytes=0-" . $info['size'];
        }
        
        $testRange = $this->checkRange($range, $info['size']);
        
        if($testRange) {
            $range = $testRange;
            
        } else {
            $range = "bytes=0-" . $info['size'];
        }

       
        // S3 connection 
        try {
            $s3 = S3Client::factory([
                'credentials' => [
                    'key'       => config('filesystems.disks.s3.key'),
                    'secret'    => config('filesystems.disks.s3.secret')
                ],
                'version'       => 'latest',
                'region'        => config('filesystems.disks.s3.region')
            ]);

            // to get the file information from S3
            $result = $s3->getObject(array(
              'Bucket' => config('filesystems.disks.s3.bucket'),
              'Key'    => $currentPath,
              'Range'  => $range,
            ));
            
            // Устанавливаю заголовки:
            $headers = array(
               'Content-Type: ' . $result['ContentType'],
               'Content-Length: '. $info['size'],
            );
            
            // Передаю клиенту:
            return  Response::stream(function() use($result) {
              echo $result['Body'];
            }, 200, $headers);



        } catch (Exception $e) {
            Log::info('Ошибка при скачивании файла с облака', [
                "user_id" => $user->id,
                "message" => $e->getMessage()
            ]);
            
        }

    }
    
    public function checkRange($string, $max)
    {
        if(strlen($string) < 6) {
            return false;
        }
        
        $string = substr($string, 6); 
        
        $parts = explode("-",$string);
        
        if(count($parts) != 2) {
            return false;
        }
        
        if
        (
            is_numeric($parts[0]) and
            is_numeric($parts[1]) and
            strlen($parts[0]) > 0 and
            strlen($parts[1]) > 0 and
            intval($parts[0]) < intval($parts[1])
        ) 
        {

            $from = intval($parts[0]);
            $to = 1;
            
            if(intval($parts[1]) >= $max) {
                $to = $max;
            } else {
                $to = intval($parts[1]);
            }
            
            return "bytes=". $from."-" . $to;
        }

        return false;
    }
    
    
    public function delete(Request $request) 
    {
        /*
        Log::info("5. Получил delete запрос", [
            $request->artifact_id, 
            $request->created_by_user_id, 
            $request->link,
            $request->upload_is_complete
        ]);
        */

        if ($request->isMethod('get')) {
            return response()->json([]);
        }
        
       // $currentPath = '5/83/XXXTentacion — Moonlight.mp3';
        
        if ($request->isMethod('post')) {
            
            if($request->upload_is_complete == 1) {
                
                $currentPath = $request->created_by_user_id . '/' . $request->artifact_id . '/' . $request->link;
                
                try {
                    $s3 = S3Client::factory([
                        'credentials' => [
                            'key'       => config('filesystems.disks.s3.key'),
                            'secret'    => config('filesystems.disks.s3.secret')
                        ],
                        'version'       => 'latest',
                        'region'        => config('filesystems.disks.s3.region')
                    ]);

                    $result = $s3->deleteObject(array(
                      'Bucket' => config('filesystems.disks.s3.bucket'),
                      'Key'    => $currentPath,
                    ));
                    
                } catch (Exception $e) {
                    Log::info('Ошибка при удалении файла с облака', [
                        "message" => $e->getMessage()
                    ]);
                    
                }
            }
        }

    }
    
    public function storageStatistics(Request $request) 
    { 
        $array = [];
        
        //$path = storage_path("public/uploads/users");
        $path = "public/uploads/users/";
        
        //$files1 = Storage::files($path);
        //$files2 = Storage::files($path);
        
        //$directories = Storage::allDirectories($path);
        $allFiles = Storage::allFiles($path);
        $maxSize = 0;
        
        foreach($allFiles as $file) {

            
            if (Storage::disk('local')->exists($file)) {
                $size = Storage::size($file);
                $array[] = [
                    'path' => $file,
                    'size' => $size
                ];
                $maxSize += $size;
            }
            
            
        }
        
        if(count($array) == 0) {
            return response()->json([
                "staus" => 200,
                "data" => [
                    "files_count" => 0,
                    "files_max_size" => 0,
                    "files_average_size" => 0,
                ],
            ]);  
        }
        
        return response()->json([
            "staus" => 200,
            "data" => [
                //"files" => $array,
                "files_count" => count($array),
                "files_max_size" => floor($maxSize / 1024 / 1024),
                "files_average_size" => floor(floor($maxSize / count($array)) / 1024 / 1024),
            ],
        ]);
    }    
 
}