<?php
/*
Plugin Name: GA4 Tracker
Plugin URI: https://geanramos.com.br/#GA4-Tracker
Description: Envia eventos de redirecionamento para o Google Analytics 4 usando o Measurement Protocol.
Version: 1.0
Author: Gean Ramos
*/

// Medida de segurança
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// -- PARTE 1: PÁGINA DE ADMINISTRAÇÃO --

// Registra a página de configuração do nosso plugin
yourls_add_action( 'plugins_loaded', 'ga4_tracker_register_page' );
function ga4_tracker_register_page() {
    yourls_register_plugin_page(
        'ga4_tracker_config', // Slug da nossa página
        'GA4 Tracker Settings', // Título que aparece no menu
        'ga4_tracker_display_page' // Função que vai renderizar a página
    );
}

// Função que exibe o formulário de configuração e salva os dados
function ga4_tracker_display_page() {
    // Verificar se o formulário foi enviado
    if ( isset( $_POST['ga4_measurement_id'] ) && isset( $_POST['ga4_api_secret'] ) ) {
        // Validar nonce para segurança
        yourls_verify_nonce( 'ga4_tracker_nonce' );

        // Sanitizar e salvar os dados
        $measurement_id = trim( $_POST['ga4_measurement_id'] );
        $api_secret = trim( $_POST['ga4_api_secret'] );

        yourls_update_option( 'ga4_measurement_id', $measurement_id );
        yourls_update_option( 'ga4_api_secret', $api_secret );

        echo '<p style="color: green;">Configurações salvas com sucesso!</p>';
    }

    // Obter as configurações salvas para preencher o formulário
    $current_measurement_id = yourls_get_option( 'ga4_measurement_id', '' );
    $current_api_secret = yourls_get_option( 'ga4_api_secret', '' );

    // Formulário HTML
    $nonce = yourls_create_nonce( 'ga4_tracker_nonce' );
    echo <<<HTML
    <h2>Configurações do GA4 Tracker</h2>
    <p>Insira aqui as credenciais obtidas na sua propriedade do Google Analytics 4.</p>
    <form method="post">
        <input type="hidden" name="nonce" value="$nonce" />
        <p>
            <label for="ga4_measurement_id">Measurement ID (ID da Métrica)</label><br/>
            <input type="text" id="ga4_measurement_id" name="ga4_measurement_id" value="$current_measurement_id" size="30" placeholder="G-TZBPFV014K" />
        </p>
        <p>
            <label for="ga4_api_secret">API Secret (Segredo da API)</label><br/>
            <input type="text" id="ga4_api_secret" name="ga4_api_secret" value="$current_api_secret" size="50" placeholder="7d5bS_TgQA2OOiJXPUPBmg" />
        </p>
        <p>
            <input type="submit" value="Salvar Configurações" class="button" />
        </p>
    </form>
HTML;
}

// -- PARTE 2: LÓGICA DE RASTREAMENTO --

// Adiciona nossa função de rastreamento ao gancho 'pre_redirect'
yourls_add_action( 'pre_redirect', 'ga4_tracker_send_event', 10, 2 );

function ga4_tracker_send_event( $long_url, $keyword ) {
    // Obter as credenciais salvas
    $measurement_id = yourls_get_option( 'ga4_measurement_id' );
    $api_secret = yourls_get_option( 'ga4_api_secret' );

    // Se as credenciais não estiverem configuradas, não faz nada
    if ( empty( $measurement_id ) || empty( $api_secret ) ) {
        return;
    }

    // 1. Montar a URL do Measurement Protocol
    $ga4_url = 'https://www.google-analytics.com/mp/collect?api_secret=' . $api_secret . '&measurement_id=' . $measurement_id;

    // 2. Criar um client_id pseudo-anônimo para o usuário
    // O GA4 exige um client_id. Vamos criar um a partir do IP e do User Agent do usuário.
    $user_ip = yourls_get_user_ip();
    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $client_id = sha1( $user_ip . $user_agent ); // Usamos sha1 para anonimizar

    // 3. Construir o corpo (payload) da requisição em formato de array PHP
    $payload = [
        'client_id' => $client_id,
        'events' => [
            [
                'name' => 'yourls_redirect',
                'params' => [
                    'page_location' => $long_url, // A URL de destino
                    'page_title' => yourls_get_keyword_title( $keyword ),
                    'short_url' => yourls_link( $keyword ),
                    'engagement_time_msec' => '1', // Parâmetro obrigatório para contar como conversão
                ],
            ],
        ],
    ];

    // 4. Enviar os dados para o Google usando a função interna do YOURLS
    wp_remote_post( $ga4_url, [
        'headers' => [
            'Content-Type' => 'application/json; charset=utf-8',
        ],
        'body' => json_encode( $payload ),
        'blocking' => false, // Importante: 'false' para não atrasar o redirecionamento
    ]);
}
