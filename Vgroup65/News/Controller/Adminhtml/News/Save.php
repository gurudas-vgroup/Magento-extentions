<?php

namespace Vgroup65\News\Controller\Adminhtml\News;

use Vgroup65\News\Controller\Adminhtml\News;

class Save extends News
{

    /**
     * @return void
     */
    public function execute()
    {
        $isPost = $this->getRequest()->getPost();

        if ($isPost) {
            $newsModel = $this->_newsFactory->create();

            $newsId = $this->getRequest()->getParam('category_id');
            if ($newsId) {
                $newsModel->load($newsId);
            }

            $formData = $this->getRequest()->getParam('news');
           
            if (isset($formData['category_id'])) {
                $collection = $newsModel->getCollection();
                $collection->addFieldToFilter('category_id', ['neq' => $formData['category_id']]);

                $formData['title'] = trim($formData['title']);
                $formData['category_url'] = trim(str_replace(" ", "", $formData['category_url']));

                $categoryTitleCollection = $collection->addFieldToFilter('title', $formData['title']);
                $categoryUrlCollection = $collection->addFieldToFilter('category_url', $formData['category_url']);
                
                $checkValidationForTitle = '';
                if ($categoryTitleCollection->count() > 0):
                      $checkValidationForCategory = "Category title already exist.<br>";
                endif;
                if ($categoryUrlCollection->count() > 0):
                      $checkValidationForCategory .="Category Url already exist.";
                endif;
                
                if (!empty($checkValidationForCategory)):
                       $this->messageManager->addError($checkValidationForCategory);
                       $this->_getSession()->setFormData($formData);
                       $this->_redirect('*/*/edit', ['id' => $formData['category_id']]);
                       return;
                endif;
                $newsModel->setData($formData);
                try {
                    // Save news
                    $newsModel->save();

                    // Success message display
                    $this->messageManager->addSuccess(__('Category updated successfully.'));

                    // Check if 'Save and Continue'
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', ['id' => $newsModel->getId(), '_current' => true]);
                        return;
                    }

                    // Go to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }

            
            //Create new category
            $collectionTitle = $newsModel->getCollection();
            $collectionUrl = $newsModel->getCollection();

            $formData['title'] = trim($formData['title']);
            $formData['category_url'] = trim(str_replace(" ", "", $formData['category_url']));

            $collectionTitle->addFieldToFilter('title', $formData['title']);
            $collectionUrl->addFieldToFilter('category_url', $formData['category_url']);
            
            $checkCategoryDetails = '';
            if ($collectionTitle->count() > 0):
                $checkCategoryDetails = 'Category title already exist.<br>';
            endif;
            if ($collectionUrl->count() > 0):
                $checkCategoryDetails .= 'Category Url already exist.<br>';
            endif;
            //Set Error msg for same category name
            if (!empty($checkCategoryDetails)):
                $this->messageManager->addError($checkCategoryDetails);
                $this->_getSession()->setFormData($formData);
                $this->_redirect('*/*/new', ['id' => $newsId]);
                return;
            endif;

            $newsModel->setData($formData);

            try {
                // Save news
                $newsModel->save();

                // Display success message
                $this->messageManager->addSuccess(__('Category created successfully.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $newsModel->getId(), '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $newsId]);
        }
    }
}
