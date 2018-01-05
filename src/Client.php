<?php

namespace Aidenko\CourseBuilderClient;

use Httpful\Exception\ConnectionErrorException;
use Httpful\Mime;
use Httpful\Request;

/**
 * Class Client
 * This is a basic API handler for a CourseBuilder / CB License applications
 */
class Client
{
    /**
     * This is a "template" type name in a CourseBuilder.
     * DO NOT CHANGE IT
     */
    const CB_TEMPLATE = 'template';


    /**
     * This is a "course" type name in a CourseBuilder.
     * DO NOT CHANGE IT
     */
    const CB_COURSE = 'course';


    /**
     * This is a "certificate" type name in a CourseBuilder.
     * DO NOT CHANGE IT
     */
    const CB_CERTIFICATE = 'certificate';


    /**
     * @var null
     *
     * This is a Config for the Cb client
     */
    protected $config = null;


    /**
     * CourseBuilderClient constructor.
     * @param Config $config
     */
    function __construct(Config $config)
    {
        $this->config = $config;
    }


    /**
     * The method allows to get a url of the CourseBuilder application where a use can create a new course
     *
     * @param $save_callback_url - url that CourseBuilder sends a request to when saves a Course
     * @return object
     */
    public function getCreateCourseUrl($save_callback_url)
    {
        return $this->getCreateItemUrl(self::CB_COURSE, $save_callback_url);
    }


    /**
     * The method allows to get a url of the CourseBuilder application where a use can create a new template
     *
     * @param $save_callback_url - url that CourseBuilder sends a request to when saves a Template
     * @return object
     */
    public function getCreateTemplateUrl($save_callback_url)
    {
        return $this->getCreateItemUrl(self::CB_TEMPLATE, $save_callback_url);
    }


    /**
     * The method allows to get a url of the CourseBuilder application where a use can create a new certificate
     *
     * @param $save_callback_url - url that CourseBuilder sends a request to when saves a Certificate
     * @return object
     */
    public function getCreateCertificateUrl($save_callback_url)
    {
        return $this->getCreateItemUrl(self::CB_CERTIFICATE, $save_callback_url);
    }


    /**
     * The method allows to save a new course remotely in a CourseBuilder application
     *
     * @param string $title
     * @param string $description
     * @param string $keywords
     * @param string $notes
     * @return array|mixed|object
     */
    public function createCourse($title = '', $description = '', $keywords = '', $notes = '')
    {
        return $this->createItem(self::CB_COURSE, $title, $description, $keywords, $notes);
    }


    /**
     * The method allows to save a new template remotely in a CourseBuilder application
     *
     * @param string $title
     * @param string $description
     * @param string $keywords
     * @param string $notes
     * @return array|mixed|object
     */
    public function createTemplate($title = '', $description = '', $keywords = '', $notes = '')
    {
        return $this->createItem(self::CB_TEMPLATE, $title, $description, $keywords, $notes);
    }


    /**
     * The method allows to save a new certificate remotely in a CourseBuilder application
     *
     * @param string $title
     * @param string $description
     * @param string $keywords
     * @param string $notes
     * @return array|mixed|object
     */
    public function createCertificate($title = '', $description = '', $keywords = '', $notes = '')
    {
        return $this->createItem(self::CB_CERTIFICATE, $title, $description, $keywords, $notes);
    }


    /**
     * The method allows to get a url in a CourseBuilder application where a user can view / edit a course
     *
     * @param $cb_id - id of a course in a CourseBuilder application
     * @param $save_callback_url - url that CourseBuilder sends a request to when saves a Course
     * @return object
     */
    public function getCourseUrl($cb_id, $save_callback_url)
    {
        try {
            $token = $this->getToken($save_callback_url);

            if ($this->validateToken($token)) {
                return (object)array(
                    'status' => true,
                    'url' => $this->config->getCourseBuilderUrl() . 'token/' . $token->token . '/show/' . $cb_id
                );
            }

        } catch (\Exception $e) {
            return (object)array(
                'status' => false,
                'msg' => $e->getMessage()
            );
        }
    }


    /**
     * The method allows to get a url in a CourseBuilder application where a user can view / edit a template
     *
     * @param $cb_id - id of a template in a CourseBuilder application
     * @param $save_callback_url - url that CourseBuilder sends a request to when saves a Template
     * @return object
     */
    public function getTemplateUrl($cb_id, $save_callback_url)
    {
        return $this->getCourseUrl($cb_id, $save_callback_url);
    }


    /**
     * The method allows to get a url in a CourseBuilder application where a user can view / edit a certificate
     *
     * @param $cb_id - id of a certificate in a CourseBuilder application
     * @param $save_callback_url - url that CourseBuilder sends a request to when saves a Certificate
     * @return object
     */
    public function getCertificateUrl($cb_id, $save_callback_url)
    {
        return $this->getCourseUrl($cb_id, $save_callback_url);
    }


    /**
     * The method allows to receive a full HTML of a CoursePLayer with a rendered Course.
     * This HTML can be inserted on a page. Includes a valid HTML page with DOCTYPE etc.
     *
     * @param $cb_id - id of a course in a CourseBuilder application
     * @return array|mixed|object
     */
    public function getPreview($cb_id)
    {
        try {

            $token = $this->getToken();

            if ($this->validateToken($token)) {
                $params = array(
                    'action' => 'preview',
                    'id' => $cb_id,
                    'token' => $token->token
                );

                return $this->makeCall($this->config->getCourseBuilderUrl() . 'api',
                    $this->config->getCourseBuilderUsername(),
                    $this->config->getCourseBuilderPassword(), $params);
            }

        } catch (\Exception $e) {
            return (object)array(
                'status' => false,
                'msg' => $e->getMessage()
            );
        }
    }


    /**
     * The method allows to receive a url of a CoursePLayer with a rendered Course.
     *
     * @param $cb_id - id of a course in a CourseBuilder application
     * @param array $params - different parameters for url, added after # as name=value&name2=value2
     * @param bool $wrapper - when set FALSE the player does not add a header, margins etc, it displays only a course container.
     *              Useful for embedding a player on different pages, to scale a player etc.
     * @return object
     */
    public function getPreviewUrl($cb_id, $params = array(), $wrapper = true)
    {
        try {
            $token = $this->getToken();

            if ($this->validateToken($token)) {

                $url = $this->config->getCourseBuilderUrl() . 'token/' . $token->token . '/preview/' . $cb_id . '/' . intval($wrapper);

                if ($params && is_array($params) && count($params)) {

                    $hash = '';

                    foreach ($params as $k => $v) {
                        $hash .= '&' . $k . '=' . $v;
                    }

                    $url .= '#' . trim($hash, ' &');
                }

                return (object)array(
                    'status' => true,
                    'url' => $url
                );
            }
        } catch (\Exception $e) {
            return (object)array(
                'status' => false,
                'msg' => $e->getMessage()
            );
        }
    }


    /**
     * The method allows to receive course details with slides, rendered preview, rendered HTML slides etc.
     *
     * @param $cb_id - id of a course in a CourseBuilder application
     * @return array|mixed|object
     */
    public function getCourseDetails($cb_id)
    {
        return $this->getItemDetails($cb_id);
    }


    /**
     * The method allows to receive template details with slides, rendered preview, rendered HTML slides etc.
     *
     * @param $cb_id - id of a template in a CourseBuilder application
     * @return array|mixed|object
     */
    public function getTemplateDetails($cb_id)
    {
        return $this->getItemDetails($cb_id);
    }


    /**
     * The method allows to receive certificate details with slides, rendered preview, rendered HTML slides etc.
     *
     * @param $cb_id - id of a certificate in a CourseBuilder application
     * @return array|mixed|object
     */
    public function getCertificateDetails($cb_id)
    {
        return $this->getItemDetails($cb_id);
    }


    /**
     *
     * The method allows to get info about current account
     *
     * @return array|mixed|object
     */
    public function getAccountDetails()
    {
        try {
            $token = $this->getToken();

            if ($this->validateToken($token)) {

                $params = array(
                    'action' => 'account-info',
                    'token' => $token->token
                );

                return $this->makeCall($this->config->getVerificationUrl() . 'api/token/info',
                    $this->config->getVerificationUsername(), $this->config->getVerificationPassword(), $params);
            }

        } catch (\Exception $e) {
            return (object)array(
                'status' => false,
                'msg' => $e->getMessage()
            );
        }
    }


    /**
     * The method makes a call to a License server and receives a token for CourseBuilder requests
     *
     * @param string $save_callback_url - url that CourseBuilder sends a request to when saves a Course / Template / Certificate
     * @return array|mixed|object
     */
    protected function getToken($save_callback_url = '')
    {
        $secret = '';

        if (!openssl_public_encrypt($this->config->getSecret(), $secret, $this->config->getPublicKey())) {
            $secret = $this->config->getSecret();
        }

        $params = array(
            'key' => $this->config->getLicenseKey(),
            'secret' => base64_encode($secret),
            'id' => $this->config->getCustomerId()
        );

        if ($save_callback_url) {
            $url = filter_var($save_callback_url, FILTER_SANITIZE_URL);

            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL) !== false) {
                $params['save_callback_url'] = $save_callback_url;
            }
        }

        return $this->makeCall($this->config->getVerificationUrl() . 'api/token/get',
            $this->config->getVerificationUsername(),
            $this->config->getVerificationPassword(), $params);
    }


    /**
     * The method validates a token received from a License server
     *
     * @param $token - this is a token object returned by getToken() method
     * @return bool
     * @throws \Exception
     */
    protected function validateToken($token)
    {
        $msg = (isset($token->message) && is_array($token->message) ? implode('; ', $token->message) : '');

        if (!is_object($token)) {
            throw new \Exception(__METHOD__ . ': wrong token object: [' . $msg . ']');
        }

        if (!isset($token->token) || $token->token == '') {
            throw new \Exception(__METHOD__ . ': token is empty: [' . $msg . ']');
        }

        if (!isset($token->valid) || $token->valid === false) {
            throw new \Exception(__METHOD__ . ': token is not valid: [' . $msg . ']');
        }

        return true;
    }


    /**
     * @param $type
     * @param string $title
     * @param string $description
     * @param string $keywords
     * @param string $notes
     * @return array|mixed|object
     */
    protected function createItem($type, $title = '', $description = '', $keywords = '', $notes = '')
    {
        try {

            $token = $this->getToken();

            if ($this->validateToken($token)) {
                $params = array(
                    'action' => 'create',
                    'type' => $type,
                    'token' => $token->token,
                    'attributes' => json_encode((object)array(
                        'title' => $title,
                        'description' => $description,
                        'keywords' => $keywords,
                        'notes' => $notes
                    ))
                );

                return $this->makeCall($this->config->getCourseBuilderUrl() . 'api',
                    $this->config->getCourseBuilderUsername(),
                    $this->config->getCourseBuilderPassword(), $params);
            }
        } catch (\Exception $e) {
            return (object)array(
                'status' => false,
                'msg' => $e->getMessage()
            );
        }
    }

    /**
     * @param $cb_id
     * @return array|mixed|object
     */
    protected function getItemDetails($cb_id)
    {
        try {
            $token = $this->getToken();

            if ($this->validateToken($token)) {

                $params = array(
                    'action' => 'details',
                    'id' => $cb_id,
                    'token' => $token->token
                );

                return $this->makeCall($this->config->getCourseBuilderUrl() . 'api',
                    $this->config->getCourseBuilderUsername(),
                    $this->config->getCourseBuilderPassword(), $params);
            }

        } catch (\Exception $e) {
            return (object)array(
                'status' => false,
                'msg' => $e->getMessage()
            );
        }
    }

    /**
     * @param $type
     * @param $save_callback_url
     * @return object
     */
    protected function getCreateItemUrl($type, $save_callback_url)
    {
        try {
            $token = $this->getToken($save_callback_url);

            if ($this->validateToken($token)) {
                return (object)array(
                    'status' => true,
                    'url' => $this->config->getCourseBuilderUrl() . 'token/' . $token->token . '/' . $type . 's/create'
                );
            }

        } catch (\Exception $e) {
            return (object)array(
                'status' => false,
                'msg' => $e->getMessage()
            );
        }
    }

    /**
     * @param $url
     * @param $username
     * @param $password
     * @param array $params
     * @return array|mixed|object
     */
    protected function makeCall($url, $username, $password, $params = array())
    {
        try {
            $request = Request::post($url, $params, Mime::FORM)
                ->addHeader('User-Agent', $_SERVER["HTTP_USER_AGENT"])
                ->expects(Mime::JSON);

            if ($username !== '' || $password !== '') {
                $request->basicAuth($username, $password);
            }

            $response = $request->send()->body;

        } catch (ConnectionErrorException $e) {
            $response = null;
        }

        return $response;
    }
}