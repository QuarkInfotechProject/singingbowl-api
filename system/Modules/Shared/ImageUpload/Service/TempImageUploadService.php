<?php

namespace Modules\Shared\ImageUpload\Service;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TempImageUploadService
{
    private $basePath;

    /**
     * @param $path
     * @param $filename
     * @param $thumb
     * @return string
     */
    public function getAbsolutePath($path = null, $filename = null, $thumb = false)
    {
        return $this->getFilePath($path, $filename ,$thumb);
    }

    /**
     * @param $file
     * @param $destinationPath
     * @param $filename
     * @param $prefix
     * @return mixed|string|null
     * @throws \Src\Admin\ImageUpload\Exception\ThumbnailImageException
     */
    public function upload($file, $destinationPath, $filename=null, $prefix=null)
    {
        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException();
        }

        /** @var  $file UploadedFile */
        if(!$filename) {
            $filename = $this->getFilename($file, $prefix);
        }

        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        return $filename;
    }

    /**
     * @param $filename
     * @param $path
     * @param $thumb
     * @return bool
     */
    public function fileExists($filename, $path = null, $thumb = false)
    {
        return file_exists($this->getAbsolutePath($path, $filename, $thumb));
    }

    /**
     * @param $file
     * @param $prefix
     * @return string
     */
    public function getFilename($file, $prefix= null): string
    {
        /** @var UploadedFile $file */
        $filename = date('HisdmY').TokenGenerator::randomString('alnum',6).'.'.$file->guessClientExtension();
        return $prefix ? $prefix . '_' . $filename : $filename;
    }

    /**
     * @param $filename
     * @param $path
     * @return void
     */
    public function remove($filename, $path = null)
    {
        $image_path = public_path($path) .$filename;

        if(File::exists($image_path)) {
            File::delete($image_path);
        }
    }
}
