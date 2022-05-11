<?php 

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use URL;
use Storage;
use File;
use Auth;
use Hash;
use Log;
use App\TripsModels\User;
use App\TripsModels\Artifact;
use App\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Rules\TikTokUsername;
use Carbon\Carbon;

use App\Statistics\Daily;

class DashboardController extends Controller
{
    private $statistics = [
            "status" => 200,
            "data" => [
                "user_registrations" => [
                    "all"       => [ "absolute" => 0, "percentage" => 0 ],
                    "email"     => [ "absolute" => 0, "percentage" => 0 ],
                    "apple"     => [ "absolute" => 0, "percentage" => 0 ],
                    "facebook"  => [ "absolute" => 0, "percentage" => 0 ],
                    "google"    => [ "absolute" => 0, "percentage" => 0 ],
                ],
                "artifacts" => [
                    "all"           => [ "absolute" => 0, "percentage" => 0, "average" => 0  ],
                    "trips"         => [ "absolute" => 0, "percentage" => 0, "average" => 0  ],
                    "towns"         => [ "absolute" => 0, "percentage" => 0, "average" => 0  ],
                    "files"         => [ "absolute" => 0, "percentage" => 0 ],
                    "note_photos"   => [ "absolute" => 0, "percentage" => 0 ],
                    
                ],
            ],
            "memory" => [ "average_memory_usage" => 0, "peak_memory_usage" => 0, ],
            "last_update" => null,
            "genetation_time" => 0
        ];
   
    private $daumau = [
            "status" => 200,
            "data" => [
                "dau" => [],
                "mau" => [],
                "average" => [
                    "dau" => 0,
                    "mau" => 0,
                ],
                "total_users" => [
                    "dau" => 0,
                    "mau" => 0,
                ],
            ]
    ];
   
    public function __construct()
    {
    }

    public function index()
    {
        $items = [];
        
        return view(
            'cp.Dashboard.index',
            compact('items')
        );
    }

    public function statistics()
    {
        // TODO: кешировать
        // TODO: изменить память
        $this->calcUserStatistics();
        $this->calcArtifactsStatistics();
        
        // МБ
        $this->statistics["memory"]["average_memory_usage"] = round(memory_get_usage() / 1024 / 1024, 2); 
        $this->statistics["memory"]["peak_memory_usage"] = round(memory_get_peak_usage() / 1024 / 1024);
        
        return response()->json($this->statistics);
    }
    
    public function calcUserStatistics()
    {
        User::chunk(100, function($items){
            foreach ($items as $item){
                
                $userSocialAccounts = $item->socialAccounts;
                
                if(count($userSocialAccounts) > 0) {
                    foreach($userSocialAccounts as $socialAccount) {

                        if($socialAccount->provider == "facebook") {
                            $this->statistics["data"]["user_registrations"]["facebook"]["absolute"] += 1;
                        }
                        
                        if($socialAccount->provider == "google") {
                            $this->statistics["data"]["user_registrations"]["google"]["absolute"] += 1;
                        }
                        
                        if($socialAccount->provider == "apple") {
                            $this->statistics["data"]["user_registrations"]["apple"]["absolute"] += 1;
                        }
                        
                    }
                } else {
                    $this->statistics["data"]["user_registrations"]["email"]["absolute"] += 1;
                }
                
                $this->statistics["data"]["user_registrations"]["all"]["absolute"] += 1;
            }
        });
        
        $this->statistics["data"]["user_registrations"]["facebook"]["percentage"] = 
            round(($this->statistics["data"]["user_registrations"]["facebook"]["absolute"] / $this->statistics["data"]["user_registrations"]["all"]["absolute"]) * 100, 2);
            
        $this->statistics["data"]["user_registrations"]["google"]["percentage"] = 
            round(($this->statistics["data"]["user_registrations"]["google"]["absolute"] / $this->statistics["data"]["user_registrations"]["all"]["absolute"]) * 100, 2);
            
        $this->statistics["data"]["user_registrations"]["apple"]["percentage"] = 
            round(($this->statistics["data"]["user_registrations"]["apple"]["absolute"] / $this->statistics["data"]["user_registrations"]["all"]["absolute"]) * 100, 2);
            
        $this->statistics["data"]["user_registrations"]["email"]["percentage"] = 
            round(($this->statistics["data"]["user_registrations"]["email"]["absolute"] / $this->statistics["data"]["user_registrations"]["all"]["absolute"]) * 100, 2);
        
        
    }
    
    public function calcArtifactsStatistics()
    {
        Artifact::chunk(100, function($items){
            foreach ($items as $item){
                
                if($item->artifact_type == 1) {
                    $this->statistics["data"]["artifacts"]["trips"]["absolute"] += 1;
                }
                
                if($item->artifact_type == 2) {
                    $this->statistics["data"]["artifacts"]["towns"]["absolute"] += 1;
                }
                
                if($item->artifact_type == 3) {
                    $this->statistics["data"]["artifacts"]["files"]["absolute"] += 1;
                }
                
                if($item->artifact_type == 8) {
                    $this->statistics["data"]["artifacts"]["note_photos"]["absolute"] += 1;
                }
                
                $this->statistics["data"]["artifacts"]["all"]["absolute"] += 1;
            }
        });
        
        $this->statistics["data"]["artifacts"]["trips"]["percentage"] = 
            round(($this->statistics["data"]["artifacts"]["trips"]["absolute"] / $this->statistics["data"]["artifacts"]["all"]["absolute"]) * 100, 2);
            
        $this->statistics["data"]["artifacts"]["towns"]["percentage"] = 
            round(($this->statistics["data"]["artifacts"]["towns"]["absolute"] / $this->statistics["data"]["artifacts"]["all"]["absolute"]) * 100, 2);
            
        $this->statistics["data"]["artifacts"]["files"]["percentage"] = 
            round(($this->statistics["data"]["artifacts"]["files"]["absolute"] / $this->statistics["data"]["artifacts"]["all"]["absolute"]) * 100, 2);
            
        $this->statistics["data"]["artifacts"]["note_photos"]["percentage"] = 
            round(($this->statistics["data"]["artifacts"]["note_photos"]["absolute"] / $this->statistics["data"]["artifacts"]["all"]["absolute"]) * 100, 2); 
            
            
        //  Средние показатели:
        $this->statistics["data"]["artifacts"]["all"]["average"] = ceil($this->statistics["data"]["artifacts"]["all"]["absolute"] / $this->statistics["data"]["user_registrations"]["all"]["absolute"]);
        $this->statistics["data"]["artifacts"]["trips"]["average"] = ceil($this->statistics["data"]["artifacts"]["trips"]["absolute"] / $this->statistics["data"]["user_registrations"]["all"]["absolute"]);
        $this->statistics["data"]["artifacts"]["towns"]["average"] = ceil($this->statistics["data"]["artifacts"]["towns"]["absolute"] / $this->statistics["data"]["user_registrations"]["all"]["absolute"]);
    }
    
    public function getDauMau(Request $request)
    {
        if($request->has('type')) {
            if($request->type == "current") {
                
                $this->getDauMauCurrentPeriods();
                $this->calcDauMau();
                

            }
        }
        
        
        return response()->json($this->daumau);
    }
    
    public function getDauMauCurrentPeriods()
    {
        // периоды DAU:
        for($i = 0; $i < 30; $i++) {
            $this->daumau["data"]["dau"][] = [
                "unique_users" => 0,
                "from" => Carbon::now()->subDays($i)->startOfDay()->format("Y-m-d H:i:s"),
                "to" => Carbon::now()->subDays($i)->endOfDay()->format("Y-m-d H:i:s"),
            ];
        }
        
        // периоды MAU:
        for($i = 1; $i <= 3; $i++) {
            $this->daumau["data"]["mau"][] = [
                "unique_users" => 0,
                "from" => Carbon::now()->subDays($i * 30)->endOfDay()->format("Y-m-d H:i:s"),
                "to" => Carbon::now()->subDays(($i - 1) * 30)->endOfDay()->format("Y-m-d H:i:s"),
            ];
        }
    }
    
    public function calcDauMau()
    {
        foreach($this->daumau["data"]["dau"] as $key => $period) {
            
            $usersArray = Daily::where('date', '>=', $period['from'])
                        ->where('date', '<=', $period['to'])
                        ->select('user_id')
                        ->groupBy('user_id')
                        ->get();
            
            $this->daumau["data"]["dau"][$key]["unique_users"] = count($usersArray);
        }
        
        if(count($this->daumau["data"]["dau"]) > 0) {
            
            $total = 0;
            
            foreach($this->daumau["data"]["dau"] as $period) {
                $total += $period["unique_users"];
            }
            
            $this->daumau["data"]["total_users"]["dau"] = $total;
            $this->daumau["data"]["average"]["dau"] = round($total / count($this->daumau["data"]["dau"]), 2);
        }

        foreach($this->daumau["data"]["mau"] as $key => $period) {
            
            $usersArray = Daily::where('date', '>=', $period['from'])
                        ->where('date', '<=', $period['to'])
                        ->select('user_id')
                        ->groupBy('user_id')
                        ->get();
            
            $this->daumau["data"]["mau"][$key]["unique_users"] = count($usersArray);
        }
        
        if(count($this->daumau["data"]["mau"]) > 0) {
            
            $total = 0;
            
            foreach($this->daumau["data"]["mau"] as $period) {
                $total += $period["unique_users"];
            }
            
            $this->daumau["data"]["total_users"]["mau"] = $total;
            $this->daumau["data"]["average"]["mau"] = round($total / count($this->daumau["data"]["mau"]), 2);
        }
    }
  
}