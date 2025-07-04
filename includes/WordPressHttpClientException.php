<?php
/**
 * HTTP Client Exception for WordPress HTTP Client
 *
 * @package \WpAiContentGeneration
 */

declare(strict_types=1);

namespace WpAiContentGeneration;

use Psr\Http\Client\ClientExceptionInterface;

/**
 * WordPress HTTP Client Exception
 */
class WordPressHttpClientException extends \RuntimeException implements ClientExceptionInterface {
}
