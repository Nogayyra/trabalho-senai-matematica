<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Controller/SistemaLinearController.php';

class SistemaLinearTest extends TestCase
{
    private SistemaLinearController $s;

    protected function setUp(): void
    {
        $this->s = new SistemaLinearController();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function resolve_sistema_2x2_com_solucao_conhecida(): void
    {

        $resultado = $this->s->resolver([[1, 1], [2, -1]], [5, 1]);

        $this->assertSame('determinado', $resultado['tipo']);
        $this->assertEqualsWithDelta(2.0, $resultado['solucao'][0], 0.0001);
        $this->assertEqualsWithDelta(3.0, $resultado['solucao'][1], 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function resolve_sistema_3x3_com_solucao_conhecida(): void
    {

        $resultado = $this->s->resolver(
            [[2, 1, -1], [-3, -1, 2], [-2, 1, 2]],
            [8, -11, -3]
        );

        $this->assertSame('determinado', $resultado['tipo']);
        $this->assertEqualsWithDelta(2.0, $resultado['solucao'][0], 0.0001);
        $this->assertEqualsWithDelta(3.0, $resultado['solucao'][1], 0.0001);
        $this->assertEqualsWithDelta(-1.0, $resultado['solucao'][2], 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function resolve_sistema_que_precisa_de_pivo(): void
    {

        $resultado = $this->s->resolver([[0, 1], [1, 1]], [2, 3]);

        $this->assertSame('determinado', $resultado['tipo']);
        $this->assertEqualsWithDelta(1.0, $resultado['solucao'][0], 0.0001);
        $this->assertEqualsWithDelta(2.0, $resultado['solucao'][1], 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function detecta_sistema_impossivel(): void
    {

        $resultado = $this->s->resolver([[1, 1], [1, 1]], [1, 2]);

        $this->assertSame('impossivel', $resultado['tipo']);
        $this->assertNull($resultado['solucao']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function detecta_sistema_indeterminado(): void
    {

        $resultado = $this->s->resolver([[1, 1], [2, 2]], [2, 4]);

        $this->assertSame('indeterminado', $resultado['tipo']);
        $this->assertNull($resultado['solucao']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function resolve_com_negativos_e_zeros(): void
    {

        $resultado = $this->s->resolver([[-1, 2], [3, 0]], [4, 6]);

        $this->assertSame('determinado', $resultado['tipo']);
        $this->assertEqualsWithDelta(2.0, $resultado['solucao'][0], 0.0001);
        $this->assertEqualsWithDelta(3.0, $resultado['solucao'][1], 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_matriz_nao_quadrada(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->s->resolver([[1, 2, 3], [4, 5, 6]], [1, 2]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_entradas_nao_numericas(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->s->resolver([['a', 'b'], ['c', 'd']], [1, 2]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function descreve_os_tres_tipos(): void
    {
        $this->assertStringContainsString('SPD', $this->s->descreverTipo('determinado'));
        $this->assertStringContainsString('SPI', $this->s->descreverTipo('indeterminado'));
        $this->assertStringContainsString('SI', $this->s->descreverTipo('impossivel'));
    }
}
