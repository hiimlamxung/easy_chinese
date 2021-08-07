<?php

namespace App\Core\Traits;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;

trait UploadTable {

    /**
     * Save image
     * @param UploadedFile $file
     * @param string $filename
     * @param string $disk
     * 
     * @return path
     */
    public function saveImage(UploadedFile $file, $filename, $disk = null){
        $folder = !is_null($disk) ? $disk : public_path('/uploads/images/');
        $path = $folder . $filename;
        Image::make($file->path())->save($path);

        return !is_null($disk) ? $path : url("uploads/images/$filename");
    }
}