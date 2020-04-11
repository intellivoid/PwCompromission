<?php

    namespace pwc\Response;

    /**
     * Class PasswordResponse
     * @package pwc\Response
     */
    class PasswordResponse
    {
        /**
         * @var string
         */
        private $responseArray;

        /**
         * @var string
         */
        private $password;

        /**
         * PasswordResponse constructor.
         * @param $responseArray
         * @param $password
         */
        public function __construct($responseArray, $password)
        {
            $this->responseArray = $responseArray;
            $this->password = $password;
        }

        /**
         * @return int|mixed
         */
        public function getPassword()
        {
            $sha1 = strtoupper(sha1($this->password));
            $fragment = substr($sha1, 0, 5);
            $passwordCount = 0;

            foreach (explode("\n", $this->responseArray) as $match)
            {
                $line = explode(":", $match);
                if ($fragment . $line[0] === $sha1)
                {
                    $passwordCount = $line[1];
                    break;
                }
            }

            return $passwordCount;
        }
    }
