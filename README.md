# Álgebra Linear — Matrizes e Sistemas

Aplicação web em PHP para operações com matrizes (soma, subtração,
multiplicação, escalar, transposta, identidade, nula, determinante e
inversa) e resolução de sistemas lineares pelo método do escalonamento
(eliminação de Gauss), com classificação na nomenclatura ensinada pelo
professor Gabriel Sena: SPD, SPI ou SI.

**Autor:** Arthur — SENAI (trabalho interárea)

## Material de referência

- `[Matemática] [EM - 3º ano] - sistemas.pptx` (professor Gabriel Sena,
  cópia em `docs/anexos/`): equações, resolução de sistemas lineares,
  aplicações contextualizadas e classificação em Sistema Possível
  Determinado (SPD — uma solução), Sistema Possível Indeterminado
  (SPI — infinitas soluções) e Sistema Impossível (SI — nenhuma solução).
  O escalonamento (eliminação de Gauss) é o algoritmo computacional usado
  para resolver e classificar os sistemas.

## Tecnologias

- PHP >= 8.4 (requisito do `composer.json`; testado localmente no PHP 8.3.33 —
  validação final em PHP 8.4 pendente, pois não há PHP 8.4 instalado neste computador)
- HTML5 + CSS3 + Bootstrap 5 (somente CSS, sem JavaScript)
- Composer (autoload) + PHPUnit 12 (testes)
- Sem banco de dados, sem login, sem Node.js

## Estrutura

```text
algebra-linear/
├── Controller/
│   ├── MatrizController.php         # operações matriciais
│   └── SistemaLinearController.php  # escalonamento + classificação
├── View/
│   ├── formulario.php               # grades 2×2/3×3 e sistema
│   └── resultado.php                # matrizes e solução
├── templates/css/style.css
├── tests/
│   ├── MatrizTest.php
│   └── SistemaLinearTest.php
├── docs/
│   ├── relatorio-tecnico.docx
│   ├── anexos/                      # material do professor Gabriel Sena
│   └── evidencias/testes.txt
├── index.php
├── composer.json
└── phpunit.xml
```

## Instalação

```bash
cd algebra-linear
composer install
```

## Execução com Laravel Herd

1. Aponte o Herd para a pasta `algebra-linear/` (ou sirva com `php -S localhost:8000`);
2. Acesse `index.php` no navegador;
3. Escolha o tamanho (2×2 ou 3×3), preencha as matrizes e calcule;
4. Para sistemas, preencha os coeficientes e os termos independentes.

## Funcionamento

```text
formulário (POST) → index.php → Controller → algoritmo → View (resultado)
```

## Algoritmos

- `MatrizController`: soma, subtração, multiplicação, escalar, transposta,
  identidade, nula, determinante (eliminação de Gauss, qualquer ordem) e
  inversa (Gauss-Jordan; erro em matriz singular). Valida dimensões,
  matriz irregular e valores não numéricos.
- `SistemaLinearController::resolver()`: escalonamento com pivoteamento
  parcial + substituição reversa. Classifica em `determinado`
  (SPD — Sistema Possível Determinado, solução única), `indeterminado`
  (SPI — Sistema Possível Indeterminado, infinitas soluções) ou `impossivel`
  (SI — Sistema Impossível, sem solução).

## Testes

```powershell
vendor\bin\phpunit
```

Cobertura: todos os métodos públicos dos dois Controllers possuem testes
(casos felizes com resultado conhecido, bordas 1×1/identidade/nula,
erros de dimensão/singular/impossível/indeterminado e precisão com
`assertEqualsWithDelta`). Resultado atual, executado no PHP 8.3.33 com PHPUnit 12.5.38: **20 testes,
113 asserções, OK** (ver `docs/evidencias/testes.txt`). Não foi possível gerar
o relatório percentual de cobertura porque o ambiente não possui Xdebug/PCOV.

## Decisões importantes

- Um Controller por tema (não uma classe por operação) para manter
  o projeto pequeno e legível.
- Sem `Model/`: não há persistência; Controller + View bastam.
- Comparações de ponto flutuante usam tolerância (`1e-9` no código,
  `assertEqualsWithDelta` nos testes).
- O anexo do professor Gabriel Sena foi analisado e confirmou o conteúdo
  central (resolução e classificação SPD/SPI/SI); por isso o método de
  escalonamento (eliminação de Gauss) foi mantido, sem adicionar
  Cramer, Gauss-Jordan ou outros algoritmos.
