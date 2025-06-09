# GA4 Tracker for YOURLS

Este plugin integra sua instalação do YOURLS ao Google Analytics 4 (GA4), permitindo que você rastreie cada clique em seus links encurtados como um evento personalizado. A comunicação é feita de forma segura e eficiente através do Measurement Protocol v2 do Google, sem impactar a velocidade de redirecionamento para o usuário.

**Versão do Plugin:** 1.0
**Compatibilidade com YOURLS:** 1.7.x ou superior
**Autor:** Seu Nome

---

## Índice

- [O que este plugin faz?](#o-que-este-plugin-faz)
- [Por que usar este plugin?](#por-que-usar-este-plugin)
- [Como Funciona?](#como-funciona)
- [Instalação e Configuração](#instalação-e-configuração)
- [Verificando o Funcionamento](#verificando-o-funcionamento)
- [Compatibilidade com Parâmetros UTM](#compatibilidade-com-parâmetros-utm)
- [Contribuições](#contribuições)
- [Licença](#licença)

## O que este plugin faz?

* **Integração Nativa com GA4:** Envia dados diretamente para a sua propriedade do Google Analytics 4.
* **Rastreamento de Eventos:** Cada clique em um link encurtado é registrado como um evento personalizado chamado `yourls_redirect`.
* **Uso do Measurement Protocol:** Utiliza o método oficial e recomendado pelo Google para envio de dados do servidor, garantindo velocidade e confiabilidade.
* **Painel de Configuração Simples:** Adiciona uma página de administração de fácil uso para você inserir suas credenciais do GA4 com segurança.

## Por que usar este plugin?

1.  **Velocidade Máxima:** Diferente de métodos baseados em JavaScript, o rastreamento via Measurement Protocol acontece no servidor. Isso significa que o redirecionamento do seu usuário não sofre **nenhum atraso**.
2.  **Confiabilidade Superior:** A comunicação servidor-servidor é mais robusta e não é afetada por bloqueadores de anúncios (ad-blockers) ou configurações de privacidade do navegador do usuário, garantindo uma coleta de dados mais precisa.
3.  **Dados Detalhados para Análise:** Ter um evento `yourls_redirect` dedicado permite criar relatórios, funis e segmentos específicos no GA4 para analisar o desempenho dos seus links encurtados, algo que não é possível com o rastreamento padrão de visualização de página.
4.  **Segurança e Boas Práticas:** Suas credenciais (API Secret) são armazenadas de forma segura no banco de dados do YOURLS e a comunicação com o Google é feita via HTTPS.

## Como Funciona?

O plugin utiliza uma abordagem moderna e eficiente para o rastreamento:

1.  **Gancho (Hook) de Redirecionamento:** O plugin usa o gancho `pre_redirect` do YOURLS, que é uma função acionada segundos antes de um link ser redirecionado.
2.  **Coleta de Dados:** Neste momento, o plugin coleta informações essenciais: a URL longa de destino, a palavra-chave (URL curta) e dados anônimos do usuário, como o endereço de IP e o User-Agent (informações do navegador).
3.  **Criação de `client_id`:** O GA4 exige um `client_id` para identificar um "usuário". O plugin cria um identificador pseudo-anônimo ao combinar e criptografar (com SHA1) o IP e o User-Agent, respeitando a privacidade.
4.  **Envio via Measurement Protocol:** Com todos os dados em mãos, o plugin monta uma requisição JSON e a envia para os servidores do Google Analytics em segundo plano, sem aguardar uma resposta, garantindo que o usuário seja redirecionado instantaneamente.

## Instalação e Configuração

Siga estes passos para ter o plugin funcionando em minutos.

### 1. Instalação do Plugin

1.  Crie uma pasta chamada `ga4-tracker` dentro do diretório `/user/plugins/` da sua instalação do YOURLS.
2.  Crie um arquivo chamado `plugin.php` dentro da pasta `ga4-tracker`.
3.  Copie e cole o [código do plugin](LINK_PARA_O_SEU_ARQUIVO_PHP_NO_GITHUB) no arquivo `plugin.php` que você criou.
4.  Vá até a página de administração do seu YOURLS, clique em **Manage Plugins** e ative o plugin **"GA4 Tracker"**.

### 2. Configuração no Google Analytics 4

Para o plugin funcionar, você precisa de um **ID da Métrica** e um **Segredo da API**.

1.  **Obtenha o ID da Métrica:**
    * Acesse sua propriedade do GA4.
    * Vá em **Administrador** > **Fluxos de dados**.
    * Clique no seu fluxo de dados da Web. Seu ID da Métrica (ex: `G-XXXXXXXXXX`) estará visível. Copie-o.

2.  **Crie o Segredo da API:**
    * Na mesma tela de "Fluxos de dados", clique em **Segredos da API do Measurement Protocol**.
    * Clique em **Criar**, dê um apelido (ex: `Plugin YOURLS`) e clique em **Criar** novamente.
    * **Copie o "Valor do segredo" imediatamente.** Ele não será exibido novamente.

Para mais detalhes, consulte a [documentação oficial do Google](https://developers.google.com/analytics/devguides/collection/protocol/v2/getting-started).

### 3. Configuração do Plugin no YOURLS

1.  Após ativar o plugin, um novo link chamado **"GA4 Tracker Settings"** aparecerá no menu de administração.
2.  Clique nele, insira o **ID da Métrica** e o **Segredo da API** que você obteve e clique em **"Salvar Configurações"**.

## Verificando o Funcionamento

A melhor forma de verificar se os eventos estão sendo enviados é usando o **DebugView** do GA4:

1.  Clique em um dos seus links encurtados para gerar um evento.
2.  No GA4, vá para **Administrador** > **Exibição de depuração** (DebugView).
3.  Em poucos segundos, você verá o evento `yourls_redirect` aparecer na linha do tempo, confirmando que a integração está funcionando.

## Compatibilidade com Parâmetros UTM

Este plugin é **100% compatível** com URLs que contêm parâmetros UTM (`utm_source`, `utm_medium`, etc.) e, na verdade, melhora o rastreamento.

| Passo | Ação | Resultado |
| :--- | :--- | :--- |
| **1. Clique** | Usuário clica em `sho.rt/promo23`. | O processo é iniciado. |
| **2. Plugin** | Nosso plugin envia o evento `yourls_redirect` para o GA4 com a URL longa e seus UTMs. | Você obtém dados de clique em tempo real. |
| **3. YOURLS** | Redireciona o navegador do usuário para a URL longa com os UTMs. | O usuário chega ao destino correto. |
| **4. GA4** | O script no seu site de destino lê os UTMs da URL e atribui a sessão à campanha. | Seus relatórios de Aquisição de Tráfego ficam corretos. |

Você obtém o melhor dos dois mundos: o rastreamento de campanha padrão do GA4 funcionando perfeitamente e um evento personalizado para analisar os cliques especificamente.

## Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para abrir uma *issue* para relatar bugs ou sugerir melhorias, ou enviar um *pull request* com suas alterações.

## Licença

Este plugin é distribuído sob a licença [MIT](?tab=MIT-1-ov-file).
