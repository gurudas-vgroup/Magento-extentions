<?php
 
namespace Vgroup65\News\Controller\Adminhtml\News;
 
use Vgroup65\News\Controller\Adminhtml\News;
 
class Delete extends News
{
    /**
     * @return void
     */
    public function execute()
    {
        $newsId = (int) $this->getRequest()->getParam('id');
 
        if ($newsId) {
            /**

            *
   * @var $newsModel \Mageworld\SimpleNews\Model\News
*/
            $newsModel = $this->_newsFactory->create();
            $newsModel->load($newsId);
 
            // Check this news exists or not
            if (!$newsModel->getId()) {
                $this->messageManager->addError(__('This news no longer exists.'));
            } else {
                try {
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
