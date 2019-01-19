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
        $query_param = $this->request->getQuery('img');
        $picture_dirname = pathinfo($query_param)['dirname'];
        $picture_name = pathinfo($query_param)['filename'];
        $picture_extension = pathinfo($query_param)['extension'];
        $picture_width = $this->request->getQuery('width');
        $picture_height = $this->request->getQuery('height');

        $original_img_path = WWW_ROOT . 'img' . DS . $picture_dirname . DS . $picture_name . '.' . $picture_extension;

        if ($picture_width && $picture_height) {
            $path = WWW_ROOT . 'img' . DS . $picture_dirname . DS . $picture_name . '_' . $picture_width . 'x' . $picture_height . '.' . $picture_extension;
        } else {
            $path = $original_img_path;
        }

        if (file_exists($path)) {
            $response = $this->response
                ->withFile($path)
                ->withStatus(200);
        } else {
            $manager = new ImageManager(['driver' => 'imagick']);
            $generated_picture = $manager->make($original_img_path);
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

            $generated_picture->save($path);
            $response = $this->response
                ->withFile($path)
                ->withStatus(200);
        }

        return $response;
    }

}

?>
