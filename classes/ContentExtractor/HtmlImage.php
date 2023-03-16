<?php

/**
 * Html image content extractor.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\ContentExtractor;

/**
 * Html image content extractor.
 *
 * Extracts images from stored in CMS_HTML/CMS_HTMLTEXT content types.
 */
class HtmlImage
{

    /**
     * @var string
     */
    private $value;

    /**
     * @var \cDb|null
     */
    private $db;

    /**
     * Constructor.
     *
     * @param string $value Stored content value.
     * @param \cDb|null $db
     */
    public function __construct(string $value, \cDb $db = null)
    {
        $this->value = $value;
        $this->db = $db instanceof \cDb ? $db : \cRegistry::getDb();
    }

    /**
     * Extracts the first image from stored content.
     *
     * @param int $pos The position of image to extract, 0 for first image.
     * @return array Assoziative array as follows or empty array:
     *     ['idupl' => (int), 'path' => (string), 'name' => (string)]
     * @throws \cDbException
     * @throws \cInvalidArgumentException
     */
    public function extract(int $pos = 0): array
    {
        $imageInfo = [];

        // Extract image from content
        $sTmpValue = urldecode($this->value);

        $doc = new \DOMDocument();
        $doc->loadHTML($sTmpValue);
        $xpath = new \DOMXPath($doc);
        $aImg = $xpath->evaluate('//img');
        $allImages = [];

        if ($aImg->length > 0) {
            // Collect all images
            foreach ($aImg as $val) {
                $extractedImg = [];
                $uploadFilePath = explode('upload/', $val->getAttribute('src'));
                $extractedImg['name'] = basename($uploadFilePath[1]);
                $uploadDirPath = explode($extractedImg['name'], $uploadFilePath[1]);
                $extractedImg['path'] = $uploadDirPath[0];
                $allImages[] = $extractedImg;
            }

            // Get detailed info about the first image
            if (count($allImages) && isset($allImages[$pos])) {
                $imageInfo['name'] = $allImages[$pos]['name'];
                $imageInfo['path'] = $allImages[$pos]['path'];

                // Try to get idupl of the image
                $imageInfo['idupl'] = $this->getIdUpl($imageInfo['path'], $imageInfo['name']);
            }
        }

        return $imageInfo;
    }

    private function getIdUpl(string $dirName, string $fileName): int
    {
        $comment = '-- ' . __CLASS__ . '->' . __FUNCTION__ . '()';
        $sql = $comment . "
            SELECT `idupl` FROM `%s` WHERE `dirname` = '%s' AND `filename` = '%s'";
        $this->db->query($sql, \cRegistry::getDbTableName('upl'), $dirName, $fileName);
        if ($this->db->nextRecord()) {
            return \cSecurity::toInteger($this->db->f('idupl'));
        }
        else {
            return 0;
        }
    }

}