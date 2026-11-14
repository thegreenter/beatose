<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Model\CpeCdrResult;
use App\Model\CpeDocument;
use App\Repository\CpeDocumentRepository;
use App\Services\File\FileStoreInterface;
use App\Services\Xml\HashExtract;
use DateTime;
use DOMDocument;
use function Safe\base64_decode;

class CdrBridge implements CdrOutputInterface
{
    /**
     * CdrBridge constructor.
     * @param AppCdrCreatorInterface $appCdrCreator
     * @param CdrWriterInterface $cdrWriter
     * @param HashExtract $hashExtractor
     * @param CpeDocumentRepository $repository
     * @param FileStoreInterface $fileStore
     */
    public function __construct(private AppCdrCreatorInterface $appCdrCreator, private CdrWriterInterface $cdrWriter, private HashExtract $hashExtractor, private CpeDocumentRepository $repository, private FileStoreInterface $fileStore)
    {
    }

    public function output(DOMDocument $document, CpeCdrResult $result): ?string
    {
        $appCdr = $this->appCdrCreator->create($document, $result);
        $cdr = $this->cdrWriter->write($appCdr);

        // Un resumen ya tiene su registro (creado por sendSummary) identificado por ticket.
        $cpe = $result->getTicket() !== null ? $this->repository->findOneByTicket($result->getTicket()) : null;
        $cpe ??= $this->repository->findOneByName($appCdr->getFilename()) ?? new CpeDocument();

        $cpe
            ->setName($cpe->getName() ?? $appCdr->getFilename())
            ->setStateCode($result->getCodeResult())
            ->setHashCpe($this->hashExtractor->fromDoc($document))
            ->setHashCdr($this->hashExtractor->fromXml($cdr))
            ->setCreateDate(new DateTime())
            ->setTicket($result->getTicket());


        $this->fileStore->save($this->getHexHash($cpe->getHashCpe()).'.xml', $document->saveXML());
        $this->fileStore->save($this->getHexHash($cpe->getHashCdr()).'.xml', $cdr);

        $this->repository->save($cpe);

        return $cdr;
    }

    private function getHexHash(?string $hash): string
    {
        return bin2hex(base64_decode($hash));
    }
}