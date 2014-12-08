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
 * Console response class
 *
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
class Response
{

    /**
     * Response body
     * @var string
     */
    protected $body = null;

    /**
     * Instantiate a new console response object
     *
     * @param  string $body
     * @return Response
     */
    public function __construct($body = null)
    {
        if (null !== $body) {
            $this->setBody($body);
        }
    }

    /**
     * Set the response body
     *
     * @param  string $body
     * @return Response
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Append text to the response body
     *
     * @param  string $text
     * @return Response
     */
    public function append($text)
    {
        $this->body .= $text;
        return $this;
    }

    /**
     * Get the response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Send the response
     *
     * @return Response
     */
    public function send()
    {
        echo $this->body;
        $this->body = null;
        return $this;
    }

}
