<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeInterface;

class ApplicationResponse
{
    private ?string $id = null;
    private ?DateTimeInterface $fechaRecepcion = null;
    private ?DateTimeInterface $fechaGeneracion = null;
    private ?string $rucEmisorCdr = null;
    private ?string $rucEmisorCpe = null;
    private ?string $tipoDocReceptorCpe = null;
    private ?string $nroDocReceptorCpe = null;
    private ?string $CpeId = null;
    private ?string $codigoRespuesta = null;
    private ?string $descripcionRespuesta = null;
    /**
     * @var string[]
     */
    private ?array $notasAsociadas = null;

    /**
     * Filename without extension.
     */
    private ?string $filename = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return ApplicationResponse
     */
    public function setId(?string $id): ApplicationResponse
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFechaRecepcion(): ?DateTimeInterface
    {
        return $this->fechaRecepcion;
    }

    /**
     * @param DateTimeInterface|null $fechaRecepcion
     * @return ApplicationResponse
     */
    public function setFechaRecepcion(?DateTimeInterface $fechaRecepcion): ApplicationResponse
    {
        $this->fechaRecepcion = $fechaRecepcion;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getFechaGeneracion(): ?DateTimeInterface
    {
        return $this->fechaGeneracion;
    }

    /**
     * @param DateTimeInterface|null $fechaGeneracion
     * @return ApplicationResponse
     */
    public function setFechaGeneracion(?DateTimeInterface $fechaGeneracion): ApplicationResponse
    {
        $this->fechaGeneracion = $fechaGeneracion;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRucEmisorCdr(): ?string
    {
        return $this->rucEmisorCdr;
    }

    /**
     * @param string|null $rucEmisorCdr
     * @return ApplicationResponse
     */
    public function setRucEmisorCdr(?string $rucEmisorCdr): ApplicationResponse
    {
        $this->rucEmisorCdr = $rucEmisorCdr;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRucEmisorCpe(): ?string
    {
        return $this->rucEmisorCpe;
    }

    /**
     * @param string|null $rucEmisorCpe
     * @return ApplicationResponse
     */
    public function setRucEmisorCpe(?string $rucEmisorCpe): ApplicationResponse
    {
        $this->rucEmisorCpe = $rucEmisorCpe;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTipoDocReceptorCpe(): ?string
    {
        return $this->tipoDocReceptorCpe;
    }

    /**
     * @param string|null $tipoDocReceptorCpe
     * @return ApplicationResponse
     */
    public function setTipoDocReceptorCpe(?string $tipoDocReceptorCpe): ApplicationResponse
    {
        $this->tipoDocReceptorCpe = $tipoDocReceptorCpe;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNroDocReceptorCpe(): ?string
    {
        return $this->nroDocReceptorCpe;
    }

    /**
     * @param string|null $nroDocReceptorCpe
     * @return ApplicationResponse
     */
    public function setNroDocReceptorCpe(?string $nroDocReceptorCpe): ApplicationResponse
    {
        $this->nroDocReceptorCpe = $nroDocReceptorCpe;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCpeId(): ?string
    {
        return $this->CpeId;
    }

    /**
     * @param string|null $CpeId
     * @return ApplicationResponse
     */
    public function setCpeId(?string $CpeId): ApplicationResponse
    {
        $this->CpeId = $CpeId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodigoRespuesta(): ?string
    {
        return $this->codigoRespuesta;
    }

    /**
     * @param string|null $codigoRespuesta
     * @return ApplicationResponse
     */
    public function setCodigoRespuesta(?string $codigoRespuesta): ApplicationResponse
    {
        $this->codigoRespuesta = $codigoRespuesta;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescripcionRespuesta(): ?string
    {
        return $this->descripcionRespuesta;
    }

    /**
     * @param string|null $descripcionRespuesta
     * @return ApplicationResponse
     */
    public function setDescripcionRespuesta(?string $descripcionRespuesta): ApplicationResponse
    {
        $this->descripcionRespuesta = $descripcionRespuesta;
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getNotasAsociadas(): ?array
    {
        return $this->notasAsociadas;
    }

    /**
     * @param string[]|null $notasAsociadas
     * @return ApplicationResponse
     */
    public function setNotasAsociadas(?array $notasAsociadas): ApplicationResponse
    {
        $this->notasAsociadas = $notasAsociadas;
        return $this;
    }

    /**
     * Get Filename without extension.
     *
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Set Filename without extension.
     *
     * @param string|null $filename
     *
     * @return ApplicationResponse
     */
    public function setFilename(?string $filename): ApplicationResponse
    {
        $this->filename = $filename;
        return $this;
    }
}