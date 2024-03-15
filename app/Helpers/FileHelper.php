<?php

namespace App\Helpers;

class FileHelper
{
    public static function storeFiles($files, $path) : void
    {
        $path = public_path($path);
        foreach ($files as $file)
        {
            $fileName = rand(0000,1111) . '-' . date('YmdHis'). '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);
        }
    }

    public static function storeFile($file, $path) : string
    {
        $path = public_path($path);
        $fileName = rand(0000,1111) . '-' . date('YmdHis'). '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);
        return $fileName;
    }

}
