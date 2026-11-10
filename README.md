# Conversor Bananal 🍌 - O conversor de arquivos nada banal!

Sistema web completo para conversão de arquivos em múltiplos formatos, desenvolvido estritamente de acordo com as especificações e regras de negócio contidas em [requisitos.md](requisitos.md) e no repositório [Conversor-Bananal](https://github.com/rafaelm4rques16-lab/Conversor-Bananal.git).

---

## 🛠️ Stack Tecnológico

- **Backend**: PHP 8+ estruturado e modularizado com sessões nativas (`session_start()`).
- **Banco de Dados**: MySQL gerenciado via **PDO (PHP Data Objects)** com *Prepared Statements* em 100% das operações, prevenindo injeções SQL (*SQL Injections*). Inclui fallback automático para PDO SQLite quando testado sem MySQL ativo.
- **Frontend**: HTML5, PHP, CSS3, **Tailwind CSS** (design responsivo priorizando *Mobile-First*), Lucide Icons, JSZip e pdf-lib.

---

## 🍌 Funcionalidades e Regras de Negócio (CORE)

1. **Menu Lateral Estilo Convertio (`includes/sidebar.php`)**:
   - Menu lateral retrátil (*side drawer*) acessível a qualquer momento pelo botão **Menu** ou pelo banner.
   - Categorias rápidas: Conversor de Imagem, Áudio, Documento, Vídeo, Apresentação e Compactados.
   - Atalhos de conversão rápida (*JPG para PNG*, *PNG para WEBP*, *WAV para MP3*, *TXT para PDF*, etc.).
   - Filtro de pesquisa em tempo real dentro do menu.

2. **Upload Duplo (Drag & Drop e Botão)**:
   - Permite que o usuário arraste arquivos para a área pontilhada (*Drag & Drop*) ou clique no botão de seleção estilo Convertio.
   - Suporte a seleção e conversão múltipla (*batch conversion*).

3. **Detecção Automática e Sugestões Inteligentes de Formato**:
   - Identifica instantaneamente o tipo de arquivo (*Imagem*, *Áudio*, *Vídeo*, *Documento*).
   - Apresenta botões (*pills*) com sugestões recomendadas e caixa de seleção com formatos compatíveis.

4. **Conversão e Processamento Digital**:
   - Animação temática de liquidificador digital batendo os arquivos.
   - Conversão real de imagens via HTML5 Canvas (JPG, PNG, WEBP, BMP, SVG).
   - Conversão de áudio via Web Audio API e encoder PCM WAV.
   - Geração de documentos PDF a partir de textos e imagens via `pdf-lib`.
   - Download individual de arquivos convertidos ou download em lote via pacote **ZIP** com `JSZip`.

5. **Tratamento Seguro de Senhas (Bcrypt)**:
   - Senhas criptografadas exclusivamente com `password_hash($senha, PASSWORD_BCRYPT)` e verificadas com `password_verify()`.

6. **Histórico e Trilha de Auditoria Contínua**:
   - **Histórico**: Página completa de histórico com filtros por formato, período e busca textual.
   - **Auditoria**: Registro imutável de todas as ações de usuários (logins com sucesso ou falha, cadastros, alterações de perfil, trocas de senha, conversões e exclusões) com IP e User Agent.
   - Exportação completa da auditoria nos formatos **CSV** e **JSON**.

7. **Interface Amigável (Toasts e Modais)**:
   - **TOASTS**: Notificações flutuantes estilizadas no tema banana com barra de tempo para feedbacks de sucesso, erro, aviso e informação.
   - **MODAIS**: Substituição completa dos diálogos nativos do navegador (`alert` e `confirm`) por modais elegantes com confirmação assíncrona.

---

## 📁 Estrutura de Arquivos Modular

```
├── config/
│   └── bd.php                 # Conexão PDO (MySQL / fallback SQLite), tabelas e helpers de auditoria
├── backend/
│   ├── auth_login.php         # Autenticação segura de usuários
│   ├── auth_register.php      # Cadastro com hash Bcrypt e sessão automática
│   ├── auth_logout.php        # Encerramento seguro da sessão
│   ├── profile_update.php     # Atualização de nome e troca de senha segura com Bcrypt
│   ├── convert_api.php        # API de processamento e persistência das conversões via PDO
│   ├── history_delete.php     # Exclusão unitária ou limpeza de histórico com auditoria
│   └── export_logs.php        # Exportador de auditoria para CSV e JSON
├── includes/
│   ├── header.php             # Cabeçalho global, navbar responsiva, tema banana e metadados
│   ├── sidebar.php            # Menu lateral retrátil estilo Convertio com atalhos de conversores
│   ├── footer.php             # Rodapé padronizado
│   ├── toasts.php             # Sistema global de notificações Toast
│   └── modals.php             # Sistema universal de Modais customizados
├── assets/
│   └── images/                # Elementos visuais e vetores extraídos do projeto Bananal
├── index.php                  # Painel principal do Conversor Bananal (Passos 1 a 5)
├── historico.php              # Painel de histórico de conversões e métricas
├── auditoria.php              # Painel gerencial e trilha de auditoria
├── manual.php                 # Guia de formatos, prós/contras e FAQ
├── login.php                  # Tela de login estilizada
├── registro.php               # Tela de cadastro com Bcrypt
├── perfil.php                 # Painel do usuário autenticado
├── conversor_bananal.html     # Versão standalone offline (executável direto no navegador)
├── uploads/                   # Diretório de armazenamento local
└── requisitos.md              # Documento original de requisitos
```

---

## 🚀 Como Executar o Projeto

### Opção 1: Servidor Local PHP (Recomendado)
No terminal, dentro da pasta do projeto, execute:
```bash
php -S 127.0.0.1:8000
```
Em seguida, acesse no navegador:
[http://127.0.0.1:8000](http://127.0.0.1:8000)

### Opção 2: XAMPP / WampServer / Laragon
1. Copie esta pasta para o diretório `htdocs` ou `www`.
2. Certifique-se de que o Apache e o MySQL estejam ativos no painel.
3. Acesse `http://localhost/sua-pasta/index.php`.

### Opção 3: Teste Imediato Offline
Abra o arquivo `conversor_bananal.html` com dois cliques no Google Chrome, Microsoft Edge ou Firefox para testar todas as conversões de mídia e o menu lateral imediatamente!

---

## 👤 Credenciais de Teste / Demonstração
- **E-mail**: `admin@bananal.com`
- **Senha**: `bananal123`
*(Você também pode registrar novos usuários a qualquer momento pela tela de Cadastro).*
