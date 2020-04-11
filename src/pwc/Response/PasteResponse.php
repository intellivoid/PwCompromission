<?php

    namespace pwc\Response;

    /**
     * Class PasteResponse
     * @package pwc\Response
     */
    class PasteResponse
    {
        /**
         * @var array
         */
        private $pasteArray;

        /**
         * PasteResponse constructor.
         * @param $pasteArray
         */
        public function __construct($pasteArray)
        {
            $this->pasteArray = $pasteArray;
        }

        /**
         * @return mixed
         */
        public function getSource()
        {
            return $this->pasteArray['Source'];
        }

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->pasteArray['Id'];
        }

        /**
         * @return mixed
         */
        public function getTitle()
        {
            return $this->pasteArray['Title'];
        }

        /**
         * @return mixed
         */
        public function getDate()
        {
            return $this->pasteArray['Date'];
        }

        /**
         * @return mixed
         */
        public function getEmailCount()
        {
            return $this->pasteArray['EmailCount'];
        }
    }
