<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use DB;
use App\CarAd;
use App\CarBodyStyle;
use App\CarCondition;
use App\CarFuelType;
use App\CarMaker;
use App\CarModel;
use App\CarTransmission;
use App\Http\Requests;
use App\VehicleType;
use Illuminate\Http\Request;

use App\Models\Tag;

class HomeController extends Controller
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
    public function serveApp()
    {
       // dd(1);
        return view('index');
    }

    public function unsupported()
    {
        return view('not_supported');
    }

    /**
     * Serve the angular application.
     *
     * @param string $step
     * @return \Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function serveAppPostAd($step = '')
    {
      
        // Prevent direct access other than general tab.
        if ($step && $step != 'general') {
            return redirect('post-add/general');
        }
        return view('index');
    }

    /**
     * Serve the angular application.
     *
     * @param string $step
     * @return \Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function serveAppEditPost(Request $request, $step = '')
    {
        $id = $request->input('id');
        // Prevent direct access other than general tab.
        if ($step && $step != 'general') {
            return redirect('post-edit/general?id=' . $id);
        }
        return view('index');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function serveHeader()
    {
        return view('tpl.block.header');
    }
    // For non login version 
    public function serveHeaderNonLoginVers()
    {
        return view('tpl.block.non_login_header');
    }


    /**
     * @return \Illuminate\View\View
     */
    public function serveFooter()
    {
        return view('tpl.block.footer');
    }

    /**
     * @return \Illuminate\View\View
     */
     public function serveSidebar()
    {
        return view('tpl.block.sidebar');
    }

     // For non login version 
   
    public function serveSidebarNonLoginVers()
    {
        return view('tpl.block.non_login_sidebar');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function serveQuickView()
    {
        return view('tpl.block.quick_view');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function serveQuickSearch()
    {
        return view('tpl.block.quick_search');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function servePostCardView()
    {
        return view('tpl.post.post-card');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function serveSearchPostCardView()
    {
        return view('tpl.post.search-post-card');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function servePostCardMenuView()
    {
        return view('tpl.post.card.post-card-menu');
    }

    /**
     * @return \Illuminate\View\View
     */
	public function serveSharePostCardView()
    {
        return view('tpl.post.sharepost-card');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function postTypeNavView($templateType)
    {
        if ($templateType == 'mob') {
            return view('tpl.post.post_type_nav_mob');
        }
        else {
            return view('tpl.post.post_type_nav_desk');
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function serveCategoryTagPostView()
    {
        return view('tpl.category-tag.post');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function serveCategoryTagTopChannelView()
    {
        return view('tpl.category-tag.top-channel');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function serveChannelCardView()
    {
        return view('tpl.post.channel-card');
    }
    
    public function serveSearchChannelCardView()
    {
        return view('tpl.post.search-channel-card');
    }

    public function error404()
    {

       return view('errors.404');
    }
	
	public function privacyPolicy()
    {
        return view('privacy.privacy');
    }

    public function termsAndConditions()
    {
        return view('privacy.terms_and_conditions');
    }

    /**
     * Serve the angular application.
     *
     * @return \Illuminate\Http\Response
     */
    public function serveAppFeed()
    {
        // Check if user following any other user.
        $user_follower = DB::table('followers')
            ->where('follower_id', Auth::user()->id)
            ->first(['id']);
        if (!empty($user_follower)) {
            return view('index');
        }
        // Check if user following any category.
        $category_follower = DB::table('category_follower')
            ->where('follower_id', Auth::user()->id)
            ->first(['id']);
        if (!empty($category_follower)) {
            return view('index');
        }
        // Check if user following any tag.
        $tag_user = DB::table('tag_user')
            ->where('user_id', Auth::user()->id)
            ->first(['id']);
        if (!empty($tag_user)) {
            return view('index');
        }
        // Otherwise redirect to explore page.
        return redirect('explore');
    }

    public function mailEarlyAccess(Request $request)
    {
        if (! $request->has('email')) {
            $response = [
                'error_message' => "Invalid request. Missing the 'email' parameter",
                'status' => 'INVALID_REQUEST'
            ];
            return response()->json($response, 400);
        }
        $email = $request->input('email');
        $data = [
            'email' => $email
        ];

        Mail::send('emails.early_access', $data, function ($m) use ($email) {
            $m->from('admin@swolk.com', 'Swolk');

            $m->to($email)->subject('Welcome to Swolk');
        });

        $response ['status'] = ['SUCCESS'];
        return response()->json($response);
    }

    public function changeTagText()
    {
        $tags=Tag::all();
        //dd($tags);
        foreach($tags as $tag)
        {
           // dump($tag->tag_name);
            $tagName=$tag->tag_name;
            $tagName= preg_replace('!\s+!', '-', $tagName);//modify tag space to "-"
            $tagName = preg_replace('/-{2,}/', '-', $tagName);
            $tagName = rtrim($tagName, '-');
            $tagName = ltrim($tagName, '-');
            $tagName=preg_replace('/[^A-Za-z0-9\-]/', '', $tagName);

            Tag::where('id', $tag->id)
                ->update(['tag_name' => $tagName]); 
        }

    }

    public function syncDuplicateTag()
    {
        $tags=DB::table('tags')->orderBy('id')->chunk(100, function ($tags) {
		
            foreach($tags as $tag)
            {
                $similarTags=DB::table('tags')->where('tag_name',$tag->tag_name)->whereNotIn('id',[$tag->id])->get();
                
                foreach($similarTags as $similarTag)
                {
                
                    DB::table('post_tag') ->where('tag_id', $similarTag->id)->update(['tag_id' => $tag->id]);
                    DB::table('tags')->where('id', $similarTag->id)->delete();
                }
                
            }
            });
    }

}
