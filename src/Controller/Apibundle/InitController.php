<?php

namespace App\Controller\Apibundle;


use App\Controller\AppController;
use App\Utility\Socket;
use App\Utility\Tools;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;
use Cake\Http\Client;
use Cake\Log\Log;


class InitController extends AppController
{
    public $api_response_code = 200;
    public $api_response_status = 'success';
    public $api_response_charset = 'utf-8';
    public $api_response_type = 'application/json';
    public $api_response_flash = null;
    public $api_response_new_jwt = null;
    public $api_response_new_socket_jwt = null;
    public $api_response_data = [];
    public $api_response_required_update = false;
    public $api_response_logout = false;
    public $api_response_account_logout = false;
    public $api_response_require_terms_acceptance = false;
    public $payloads;
    public $jwt;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->jwt = $this->getJwt();
        $this->payloads = Tools::decodeJwt($this->jwt);

        if (!empty($this->request->getParam('pass'))) {
            foreach ($this->request->getParam('pass') AS $k => $d) {
                if ($d == 'null' || $d == 'undefined') {
                    $this->request->params['pass'][$k] = null;
                }
            }
        }


    }

    //////////////////////////////////////////////////////////////////////////////

    public function getJwt()
    {
        $jwt = null;
        $header = $this->request->getHeader('Authorization');
        if (isset($header[0]) && !empty($header[0])) {
            $explode = explode(' ', $header[0]);
            if (isset($explode[1])) {
                $jwt = $explode[1];
            }
        }
        return $jwt;
    }

    public function checkSession()
    {
        if ($this->payloads == false) {
            // expired session, trying to renew session $check : boolean
            return $this->renewSession();
        } else if ($this->payloads != null) {
            return true;
        }

        $this->api_response_code = 400;
        $this->api_response_logout = true;
        $this->api_response_flash = "Token invalide";
        return $this->buildApiResponse();
    }


    public function checkAppUpdate()
    {
        if ($this->request->getQuery('api')) {
            if (version_compare($this->request->getQuery('api'), APP_MIN_VERSION) < 0) {
                $this->api_response_required_update = true;
                $this->api_response_flash = "Pour continuer à utiliser l'application, vous devez la mettre à jour";
                $this->api_response_code = 400;
                return $this->buildApiResponse();
            }
        }

        return true;
    }


    public function checkBundleAccess($bundle)
    {
        $check = $this->checkAppUpdate();
        if ($check == true) {
            $check = $this->checkSession();
            if ($check != true) {
                return $check;
            }

            if ($bundle == 'administrator') {
                if (isset($this->payloads->user) && $this->payloads->user->admin == 1) {
                    return true;
                } else {
                    $this->api_response_account_logout = true;
                }
            } elseif ($bundle == 'user') {
                if (isset($this->payloads->user)) {
                    return true;
                } else {
                    $this->api_response_logout = true;
                }

            } elseif ($bundle == 'shared') {
                if (isset($this->payloads->user)) {
                    return true;
                } else {
                    $this->api_response_logout = true;
                }
            }
        }


        $this->api_response_code = 400;
        return $this->buildApiResponse();
    }


    public function renewSession()
    {
        $this->payloads = Tools::decodeJwt($this->jwt, 31536000);
        if (isset($this->payloads->user)) {
            $check = false;
            if ($this->payloads->user->cellphone) {
                $check = $this->renewPrincipalSession($this->payloads->user->cellphone, null);
            } elseif ($this->payloads->user->email) {
                $check = $this->renewPrincipalSession($this->payloads->user->email, null);
            }


            if ($check == true) {
                return $check;
            }
        }

        //$this->api_response_code = 400;
        //$this->api_response_account_logout = true;
        $this->api_response_flash = "Veuillez vous reconnecter";
        return $this->buildApiResponse();
    }

    protected function buildApiResponse()
    {
        if ($this->api_response_code != 200) {
            $this->api_response_status = 'error';
        }


        if (isset($this->viewVars['code']) && isset($this->viewVars['message'])) {
            $this->api_response_code = $this->viewVars['code'];
            $this->api_response_flash = $this->viewVars['message'];
        }

        $return = [];
        $return['code'] = $this->api_response_code;
        $return['status'] = $this->api_response_status;
        $return['flash'] = $this->api_response_flash;


        if (!empty($this->api_response_required_update)) {
            $return['required_update'] = $this->api_response_required_update;
        }
        if (!empty($this->api_response_logout)) {
            $return['logout'] = $this->api_response_logout;
        }

        if (!empty($this->api_response_account_logout)) {
            $return['account_logout'] = $this->api_response_account_logout;
        }

        if (!empty($this->api_response_new_jwt)) {
            $return['new_jwt'] = $this->api_response_new_jwt;
        }

        if (!empty($this->api_response_new_socket_jwt)) {
            $return['new_socket_jwt'] = $this->api_response_new_socket_jwt;
        }

        if (!empty($this->api_response_data)) {
            $return['data'] = $this->api_response_data;
        }

        $this->response = $this->response
            ->withCharset($this->api_response_charset)
            ->withType($this->api_response_type)
            ->withStatus($this->api_response_code)
            ->withDisabledCache();

        $this->response = $this->response->withStringBody(json_encode($return));
        return $this->response;
    }


    protected function renewPrincipalSession($credential, $password = null)
    {
        $this->loadModel('Users');
        $user = $this->Users->find()->where([
            'OR' => [
                ['Users.cellphone' => trim($credential)],
                ['Users.email' => trim($credential)]
            ]
        ])->first();
        if ($user) {
            if ($password) {
                $verify = (new DefaultPasswordHasher())->check($password, $user->password);
            } else {
                $verify = true;
            }
            if ($verify == true) {
                if ($user->registered == 1) {
                    $payloads = Tools::setPayload($user);
                    $payloads['user_type'] = 'user';

                    $payloads['jwt'] = Tools::encodeJwt($this->payloads);

                    if ($this->payloads) {
                        $this->payloads = $this->array_to_object(array_merge((array)$payloads, (array)$this->payloads));
                    } else {
                        $this->payloads = $this->array_to_object($payloads);
                    }
                    $this->api_response_new_jwt = Tools::encodeJwt($this->payloads);
                    return true;
                } else {
                    $this->api_response_flash = "Veuillez valider votre adresse email";
                }

            } else {
                $this->api_response_flash = "Login incorrect";
            }
        } else {
            $this->api_response_flash = "login incorrect";
        }


        $this->api_response_code = 400;
        return $this->buildApiResponse();
    }

    public function createFolderIfNotExist($path)
    {
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                Log::warning("Impossible de créer le répertoire " . $path . "' ", 'activite');
                return false;
            }
        }

        return true;
    }


    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        return $this->buildApiResponse();
    }

    public function logfail($content)
    {
        Log::info($content, 'fail');
        return true;
    }

    public function array_to_object($array)
    {
        $obj = new \stdClass;
        foreach ($array as $k => $v) {
            if (strlen($k)) {
                if (is_array($v)) {
                    $obj->{$k} = $this->array_to_object($v); //RECURSION
                } else {
                    $obj->{$k} = $v;
                }
            }
        }
        return $obj;
    }


    public function transformRequestData()
    {
        foreach ($this->request->getData() AS $k => $d) {
            if (is_string($d) && (in_array(strlen($d), [20, 24, 25]))) {
                preg_match('/(\d{4})-(\d{2})-(\d{2})T(\d{2})\:(\d{2})\:(\d{2})\.(\d{3})Z/', $d, $match);
                if (isset($match[0])) {
                    $this->request->data[$k] = date('Y-m-d H:i:s', strtotime($match[0]));
                }
                preg_match('/(\d{4})-(\d{2})-(\d{2})T(\d{2})\:(\d{2})\:(\d{2})\+(\d{2})\:(\d{2})/', $d, $match);
                if (isset($match[0])) {
                    $this->request->data[$k] = date('Y-m-d H:i:s', strtotime($match[0]));
                }

                preg_match('/(\d{4})-(\d{2})-(\d{2})T(\d{2})\:(\d{2})\:(\d{2})Z/', $d, $match);
                if (isset($match[0])) {
                    $this->request->data[$k] = date('Y-m-d H:i:s', strtotime($match[0]));
                }

                preg_match('/(\d{4})-(\d{2})-(\d{2})T(\d{2})\:(\d{2})\:(\d{2}).(\d{3})Z/', $d, $match);
                if (isset($match[0])) {
                    $this->request->data[$k] = date('Y-m-d H:i:s', strtotime($match[0]));
                }

                preg_match('/(\d{4})-(\d{2})-(\d{2})T(\d{2})\:(\d{2})\:(\d{2})\-(\d{2})\:(\d{2})/', $d, $match);
                if (isset($match[0])) {
                    $this->request->data[$k] = date('Y-m-d H:i:s', strtotime($match[0]));
                }
            }
        }
    }

    public function shortUrl($url)
    {
        try {
            $request = (new Client())->post(URL_SHORTENER_URL, [
                'url' => \App\Utility\Tools::generateFirebaseRedirectLink($url)
            ]);

            $response = json_decode($request->body());
            if ($response && $response->code == 200) {
                return $response->data->formated_url;
            } else {
                return $url;
            }
        } catch (\Exception $e) {
            $this->log($e);
            unset($e);
            return $url;
        }
    }

}
