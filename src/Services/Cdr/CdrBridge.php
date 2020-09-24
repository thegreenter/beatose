<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Entity\CpeDocument;
use App\Model\CpeCdrResult;
use App\Repository\CpeDocumentRepository;
use App\Services\File\FileStoreInterface;
use App\Services\Xml\HashExtract;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;

class CdrBridge implements CdrOutputInterface
{
    private AppCdrCreatorInterface $appCdrCreator;

    private CdrWriterInterface $cdrWriter;

    private HashExtract $hashExtractor;

    private EntityManagerInterface $entityManager;

    private CpeDocumentRepository $repository;

    private FileStoreInterface $fileStore;

    /**
     * CdrBridge constructor.
     * @param AppCdrCreatorInterface $appCdrCreator
     * @param CdrWriterInterface $cdrWriter
     * @param HashExtract $hashExtractor
     * @param EntityManagerInterface $entityManager
     * @param CpeDocumentRepository $repository
     * @param FileStoreInterface $fileStore
     */
    public function __construct(AppCdrCreatorInterface $appCdrCreator, CdrWriterInterface $cdrWriter, HashExtract $hashExtractor, EntityManagerInterface $entityManager, CpeDocumentRepository $repository, FileStoreInterface $fileStore)
    {
        $this->appCdrCreator = $appCdrCreator;
        $this->cdrWriter = $cdrWriter;
        $this->hashExtractor = $hashExtractor;
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->fileStore = $fileStore;
    }

    public function output(DOMDocument $document, CpeCdrResult $result): ?string
    {
        $appCdr = $this->appCdrCreator->create($document, $result);
        $cdr = $this->cdrWriter->write($appCdr);

        $cpe = $this->repository->findOneByName($appCdr->getFilename());

        if ($cpe === null) {
            $cpe = new CpeDocument();
        }

        $cpe
            ->setName($appCdr->getFilename())
            ->setStateCode($result->getCodeResult())
            ->setHashCpe($this->hashExtractor->fromDoc($document))
            ->setHashCdr($this->hashExtractor->fromXml($cdr))
            ->setCreateDate(new DateTime())
            ->setTicket($result->getTicket())
        ;


        $this->fileStore->save($this->getHexHash($cpe->getHashCpe()).'.xml', $document->saveXML());
        $this->fileStore->save($this->getHexHash($cpe->getHashCdr()).'.xml', $cdr);

        $this->entityManager->persist($cpe);
        $this->entityManager->flush();

        return $cdr;
    }

    private function getHexHash(?string $hash)
    {
        return bin2hex(base64_decode($hash));
    }
}