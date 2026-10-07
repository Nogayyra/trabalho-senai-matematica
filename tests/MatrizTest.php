<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Controller/MatrizController.php';

class MatrizTest extends TestCase
{
    private MatrizController $m;

    protected function setUp(): void
    {
        $this->m = new MatrizController();
    }

    private function assertMatriz(array $esperada, array $obtida, float $delta = 0.0001): void
    {
        $this->assertCount(count($esperada), $obtida);
        foreach ($esperada as $i => $linha) {
            foreach ($linha as $j => $valor) {
                $this->assertEqualsWithDelta($valor, $obtida[$i][$j], $delta);
            }
        }
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function soma_e_subtracao_com_resultado_conhecido(): void
    {
        $a = [[1, 2], [3, 4]];
        $b = [[5, 6], [7, 8]];

        $this->assertMatriz([[6, 8], [10, 12]], $this->m->somar($a, $b));
        $this->assertMatriz([[-4, -4], [-4, -4]], $this->m->subtrair($a, $b));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function multiplicacao_com_resultado_conhecido(): void
    {
        $a = [[1, 2], [3, 4]];
        $b = [[2, 0], [1, 2]];

        $this->assertMatriz([[4, 4], [10, 8]], $this->m->multiplicar($a, $b));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function escalar_e_transposta(): void
    {
        $this->assertMatriz([[2, 4], [6, 8]], $this->m->multiplicarPorEscalar([[1, 2], [3, 4]], 2.0));
        $this->assertMatriz([[1, 3], [2, 4]], $this->m->transposta([[1, 2], [3, 4]]));

        $this->assertMatriz([[1, 4], [2, 5], [3, 6]], $this->m->transposta([[1, 2, 3], [4, 5, 6]]));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function identidade_e_nula(): void
    {
        $this->assertMatriz([[1, 0], [0, 1]], $this->m->identidade(2));
        $this->assertMatriz([[1, 0, 0], [0, 1, 0], [0, 0, 1]], $this->m->identidade(3));
        $this->assertMatriz([[0, 0], [0, 0]], $this->m->nula(2, 2));

        $a = [[1, 2], [3, 4]];
        $this->assertMatriz($a, $this->m->multiplicar($a, $this->m->identidade(2)));

        $this->assertMatriz($a, $this->m->somar($a, $this->m->nula(2, 2)));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function determinante_casos_conhecidos(): void
    {
        $this->assertEqualsWithDelta(7.0, $this->m->determinante([[7]]), 0.0001);
        $this->assertEqualsWithDelta(-2.0, $this->m->determinante([[1, 2], [3, 4]]), 0.0001);
        $this->assertEqualsWithDelta(1.0, $this->m->determinante($this->m->identidade(3)), 0.0001);
        $this->assertEqualsWithDelta(0.0, $this->m->determinante([[1, 2], [2, 4]]), 0.0001);
        $this->assertEqualsWithDelta(-306.0, $this->m->determinante([[6, 1, 1], [4, -2, 5], [2, 8, 7]]), 0.0001);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function inversa_2x2_e_verificacao_a_vezes_inversa_igual_identidade(): void
    {
        $inversa = $this->m->inversa([[4, 7], [2, 6]]);
        $this->assertMatriz([[0.6, -0.7], [-0.2, 0.4]], $inversa);

        $produto = $this->m->multiplicar([[4, 7], [2, 6]], $inversa);
        $this->assertMatriz($this->m->identidade(2), $produto);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_dimensoes_incompativeis(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->m->somar([[1, 2], [3, 4]], [[1, 2, 3], [4, 5, 6]]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_multiplicacao_incompativel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->m->multiplicar([[1, 2, 3], [4, 5, 6]], [[1, 2], [3, 4]]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_inversa_de_matriz_singular(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->m->inversa([[1, 2], [2, 4]]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function rejeita_valores_nao_numericos(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->m->somar([['a', 'b'], ['c', 'd']], [[1, 2], [3, 4]]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function opera_com_negativos_e_zeros(): void
    {
        $this->assertMatriz([[0, 0], [0, 0]], $this->m->somar([[-1, 2], [3, -4]], [[1, -2], [-3, 4]]));
        $this->assertMatriz([[0, 0], [0, 0]], $this->m->multiplicar([[0, 0], [0, 0]], [[1, 2], [3, 4]]));
    }
}
