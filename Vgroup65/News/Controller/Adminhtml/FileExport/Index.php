<?php
namespace Vgroup65\News\Controller\Adminhtml\FileExport;

use Vgroup65\News\Controller\Adminhtml\FileExport;

class Index extends FileExport
{
    public function execute()
    {
        $newsModel = $this->_newslistFactory->create();
        $collection = $newsModel->getCollection();
        $newsCollection = $collection->getData();

        $imageHelper = $this->helper;
        $basePath = $imageHelper->getBaseDir();
        $currentStores = $imageHelper->getCurrentStores();
        
        if (!file_exists($basePath)):
            try {
                mkdir($basePath, 0775, true);
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    'issue with directory permission, please set permission of the News folder in the media directory'
                );
                $this->_redirect('news/newslist/index');
                return;
            }
        endif;

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
         
        if (!file_exists($basePath)):
            mkdir($basePath, 0775, true);
        endif;
         
        $outputFile = $basePath . "/NewsList.csv";
        $handle = fopen($outputFile, 'w');

        // ? pass separator, enclosure, escape explicitly
        fputcsv($handle, $heading, ',', '"', '\\');

        foreach ($newsCollection as $news) {
            //set store name
            $storeView = explode(',', $news['store_view']);
            $storesForExport = [];
            foreach ($storeView as $stores):
                $storesForExport[] = $this->getStoreName($stores, $currentStores);
            endforeach;
             
            $category_id = ($news['category_id'] == '0') ? '' : $news['category_id'];
            $status = ($news['status'] == '1') ? 'Enabled' : 'Disabled';
             
            //image
            $newsImage = $news['image'];
            if (!empty($newsImage)):
                if (!filter_var($newsImage, FILTER_VALIDATE_URL)):
                    $getBaseUrl = $imageHelper->getBaseUrl();
                    $newsImageValue = $getBaseUrl . $newsImage;
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

            // ? updated here too
            fputcsv($handle, $row, ',', '"', '\\');
        }

        $this->downloadCsv($outputFile);
    }
 
    public function downloadCsv($file)
    {
        if (file_exists($file)) {
            //set appropriate headers
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=' . basename($file));
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
