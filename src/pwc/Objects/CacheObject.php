<?php


    namespace pwc\Objects;

    /**
     * Class CacheObject
     * @package pwc\Objects
     */
    class CacheObject
    {
        /**
         * Unique internal database ID
         *
         * @var int
         */
        public $ID;

        /**
         * Sha256 hashed password
         *
         * @var string
         */
        public $Hash;

        /**
         * Plain text version of the password
         *
         * @var string
         */
        public $PlainText;

        /**
         * Indicates if this password as compromised
         *
         * @var bool
         */
        public $Compromised;

        /**
         * The timestamp when this record was created
         *
         * @var int
         */
        public $Timestamp;

        /**
         * Returns an array which represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'hash' => $this->Hash,
                'plain_text' => $this->PlainText,
                'compromised' => (bool)$this->Compromised,
                'timestamp' => (int)$this->Timestamp
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return CacheObject
         */
        public static function fromArray(array $data): CacheObject
        {
            $CacheObject = new CacheObject();

            if(isset($data['id']))
            {
                $CacheObject->ID = (int)$data['id'];
            }

            if(isset($data['hash']))
            {
                $CacheObject->Hash = $data['hash'];
            }

            if(isset($data['plain_text']))
            {
                $CacheObject->PlainText = $data['plain_text'];
            }

            if(isset($data['compromised']))
            {
                $CacheObject->Compromised = (bool)$data['compromised'];
            }

            if(isset($data['timestamp']))
            {
                $CacheObject->Timestamp = (int)$data['timestamp'];
            }

            return $CacheObject;
        }
    }