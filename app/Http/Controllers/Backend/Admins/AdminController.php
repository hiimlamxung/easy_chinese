<?php

namespace App\Http\Controllers\Backend\Admins;

use App\Core\Traits\Authorization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRequest;
use App\Models\Admins\Admin;
use App\Repositories\Admins\AdminRepository;
use App\Repositories\Admins\Contract\AdminRepositoryInterface;
use Response;

class AdminController extends Controller
{
    use Authorization;

    private $admin;

    public function __construct(AdminRepositoryInterface $admin)
    {
        $this->admin = $admin;
    }

    public function updateProfile(Request $request, Admin $admin){
        $attributes = $request->only('username', 'password');
        foreach($attributes as $key => $value){
            if(empty($value)){
                unset($attributes[$key]);
                continue;
            }
            if($key == 'password'){
                $attributes[$key] = bcrypt($value);
            }
        }
        if(!empty($attributes)){
            $guard = $this->guard()->user();
            if($guard->can('update', $admin)){
                $repo = new AdminRepository($admin);
                $repo->update($attributes);
            }
        }
        return redirect()->back();
    }

    /**
     * Update image profile
     * @param File $image
     */
    public function updateImage(Request $request, Admin $admin){
        if($request->hasFile('image')){
            $repo = new AdminRepository($admin);
            $repo->updateImage($request->file('image'));
        }
        return redirect()->back();
    }

    /**
     * Get profile admin
     * @param int $admin
     */
    public function profile(Admin $admin){
        $view = view('backend.admins.profile');
        $view->with('admin', $admin);
        return $view;
    }

    /**
     * Get all admin
     * @param int $admin
     */
    public function getAllAdmin(Request $request) {

        $users = $this->admin->getAllWithPaginate();

        return view('backend.admins.user', compact('users'));
    }

    public function changeInfor(Request $request) {

        if($request->ajax()) {

            $type = $request->type;
            $data = $request->value;
            $id = $request->id;

            $arr = [];
            $arr[$type] = $data;

            $update = $this->admin->updateInfor($id, $arr);
    
            return Response::json($update, 200);
        } else {
            return Response::json('Not access', 500);
        }
    }
}
