<?php

namespace App\Repositories\Admins;

use App\Core\Repositories\BaseRepository;
use App\Models\Admins\Admin;
use App\Repositories\Admins\Contract\AdminRepositoryInterface;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface {

    protected $model;

    public function __construct(Admin $admin)
    {
        parent::__construct($admin);
        $this->model = $admin;
    }

    public function updateImage($file)
    {
        $filename = 'Thumb_image_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $this->saveImage($file, $filename);
        return $this->model->update([
            'image' => $path
        ]);
    }

     // public function createAdmin($data){
    //     if($this->checkAdminUnique($data['name'], $data['email'])){
    //         return false;
    //     }else{
    //         return $this->create([
    //             'name' => $data['name'],
    //             'email' => $data['email'],
    //             'password'   => $data['password'],
    //             'created_at' => date('Y-m-d H:i:s')
    //         ]);
    //     }
    // }

    // public function checkAdminUnique($name, $email){
    //     return $this->model->where('name', $name)->where('email', $email)->count() > 0;
    // }

    // public function getCode(){
    //     $code = Code::select('id', 'code')
    //     ->where('status','=','0')
    //     ->limit(1)
    //     ->get();
    //    return $code;
        
    // }

    // public function getListCollabo($start, $end){
    //     $collab = Admin::with(['news' => function($q) use($start, $end){
    //         $q->select('*')->where('created_at','>=', $start)->where('created_at','<=', $end)->where('status','=','2')->count();
    //     }])->where('role','=','2')->paginate(12);

    //     return $collab;
    // }

    public function updateInfor($id, $arrUpdate) {
        
        return $this->model->where('id', $id)->update($arrUpdate);
    }

    // public function searchCode($code){
    //     $codes = Code::select('id', 'code', 'expiry_date','uid', 'status')
    //     ->where('code','=',$code)
    //     ->get();
    //    return $codes;
    // }
 
}