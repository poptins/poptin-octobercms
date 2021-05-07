<?php namespace Poptin\Poptin\Controllers;

use App;
use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Lang;
use BackendAuth;
use Db;
use Input;
use Request;
use Response;
use Session;

class Info extends Controller
{
    public $implement = [        'Backend\Behaviors\FormController'    ];
    
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'poptin.poptin.info' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Poptin.Poptin', 'main-menu-item');
    }
    
    public function index()
    {
        
        return \Backend::redirect('poptin/poptin/info/dashboard');
    }
    
    public function dashboard()
    {
        $this->addCss('/plugins/poptin/poptin/assets/css/poptin.css');
        $this->addJs('/plugins/poptin/poptin/assets/js/jquery-2.1.4.min.js');
        $this->addJs('/plugins/poptin/poptin/assets/js/poptincustom.js');
        $this->addJs('/plugins/poptin/poptin/assets/js/sweetalert.min.js');
        
        $user = BackendAuth::getUser();
        $email = $user->email;
        
        $sh_status='';
        $dash_status='';
        $with_token='show';
        $without_token='hide';
        $show_dashboard = 'hide';
        $btm_section='';
        $client_id = '';
        if($email){
            $popup_info = $this->getPoptinFormValues();
            $popup_info = json_decode($popup_info,true);
         
            if(!empty($popup_info)){
                if($popup_info['user_id'] == ''){
                    $without_token = 'show';
                    $with_token = 'hide';
                    $dash_status = 'hide';
                    $show_dashboard = 'show';
                    $sh_status = 'hide';
                    $btm_section='show';
                }
                if($popup_info['user_id'] != ''){
                    $sh_status = 'hide';
                    $dash_status = 'hide';
                    $btm_section='hide';
                    $show_dashboard='show';
                }
            }else{
                $sh_status = 'show';
                $btm_section='show';
                $dash_status = 'hide';
                $show_dashboard = 'hide';
            }
            $client_id = $popup_info['client_id'];
        }
        $this->vars['sh_status'] = $sh_status;
        $this->vars['dash_status'] = $dash_status;
        $this->vars['with_token'] = $with_token;
        $this->vars['without_token'] = $without_token;
        $this->vars['show_dashboard'] = $show_dashboard;
        $this->vars['btm_section'] = $btm_section;
        $this->vars['client_id'] = $client_id;
        
        
       
    }
    
    public function getPoptinFormValues(){
        $user = Db::table('poptin_poptin_info')->first();
        return json_encode($user);
    }
    
    public function APICall($url,$data){
        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "POST", CURLOPT_POSTFIELDS => $data, CURLOPT_HTTPHEADER => array("cache-control: no-cache", "content-type: application/x-www-form-urlencoded", "postman-token: 16ba048a-499c-06c8-517c-cea2abb11945"),));
        $response = curl_exec($curl);
        
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
            die;
         }
         else{
             return $response;
         }
    }
    
    public function onSubmitSignUpForm(){
        if (Session::token() !== $_REQUEST['_token']) {
            $error = 'Invalid token';

        } else {
              $email = $_REQUEST['email'];
             if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                   $return_data['status'] = 0;
                   $return_data['message'] = 'Invalid email';
                   echo json_encode($return_data);
                   die();
             }
              $permission_msg="you don't have permission to access.";
              $api_url="https://app.popt.in/api/marketplace/";
              $marketplace="octobercms";
              $data = "email=" . $email. "&marketplace=" . $marketplace;
              $url = $api_url."register";
              $response=$this->APICall($url,$data);
              if($response){
                  $result = json_decode($response);
                
                
                    if (isset($result->success) && ($result->success == '1')) {
                        $uid=$result->user_id;
                        $cid=$result->client_id;
                        $token=$result->token;
                        $login_url=$result->login_url;

                        $return_data['status'] = 1;
                        $return_data['user_id'] = $uid;
                        $return_data['client_id'] = $cid;
                        $return_data['token'] = $token;
                        $return_data['login_url'] = $login_url;
                        $return_data['email'] = $email;

                        $d=date('m/d/Y h:i:s a', time());
                        Db::table('poptin_poptin_info')->insert(
                        ['user_id' => $uid,
                         'client_id' => $cid,
                         'token' => $token,
                         'login_url' => $login_url,
                         'account_email' => $email,
                         'reg_date' => $d
                        ]
                        );
                        
                        
                        $return_data['status'] = 1;
                        $return_data['user_id'] = $cid;
                        $return_data['message'] = "user inserted successfully";
                    
                        
                    } else {
                        $return_data['status'] = 0;
                        $return_data['message'] = 'Already exists';
                    }
                    echo json_encode($return_data);
                    die();
               
                 
              }
        }
    }
    
    public function onSubmitLoginForm()
    {
        if (Session::token() !== $_REQUEST['_token']) {
            $error = "Invalid token";

        } else {
            $cid = $_REQUEST['user_id'];
            if($cid){
                $d=date('m/d/Y h:i:s a', time());
                Db::table('poptin_poptin_info')->insert(
                ['user_id' => '',
                 'client_id' => $cid,
                 'token' => '',
                 'login_url' => '',
                 'account_email' => '',
                 'reg_date' => $d
                ]
                );
                $return_data['status'] = 1;
                $return_data['message'] = "user inserted successfully";
                $return_data['client_id'] = $cid;
                echo json_encode($return_data);
            }else{
                $return_data['status'] = 0;
                $return_data['message'] = "No user id";
                echo json_encode($return_data);
            }
        }
        die;
        
    }
    
    public function goTODashboard(){
      
        $permission_msg="you don't have permission to access.";
       
        $api_url="https://app.popt.in/api/marketplace/";
        $poptin_info = $this->getPoptinFormValues();
        $poptin_info = json_decode($poptin_info,true);
        $token = $poptin_info['token'];
        $user_id = $poptin_info['user_id'];
        $data = "token=" . $token . "&user_id=" . $user_id; 
        $url = $api_url."auth";
        
        $response=$this->APICall($url,$data);
          if($response){
              $result = json_decode($response);
            
            
                if (isset($result->success) && ($result->success == '1')) {
                    $return_data['status'] = 1;
                    $return_data['token'] = $result->token;
                    $return_data['login_url'] = $result->login_url;
               
                    Db::table('poptin_poptin_info')->where('user_id', 1)->update([
                        'token' => $result->token,
                        'login_url' => $result->login_url,
                    ]);
                    
                    $final_url = $result->login_url;
                    header('Location:'.$final_url);
                    exit();
                
                    
                } else {
                    $return_data['status'] = 0;
                    $return_data['message'] = $result->message;
                    echo json_encode($return_data);
                }
                
                die();
           
             
          }
          
    }
    
    public function onRemoveuser(){
        if (Session::token() !== $_REQUEST['_token']) {
            $error = 'Invalid token';

        } else {
            $cid = $_REQUEST['user_id'];
            if(Db::table('poptin_poptin_info')->where('client_id', '=', $cid)->delete()){
                $return_data['status'] = 1;
                $return_data['message'] = "User deleted successfully";
            }else{
                $return_data['status'] = 0;
                $return_data['message'] = "User Not found";
            }
            echo json_encode($return_data);
            die;
        }
    }
    
   

}
