<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A stock operation was rejected by a business rule (e.g. insufficient stock).
 * The message is safe to show to the user.
 */
class StockException extends RuntimeException {}
