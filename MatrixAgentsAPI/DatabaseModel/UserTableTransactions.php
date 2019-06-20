<?php namespace MatrixAgentsAPI\DatabaseModel;

ini_set('display_errors', 1);
error_reporting(E_ALL);

use MatrixAgentsAPI\Utilities\EventLogger;
use MatrixAgentsAPI\Security\Models\MatrixRegistrationResponseModel;
use MatrixAgentsAPI\DatabaseModel\DBConstants;
use MatrixAgentsAPI\Modules\Login\Model\LoginResponseModel;
use MatrixAgentsAPI\Modules\Login\Model\LoginUserRecord;

class UserTableTransactions
{

    private $logger;
    private $dbConnection;
    private $iRememberProperties = null;
    private $userName;
    private $password;
    private $serverName;
    private $dbName;
    private $port;

    private $constStatusFlags = DBConstants::StatusFlags;
    private $constResponseCode = DBConstants::ResponseCode;
    private $constDisplayMessages = DBConstants::DisplayMessages;

    const MASK_LOG_TRUE = false;

    public function __construct()
    {
        $this->logger = new EventLogger();
    }

    public function getIRememberProperties()
    {
        if ($this->iRememberProperties) {
            //get properties as a section segrated array
            $PROCESS_SECTIONS = true;
            $this->iRememberProperties = parse_ini_file(realpath('../../i-remember-properties.ini'), $PROCESS_SECTIONS);
        }
        return $this->iRememberProperties;
    }

    public function setIRememberProperties($_iRememberProperties)
    {
        $this->iRememberProperties = $_iRememberProperties;
        return $this;
    }

    private function getIRememberDBProperties()
    {
        $dbConfig = null;
        try {
            $this->logger->debug('UserTableTransactions >>> into getIRememberDBProperties method ', self::MASK_LOG_TRUE);
            $iremProps = $this->getIRememberProperties();
            $dbConfig = $iremProps['database-configuration'];
            $this->serverName = $dbConfig['iRemember_servername'];
            $this->port = $dbConfig['iRemember_server_port'];
            $this->dbName = $dbConfig['iRemember_db'];
            $this->userName = $dbConfig['iRemember_nrml_user'];
            $this->password = $dbConfig['iRemember_nrml_user_password'];
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug('UserTableTransactions >>> out of getIRememberDBProperties method ', self::MASK_LOG_TRUE);
            return $dbConfig;
        }
    }

    private function disConnect()
    {
        $this->logger->debug('UserTableTransactions >>> into  disConnect method ', self::MASK_LOG_TRUE);

        $this->dbConnection = null;

        $this->logger->debug('UserTableTransactions >>> out of  disConnect method', self::MASK_LOG_TRUE);
    }

    public function getUser($username, $password)
    {
        $loginResponse = new LoginResponseModel();
        try {
            $this->logger->setSendLogsInResponse(true);
            $this->logger->debug('UserTableTransactions >>> into of getUser method', self::MASK_LOG_TRUE);

            $dbConfig = $this->getIRememberDBProperties();

            $serverName = $dbConfig["iRemember_servername"];
            $port = $dbConfig["iRemember_server_port"];
            $dbName = $dbConfig["iRemember_db"];
            $dbUser = $dbConfig["iRemember_nrml_user"];
            $dbPassword = $dbConfig['iRemember_nrml_user_password'];;

            $this->logger->debug('UserTableTransactions >>> getUser >>> before creating connection', self::MASK_LOG_TRUE);
            
            $conn = mysqli_connect($serverName, $dbUser, $dbPassword, $dbName);

            $this->logger->debug('UserTableTransactions >>> getUser >>> after creating connection', self::MASK_LOG_TRUE);
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            $irem_usr_username = mysqli_real_escape_string($conn, $username);
            $irem_usr_username = filter_var($irem_usr_username, FILTER_SANITIZE_EMAIL);
            $irem_usr_username = filter_var($irem_usr_username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $irem_usr_password = mysqli_real_escape_string($conn, $password);
            $irem_usr_password = password_hash($irem_usr_password, PASSWORD_DEFAULT, ['cost' => 11]);

            $this->logger->debug("attempting query >>>> SELECT * FROM irem_users WHERE irem_usr_username='$irem_usr_username' LIMIT 1", self::MASK_LOG_TRUE);

            // check if user already exists
            $query = mysqli_query($conn, "SELECT * FROM irem_users WHERE irem_usr_username='$irem_usr_username' LIMIT 1");

            if (!$query) {
                die('Error: ' . mysqli_error($conn));
            }

            if (mysqli_num_rows($query) > 0) {

                $this->logger->debug('found matching rows', self::MASK_LOG_TRUE);

                $userRecord = $query->fetch_assoc();
                //check if passwords are matching
                if (password_verify($password, $userRecord['irem_usr_password'])) {
                    $this->logger->debug('passwords are matching', self::MASK_LOG_TRUE);

                    $userRecordModel = new LoginUserRecord();

                    $this->logger->debug('test1', self::MASK_LOG_TRUE);
                    $userRecordModel->setUserId($userRecord['irem_usr_userid'])
                        ->setUserName($userRecord['irem_usr_username'])
                        ->setUserRole($userRecord['irem_usr_userrole']);


                    $loginResponse = $loginResponse->setStatus($this->constStatusFlags['Success'])
                        ->setDisplayMessage('')
                        ->setErrorMessage('')
                        ->setResponseCode($this->constResponseCode['LoginSuccess'])
                        ->setUserRecord($userRecordModel);

                    $this->logger->debug('test3 >>>'
                        . var_export($loginResponse, true), self::MASK_LOG_TRUE);
                } else {
                    $this->logger->debug('passwords are not matching', self::MASK_LOG_TRUE);
                    $loginResponse = $loginResponse->setStatus($this->constStatusFlags['Failure'])
                        ->setDisplayMessage($this->constDisplayMessages['LoginIncorrectUserNamePassword'])
                        ->setErrorMessage('')
                        ->setResponseCode($this->constResponseCode['LoginIncorrectUserNamePassword']);
                }
            } else {

                $this->logger->debug('no user found >>> ' . $irem_usr_username, self::MASK_LOG_TRUE);

                $loginResponse = $loginResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['LoginIncorrectUserNamePassword'])
                    ->setErrorMessage('')
                    ->setResponseCode($this->constResponseCode['LoginFailure']);
            }

            mysqli_close($conn);
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
            $loginResponse = $loginResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode['RegistrationFailure']);
        } finally {
            // $this->logger->debug('Authenticator >>> login >>> loginResponse is' . var_export($loginResponse, true), self::MASK_LOG_TRUE);
            $this->logger->debug('UserTableTransactions >>> out of getUser method ', self::MASK_LOG_TRUE);
            $loginResponse->setLogs($this->logger->getLogs());
            return $loginResponse;
        }
    }

    public function isUser($username)
    {
        $loginResponse = new LoginResponseModel();
        try {
            $this->logger->debug('UserTableTransactions >>> into of getUser method', self::MASK_LOG_TRUE);

            $dbConfig = $this->getIRememberDBProperties();

            $serverName = $dbConfig["iRemember_servername"];
            $port = $dbConfig["iRemember_server_port"];
            $dbName = $dbConfig["iRemember_db"]; //"techdotm_iremakoz";
            $dbUser = $dbConfig["iRemember_nrml_user"]; //"techdotm_iRemNRMLSub";
            $dbPassword = $dbConfig["iRemember_nrml_user_password"]; //"whsGiF04brLNV10f";

            $conn = mysqli_connect($serverName, $dbUser, $dbPassword, $dbName);
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            $irem_usr_username = mysqli_real_escape_string($conn, $username);
            $irem_usr_username = filter_var($irem_usr_username, FILTER_SANITIZE_EMAIL);
            $irem_usr_username = filter_var($irem_usr_username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


            $this->logger->debug("attempting query >>>> SELECT * FROM irem_users WHERE irem_usr_username='$irem_usr_username' LIMIT 1", self::MASK_LOG_TRUE);

            // check if user already exists
            $query = mysqli_query($conn, "SELECT * FROM irem_users WHERE irem_usr_username='$irem_usr_username' LIMIT 1");

            if (!$query) {
                die('Error: ' . mysqli_error($conn));
            }

            if (mysqli_num_rows($query) > 0) {

                $this->logger->debug('found matching rows', self::MASK_LOG_TRUE);

                $userRecord = $query->fetch_assoc();

                $userRecordModel = new LoginUserRecord();

                $userRecordModel->setUserId($userRecord['irem_usr_userid'])
                    ->setUserName($userRecord['irem_usr_username'])
                    ->setUserRole($userRecord['irem_usr_userrole']);

                $loginResponse = $loginResponse->setStatus($this->constStatusFlags['Success'])
                    ->setDisplayMessage('')
                    ->setErrorMessage('')
                    ->setResponseCode($this->constResponseCode['LoginSuccess'])
                    ->setUserRecord($userRecordModel);
            } else {

                $this->logger->debug('no user found >>> ' . $irem_usr_username, self::MASK_LOG_TRUE);

                $loginResponse = $loginResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['LoginIncorrectUserNamePassword'])
                    ->setErrorMessage('')
                    ->setResponseCode($this->constResponseCode['LoginFailure']);
            }

            mysqli_close($conn);
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
            $loginResponse = $loginResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode['RegistrationFailure']);
        } finally {
            // $this->logger->debug('Authenticator >>> login >>> loginResponse is' . var_export($loginResponse, true), self::MASK_LOG_TRUE);
            $this->logger->debug('UserTableTransactions >>> out of getUser method ', self::MASK_LOG_TRUE);
            return $loginResponse;
        }
    }

    public function addUser($username, $password, $userrole)
    {
        $registrationResponse = new MatrixRegistrationResponseModel();
        $returnValueText = $this->constDisplayMessages['TemporaryServiceDownMessage'];
        try {
            $this->logger->debug('UserTableTransactions >>> into of addUser method', self::MASK_LOG_TRUE);

            $registrationResponse->setStatus($this->constStatusFlags['Success'])
                ->setDisplayMessage($returnValueText)
                ->setErrorMessage('')
                ->setResponseCode($this->constResponseCode['RegistrationFailure']);

            // $iremProps = $this->getIRememberProperties();
            $dbConfig = $this->getIRememberDBProperties();

            $serverName = $dbConfig["iRemember_servername"];
             $port = $dbConfig["iRemember_server_port"];
            $dbName = $dbConfig["iRemember_db"];
            $dbUser = $dbConfig["iRemember_nrml_user"];
            $dbPassword = $dbConfig['iRemember_nrml_user_password'];;

            $conn = mysqli_connect($serverName, $dbUser, $dbPassword, $dbName);
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            // new row details
            $irem_usr_userid = uniqid();
            $irem_usr_username = mysqli_real_escape_string($conn, $username);
            $irem_usr_username = filter_var($irem_usr_username, FILTER_SANITIZE_EMAIL);
            $irem_usr_username = filter_var($irem_usr_username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $irem_usr_password = mysqli_real_escape_string($conn, $password);
            $irem_usr_password = password_hash($irem_usr_password, PASSWORD_DEFAULT, ['cost' => 11]);

            $irem_usr_userrole = mysqli_real_escape_string($conn, $userrole);

            // check if user already exists

            $query = mysqli_query($conn, "SELECT * FROM irem_users WHERE irem_usr_username='$irem_usr_username' LIMIT 1");

            if (!$query) {
                die('Error: ' . mysqli_error($conn));
            }

            if (mysqli_num_rows($query) > 0) {

                $this->logger->debug('result is ' . var_export($query, true), self::MASK_LOG_TRUE);
                $returnValueText = $this->constDisplayMessages['RegistrationUserNameExists'];
                $registrationResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($returnValueText)
                    ->setErrorMessage('')
                    ->setResponseCode($this->constDisplayMessages['RegistrationNameAlreadyExists']);
            } else {

                $irem_usr_created = date('Y-m-d h:m:s');
                $irem_usr_lastupdated = date('Y-m-d h:m:s');

                mysqli_query($conn, "INSERT INTO irem_users (irem_usr_userid, irem_usr_username, irem_usr_password,irem_usr_userrole,irem_usr_created,irem_usr_lastupdated)
                VALUES ('$irem_usr_userid','$irem_usr_username','$irem_usr_password','$irem_usr_userrole','$irem_usr_created','$irem_usr_lastupdated')");

                $returnValueText = 'User registration is successful';
                $registrationResponse->setStatus($this->constStatusFlags['Success'])
                    ->setDisplayMessage($returnValueText)
                    ->setErrorMessage('')
                    ->setResponseCode($this->constResponseCode['RegistrationSuccess']);
            }

            $this->logger->debug('display text >>> ' . $returnValueText, self::MASK_LOG_TRUE);
            mysqli_close($conn);
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
            $registrationResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($returnValueText)
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode['RegistrationFailure']);
        } finally {
            $this->disConnect();
            $this->logger->debug('UserTableTransactions >>> out of addUser method ', self::MASK_LOG_TRUE);

            return $registrationResponse;
        }
    }
}
