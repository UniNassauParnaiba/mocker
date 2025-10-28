<?php

namespace Alura\Leilao\Model;

class Leilao
{
    private array $lances = [];
    private bool $finalizado = false;

    public function __construct(
        private readonly string $descricao,
        private readonly \DateTimeImmutable $dataInicio = new \DateTimeImmutable(),
        private readonly ?int $id = null
    ) {}

    public function recebeLance(Lance $lance): void
    {
        if ($this->finalizado) {
            throw new \DomainException('Este leilão já está finalizado');
        }

        $ultimoLance = empty($this->lances)
            ? null
            : $this->lances[array_key_last($this->lances)];
        
        if ($ultimoLance?->getUsuario() == $lance->getUsuario()) {
            throw new \DomainException('Usuário já deu o último lance');
        }

        $this->lances[] = $lance;
    }

    public function finaliza(): void
    {
        $this->finalizado = true;
    }

    public function getLances(): array
    {
        return $this->lances;
    }

    public function recuperarDescricao(): string
    {
        return $this->descricao;
    }

    public function estaFinalizado(): bool
    {
        return $this->finalizado;
    }

    public function recuperarDataInicio(): \DateTimeImmutable
    {
        return $this->dataInicio;
    }

    public function temMaisDeUmaSemana(): bool
    {
        $hoje = new \DateTimeImmutable();
        $intervalo = $this->dataInicio->diff($hoje);

        return $intervalo->days > 7;
    }

    public function recuperarId(): ?int
    {
        return $this->id;
    }
}