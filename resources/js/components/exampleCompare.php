<?php 

use App\User;

/**
 * 
 */

class searchController extends Controller
{
	
	public function searchUsersMethodOne($search='')
	{
		if ($search != '') {
			
			$users = User::where(function($query) use ($search){
				$query->where('name','LIKE',"%$search%")
				->orWhere('email','LIKE',"%$search%");
			})->paginate(10);

		}
	}
	public function searchUsersMethodTwo($search='')
	{
		if ($search != '') {
			
			$users = User::where('name','LIKE',"%$search%")
						->orWhere('email','LIKE',"%$search%")
						->paginate(10);

		}
	}


	public function search(Request $request)
    {
        $request->validate([
            'search'=>'string'
        ]);
        $search = sanitize($request->input('search'));
        $searchTerms = explode(' ', $search);
        $query = Product::query();
        foreach ($searchTerms as $searchTerm) {
                    $query->with('brand')->Where('name', 'like', '%' . $searchTerm . '%')->orWhereHas('brand',function ($q) use($searchTerm){
                        $q->where('name','like','%' . $searchTerm . '%');
                    });
		}

        $searchedProducts = $query->where('availability',1)->paginate(10);
        return view('front.shop.search', compact('searchedProducts'));
    }
}