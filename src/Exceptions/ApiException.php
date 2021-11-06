<?php
/**
 * ApiException.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\Exceptions;

class ApiException extends \Exception
{
    protected string $errorCode;

    public function __construct(
        string $errorCode = '',
        string $errorMessage = '',
        int $httpResponseCode = 400,
        \Throwable $previous = null
    ) {
        parent::__construct($errorMessage, $httpResponseCode, $previous);

        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

}
