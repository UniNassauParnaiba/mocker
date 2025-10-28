<?php

namespace Alura\Leilao\Tests\Domain;

use DomainException;
use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class LeilaoTest extends TestCase
{
    public function testProporLanceEmLeilaoFinalizadoDeveLancarExcecao(): void
    {
        // Arrange
        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->finaliza();
        
        $usuario = new Usuario('João Silva');
        $lance = new Lance($usuario, 1000);

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Este leilão já está finalizado');
        
        // Act
        $leilao->recebeLance($lance);
    }

    #[dataProvider('dadosParaProporLances')]
    public function testProporLancesEmLeilaoDeveFuncionar(int $qtdEsperado, array $lances)
    {
        $leilao = new Leilao('Fiat 147 0KM');
        foreach ($lances as $lance) {
            $leilao->recebeLance($lance);
        }

        self::assertCount($qtdEsperado, $leilao->getLances());
    }

    public function testMesmoUsuarioNaoPodeProporDoisLancesSeguidos()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Usuário já deu o último lance');
        $usuario = new Usuario('Ganancioso');

        $leilao = new Leilao('Objeto inútil');

        $leilao->recebeLance(new Lance($usuario, 1000));
        $leilao->recebeLance(new Lance($usuario, 1100));
    }

    public static function dadosParaProporLances()
    {
        $usuario1 = new Usuario('Usuário 1');
        $usuario2 = new Usuario('Usuário 2');
        return [
            [1, [new Lance($usuario1, 1000)]],
            [2, [new Lance($usuario1, 1000), new Lance($usuario2, 2000)]],
        ];
    }
}
