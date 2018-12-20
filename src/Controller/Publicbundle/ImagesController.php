<?php

namespace App\Controller\Publicbundle;

use App\Form\ImageValidator;
use Intervention\Image\ImageManager;

class ImagesController extends InitController
{
    private $entities = [
        'ad' => [
            'folder' => 'ads',
            'formats' => [
                'xs' => '400x200',
                'sm' => '800x400'
            ]
        ],
        'city' => [
            'folder' => 'cities',
            'formats' => [
                'xs' => '600x300',
                'sm' => '800x350',
                'md' => '1100x400',
                'lg' => '1600x600'
            ]
        ],
        'negociation' => [
            'folder' => 'devis',
            'formats' => [
                'xs' => '400x200',
                'sm' => '800x400'
            ]
        ],
        'supposed-mayor' => [
            'folder' => 'supposedmayors',
            'formats' => [
                'portrait' => '120x150',
                'square' => '120x120',
            ]
        ],
        'directory-partner' => [
            'folder' => 'directorypartners',
            'formats' => [
                'xs' => '400x200',
                'sm' => '800x400'
            ]
        ],
        'councillor' => [
            'folder' => 'councillors',
            'formats' => [
                'portrait' => '120x150',
                'square' => '120x120',
            ]
        ],
        'picture' => [
            'folder' => 'pictures',
            'formats' => [
                'xs' => '400x200',
                'sm' => '800x400'
            ]
        ],
        'publication' => [
            'folder' => 'publications',
            'formats' => [
                'xs' => '400x200',
                'sm' => '800x400'
            ]
        ],
        'publication-diffusion' => [
            'folder' => 'publication_diffusions',
            'formats' => [
                'xs' => '400x200',
                'sm' => '800x400'
            ]
        ],
        'user' => [
            'folder' => 'users',
            'formats' => [
                'portrait' => '120x150',
                'square' => '120x120',
            ]
        ],
        'signaling' => [
            'folder' => 'signalings',
            'formats' => [
                'xs' => '400x200',
                'sm' => '800x400'
            ]
        ],
        'pdf' => [
            'folder' => 'cities',
            'formats' => [
                'xs' => '400x200',
                'sm' => '800x400'
            ]
        ],
    ];


    public function initialize()
    {

    }

    public function image($type, $id, $format, $filename)
    {
        $response = $this->response->withStatus(404);

        $validation = new ImageValidator();
        if ($validation->execute(['type' => $type, 'id' => $id, 'format' => $format, 'filename' => $filename])) {
            $picture_folder = $this->entities[$type]['folder'];
            $picture_foreign_id = $id;
            $picture_name = pathinfo($filename)['filename'];
            $picture_extension = pathinfo($filename)['extension'];

            if ($format != 'default') {

                if (isset($this->entities[$type]['formats'][$format])) {
                    $picture_width = explode('x', $this->entities[$type]['formats'][$format])[0];
                    $picture_height = explode('x', $this->entities[$type]['formats'][$format])[1];


                    if (file_exists(MEDIA_PATH . $picture_folder . DS . $picture_foreign_id . DS . $picture_name . '_' . $picture_width . 'x' . $picture_height . '.' . $picture_extension)) {
                        $response = $this->response
                            ->withFile(MEDIA_PATH . $picture_folder . DS . $picture_foreign_id . DS . $picture_name . '_' . $picture_width . 'x' . $picture_height . '.' . $picture_extension)
                            ->withStatus(200);
                    } else if (file_exists(MEDIA_PATH . $picture_folder . DS . $picture_foreign_id . DS . $picture_name . '.' . $picture_extension)) {

                        $manager = new ImageManager(['driver' => 'imagick']);
                        $generated_picture = $manager->make(MEDIA_PATH . $picture_folder . DS . $picture_foreign_id . DS . $picture_name . '.' . $picture_extension);
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

                        $generated_picture->save(MEDIA_PATH . $picture_folder . DS . $picture_foreign_id . DS . $picture_name . '_' . $picture_width . 'x' . $picture_height . '.' . $picture_extension);
                        $response = $this->response
                            ->withFile(MEDIA_PATH . $picture_folder . DS . $picture_foreign_id . DS . $picture_name . '_' . $picture_width . 'x' . $picture_height . '.' . $picture_extension)
                            ->withStatus(200);
                    }

                } else if (file_exists(MEDIA_PATH . $picture_folder . DS . $picture_foreign_id . DS . $picture_name . '.' . $picture_extension)) {
                    $response = $this->response
                        ->withFile(MEDIA_PATH . $picture_folder . DS . $picture_foreign_id . DS . $picture_name . '.' . $picture_extension)
                        ->withStatus(200);
                }
            }
        }

        return $response;
    }


//    public function ximage()
//    {
//        $debug_mode = 0;
//        if (!$this->request->getQuery('img')) {
//            $response = $this->response->withFile(MEDIA_PATH . 'introuvable.jpg');
//            return $response;
//        }
//        $url = urldecode($this->request->getQuery('img'));
//
//        //if file exist return file
//        if (file_exists(MEDIA_PATH . $url) && $debug_mode != 1) {
//            $response = $this->response->withFile(MEDIA_PATH . $url);
//            return $response;
//        }
//
//        //Retrieve original file from $url
//        $tmp_url = explode('_', $url);
//        //if not "_" in filename
//        if (!isset($tmp_url[1])) {
//            $response = $this->response->withFile(MEDIA_PATH . 'introuvable.jpg');
//            return $response;
//        }
//        $tmp_ext = explode('.', $tmp_url[1])[1];
//        $original_file_path = MEDIA_PATH . $tmp_url[0] . '.' . $tmp_ext;
//        $res = explode('x', explode('.', $tmp_url[1])[0]);
//        $final_width = $res[0];
//
//        //check if original file exists
//        if (!file_exists($original_file_path)) {
//            $response = $this->response->withFile(MEDIA_PATH . 'introuvable.jpg');
//            return $response;
//        }
//
//        //initialize ImageManager
//        $im = new ImageManager(array('driver' => 'imagick'));
//
//        //get original image informations
//        $original_img['filename'] = pathinfo($original_file_path)['filename'];
//        $original_img['extension'] = pathinfo($original_file_path)['extension'];
//        $im_original_file = $im->make($original_file_path);
//        $original_img['mimetype'] = $im_original_file->exif('MimeType');
//
//
//        $im_original_file->resize($final_width, null, function ($constraint) {
//            $constraint->aspectRatio();
//        });
//
//
//        if ($im_original_file->height() > 600) {
//            if ($im_original_file->width() / $im_original_file->height() < 0.5) {
//                $im_original_file->crop($final_width, 600, 0, 0);
//            } else {
//                $im_original_file->crop($final_width, 600);
//
//            }
//        }
//        $im_original_file->save(MEDIA_PATH . $url);
//
//
//        $response = $this->response->withFile(MEDIA_PATH . $url);
//        return $response;
//
//    }


}

?>
