<?php 

namespace App\Http\Controllers\Parser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Log;
use Carbon\Carbon;

class ParserController extends Controller
{

    public $data = [];

    public function __construct()
    {
    }


}
