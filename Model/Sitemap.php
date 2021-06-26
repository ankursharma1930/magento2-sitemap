<?php
declare(strict_types=1);
namespace Amage\Sitemap\Model;
use Magento\Framework\App\ObjectManager;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * Generate XML file
     *
     * @see http://www.sitemaps.org/protocol.html
     *
     * @return $this
     */
    public function generateXml()
    {
        $this->_initSitemapItems();

        $exclude_url = ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('custom/exclude/remove_urls', \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
        if ($exclude_url) {
            $exclude_url = explode("\n", $exclude_url);
            $exclude_url = array_map('trim', $exclude_url);
        }
        /** @var $item SitemapItemInterface */
        foreach ($this->_sitemapItems as $item) {

            if (in_array($item->getUrl(), $exclude_url)) {
                    continue;
                }
            
            $xml = $this->_getSitemapRow(
                $item->getUrl(),
                $item->getUpdatedAt(),
                $item->getChangeFrequency(),
                $item->getPriority(),
                $item->getImages()
            );

            if ($this->_isSplitRequired($xml) && $this->_sitemapIncrement > 0) {
                $this->_finalizeSitemap();
            }

            if (!$this->_fileSize) {
                $this->_createSitemap();
            }

            $this->_writeSitemapRow($xml);
            // Increase counters
            $this->_lineCount++;
            $this->_fileSize += strlen($xml);
        }


        /*** add custom url here***/
        $custom = ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('custom/url/custom_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
        
        if ($custom) {
            $custom = \Zend_Json::decode($custom);
            foreach ($custom as $urls) {
                $url = $urls['url'];
                $frequ = 'daily';
                $priority = 0.5;
                $date = $this->_getCurrentDateTime();
                $xml = $this->_getSitemapRow($url, $date, $frequ, $priority, "");
                $this->_writeSitemapRow($xml);
            }
        }


        $this->_finalizeSitemap();

        if ($this->_sitemapIncrement == 1) {
            // In case when only one increment file was created use it as default sitemap
            $path = rtrim($this->getSitemapPath(), '/')
                . '/'
                . $this->_getCurrentSitemapFilename($this->_sitemapIncrement);
            $destination = rtrim($this->getSitemapPath(), '/') . '/' . $this->getSitemapFilename();

            $this->_directory->renameFile($path, $destination);
        } else {
            // Otherwise create index file with list of generated sitemaps
            $this->_createSitemapIndex();
        }

        $this->setSitemapTime($this->_dateModel->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }

}