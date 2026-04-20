<?php
/**
 * Script de seed para advogados e modelos de documentos.
 * Acessar uma vez via browser após o deploy. Idempotente (verifica antes de inserir).
 */
require_once __DIR__ . '/conexao.php';

$log = [];

// ── Advogados ────────────────────────────────────────────────────────────────

$advogados = [
    [
        'nome'      => 'EDSON SILVA SANTIAGO',
        'documento' => '22.162.240/0001-25',
        'oab'       => 'OAB/RR 619',
        'endereco'  => 'Rua Professor Agnelo Bittencourt, nº 335, Centro',
        'cidade'    => 'Boa Vista',
        'uf'        => 'RR',
        'fone'      => '',
        'email'     => '',
    ],
    [
        'nome'      => 'OSTIVALDO MENEZES DO NASCIMENTO JÚNIOR',
        'documento' => '22.162.240/0001-25',
        'oab'       => 'OAB/RR 1280',
        'endereco'  => 'Rua Professor Agnelo Bittencourt, nº 335, Centro',
        'cidade'    => 'Boa Vista',
        'uf'        => 'RR',
        'fone'      => '',
        'email'     => '',
    ],
];

$stmt = $conn->prepare("SELECT id FROM advogados WHERE nome = ? LIMIT 1");
$ins  = $conn->prepare("INSERT INTO advogados (nome,documento,oab,endereco,cidade,uf,fone,email) VALUES (?,?,?,?,?,?,?,?)");

foreach ($advogados as $adv) {
    $stmt->bind_param('s', $adv['nome']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $log[] = "Advogado já existe: {$adv['nome']}";
        continue;
    }
    $ins->bind_param('ssssssss',
        $adv['nome'], $adv['documento'], $adv['oab'],
        $adv['endereco'], $adv['cidade'], $adv['uf'],
        $adv['fone'], $adv['email']
    );
    $ins->execute();
    $log[] = "Advogado inserido: {$adv['nome']} (id={$conn->insert_id})";
}

// ── Modelos de documentos ────────────────────────────────────────────────────

$modelos = [];

// 1 — Procuração Administrativa (Padrão)
$modelos[] = [
    'nome'      => 'Procuração Administrativa (Padrão)',
    'categoria' => 'Procuração',
    'descricao' => 'Procuração para representação administrativa perante o INSS e demais órgãos',
    'conteudo'  => <<<'HTML'
<p style="text-align:center"><strong>PROCURAÇÃO</strong></p>

<p><strong>OUTORGANTE:</strong> {{cliente_nome}}, {{cliente_nacionalidade}}, {{cliente_estado_civil}}, {{cliente_profissao}}, portador(a) do CPF nº {{cliente_cpf}} e RG nº {{cliente_rg}}, residente e domiciliado(a) na {{cliente_endereco}}, {{cliente_cidade}}-{{cliente_uf}}, CEP {{cliente_cep}}.</p>

<p><strong>OUTORGADO:</strong> {{empresa_nome}}, inscrita no CNPJ sob o nº {{empresa_cnpj}}, com endereço na {{empresa_endereco}}, {{empresa_cidade}}, bem como o(a) Dr(a). {{advogado_1_nome}}, inscrito(a) na OAB sob o nº {{advogado_1_oab}}.</p>

<p><strong>PODERES:</strong> Pelo presente instrumento particular de procuração, o(a) outorgante nomeia e constitui seu bastante procurador o outorgado acima qualificado, a quem confere amplos e gerais poderes para em seu nome e por sua conta, representá-lo(la) perante o Instituto Nacional do Seguro Social – INSS e demais órgãos previdenciários, para requerer, receber, dar quitação, assinar requerimentos, recursos e demais documentos necessários ao reconhecimento e concessão de benefícios previdenciários e assistenciais, podendo ainda substabelecer esta com ou sem reserva de iguais poderes.</p>

<p>{{empresa_cidade}}, {{data_hoje_extenso}}.</p>

<br/><br/>

<p style="text-align:center">_____________________________________________<br/>
{{cliente_nome}}<br/>
CPF: {{cliente_cpf}}</p>
HTML,
];

// 2 — Procuração A Rogo
$modelos[] = [
    'nome'      => 'Procuração A Rogo',
    'categoria' => 'Procuração',
    'descricao' => 'Procuração assinada a rogo por terceiro em nome do outorgante',
    'conteudo'  => <<<'HTML'
<p style="text-align:center"><strong>PROCURAÇÃO A ROGO</strong></p>

<p><strong>OUTORGANTE:</strong> {{cliente_nome}}, {{cliente_nacionalidade}}, {{cliente_estado_civil}}, {{cliente_profissao}}, portador(a) do CPF nº {{cliente_cpf}}, residente e domiciliado(a) na {{cliente_endereco}}, {{cliente_cidade}}-{{cliente_uf}}, CEP {{cliente_cep}}, pessoa que não sabe ou não pode assinar, motivo pelo qual assina a seu rogo:</p>

<p><strong>ASSINANTE A ROGO:</strong> {{a_rogo_nome}}, portador(a) do CPF nº {{a_rogo_cpf}} e Identidade nº {{a_rogo_identidade}}.</p>

<p><strong>OUTORGADO:</strong> {{empresa_nome}}, inscrita no CNPJ sob o nº {{empresa_cnpj}}, com endereço na {{empresa_endereco}}, {{empresa_cidade}}, bem como o(a) Dr(a). {{advogado_1_nome}}, inscrito(a) na OAB sob o nº {{advogado_1_oab}}.</p>

<p><strong>PODERES:</strong> Pelo presente instrumento particular de procuração a rogo, o(a) outorgante nomeia e constitui seu bastante procurador o outorgado acima qualificado, a quem confere amplos e gerais poderes para em seu nome e por sua conta, representá-lo(la) perante o Instituto Nacional do Seguro Social – INSS e demais órgãos previdenciários, para requerer, receber, dar quitação, assinar requerimentos, recursos e demais documentos necessários ao reconhecimento e concessão de benefícios previdenciários e assistenciais, podendo ainda substabelecer esta com ou sem reserva de iguais poderes.</p>

<p>{{empresa_cidade}}, {{data_hoje_extenso}}.</p>

<br/><br/>

<p style="text-align:center">_____________________________________________<br/>
{{a_rogo_nome}}<br/>
(Assina a rogo de {{cliente_nome}})<br/>
CPF: {{a_rogo_cpf}}</p>
HTML,
];

// 3 — Procuração Incapaz
$modelos[] = [
    'nome'      => 'Procuração Representante Legal (Incapaz)',
    'categoria' => 'Procuração',
    'descricao' => 'Procuração outorgada por representante legal de pessoa incapaz',
    'conteudo'  => <<<'HTML'
<p style="text-align:center"><strong>PROCURAÇÃO</strong></p>

<p><strong>OUTORGANTE:</strong> {{cliente_nome}}, {{cliente_nacionalidade}}, {{cliente_estado_civil}}, {{cliente_profissao}}, portador(a) do CPF nº {{cliente_cpf}} e RG nº {{cliente_rg}}, residente e domiciliado(a) na {{cliente_endereco}}, {{cliente_cidade}}-{{cliente_uf}}, CEP {{cliente_cep}}, na qualidade de representante legal de:</p>

<p><strong>REPRESENTADO (INCAPAZ):</strong> {{incapaz_nome}}, portador(a) do CPF nº {{incapaz_cpf}}, nascido(a) em {{incapaz_data_nascimento}}.</p>

<p><strong>OUTORGADO:</strong> {{empresa_nome}}, inscrita no CNPJ sob o nº {{empresa_cnpj}}, com endereço na {{empresa_endereco}}, {{empresa_cidade}}, bem como o(a) Dr(a). {{advogado_1_nome}}, inscrito(a) na OAB sob o nº {{advogado_1_oab}}.</p>

<p><strong>PODERES:</strong> Pelo presente instrumento particular de procuração, o(a) outorgante, na qualidade de representante legal do incapaz acima qualificado, nomeia e constitui seu bastante procurador o outorgado acima qualificado, a quem confere amplos e gerais poderes para em nome do representado e por sua conta, representá-lo(la) perante o Instituto Nacional do Seguro Social – INSS e demais órgãos previdenciários, para requerer, receber, dar quitação, assinar requerimentos, recursos e demais documentos necessários ao reconhecimento e concessão de benefícios previdenciários e assistenciais, podendo ainda substabelecer esta com ou sem reserva de iguais poderes.</p>

<p>{{empresa_cidade}}, {{data_hoje_extenso}}.</p>

<br/><br/>

<p style="text-align:center">_____________________________________________<br/>
{{cliente_nome}}<br/>
CPF: {{cliente_cpf}}<br/>
(Representante Legal de {{incapaz_nome}})</p>
HTML,
];

// 4 — Procuração Filho Menor
$modelos[] = [
    'nome'      => 'Procuração Representante Legal (Filho Menor)',
    'categoria' => 'Procuração',
    'descricao' => 'Procuração outorgada por responsável legal de filho menor',
    'conteudo'  => <<<'HTML'
<p style="text-align:center"><strong>PROCURAÇÃO</strong></p>

<p><strong>OUTORGANTE:</strong> {{cliente_nome}}, {{cliente_nacionalidade}}, {{cliente_estado_civil}}, {{cliente_profissao}}, portador(a) do CPF nº {{cliente_cpf}} e RG nº {{cliente_rg}}, residente e domiciliado(a) na {{cliente_endereco}}, {{cliente_cidade}}-{{cliente_uf}}, CEP {{cliente_cep}}, na qualidade de responsável legal por:</p>

<p><strong>MENOR REPRESENTADO:</strong> {{filho_nome}}, portador(a) do CPF nº {{filho_cpf}}, nascido(a) em {{filho_data_nascimento}}.</p>

<p><strong>OUTORGADO:</strong> {{empresa_nome}}, inscrita no CNPJ sob o nº {{empresa_cnpj}}, com endereço na {{empresa_endereco}}, {{empresa_cidade}}, bem como o(a) Dr(a). {{advogado_1_nome}}, inscrito(a) na OAB sob o nº {{advogado_1_oab}}.</p>

<p><strong>PODERES:</strong> Pelo presente instrumento particular de procuração, o(a) outorgante, na qualidade de responsável legal pelo menor acima qualificado, nomeia e constitui seu bastante procurador o outorgado acima qualificado, a quem confere amplos e gerais poderes para em nome do menor e por sua conta, representá-lo(la) perante o Instituto Nacional do Seguro Social – INSS e demais órgãos previdenciários e assistenciais, para requerer, receber, dar quitação, assinar requerimentos, recursos e demais documentos necessários ao reconhecimento e concessão de benefícios previdenciários e assistenciais, podendo ainda substabelecer esta com ou sem reserva de iguais poderes.</p>

<p>{{empresa_cidade}}, {{data_hoje_extenso}}.</p>

<br/><br/>

<p style="text-align:center">_____________________________________________<br/>
{{cliente_nome}}<br/>
CPF: {{cliente_cpf}}<br/>
(Responsável Legal de {{filho_nome}})</p>
HTML,
];

// 5 — Contrato de Honorários 30%
$modelos[] = [
    'nome'      => 'Contrato de Honorários 30%',
    'categoria' => 'Contrato',
    'descricao' => 'Contrato de prestação de serviços advocatícios com honorários de 30% sobre o benefício',
    'conteudo'  => <<<'HTML'
<p style="text-align:center"><strong>CONTRATO DE PRESTAÇÃO DE SERVIÇOS ADVOCATÍCIOS</strong></p>

<p>Pelo presente instrumento particular de contrato de prestação de serviços advocatícios, as partes abaixo identificadas têm entre si justo e contratado o seguinte:</p>

<p><strong>CONTRATANTE:</strong> {{cliente_nome}}, {{cliente_nacionalidade}}, {{cliente_estado_civil}}, {{cliente_profissao}}, portador(a) do CPF nº {{cliente_cpf}} e RG nº {{cliente_rg}}, residente e domiciliado(a) na {{cliente_endereco}}, {{cliente_cidade}}-{{cliente_uf}}, CEP {{cliente_cep}}.</p>

<p><strong>CONTRATADO:</strong> {{empresa_nome}}, inscrita no CNPJ sob o nº {{empresa_cnpj}}, com endereço na {{empresa_endereco}}, {{empresa_cidade}}, CEP 69301-430, neste ato representada pelos advogados Dr. {{advogado_1_nome}}, inscrito na OAB sob o nº {{advogado_1_oab}}, e Dr. {{advogado_2_nome}}, inscrito na OAB sob o nº {{advogado_2_oab}}.</p>

<p><strong>CLÁUSULA PRIMEIRA – DO OBJETO</strong><br/>
O presente contrato tem por objeto a prestação de serviços advocatícios pelo CONTRATADO ao CONTRATANTE, consistindo no acompanhamento e defesa dos interesses do CONTRATANTE perante o Instituto Nacional do Seguro Social – INSS e demais órgãos competentes, visando ao reconhecimento e concessão do benefício previdenciário/assistencial.</p>

<p><strong>CLÁUSULA SEGUNDA – DOS HONORÁRIOS</strong><br/>
Pelos serviços ora contratados, o CONTRATANTE pagará ao CONTRATADO a título de honorários advocatícios o equivalente a <strong>30% (trinta por cento)</strong> sobre o valor total do benefício concedido, incluindo parcelas atrasadas (retroativo), calculados sobre o total bruto recebido.</p>

<p><strong>CLÁUSULA TERCEIRA – DO PAGAMENTO</strong><br/>
O pagamento dos honorários será realizado no momento do recebimento das parcelas em atraso (atrasados), deduzindo-se automaticamente do valor a ser recebido pelo CONTRATANTE, mediante a emissão de recibo.</p>

<p><strong>CLÁUSULA QUARTA – DAS OBRIGAÇÕES DO CONTRATADO</strong><br/>
O CONTRATADO obriga-se a: (a) envidar todos os esforços possíveis para a obtenção do benefício pleiteado; (b) manter o CONTRATANTE informado sobre o andamento do processo; (c) agir com diligência, competência e ética profissional.</p>

<p><strong>CLÁUSULA QUINTA – DAS OBRIGAÇÕES DO CONTRATANTE</strong><br/>
O CONTRATANTE obriga-se a: (a) fornecer ao CONTRATADO todos os documentos necessários para a instrução do processo; (b) comunicar ao CONTRATADO qualquer alteração em seus dados pessoais; (c) comparecer quando solicitado para assinatura de documentos ou prestação de informações.</p>

<p><strong>CLÁUSULA SEXTA – DA RESCISÃO</strong><br/>
O presente contrato poderá ser rescindido por qualquer das partes, mediante notificação prévia de 15 (quinze) dias, hipótese em que o CONTRATANTE deverá ressarcir o CONTRATADO pelas despesas processuais eventualmente incorridas.</p>

<p><strong>CLÁUSULA SÉTIMA – DO FORO</strong><br/>
Fica eleito o foro da Comarca de {{empresa_cidade}}-{{advogado_1_uf}} para dirimir quaisquer controvérsias oriundas do presente contrato.</p>

<p>E por estarem assim justos e contratados, as partes assinam o presente instrumento em duas vias de igual teor e forma.</p>

<p>{{empresa_cidade}}, {{data_hoje_extenso}}.</p>

<br/><br/>

<table style="width:100%; border-collapse:collapse;">
<tr>
<td style="width:50%; text-align:center; padding:10px;">
_____________________________________________<br/>
{{cliente_nome}}<br/>
CPF: {{cliente_cpf}}<br/>
<strong>CONTRATANTE</strong>
</td>
<td style="width:50%; text-align:center; padding:10px;">
_____________________________________________<br/>
{{advogado_1_nome}}<br/>
{{advogado_1_oab}}<br/>
<strong>CONTRATADO</strong>
</td>
</tr>
</table>
HTML,
];

$check = $conn->prepare("SELECT id FROM modelos_documentos WHERE nome = ? LIMIT 1");
$ins2  = $conn->prepare(
    "INSERT INTO modelos_documentos (nome, categoria, descricao, conteudo, criado_por) VALUES (?,?,?,?,'seed')"
);

foreach ($modelos as $m) {
    $check->bind_param('s', $m['nome']);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $log[] = "Modelo já existe: {$m['nome']}";
        continue;
    }
    $ins2->bind_param('ssss', $m['nome'], $m['categoria'], $m['descricao'], $m['conteudo']);
    $ins2->execute();
    $log[] = "Modelo inserido: {$m['nome']} (id={$conn->insert_id})";
}

// ── Output ───────────────────────────────────────────────────────────────────
?><!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><title>Seed</title>
<style>body{font-family:monospace;padding:2rem;background:#111;color:#0f0;}
li{margin:.3rem 0;}</style></head>
<body>
<h2 style="color:#ff0">Seed concluído</h2>
<ul>
<?php foreach ($log as $line): ?>
  <li><?= htmlspecialchars($line) ?></li>
<?php endforeach; ?>
</ul>
<p style="color:#888">Pode fechar esta aba agora.</p>
</body>
</html>
