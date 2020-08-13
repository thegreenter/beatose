<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;

class ApplicationResponse
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var DateTimeInterface
     */
    private $fechaRecepcion;
    /**
     * @var DateTimeInterface
     */
    private $fechaGeneracion;
    /**
     * @var string
     */
    private $rucEmisorCdr;
    /**
     * @var string
     */
    private $rucEmisorCpe;
    /**
     * @var string
     */
    private $tipoDocReceptorCpe;
    /**
     * @var string
     */
    private $nroDocReceptorCpe;
    /**
     * @var string
     */
    private $CpeId;
    /**
     * @var string
     */
    private $codigoRespuesta;
    /**
     * @var string
     */
    private $descripcionRespuesta;
    /**
     * @var string[]
     */
    private $notasAsociadas;

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return ApplicationResponse
     */
    public function setId(?string $id): ApplicationResponse
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getFechaRecepcion(): ?DateTimeInterface
    {
        return $this->fechaRecepcion;
    }

    /**
     * @param DateTimeInterface $fechaRecepcion
     * @return ApplicationResponse
     */
    public function setFechaRecepcion(?DateTimeInterface $fechaRecepcion): ApplicationResponse
    {
        $this->fechaRecepcion = $fechaRecepcion;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getFechaGeneracion(): ?DateTimeInterface
    {
        return $this->fechaGeneracion;
    }

    /**
     * @param DateTimeInterface $fechaGeneracion
     * @return ApplicationResponse
     */
    public function setFechaGeneracion(?DateTimeInterface $fechaGeneracion): ApplicationResponse
    {
        $this->fechaGeneracion = $fechaGeneracion;
        return $this;
    }

    /**
     * @return string
     */
    public function getRucEmisorCdr(): ?string
    {
        return $this->rucEmisorCdr;
    }

    /**
     * @param string $rucEmisorCdr
     * @return ApplicationResponse
     */
    public function setRucEmisorCdr(?string $rucEmisorCdr): ApplicationResponse
    {
        $this->rucEmisorCdr = $rucEmisorCdr;
        return $this;
    }

    /**
     * @return string
     */
    public function getRucEmisorCpe(): ?string
    {
        return $this->rucEmisorCpe;
    }

    /**
     * @param string $rucEmisorCpe
     * @return ApplicationResponse
     */
    public function setRucEmisorCpe(?string $rucEmisorCpe): ApplicationResponse
    {
        $this->rucEmisorCpe = $rucEmisorCpe;
        return $this;
    }

    /**
     * @return string
     */
    public function getTipoDocReceptorCpe(): ?string
    {
        return $this->tipoDocReceptorCpe;
    }

    /**
     * @param string $tipoDocReceptorCpe
     * @return ApplicationResponse
     */
    public function setTipoDocReceptorCpe(?string $tipoDocReceptorCpe): ApplicationResponse
    {
        $this->tipoDocReceptorCpe = $tipoDocReceptorCpe;
        return $this;
    }

    /**
     * @return string
     */
    public function getNroDocReceptorCpe(): ?string
    {
        return $this->nroDocReceptorCpe;
    }

    /**
     * @param string $nroDocReceptorCpe
     * @return ApplicationResponse
     */
    public function setNroDocReceptorCpe(?string $nroDocReceptorCpe): ApplicationResponse
    {
        $this->nroDocReceptorCpe = $nroDocReceptorCpe;
        return $this;
    }

    /**
     * @return string
     */
    public function getCpeId(): ?string
    {
        return $this->CpeId;
    }

    /**
     * @param string $CpeId
     * @return ApplicationResponse
     */
    public function setCpeId(?string $CpeId): ApplicationResponse
    {
        $this->CpeId = $CpeId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodigoRespuesta(): ?string
    {
        return $this->codigoRespuesta;
    }

    /**
     * @param string $codigoRespuesta
     * @return ApplicationResponse
     */
    public function setCodigoRespuesta(?string $codigoRespuesta): ApplicationResponse
    {
        $this->codigoRespuesta = $codigoRespuesta;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescripcionRespuesta(): ?string
    {
        return $this->descripcionRespuesta;
    }

    /**
     * @param string $descripcionRespuesta
     * @return ApplicationResponse
     */
    public function setDescripcionRespuesta(?string $descripcionRespuesta): ApplicationResponse
    {
        $this->descripcionRespuesta = $descripcionRespuesta;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getNotasAsociadas(): ?array
    {
        return $this->notasAsociadas;
    }

    /**
     * @param string[] $notasAsociadas
     * @return ApplicationResponse
     */
    public function setNotasAsociadas(?array $notasAsociadas): ApplicationResponse
    {
        $this->notasAsociadas = $notasAsociadas;
        return $this;
    }
}