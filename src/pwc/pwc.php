<?php


    namespace pwc;


    use acm\acm;
    use Exception;
    use mysqli;

    if(class_exists('acm\acm') == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'acm' . DIRECTORY_SEPARATOR . 'acm.php');
    }

    include(__DIR__ . DIRECTORY_SEPARATOR . 'AutoConfig.php');

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
    }