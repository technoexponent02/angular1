<?php

namespace App\Http\Controllers;

use Validator;
use Auth;
use View;
use Response;
use Session;
use Carbon\Carbon;
use DB;

use App\Models\User;
use App\Models\Userview;
use App\Models\Post;
use App\Models\Postview;
use App\Models\Notification;
use App\Models\CommentNotification;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Follower;
use App\Models\State;
use App\Models\Category;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests;

class AnalyticsController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("analytics/index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Get All Analytics of a particular user
     *
     * @return JSON response of full analytics report
     */
    public function getAllAnalytics(){
        $data = [];
        $logged_in_user_id = Auth::user()->id;

        $currentDateTime       = Carbon::now()->toDateTimeString();
        $dayBeforeDateTime     = Carbon::now()->addDays(-1);
        $datetimeBefore24hours = Carbon::now()->addDays(-1);
        $datetimeBefore48hours = Carbon::now()->addDays(-2);
        
        //$online_users
        $totalOnlineUsers           = $this->getAllOnlineUsers($logged_in_user_id);

        //Post Point calculation in last 0-24 hours and 24-48 hours
        $totalPostPointAnalytic     = $this->getPostPointAnalytic($logged_in_user_id, $dayBeforeDateTime, $currentDateTime, $datetimeBefore48hours, $datetimeBefore24hours);

        //Post Visitor calculation in last 0-24 hours and 24-48 hours
        $totalPostVisitorAnalytic   = $this->getPostVisitorAnalytic($logged_in_user_id, $dayBeforeDateTime, $currentDateTime, $datetimeBefore48hours, $datetimeBefore24hours);

        //Total Post Views calculation in last 0-24 hours and 24-48 hours
        $totalPostViewAnalytic      = $this->getPostViewAnalytic($logged_in_user_id, $dayBeforeDateTime, $currentDateTime, $datetimeBefore48hours, $datetimeBefore24hours);

        //Total Profile Views calculation in last 0-24 hours and 24-48 hours
        $totalEngagementScoreAnalytic   = $this->getEngagementScoreAnalytic($logged_in_user_id, $dayBeforeDateTime, $currentDateTime, $datetimeBefore48hours, $datetimeBefore24hours);

        //Total Profile Views calculation in last 0-24 hours and 24-48 hours
        $totalProfileViewAnalytic   = $this->getProfileViewAnalytic($logged_in_user_id, $dayBeforeDateTime, $currentDateTime, $datetimeBefore48hours, $datetimeBefore24hours); 

        //Total Upvotes and downvotes calculation in last 0-24 hours and 24-48 hours
        $totalUpvoteDownvoteAnalytic = $this->getTotalUpvoteDownvoteAnalytic($logged_in_user_id, $type='both', $dayBeforeDateTime, $currentDateTime, $datetimeBefore48hours, $datetimeBefore24hours);

        $data = [
            'totalOnlineUsers'              => $totalOnlineUsers,
            'totalPostPointAnalytic'        => $totalPostPointAnalytic,
            'totalPostVisitorAnalytic'      => $totalPostVisitorAnalytic,
            'totalPostViewAnalytic'         => $totalPostViewAnalytic,
            'totalEngagementScoreAnalytic'  => $totalEngagementScoreAnalytic,
            'totalProfileViewAnalytic'      => $totalProfileViewAnalytic,
            'totalUpvoteDownvoteAnalytic'   => $totalUpvoteDownvoteAnalytic
        ];

        return response()->json($data);
    }

    public function getParticularAnalyticsReport(Request $request){
        $data = [];
        $logged_in_user_id = Auth::user()->id;
        $input_data        = $request->all();
        
        switch($input_data['analytic_type']){
            case 'daily':
                 $to_time_1     = Carbon::now()->toDateTimeString();
                 $from_time_1   = Carbon::now()->addDays(-1);
                 $from_time_2   = Carbon::now()->addDays(-2);
                 $to_time_2     = Carbon::now()->addDays(-1);
                 break;
            case 'weekly':
                 $to_time_1     = Carbon::now()->toDateTimeString();
                 $from_time_1   = Carbon::now()->addDays(-7);
                 $from_time_2   = Carbon::now()->addDays(-14);
                 $to_time_2     = Carbon::now()->addDays(-7);
                 break;
            case 'monthly':
                 $to_time_1     = Carbon::now()->toDateTimeString();
                 $from_time_1   = Carbon::now()->addDays(-30);
                 $from_time_2   = Carbon::now()->addDays(-60);
                 $to_time_2     = Carbon::now()->addDays(-30);
                 break;
                                  
        }

        switch($input_data['analytic_category']){
            case 'points':
                $data = $this->getPostPointAnalytic($logged_in_user_id, $from_time_1, $to_time_1, $from_time_2, $to_time_2);
                break;
            case 'visitors':
                $data = $this->getPostVisitorAnalytic($logged_in_user_id, $from_time_1, $to_time_1, $from_time_2, $to_time_2);
                break;
            case 'postviews':
                $data = $this->getPostViewAnalytic($logged_in_user_id, $from_time_1, $to_time_1, $from_time_2, $to_time_2);
                break;
            case 'profileviews':
                $data = $this->getProfileViewAnalytic($logged_in_user_id, $from_time_1, $to_time_1, $from_time_2, $to_time_2);
                break;
            case 'engagementscore':
                $data = $this->getEngagementScoreAnalytic($logged_in_user_id, $from_time_1, $to_time_1, $from_time_2, $to_time_2);
                break;
            case 'upvote':
                $data = $this->getTotalUpvoteDownvoteAnalytic($logged_in_user_id, $type='upvote', $from_time_1, $to_time_1, $from_time_2, $to_time_2);
                break;
            case 'downvote':
                $data = $this->getTotalUpvoteDownvoteAnalytic($logged_in_user_id, $type='downvote', $from_time_1, $to_time_1, $from_time_2, $to_time_2);
                break;        


        }

        return response()->json($data);

    }

    /**
     * Get All online user currently visiting a user's post
     *
     * @return integer count of online user
     */

    public function getAllOnlineUsers($user_id=0){
        $totalOnlineUsers = 0;
        if($user_id > 0){
            
            $sql_get_online_user = "SELECT COUNT(`post_id`) as `totalPostOpened`,`user_id` FROM `post_opened` WHERE 
            `post_id` IN
            (SELECT 
            `id` 
            FROM `posts` WHERE 
`created_by`='$user_id') AND `user_id`!='$user_id' AND `user_id` IS NOT NULL GROUP BY `user_id`";
            $result_set          = DB::select($sql_get_online_user);

            $sql_get_notlogged_user = "SELECT COUNT(`post_id`) as `totalPostOpened`,`ip` FROM `post_opened` WHERE `post_id` IN(SELECT `id` FROM `posts` WHERE `created_by`='$user_id') AND `user_id` IS NULL GROUP BY `ip`";
            $result_set_2        = DB::select($sql_get_notlogged_user);

            $result_set          = array_merge($result_set, $result_set_2);

            $totalOnlineUsers    = count($result_set);
        }
        return $totalOnlineUsers;
    }

     /**
     * Get All post point analytic between 24 hours and 24-48 hours
     *
     * @return array containing all the post point analytics report
     */

    public function getPostPointAnalytic($user_id=0, $from_time_1, $to_time_1, $from_time_2, $to_time_2){
        $data = [];
        $point_in_last_day     = 0;
        $points_in_24_48_hours = 0;
       if($user_id > 0){

            /****************Calculate Total Post Points in last 24 hours******************/
             $point_in_last_day        = $this->calculatePostPointWithinGivenTime($user_id, $from_time_1, $to_time_1);
            /****************Calculate Total Post Points in last 24 hours******************/

            /****************Calculate Total Post Points in 24 - 48 hours******************/
             $points_in_24_48_hours    = $this->calculatePostPointWithinGivenTime($user_id, $from_time_2, $to_time_2);
            /****************Calculate Total Post Points in 24 - 48 hours******************/

            $pointDifference        = $point_in_last_day - $points_in_24_48_hours;
            $percentageDifference   = ($pointDifference / ($point_in_last_day == 0 ? 1 : $point_in_last_day)) * 100;
            $percentageDifference   = round(number_format($percentageDifference, 2, '.', ''), 0, PHP_ROUND_HALF_UP);

             $data = [
             'pointLastDay'          => $point_in_last_day,
             'pointPercentageDifference'  => $percentageDifference,
             'pointDifference'       => $pointDifference
             ];
        }
        return $data;
    }

    /**
     * Get All post visitor analytic between 24 hours and 24-48 hours
     *
     * @return array containing all the post visitor analytics report
     */

    public function getPostVisitorAnalytic($user_id=0, $from_time_1, $to_time_1, $from_time_2, $to_time_2){
        $data = [];
        $visitor_in_last_day     = 0;
        $visitor_in_24_48_hours  = 0;
        if($user_id > 0){

            /****************Calculate Total Post Visitors in last 24 hours******************/
             $visitor_in_last_day   = $this->calculatePostVisitorWithinGivenTime($user_id, $from_time_1, $to_time_1);
             /****************Calculate Total Post Visitors in last 24 hours******************/


             /****************Calculate Total Post Visitors in last 24 - 48 hours******************/
             $visitor_in_24_48_hours   = $this->calculatePostVisitorWithinGivenTime($user_id, $from_time_2, $to_time_2);
             /****************Calculate Total Post Visitors in last 24 - 48 hours******************/

             $visitorDifference           = $visitor_in_last_day - $visitor_in_24_48_hours;
             $visiotrPercentageDifference = ($visitorDifference / ($visitor_in_last_day == 0 ? 1 : $visitor_in_last_day)) * 100;
             $visiotrPercentageDifference = round(number_format($visiotrPercentageDifference, 2, '.', ''), 0, PHP_ROUND_HALF_UP);

             $data = [
             'visitorInLastDay'            => $visitor_in_last_day,
             'visitorDifference'           => $visitorDifference,
             'visiotrPercentageDifference' => $visiotrPercentageDifference
             ];

        }
        return $data;
    }

    /**
     * Get All post visitor analytic between 24 hours and 24-48 hours
     *
     * @return array containing all the post visitor analytics report
     */
    public function getPostViewAnalytic($user_id=0, $from_time_1, $to_time_1, $from_time_2, $to_time_2){
        $data = [];
        $postview_in_last_day     = 0;
        $postview_in_24_48_hours  = 0;
        if($user_id > 0){
            /****************Calculate Total Post View in last 24 hours******************/
             $postview_in_last_day  = $this->calculatePostViewWithinGivenTime($user_id, $from_time_1, $to_time_1);
            /****************Calculate Total Post View in last 24 hours******************/

            /****************Calculate Total Post View in last 24 - 48 hours******************/
             $postview_in_24_48_hours   = $this->calculatePostViewWithinGivenTime($user_id, $from_time_2, $to_time_2);
             /****************Calculate Total Post View in last 24 - 48 hours******************/

             $postviewDifference           = $postview_in_last_day - $postview_in_24_48_hours;
             $postviewPercentageDifference = ($postviewDifference / ($postview_in_last_day == 0 ? 1 : $postview_in_last_day)) * 100;
             $postviewPercentageDifference = round(number_format($postviewPercentageDifference, 2, '.', ''), 0, PHP_ROUND_HALF_UP);

             $data = [
             'postviewInLastDay'            => $postview_in_last_day,
             'postviewDifference'           => $postviewDifference,
             'postviewPercentageDifference' => $postviewPercentageDifference
             ];
        }
        return $data;
    }

    /**
     * Get all engagement score between a certain period of time
     *
     * @return array containing all the engagement score analytics report
     */
    public function getEngagementScoreAnalytic($user_id=0, $from_time_1, $to_time_1, $from_time_2, $to_time_2){
        $data = [];
        if($user_id > 0){

            /****************Calculate Total Profile view in last 24 hours******************/
            $engagementScore_in_last_day   = $this->calculateEngagementScoreWithinGivenTime($user_id, $from_time_1,
                $to_time_1);


            /****************Calculate Total Profile View in last 24 - 48 hours******************/
            $engagementScore_in_24_48_hours = $this->calculateEngagementScoreWithinGivenTime($user_id, $from_time_2, $to_time_2);


            $engagementScoreDifference           = $engagementScore_in_last_day - $engagementScore_in_24_48_hours;
            $engagementScorePercentageDifference = ($engagementScoreDifference / ($engagementScore_in_last_day == 0 ? 1 : $engagementScore_in_last_day)) * 100;
            $engagementScorePercentageDifference = round(number_format($engagementScorePercentageDifference, 2, '.', ''), 0, PHP_ROUND_HALF_UP);

            $data = [
                'engagementScoreInLastDay'            => $engagementScore_in_last_day,
                'engagementScoreDifference'           => $engagementScoreDifference,
                'engagementScorePercentageDifference' => $engagementScorePercentageDifference
            ];
        }
        return $data;
    }


     /**
     * Get All profile view analytic between 24 hours and 24-48 hours
     *
     * @return array containing all the post visitor analytics report
     */
    public function getProfileViewAnalytic($user_id=0, $from_time_1, $to_time_1, $from_time_2, $to_time_2){
        $data = [];
        $profileview_in_last_day     = 0;
        $profileview_in_24_48_hours  = 0;
        if($user_id > 0){

            /****************Calculate Total Profile view in last 24 hours******************/
             $profileview_in_last_day = $this->calculateProfileViewWithinGivenTime($user_id, $from_time_1, $to_time_1);
             /****************Calculate Total Profile view in last 24 hours******************/

             /****************Calculate Total Profile View in last 24 - 48 hours******************/
             $profileview_in_24_48_hours = $this->calculateProfileViewWithinGivenTime($user_id, $from_time_2, $to_time_2);
             /****************Calculate Total Profile View in last 24 - 48 hours******************/

             $profileviewDifference           = $profileview_in_last_day - $profileview_in_24_48_hours;
             $profileviewPercentageDifference = ($profileviewDifference / ($profileview_in_last_day == 0 ? 1 : $profileview_in_last_day)) * 100;
             $profileviewPercentageDifference = round(number_format($profileviewPercentageDifference, 2, '.', ''), 0, PHP_ROUND_HALF_UP);

             $data = [
             'profileviewInLastDay'            => $profileview_in_last_day,
             'profileviewDifference'           => $profileviewDifference,
             'profileviewPercentageDifference' => $profileviewPercentageDifference
             ];
        }
        return $data;
    }

    public function getTotalUpvoteDownvoteAnalytic($user_id=0, $type='both', $from_time_1, $to_time_1, $from_time_2, $to_time_2){
        $data = [];
        $upvote_in_last_day      = 0;
        $upvote_in_24_48_hours   = 0;
        $downvote_in_last_day    = 0;
        $downvote_in_24_48_hours = 0;


        if($user_id > 0){
            if($type == 'both' || $type == 'upvote')
            {
                /**********************Get Upvote Analytics**************/
                $upvote_in_last_day     = $this->calculateUpvoteOrDownvoteWithinGivenTime($user_id, $from_time_1, $to_time_1, 'upvote');
                $upvote_in_24_48_hours  = $this->calculateUpvoteOrDownvoteWithinGivenTime($user_id, $from_time_2, $to_time_2, 'upvote');
    
                $upvoteDifference                = $upvote_in_last_day - $upvote_in_24_48_hours;
                $upvotePercentageDifference      = ($upvoteDifference / ($upvote_in_last_day == 0 ? 1 : $upvote_in_last_day)) * 100;
                $upvotePercentageDifference      = round(number_format($upvotePercentageDifference, 2, '.', ''), 0, PHP_ROUND_HALF_UP);
    
                $data['upvote'] = array(
                    'upvoteInLastDay'                 => $upvote_in_last_day,
                    'upvoteDifference'                => $upvoteDifference,
                    'upvotePercentageDifference'      => $upvotePercentageDifference      
                );
            }


            if($type == 'both' || $type == 'downvote')
            {
                /*********************Get Downvote analytics*****************/
                $downvote_in_last_day     = $this->calculateUpvoteOrDownvoteWithinGivenTime($user_id, $from_time_1, $to_time_1, 'downvote');
                $downvote_in_24_48_hours    = $this->calculateUpvoteOrDownvoteWithinGivenTime($user_id, $from_time_2, $to_time_2, 'downvote');

                $downvoteDifference              = $downvote_in_last_day - $downvote_in_24_48_hours;
                $downvotePercentageDifference    = ($downvoteDifference / ($downvote_in_last_day == 0 ? 1 : $downvote_in_last_day)) * 100;
                $downvotePercentageDifference    = round(number_format($downvotePercentageDifference, 2, '.', ''), 0, PHP_ROUND_HALF_UP);

                $data['downvote'] = array(
                    'downvoteInLastDay'               => $downvote_in_last_day,
                    'downvoteDifference'              => $downvoteDifference,
                    'downvotePercentageDifference'    => $downvotePercentageDifference      
                );
            }

        }
        return $data;
    }


    public function calculatePostPointWithinGivenTime($user_id, $fromDate, $toDate){
        $ap_points             = 0;
        $ac_points             = 0;
        if($user_id > 0 && $fromDate != '' && $toDate != ''){
            
            /******************Get from post activity Table**********************/
            $sql_postactivity = "select ap.activity_id, count(*) as `total_post_under_activity` from activity_post ap inner join posts p on ap.post_id = p.id where p.created_by = '$user_id' and ap.created_at >= '$fromDate' and ap.created_at <= '$toDate' and ap.user_id <> '$user_id' group by ap.activity_id";

             $result_postactivity = DB::select($sql_postactivity);
             
             foreach($result_postactivity as $key=>$result){
                 $ap_points += $result->total_post_under_activity * $this->getPointFromActivityId
                 ($result->activity_id);
             }

             /******************Get from comment activity Table**********************/
             //$sql_commentactivity = "select ac.activity_id, count(*) as `total_post_under_activity` from activity_comment ac inner join comments c on ac.comment_id = c.id inner join  posts p on p.id = c.post_id where p.created_by = '$user_id' and ac.created_at >= '$fromDate' and ac.created_at <= '$toDate' and ac.user_id <> '$user_id' group by ac.activity_id";
             $sql_commentactivity = "select ac.activity_id from activity_comment ac inner join comments c on ac.comment_id = c.id  where c.user_id='$user_id' and ac.created_at >= '$fromDate' and ac.created_at <= '$toDate' and ac.user_id <> '$user_id'";

            $result_commentactivity = DB::select($sql_commentactivity);
            foreach($result_commentactivity as $key=>$comment) {
                //$ac_points += $comment->total_post_under_activity * $this->getPointFromActivityId($comment->activity_id);
                $ac_points += $this->getPointFromActivityId($comment->activity_id);
            }

        }
        return $ap_points + $ac_points;
    }

    public function calculatePostVisitorWithinGivenTime($user_id, $fromDate, $toDate){
        $total_post_visitor = 0;
        if($user_id > 0 && $fromDate != '' && $toDate != ''){
            
            /******************Get from post activity analytics Table**********************/
            //$sql_postvisitor = "SELECT `ap`.`user_id`,`ap`.`ip_address` FROM `activity_post_analytics` `ap` INNER JOIN `posts` `p` ON `ap`.`post_id`=`p`.`id` WHERE `ap`.`created_at`>='$fromDate' AND `ap`.`created_at`<='$toDate' AND `p`.`created_by`='$user_id' GROUP BY `ap`.`user_id`, `ap`.`ip_address`";

            $sql_postvisitor   = "select distinct(ap.user_id) from activity_post_analytics ap inner join posts p on ap.post_id = p.id where p.created_by = $user_id and ap.created_at >= '$fromDate' and ap.created_at <= '$toDate' and ap.user_id NOT IN (1,$user_id)";
            $result_postvisitor = DB::select($sql_postvisitor);
            
            $sql_postvisitor_ip    = "select distinct(ap.ip_address) from activity_post_analytics ap inner join posts p on ap.post_id = p.id where p.created_by = $user_id and ap.created_at >= '$fromDate' and ap.created_at <= '$toDate' and ap.user_id = 1";
           $result_postvisitor_ip = DB::select($sql_postvisitor_ip); 

           $result_postvisitor = array_merge($result_postvisitor, $result_postvisitor_ip);

           $total_post_visitor = count($result_postvisitor);

        }
        return $total_post_visitor;
    }

    public function calculatePostViewWithinGivenTime($user_id, $fromDate, $toDate){
        $total_post_view = 0;
        if($user_id > 0 && $fromDate != '' && $toDate != ''){
            /******************Get from post activity analytics Table**********************/
            $sql_postview    = "SELECT `ap`.`post_id`,`ap`.`user_id`,`ap`.`ip_address` FROM `activity_post_analytics` `ap` INNER JOIN `posts` `p` ON `ap`.`post_id`=`p`.`id` WHERE `ap`.`created_at`>='$fromDate' AND `ap`.`created_at`<='$toDate' AND `ap`.`user_id` <> '$user_id' AND `p`.`created_by`='$user_id'";
             $result_postview = DB::select($sql_postview);
             $total_post_view = count($result_postview);
        }
        return $total_post_view;
    }

    /**
     * @param $user_id
     * @param $fromDate
     * @param $toDate
     * @return int
     */
    public function calculateEngagementScoreWithinGivenTime($user_id, $fromDate, $toDate){
        $total_engagement_score = 0;

        if($user_id > 0 && $fromDate != '' && $toDate != ''){

            /*** Calculate Engagement Score From Activity Post Table ***/
            $sql_score_profile_activity   = "SELECT `ap`.`activity_id`, COUNT(`ap`.`post_id`) as 
            `total_post_under_activity`, CASE WHEN `ap`.`activity_id`=1 THEN 2 WHEN `ap`.`activity_id`=1 THEN -2 WHEN `ap`.`activity_id`=3 THEN 10 WHEN `ap`.`activity_id`=4 THEN 10 WHEN `ap`.`activity_id`=5 THEN 10 WHEN `ap`.`activity_id`=6 THEN 10 WHEN `ap`.`activity_id`=9 THEN 2 WHEN `ap`.`activity_id`=10 THEN 2 WHEN `ap`.`activity_id`=11 THEN 2 END points FROM `activity_post` as `ap` INNER JOIN `posts` as `p` ON `ap`.`post_id`=`p`.`id` WHERE `p`.`created_by`='$user_id' AND `ap`.`user_id` <> '$user_id' AND `ap` .`created_at`>='$fromDate' AND `ap`.`created_at`<='$toDate' AND `ap`.`activity_id` IN(1 , 2 , 3, 4, 5, 6 , 9, 10, 11) GROUP BY `ap`.`activity_id`";
            $result_score_profile_activity   = DB::select($sql_score_profile_activity);

            foreach($result_score_profile_activity as $key=>$score){
                $total_engagement_score += $score->total_post_under_activity * $score->points;
            }

            /*** Calculate Engagement Score From Activity Post Analytics Table ***/
            $sql_score_profile_activity   = "SELECT `ap`.`activity_id`, COUNT(`ap`.`post_id`) as `total_post_under_activity`, CASE WHEN `ap`.`activity_id`=8 THEN 2 END points FROM `activity_post_analytics` as `ap` INNER JOIN `posts` as `p` ON `ap`.`post_id`=`p`.`id` WHERE `p`.`created_by`='$user_id' AND `ap`.`user_id` <> '$user_id' AND `ap` .`created_at`>='$fromDate' AND `ap`.`created_at`<='$toDate' GROUP BY `ap`.`activity_id`";
            $result_score_profile_activity   = DB::select($sql_score_profile_activity);

            foreach($result_score_profile_activity as $key=>$score){
                $total_engagement_score += $score->total_post_under_activity * $score->points;
            }

            /*** Calculate Engagement Score From Comment Table ***/
            $sql_score_profile_activity   = "SELECT `p`.`id`, `p`.`title`,`c`.`user_id` FROM `posts` as `p` INNER JOIN `comments` as `c` ON `p`.`id`=`c`.`post_id` WHERE `p`.`created_by`='$user_id' AND `c`.`user_id` <> '$user_id' AND `c` .`created_at`>='$fromDate' AND `c`.`created_at`<='$toDate'";
            $result_score_profile_activity   = DB::select($sql_score_profile_activity);

            $total_engagement_score += count($result_score_profile_activity) * 2;

        }

        return $total_engagement_score;
    }


    public function calculateProfileViewWithinGivenTime($user_id, $fromDate, $toDate){
        $total_profile_view = 0;
        if($user_id > 0 && $fromDate != '' && $toDate != ''){
            /******************Get from userview Table**********************/
            $sql_profileview     = "SELECT * FROM `userviews` `uv` WHERE `uv`.`user_id`='$user_id' AND `uv`.`created_at`>='$fromDate' AND `uv`.`created_at`<='$toDate'";
            $result_profileview  = DB::select($sql_profileview);
            $total_profile_view  = count($result_profileview);
        }
        return $total_profile_view;
    }

    public function calculateUpvoteOrDownvoteWithinGivenTime($user_id, $fromDate, $toDate, $type=''){
        $vote_count  = 0;
        $activity_id = 0;
        if($user_id > 0 && $fromDate != '' && $toDate != '' && $type != ''){
            $activity_id = ($type == 'upvote') ? 1 : 2;
            
            /******************Get From Post activity Table**********************/
            //$sql_post_vote     = "select ap.activity_id from activity_post ap inner join posts p on ap.post_id = p.id where p.created_by = '$user_id' and ap.activity_id = '$activity_id' and ap.created_at >= '$fromDate' and ap.created_at <= '$toDate' and ap.user_id <> '$user_id'";
            $result_post_vote     = DB::table('activity_post as ap')
                                 ->join('posts as p', 'ap.post_id', '=', 'p.id')
                                 ->select('ap.activity_id')
                                 ->where([
                                    ['p.created_by', '=', $user_id],
                                    ['ap.activity_id', '=', $activity_id],
                                    ['ap.user_id', '<>', $user_id]
                                  ])
                                 ->whereBetween('ap.created_at', [$fromDate, $toDate])
                                 ->get();
            //$result_post_vote  = DB::select($sql_post_vote);
            $vote_count        = count($result_post_vote);

            /*****************Get From Comment activity table*******************/
            //$sql_comment_vote   = "select ac.activity_id from activity_comment ac inner join comments c on ac.comment_id = c.id inner join posts p on p.id = c.post_id where p.created_by = '$user_id' and ac.activity_id = '$activity_id' and ac.created_at >= '$fromDate' and ac.created_at <= '$toDate' and ac.user_id <> '$user_id'";
            $result_comment_vote  = DB::table('activity_comment as ac')
                                 ->join('comments as c', 'ac.comment_id', '=', 'c.id')
                                 ->select('ac.activity_id')
                                 ->where([
                                    ['c.user_id', '=', $user_id],
                                    ['ac.activity_id', '=', $activity_id],
                                    ['ac.user_id', '<>', $user_id]
                                  ])
                                 ->whereBetween('ac.created_at', [$fromDate, $toDate])
                                 ->get();

            //$result_comment_vote = DB::select($sql_comment_vote);
            $vote_count         += count($result_comment_vote); 

        }
        return $vote_count;
    }


    public function getPointFromActivityId($activity_id=0){
        $point = 0;
        switch($activity_id){
            case 1: $point = 2;break;
            case 2: $point = -2;break;
            case 3: 
            case 4: 
            case 5:
            case 6: $point = 10;break;
            case 7: $point = 0;break;
            case 8: $point = 2;break;
            case 9:
            case 10:
            case 11: $point = 2;break;
            case 12:
            case 13: $point = 0;break;
			case 14: $point = 1;break;
        }
        return $point;

    }
}
