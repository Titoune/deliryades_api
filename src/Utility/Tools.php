<?php

namespace App\Utility;


use Cake\Utility\Text;
use Firebase\JWT\JWT;

class Tools
{
    public static function _getRandomHash()
    {
        $str = time() . '-';
        $characters = array_merge(range('a', 'z'), range('2', '9'));
        $max = (count($characters) - 1);
        for ($i = 0; $i < 128; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }

        return hash('sha512', $str);
    }

    public static function _getRandomFilename($length)
    {
        $str = time() . '-';
        $characters = array_merge(range('a', 'z'), range('2', '9'));
        $max = (count($characters) - 1);
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }

        return $str;
    }

    public static function getLoremIpsum($length)
    {
        $return = str_replace("Lorem ipsum dolor sit amet, consectetur adipiscing elit.", "", file_get_contents('http://loripsum.net/api/1/medium/plaintext'));
        return Text::truncate($return, $length, ['exact' => false, 'html' => false, 'ellipsis' => '']);
    }

    public static function _rmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rmdir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function getErrors($errors)
    {
        $array = [];
        foreach ($errors AS $k => $error) {
            foreach ($error AS $e) {
                $array[$k] = $e;
            }
        }
        return $array;
    }


    public static function base64ToJpeg($base64)
    {
        $base64 = str_replace('data:image/png;base64,', '', $base64);
        $base64 = str_replace('data:image/jpeg;base64,', '', $base64);
        $base64 = str_replace(' ', '+', $base64);
        $data = base64_decode($base64);
        return imagecreatefromstring($data);
    }

    public static function getValidations($validation_fields, $context = 'create')
    {
        $array = [];
        foreach ($validation_fields AS $k => $validations) {
            $unprivate = \Closure::bind(function ($prop) {
                return $this->$prop;
            }, $validations, $validations);

            if ($unprivate('_allowEmpty') == false || $unprivate('_allowEmpty') != $context) {
                $array[$k]['notEmpty']['message'] = 'Ce champ est obligatoire';
                $array[$k]['notEmpty']['pass'] = [];
            }

            $rules = $unprivate('_rules');
            foreach ($rules AS $y => $rule) {
                $unprivate_rule = \Closure::bind(function ($prop) {
                    return $this->$prop;
                }, $rule, $rule);
                if ((!is_object($unprivate_rule('_rule'))) && ($unprivate_rule('_on') == null || $unprivate_rule('_on') == $context)) {
                    $array[$k][$unprivate_rule('_rule')]['message'] = $unprivate_rule('_message');
                    $array[$k][$unprivate_rule('_rule')]['pass'] = $unprivate_rule('_pass');
                }
            }
        }

        return $array;
    }

    public static function decodeJwt($authorization, $leeway = null)
    {
        try {
            if ($leeway) {
                JWT::$leeway = 31536000;
            }

            return JWT::decode(str_replace('Bearer ', '', $authorization), TOKEN_CYPHER_KEY, [TOKEN_ALGORYTHM]);
        } catch (\Exception $e) {
            if ($e->getCode() == 0) {
                return false;
            }
            return null;
        }
    }

    public static function encodeJwt($payload = null)
    {
        if (empty($payload)) {
            return false;
        }

        $token = 'Bearer ' . JWT::encode($payload, TOKEN_CYPHER_KEY, TOKEN_ALGORYTHM);
        return $token;
    }


    public static function setPayload($user)
    {
        return [
            'iat' => time() - 2000,
            'exp' => (time() + 3600), //3600
            'platform' => 'mobile',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'cellphone' => $user->cellphone,
                'fullname' => $user->fullname,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'picture_sizes' => $user->picture_sizes,
                'admin' => $user->admin,
                'family_id' => $user->family_id
            ]
        ];
    }

    public static function generateFirebaseRedirectLink($url)
    {
        return FIREBASE_PAGE_LINK_DOMAIN . '?link=' . urlencode($url) . '&apn=' . ANDROID_PACKAGE_NAME . '&ibi=' . IOS_PACKAGE_NAME;
    }
}
