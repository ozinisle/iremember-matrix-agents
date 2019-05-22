<?php namespace MatrixAgentsAPI\Modules\IRemNotes;

use MatrixAgentsAPI\Utilities\EventLogger;
use MatrixAgentsAPI\DatabaseModel\DBConstants;
use MatrixAgentsAPI\Security\JWT\Token;

use MatrixAgentsAPI\Modules\IRemNotes\Model\IRemNoteItem;

use MatrixAgentsAPI\Modules\Login\Model\GenericTableTransactionResponseModel;
use MatrixAgentsAPI\Modules\IRemNotes\Model\IRemNoteItemCategory;
use MatrixAgentsAPI\Modules\IRemNotes\Model\IRemGetNotesList;

class IRemNotesTableTransactions
{

    private $logger;
    private $iRememberProperties = null;
    private $userName;
    private $password;
    private $serverName;
    private $dbName;

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

    public function getNotesList($notesListRequestData)
    {
        $getNotesListResponse = null;
        $methodExectionComplete = false;
        $conn = null;
        $loggedInUserId = null;
        $getNotesListResponse = new GenericTableTransactionResponseModel();
        $isTrashDataRequest = false;

        try {
            $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> into method ',  self::MASK_LOG_TRUE);

            $getNotesListResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setResponseCode($this->constResponseCode["NotesListFailure"])
                ->setMatchingRecords(null);

            $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> initalized response ',  self::MASK_LOG_TRUE);
            if (empty($notesListRequestData)) {
                $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> isTrashDataRequest  >>> ' . $isTrashDataRequest,  self::MASK_LOG_TRUE);
            } else {
                // code to retrieve notes list based on search queries and advanced search queries
                $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> request is not empty >>> ' . var_export($notesListRequestData, true),  self::MASK_LOG_TRUE);

                $_requestData = (array)$notesListRequestData;
                $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> $notesListRequestData[\' softDeletedDataOnly \']  >>> ' . $_requestData['softDeletedDataOnly'],  self::MASK_LOG_TRUE);
                if (isset($_requestData['softDeletedDataOnly'])) {
                    if ($_requestData['softDeletedDataOnly'] === true) {
                        $isTrashDataRequest = true;
                    }
                }
                $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> isTrashDataRequest  >>> ' . $isTrashDataRequest,  self::MASK_LOG_TRUE);
            }


            $loggedInUserId = $this->getLoggedInUserName();
            $conn = $this->getDBConnection();

            $_notesListData = $this->getNotesListDataInResponseFormat($isTrashDataRequest, $conn, $loggedInUserId);
            $_iremGetNotesList = new IRemGetNotesList();
            $_iremGetNotesList->setNotesList($_notesListData);

            $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> $_apiResponse  >>> ' . var_export($_iremGetNotesList, true), self::MASK_LOG_TRUE);

            $methodExectionComplete = true;
            if (sizeof($_notesListData) === 0) {
                $methodExectionComplete = true;
                return $getNotesListResponse->setStatus($this->constStatusFlags['Success'])
                    ->setDisplayMessage('')
                    ->setErrorMessage('')
                    ->setResponseCode($this->constResponseCode["NotesListSuccess"])
                    ->setMatchingRecords([]);
            } else {
                return $getNotesListResponse->setStatus($this->constStatusFlags['Success'])
                    ->setDisplayMessage('')
                    ->setErrorMessage('')
                    ->setResponseCode($this->constResponseCode["NotesListSuccess"])
                    ->setMatchingRecords($_iremGetNotesList->getJson());
            }

            $methodExectionComplete = true;
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('IRemNotesTableTransactions >>> getNotesList >>> Caught exception: ' . var_export($e, true) . "\n");
            $getNotesListResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode["CodeError"]);
        } finally {
            $this->logger->debug('IRemNotesTableTransactions >>> out of getNotesList method ', self::MASK_LOG_TRUE);
            if ($methodExectionComplete) {
                return $getNotesListResponse;
            } else {
                $getNotesListResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                    ->setErrorMessage($this->constDisplayMessages['SilentFailure'])
                    ->setResponseCode($this->constResponseCode["SilentFailure"]);
                return  $getNotesListResponse;
            }
        }
    }

    public function getNotesCategoryList($notesListRequestData)
    {
        $getNotesCategoryListResponse = null;
        $methodExectionComplete = false;
        $conn = null;
        $loggedInUserId = null;

        $getNotesCategoryListResponse = new GenericTableTransactionResponseModel();
        $categoryDataResponse = array("categoryTagData" => array());
        try {
            $this->logger->debug('IRemNotesTableTransactions >>> getNotesCategoryList >>> into method ',  self::MASK_LOG_TRUE);
            $this->logger->debug('IRemNotesTableTransactions >>> getNotesCategoryList >>> test cache ',  self::MASK_LOG_TRUE);

            $getNotesCategoryListResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setResponseCode($this->constResponseCode["NotesCategoryListFailure"])
                ->setMatchingRecords(null);

            $this->logger->debug('IRemNotesTableTransactions >>> getNotesCategoryList >>> initalized response ',  self::MASK_LOG_TRUE);
            if (empty($notesListRequestData)) {
                $loggedInUserId = $this->getLoggedInUserName();
                $conn = $this->getDBConnection();

                // Check connection
                if (mysqli_connect_errno()) {
                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                }

                $_categoryDataQueryResult = mysqli_query($conn, "SELECT * FROM irem_notes_categories WHERE irem_nc_userid='$loggedInUserId'");

                $this->logger->debug('IRemNotesTableTransactions >>> getNotesCategoryList >>> looping through result ',  self::MASK_LOG_TRUE);
                $_groupFilter = array();
                while ($__categoryRow = mysqli_fetch_array($_categoryDataQueryResult)) {

                    if (!isset($_groupFilter[$__categoryRow['irem_nc_groupname']])) {
                        $_groupFilter[$__categoryRow['irem_nc_groupname']] = array();
                    }
                    array_push(
                        $_groupFilter[$__categoryRow['irem_nc_groupname']],
                        array(
                            "categoryName" => $__categoryRow['irem_nc_categoryname'],
                            "categoryId" => $__categoryRow['irem_nc_categoryid'],
                            "markedForDeletion" => $__categoryRow['irem_nc_ismarkedfordeletion'],
                            "iconName" => $__categoryRow['irem_nc_iconname'],
                            "categoryDescription" => $__categoryRow['irem_nc_categorydescription']
                        )
                    );
                }

                foreach ($_groupFilter as $key => $value) {
                    array_push($categoryDataResponse["categoryTagData"], array(
                        "categoryGroupHeader" => $key,
                        "categoryTags" => $value
                    ));
                }
            } else {
                // code to retrieve notes list based on search queries and advanced search queries
                $this->logger->debug('IRemNotesTableTransactions >>> getNotesCategoryList >>> request is not empty >>> ' .
                    var_export($notesListRequestData, true),  self::MASK_LOG_TRUE);
            }

            $this->logger->debug('IRemNotesTableTransactions >>> getNotesCategoryList >>> response will be >>> ' . var_export($categoryDataResponse, true),  self::MASK_LOG_TRUE);

            $getNotesCategoryListResponse->setStatus($this->constStatusFlags['Success'])
                ->setDisplayMessage('')
                ->setErrorMessage('')
                ->setResponseCode($this->constResponseCode["NotesCategoryListSuccess"])
                ->setMatchingRecords($categoryDataResponse);
            //->setMatchingRecords(json_decode(json_encode($categoryDataResponse)));

            $methodExectionComplete = true;
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('IRemNotesTableTransactions >>> getNotesCategoryList >>> Caught exception: ' . var_export($e, true) . "\n");
            $getNotesCategoryListResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode["CodeError"]);
        } finally {
            $this->logger->debug('IRemNotesTableTransactions >>> out of getNotesCategoryList method ', self::MASK_LOG_TRUE);
            if ($methodExectionComplete) {
                return $getNotesCategoryListResponse;
            } else {
                $getNotesCategoryListResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                    ->setErrorMessage($this->constDisplayMessages['SilentFailure'])
                    ->setResponseCode($this->constResponseCode["SilentFailure"]);
                return  $getNotesCategoryListResponse;
            }
        }
    }

    public function softDeleteNotes($softDeleteNotesRequestData, $hardDeleteFlag)
    {
        $softDeleteNotesResponse = null;
        $methodExectionComplete = false;
        $conn = null;
        $loggedInUserId = null;

        $softDeleteNotesResponse = new GenericTableTransactionResponseModel();
        try {
            $this->logger->debug('IRemNotesTableTransactions >>> softDeleteNotes >>> into method ',  self::MASK_LOG_TRUE);

            $softDeleteNotesResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setResponseCode($hardDeleteFlag ? $this->constResponseCode["HardDeleteNotesFailure"] : $this->constResponseCode["SoftDeleteNotesFailure"])
                ->setMatchingRecords(null);

            $this->logger->debug('IRemNotesTableTransactions >>> softDeleteNotes >>> initalized response ',  self::MASK_LOG_TRUE);

            $loggedInUserId = $this->getLoggedInUserName();
            $conn = $this->getDBConnection($hardDeleteFlag);

            if (empty($softDeleteNotesRequestData)) {

                $this->logger->debug('IRemNotesTableTransactions >>> softDeleteNotes >>> request is empty >>> ',  self::MASK_LOG_TRUE);
                $softDeleteNotesResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['InvalidRequest'])
                    ->setErrorMessage($this->constDisplayMessages['InvalidRequest'])
                    ->setResponseCode($hardDeleteFlag ? $this->constResponseCode["HardDeleteNotesFailure"] : $this->constResponseCode["SoftDeleteNotesFailure"])
                    ->setMatchingRecords([]);
            } else {
                // code to retrieve notes list based on search queries and advanced search queries
                $this->logger->debug('IRemNotesTableTransactions >>> softDeleteNotes >>> request is not empty >>> ' .
                    var_export($softDeleteNotesRequestData, true),  self::MASK_LOG_TRUE);

                if ($hardDeleteFlag) {
                    for ($noteIdItr = 0; $noteIdItr < sizeof($softDeleteNotesRequestData); $noteIdItr++) {
                        mysqli_query($conn, "DELETE from irem_notes WHERE irem_notes_id='$softDeleteNotesRequestData[$noteIdItr]' AND irem_notes_userid='$loggedInUserId'");
                    }
                } else {
                    for ($noteIdItr = 0; $noteIdItr < sizeof($softDeleteNotesRequestData); $noteIdItr++) {
                        mysqli_query($conn, "UPDATE irem_notes SET irem_notes_ismarkedfordeletion='true' WHERE irem_notes_id='$softDeleteNotesRequestData[$noteIdItr]' AND irem_notes_userid='$loggedInUserId'");
                    }
                }


                $_notesListData = $this->getNotesListDataInResponseFormat($hardDeleteFlag, $conn, $loggedInUserId);
                $_iremGetNotesList = new IRemGetNotesList();
                $_iremGetNotesList->setNotesList($_notesListData);

                $softDeleteNotesResponse->setStatus($this->constStatusFlags['Success'])
                    ->setDisplayMessage('')
                    ->setErrorMessage('')
                    ->setResponseCode($hardDeleteFlag ? $this->constResponseCode["HardDeleteNotesSuccess"] : $this->constResponseCode["SoftDeleteNotesSuccess"])
                    ->setMatchingRecords($_iremGetNotesList->getJson());
            }

            $this->logger->debug('IRemNotesTableTransactions >>> softDeleteNotes >>> response will be >>> ' . var_export($softDeleteNotesResponse, true),  self::MASK_LOG_TRUE);

            $methodExectionComplete = true;
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('IRemNotesTableTransactions >>> softDeleteNotes >>> Caught exception: ' . var_export($e, true) . "\n");
            $softDeleteNotesResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode["CodeError"]);
        } finally {
            $this->logger->debug('IRemNotesTableTransactions >>> out of softDeleteNotes method ', self::MASK_LOG_TRUE);
            if ($methodExectionComplete) {
                $this->logger->debug('IRemNotesTableTransactions >>> softDeleteNotes >>> response is >>> ' . var_export($softDeleteNotesResponse, true), self::MASK_LOG_TRUE);
                return $softDeleteNotesResponse;
            } else {
                $this->logger->debug('IRemNotesTableTransactions >>> softDeleteNotes >>> response is >>> ' . var_export($softDeleteNotesResponse, true), self::MASK_LOG_TRUE);
                $softDeleteNotesResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                    ->setErrorMessage($this->constDisplayMessages['SilentFailure'])
                    ->setResponseCode($this->constResponseCode["SilentFailure"]);
                return  $softDeleteNotesResponse;
            }
        }
    }

    public function updateNote($noteData)
    {
        $this->logger->debug('IRemNotesTableTransactions >>> into method updateNote ',  self::MASK_LOG_TRUE);
        $methodExectionComplete = false;

        $updateNoteResponse = new GenericTableTransactionResponseModel();

        $loggedInUserId = null;

        $conn = null;

        $isNewRecord = false;
        $isFormDataValid = false;

        $formData_noteTitle = null;
        $formData_categoryTags =  null;
        $formData_noteDescription = null;
        $formData_noteId = null;

        $iremNoteItem = new IRemNoteItem();

        $irem_notes_id = null;
        $irem_notes_title = null;
        $irem_notes_tags = null;
        $irem_notes_description = null;
        $irem_notes_userid = null;
        $irem_notes_created = null;
        $irem_notes_lastupdated = null;

        $markedForDeletion = false;
        $noteData_Catagories = [];
        $iremNoteItemCategoryGroup = [];

        try {
            $updateNoteResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setResponseCode($this->constResponseCode["RandomError"]);

            $loggedInUserId = $this->getLoggedInUserName();
            $conn = $this->getDBConnection();

            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            // new row details
            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> request data is >>> ' . var_export($noteData, true),  self::MASK_LOG_TRUE);

            $noteData = (array)$noteData;

            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $noteData[noteTitle] >>> ' . $noteData['noteTitle'], self::MASK_LOG_TRUE);

            if (isset($noteData['noteId'])) {
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> noteid is set', self::MASK_LOG_TRUE);
                if ($noteData['noteId'] === null) {
                    $isNewRecord = true;
                } else {
                    $bufferNoteId = mysqli_real_escape_string($conn, $noteData['noteId']);
                    if (!is_string($bufferNoteId)) {
                        $isNewRecord = true;
                    } else if (strlen($bufferNoteId) < 13) {
                        $isNewRecord = true;
                    } else {
                        $formData_noteId = $noteData['noteId'];
                    }
                }
            } else {
                $isNewRecord = true;
            }

            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 1', self::MASK_LOG_TRUE);

            if (isset($noteData['categoryTags'])) {
                for ($categoryTagEntityItr = 0; $categoryTagEntityItr < sizeof($noteData['categoryTags']); $categoryTagEntityItr++) {
                    $categoryTagEntity = $noteData['categoryTags'][$categoryTagEntityItr];

                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> 1.categoryTagItem >>> ' . var_export($categoryTagEntity, true), self::MASK_LOG_TRUE);
                    $categoryTagEntityArr = (array)$categoryTagEntity;

                    if (isset($categoryTagEntityArr['markedForDeletion'])) {
                        $markedForDeletion = mysqli_real_escape_string($conn, $categoryTagEntityArr['markedForDeletion']);
                        $markedForDeletion .= '';
                        if (strtoupper($markedForDeletion) === "true") {
                            continue;
                        }
                    }
                    if (isset($categoryTagEntityArr['categoryId'])) {
                        $categoryId = mysqli_real_escape_string($conn, $categoryTagEntityArr['categoryId']);
                        $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $categoryId >>> ' . $categoryId, self::MASK_LOG_TRUE);
                        // if no category id is found in the request, then replace the same with a new category id
                        if ($categoryId === '' || $categoryId === null) {
                            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $categoryId not found ', self::MASK_LOG_TRUE);
                            $categoryId = uniqid();
                            $categoryTagEntityArr['categoryId'] = $categoryId;
                            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $categoryId generated ', self::MASK_LOG_TRUE);
                        }
                    } else {
                        $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $categoryId not found (else)', self::MASK_LOG_TRUE);
                        $categoryId = uniqid();
                        $categoryTagEntityArr['categoryId'] = $categoryId;
                        $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $categoryId generated (else)', self::MASK_LOG_TRUE);
                    }

                    array_push($noteData_Catagories, $categoryTagEntityArr);
                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $categoryId >>> ' . $categoryId, self::MASK_LOG_TRUE);
                    $formData_categoryTags .= "$categoryId, ";
                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $formData_categoryTags>>> ' . $formData_categoryTags, self::MASK_LOG_TRUE);
                }
                $formData_categoryTags = substr($formData_categoryTags, 0, strlen($formData_categoryTags) - 2);
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $formData_categoryTags >>> ' . $formData_categoryTags, self::MASK_LOG_TRUE);
            } else {
                $formData_categoryTags = '';
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> no category tags found', self::MASK_LOG_TRUE);
            }
            $formData_noteTitle = $noteData['noteTitle'] ?: null;
            $formData_noteDescription = $noteData['noteDescription'] ?: null;

            if (is_string($loggedInUserId) && is_string($formData_noteTitle)  && is_string($formData_noteDescription)) {
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 2.0', self::MASK_LOG_TRUE);
                $formData_noteTitle = trim($formData_noteTitle, " ");
                $formData_noteDescription = trim($formData_noteDescription, " ");
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 2.0.1', self::MASK_LOG_TRUE);

                if (strlen($formData_noteTitle) >= 1 && strlen($formData_categoryTags) >= 1 && strlen($formData_noteDescription) >= 1) {
                    $isFormDataValid = true;
                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 2.1', self::MASK_LOG_TRUE);
                } else {
                    $updateNoteResponse->setStatus($this->constStatusFlags['Failure'])
                        ->setDisplayMessage($this->constDisplayMessages['InvalidRequest'])
                        ->setErrorMessage($this->constDisplayMessages['InvalidRequest'])
                        ->setResponseCode($this->constResponseCode["InvalidRequest"]);
                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> Invalid Request', self::MASK_LOG_TRUE);
                }
            } else {
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 2.3', self::MASK_LOG_TRUE);
            }

            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 3', self::MASK_LOG_TRUE);
            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>>  $isFormDataValid >>> ' .
                json_encode($isFormDataValid),  self::MASK_LOG_TRUE);

            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>>  check 3.1',  self::MASK_LOG_TRUE);

            if ($isFormDataValid) {
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>>  check 3.5',  self::MASK_LOG_TRUE);
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>>  $isNewRecord >>> ' .
                    $isNewRecord ? 'true' : 'false',  self::MASK_LOG_TRUE);
                if ($isNewRecord === true) {
                    $irem_notes_id = uniqid();
                } else {
                    $irem_notes_id = $formData_noteId;
                }
                $irem_notes_title =  $formData_noteTitle;
                $irem_notes_tags = $formData_categoryTags;
                $irem_notes_description =  $formData_noteDescription;
                $irem_notes_userid = $loggedInUserId;
                $irem_notes_created = date('Y-m-d h:m:s');
                $irem_notes_lastupdated = date('Y-m-d h:m:s');

                $markedForDeletion = false;
                if (isset($noteData['markedForDeletion'])) {
                    if ($noteData['markedForDeletion'] === "true" || $noteData['markedForDeletion'] === "false") {
                        $markedForDeletion = $noteData['markedForDeletion'] == "true" ? true : false;
                    } else {
                        $markedForDeletion = false;
                    }
                }

                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> query is >>> ' . "INSERT INTO irem_notes (irem_notes_id,irem_notes_title,irem_notes_tags,irem_notes_description,irem_notes_userid,irem_notes_created,irem_notes_lastupdated,irem_notes_ismarkedfordeletion) VALUES ('$irem_notes_id','$irem_notes_title','$irem_notes_tags','$irem_notes_description','$irem_notes_userid','$irem_notes_created','$irem_notes_lastupdated','$markedForDeletion')",  self::MASK_LOG_TRUE);

                if ($isNewRecord === true) {
                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> creating new record note >>> ' . $irem_notes_id,  self::MASK_LOG_TRUE);
                    mysqli_query($conn, "INSERT INTO irem_notes (irem_notes_id,irem_notes_title,irem_notes_tags,irem_notes_description,irem_notes_userid,irem_notes_created,irem_notes_lastupdated,irem_notes_ismarkedfordeletion) VALUES ('$irem_notes_id','$irem_notes_title','$irem_notes_tags','$irem_notes_description','$irem_notes_userid','$irem_notes_created','$irem_notes_lastupdated','$markedForDeletion')");
                } else {
                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> updating existing note with id >>> ' . $irem_notes_id,  self::MASK_LOG_TRUE);
                    mysqli_query($conn, "UPDATE irem_notes SET irem_notes_title='$irem_notes_title',irem_notes_tags='$irem_notes_tags',irem_notes_description='$irem_notes_description',irem_notes_lastupdated='$irem_notes_lastupdated',irem_notes_ismarkedfordeletion='$markedForDeletion' WHERE irem_notes_id='$irem_notes_id' AND irem_notes_userid='$loggedInUserId'");
                }


                $declaredIds = explode(",", $irem_notes_tags);
                for ($categoryTagEntityItr = 0; $categoryTagEntityItr < sizeof($noteData_Catagories); $categoryTagEntityItr++) {
                    $categoryTagEntityArr = $noteData_Catagories[$categoryTagEntityItr];

                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> 2.categoryTagItem >>> ' . var_export($categoryTagEntity, true), self::MASK_LOG_TRUE);

                    $categoryName = '';
                    if (isset($categoryTagEntityArr['categoryName'])) {
                        $categoryName =  mysqli_real_escape_string($conn, $categoryTagEntityArr['categoryName']);
                    }

                    $iconName = '';
                    if (isset($categoryTagEntityArr['iconName'])) {
                        $iconName = mysqli_real_escape_string($conn, $categoryTagEntityArr['iconName']);
                    }

                    $categoryId = $declaredIds[$categoryTagEntityItr];
                    // $categoryId = "";
                    $new_categoryFlag = false;
                    if (isset($categoryTagEntityArr['categoryId'])) {
                        //$categoryId = $categoryTagEntityArr['categoryId'];
                        if ($categoryId !== '') {
                            //$categoryId = uniqid();
                            $new_categoryFlag = true;
                        } else {
                            $new_categoryFlag = false;
                        }
                    } else {
                        // $categoryId = uniqid();
                        $new_categoryFlag = true;
                    }

                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $new_categoryFlag >>> ' . $new_categoryFlag,  self::MASK_LOG_TRUE);
                    if ($new_categoryFlag) {
                        $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> updating categoryId to table>>> ' . $categoryId, self::MASK_LOG_TRUE);

                        $existingCategoryRecordIfAny = mysqli_query($conn, "SELECT * FROM irem_notes_categories WHERE irem_nc_categoryname='$categoryName' AND irem_nc_userid='$loggedInUserId' LIMIT 1");
                        $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> number of rows >>> ' . mysqli_num_rows($existingCategoryRecordIfAny), self::MASK_LOG_TRUE);
                        if (mysqli_num_rows($existingCategoryRecordIfAny) == 0) {
                            $irem_nc_created = date('Y-m-d h:m:s');
                            $irem_nc_lastupdated = date('Y-m-d h:m:s');
                            $irem_nc_groupname = 'Custom';

                            // $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> new category save triggering command >>> ' . PHP_EOL . "INSERT INTO irem_notes_categories (irem_nc_categoryid,irem_nc_categoryname,irem_nc_ismarkedfordeletion,irem_nc_iconname,irem_nc_created,irem_nc_lastupdated) VALUES ('$categoryId','$categoryName','false','$iconName','$irem_nc_created','$irem_nc_lastupdated')", self::MASK_LOG_TRUE);

                            mysqli_query($conn, "INSERT INTO irem_notes_categories (irem_nc_categoryid,irem_nc_categoryname,irem_nc_ismarkedfordeletion,irem_nc_iconname,irem_nc_created,irem_nc_lastupdated,irem_nc_userid,irem_nc_groupname) VALUES ('$categoryId','$categoryName','false','$iconName','$irem_nc_created','$irem_nc_lastupdated','$loggedInUserId','$irem_nc_groupname')");
                        } else {
                            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> found a match so wont create new category id >>> ' . $categoryId, self::MASK_LOG_TRUE);
                            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $existingCategoryRecordIfAny >>> ' . var_export($existingCategoryRecordIfAny, true), self::MASK_LOG_TRUE);
                            $firstrow = mysqli_fetch_array($existingCategoryRecordIfAny); //mysql_fetch_assoc($existingCategoryRecordIfAny);
                            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $row >>> ' . var_export($firstrow, true), self::MASK_LOG_TRUE);
                            $categoryId = $firstrow['irem_nc_categoryid'];
                        }
                    }

                    $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $categoryId >>> ' . $categoryId,  self::MASK_LOG_TRUE);


                    $iremNoteItemCategory  = new IRemNoteItemCategory();
                    $iremNoteItemCategory->setCategoryName($categoryName);
                    $iremNoteItemCategory->setCategoryId($categoryId);
                    if (isset($categoryTagEntityArr['markedForDeletion'])) {
                        $iremNoteItemCategory->setMarkedForDeletion($categoryTagEntityArr['markedForDeletion']);
                    } else {
                        $iremNoteItemCategory->setMarkedForDeletion(false);
                    }

                    array_push($iremNoteItemCategoryGroup, $iremNoteItemCategory);
                }

                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $iremNoteItemCategoryGroup >>> ' . var_export($iremNoteItemCategoryGroup, true),  self::MASK_LOG_TRUE);

                mysqli_close($conn);

                $updateNoteResponse->setStatus($this->constStatusFlags['Success'])
                    ->setDisplayMessage('Note has been saved')
                    ->setErrorMessage('')
                    ->setResponseCode($this->constResponseCode["NewNoteCreatedSuccess"]);

                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> about to construct response if success response',  self::MASK_LOG_TRUE);
            } else {
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> formDataInvalid - failure response',  self::MASK_LOG_TRUE);
                $updateNoteResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['InvalidInputData'])
                    ->setErrorMessage($this->constDisplayMessages['InvalidInputData'])
                    ->setResponseCode($this->constResponseCode['InvalidInputData']);
            }

            if ($updateNoteResponse->getStatus() === $this->constStatusFlags['Success']) {
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> constructing response',  self::MASK_LOG_TRUE);
                $iremNoteItem->setCategoryTags($iremNoteItemCategoryGroup);
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 3.5.0',  self::MASK_LOG_TRUE);
                $iremNoteItem->setNoteId($irem_notes_id);
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 3.5.1',  self::MASK_LOG_TRUE);
                $iremNoteItem->setNoteTitle($irem_notes_title);
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 3.5.2',  self::MASK_LOG_TRUE);
                $iremNoteItem->setNoteDescription($irem_notes_description);
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 3.5.3',  self::MASK_LOG_TRUE);
                $iremNoteItem->setUserId($irem_notes_userid);
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 3.5.4',  self::MASK_LOG_TRUE);
                $iremNoteItem->setCreated($irem_notes_created);
                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> check 3.5.5',  self::MASK_LOG_TRUE);
                $iremNoteItem->setLastUpdated($irem_notes_lastupdated);
                $iremNoteItem->setMarkedForDeletion($markedForDeletion);

                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $iremNoteItem is ' . var_export($iremNoteItem, true),  self::MASK_LOG_TRUE);

                $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> $iremNoteItem json is ' . var_export($iremNoteItem->getJson(), true),  self::MASK_LOG_TRUE);

                $updateNoteResponse->setMatchingRecords($iremNoteItem->getJson());
            }

            $this->logger->debug('IRemNotesTableTransactions >>> updateNote >>> Completed code execution ',  self::MASK_LOG_TRUE);
            $methodExectionComplete = true;
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('IRemNotesTableTransactions >>> updateNote >>> Caught exception: ' . var_export($e, true) . "\n");
            $updateNoteResponse->setStatus($this->constStatusFlags['Failure'])
                ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                ->setErrorMessage(var_export($e, true))
                ->setResponseCode($this->constResponseCode["CodeError"]);
        } finally {
            $this->logger->debug('IRemNotesTableTransactions >>> out of updateNote method ', self::MASK_LOG_TRUE);
            if ($methodExectionComplete) {
                return $updateNoteResponse;
            } else {
                $updateNoteResponse->setStatus($this->constStatusFlags['Failure'])
                    ->setDisplayMessage($this->constDisplayMessages['TemporaryServiceDownMessage'])
                    ->setErrorMessage($this->constDisplayMessages['SilentFailure'])
                    ->setResponseCode($this->constResponseCode["SilentFailure"]);
                return  $updateNoteResponse;
            }
        }
    }

    /** 
     * Get header Authorization
     * */
    function getAuthorizationHeader()
    {
        $headers = null;
        try {
            $this->logger->debug('IRemNotesTableTransactions >>> into method getAuthorizationHeader ',  self::MASK_LOG_TRUE);
            if (isset($_SERVER['Authorization'])) {
                $headers = trim($_SERVER["Authorization"]);
                $this->logger->debug('IRemNotesTableTransactions >>> $_SERVER[\'Authorization\'] ',  self::MASK_LOG_TRUE);
            } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
                $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
                $this->logger->debug('IRemNotesTableTransactions >>> $_SERVER[\'HTTP_AUTHORIZATION\'] ',  self::MASK_LOG_TRUE);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                //print_r($requestHeaders);
                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }

                $this->logger->debug('IRemNotesTableTransactions >>> function_exists(apache_request_headers) ',  self::MASK_LOG_TRUE);
            }
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('IRemNotesTableTransactions >>> getAuthorizationHeader >>> Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug('IRemNotesTableTransactions >>> out of getAuthorizationHeader method ', self::MASK_LOG_TRUE);
            return $headers;
        }
    }

    /**
     * get access token from header
     * */
    function getBearerToken()
    {
        $bearerToken = null;
        try {
            $this->logger->debug('IRemNotesTableTransactions >>> into method getBearerToken ',  self::MASK_LOG_TRUE);
            $headers = $this->getAuthorizationHeader();
            $this->logger->debug('IRemNotesTableTransactions >>> getBearerToken >>> authorizationHeader is >>> ' . var_export($headers, true),  self::MASK_LOG_TRUE);
            // HEADER: Get the access token from the header
            if (!empty($headers)) {
                if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                    $bearerToken = $matches[1];
                }
            }
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('IRemNotesTableTransactions >>> getBearerToken >>> Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug('IRemNotesTableTransactions >>> out of getBearerToken method ', self::MASK_LOG_TRUE);
            return $bearerToken;
        }
    }

    private function getLoggedInUserName()
    {
        $bearerToken = null;
        $token = null;
        $payloadInToken = null;
        $payloadJSON = null;
        $loggedInUserId = null;

        try {

            $this->logger->debug('UserTableTransactions >>> into of getLoggedInUserName method ', self::MASK_LOG_TRUE);

            $bearerToken = $this->getBearerToken();
            $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> bearer token >>> ' . $bearerToken,  self::MASK_LOG_TRUE);

            $token = new Token();
            $payloadInToken = $token->getPayload($bearerToken);
            $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> payloadInToken >>> ' . $payloadInToken,  self::MASK_LOG_TRUE);

            $payloadJSON = json_decode($payloadInToken, true);
            $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> payloadJSON >>> ' . var_export($payloadJSON, true),  self::MASK_LOG_TRUE);

            if (isset($payloadJSON['user_id'])) {
                $loggedInUserId = $payloadJSON['user_id'];
            }
            $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> userId >>> ' . $loggedInUserId,  self::MASK_LOG_TRUE);
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug('UserTableTransactions >>> out of getLoggedInUserName method ', self::MASK_LOG_TRUE);
            return $loggedInUserId;
        }
    }

    private function getDBConnection($isDeleteCapableUser = false)
    {
        $dbConfig = null;
        $serverName = null;
        $dbName = null;
        $dbUser = null;
        $dbPassword = null;
        try {

            $this->logger->debug('UserTableTransactions >>> into of getDBConnection method ', self::MASK_LOG_TRUE);

            $dbConfig = $this->getIRememberDBProperties();
            $serverName = $dbConfig["iRemember_servername"];
            $dbName = $dbConfig["iRemember_db"];
            $dbUser = $isDeleteCapableUser ? $dbConfig["iRemember_del_only_user"] : $dbConfig["iRemember_nrml_user"];
            $dbPassword = $isDeleteCapableUser ? $dbConfig["iRemember_del_only_password"] : $dbConfig["iRemember_nrml_user_password"];

            $conn = mysqli_connect($serverName, $dbUser, $dbPassword, $dbName);
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug('UserTableTransactions >>> out of getDBConnection method ', self::MASK_LOG_TRUE);
            return $conn;
        }
    }

    private function getIRememberDBProperties()
    {
        $dbConfig = null;
        try {
            $this->logger->debug('UserTableTransactions >>> into getIRememberDBProperties method ', self::MASK_LOG_TRUE);
            $iremProps = $this->getIRememberProperties();
            $dbConfig = $iremProps['database-configuration'];
            $this->serverName = $dbConfig['iRemember_servername'];
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

    private function getNotesListDataInResponseFormat($isTrashDataRequest, $conn, $loggedInUserId)
    {
        $_notesListData = [];

        try {
            $this->logger->debug('IRemNotesTableTransactions >>> into getNotesListDataInResponseFormat method ', self::MASK_LOG_TRUE);
            $_categoryDataQueryResult = mysqli_query($conn, "SELECT * FROM irem_notes_categories WHERE irem_nc_userid='$loggedInUserId'");
            $_categoryData = [];
            while ($__categoryRow = mysqli_fetch_array($_categoryDataQueryResult)) {

                $iremNoteItemCategory  = new IRemNoteItemCategory();
                $iremNoteItemCategory->setCategoryName($__categoryRow['irem_nc_categoryname']);
                $iremNoteItemCategory->setCategoryId($__categoryRow['irem_nc_categoryid']);

                // $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> category record >>> ' . var_export($__categoryRow, true), self::MASK_LOG_TRUE);
                $_categoryData[$__categoryRow['irem_nc_categoryid']] = $iremNoteItemCategory;
                $iremNoteItemCategory = null;
            }
            // $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> category records >>> ' . var_export($_categoryData, true), self::MASK_LOG_TRUE);

            $_notesListQueryResult = null;
            if ($isTrashDataRequest) {
                //fetch deleted data
                $_notesListQueryResult = mysqli_query($conn, "SELECT * FROM irem_notes WHERE irem_notes_userid='$loggedInUserId' AND irem_notes_ismarkedfordeletion='true'");
            } else {
                //fetch normal data
                $_notesListQueryResult = mysqli_query($conn, "SELECT * FROM irem_notes WHERE irem_notes_userid='$loggedInUserId' AND (irem_notes_ismarkedfordeletion='false' OR irem_notes_ismarkedfordeletion='')");
            }

            $_notesListData = [];
            while ($__noteRow = mysqli_fetch_array($_notesListQueryResult)) {
                $__iremNoteItem = new IRemNoteItem();
                $__iremNoteItem->setNoteId($__noteRow['irem_notes_id'])
                    ->setNoteTitle($__noteRow['irem_notes_title'])
                    ->setNoteDescription($__noteRow['irem_notes_description'])
                    ->setCreated($__noteRow['irem_notes_created'])
                    ->setLastUpdated($__noteRow['irem_notes_lastupdated'])
                    ->setMarkedForDeletion($__noteRow['irem_notes_ismarkedfordeletion']);

                $__categoryTags = explode(",", $__noteRow['irem_notes_tags']);

                $__iremNoteItemCategoryGroup = [];
                for ($_categoryTagsItr_ = 0; $_categoryTagsItr_ < sizeof($__categoryTags); $_categoryTagsItr_++) {
                    $_catId_ = trim($__categoryTags[$_categoryTagsItr_], ' ');

                    // $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> $$_catId_  >>> ' . $_catId_, self::MASK_LOG_TRUE);
                    // $this->logger->debug('IRemNotesTableTransactions >>> getNotesList >>> $_categoryData  >>> ' . var_export($_categoryData, true), self::MASK_LOG_TRUE);
                    if (isset($_categoryData["$_catId_"])) {
                        array_push($__iremNoteItemCategoryGroup, $_categoryData["$_catId_"]);
                    } else {
                        $this->logger
                            ->errorEvent()
                            ->log("SEVERE >>> IRemNotesTableTransactions >>> getNotesList >>> Data error : category data with id '$_catId_' is not found in category table. Where as its present in Notes Table "  . "\n");
                    }
                }

                $__iremNoteItem->setCategoryTags($__iremNoteItemCategoryGroup);

                array_push($_notesListData, $__iremNoteItem);

                $this->logger->debug('IRemNotesTableTransactions >>> out of getNotesListDataInResponseFormat method ', self::MASK_LOG_TRUE);
            }
        } catch (Exception $e) {
            $this->logger
                ->errorEvent()
                ->log('IRemNotesTableTransactions >>> getNotesListDataInResponseFormat >>> Caught exception: ' . var_export($e, true) . "\n");
        } finally {
            $this->logger->debug('IRemNotesTableTransactions >>> getNotesListDataInResponseFormat >>> executing finally  method ', self::MASK_LOG_TRUE);
            return $_notesListData;
        }
    }
}
