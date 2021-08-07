<?php

namespace App\Repositories\Admins\Contract;

use App\Core\Repositories\Contract\BaseRepositoryInterface;

interface AdminRepositoryInterface extends BaseRepositoryInterface {
    
    public function updateImage($file);
}