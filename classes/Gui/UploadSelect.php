<?php

/**
 * Renders a select for upload files.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Gui;

use CONTENIDO\Plugin\MpDevTools\Client\Info;
use CONTENIDO\Plugin\MpDevTools\Module\CmsToken;

/**
 * Upload files select class.
 */
class UploadSelect extends AbstractBaseSelect
{

    const UPLOAD_KEY = 'idupl';

    const DBFS_KEY = 'iddbfs';

    /**
     * @var Info
     */
    private $clientInfo;

    /**
     * @var array
     */
    private $uploadFiles;

    /**
     * @var array
     */
    private $dbfsFiles;

    /**
     * Constructor.
     *
     * @inheritdoc
     */
    public function __construct(
        string $name, int $clientId, int $languageId, array $attr = [], \cDb $db = null
    )
    {
        parent::__construct($name, $clientId, $languageId, $attr, $db);

        $this->clientInfo = new Info($clientId);
    }

    /**
     * Renders a select box for a specific category.
     *
     * @param string $path the path to start from.
     * @param string $selUpload Selected upload, e.g. `idupl:<idupl>,iddbfs:<iddbfs>`.
     *      Single value or comma separated values.
     * @param array $parameter Additional parameter as follows:
     *      [
     *          'optionLabel' => (string) Label for the first option.
     *          'noFirstOption' => (bool) Flag to not render the first option. Default `false`.
     *          'directoryIsSelectable' => (bool) Flag to enable selection of folder. Default `true`.
     *          'filterEmptyDirectory' => (bool) Flag to filter empty folder. Default `false`.
     *          'fileTypes' => (array) List of file types to filter for, e.g. `['png','jpg','gif']`. Default `[]`.
     *      ]
     * @return string
     * @throws \cDbException
     * @throws \cException
     * @throws \cInvalidArgumentException
     */
    public function render(
        string $path, string $selUpload, array $parameter = []
    ): string
    {
        $this->initializeSelect($parameter);

        $selUpload = explode(self::VALUES_DELIMITER, $selUpload);

        if ($path === '/') {
            $path = '';
        }

        $numRecords = $this->fetchUploadFiles($path);
        $numRecords += $this->fetchDbfsFiles($path);

        if ($numRecords == 0) {
            $this->select->setDisabled(true);
        }

        if ($numRecords > 0) {
            $folderSymbol = $this->getFolderSymbol();

            // Upload directory
            $option = new \cHTMLOptionElement($folderSymbol . ' ' . i18n("Upload directory"), '');
            $option->setClass('mp_dev_tools_option_optgroup')
                ->setDisabled(true);
            $this->select->appendOptionElement($option);
            $this->fillOptions($this->uploadFiles, $selUpload);

            // Database file system
            $option = new \cHTMLOptionElement($folderSymbol . ' ' . i18n("Database file system"), '');
            $option->setClass('mp_dev_tools_option_optgroup')
                ->setDisabled(true);
            $this->select->appendOptionElement($option);
            $this->fillOptions($this->dbfsFiles, $selUpload);
        }

        return parent::renderBase() . $this->select->render();
    }

    /**
     * Returns the selected values.
     *
     * @param CmsToken|string $value CmsToken instance, or the token value.
     * @return array List of values where each item is
     *      `['idupl' => (int)] or ['iddbfs' => (int)]`.
     */
    public static function getSelectedValues($value): array
    {
        $rawValue = self::getSelectedRawValue($value);
        $return = [];

        // `idupl:<idupl>,iddbfs:<iddbfs>`
        $values = explode(self::VALUES_DELIMITER, $rawValue);
        foreach ($values as $item) {
            $itemIdValues = explode(self::ITEM_ID_VALUES_DELIMITER, $rawValue);
            if (count($itemIdValues) === 2) {
                if ($itemIdValues[0] === 'idupl') {
                    $return[] = [
                        'idupl' => \cSecurity::toInteger($itemIdValues[1]),
                    ];
                } elseif ($itemIdValues[0] === 'iddbfs') {
                    $return[] = [
                        'idupl' => \cSecurity::toInteger($itemIdValues[1]),
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * Fills the select box with options by using the passed files.
     *
     * @param array $files
     * @param array $selUpload
     * @return void
     */
    private function fillOptions(array $files, array $selUpload)
    {
        $folderSymbol = $this->getFolderSymbol();
        $filterEmptyDirectory = $this->getParameter('filterEmptyDirectory', false);

        // Get directories and loop through them
        $dirItems = $this->filterDirItems($files);
        foreach ($dirItems as $dirItem) {
            // Get files in directory
            $fileItems = $this->filterFileItems($files, $dirItem->dirName);
            if ($filterEmptyDirectory && empty($fileItems)) {
                // Don't render empty folder
                continue;
            }

            // Render directory option
            $option = $this->createOptionItem($dirItem, $selUpload, $folderSymbol);
            $this->select->appendOptionElement($option);

            // Render options for files within current directory
            foreach ($fileItems as $fileItem) {
                $option = $this->createOptionItem($fileItem, $selUpload, $folderSymbol);
                $this->select->appendOptionElement($option);
            }
        }

        // Render options for files in root directory
        $fileItems = $this->filterFileItems($files, '/');
        foreach ($fileItems as $fileItem) {
            $option = $this->createOptionItem($fileItem, $selUpload, $folderSymbol);
            $this->select->appendOptionElement($option);
        }
    }

    /**
     * Creates a single option item.
     *
     * @param \stdClass $item
     * @param array $selUpload
     * @param string $folderSymbol
     * @return \cHTMLOptionElement
     */
    private function createOptionItem(
        \stdClass $item, array $selUpload, string $folderSymbol
    ): \cHTMLOptionElement
    {
        $cssClasses = [];
        $styles = [];
        $disabled = false;

        $level = count(explode('/', $item->cleanPath));

        $symbol = '';
        if ($item->isDir) {
            $symbol = $folderSymbol;
            if (!$this->getParameter('directoryIsSelectable', true)) {
                $disabled = true;
            }
        } elseif ($item->isFile) {
            $level++;
        } else {
            $cssClasses[] = 'mp_dev_tools_color_warn';
        }

        $indent = $this->getSpacer($level);

        $title = $indent . $symbol . $item->displayName;
        $option = new \cHTMLOptionElement($title, $item->identifier);

        if (!empty($styles)) {
            $option->setStyle(implode('', $styles));
        }
        if (!empty($cssClasses)) {
            $option->setClass(implode(' ', $cssClasses));
        }
        if ($disabled) {
            $option->setDisabled(true);
        } elseif (in_array($item->identifier, $selUpload)) {
            $option->setSelected(true);
        }

        return $option;
    }

    /**
     * Checks if passed upload item is a directory.
     *
     * @param string $path
     * @param \stdClass $item
     * @return bool
     */
    private function isDir(string $path, \stdClass $item): bool
    {
        if ($item->type === self::UPLOAD_KEY) {
            return \cDirHandler::exists($path);
        } else {
            // Dbfs item is a folder when filename is empty
            return empty($item->fileName);
        }
    }

    /**
     * Checks if passed upload item is a file.
     *
     * @param string $path
     * @param \stdClass $item
     * @return bool
     */
    private function isFile(string $path, \stdClass $item): bool
    {
        if ($item->type === self::UPLOAD_KEY) {
            return \cFileHandler::isFile($path);
        } else {
            // Dbfs item is a file when filename is not empty
            return !empty($item->fileName);
        }
    }

    /**
     * Fetch upload files from the database.
     *
     * @param string $path The path to filter for.
     * @return int Number of found records.
     */
    private function fetchUploadFiles(string $path): int
    {
        $this->uploadFiles = [];
        $files = [];
        $uploadPath = $this->clientInfo->getUploadPath();

        $whereSql = '';
        if (!empty($path)) {
            $whereSql .= " AND
                upl.dirname LIKE '" . $this->db->escape($path) . "%'
            ";
        }

        $fileTypes = $this->getParameter('fileTypes', []);
        if (count($fileTypes) > 0) {
            $fileTypes = array_map(function ($item) {
                return $this->db->escape($item);
            }, $fileTypes);
            $whereSql .= " AND
                (
                    upl.filetype IN ('" . implode("','", $fileTypes) . "') OR
                    upl.filetype IN ('', NULL)
                )
            ";
        }

        $comment = '-- ' . __CLASS__ . '->' . __FUNCTION__ . '()';
        $sql = $comment . "
            SELECT
                upl.idupl, 
                upl.filename,
                upl.dirname,
                upl.filetype,
                upl.size
            FROM
                 " . \cRegistry::getDbTableName('upl') . " AS upl
            WHERE
                upl.idclient = " . $this->clientId . " AND
                upl.dirname NOT LIKE '" . \cApiDbfs::PROTOCOL_DBFS . "%'" . $whereSql . "
            ORDER BY
                upl.dirname,
                upl.filename
        ";

        $this->db->query($sql);

        $numRecords = $this->db->numRows();

        while ($this->db->nextRecord()) {
            $idupl = \cSecurity::toInteger($this->db->f('idupl'));
            $dirName = $this->db->f('dirname');
            $fileName = $this->db->f('filename');

            if ($dirName === '/') {
                $dirName = '';
            }

            $dirName = '/' . $dirName;
            $path = $dirName . $fileName;
            $cleanPath = trim($path, '/');

            $item = (object) [
                'idupl' => $idupl,
                'type' => self::UPLOAD_KEY,
                'identifier' => self::UPLOAD_KEY . self::ITEM_ID_VALUES_DELIMITER . $idupl,
                'dirName' => $dirName,
                'fileName' => $fileName,
                'displayName' => $fileName,
                'cleanPath' => $cleanPath,
            ];

            $item->isDir = $this->isDir($uploadPath . $cleanPath, $item);
            if (!$item->isDir) {
                $item->isFile = $this->isFile($uploadPath . $cleanPath, $item);
            } else {
                $item->isFile = false;
                $dirName = trim($dirName, '/');
                $dirName = empty($dirName) ? '/' : '/' . $dirName . '/';
                if (!empty(trim($fileName, '/'))) {
                    $dirName .= trim($fileName, '/') . '/';
                }
                $item->dirName = $dirName;
            }

            if (!isset($files[$dirName])) {
                $files[$dirName] = [];
            }
            $files[$dirName][] = $item;
        }

        foreach ($files as $dirName => $paths) {
            foreach ($paths as $item) {
                $this->uploadFiles[] = $item;
            }
        }

        return $numRecords;
    }

    /**
     * Fetch dbfs files from the database.
     *
     * @param string $path The path to filter for.
     * @return int Number of found records.
     */
    private function fetchDbfsFiles(string $path): int
    {
        $this->dbfsFiles = [];
        $files = [];
        $uploadPath = $this->clientInfo->getUploadPath();

        $whereSql = '';
        $path = \cApiDbfs::stripPath($path);
        if (!empty($path)) {
            $whereSql .= " AND
                dbfs.dirname LIKE '" . $this->db->escape($path) . "%'
            ";
        }

        $fileTypes = $this->getParameter('fileTypes', []);
        if (count($fileTypes) > 0) {
            $fileTypes = array_map(function ($item) {
                return $this->db->escape($item);
            }, $fileTypes);
            $fileTypeSql = [];
            foreach ($fileTypes as $fileType) {
                $fileTypeSql[] = "dbfs.filename LIKE '%.{$fileType}'";
            }
            $whereSql .= " AND
                (
                    (" . implode(' OR ', $fileTypeSql) . ") OR
                    dbfs.filename IN ('', NULL)
                )
            ";
        }

        $comment = '-- ' . __CLASS__ . '->' . __FUNCTION__ . '()';
        $sql = $comment . "
            SELECT
                dbfs.iddbfs, 
                dbfs.filename,
                dbfs.dirname,
                dbfs.mimetype,
                dbfs.size
            FROM
                 " . \cRegistry::getDbTableName('dbfs') . " AS dbfs
            WHERE
                dbfs.idclient = " . $this->clientId . $whereSql . "
            ORDER BY
                dbfs.dirname,
                dbfs.filename
        ";

        $this->db->query($sql);

        $numRecords = $this->db->numRows();

        while ($this->db->nextRecord()) {
            $iddbfs = \cSecurity::toInteger($this->db->f('iddbfs'));
            $dirName = $this->db->f('dirname');
            $fileName = $this->db->f('filename');

            if ($fileName === '.') {
                $fileName = '';
            }

            $path = $dirName . '/' . $fileName;

            $dirName = empty($dirName) ? '/' : '/' . $dirName . '/';

            $displayName = $fileName;

            // Dbfs item has no filename, if it's a directory, extract the name from directory.
            if (empty($fileName)) {
                $dirParts = explode('/', trim($dirName, '/'));
                $displayName = array_pop($dirParts);
            }

            $cleanPath = trim(\cApiDbfs::stripProtocol($path), '/');

            if (!isset($files[$dirName])) {
                $files[$dirName] = [];
            }

            $item = (object) [
                'iddbfs' => $iddbfs,
                'type' => self::DBFS_KEY,
                'identifier' => self::DBFS_KEY . self::ITEM_ID_VALUES_DELIMITER . $iddbfs,
                'dirName' => $dirName,
                'fileName' => $fileName,
                'displayName' => $displayName,
                'cleanPath' => $cleanPath,
            ];

            $item->isDir = $this->isDir($uploadPath . $cleanPath, $item);
            if (!$item->isDir) {
                $item->isFile = $this->isFile($uploadPath . $cleanPath, $item);
            } else {
                $item->isFile = false;
            }

            $files[$dirName][] = $item;
        }

        foreach ($files as $dirName => $paths) {
            foreach ($paths as $item) {
                $this->dbfsFiles[] = $item;
            }
        }

        return $numRecords;
    }

    /**
     * Returns the entries of type directory.
     *
     * @param array $fileItems
     * @return array
     */
    private function filterDirItems(array $fileItems): array
    {
        $filter = [];

        foreach ($fileItems as $item) {
            if ($item->isDir) {
                $filter[$item->dirName] = $item;
            }
        }

        return $filter;
    }

    /**
     * Returns the entries of type file or not directory.
     * An entry which is whether file nor directory is most likely a file,
     * where a record in the database exists but the file itself was deleted
     * from the file system.
     *
     * @param array $fileItems
     * @param string $dir
     * @return array
     */
    private function filterFileItems(array $fileItems, string $dir): array
    {
        $filter = [];

        foreach ($fileItems as $item) {
            if (($item->isFile || !$item->isDir) && $item->dirName === $dir) {
                $filter[] = $item;
            }
        }

        return $filter;
    }

}