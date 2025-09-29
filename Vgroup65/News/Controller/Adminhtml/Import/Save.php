<?php

namespace Vgroup65\News\Controller\Adminhtml\Import;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Vgroup65\News\Controller\Adminhtml\Newslist;

class Save extends Newslist {

    public function execute() {
        $newsListModel = $this->_newslistFactory->create();
        $newsListCollection = $newsListModel->getCollection();
        $newsListCollection->addFieldToSelect('url_identifier');

        $currentUrlIdentifier = [];
        foreach ($newsListCollection as $newsList):
            $currentUrlIdentifier[] = $newsList->getUrlIdentifier();
        endforeach;

        $dataFiles = $this->getRequest()->getFiles('news');

        foreach ($dataFiles as $values):
            $fileType = $values['type'];
            $fileTmpName = $values['tmp_name'];
            $fileName = $values['name']; // ? capture original file name
        endforeach;

// ? safer CSV file validation
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedTypes = [
            'text/csv',
            'text/plain',
            'application/vnd.ms-excel'
        ];

        if (strtolower($fileExtension) !== 'csv' || !in_array($fileType, $allowedTypes)) {
            $this->messageManager->addError(__('Please upload a valid .csv file'));
            $this->_redirect('*/*/');
            return;
        }

        $row = 1;
        $newsFileArray = [];
        $fileNewsIdCheck = [];
        $fileTitlesCheck = [];
        $fileUrlIdentifierCheck = [];
        $fileCategoryIdCheck = [];
        $fileStoreNameCheck = [];
        $fileDescriptionCheck = [];
        $fileStatusCheck = [];
        $fileStatusValidation = [];
        $fileUrlIdentifierVelidation = [];
        $fileUrlIdentifierForUpdate = [];
        $fileURLValidation = [];
        $fileImageUrlValidation = [];
        $checkDuplicateUrlIdentifier = [];

        if (($handle = fopen($fileTmpName, "r")) !== false) {
            $no = 0;

            //get current store name
            $getCurrentStores = $this->getCurrentStores();
            $currentStoreNameArray = [];
            foreach ($getCurrentStores as $CurrentStore):
                $currentStoreNameArray[] = $CurrentStore['name'];
            endforeach;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($no == 0):
                    $no++;
                    continue;
                endif;

                $num = count($data);
                $rowArray = [];
                for ($c = 0; $c < $num; $c++) {
                    //echo $data[$c] . "<br />\n";
                    $rowArray[] = $data[$c];
                }

                //Field News Id Check
                if (!empty($rowArray[0])):
                    //check file news id
                    $checkNewsId = $this->checkNewsId(trim($rowArray[0]));

                    if ($checkNewsId == true):
                        //check current url identifier for update
                        if (!empty($rowArray[2])):
                            $fileUrlIdentifierUpdateValue = trim(str_replace(" ", "", $rowArray[2]));
                            $checkUrlIdentifierForUpdate = $this->checkUrlIdentifierForUpdate(trim($rowArray[0]), $fileUrlIdentifierUpdateValue);

                            if ($checkUrlIdentifierForUpdate == true):
                                $fileUrlIdentifierForUpdate[] = $no;
                            endif;
                        else:
                            $fileUrlIdentifierCheck[] = $no;
                        endif;
                    endif;
                    if ($checkNewsId == false):
                        $fileNewsIdCheck[] = $no;
                    endif;

                endif;

                //Check Duplicate Url Identifier
                if (!empty($rowArray[2])):
                    $checkDuplicateUrlIdentifier[] = str_replace(" ", "", strtolower(trim($rowArray[2])));
                endif;

                //Field UrlIdentifier check
                if (empty($rowArray[0])):
                    if (!empty($rowArray[2])):
                        $fileUrlIdentifier = trim(str_replace(" ", "", $rowArray[2]));
                        $checkUrlIdentifier = $this->checkUrlIdentifier($fileUrlIdentifier, $currentUrlIdentifier);
                        if ($checkUrlIdentifier == true):
                            $fileUrlIdentifierVelidation[] = $no;
                        endif;

                    else:
                        $fileUrlIdentifierCheck[] = $no;
                    endif;
                endif;

                //Field title check
                if (empty($rowArray[1])):
                    $fileTitlesCheck[] = $no;
                endif;

                //Field DescriptionCheck check
                if (empty($rowArray[6])):
                    $fileDescriptionCheck[] = $no;
                endif;
                //Field StatusCheck check
                if ($rowArray[7] != ''):
                    if (strtolower($rowArray[7]) == 'enabled' || strtolower($rowArray[7]) == 'disabled'):
                    else:
                        $fileStatusValidation[] = $no;
                    endif;

                else:
                    //foe empty status field
                    $fileStatusCheck[] = $no;
                endif;
                //Field CategoryId Check
                //get current news collection
                $currentCategory = $this->getNewsCategory();
                if (!empty($rowArray[3])):
                    $checkCategoryResponce = $this->checkNewsCategory($rowArray[3], $currentCategory);
                    if ($checkCategoryResponce == false):
                        $fileCategoryIdCheck[] = $no;
                    endif;
                endif;

                //check image validation
                if (!empty($rowArray[5])):
                    if (filter_var(trim($rowArray[5]), FILTER_VALIDATE_URL)) {
                        if (getImageSize(trim($rowArray[5]))):
                        else:
                            $fileImageUrlValidation[] = $no;
                        endif;
                    } else {
                        $fileURLValidation[] = $no;
                    }
                endif;

                //Field store check
                if (!empty($rowArray[4])):
                    $fileStoreName = $rowArray[4];
                    //check store name
                    $checkStores = $this->checkStores($fileStoreName, $currentStoreNameArray);

                    if ($checkStores == false):
                        $fileStoreNameCheck[] = $no;
                    endif;
                endif;

                $newsFileArray[] = $rowArray;
                $no++;
            }
            fclose($handle);

            if (empty($newsFileArray)):
                $BlankFileErrorMsg = "Row 1  has blank Title field. <br>
                                        Row 1 has blank URL Identifier field. <br>
                                        Row 1 has blank Description field. <br>
                                        Row 1 has blank Status field. ";

                $this->messageManager->addError($BlankFileErrorMsg);
                $this->_redirect('*/*/');
                return;
            endif;

            $checkRequiredFields = $this->checkRequiredFields($fileTitlesCheck, $fileUrlIdentifierCheck, $fileDescriptionCheck, $fileStatusCheck);

            if (count($checkDuplicateUrlIdentifier) > count(array_unique($checkDuplicateUrlIdentifier))):
                $duplicateValues[] = array_diff_assoc($checkDuplicateUrlIdentifier, array_unique($checkDuplicateUrlIdentifier));

                if (!empty($duplicateValues)):
                    $duplicateValuess = implode(',', $duplicateValues[0]);
                    $checkRequiredFields .= 'Duplicate url identifier "' . $duplicateValuess . '" found.';
                endif;
            endif;

            if (!empty($fileNewsIdCheck)):
                $fileNewsIdCheck = implode(',', $fileNewsIdCheck);
                $checkRequiredFields .= 'Row ' . $fileNewsIdCheck . ' has Invalid News Id. <br>';
            endif;

//            if (!empty($fileURLValidation)):
//                $fileURLValidation = implode(',', $fileURLValidation);
//                $checkRequiredFields .= 'Row ' . $fileURLValidation . ' has Invalid URL path. <br>';
//            endif;

            if (!empty($fileImageUrlValidation)):
                $fileImageUrlValidation = implode(',', $fileImageUrlValidation);
                $checkRequiredFields .= 'Row ' . $fileImageUrlValidation . ' has Invalid Image URL. <br>';
            endif;

            if (!empty($fileUrlIdentifierForUpdate)):
                $fileUrlIdentifierForUpdate = implode(',', $fileUrlIdentifierForUpdate);
                $checkRequiredFields .= 'URL Identifier is already exist in Row no ' . $fileUrlIdentifierForUpdate . '<br>';
            endif;

            if (!empty($fileUrlIdentifierVelidation)):
                $fileUrlIdentifierVelidation = implode(',', $fileUrlIdentifierVelidation);
                $checkRequiredFields .= 'Row ' . $fileUrlIdentifierVelidation . ' Url identifier already exists. <br>';
            endif;

            if (!empty($fileCategoryIdCheck)):
                $fileCategoryIdCheck = implode(',', $fileCategoryIdCheck);
                $checkRequiredFields .= 'Row ' . $fileCategoryIdCheck . ' has Invalid Category Id. <br>';
            endif;

            if (!empty($fileStoreNameCheck)):
                $fileStoreNameCheck = implode(',', $fileStoreNameCheck);
                $checkRequiredFields .= 'Row ' . $fileStoreNameCheck . ' has Invalid Store Name. <br>';
            endif;

            if (!empty($fileStatusValidation)):
                $fileStatusValidation = implode(',', $fileStatusValidation);
                $checkRequiredFields .= 'Row ' . $fileStatusValidation . ' has invalid value in Status field. <br>';
            endif;
            if (!empty($checkRequiredFields)):
                $this->messageManager->addError($checkRequiredFields);
                $this->_redirect('*/*/');
                return;
            endif;
        }

        $formData = [];
        try {
            foreach ($newsFileArray as $key => $news):
                //update news
                if (!empty($news[0])):
                    $responce = $this->UpdateNews($news);
                    continue;
                endif;

                //import news
                if ($news[4] != ''):
                    $storeIdForFormData = [];
                    $fileStoreName = explode(',', $news[4]);
                    foreach ($fileStoreName as $singleStore):
                        $storeIdForFormData[] = $this->getStoreId($singleStore);
                    endforeach;
                    $storeIdValue = implode(',', $storeIdForFormData);
                else:
                    $storeIdValue = 1;
                endif;

                if ($news[3] != ''):
                    $categoryIdForFormData = $news[3];
                else:
                    $categoryIdForFormData = 0;
                endif;

                //publish date
                if ($news[8] != ''):
                    $fileDate = str_replace("-", "/", trim($news[8]));
                    $publishDate = date('Y-m-d H:i:s', strtotime($fileDate));
                else:
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $objDate = $objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
                    $publishDate = $objDate->date()->format('Y-m-d H:i:s');
                endif;

                //status value
                if (strtolower($news[7]) == 'enabled'):
                    $statusForImport = 1;
                endif;
                if (strtolower($news[7]) == 'disabled'):
                    $statusForImport = 0;
                endif;

                try {
                    $formData = [
                        'title' => trim($news[1]),
                        'url_identifier' => trim(str_replace(" ", "", $news[2])),
                        'category_id' => $categoryIdForFormData,
                        'store_view' => $storeIdValue,
                        'image' => trim($news[5]),
                        'description' => trim($news[6]),
                        'status' => $statusForImport,
                        'publish_date' => $publishDate
                    ];

                    $newsListModel->setData($formData);
                    $newsListModel->save();
                    $currentNewsId = $newsListModel->getNewsId();

                    if ($categoryIdForFormData != 0):
                        $this->setCategoryNewsJunction($categoryIdForFormData, $currentNewsId);
                    endif;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/');
                    return;
                }
            endforeach;
            $this->messageManager->addSuccess(__('News imported successfully'));
            $this->_redirect('*/*/');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }
    }

    public function UpdateNews($news) {
        //get news list instence
        $newsListModel = $this->_newslistFactory->create();
        $newsListModel->load($news[0]);

        $currentCategoryIds = $newsListModel->getCategoryId();
        $currentImage = $newsListModel->getImage();

        //category
        if (empty($news[3])):
            $categoryId = '0';
        else:
            if ($currentCategoryIds == $news[3]):
                $categoryId = $news[3];
            else:

                //get category News junction collection
                $categoryNewsJunctionFactory = $this->categoryNewsJunctionFactory->create();
                $categoryNewsJunctionCollection = $categoryNewsJunctionFactory->getCollection();
                $categoryNewsJunctionCollection->addFieldToFilter('news_id', $news[0]);

                //delete Current category id from category nes junction
                foreach ($categoryNewsJunctionCollection as $currentCategoryNewsJunction):
                    $currentCategoryNewsJunction->delete();
                endforeach;

                //assign category to news
                $filedCategorys = explode(',', $news[3]);

                try {
                    foreach ($filedCategorys as $filedCategory):
                        $categoryNewsJunctionFactory->setData(['category_id' => $filedCategory, 'news_id' => $news[0]]);
                        $categoryNewsJunctionFactory->save();
                    endforeach;
                } catch (\Exception $e) {
                    $this->messageManager->addError(__($e->getMessage()));
                }

                $categoryId = $news[3];
            endif;
        endif;

        //storeId
        if (!empty($news[4])):
            $storeNames = $news[4];
            $storeNames = explode(',', $storeNames);
            foreach ($storeNames as $storeName):
                $getStoreId[] = $this->getStoreId(trim($storeName));
            endforeach;

            $storeId = implode(',', $getStoreId);

        else:
            //default store
            $storeId = '1';
        endif;

        if (empty($news[8])):
            $publishDate = date('Y-m-d H:i:s');
        else:
            $fileDate = str_replace("-", "/", trim($news[8]));
            $publishDate = date('Y-m-d H:i:s', strtotime($fileDate));
        endif;

        //update image
        if (!empty($news[5])):
            $helper = $this->helper;

            //if current image is null
            if ($currentImage == ''):
                $imageFormValue = trim($news[5]);
            else:
                //if curretn image is not null
                if ($helper->getBaseUrl() . $currentImage == trim($news[5])):
                    $imageFormValue = $currentImage;
                else:
                    $helper = $this->helper;
                    if (!filter_var($currentImage, FILTER_VALIDATE_URL)):
                        if (file_exists($helper->getBaseDir() . $currentImage)):
                            unlink($helper->getBaseDir() . $currentImage);
                        endif;
                    endif;

                    $imageFormValue = trim($news[5]);
                endif;
            endif;
        endif;

        if (empty($news[5])):
            $helper = $this->helper;
            if (!empty($currentImage)):
                if (!filter_var($currentImage, FILTER_VALIDATE_URL)):
                    if (file_exists($helper->getBaseDir() . $currentImage)):
                        unlink($helper->getBaseDir() . $currentImage);
                    endif;
                endif;

                $imageFormValue = '';
            endif;

            $imageFormValue = '';
        endif;

        //status value
        if (strtolower($news[7]) == 'enabled'):
            $statusForUpdate = 1;
        endif;
        if (strtolower($news[7]) == 'disabled'):
            $statusForUpdate = 0;
        endif;

        $formdata = ['news_id' => $news[0],
            'category_id' => $categoryId,
            'title' => trim($news[1]),
            'url_identifier' => trim(str_replace(" ", "", $news[2])),
            'description' => trim($news[6]),
            'image' => $imageFormValue,
            'publish_date' => $publishDate,
            'store_view' => $storeId,
            'status' => $statusForUpdate
        ];

        $newsListModel->setData($formdata);
        $updateResponce = $newsListModel->save();
        return true;
    }

    public function checkUrlIdentifierForUpdate($newsId, $fileUrlIdentifierUpdateValue) {

        $newsListModel = $this->_newslistFactory->create();
        $newsListCollection = $newsListModel->getCollection();
        $newsListCollection->addFieldToFilter('news_id', ['neq' => $newsId]);
        $newsListCollection->addFieldToFilter('url_identifier', $fileUrlIdentifierUpdateValue);

        if ($newsListCollection->count() > 0):
            return true;
        else:
            return false;
        endif;
    }

    public function checkNewsId($newsId) {
        $newsListModel = $this->_newslistFactory->create();
        $newsListCollection = $newsListModel->getCollection();
        $newsListCollection->addFieldToFIlter('news_id', $newsId);
        if ($newsListCollection->count() > 0):
            return true;
        else:
            return false;
        endif;
    }

    public function setCategoryNewsJunction($categoryIdForFormData, $currentNewsId) {
        $categoryNewsJunctionModel = $this->categoryNewsJunctionFactory->create();
        $categoryIdForFormData = explode(',', $categoryIdForFormData);
        foreach ($categoryIdForFormData as $catId):
            $categoryNewsJunctionModel->setData(['category_id' => $catId, 'news_id' => $currentNewsId]);
            $categoryNewsJunctionModel->save();
        endforeach;
    }

    public function getCurrentStores() {
        $helper = $this->helper;
        $currentStores = $helper->getCurrentStores();
        return $currentStores;
    }

    public function getStoreId($fileStoreName) {
        $helper = $this->helper;
        $currentStores = $helper->getCurrentStores();
        foreach ($currentStores as $store):
            if (strtolower($store['name']) == strtolower($fileStoreName)):
                return $store['store_id'];
            endif;
        endforeach;
    }

    public function getNewsCategory() {
        $newsFactory = $this->newsFactory;
        $newsFactory = $newsFactory->create();
        $newsCollection = $newsFactory->getCollection();

        //get current news categorys
        $currentCategory = [];
        foreach ($newsCollection as $news):
            $currentCategory[] = $news->getCategoryId();
        endforeach;
        return $currentCategory;
    }

    public function checkNewsCategory($fileCategoryIds, $currentCategory) {
        $fileCategoryIds = explode(',', $fileCategoryIds);
        //check file category
        foreach ($fileCategoryIds as $id):
            $resp = in_array($id, $currentCategory);
            if ($resp == false):
                return false;
            endif;
        endforeach;
        return true;
    }

    public function checkUrlIdentifier($fileUrlIdentifier, $currentUrlIdentifier) {
        $fileUrlIdentifier = strtolower(trim($fileUrlIdentifier));
        $currentUrlIdentifier = array_map('strtolower', $currentUrlIdentifier);
        //check file category
        $resp = in_array($fileUrlIdentifier, $currentUrlIdentifier);
        if ($resp == true):
            return true;
        else:
            return false;
        endif;
    }

    public function checkStores($fileStoreNames, $currentStoreNameArray) {
        $currentStoreNameArray = array_map('strtolower', $currentStoreNameArray);

        $fileStoreNames = explode(',', $fileStoreNames);
        foreach ($fileStoreNames as $fileStoreName):
            //check file category
            $resp = in_array(strtolower(trim($fileStoreName)), $currentStoreNameArray);

            if ($resp == false):
                return false;
            endif;
        endforeach;
        return true;
    }

    public function checkRequiredFields($fileTitlesCheck, $fileUrlIdentifierCheck, $fileDescriptionCheck, $fileStatusCheck) {
        $BlnkFieldErrorMsg = '';
        if (!empty($fileTitlesCheck)):
            $BlnkFieldErrorMsg .= 'Row ' . implode(',', $fileTitlesCheck) . ' has blank Title field. <br>';
        endif;
        if (!empty($fileUrlIdentifierCheck)):
            $BlnkFieldErrorMsg .= 'Row ' . implode(',', $fileUrlIdentifierCheck) . ' has blank URL Identifier field. <br>';
        endif;
        if (!empty($fileDescriptionCheck)):
            $BlnkFieldErrorMsg .= 'Row ' . implode(',', $fileDescriptionCheck) . ' has blank Description field. <br>';
        endif;
        if (!empty($fileStatusCheck)):
            $BlnkFieldErrorMsg .= 'Row ' . implode(',', $fileStatusCheck) . ' has blank Status field. <br>';
        endif;
        return $BlnkFieldErrorMsg;
    }

}
