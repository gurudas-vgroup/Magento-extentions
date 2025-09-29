<?php
 
namespace Vgroup65\News\Controller\Adminhtml\Newslist;
 
use Vgroup65\News\Controller\Adminhtml\Newslist;
 
class Delete extends Newslist
{
    /**
     * @return void
     */
    public function execute()
    {
        $newsId = (int) $this->getRequest()->getParam('id');
      
        $imageHelper = $this->helper;
      
        if ($newsId) {
            /**

            *
   * @var $newsModel \Mageworld\SimpleNews\Model\News
*/
            $newsModel = $this->_newslistFactory->create();
            $newsModel->load($newsId);
 
            // Check this news exists or not
            if (!$newsModel->getId()) {
                $this->messageManager->addError(__('This news no longer exists.'));
            } else {
                try {
                    $newsData = $newsModel->getData();
                    //delete category ID from category news junction table
                    if (!empty($newsData['category_id'])):
                        $categoryNewsJunctionModel = $this->categoryNewsJunctionFactory->create();
                        $categoryNewsJunctionCollection = $categoryNewsJunctionModel->getCollection();
                        $categoryNewsJunctionCollection->addFieldToFilter('news_id', $newsData['news_id']);
                        if ($categoryNewsJunctionCollection->count() > 0):
                            foreach ($categoryNewsJunctionCollection as $newsCategoryId):
                                $newsCategoryId->delete();
                            endforeach;
                        endif;
                    endif;
                
                    if (!empty($newsData['image'])):
                        $helper = $this->helper;
                        $currentImage = $newsData['image'];
                        if (!filter_var($currentImage, FILTER_VALIDATE_URL)):
                            if (file_exists($helper->getBaseDir().$currentImage)):
                                unlink($helper->getBaseDir().$currentImage);
                            endif;
                        endif;
                    endif;
                   
                    // Delete news
                    $newsModel->delete();
                    $this->messageManager->addSuccess(__('The news has been deleted.'));
 
                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $newsModel->getId()]);
                }
            }
        }
    }
}
