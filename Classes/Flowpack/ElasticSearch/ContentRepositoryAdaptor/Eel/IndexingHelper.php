<?php
namespace Flowpack\ElasticSearch\ContentRepositoryAdaptor\Eel;

/*                                                                                                  *
 * This script belongs to the TYPO3 Flow package "Flowpack.ElasticSearch.ContentRepositoryAdaptor". *
 *                                                                                                  *
 * It is free software; you can redistribute it and/or modify it under                              *
 * the terms of the GNU Lesser General Public License, either version 3                             *
 *  of the License, or (at your option) any later version.                                          *
 *                                                                                                  *
 * The TYPO3 project - inspiring people to share!                                                   *
 *                                                                                                  */

use Flowpack\ElasticSearch\ContentRepositoryAdaptor\Exception\IndexingException;
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Media\Domain\Model\AssetInterface;

/**
 * IndexingHelper
 */
class IndexingHelper implements ProtectedContextAwareInterface
{

    /**
     * Index an asset list or a single asset (by base64-encoding-it);
     * in the same manner as expected by the ElasticSearch "attachment"
     * core plugin.
     *
     * @param $value
     *
     * @return array|null|string
     * @throws IndexingException
     */
    public function indexAsset($value)
    {
        if (is_array($value)) {
            $result = [];
            foreach ($value as $element) {
                $result[] = $this->indexAsset($element);
            }
            return $result;
        } elseif (empty($value)) {
            return null;
        } elseif ($value instanceof AssetInterface) {
            $stream = $value->getResource()->getStream();
            stream_filter_append($stream, 'convert.base64-encode');
            $result = stream_get_contents($stream);
            return $result;
        } else {
            throw new IndexingException('Value of type ' . gettype($value) . ' - ' . get_class($value) . ' could not be converted to asset binary.', 1437555909);
        }
    }

    /**
     * All methods are considered safe
     *
     * @param string $methodName
     *
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
