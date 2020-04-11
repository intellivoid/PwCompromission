<?php /** @noinspection PhpUnused */

    namespace pwc\Response;

    /**
     * Class AccountResponse
     * @package pwc\Response
     */
    class AccountResponse
    {
        /**
         * @var array
         */
        private $responseArray;

        /**
         * AccountResponse constructor.
         * @param $responseArray
         */
        public function __construct($responseArray)
        {
            $this->responseArray = $responseArray;
        }

        /**
         * @return bool
         */
        public function hasBreaches()
        {
            return count($this->responseArray) !== 0;
        }

        /**
         * @return array
         */
        public function getBreaches()
        {
            $breaches = [];
            foreach ($this->responseArray as $breach)
            {
                $breaches [] = new BreachResponse($breach);
            }

            return $breaches;
        }

        /**
         * @return array
         */
        public function getDataclasses()
        {
            $dataClasses = [];
            foreach ($this->responseArray as $breach)
            {
                $dataClasses[] = new DataClassResponse($breach['DataClasses']);
            }

            return $dataClasses;
        }
    }
