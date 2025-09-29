<?php
 
namespace Vgroup65\News\Controller\Adminhtml\Newslist;
 
use Vgroup65\News\Controller\Adminhtml\Newslist;
 
class MassExport extends Newslist
{
    /**
     * @return void
     */
    public function execute()
    {
        // Get IDs of the selected news
        $newsIds = $this->getRequest()->getParam('news');
        /* @var Vgroup65\News\Model\NewslistFactory */
        $newsModel = $this->_newslistFactory->create();
        $newsCollection = $newsModel->getCollection();
       
        $newsCollection->addFieldToFilter('news_id', ['in' => $newsIds]);
        
        /* @var Vgroup65\News\Helper\Data */
        $imageHelper = $this->helper;
        $basePath = $imageHelper->getBaseDir();
        $currentStores = $imageHelper->getCurrentStores();

        $heading = [
             __('News ID'),
             __('News Title'),
             __('URL Identifier'),
             __('Category ID'),
             __('Store Name'),
             __('Image Url'),
             __('Description'),
             __('Status'),
             __('Publish Date')
         ];
         $outputFile = $basePath."/NewsList.csv";
         $handle = fopen($outputFile, 'w');
         fputcsv($handle, $heading);
        foreach ($newsCollection as $news) {
            //set store name
            $storeView = explode(',', $news['store_view']);
            $storesForExport = [];
            foreach ($storeView as $stores):
                $storesForExport[] = $this->getStoreName($stores, $currentStores);
            endforeach;
             
            if ($news['category_id'] == '0'):
                $category_id = '';
            else:
                    $category_id = $news['category_id'];
            endif;
             
                //status
            if ($news['status'] == '1'):
                $status = 'Enabled';
            else:
                     $status = 'Disabled';
            endif;
             
                 //image
                 $newsImage = $news['image'];
            if (!empty($newsImage)):
                if (!filter_var($newsImage, FILTER_VALIDATE_URL)):
                    $getBaseUrl = $imageHelper->getBaseUrl();
                        $newsImageValue = $getBaseUrl.'/'.$newsImage;
                else:
                             $newsImageValue = $newsImage;
                endif;
                
            else:
                     $newsImageValue = '';
            endif;
             
                 $row = [
                 $news['news_id'],
                 $news['title'],
                 $news['url_identifier'],
                 $category_id,
                 implode(',', $storesForExport),
                 $newsImageValue,
                 strip_tags($news['description']),
                 $status,
                 date('m/d/Y H:i:s', strtotime($news['publish_date']))
                 ];
                 fputcsv($handle, $row);
        }
         $this->downloadCsv($outputFile);
    }
   

    public function downloadCsv($file)
    {
        if (file_exists($file)) {
            //set appropriate headers
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
        }
    }
    
    public function getStoreName($storeId, $currentStores)
    {
        foreach ($currentStores as $currentStore):
            if ($currentStore['store_id'] == $storeId):
                 return $currentStore['name'];
            endif;
        endforeach;
    }
}
