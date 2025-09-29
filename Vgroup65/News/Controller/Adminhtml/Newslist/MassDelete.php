<?php
 
namespace Vgroup65\News\Controller\Adminhtml\Newslist;
 
use Vgroup65\News\Controller\Adminhtml\Newslist;
 
class MassDelete extends Newslist
{
    /**
     * @return void
     */
    public function execute()
    {
        // Get IDs of the selected news
        $newsIds = $this->getRequest()->getParam('news');
      
        $imageHelper = $this->helper;
      
        foreach ($newsIds as $newsId) {
            try {
                /**
*
                 *
 * @var $newsModel \Vgroup65\News\Model\Newslist
*/
                $newsModel = $this->_newslistFactory->create();
                
                $newsModel->load($newsId);
                
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
                    $helper = $this->getHelper();
                    $currentImage = $newsData['image'];
                    if (!filter_var($currentImage, FILTER_VALIDATE_URL)):
                        if (file_exists($helper->getBaseDir().$currentImage)):
                            unlink($imageHelper->getBaseDir().$currentImage);
                        endif;
                    endif;
                endif;

                $newsModel->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
 
        if (count($newsIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($newsIds))
            );
        }
 
        $this->_redirect('*/*/index');
    }
   
    public function getHelper()
    {
        return $this->helper;
    }
}
