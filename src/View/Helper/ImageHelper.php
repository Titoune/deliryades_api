<?php

namespace App\View\Helper;

use Cake\View\Helper;


class ImageHelper extends Helper
{

    public $helpers = array('Html', 'Form');

    /**
     * Generate an image with a specific size
     * @param  string $image Path of the image (from the webroot directory)
     * @param  int $width
     * @param  int $height
     * @param  array $options Options (same that HtmlHelper::image)
     * @param  int $quality
     * @return string   <img> tag
     */
    public function resize($image, $width, $height, $options = array(), $image_path = IMAGE_PATH, $image_url = IMAGE_URL, $quality = 100)
    {
        if (!file_exists($image_path . $image)) {
            $image_path = WWW_ROOT . 'img' . DS;
            $image = 'introuvable.jpg';
            $image_url = 'img/';
        }
        if (isset($options['nobalise']) && $options['nobalise'] == 1) {
            unset($options['nobalise']);
        } else {
            $options['width'] = $width;
            $options['height'] = $height;
        }

        $ratio = isset($options['ratio']) ? $options['ratio'] : 0;
        $front = (isset($options['front']) && $options['front'] == 1) ? 1 : 0;
        return $this->Html->image($this->resizedUrl($image, $width, $height, $quality, $ratio, $front, $image_path, $image_url), $options);
    }

    /**
     * Create an image with a specific size
     * @param  string $file Path of the image (from the webroot directory)
     * @param  int $width
     * @param  int $height
     * @param  array $options Options (same that HtmlHelper::image)
     * @param  int $quality
     * @return string   image path
     */

    public function resizedUrl($file, $width, $height, $quality = 100, $ratio, $front, $image_path, $image_url)
    {
        $imageDir = $image_path;

        # We find the right file
        $pathinfo = pathinfo(trim($file, '/'));
        $file = $imageDir . trim($file, '/');
        if ($ratio == 0):
            $output = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $width . 'x' . $height . '.' . $pathinfo['extension'];
        else:
            $output = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $width . 'x' . $height . 'r.' . $pathinfo['extension'];
        endif;

        if (!file_exists($imageDir . $output)) {

            # Setting defaults and meta
            $info = getimagesize($file);
            list($width_old, $height_old) = $info;

            # Create image ressource
            switch ($info[2]) {
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($file);
                    break;
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($file);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($file);
                    break;
                default:
                    return false;
            }

            # We find the right ratio to resize the image before cropping
            $heightRatio = $height_old / $height;
            $widthRatio = $width_old / $width;

            $optimalRatio = $widthRatio;

            if ($ratio == 1) {
                if ($heightRatio > $widthRatio) {
                    $optimalRatio = $heightRatio;
                }
            } else {

                if ($heightRatio < $widthRatio) {
                    $optimalRatio = $heightRatio;
                }
            }
            $height_crop = ($height_old / $optimalRatio);
            $width_crop = ($width_old / $optimalRatio);

            # The two image ressources needed (image resized with the good aspect ratio, and the one with the exact good dimensions)


            $image_crop = imagecreatetruecolor($width_crop, $height_crop);
            $image_resized = imagecreatetruecolor($width, $height);

            $white = imagecolorallocate($image_resized, 255, 255, 255);
            imagefill($image_resized, 0, 0, $white);
            $white = imagecolorallocate($image_crop, 255, 255, 255);
            imagefill($image_crop, 0, 0, $white);


            # This is the resizing/resampling/transparency-preserving magic
            if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
                $transparency = imagecolortransparent($image);
                if ($transparency >= 0) {
                    $trnprt_color['red'] = 255;
                    $trnprt_color['green'] = 255;
                    $trnprt_color['blue'] = 255;
                    $trnprt_indx = 127;
                    $transparent_color = imagecolorsforindex($image, $trnprt_indx);
                    $transparency = imagecolorallocate($image_crop, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                    imagefill($image_crop, 0, 0, $transparency);
                    imagecolortransparent($image_crop, $transparency);
                    imagefill($image_resized, 0, 0, $transparency);
                    imagecolortransparent($image_resized, $transparency);
                } elseif ($info[2] == IMAGETYPE_PNG) {
                    imagealphablending($image_crop, false);
                    imagealphablending($image_resized, false);
                    $color = imagecolorallocatealpha($image_crop, 0, 0, 0, 127);
                    imagefill($image_crop, 0, 0, $color);
                    imagesavealpha($image_crop, true);
                    imagefill($image_resized, 0, 0, $color);
                    imagesavealpha($image_resized, true);
                }
            }


            if ($ratio == 1) {

                imagecopyresampled($image_crop, $image, 0, 0, 0, 0, $width_crop, $height_crop, $width_old, $height_old);

                if ($width_crop > $height_crop) { // si paysage
                    $xposition = 0;
                    if ($width_crop < $width) {
                        $xposition = ($width - $width_crop) / 2;
                    }
                    $yposition = ($height - $height_crop) / 2;
                    imagecopyresampled($image_resized, $image_crop, $xposition, $yposition, 0, 0, $width_crop, $height_crop, $width_crop, $height_crop);
                } else { //portrait
                    $yposition = 0;
                    if ($height_crop < $height) {
                        $yposition = ($height - $height_crop) / 2;
                    }
                    $xposition = ($width - $width_crop) / 2;
                    imagecopyresampled($image_resized, $image_crop, $xposition, $yposition, 0, 0, $width_crop, $height_crop, $width_crop, $height_crop);
                }
            } else {
                imagecopyresampled($image_crop, $image, 0, 0, 0, 0, $width_crop, $height_crop, $width_old, $height_old);
                imagecopyresampled($image_resized, $image_crop, 0, 0, ($width_crop - $width) / 2, ($height_crop - $height) / 2, $width, $height, $width, $height);


            }


            # Writing image according to type to the output destination and image quality
            switch ($info[2]) {
                case IMAGETYPE_GIF:
                    imagegif($image_resized, $imageDir . $output, $quality);
                    break;
                case IMAGETYPE_JPEG:
                    imagejpeg($image_resized, $imageDir . $output, $quality);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($image_resized, $imageDir . $output, 9);
                    break;
                default:
                    return false;
            }
        }

        if ($front == 1) {
            return WEBSITE_URL . $image_url . $output;
        } else {
            return $image_url . $output;
        }

    }


}