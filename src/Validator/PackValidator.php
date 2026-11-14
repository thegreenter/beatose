<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\ErrorCodeList;
use App\Model\ValidationError;

/**
 * Reglas del contenido de un lote (sendPack) que se validan al recibirlo:
 * cantidad de comprobantes y nombre de cada XML.
 */
class PackValidator
{
    /** Tipo de documento en el nombre del ZIP: {RUC}-LT-{AAAAMMDD}-{correlativo}.zip */
    public const TYPE = 'LT';

    public const MAX_DOCUMENTS = 500;

    /**
     * {RUC}-{tipo}-{serie}-{número}.xml; tipos: factura, boleta, nota de crédito y de débito.
     */
    private const XML_NAME_PATTERN = '/^(\d{11})-(01|03|07|08)-[A-Z0-9]{4}-\d{1,8}\.xml$/i';

    /**
     * @param string $packFileName nombre del ZIP del lote
     * @param array<string, string> $files nombre de cada XML => contenido
     */
    public function validate(string $packFileName, array $files): ?ValidationError
    {
        if (count($files) > self::MAX_DOCUMENTS) {
            return new ValidationError(
                ErrorCodeList::ZIP_TOO_MANY_CPE,
                sprintf('El lote tiene %d comprobantes, máximo %d', count($files), self::MAX_DOCUMENTS),
            );
        }

        $ruc = explode('-', $packFileName)[0];
        foreach ($files as $name => $content) {
            $name = (string) $name;
            if (preg_match(self::XML_NAME_PATTERN, $name, $matches) !== 1 || $matches[1] !== $ruc) {
                return new ValidationError(ErrorCodeList::XML_INVALID_NAME, $name);
            }

            if (trim($content) === '') {
                return new ValidationError(ErrorCodeList::XML_EMPTY, $name);
            }
        }

        return null;
    }
}
