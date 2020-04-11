<?php

    namespace pwc\Response;

    /**
     * Class DataClassResponse
     * @package pwc\Response
     */
    class DataClassResponse
    {
        /**
         * @var
         */
        private $dataClass;

        /**
         * DataClassResponse constructor.
         * @param $dataClass
         */
        public function __construct($dataClass)
        {
            $this->dataClass = $dataClass;
        }

        /**
         * @return mixed
         */
        public function getDataClasses()
        {
            return $this->dataClass;
        }
    }
