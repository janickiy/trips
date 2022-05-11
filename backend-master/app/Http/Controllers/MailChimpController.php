<?php 

namespace App\Http\Controllers;

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
use Mail;
use App\User;
use Newsletter;

class MailChimpController extends Controller
{

    /**
     * Make an HTTP GET request - for retrieving data
     *
     * @param   string $method  URL of the API request method
     * @param   array  $args    Assoc array of arguments (usually your data)
     * @param   int    $timeout Timeout limit for request in seconds
     *
     * @return  array|false   Assoc array of API response, decoded from JSON
     */
    public function __construct()
    {
        $this->mailChimp = Newsletter::getApi();
        $this->registrationListID = 'aaef3db13c';
        $this->subscribers = [];
    }
    
    public function test(Request $request)
    {

        $this->getAllSubsctibers();

        // dd($this->subscribers);
        
        foreach($this->subscribers as $subscriber) {
            echo $subscriber['email_address'] . ' / ' . $subscriber['source'] . '<br>';
        }

        //return response()->json($lists);
    }
    
    public function getAllSubsctibers()
    {
        $perPage = 1000;
        $pages = 1;
        $currentPage = 1;
        
        $members = $this->mailChimp->get('lists/'.$this->registrationListID.'/members', [
            'offset' => 0,
            'count' => $perPage
        ]);
        
        $this->subscribers = $members['members'];
        
        $pages = ceil($members['total_items'] / $perPage);
        
        if($pages > 1) {
            for($i = 2; $i <= $pages; $i++) {
                $members = $this->mailChimp->get('lists/'.$this->registrationListID.'/members', [
                    'offset' => $perPage * ($i - 1),
                    'count' => $perPage
                ]);
                
                $this->subscribers = array_merge($this->subscribers, $members['members']);
            }
        }
        
        return true;
    }
    
    public function getLists()
    {
        return $this->mailChimp->get('lists');
    }
    
    public function subscribeEmail($email)
    {
        $result = $this->mailChimp->post("lists/".$this->registrationListID."/members", [
            'email_address' => $email,
            'status'        => 'subscribed',
        ]);
    }


}