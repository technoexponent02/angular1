<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {

    public function __construct()
    {

    }

    /**
     *
     * List all parent category.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parentCat()
    {
        $categories = Category::where('parent_id', 0)
                                ->orderBy('category_name')
                                ->get(['id', 'category_name']);
        return response()->json($categories);
    }

    /**
	 *
	 * List all parent category.
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function subCat($id = null)
	{
        $categories = [];
        if ($id) {
            $categories = Category::where('parent_id', $id)
                                    ->orderBy('category_name')
                                    ->get(['id', 'category_name']);
        }
		return response()->json($categories);
	}



}
