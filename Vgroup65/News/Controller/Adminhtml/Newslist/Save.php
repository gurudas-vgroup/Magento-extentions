<?php
namespace Vgroup65\News\Controller\Adminhtml\Newslist;

use Magento\Backend\App\Action\Context;
use Vgroup65\News\Controller\Adminhtml\Newslist;

class Save extends Newslist
{

    /**
     * @return void
     */
    public function execute()
    {
        //check validation foe permission issue
        $imageHelper = $this->getHelper();
        
        $path = $imageHelper->getBaseDir();

        if (!file_exists($path)):
            try {
                mkdir($path, 0775, true);
            } catch (\Exception $e) {
                $this->messageManager->addError('issue with dirctory permission, please set permistion of the News folder in the media directory');
                $this->_redirect('news/newslist/index');
                return;
            }
        endif;
        
        
        $isPost = $this->getRequest()->getPost();
        $imageHelper = $this->getHelper();

        if ($isPost) {
            $newsModel = $this->_newslistFactory->create();
            $formData = $this->getRequest()->getParam('news');
        
            //get image Files
            $getFiles = $this->getRequest()->getFiles('image');
            $uploadedImageSize = $getFiles['size'];

            //Update News details
            if (isset($formData['news_id'])):
                $newsId = $formData['news_id'];

                $newsCollection= $newsModel->getCollection();
                $newsCollection->addFieldToFilter('news_id', ['neq' => $newsId]);
                $newsCollection->addFieldToSelect('url_identifier');
                
                $formData['url_identifier'] = trim(str_replace(" ", "", $formData['url_identifier']));
                $newsCollection->addFieldToFilter('url_identifier', $formData['url_identifier']);
                
                
                if ($newsCollection->count() > 0):
                    $this->messageManager->addError(__('Url identifier must be unique.'));
                    $this->_redirect('*/*/edit', ['id' => $newsId, '_current' => true]);
                    return;
                endif;
                
                if ($uploadedImageSize > 0):
                    //upload image

                    $imageResponce = $this->getUploadNewsImageName($uploadedImageSize);
                
                    foreach ($imageResponce as $imagekey => $imageValue):
                        if ($imagekey == 'success'):
                            $formData['image'] = $imageValue;
                           
                             //unlink updated image
                             //current image
                             $curretnImage = $newsModel;
                             $curretnImage->load($newsId);
                             $currentImageValue =  $curretnImage->getImage();

                            if (!empty($currentImageValue)):
                                if (!filter_var($currentImageValue, FILTER_VALIDATE_URL)):
                                    if (file_exists($imageHelper->getBaseDir().$currentImageValue)):
                                          unlink($imageHelper->getBaseDir().$currentImageValue);
                                    endif;
                                endif;
                            endif;
                        endif;

                        if ($imagekey == 'errorUpload'):
                            $this->messageManager->addError(__($imageValue));
                            $this->_redirect('*/*/new');
                            return;
                        endif;
                    endforeach;
                    
                else:
                        //current image
                        $curretnImage = $newsModel;
                        $curretnImage->load($newsId);
                        $formData['image'] =  $curretnImage->getImage();
                endif;
                
                if (empty($formData['publish_date'])):
                    $formData['publish_date'] = date('m/d/Y H:i:s');
                endif;
                    $newsModel->load($newsId);
                
                    //current category ids
                    $currentCategoryId = $newsModel->getCategoryId();
                
                if (isset($formData['category_id']) && !empty($formData['category_id'])):
                    $formCategoryId = implode(',', $formData['category_id']);

                    if (trim($currentCategoryId) != trim($formCategoryId)):
                        $categoryNewsJunctionValue = $this->getCategoryNewsJunction();
                        $categoryNewsJunctionColl = $categoryNewsJunctionValue->getCollection();
                        $categoryNewsJunctionColl->addFieldToFilter('news_id', $newsId);

                        //delete CUrrent category id
                        foreach ($categoryNewsJunctionColl as $deleteCategoryColl):
                              $resp = $deleteCategoryColl->delete();
                        endforeach;

                        //assign form category to news
                        try {
                            foreach ($formData['category_id'] as $key => $singleCategoryId):
                                $categoryNewsJunctionValue->setData(['category_id' => $singleCategoryId , 'news_id' =>  $newsId]);
                                $categoryNewsJunctionValue->save();
                            endforeach;
                        } catch (\Exception $e) {
                            $this->messageManager->addError(__($e->getMessage()));
                        }

                    endif;
                    $formData['category_id'] = $formCategoryId;
                    
                else:
                            $formData['category_id'] = $currentCategoryId;
                endif;
                
                        //create multiple store view
                        $formData['store_view'] = implode(',', $formData['store_view']);
                
                        $newsModel->setData($formData);
                try {
                    //Update news
                    $newsModel->save();
                    $this->messageManager->addSuccess(__('News Updated Successfully'));
                    $this->_redirect('*/*/index');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $newsId, '_current' => true]);
                    return;
                }

                        //Create News
            else:
                                    
                $newsCollection= $newsModel->getCollection();
                $newsCollection->addFieldToSelect('url_identifier');

                $formData['url_identifier'] = trim(str_replace(" ", "", $formData['url_identifier']));
                $newsCollection->addFieldToFilter('url_identifier', $formData['url_identifier']);
                 
                if (empty($formData['publish_date'])):
                    $formData['publish_date'] = date('m/d/Y H:i:s');
                endif;
                
                if ($newsCollection->count() > 0):
                    $this->messageManager->addError(__('Url identifier must be unique.'));
                    $this->_getSession()->setFormData($formData);
                    $this->_redirect('*/*/new');
                    return;
                endif;
                
                //upload image $formData['image']
                $imageResponce = $this->getUploadNewsImageName($uploadedImageSize);
                foreach ($imageResponce as $imagekey => $imageValue):
                    if ($imagekey == 'success'):
                         $formData['image'] = $imageValue;
                    endif;

                    if ($imagekey == 'errorUpload'):
                            $this->_getSession()->setFormData($formData);
                            $this->messageManager->addError(__($imageValue));
                            $this->_redirect('*/*/new');
                            return;
                    endif;
                endforeach;
                
                //Form Categories id for assign to news
                if (!empty($formData['category_id'])):
                    $formCategoryIds = $formData['category_id'];
                    $formData['category_id'] = implode(",", $formData['category_id']);
                else:
                        $formCategoryIds = '0';
                        $formData['category_id'] = '0';
                endif;
                    $formData['store_view'] = implode(",", $formData['store_view']);
                    $newsModel->setData($formData);
                
                try {
                    // Save news
                    $newsModel->save();
                
                    //news id
                    $currentNewsId = $newsModel->getNewsId();
                    if ($formCategoryIds != '0'):
                        $categoryNewsJunctionModel = $this->getCategoryNewsJunction();
                        $categoryNewsJunctionCollection = $categoryNewsJunctionModel->getCollection();

                        try {
                            foreach ($formCategoryIds as $key => $singleCategoryId):
                                $categoryNewsJunctionModel->setData(['category_id' => $singleCategoryId , 'news_id' =>  $currentNewsId]);
                                $categoryNewsJunctionModel->save();
                            endforeach;
                        } catch (\Exception $e) {
                            $this->_getSession()->setFormData($formData);
                            $this->messageManager->addError(__($e->getMessage()));
                        }
                    endif;
                    // Display success message
                    $this->_getSession()->setFormData($formData);
                    $this->messageManager->addSuccess(__('The News has been saved.'));

                    // Check if 'Save and Continue'
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', ['id' => $newsModel->getId(), '_current' => true]);
                        return;
                    }

                    // Go to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->_getSession()->setFormData($formData);
                    $this->messageManager->addError($e->getMessage());
                }

                    $this->_getSession()->setFormData($formData);
                    $this->_redirect('*/*/edit', ['id' => $newsId]);
                    return;
            endif;

        }
    }
    
    
    public function getUploadNewsImageName($imageSize)
    {
        $imageHelper = $this->getHelper();
        
        $path = $imageHelper->getBaseDir();
        if (!file_exists($path)):
            mkdir($path, 0775, true);
        endif;
        //Upload news image
        if ($imageSize > 0):
            try {
                $uploader = $imageHelper->getFileUploaderFactory()->create(['fileId' => 'image']);

                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $path = $imageHelper->getBaseDir();
                    
                $responce = $uploader->save($path);
                    
                $imageName = $responce['file'];
                return ['success' => $imageName];
            } catch (\Exception $e) {
                return ['errorUpload' => $e->getMessage()];
            }
                    
        else:
                        return ['error' => 'Image field is empty'];
        endif;
    }
    
    public function getHelper()
    {
        return $this->helper;
    }
    
    public function getCategoryNewsJunction()
    {
        return $this->categoryNewsJunctionFactory->create();
    }
}
