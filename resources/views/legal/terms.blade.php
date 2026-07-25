<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Termos de Uso — MenuHub</title>
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

            <h1 class="text-3xl font-bold mb-2">Termos de Uso</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">Última atualização: julho de 2026</p>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-8 sm:p-10 space-y-6">
                <h2 class="text-xl font-semibold">1. Aceitação dos Termos</h2>
                <p>Ao utilizar a plataforma MenuHub ("Plataforma"), você concorda com estes Termos de Uso. Se não concordar com qualquer parte destes termos, não utilize a Plataforma.</p>

                <h2 class="text-xl font-semibold pt-4">2. Definições</h2>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li><strong>Plataforma:</strong> sistema SaaS MenuHub de gestão de pedidos e cardápios</li>
                    <li><strong>Restaurante:</strong> estabelecimento comercial que utiliza a Plataforma</li>
                    <li><strong>Cliente/Consumidor:</strong> pessoa física que realiza pedidos através do WhatsApp do restaurante</li>
                    <li><strong>Usuário:</strong> funcionário do restaurante autorizado a acessar o sistema administrativo</li>
                    <li><strong>Conteúdo:</strong> cardápios, preços, imagens e informações inseridas pelo restaurante</li>
                </ul>

                <h2 class="text-xl font-semibold pt-4">3. Cadastro e Conta</h2>
                <p>Para utilizar a Plataforma como restaurante, é necessário criar uma conta com dados precisos e atualizados. O restaurante é responsável por manter a confidencialidade de suas credenciais de acesso e por todas as atividades realizadas em sua conta.</p>

                <h2 class="text-xl font-semibold pt-4">4. Responsabilidades do Restaurante</h2>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li>Manter cardápios, preços e informações atualizados</li>
                    <li>Processar e preparar os pedidos recebidos dentro do prazo informado</li>
                    <li>Garantir a qualidade dos produtos e serviços oferecidos</li>
                    <li>Cumprir as normas sanitárias e legais aplicáveis ao seu negócio</li>
                    <li>Obter consentimento dos clientes finais para compartilhar dados com a Plataforma</li>
                </ul>

                <h2 class="text-xl font-semibold pt-4">5. Responsabilidades da MenuHub</h2>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li>Manter a Plataforma operacional com disponibilidade mínima de 99,5% ao mês</li>
                    <li>Processar pedidos e encaminhá-los corretamente ao restaurante</li>
                    <li>Proteger os dados pessoais conforme a LGPD e nossa Política de Privacidade</li>
                    <li>Prestar suporte técnico dentro do horário comercial</li>
                </ul>

                <h2 class="text-xl font-semibold pt-4">6. Planos e Pagamentos</h2>
                <p>A MenuHub opera sob modelo de assinatura mensal (SaaS). Os planos e preços estão disponíveis na página de planos da Plataforma. O pagamento é processado no início de cada ciclo mensal e não são realizados reembolsos proporcionais por cancelamento antecipado.</p>

                <h2 class="text-xl font-semibold pt-4">7. Cancelamento</h2>
                <p>O restaurante pode cancelar sua assinatura a qualquer momento. O acesso à Plataforma permanece ativo até o final do período já pago. Após o cancelamento, os dados serão mantidos por 90 dias para possível reativação, sendo então anonimizados ou eliminados.</p>

                <h2 class="text-xl font-semibold pt-4">8. Limitação de Responsabilidade</h2>
                <p>A MenuHub não se responsabiliza por:</p>
                <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-300">
                    <li>Problemas de conexão de internet do restaurante ou clientes</li>
                    <li>Indisponibilidade de serviços terceiros (WhatsApp, gateways de pagamento)</li>
                    <li>Conteúdo inserido pelo restaurante na Plataforma</li>
                    <li>Danos decorrentes de uso indevido da Plataforma</li>
                </ul>

                <h2 class="text-xl font-semibold pt-4">9. Propriedade Intelectual</h2>
                <p>A Plataforma, seu código-fonte, design e marcas são propriedade exclusiva da MenuHub. O restaurante concede à MenuHub licença não exclusiva para exibir e processar seu conteúdo (cardápios, imagens) para fins de operação da Plataforma.</p>

                <h2 class="text-xl font-semibold pt-4">10. Disposições Gerais</h2>
                <p>Estes Termos de Uso são regidos pela legislação brasileira. Fica eleito o foro da comarca de São Paulo/SP para dirimir quaisquer controvérsias. Caso qualquer disposição destes termos seja considerada inválida, as demais permanecem em vigor.</p>
            </div>
        </div>
    </div>
</body>
</html>
