<?php

use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Dao\Leilao as LeilaoDao;

class LeilaoDaoMock extends LeilaoDao
{
    private array $leiloes = [];

    public function salva(Leilao $leilao) : void {
        $this->leiloes[] = $leilao;    
    }

    public function recuperarNaoFinalizados(): array {
        return array_filter($this->leiloes, function(Leilao $leilao){
            return !$leilao->estaFinalizado();
        });
    }
    
    public function recuperarFinalizados(): array {
        return array_filter($this->leiloes, function(Leilao $leilao){
            return $leilao->estaFinalizado();
        });
    }

    public function atualiza(Leilao $leilao){}
}

class EncerrandoTest extends TestCase
{

    public function testDeveEncerrarLeiloesQueDuramMaisDeUmaSemana()
    {
        $fiat147 = new Leilao(
            'Fiat 147 0km',
            new \DateTimeImmutable('8 days ago')
        );
        $variant = new Leilao(
            'Variant 1972 0km',
            new \DateTimeImmutable('10 days ago')
        );
        $leilaoDao = new LeilaoDaoMock();
        $leilaoDao->salva($fiat147);
        $leilaoDao->salva($variant);

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        // assertions
        $leiloes = $leilaoDao->recuperarFinalizados();
        self::assertCount(2, $leiloes);
        self::assertEquals('Fiat 147 0km', $leiloes[0]->recuperarDescricao());
        self::assertEquals('Variant 1972 0km', $leiloes[1]->recuperarDescricao());
    }

}
