<?php


    namespace pwc;


    use acm\acm;
    use Exception;
    use msqg\QueryBuilder;
    use mysqli;
    use pwc\Exceptions\DatabaseException;
    use pwc\Objects\CacheObject;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'DatabaseException.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'CacheObject.php');

    if(class_exists('acm\acm') == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'acm' . DIRECTORY_SEPARATOR . 'acm.php');
    }

    if(class_exists('msqg\msqg') == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'msqg' . DIRECTORY_SEPARATOR . 'msqg.php');
    }

    include(__DIR__ . DIRECTORY_SEPARATOR . 'AutoConfig.php');

    /**
     * Class pwc
     * @package pwc
     */
    class pwc
    {
        /**
         * @var acm
         */
        private $acm;

        /**
         * @var mixed
         */
        private $DatabaseConfiguration;

        /**
         * @var mysqli|null
         */
        private $Database;

        /**
         * pwc constructor.
         */
        public function __construct()
        {
            $this->acm = new acm(__DIR__, 'pwc');

            try
            {
                $this->DatabaseConfiguration = $this->acm->getConfiguration('Database');
            }
            catch (Exception $e)
            {
                print("Error loading ACM configuration");
                exit(255);
            }

            if(strtolower($this->DatabaseConfiguration['Enabled']) == "true")
            {
                $this->Database = new mysqli(
                    $this->DatabaseConfiguration['Host'],
                    $this->DatabaseConfiguration['Username'],
                    $this->DatabaseConfiguration['Password'],
                    $this->DatabaseConfiguration['Name'],
                    $this->DatabaseConfiguration['Port']
                );
            }
            else
            {
                $this->Database = null;
            }
        }

        /**
         * Registers a cache object to the database
         *
         * @param string $password
         * @param bool $compromised
         * @throws DatabaseException
         */
        public function registerCacheObject(string $password, bool $compromised)
        {
            $Query = QueryBuilder::insert_into('pwc', array(
                'hash' => $this->Database->real_escape_string(hash('sha256', $password)),
                'plain_text' => $this->Database->real_escape_string($password) ,
                'compromised' => (int)$compromised,
                'timestamp' => (int)time()
            ));
            $QueryResults = $this->Database->query($Query);

            if($QueryResults)
            {
                return;
            }

            throw new DatabaseException($this->Database->error);
        }

        /**
         * Gets an cache object from the database, if one doesn't exist then
         * it will register one.
         *
         * @param string $password
         * @return CacheObject
         * @throws DatabaseException
         */
        public function getCacheObject(string $password): CacheObject
        {
            $hash = hash('sha256', $password);
            $Query = QueryBuilder::select('pwc', [
                'id',
                'hash',
                'plain_text',
                'compromised',
                'timestamp'
            ], 'hash', $this->Database->real_escape_string($hash));
            $QueryResults = $this->Database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->Database->error);
            }

            if($QueryResults->num_rows !== 1)
            {
                $this->registerCacheObject($password, false);
                return $this->getCacheObject($password);
            }

            return CacheObject::fromArray($QueryResults->fetch_array(MYSQLI_ASSOC));
        }

        /**
         * Updates an existing cache object
         *
         * @param CacheObject $cacheObject
         * @return bool
         * @throws DatabaseException
         */
        public function updateCacheObject(CacheObject $cacheObject): bool
        {
            $Query = QueryBuilder::update('pwc', array(
                'compromised' => (int)$cacheObject->Compromised
            ), 'id', (int)$cacheObject->ID);

            $QueryResults = $this->Database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->Database->error);
            }

            return true;
        }
    }