<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Mail;
use Auth;
use Validator;
use DB;

class sendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:testemail';
    protected $listid;
    protected $apikey;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to users to check our artisan command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->apikey = getenv('MAILCHIMP_API_KEY');
        $this->listid = getenv('MAILCHIMP_LIST_ID');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $data = [
            'email'     => 'alapan@technoexponent.com',
            'status'    => 'subscribed',
            'firstname' => 'Alapan',
            'lastname'  => 'Chatterjee'
        ];

        $apiKey    = "4d5c0aa0b2f24c9c5c4091e46186dee5-us13";
        $listId    = "e906220aa3";


        $memberId   = md5(strtolower($data['email']));
        $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);

        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

        $json = json_encode([
            'email_address' => $data['email'],
            'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
            'merge_fields'  => [
                'FNAME'     => $data['firstname'],
                'LNAME'     => $data['lastname']
            ]
        ]);

        $ch = curl_init($url);

        //Send Email to User to test our artisan command
        /*$users = DB::table('users')->where('id', '>', '1')->get();
        foreach($users as $key=>$user){
            $newUser = $user;
            $data['members'][] = array("email_address"=>$newUser->email, "status"=>"subscribed");
        }
        $data['update_existing']    = true;*/

        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $curl_response = curl_exec($ch);
        $info          = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //$this->info($curl_response);
        $this->info($info);
        $this->info('The artisan command for test email is successfull!');
    }
}
