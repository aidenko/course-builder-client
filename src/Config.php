<?php

namespace Aidenko\CourseBuilderClient;

/**
 * Class Config
 * @package Aidenko\CourseBuilderClient
 *
 * @method Config setVerificationUrl(string $url)
 * @method Config setVerificationUsername(string $username)
 * @method Config setVerificationPassword(string $password)
 *
 * @method string getVerificationUrl()
 * @method string getVerificationUsername()
 * @method string getVerificationPassword()
 *
 * @method Config setCourseBuilderUrl(string $url)
 * @method Config setCourseBuilderUsername(string $username)
 * @method Config setCourseBuilderPassword(string $password)
 *
 * @method string getCourseBuilderUrl()
 * @method string getCourseBuilderUsername()
 * @method string getCourseBuilderPassword()
 *
 * @method Config setLicenseKey(string $key);
 * @method Config setCustomerId(int $id)
 * @method Config setSecret(string $secret)
 * @method Config setPublicKey(string $file_path_or_key_string)
 *
 * @method string getLicenseKey()
 * @method int getCustomerId()
 * @method string getSecret()
 * @method string getPublicKey()
 */
class Config
{
    /**
     * @var string
     *
     * This is a licence key for a CourseBuilder application.
     * It looks like AB-1A2B3C-0D-1A2B3D-0099
     * It can be public
     * Receive it from a CB supplier
     */
    protected $license_key = '';


    /**
     * @var int
     *
     * This is a customer id, simple integer
     * It can be public
     * Receive it from a CB supplier
     */
    protected $customer_id = 0;


    /**
     * @var string
     *
     * This is a secret string for calls to CourseBuilder. It is a 32-alphanumeric string
     * Keep in secret
     * Receive it from a CB supplier
     */
    protected $secret = '';


    /**
     * @var string
     *
     * This is a public key for call to CourseBuilder. File or string
     * It can be public
     * Receive it from a CB supplier
     */
    protected $public_key = '';


    /**
     * @var string
     *
     * This is a url of a Course Builder license server
     */
    protected $verification_url = '';


    /**
     * @var string
     *
     * This is a username for a web authentication on a license server.
     * If it does not have authentication - leave blank
     */
    protected $verification_username = '';


    /**
     * @var string
     *
     * This is a password for a web authentication on a license server.
     * If it does not have authentication - leave blank
     */
    protected $verification_password = '';


    /**
     * @var string
     *
     * This is a url of a CourseBuilder application
     */
    protected $course_builder_url = '';


    /**
     * @var string
     *
     * This is a username for a web authentication on a CourseBuilder application.
     * If it does not have authentication - leave blank
     */
    protected $course_builder_username = '';


    /**
     * @var string
     *
     * This is a password for a web authentication on a CourseBuilder application.
     * If it does not have authentication - leave blank
     */
    protected $course_builder_password = '';


    /**
     * @var array
     */
    protected $__set = array();

    /**
     * @var null
     */
    protected $__hooks = null;


    /**
     * @return Config
     */
    public static function create()
    {
        return new Config();
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    function __call($name, $arguments)
    {
        if (strpos($name, 'set') === 0) {
            $params = array($this->getPropertyFromMethod($name));
            $params = array_merge($params, $arguments);

            return call_user_func_array(
                array($this, 'set'), $params
            );
        } elseif (strpos($name, 'get') === 0) {
            return call_user_func_array(
                array($this, 'get'),
                array($this->getPropertyFromMethod($name))
            );
        }
        return $this;
    }

    /**
     * @param $property
     * @param null $value
     * @return $this
     */
    public function set($property, $value = null)
    {
        if ($this->isSettableProperty($property)) {
            if ($this->hasHook($property, 'set')) {
                $this->{$property} = $this->getHook($property, 'set')($value);
            } else {
                $this->{$property} = $value;
            }

            $this->__set[] = $property;
        }

        return $this;
    }

    /**
     * @param $property
     * @return null
     */
    public function get($property)
    {
        if ($this->validateProperty($property)) {
            if ($this->hasHook($property, 'get')) {
                return $this->getHook($property, 'get')($this->{$property});
            } else {
                return $this->{$property};
            }
        }

        return null;
    }

    /**
     * @param $method
     * @return string
     */
    private function getPropertyFromMethod($method)
    {
        return strtolower(preg_replace(
            '/\B([A-Z])/',
            '_$1',
            str_replace(array('set', 'get'), '', $method)
        ));
    }

    /**
     * @param $property
     * @return bool
     */
    private function validateProperty($property)
    {
        return property_exists($this, $property);
    }

    /**
     * @param $property
     * @return bool
     */
    private function isSettableProperty($property)
    {
        return $this->validateProperty($property) && !in_array($property, $this->__set);
    }

    /**
     * @return null|\stdClass
     */
    private function hooks()
    {
        if (is_null($this->__hooks)) {
            $hooks = new \stdClass();

            $hooks->public_key = new \stdClass();

            $hooks->public_key->set = function ($public_key) {
                if (is_file($public_key)) {
                    return file_get_contents($public_key);
                } else {
                    return $public_key;
                }
            };

            $hooks->verification_url = new \stdClass();

            $hooks->verification_url->set = function ($url) {
                return trim($url, ' /') . '/';
            };

            $hooks->course_builder_url = new \stdClass();

            $hooks->course_builder_url->set = function ($url) {
                return trim($url, ' /') . '/';
            };

            $this->__hooks = $hooks;
        }

        return $this->__hooks;
    }

    /**
     * @param $property
     * @param $action
     * @return bool
     */
    private function hasHook($property, $action)
    {
        $hooks = $this->hooks();

        if (isset($hooks->{$property}, $hooks->{$property}->{$action}) && is_callable($hooks->{$property}->{$action})) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $property
     * @param $action
     * @return null
     */
    private function getHook($property, $action)
    {
        if ($this->hasHook($property, $action)) {
            return $this->hooks()->{$property}->{$action};
        } else {
            return null;
        }
    }
}