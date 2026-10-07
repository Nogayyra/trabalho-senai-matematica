<?php

declare(strict_types=1);

require_once __DIR__ . '/Controller/MatrizController.php';
require_once __DIR__ . '/Controller/SistemaLinearController.php';

$matrizes = new MatrizController();
$sistemas = new SistemaLinearController();

$ordem = (int) ($_POST['ordem'] ?? 2);
$ordemSis = (int) ($_POST['ordemSis'] ?? 2);
$operacao = $_POST['operacao'] ?? '';
$erro = '';
$resultadoMatriz = null;
$tituloResultado = '';
$numeroResultado = null;
$resultadoSistema = null;
$descricaoSistema = '';

function montarMatriz(array $dados, int $ordem): array
{
    $matriz = [];
    for ($i = 0; $i < $ordem; $i++) {
        for ($j = 0; $j < $ordem; $j++) {
            $valor = $dados[$i][$j] ?? null;
            if ($valor === null || $valor === '' || !is_numeric($valor)) {
                throw new InvalidArgumentException('Preencha todos os campos com números.');
            }
            $matriz[$i][$j] = (float) $valor;
        }
    }
    return $matriz;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (($_POST['acao'] ?? '') === 'matriz') {
            $a = montarMatriz($_POST['a'] ?? [], $ordem);
            $b = montarMatriz($_POST['b'] ?? [], $ordem);

            switch ($operacao) {
                case 'somar':
                    $resultadoMatriz = $matrizes->somar($a, $b);
                    $tituloResultado = 'A + B';
                    break;
                case 'subtrair':
                    $resultadoMatriz = $matrizes->subtrair($a, $b);
                    $tituloResultado = 'A − B';
                    break;
                case 'multiplicar':
                    $resultadoMatriz = $matrizes->multiplicar($a, $b);
                    $tituloResultado = 'A × B';
                    break;
                case 'escalar':
                    $k = $_POST['escalar'] ?? null;
                    if (!is_numeric($k)) {
                        throw new InvalidArgumentException('Informe um valor numérico para o escalar k.');
                    }
                    $resultadoMatriz = $matrizes->multiplicarPorEscalar($a, (float) $k);
                    $tituloResultado = 'k × A';
                    break;
                case 'transposta':
                    $resultadoMatriz = $matrizes->transposta($a);
                    $tituloResultado = 'Transposta de A';
                    break;
                case 'determinante':
                    $numeroResultado = $matrizes->determinante($a);
                    break;
                case 'inversa':
                    $resultadoMatriz = $matrizes->inversa($a);
                    $tituloResultado = 'Inversa de A';
                    break;
            }
        } elseif (($_POST['acao'] ?? '') === 'sistema') {
            $a = montarMatriz($_POST['s'] ?? [], $ordemSis);
            $b = [];
            for ($i = 0; $i < $ordemSis; $i++) {
                $valor = $_POST['t'][$i] ?? null;
                if ($valor === null || $valor === '' || !is_numeric($valor)) {
                    throw new InvalidArgumentException('Preencha todos os termos independentes com números.');
                }
                $b[$i] = (float) $valor;
            }
            $resultadoSistema = $sistemas->resolver($a, $b);
            $descricaoSistema = $sistemas->descreverTipo($resultadoSistema['tipo']);
        }
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Álgebra Linear — Matrizes e Sistemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="templates/css/style.css">
</head>

<body>
    <header class="topo text-center">
        <h1>Álgebra Linear</h1>
        <p>Operações com matrizes e resolução de sistemas lineares por escalonamento</p>
    </header>

    <main class="container my-4">
        <?php require __DIR__ . '/View/resultado.php'; ?>
        <?php require __DIR__ . '/View/formulario.php'; ?>

        <div class="card mb-4">
            <div class="card-header">Como funciona</div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Escolha o tamanho (2×2 ou 3×3), preencha as matrizes e selecione a operação.</li>
                    <li>Sistemas lineares são resolvidos pelo método do escalonamento (eliminação de Gauss).</li>
                    <li>O sistema é classificado conforme o material do professor: SPD (uma solução), SPI (infinitas soluções) ou SI (sem solução).
                    </li>
                </ul>
            </div>
        </div>
    </main>

    <footer class="text-center pb-4">
        <small>Trabalho interárea — Álgebra Linear · Autor: Arthur · SENAI</small>
    </footer>
</body>

</html>