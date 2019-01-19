<?php

namespace App\Controller\Publicbundle;

use App\Form\ImageValidator;
use Intervention\Image\ImageManager;

class ImagesController extends InitController
{

    public function initialize()
    {

    }

    public function image()
    {
        $response = $this->response->withStatus(404);
        $path = $this->request->getQuery('img');
        $picture_dirname = pathinfo($path)['dirname'];
        $picture_name = pathinfo($path)['filename'];
        $picture_extension = pathinfo($path)['extension'];

        $picture_width = $this->request->getQuery('width');
        $picture_height = $this->request->getQuery('height');


        if (file_exists(WWW_ROOT . 'img' . DS . $path)) {
            $response = $this->response
                ->withFile(WWW_ROOT . 'img' . DS . $path)
                ->withStatus(200);
        } else {

            $manager = new ImageManager(['driver' => 'imagick']);
            $generated_picture = $manager->make(WWW_ROOT . 'img' . DS . $path);
            $generated_picture->resize($picture_width, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            if ($generated_picture->height() > 600) {
                if ($generated_picture->width() / $generated_picture->height() < 0.5) {
                    $generated_picture->crop($picture_width, 600, 0, 0);
                } else {
                    $generated_picture->crop($picture_width, 600);
                }
            }

            $generated_picture->save(WWW_ROOT . 'img' . DS . $picture_dirname . DS . $picture_name . '_' . $picture_width . 'x' . $picture_height . '.' . $picture_extension);
            $response = $this->response
                ->withFile(WWW_ROOT . 'img' . DS . $picture_dirname . DS . $picture_name . '_' . $picture_width . 'x' . $picture_height . '.' . $picture_extension)
                ->withStatus(200);
        }

        return $response;
    }

}

?>
