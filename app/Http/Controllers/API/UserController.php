<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\Image;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('isAdmin');
        $users = User::latest()->paginate(8);
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('isAdmin');
        $data = $this->validate($request,[
            'name'          =>'required|string|max:191',
            'password'      =>'required|string|min:6',
            'email'         =>'required|string|email|unique:users,email',
            'bio'           =>'sometimes|string|min:5',
            'type'          =>'required',
            'photo'         =>'sometimes'
        ]);
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        return response()->json('user created successfully',200);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);
        $data = $this->validate($request,[
            'name'          =>'required|string|max:191',
            'password'      =>'sometimes|string|min:6',
            'email'         =>'required|string|email|unique:users,email,'.$user->id,
            'bio'           =>'sometimes|string|min:5',
            'type'          =>'required',
            'photo'         =>'sometimes'
        ]);
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }else{
            $data['password'] = $user->password;
        }
        $user->update($data);

        return response()->json('user updated successfully',200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json('user deleted successfully',200);
    }

    public function profile()
    {
        return auth('api')->user();
    }
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();
         $data = $this->validate($request,[
            'name'          =>'required|string|max:191',
            'password'      =>'sometimes|string|min:6',
            'email'         =>'required|string|email|unique:users,email,'.$user->id,
            'bio'           =>'sometimes|string|min:5',
            'type'          =>'required',
            'photo'         => 'required',

        ]);
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }else{
            $data['password'] = $user->password;
        }
        $photo =$request->photo;
        $currentPhoto = $user->photo;
        if ($photo !=  $currentPhoto) {
            $oldImagePath=public_path('images/').$currentPhoto;
            if (file_exists($oldImagePath) && $currentPhoto != null) {
                unlink($oldImagePath);
            }
           $ext = explode('/', mime_content_type($photo))[1];
           $name = rand().'.'.$ext;
           \Image::make($photo)->save(public_path('images/').$name);
            $data['photo'] = $name;
        }
        $user->update($data);
        return $data['photo'];
    }
    public function search($search='')
    {
        if ($search !== '') {
            $users = User::where('name','LIKE',"%$search%")
                        ->orWhere('email','LIKE',"%$search%")
                        ->orWhere('type','LIKE',"%$search%")
                        ->paginate(10);

        }
        return $users;
    }
}
