<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Core\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Repositories\Admins\Contract\AdminRepositoryInterface;
use Javascript;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    private $admin;

    public function __construct(AdminRepositoryInterface $admin)
    {
        $this->admin = $admin;
    }

    public function index(){
        Javascript::put([
            'register_link' => route('backend.register.create')
        ]);
        $view = view('backend.auth.register');
        return $view;
    }

    public function register(CreateAdminRequest $request){
        $params = $request->only('email', 'password', 'username');
        $params['password'] = bcrypt($params['password']);
        try{
            $admin = $this->admin->create($params);
            return response(new AdminResource($admin), Response::HTTP_CREATED);
        }catch(\Exception $e){
            return response(new JsonResponse([], $e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
