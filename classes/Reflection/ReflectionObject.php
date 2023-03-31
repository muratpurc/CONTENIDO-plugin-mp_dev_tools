<?php

/**
 * ReflectionObject.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Reflection;

/**
 * ReflectionObject class.
 */
class ReflectionObject extends \ReflectionObject
{

    /**
     * Returns magic properties extracted from doc comment block.
     *
     * @param string|null $filter Filter Read/Write properties.
     *
     * @return MagicProperty[]
     */
    public function getMagicProperties(string $filter = null): array
    {
        $properties = [];

        $docComment = $this->getDocComment();
        if ($docComment) {
            $properties = $this->extractMagicPropertiesFromComment($docComment, $filter);
        }

        $parentClass = $this->getParentClass();
        while ($parentClass) {
            try {
                $reflectionClass = new \ReflectionClass($parentClass->getName());
                $docComment = $reflectionClass->getDocComment();
                if ($docComment) {
                    $properties += $this->extractMagicPropertiesFromComment($docComment, $filter);
                }
                $parentClass = $reflectionClass->getParentClass();
            } catch (\ReflectionException $e) {
                $parentClass = false;
            }
        }

        return $properties;
    }

    protected function extractMagicPropertiesFromComment(string $docComment, string $filter = null): array
    {
        $properties = [];

        if (empty($docComment)) {
            return $properties;
        }

        // The regex below has one drawback, it can't extract the whole description
        // of a property annotation.
        // Pattern is: (annotation) (type) ($name) (description)
        $pattern = "#(@property-read|@property-write|@property)\s*([\w-]+)\s*([\$\S]+)\s*([a-z0-9, \[\].()_].*)#i";
        preg_match_all($pattern, $docComment, $matches, PREG_PATTERN_ORDER);

        if ($matches) {
            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                // Get attribute (read/write)
                $attribute = $this->getMagicPropertyAttribute($matches[1][$i]);

                // Is property to filter?
                if (!$this->isMagicPropertyToFilter($attribute, $filter)) {
                    $type = $matches[2][$i] ?? '';
                    $name = $matches[3][$i] ?? '';
                    $description = $matches[4][$i] ?? '';
                    $properties[] = new MagicProperty($attribute, $type, $name, $description);
                }
            }
        }

        return $properties;
    }

    private function getMagicPropertyAttribute(string $annotation): string
    {
        if ($annotation === '@property-read') {
            return MagicProperty::READ;
        } elseif ($annotation === '@property-write') {
            return MagicProperty::WRITE;
        } else {
            return '';
        }
    }

    private function isMagicPropertyToFilter(string $attribute, $filter): bool
    {
        if ($filter === MagicProperty::READ && $attribute === MagicProperty::READ) {
            return false;
        } elseif ($filter === MagicProperty::WRITE && $attribute === MagicProperty::WRITE) {
            return false;
        } elseif ($filter === null) {
            return false;
        }

        return true;
    }

}