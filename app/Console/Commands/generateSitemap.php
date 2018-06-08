<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Mail;
use Auth;
use Validator;
use DB;

class generateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature     = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description   = 'This command will be used to generate sitemap for our site';

    /**
     * Max number of url allowed per page
     * @var int
     */
    protected $maxUrlPerPage = 20;

    /**
     * @var string
     */

    public $siteUrl          = 'https://swolk.com/';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //Query user profiles and generate profile links from them
        $users = DB::table('users')->where([
            ['croned', '=', 'N'],
            ['id', '>', '1']
            ])->get();

        /*** Make Directory to store profile site maps ***/
        $directory_path = public_path()."/sitemap/profile/";
        @mkdir($directory_path, 0777, true);
        $file_name = $this->readDirAndGetFile($directory_path);
        if(count($users) > 0){

            /*** Check to see if file is present or not ***/
            if(!empty($file_name)){
                $file_name_with_path = public_path()."/sitemap/profile/".$file_name;

                //If file exists then add new profile links
                if(file_exists($file_name_with_path)){

                    $return          = $this->recursivelyAddUrlToFile($file_name_with_path, $users,
                        $file_status='Existing');

                    $this->info('File Updated Successfully');
                }
            }
            else{
                //If not exist then create new file and add profile links recursively
                $file_name = "profile-sitemap1.xml";
                $this->info('File Does not exist. Creating New file...');
                $file_name_with_path = public_path()."/sitemap/profile/".$file_name;
                $return              = $this->recursivelyAddUrlToFile($file_name_with_path, $users, $file_status='New');
                $this->info($return);
            }
        }
        else{
            $this->info('No New user left to be croned');
        }
    }

    /**
     * @param string $directory_path
     * @return string $file_name
     */
    public function readDirAndGetFile($directory_path=''){
        $file_name  = '';
        $file_index = 0;
        if(!empty($directory_path)){
            if(is_dir($directory_path)){
                $dr = opendir($directory_path);
                while(($file = readdir($dr)) !== false){
                    if($file == '.' || $file == '..'){
                        continue;
                    }
                    $file_arr   = explode('.', $file);
                    $name       = trim($file_arr[0]);
                    $index      = substr($name, strpos($name, "sitemap")+strlen("sitemap"));
                    if($index > $file_index){
                        //Always update filename as file with highest index will be our target file
                        $file_name  = $file;
                        $file_index = $index;
                    }

                }
            }
        }
        return $file_name;
    }

    /**
     * @param string $file_name
     * @param string $content
     * @return bool
     */
    public function writeContentToFile($file_name='', $content=''){
        if($file_name == '' || $content == ''){
            return false;
        }
        $handle  = fopen($file_name, 'w') or die('unable to create file');
        fwrite($handle, $content);
        fclose($handle);
        return true;
    }

    /**
     * @param string $file_name
     * @param string $staus
     * @return bool
     *
     */
    public function writeToSitemapIndex($file_name='', $status="add"){

        $site_map_index_file = public_path()."/sitemapindex.xml";
        $file_status         = false;
        $content             = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";

        if(!empty($file_name)){
            $arr        = explode("/", $file_name);
            $file_name  = trim(end($arr));
            $file_url = $this->siteUrl."sitemap/profile/".$file_name;

            if($status == 'add'){

                //If file already exists then get old link and add new link @end of file
                if(file_exists($site_map_index_file)){
                    $xml=simplexml_load_file($site_map_index_file) or die("Error: Cannot create object");
                    foreach($xml as $key=>$node){

                        $sitemap_url = $node->loc;
                        $last_mod    = $node->lastmod;
                        $content    .= '<sitemap>'.
                                '<loc>'.$sitemap_url.'</loc>'.
                                '<lastmod>'.$last_mod.'</lastmod>'.
                                '</sitemap>';
                    }
                }
                $new_sitemap_url  = $file_url;
                //$last_mod         = date("F j, Y, g:i a");
                $last_mod         = date("Y-m-d");
                $content         .= '<sitemap>'.
                    '<loc>'.$new_sitemap_url.'</loc>'.
                    '<lastmod>'. $last_mod.'</lastmod>'.
                    '</sitemap>';
                $content .= "</sitemapindex>";


            }
            else{
                //If update is indented then find the url and update it
                $xml=simplexml_load_file($site_map_index_file) or die("Error: Cannot create object");
                foreach($xml as $key=>$node){
                    #code to find matching sitemap url and update it
                    $sitemap_url = $node->loc;
                    if($sitemap_url == $file_url){
                        //$last_mod = date("F j, Y, g:i a");
                        $last_mod   = date("Y-m-d");
                    }
                    else{
                        $last_mod = $node->lastmod;
                    }
                    $content    .= '<sitemap>'.
                        '<loc>'.$sitemap_url.'</loc>'.
                        '<lastmod>'.$last_mod.'</lastmod>'.
                        '</sitemap>';
                }
                $content .= "</sitemapindex>";

            }
            $file_status = $this->writeContentToFile($site_map_index_file, $content);

        }
        return $file_status;
    }

    /**
     * @param string $file_name
     * @param array $users
     * @param string $file_status
     * @return bool
     */
    public function recursivelyAddUrlToFile($file_name='', $users=array(), $file_status='New'){
        $count          = 0;
        $new_user_count = 0;
        $createNewFile  = false;
        $key_index      = 0;

        if($file_status == 'New'){
            $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                        <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
            foreach ($users as $key => $user) {
                # code...
                $profileurl = htmlentities($this->siteUrl."profile/".$user->username, ENT_QUOTES);
                $content    .= '<url>'.
                    '<loc>'.$profileurl.'</loc>'.
                    '</url>';
                DB::table('users')->where('id', $user->id)->update(['croned'=>'Y']);
                $count++;
                if($count >= $this->maxUrlPerPage && count($users) > $count){
                    $createNewFile = true;
                    $key_index         = $key;
                    break;
                }
            }
        }
        else{
            $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";

            $xml=simplexml_load_file($file_name) or die("Error: Cannot create object");
            foreach($xml as $key=>$node){
                # code to check if username exists or not
                $profileurl = $node->loc;
                $arr        = explode('/', $profileurl);
                $username   = trim(end($arr));
                $exist      = DB::table('users')->where('username', $username)->count();
                if($exist > 0){

                    $content    .= '<url>'.
                        '<loc>'.$profileurl.'</loc>'.
                        '</url>';
                    $count++;
                }
            }

            //Check if old files has reached max url limit and no space to add new urls
            if($count >= $this->maxUrlPerPage){
                $content .= "</urlset>";
                $return   = $this->writeContentToFile($file_name, $content);

                /*** Update site map index accordingly ***/
                $index_status = ($file_status=='New') ? 'add' : 'update';
                $sitemapindex_status = $this->writeToSitemapIndex($file_name, $index_status);

                //Reset Everything add create a new file name
                $content  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
                $count    = 0;

                $arr        = explode('/', $file_name);

                /*** Get Current File name and file path  ***/
                $file_name  = end($arr);
                unset($arr[count($arr) - 1]);
                $file_path  = implode('/', $arr).'/';

                $file_arr   = explode('.', $file_name);
                $file_name  = trim($file_arr[0]);

                /*** Create New file name ***/
                $index      = substr($file_name, strpos($file_name, "sitemap")+strlen("sitemap"));
                $file_name  = str_ireplace($index, ++$index, $file_name);
                $file_name  = $file_path.$file_name.".xml";

            }


            //Add New urls in different array
            foreach ($users as $key => $user) {
                # code...
                $profileurl     = htmlentities($this->siteUrl."profile/".$user->username, ENT_QUOTES);
                $content    .= '<url>'.
                    '<loc>'.$profileurl.'</loc>'.
                    '</url>';
                DB::table('users')->where('id', $user->id)->update(['croned'=>'Y']);
                $count++;
                $new_user_count++;
                //check if total url limit reached and new user is still there
                if($count >= $this->maxUrlPerPage && count($users) > $new_user_count){
                    $createNewFile = true;
                    $key_index     = $key;
                    break;
                }

            }
        }

        /*** Write contents to file ***/
        $content .= "</urlset>";
        $return   = $this->writeContentToFile($file_name, $content);
        /*** Update site map index accordingly ***/
        $index_status = ($file_status=='New') ? 'add' : 'update';
        $sitemapindex_status = $this->writeToSitemapIndex($file_name, $index_status);


        if($createNewFile){

            $arr        = explode('/', $file_name);

            /*** Get Current File name and file path  ***/
            $file_name  = end($arr);
            unset($arr[count($arr) - 1]);
            $file_path  = implode('/', $arr).'/';

            $file_arr   = explode('.', $file_name);
            $file_name  = trim($file_arr[0]);

            /*** Create New file name and call function recursively ***/
            $index      = substr($file_name, strpos($file_name, "sitemap")+strlen("sitemap"));
            $file_name  = str_ireplace($index, ++$index, $file_name);
            $file_name  = $file_path.$file_name.".xml";
            $users      = array_slice($users, $key_index+1);
            $return     = $this->recursivelyAddUrlToFile($file_name, $users, $file_status='New');

        }

        return $return;
    }
}
