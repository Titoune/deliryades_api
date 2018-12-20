<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.4
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Event\Event;

/**
 * Error Handling Controller
 *
 * Controller used by ExceptionRenderer to render error responses.
 */
class ErrorController extends AppController
{

//    public $api_response_code = 200;
//    public $api_response_status = 'success';
//    public $api_response_charset = 'utf-8';
//    public $api_response_type = 'application/json';
//    public $api_response_flash = null;
//    public $api_response_new_jwt = null;
//    public $api_response_new_socket_jwt = null;
//    public $api_response_data = [];
//    public $api_response_required_update = false;
//    public $api_response_logout = false;
//    public $api_response_account_logout = false;
//    public $api_response_require_terms_acceptance = false;
//
//    public $default_error_messages = [
//        100 => 'Informational: Continue',
//        101 => 'Informational: Switching Protocols',
//        102 => 'Informational: Processing',
//        200 => 'Successful: OK',
//        201 => 'Successful: Created',
//        202 => 'Successful: Accepted',
//        203 => 'Successful: Non-Authoritative Information',
//        204 => 'Successful: No Content',
//        205 => 'Successful: Reset Content',
//        206 => 'Successful: Partial Content',
//        207 => 'Successful: Multi-Status',
//        208 => 'Successful: Already Reported',
//        226 => 'Successful: IM Used',
//        300 => 'Redirection: Multiple Choices',
//        301 => 'Redirection: Moved Permanently',
//        302 => 'Redirection: Found',
//        303 => 'Redirection: See Other',
//        304 => 'Redirection: Not Modified',
//        305 => 'Redirection: Use Proxy',
//        306 => 'Redirection: Switch Proxy',
//        307 => 'Redirection: Temporary Redirect',
//        308 => 'Redirection: Permanent Redirect',
//        400 => 'Client Error: Bad Request',
//        401 => 'Client Error: Unauthorized',
//        402 => 'Client Error: Payment Required',
//        403 => 'Client Error: Forbidden',
//        404 => 'Client Error: Not Found',
//        405 => 'Client Error: Method Not Allowed',
//        406 => 'Client Error: Not Acceptable',
//        407 => 'Client Error: Proxy Authentication Required',
//        408 => 'Client Error: Request Timeout',
//        409 => 'Client Error: Conflict',
//        410 => 'Client Error: Gone',
//        411 => 'Client Error: Length Required',
//        412 => 'Client Error: Precondition Failed',
//        413 => 'Client Error: Request Entity Too Large',
//        414 => 'Client Error: Request-URI Too Long',
//        415 => 'Client Error: Unsupported Media Type',
//        416 => 'Client Error: Requested Range Not Satisfiable',
//        417 => 'Client Error: Expectation Failed',
//        418 => 'Client Error: I\'m a teapot',
//        419 => 'Client Error: Authentication Timeout',
//        420 => 'Client Error: Method Failure',
//        422 => 'Client Error: Unprocessable Entity',
//        423 => 'Client Error: Locked',
//        424 => 'Client Error: Method Failure',
//        425 => 'Client Error: Unordered Collection',
//        426 => 'Client Error: Upgrade Required',
//        428 => 'Client Error: Precondition Required',
//        429 => 'Client Error: Too Many Requests',
//        431 => 'Client Error: Request Header Fields Too Large',
//        444 => 'Client Error: No Response',
//        449 => 'Client Error: Retry With',
//        450 => 'Client Error: Blocked by Windows Parental Controls',
//        451 => 'Client Error: Unavailable For Legal Reasons',
//        494 => 'Client Error: Request Header Too Large',
//        495 => 'Client Error: Cert Error',
//        496 => 'Client Error: No Cert',
//        497 => 'Client Error: HTTP to HTTPS',
//        499 => 'Client Error: Client Closed Request',
//        500 => 'Server Error: Internal Server Error',
//        501 => 'Server Error: Not Implemented',
//        502 => 'Server Error: Bad Gateway',
//        503 => 'Server Error: Service Unavailable',
//        504 => 'Server Error: Gateway Timeout',
//        505 => 'Server Error: HTTP Version Not Supported',
//        506 => 'Server Error: Variant Also Negotiates',
//        507 => 'Server Error: Insufficient Storage',
//        508 => 'Server Error: Loop Detected',
//        509 => 'Server Error: Bandwidth Limit Exceeded',
//        510 => 'Server Error: Not Extended',
//        511 => 'Server Error: Network Authentication Required',
//        598 => 'Server Error: Network read timeout error',
//        599 => 'Server Error: Network connect timeout error',
//    ];
//
//    /**
//     * Initialization hook method.
//     *
//     * @return void
//     */
    public function initialize()
    {
        $this->loadComponent('RequestHandler');
    }
//
//    /**
//     * beforeFilter callback.
//     *
//     * @param \Cake\Event\Event $event Event.
//     * @return \Cake\Http\Response|null|void
//     */
//    public function beforeFilter(Event $event)
//    {
//
//    }
//
//    /**
//     * beforeRender callback.
//     *
//     * @param \Cake\Event\Event $event Event.
//     * @return \Cake\Http\Response|null|void
//     */
//    public function beforeRender(Event $event)
//    {
//        parent::beforeRender($event);
//        $this->api_response_code = $event->getSubject()->response->getStatusCode();
//        $this->api_response_flash = $this->default_error_messages[$event->getSubject()->response->getStatusCode()] ?? "An error has occured";
//
//        if (DEBUG == true) {
//            $this->viewBuilder()->setTemplatePath('Error');
//        } else {
//            return $this->buildApiResponse();
//        }
//
//    }
//
//    protected function encryptData($data)
//    {
//        $salt = openssl_random_pseudo_bytes(256);
//        $iv = openssl_random_pseudo_bytes(16);
//        $iterations = 999;
//        $key = hash_pbkdf2("sha512", AES256_PASSPHRASE, $salt, $iterations, 64);
//        $return = [
//            'cipher' => base64_encode(openssl_encrypt($data, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv)),
//            'iv' => bin2hex($iv),
//            'salt' => bin2hex($salt)
//        ];
//        return json_encode($return);
//    }
//
//
////    protected function decryptData($data)
////    {
////        $jsondata = json_decode($data, true);
////        try {
////            $salt = hex2bin($jsondata["salt"]);
////            $iv = hex2bin($jsondata["iv"]);
////        } catch (\Exception $e) {
////            return null;
////        }
////
////        $cipher = base64_decode($jsondata["cipher"]);
////        $iterations = 999; //same as js encrypting
////
////        $key = hash_pbkdf2("sha512", AES256_PASSPHRASE, $salt, $iterations, 64);
////        $decrypted = openssl_decrypt($cipher, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);
////        return $decrypted;
////    }
//
//    private function buildApiResponse()
//    {
//        if ($this->api_response_code != 200) {
//            $this->api_response_status = 'error';
//        }
//
//        $return = [];
//        $return['code'] = $this->api_response_code;
//        if ($this->api_response_code == 500) {
//            $this->api_response_logout = true;
//            $this->api_response_flash = 'Vous avez été déconnecté en raison d\'une erreur de connexion avec le serveur. Nous avons été prévenu et faisons le nécessaire pour corriger le problème. Merci de vous identifier à nouveau';
//        }
//        $return['status'] = $this->api_response_status;
//        $return['flash'] = $this->api_response_flash;
//
//        if (!empty($this->api_response_require_terms_acceptance)) {
//            $return['require_terms_acceptance'] = $this->api_response_require_terms_acceptance;
//        }
//
//        if (!empty($this->api_response_required_update)) {
//            $return['required_update'] = $this->api_response_required_update;
//        }
//        if (!empty($this->api_response_logout)) {
//            $return['logout'] = $this->api_response_logout;
//
//        }
//
//        if (!empty($this->api_response_account_logout)) {
//            $return['account_logout'] = $this->api_response_account_logout;
//        }
//
//        if (!empty($this->api_response_new_jwt)) {
//            $return['new_jwt'] = $this->api_response_new_jwt;
//        }
//
//        if (!empty($this->api_response_new_socket_jwt)) {
//            $return['new_socket_jwt'] = $this->api_response_new_socket_jwt;
//        }
//
//        if (!empty($this->api_response_data)) {
//            $return['data'] = $this->api_response_data;
//        }
//
//        $this->response = $this->response
//            ->withCharset($this->api_response_charset)
//            ->withType($this->api_response_type)
//            ->withStatus($this->api_response_code)
//            ->withDisabledCache();
//
//        if (version_compare($this->request->getQuery('api'), '2.0.9') < 0) {
//            //obsolete < 2.0.9
//            $this->response = $this->response->withStringBody(json_encode($return));
//        } else {
//            $this->response = $this->response->withStringBody(json_encode($return));
////            $this->response = $this->response->withStringBody($this->encryptData(json_encode($return)));
//        }
//
//
//        return $this->response;
//    }
//
//    /**
//     * afterFilter callback.
//     *
//     * @param \Cake\Event\Event $event Event.
//     * @return \Cake\Http\Response|null|void
//     */
//    public function afterFilter(Event $event)
//    {
//
//    }
}