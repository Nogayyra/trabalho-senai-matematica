<div class="card mb-4">
    <div class="card-header">1. Operações com matrizes</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="acao" value="matriz">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="ordem">Tamanho</label>
                    <select class="form-select" id="ordem" name="ordem">
                        <option value="2" <?= ($ordem ?? 2) == 2 ? 'selected' : '' ?>>2 × 2</option>
                        <option value="3" <?= ($ordem ?? 2) == 3 ? 'selected' : '' ?>>3 × 3</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="operacao">Operação</label>
                    <select class="form-select" id="operacao" name="operacao">
                        <?php foreach (['somar' => 'Soma (A + B)', 'subtrair' => 'Subtração (A − B)', 'multiplicar' => 'Multiplicação (A × B)', 'escalar' => 'Multiplicação por escalar (k × A)', 'transposta' => 'Transposta de A', 'determinante' => 'Determinante de A', 'inversa' => 'Inversa de A'] as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= ($operacao ?? '') === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="escalar">Escalar k (só p/ escalar)</label>
                    <input class="form-control" type="number" step="any" id="escalar" name="escalar"
                           value="<?= htmlspecialchars($_POST['escalar'] ?? '2') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6>Matriz A</h6>
                    <?php for ($i = 0; $i < ($ordem ?? 2); $i++): ?>
                        <div class="d-flex gap-2 mb-2">
                        <?php for ($j = 0; $j < ($ordem ?? 2); $j++): ?>
                            <input class="form-control" type="number" step="any" required
                                   name="a[<?= $i ?>][<?= $j ?>]"
                                   value="<?= htmlspecialchars($_POST['a'][$i][$j] ?? ($i === $j ? '1' : '0')) ?>">
                        <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="col-md-6">
                    <h6>Matriz B</h6>
                    <?php for ($i = 0; $i < ($ordem ?? 2); $i++): ?>
                        <div class="d-flex gap-2 mb-2">
                        <?php for ($j = 0; $j < ($ordem ?? 2); $j++): ?>
                            <input class="form-control" type="number" step="any" required
                                   name="b[<?= $i ?>][<?= $j ?>]"
                                   value="<?= htmlspecialchars($_POST['b'][$i][$j] ?? ($i === $j ? '1' : '0')) ?>">
                        <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <button class="btn btn-primary mt-2" type="submit">Calcular</button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">2. Sistema linear (escalonamento)</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="acao" value="sistema">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="ordemSis">Tamanho</label>
                    <select class="form-select" id="ordemSis" name="ordemSis">
                        <option value="2" <?= ($ordemSis ?? 2) == 2 ? 'selected' : '' ?>>2 equações</option>
                        <option value="3" <?= ($ordemSis ?? 2) == 3 ? 'selected' : '' ?>>3 equações</option>
                    </select>
                </div>
            </div>
            <?php for ($i = 0; $i < ($ordemSis ?? 2); $i++): ?>
                <div class="d-flex gap-2 mb-2 align-items-center">
                <?php for ($j = 0; $j < ($ordemSis ?? 2); $j++): ?>
                    <input class="form-control" type="number" step="any" required
                           name="s[<?= $i ?>][<?= $j ?>]" placeholder="a<?= $i + 1 ?><?= $j + 1 ?>"
                           value="<?= htmlspecialchars($_POST['s'][$i][$j] ?? '') ?>">
                    <?php if ($j < ($ordemSis ?? 2) - 1): ?><span>·x<?= $j + 1 ?> +</span><?php else: ?><span>·x<?= $j + 1 ?> =</span><?php endif; ?>
                <?php endfor; ?>
                    <input class="form-control" type="number" step="any" required
                           name="t[<?= $i ?>]" placeholder="b<?= $i + 1 ?>"
                           value="<?= htmlspecialchars($_POST['t'][$i] ?? '') ?>">
                </div>
            <?php endfor; ?>
            <button class="btn btn-success mt-2" type="submit">Resolver sistema</button>
        </form>
    </div>
</div>
