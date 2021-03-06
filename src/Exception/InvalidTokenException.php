<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Exception;

use RuntimeException;

/**
 * Base exception when token is fetched, but invalid.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class InvalidTokenException extends RuntimeException
{
}
