<?php /** @noinspection PhpUnused */


    namespace pwc;


    use acm\acm;
    use Exception;
    use msqg\QueryBuilder;
    use mysqli;
    use pwc\Adapter\Adapter;
    use pwc\Adapter\Curl;
    use pwc\Exceptions\DatabaseException;
    use pwc\Objects\CacheObject;
    use pwc\Response\AccountResponse;
    use pwc\Response\BreachResponse;
    use pwc\Response\DataClassResponse;
    use pwc\Response\PasswordResponse;
    use pwc\Response\PasteResponse;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Adapter' . DIRECTORY_SEPARATOR . 'Adapter.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Adapter' . DIRECTORY_SEPARATOR . 'Curl.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Adapter' . DIRECTORY_SEPARATOR . 'FileGetContents.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'DatabaseException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidCredentialsException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'RateLimitExceededException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'UnsupportedException.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'CacheObject.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Response' . DIRECTORY_SEPARATOR . 'AccountResponse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Response' . DIRECTORY_SEPARATOR . 'BreachResponse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Response' . DIRECTORY_SEPARATOR . 'DataClassResponse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Response' . DIRECTORY_SEPARATOR . 'PasswordResponse.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Response' . DIRECTORY_SEPARATOR . 'PasteResponse.php');

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
         * @var string
         */
        private static $base_url = "https://haveibeenpwned.com/api/v2/";

        /**
         * @var string
         */
        private static $password_url = "https://api.pwnedpasswords.com/";

        /**
         * @var Adapter|null
         */
        protected $adapter;

        /**
         * pwc constructor.
         * @param Adapter|null $adapter
         */
        public function __construct(Adapter $adapter=null)
        {
            $this->adapter = $adapter;
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
                'hash' => $this->Database->real_escape_string(strtoupper(sha1($password))),
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
            $hash = strtoupper(sha1($password));
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

        /**
         * Return the adapter being used to connect to the remote server
         *
         * @return Adapter
         */
        protected function getAdapter(): Adapter
        {
            // Backwards compatibility as I won't bump the version number for this
            // yet. When I add PHP 7 support I'll bump it and remove this.
            if (!$this->adapter)
            {
                $this->adapter = new Curl();
            }
            return $this->adapter;
        }

        /**
         * @param Adapter $adapter
         */
        public function setAdapter(Adapter $adapter)
        {
            $this->adapter = $adapter;
        }

        /**
         * @param $url
         * @return mixed
         */
        protected function get($url)
        {
            $body = $this->getAdapter()->get(self::$base_url . $url);
            return json_decode($body ? $body : '[]', true);
        }

        /**
         * @param $account
         * @return AccountResponse
         */
        public function checkAccount($account): AccountResponse
        {
            return new AccountResponse($this->get("breachedaccount/" . urlencode($account)));
        }

        /**
         * @return array
         */
        public function getBreaches(): array
        {
            $breachArray = [];
            $result = $this->get("breaches");
            foreach ($result as $breach) {
                $breachArray[] = new BreachResponse($breach);
            }

            return $breachArray;
        }

        /**
         * @param $name
         * @return BreachResponse
         */
        public function getBreach($name): BreachResponse
        {
            return new BreachResponse($this->get("breach/" . urlencode($name)));
        }

        /**
         * @return DataClassResponse
         */
        public function getDataClasses(): DataClassResponse
        {
            return new DataClassResponse($this->get("dataclasses"));
        }

        /**
         * @param $account
         * @return array
         */
        public function getPasteAccount($account): array
        {
            $pasteArray = [];
            $result = $this->get("pasteaccount/" . urlencode($account));
            foreach ($result as $paste) {
                $pasteArray[] = new PasteResponse($paste);
            }

            return $pasteArray;
        }

        /**
         * @param $password
         * @return PasswordResponse
         */
        public function isPasswordCompromised($password): PasswordResponse
        {
            $sha1 = strtoupper(sha1($password));
            $fragment = substr($sha1, 0, 5);

            $body = $this->getAdapter()->get(self::$password_url . "range/" . urlencode($fragment));

            return new PasswordResponse($body, $password);
        }

        /**
         * Returns the cache object password with an active check
         *
         * @param $password
         * @return CacheObject
         * @throws DatabaseException
         */
        public function checkPassword($password): CacheObject
        {
            /** @var CacheObject $cache */
            $cache = $this->getCacheObject($password);

            if($cache->Compromised)
            {
                return $cache;
            }

            /** @var PasswordResponse $response */
            $response = $this->isPasswordCompromised($password);

            if($response->getPassword() > 0)
            {
                $cache->Compromised = true;
                $this->updateCacheObject($cache);
            }

            return $cache;
        }
    }