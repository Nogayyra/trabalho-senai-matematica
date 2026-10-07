<?php

declare(strict_types=1);

class MatrizController
{
    public function somar(array $a, array $b): array
    {
        $this->exigirMesmaDimensao($a, $b, 'soma');
        $resultado = [];
        foreach ($a as $i => $linha) {
            foreach ($linha as $j => $valor) {
                $resultado[$i][$j] = $valor + $b[$i][$j];
            }
        }
        return $resultado;
    }

    public function subtrair(array $a, array $b): array
    {
        $this->exigirMesmaDimensao($a, $b, 'subtração');
        $resultado = [];
        foreach ($a as $i => $linha) {
            foreach ($linha as $j => $valor) {
                $resultado[$i][$j] = $valor - $b[$i][$j];
            }
        }
        return $resultado;
    }

    public function multiplicar(array $a, array $b): array
    {
        $this->exigirRetangular($a, 'A');
        $this->exigirRetangular($b, 'B');
        if (count($a[0]) !== count($b)) {
            throw new InvalidArgumentException(
                'Multiplicação impossível: colunas de A (' . count($a[0]) . ') ≠ linhas de B (' . count($b) . ').'
            );
        }
        $resultado = [];
        for ($i = 0; $i < count($a); $i++) {
            for ($j = 0; $j < count($b[0]); $j++) {
                $soma = 0.0;
                for ($k = 0; $k < count($b); $k++) {
                    $soma += $a[$i][$k] * $b[$k][$j];
                }
                $resultado[$i][$j] = $soma;
            }
        }
        return $resultado;
    }

    public function multiplicarPorEscalar(array $matriz, float $escalar): array
    {
        $this->exigirRetangular($matriz, 'matriz');
        $resultado = [];
        foreach ($matriz as $i => $linha) {
            foreach ($linha as $j => $valor) {
                $resultado[$i][$j] = $valor * $escalar;
            }
        }
        return $resultado;
    }

    public function transposta(array $matriz): array
    {
        $this->exigirRetangular($matriz, 'matriz');
        $resultado = [];
        foreach ($matriz as $i => $linha) {
            foreach ($linha as $j => $valor) {
                $resultado[$j][$i] = $valor;
            }
        }
        ksort($resultado);
        return array_values($resultado);
    }

    public function identidade(int $n): array
    {
        if ($n < 1) {
            throw new InvalidArgumentException('A ordem da identidade deve ser maior que zero.');
        }
        $resultado = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $resultado[$i][$j] = ($i === $j) ? 1.0 : 0.0;
            }
        }
        return $resultado;
    }

    public function nula(int $linhas, int $colunas): array
    {
        if ($linhas < 1 || $colunas < 1) {
            throw new InvalidArgumentException('Linhas e colunas devem ser maiores que zero.');
        }
        return array_fill(0, $linhas, array_fill(0, $colunas, 0.0));
    }

    public function determinante(array $matriz): float
    {
        $this->exigirQuadrada($matriz);
        $n = count($matriz);
        $m = array_values(array_map('array_values', $matriz));
        $trocas = 0;

        for ($col = 0; $col < $n; $col++) {
            $pivo = $this->indicePivo($m, $col);
            if ($pivo === null) {
                return 0.0;
            }
            if ($pivo !== $col) {
                [$m[$col], $m[$pivo]] = [$m[$pivo], $m[$col]];
                $trocas++;
            }
            for ($i = $col + 1; $i < $n; $i++) {
                $fator = $m[$i][$col] / $m[$col][$col];
                for ($j = $col; $j < $n; $j++) {
                    $m[$i][$j] -= $fator * $m[$col][$j];
                }
            }
        }

        $det = ($trocas % 2 === 0) ? 1.0 : -1.0;
        for ($i = 0; $i < $n; $i++) {
            $det *= $m[$i][$i];
        }
        return $det == 0.0 ? 0.0 : $det;
    }

    public function inversa(array $matriz): array
    {
        $this->exigirQuadrada($matriz);
        $n = count($matriz);
        $m = array_values(array_map('array_values', $matriz));

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $m[$i][] = ($i === $j) ? 1.0 : 0.0;
            }
        }

        for ($col = 0; $col < $n; $col++) {
            $pivo = $this->indicePivo($m, $col);
            if ($pivo === null) {
                throw new InvalidArgumentException('Matriz singular: não possui inversa (determinante zero).');
            }
            if ($pivo !== $col) {
                [$m[$col], $m[$pivo]] = [$m[$pivo], $m[$col]];
            }
            $divisor = $m[$col][$col];
            for ($j = 0; $j < 2 * $n; $j++) {
                $m[$col][$j] /= $divisor;
            }
            for ($i = 0; $i < $n; $i++) {
                if ($i === $col) {
                    continue;
                }
                $fator = $m[$i][$col];
                for ($j = 0; $j < 2 * $n; $j++) {
                    $m[$i][$j] -= $fator * $m[$col][$j];
                }
            }
        }

        $inversa = [];
        for ($i = 0; $i < $n; $i++) {
            $inversa[$i] = array_slice($m[$i], $n);
        }
        return $inversa;
    }

    private function indicePivo(array $m, int $col): ?int
    {
        $melhor = null;
        $maior = 1e-12;
        for ($i = $col; $i < count($m); $i++) {
            if (abs($m[$i][$col]) > $maior) {
                $maior = abs($m[$i][$col]);
                $melhor = $i;
            }
        }
        return $melhor;
    }

    private function exigirRetangular(array $matriz, string $nome): void
    {
        if ($matriz === [] || !is_array($matriz[0] ?? null)) {
            throw new InvalidArgumentException("Matriz {$nome} vazia ou em formato inválido.");
        }
        $colunas = count($matriz[0]);
        foreach ($matriz as $linha) {
            if (!is_array($linha) || count($linha) !== $colunas) {
                throw new InvalidArgumentException("Matriz {$nome} irregular: todas as linhas precisam ter o mesmo tamanho.");
            }
            foreach ($linha as $valor) {
                if (!is_numeric($valor)) {
                    throw new InvalidArgumentException("Matriz {$nome} com valor não numérico.");
                }
            }
        }
    }

    private function exigirQuadrada(array $matriz): void
    {
        $this->exigirRetangular($matriz, '');
        if (count($matriz) !== count($matriz[0])) {
            throw new InvalidArgumentException('Operação exige matriz quadrada (mesmo nº de linhas e colunas).');
        }
    }

    private function exigirMesmaDimensao(array $a, array $b, string $operacao): void
    {
        $this->exigirRetangular($a, 'A');
        $this->exigirRetangular($b, 'B');
        if (count($a) !== count($b) || count($a[0]) !== count($b[0])) {
            throw new InvalidArgumentException("{$operacao} impossível: dimensões diferentes.");
        }
    }
}
