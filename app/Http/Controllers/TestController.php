<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use DB;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {

    }

    /**
     * Serve the angular application.
     *
     * @return \Illuminate\Http\Response
     */
   public function dbBackup()
    {
        $return    = '';
        $allTables = array();
        $result = DB::select('SHOW TABLES');
       // dd($result);
        foreach($result as $key=>$val){
            $tab_name='Tables_in_swolk-dev';
           // dump($val->$a);
           $allTables[] = $val->$tab_name;
        }

        foreach($allTables as $table){
            $result = DB::select('SELECT * FROM '.$table);
            $result = array_map(function($value){ return (array)$value; }, $result);

            $return.= 'DROP TABLE IF EXISTS '.$table.';';
            $row2 = DB::select('SHOW CREATE TABLE '.$table);
            $row2 = array_map(function($value){ return (array)$value; }, $row2);

            $return.= "\n\n".$row2[0]['Create Table'].";\n\n";


            foreach($result as $index=>$row){
                    $array = [];
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                    foreach($row as $j=>$val){
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n","\\n",$row[$j]);
                        if (isset($row[$j])) { $array[] = '"'.$row[$j].'"' ; }
                        else { $array[] = '""'; }

                    }
                    $return .= implode(',', $array);
                    $return .= ");\n";
            }

            $return.="\n\n";
        }

        // Create Backup Folder
        $folder   = storage_path().'/DB_Backup/';
        if (!is_dir($folder))
            mkdir($folder, 0777, true);
         // chmod($folder, 0777);

        //$date = date('m-d-Y-H-i-s', time());

        $filename = $folder."db-backup";

        $handle = fopen($filename.'.sql','w');
        fwrite($handle,$return);
        fclose($handle);

        //After Successful DB Backup move file to s3
        move_to_s3("/backupdb/db-backup.sql", $filename.".sql", $permission = 'private');


      //  $this->info("DB Backup complete");
    }
}
