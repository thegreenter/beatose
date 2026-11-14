<?php

declare(strict_types=1);

namespace App\Model;

final class ErrorCodeList
{
    public const INVALID_USER = '0102';
    public const TICKET_NOTFOUND = '0127';
    public const ZIP_INVALID_NAME = '0151';
    public const ZIP_IS_SUMMARY = '0152';
    public const ZIP_IS_PACK = '0153';
    public const ZIP_EMPTY = '0155';
    public const ZIP_CORRUPTO = '0156';
    public const ZIP_SIN_CPE = '0157';
    public const ZIP_TOO_MANY_CPE = '0158';
    public const XML_INVALID_NAME = '0159';
    public const XML_EMPTY = '0160';
    public const ZIP_ERROR_UNZIP = '0250';
    public const XML_NO_RAIZ = '0300';
    public const XML_NO_SCHEMA_FILE = '0304';
    public const XML_CANNOT_PROCESS = '0305';
    public const XML_NO_PARSE = '0306';
    public const XML_ALTERADO = '2334';
}