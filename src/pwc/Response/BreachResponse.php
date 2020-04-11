<?php /** @noinspection PhpUnused */

    namespace pwc\Response;

    /**
     * Class BreachResponse
     * @package pwc\Response
     */
    class BreachResponse
    {
        /**
         * @var array
         */
        private $breach;

        /**
         * BreachResponse constructor.
         * @param $breach
         */
        public function __construct($breach)
        {
            $this->breach = $breach;
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->breach['Name'];
        }

        /**
         * @return mixed
         */
        public function getTitle()
        {
            return $this->breach['Title'];
        }

        /**
         * @return mixed
         */
        public function getDomain()
        {
            return $this->breach['Domain'];
        }

        /**
         * @return mixed
         */
        public function getBreachDate()
        {
            return $this->breach['BreachDate'];
        }

        /**
         * @return mixed
         */
        public function getAddedDate()
        {
            return $this->breach['AddedDate'];
        }

        /**
         * @return mixed
         */
        public function getModifiedDate()
        {
            return $this->breach['ModifiedDate'];
        }

        /**
         * @return mixed
         */
        public function getPwnCount()
        {
            return $this->breach['PwnCount'];
        }

        /**
         * @return mixed
         */
        public function getDescription()
        {
            return $this->breach['Description'];
        }

        /**
         * @return mixed
         */
        public function getIsVerified()
        {
            return $this->breach['IsVerified'];
        }

        /**
         * @return mixed
         */
        public function getIsFabricated()
        {
            return $this->breach['IsFabricated'];
        }

        /**
         * @return mixed
         */
        public function getIsSensitive()
        {
            return $this->breach['IsSensitive'];
        }

        /**
         * @return mixed
         */
        public function getIsActive()
        {
            return $this->breach['IsActive'];
        }

        /**
         * @return mixed
         */
        public function getIsRetired()
        {
            return $this->breach['IsRetired'];
        }

        /**
         * @return mixed
         */
        public function getIsSpamList()
        {
            return $this->breach['IsSpamList'];
        }

        /**
         * @return mixed
         */
        public function getLogoType()
        {
            return $this->breach['LogoType'];
        }

        /**
         * @return DataClassResponse
         */
        public function getDataClasses()
        {
            return new DataClassResponse($this->breach['DataClasses']);
        }
    }
