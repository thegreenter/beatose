<?php

declare(strict_types=1);

namespace App\Services\Cdr;

use App\Entity\CpeDocument;
use App\Model\CpeCdrResult;
use App\Services\Xml\HashExtract;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;

class CdrBridge implements CdrOutputInterface
{
    private AppCdrCreatorInterface $appCdrCreator;

    private CdrWriterInterface $cdrWriter;

    private HashExtract $hashExtractor;

    private EntityManagerInterface $repository;

    /**
     * CdrBridge constructor.
     *
     * @param AppCdrCreatorInterface $appCdrCreator
     * @param CdrWriterInterface $cdrWriter
     * @param HashExtract $hashExtractor
     * @param EntityManagerInterface $repository
     */
    public function __construct(AppCdrCreatorInterface $appCdrCreator, CdrWriterInterface $cdrWriter, HashExtract $hashExtractor, EntityManagerInterface $repository)
    {
        $this->appCdrCreator = $appCdrCreator;
        $this->cdrWriter = $cdrWriter;
        $this->hashExtractor = $hashExtractor;
        $this->repository = $repository;
    }

    public function output(DOMDocument $document, CpeCdrResult $result): ?string
    {
        $appCdr = $this->appCdrCreator->create($document, $result);
        $cdr = $this->cdrWriter->write($appCdr);

        $cpe = $this->repository->getRepository(CpeDocument::class)
                            ->findOneByName($appCdr->getFilename());

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

        $document->save($cpe->getName().'.xml');
        file_put_contents('R-'.$cpe->getName().'.xml', $cdr);

        $this->repository->persist($cpe);
        $this->repository->flush();

        return $cdr;
    }
}