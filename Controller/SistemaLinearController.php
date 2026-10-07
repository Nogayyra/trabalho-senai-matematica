<?php

declare(strict_types=1);

class SistemaLinearController
{
    public function resolver(array $a, array $b): array
    {
        $n = count($a);
        if ($n === 0 || count($b) !== $n) {
            throw new InvalidArgumentException('Sistema inválido: A precisa ser n×n e b precisa ter n valores.');
        }
        foreach ($a as $linha) {
            if (!is_array($linha) || count($linha) !== $n) {
                throw new InvalidArgumentException('Sistema inválido: A precisa ser quadrada (n×n).');
            }
            foreach ($linha as $valor) {
                if (!is_numeric($valor)) {
                    throw new InvalidArgumentException('Sistema com valor não numérico em A.');
                }
            }
        }
        foreach ($b as $valor) {
            if (!is_numeric($valor)) {
                throw new InvalidArgumentException('Sistema com valor não numérico em b.');
            }
        }

        $m = [];
        for ($i = 0; $i < $n; $i++) {
            $m[$i] = array_values($a[$i]);
            $m[$i][] = (float) $b[$i];
        }

        $linha = 0;
        for ($col = 0; $col < $n && $linha < $n; $col++) {
            $pivo = $this->indicePivo($m, $linha, $col, $n);
            if ($pivo === null) {
                continue;
            }
            if ($pivo !== $linha) {
                [$m[$linha], $m[$pivo]] = [$m[$pivo], $m[$linha]];
            }
            for ($i = $linha + 1; $i < $n; $i++) {
                $fator = $m[$i][$col] / $m[$linha][$col];
                for ($j = $col; $j <= $n; $j++) {
                    $m[$i][$j] -= $fator * $m[$linha][$j];
                }
            }
            $linha++;
        }

        for ($i = 0; $i < $n; $i++) {
            $todaZero = true;
            for ($j = 0; $j < $n; $j++) {
                if (abs($m[$i][$j]) > 1e-9) {
                    $todaZero = false;
                    break;
                }
            }
            if ($todaZero && abs($m[$i][$n]) > 1e-9) {
                return ['tipo' => 'impossivel', 'solucao' => null];
            }
        }

        $posto = 0;
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if (abs($m[$i][$j]) > 1e-9) {
                    $posto++;
                    break;
                }
            }
        }
        if ($posto < $n) {
            return ['tipo' => 'indeterminado', 'solucao' => null];
        }

        $x = array_fill(0, $n, 0.0);
        for ($i = $n - 1; $i >= 0; $i--) {
            $soma = $m[$i][$n];
            for ($j = $i + 1; $j < $n; $j++) {
                $soma -= $m[$i][$j] * $x[$j];
            }
            $x[$i] = $soma / $m[$i][$i];
            if ($x[$i] == 0.0) {
                $x[$i] = 0.0;
            }
        }

        return ['tipo' => 'determinado', 'solucao' => $x];
    }

    public function descreverTipo(string $tipo): string
    {
        return match ($tipo) {
            'determinado' => 'Sistema Possível Determinado (SPD): uma solução única.',
            'indeterminado' => 'Sistema Possível Indeterminado (SPI): infinitas soluções.',
            'impossivel' => 'Sistema Impossível (SI): nenhuma solução.',
            default => 'Classificação desconhecida.',
        };
    }

    private function indicePivo(array $m, int $inicio, int $col, int $n): ?int
    {
        $melhor = null;
        $maior = 1e-12;
        for ($i = $inicio; $i < $n; $i++) {
            if (abs($m[$i][$col]) > $maior) {
                $maior = abs($m[$i][$col]);
                $melhor = $i;
            }
        }
        return $melhor;
    }
}
