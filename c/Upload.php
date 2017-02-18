<?php

final class UploadController extends Base
{
    public function uploadImgFile()
    {
        $file = $_FILES['upload1'];
        $path = $this->uploadImg($file);
        $this->_jsonEn('1', $path);
    }
}