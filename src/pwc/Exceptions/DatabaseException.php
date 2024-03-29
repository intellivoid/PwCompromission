<?php


    namespace pwc\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class DatabaseException
     * @package pwc\Exceptions
     */
    class DatabaseException extends Exception
    {
        /**
         * DatabaseException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }