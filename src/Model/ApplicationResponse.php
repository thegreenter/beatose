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
     * @return static
     */
    public function setId(?string $id): static
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
     * @return static
     */
    public function setFechaRecepcion(?DateTimeInterface $fechaRecepcion): static
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
     * @return static
     */
    public function setFechaGeneracion(?DateTimeInterface $fechaGeneracion): static
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
     * @return static
     */
    public function setRucEmisorCdr(?string $rucEmisorCdr): static
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
     * @return static
     */
    public function setRucEmisorCpe(?string $rucEmisorCpe): static
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
     * @return static
     */
    public function setTipoDocReceptorCpe(?string $tipoDocReceptorCpe): static
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
     * @return static
     */
    public function setNroDocReceptorCpe(?string $nroDocReceptorCpe): static
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
     * @return static
     */
    public function setCpeId(?string $CpeId): static
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
     * @return static
     */
    public function setCodigoRespuesta(?string $codigoRespuesta): static
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
     * @return static
     */
    public function setDescripcionRespuesta(?string $descripcionRespuesta): static
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
     * @return static
     */
    public function setNotasAsociadas(?array $notasAsociadas): static
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
     * @return static
     */
    public function setFilename(?string $filename): static
    {
        $this->filename = $filename;
        return $this;
    }
}