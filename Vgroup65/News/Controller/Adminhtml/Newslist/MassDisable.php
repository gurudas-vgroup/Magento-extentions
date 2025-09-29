<?php
namespace Vgroup65\News\Controller\Adminhtml\Newslist;
 
use Vgroup65\News\Controller\Adminhtml\Newslist;

class MassDisable extends Newslist
{
   /**
    * @return void
    */
    public function execute()
    {
       // Get IDs of the selected news
        $newsIds = $this->getRequest()->getParam('news');
        $imageHelper = $this->helper;
      
        if (is_array($newsIds)):
            $newsIdArray = $newsIds;
        else:
              $newsIdArray = explode(',', $newsIds);
        endif;
        try {
            foreach ($newsIdArray as $newsId) {


                /** @var $newsModel \Vgroup65\News\Model\Newslist */
                $newsModel = $this->_newslistFactory->create();
                $newsModel->load($newsId);
                $newsModel->setData('status', 0);
                $newsModel->save();

            }
        } catch (\Exception $e) {
             $this->messageManager->addError($e->getMessage());
        }
 
        if (count($newsIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were disabled.', count($newsIdArray))
            );
        }
          $this->_redirect('*/*/index');
    }
   
    public function getHelper()
    {
        return $this->helper;
    }
}
