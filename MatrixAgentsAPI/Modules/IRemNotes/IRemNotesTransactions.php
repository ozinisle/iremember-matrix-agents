<?php namespace MatrixAgentsAPI\Modules\IRemNotes;

use MatrixAgentsAPI\DatabaseModel\DBConstants;
use MatrixAgentsAPI\Utilities\EventLogger;
use MatrixAgentsAPI\Security\Encryption\OpenSSLEncryption;
use MatrixAgentsAPI\Modules\Login\Model\GenericTableTransactionResponseModel;

class IRemNotesTransactions
{
    private $logger;
    private $opensslEncryption;
    private $iRememberProperties;
    private $login_pay_load = null;

    private $constStatusFlags = DBConstants::StatusFlags;
    private $constResponseCode = DBConstants::ResponseCode;
    private $constDisplayMessages = DBConstants::DisplayMessages;

    const MASK_LOG_TRUE = false;

    public function __construct()
    {
        $this->logger = new EventLogger();
        $this->opensslEncryption = new OpenSSLEncryption();
    }

    public function getNotesList()
    {
        //create the response object
        $getNotesListResponse = new GenericTableTransactionResponseModel();
        try {

            $this->logger->debug('IRemNotesTransactions >>> into method getNotesList >>> ', self::MASK_LOG_TRUE);

            $getNotesListResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setResponseCode($this->constResponseCode["RandomError"])
                ->setMatchingRecords(null);

            //Start :: gather data relevant to the login attempt
            //initialize session
            $this->initializeSession();

            //compiler gets here only if the  request is from a valid origin
            //get the request body to extract the parameters posted to the request
            $request_body = $this->getRequestBody();


            $decryptedGetNotesListRequest = $this->opensslEncryption->CryptoJSAesDecrypt($_SESSION['request_decryption_pass_phrase'], $request_body);

            $this->logger->debug('IRemNotesTransactions >>> about to create IRemNotesTableTransactions', self::MASK_LOG_TRUE);
            $notesTbl = new IRemNotesTableTransactions();

            $this->logger->debug('IRemNotesTransactions >>> about to call setIRememberProperties', self::MASK_LOG_TRUE);
            $notesTbl->setIRememberProperties($this->iRememberProperties);

            $this->logger->debug('IRemNotesTransactions >>> about to call $notesTbl->getNotesList', self::MASK_LOG_TRUE);
            $getNotesListResponse = $notesTbl->getNotesList($decryptedGetNotesListRequest);
        } catch (Exception $e) {

            $getNotesListResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode['RegistrationFailure'])
                ->setMatchingRecords(null);

            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug(' executing finally block in IRemNotesTransactions >>> getNotesList() method', self::MASK_LOG_TRUE);

            return $this->getEncryptedResponse($getNotesListResponse->getJsonString());
        }
    }

    public function getNotesCategoryList()
    {
        //create the response object
        $getNotesCategoryListResponse = new GenericTableTransactionResponseModel();
        try {

            $this->logger->debug('IRemNotesTransactions >>> into method getNotesCategoryList >>> ', self::MASK_LOG_TRUE);

            $getNotesCategoryListResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setResponseCode($this->constResponseCode["RandomError"])
                ->setMatchingRecords(null);

            //Start :: gather data relevant to the login attempt
            //initialize session
            $this->initializeSession();

            //compiler gets here only if the  request is from a valid origin
            //get the request body to extract the parameters posted to the request
            $request_body = $this->getRequestBody();

            $decryptedGetNotesCategoryListRequest = $this->opensslEncryption->CryptoJSAesDecrypt($_SESSION['request_decryption_pass_phrase'], $request_body);

            $this->logger->debug('IRemNotesTransactions >>> about to create IRemNotesTableTransactions', self::MASK_LOG_TRUE);
            $notesTbl = new IRemNotesTableTransactions();

            $this->logger->debug('IRemNotesTransactions >>> about to call setIRememberProperties', self::MASK_LOG_TRUE);
            $notesTbl->setIRememberProperties($this->iRememberProperties);

            $this->logger->debug('IRemNotesTransactions >>> about to call $notesTbl->getNotesCategoryList', self::MASK_LOG_TRUE);
            $getNotesCategoryListResponse = $notesTbl->getNotesCategoryList($decryptedGetNotesCategoryListRequest);
        } catch (Exception $e) {

            $getNotesCategoryListResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode['RegistrationFailure'])
                ->setMatchingRecords(null);

            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug(' executing finally block in IRemNotesTransactions >>> getNotesCategoryList() method', self::MASK_LOG_TRUE);

            return $this->getEncryptedResponse($getNotesCategoryListResponse->getJsonString());
        }
    }


    public function hardDeleteNotes()
    {
        return $this->softDeleteNotes(true);
    }

    public function softDeleteNotes($hardDeleteFlag)
    {
        //create the response object
        $softDeleteNotesResponse = new GenericTableTransactionResponseModel();
        try {

            $this->logger->debug('IRemNotesTransactions >>> into method softDeleteNotes >>> ', self::MASK_LOG_TRUE);

            $softDeleteNotesResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setResponseCode($this->constResponseCode["RandomError"])
                ->setMatchingRecords(null);

            //Start :: gather data relevant to the login attempt
            //initialize session
            $this->initializeSession();

            //compiler gets here only if the  request is from a valid origin
            //get the request body to extract the parameters posted to the request
            $request_body = $this->getRequestBody();

            $decryptedSoftDeleteNotesRequest = $this->opensslEncryption->CryptoJSAesDecrypt($_SESSION['request_decryption_pass_phrase'], $request_body);

            $this->logger->debug('IRemNotesTransactions >>> about to create IRemNotesTableTransactions', self::MASK_LOG_TRUE);
            $notesTbl = new IRemNotesTableTransactions();

            $this->logger->debug('IRemNotesTransactions >>> about to call setIRememberProperties', self::MASK_LOG_TRUE);
            $notesTbl->setIRememberProperties($this->iRememberProperties);

            $this->logger->debug('IRemNotesTransactions >>> about to call $notesTbl->softDeleteNotes', self::MASK_LOG_TRUE);
            $softDeleteNotesResponse = $notesTbl->softDeleteNotes($decryptedSoftDeleteNotesRequest, $hardDeleteFlag);
        } catch (Exception $e) {

            $softDeleteNotesResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode['RegistrationFailure'])
                ->setMatchingRecords(null);

            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug(' executing finally block in IRemNotesTransactions >>> softDeleteNotes() method', self::MASK_LOG_TRUE);

            $this->logger->debug('IRemNotesTransactions >>> softDeleteNotes >>> response is >>> ' . var_export($softDeleteNotesResponse, true), self::MASK_LOG_TRUE);

            return $this->getEncryptedResponse($softDeleteNotesResponse->getJsonString());
        }
    }

    public function updateNote(): string
    {

        //create the response object
        $updateResponse = new GenericTableTransactionResponseModel();

        try {

            $this->logger->debug('IRemNotesTransactions >>> into method updateNote >>> ', self::MASK_LOG_TRUE);

            $updateResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setResponseCode($this->constResponseCode["RandomError"])
                ->setMatchingRecords(null);

            //Start :: gather data relevant to the login attempt
            //initialize session
            $this->initializeSession();

            //compiler gets here only if the  request is from a valid origin
            //get the request body to extract the parameters posted to the request
            $request_body = $this->getRequestBody();

            $decryptedUpdateNoteRequest = $this->opensslEncryption->CryptoJSAesDecrypt($_SESSION['request_decryption_pass_phrase'], $request_body);

            if (empty($decryptedUpdateNoteRequest)) {
                $this->logger
                    ->errorEvent()
                    ->log($this->constDisplayMessages['InvalidRequest']);
                return $updateResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['InvalidRequest'])
                    ->setErrorMessage($this->constDisplayMessages['InvalidRequest'])
                    ->setResponseCode($this->constResponseCode['InvalidRequest'])
                    ->setMatchingRecords(null);
            }

            $this->logger->debug('IRemNotesTransactions >>> about to create IRemNotesTableTransactions', self::MASK_LOG_TRUE);
            $notesTbl = new IRemNotesTableTransactions();

            $this->logger->debug('IRemNotesTransactions >>> about to call setIRememberProperties', self::MASK_LOG_TRUE);
            $notesTbl->setIRememberProperties($this->iRememberProperties);

            $this->logger->debug('IRemNotesTransactions >>> about to call $notesTbl->updateNote', self::MASK_LOG_TRUE);
            $updateResponse = $notesTbl->updateNote($decryptedUpdateNoteRequest);
        } catch (Exception $e) {

            $updateResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode['RegistrationFailure'])
                ->setMatchingRecords(null);

            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug(' executing finally block in IRemNotesTransactions >>> updateNote() method', self::MASK_LOG_TRUE);

            return $this->getEncryptedResponse($updateResponse->getJsonString());
        }
    }

    private function getEncryptedResponse($response): string
    {
        try {
            return $this->opensslEncryption->CryptoJSAesEncrypt($_SESSION['response_encryption_pass_phrase'], $response);
        } catch (Exception $e) {

            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        }

        return null;
    }

    private function initializeSession()
    {

        try {
            $this->logger->debug(' into method IRemNotesTransactions::initializeSession()', self::MASK_LOG_TRUE);

            //get properties as a section segrated array
            $PROCESS_SECTIONS = true;

            //get app properties
            $this->iRememberProperties = parse_ini_file(realpath('../../i-remember-properties.ini'), $PROCESS_SECTIONS);

            $matrixAppFlags = $this->iRememberProperties['matrix-app-flags'];
            $_SESSION['debug_mode'] = $matrixAppFlags['debug_mode'];

            $matrixCommChannelPassPhrase = $this->iRememberProperties['matrix-comm-channel-pass-phrase'];
            $_SESSION['request_decryption_pass_phrase'] = $matrixCommChannelPassPhrase['request_decryption_pass_phrase'];
            $_SESSION['response_encryption_pass_phrase'] = $matrixCommChannelPassPhrase['response_encryption_pass_phrase'];

            $this->logger->debug(' completed method IRemNotesTransactions::initializeSession()', self::MASK_LOG_TRUE);
        } catch (Exception $e) {

            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        }
    }


    private function getRequestBody()
    {
        try {
            //compiler gets here only if the  request is from a valid origin
            //get the request body to extract the parameters posted to the request
            if ($this->login_pay_load === null) {
                $this->login_pay_load = file_get_contents('php://input');
            }
            $this->logger->debug('IRemNotesTransactions >>>  getRequestBody >>> login_pay_load >>> ' . $this->login_pay_load, self::MASK_LOG_TRUE);
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            return $this->login_pay_load;
        }
    }

    private function  getRequestHeaders()
    {
        $headers = array();
        try {

            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) <> 'HTTP_') {
                    continue;
                }
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }

            $this->logger->debug(' $headers >>>' . var_export($headers, true), self::MASK_LOG_TRUE);
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            return $headers;
        }
    }
}
