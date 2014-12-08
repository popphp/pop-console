<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

/**
 * Console class
 *
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
class Console
{

    /**
     * Console request object
     * @var Request
     */
    protected $request = null;

    /**
     * Console response object
     * @var Response
     */
    protected $response = null;

    /**
     * Instantiate a new console object
     *
     * @return Console
     */
    public function __construct()
    {
        $this->request  = new Request();
        $this->response = new Response();
    }

    /**
     * Get the request object
     *
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Get the response object
     *
     * @return Response
     */
    public function response()
    {
        return $this->response;
    }

}
