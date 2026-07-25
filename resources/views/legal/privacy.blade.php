<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Política de Privacidade — MenuHub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white">
    <div class="min-h-screen">
        <div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <a href="/" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 mb-8 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao início
            </a>

            <h1 class="text-3xl font-bold mb-2">Política de Privacidade</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">Última atualização: julho de 2026</p>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-8 sm:p-10 space-y-6">
                <p>A <strong>MenuHub</strong> está comprometida em proteger a privacidade dos dados pessoais que trata. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e protegemos as informações dos usuários da nossa plataforma, em conformidade com a <strong>Lei Geral de Proteção de Dados Pessoais (LGPD - Lei nº 13.709/2018)</strong>.</p>

                <h2 class="text-xl font-semibold pt-4">1. Dados que Coletamos</h2>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li><strong>Dados de identificação:</strong> nome, telefone, e-mail, endereço de entrega</li>
                    <li><strong>Dados de uso:</strong> histórico de pedidos, preferências alimentares, observações</li>
                    <li><strong>Dados de comunicação:</strong> conversas via WhatsApp para atendimento de pedidos</li>
                    <li><strong>Dados de pagamento:</strong> informações de transações PIX (chave PIX, valor, data)</li>
                </ul>

                <h2 class="text-xl font-semibold pt-4">2. Finalidade do Tratamento</h2>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li>Processar e gerenciar pedidos realizados via plataforma</li>
                    <li>Comunicar status de pedidos via WhatsApp</li>
                    <li>Processar pagamentos e emissão de cobranças</li>
                    <li>Melhorar nossos serviços e experiência do usuário</li>
                    <li>Cumprir obrigações legais e regulatórias</li>
                </ul>

                <h2 class="text-xl font-semibold pt-4">3. Base Legal</h2>
                <p>Tratamos seus dados pessoais com base nas seguintes hipóteses legais previstas na LGPD:</p>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li><strong>Execução de contrato (Art. 7º, V):</strong> para processar seus pedidos e prestar o serviço contratado</li>
                    <li><strong>Legítimo interesse (Art. 7º, IX):</strong> para melhorar nossos serviços e prevenir fraudes</li>
                    <li><strong>Cumprimento de obrigação legal (Art. 7º, II):</strong> para atender exigências fiscais e regulatórias</li>
                </ul>

                <h2 class="text-xl font-semibold pt-4">4. Compartilhamento de Dados</h2>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li><strong>Processadores de pagamento:</strong> Mercado Pago, Asaas e Gerencianet, exclusivamente para processar pagamentos</li>
                    <li><strong>Meta (WhatsApp):</strong> para envio de mensagens transacionais e de atendimento</li>
                    <li><strong>Autoridades legais:</strong> quando exigido por lei ou ordem judicial</li>
                </ul>
                <p>Não vendemos dados pessoais a terceiros.</p>

                <h2 class="text-xl font-semibold pt-4">5. Armazenamento e Segurança</h2>
                <p>Seus dados são armazenados em servidores seguros na AWS (Amazon Web Services), com criptografia em repouso e em trânsito. Implementamos medidas técnicas e organizacionais para proteger seus dados contra acesso não autorizado, perda ou destruição.</p>
                <p>Os dados são retidos pelo período necessário à prestação dos serviços ou conforme exigido por lei. Após esse período, os dados são anonimizados ou eliminados.</p>

                <h2 class="text-xl font-semibold pt-4">6. Seus Direitos (LGPD)</h2>
                <p>Você tem direito a:</p>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li><strong>Confirmação e acesso:</strong> saber se tratamos seus dados e acessá-los</li>
                    <li><strong>Correção:</strong> solicitar a retificação de dados incompletos ou inexatos</li>
                    <li><strong>Anonimização ou exclusão:</strong> solicitar a anonimização ou exclusão de dados desnecessários</li>
                    <li><strong>Portabilidade:</strong> solicitar a transferência dos dados a outro fornecedor</li>
                    <li><strong>Oposição:</strong> opor-se ao tratamento realizado com base no legítimo interesse</li>
                </ul>
                <p>Para exercer seus direitos, entre em contato pelo e-mail: <a href="mailto:privacidade@menuhub.com.br" class="text-primary-600 hover:text-primary-500">privacidade@menuhub.com.br</a></p>

                <h2 class="text-xl font-semibold pt-4">7. Cookies</h2>
                <p>Utilizamos cookies essenciais para o funcionamento da plataforma. Não utilizamos cookies de rastreamento ou publicidade sem seu consentimento explícito.</p>

                <h2 class="text-xl font-semibold pt-4">8. Encarregado (DPO)</h2>
                <p>Nosso Encarregado pelo Tratamento de Dados Pessoais (DPO) pode ser contatado pelo e-mail: <a href="mailto:dpo@menuhub.com.br" class="text-primary-600 hover:text-primary-500">dpo@menuhub.com.br</a></p>

                <h2 class="text-xl font-semibold pt-4">9. Alterações nesta Política</h2>
                <p>Esta política pode ser atualizada periodicamente. Notificaremos alterações significativas através da plataforma ou por e-mail.</p>

                <h2 class="text-xl font-semibold pt-4">10. Foro</h2>
                <p>Fica eleito o foro da comarca de São Paulo/SP para dirimir quaisquer controvérsias decorrentes desta Política de Privacidade.</p>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-6 mt-8 text-sm text-slate-500 dark:text-slate-400">
                    <p>MenuHub Tecnologia Ltda. — CNPJ: 00.000.000/0001-00</p>
                    <p>E-mail: <a href="mailto:privacidade@menuhub.com.br" class="text-primary-600 hover:text-primary-500">privacidade@menuhub.com.br</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
