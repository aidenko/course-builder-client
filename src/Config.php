<?php

namespace Aidenko\CourseBuilderClient;

class Config
{
    protected $license_key = null;
    protected $customer_id = null;
    protected $secret = null;
    protected $public_key = null;
    protected $verification_url = null;
    protected $verification_username = null;
    protected $verification_password = null;
    protected $course_builder_url = null;
    protected $course_builder_username = null;
    protected $course_builder_password = null;

    protected $__set = array();

    function __construct(
        $license_key,
        $customer_id,
        $secret,
        $public_key,
        $verification_url,
        $verification_username,
        $verification_password,
        $course_builder_url,
        $course_builder_username,
        $course_builder_password
    ) {

    }

    function __call($name, $arguments)
    {
        $property = '';

        if (strpos($name, 'set') === 0) {

            $property = $this->getPropertyFromMethod($name);

            $params = array($property);
            $params = array_merge($params, $arguments);

            return call_user_func_array(
                array(get_class($this), 'set'), $params
            );


        } elseif (strpos($name, 'get') === 0) {
            $property = $this->getPropertyFromMethod($name);
            
            return call_user_func_array(
                array(get_class($this), 'set'), array($property)
            );
        }
    }

    public function set($property, $value = null)
    {
        if ($this->isSettableProperty($property)) {
            $this->{$property} = $value;
            $this->__set[] = $property;
        }

        return $this;
    }

    public function get($property)
    {
        if ($this->validateProperty($property)) {
            return $this->{$property};
        }

        return null;
    }

    private function getPropertyFromMethod($method)
    {
        return strtolower(preg_replace(
            '/\B([A-Z])/',
            '_$1',
            str_replace(array('set', 'get'), '', $method)
        ));
    }

    private function validateProperty($property)
    {
        return isset($this->{$property});
    }

    private function isSettableProperty($property)
    {
        return $this->validateProperty($property) && !in_array($property, $this->__set);
    }
}