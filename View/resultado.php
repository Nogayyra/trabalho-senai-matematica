<?php

function imprimirMatriz(array $matriz): void
{
    echo '<table class="table table-bordered text-center matriz">';
    foreach ($matriz as $linha) {
        echo '<tr>';
        foreach ($linha as $valor) {
            echo '<td>' . htmlspecialchars(formatarNumero((float) $valor)) . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}

function formatarNumero(float $valor): string
{
    $texto = number_format(round($valor, 4), 4, ',', '.');
    return rtrim(rtrim($texto, '0'), ',') ?: '0';
}
?>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<?php if (!empty($resultadoMatriz)): ?>
    <div class="card mb-4">
        <div class="card-header">Resultado — <?= htmlspecialchars($tituloResultado ?? 'operação') ?></div>
        <div class="card-body"><?php imprimirMatriz($resultadoMatriz); ?></div>
    </div>
<?php endif; ?>

<?php if ($numeroResultado !== null): ?>
    <div class="alert alert-info">Determinante = <strong><?= htmlspecialchars(formatarNumero($numeroResultado)) ?></strong>
    </div>
<?php endif; ?>

<?php if (!empty($resultadoSistema)): ?>
    <div class="card mb-4">
        <div class="card-header">Solução do sistema</div>
        <div class="card-body">
            <p><strong><?= htmlspecialchars($descricaoSistema) ?></strong></p>
            <?php if (!empty($resultadoSistema['solucao'])): ?>
                <ul class="mb-0">
                    <?php foreach ($resultadoSistema['solucao'] as $i => $valor): ?>
                        <li>x<?= $i + 1 ?> = <?= htmlspecialchars(formatarNumero((float) $valor)) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>