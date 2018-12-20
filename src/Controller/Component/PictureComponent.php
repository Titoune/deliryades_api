<?php

namespace App\Controller\Component;

use App\Utility\Tools;
use Cake\Controller\Component;

class PictureComponent extends Component
{
    public function imgconvert($picture, $path = IMAGE_PATH)
    {
        $picture = str_replace('data:image/png;base64,', '', $picture);
        $picture = str_replace('data:image/jpeg;base64,', '', $picture);
        $picture = str_replace(' ', '+', $picture);
        $data = base64_decode($picture);
        $picture = Tools::_getRandomFilename(10) . '.jpg';
        $photo = imagecreatefromstring($data);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        if (imagejpeg($photo, $path . DS . $picture, 100)) {
            return $picture;
        } else
            return ('error');
    }
}