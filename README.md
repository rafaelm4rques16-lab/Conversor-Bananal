# 🍌 Conversor Bananal

Um conversor de arquivos online multi-formato inspirado na experiência fluida do **Convertio** e na identidade visual descontraída, moderna e amarelada da **BananaJobs**.

---

## 🚀 Stack Tecnológica
- **Backend:** PHP 8.x estruturado com sessões nativas (`session_start()`, cookies HttpOnly e SameSite).
- **Banco de Dados:** MySQL com **PDO** e *Prepared Statements* (100% blindado contra SQL Injection) com **fallback automático para PDO SQLite** caso o MySQL não esteja ativo.
- **Frontend:** HTML5 semântico, PHP modular, CSS3 com animações personalizadas e **Tailwind CSS** (design responsivo priorizando **Mobile-First**).
- **Segurança & Criptografia:** Senhas tratadas exclusivamente com a função nativa `password_hash($senha, PASSWORD_BCRYPT)` e verificadas via `password_verify()`.
- **Trilha de Auditoria:** Registro de IP, navegador (User-Agent), usuário e alterações do sistema, com exportação para **CSV** e **JSON**.
- **Notificações & Diálogos:** Sistema próprio de **Toasts** reativos e substituição total de `alert()` e `confirm()` nativos por **Modais estilizados**.

---

## 📂 Estrutura Modular de Pastas

```text
C:\ConversorBananal\
├── config/
│   └── bd.php                    # Conexão PDO (MySQL com fallback transparente para SQLite)
├── backend/
│   ├── auth_session.php          # Inicialização segura de sessão nativa PHP
│   ├── auth_login.php            # Autenticação de usuários com verificação Bcrypt
│   ├── auth_register.php         # Cadastro com criptografia nativa Bcrypt
│   ├── auth_logout.php           # Encerramento seguro de sessão
│   ├── profile_update.php        # Atualização cadastral e troca de senha segura
│   ├── detect_format.php         # Endpoint AJAX que detecta tipo e busca sugestões
│   ├── convert_file.php          # Endpoint AJAX de conversão de arquivos e registro no BD
│   ├── convert_api.php           # API de persistência das conversões via PDO
│   ├── download.php              # Download seguro do arquivo com verificação de token e sessão
│   ├── list_history.php          # Listagem do histórico da sessão em tempo real (JSON)
│   ├── history_delete.php        # Exclusão unitária ou limpeza de histórico com auditoria
│   └── export_logs.php           # Exportador de logs de auditoria (CSV e JSON)
├── includes/
│   ├── header.php                # Cabeçalho global, navbar responsiva e metadados
│   ├── sidebar.php               # Menu lateral retrátil estilo Convertio com atalhos
│   ├── footer.php                # Rodapé padronizado
│   ├── toasts.php                # Sistema de notificações flutuantes Toast
│   └── modals.php                # Sistema de Modais universais (substitui alert/confirm)
├── services/
│   ├── FileConverter.php         # Motor isolado de conversão multi-formato (GD, CSV/JSON/XML, MD/HTML/PDF)
│   └── StorageService.php        # Gerenciamento de uploads, nomes seguros e limpeza de temporários (2h)
├── assets/
│   ├── css/
│   │   └── custom.css            # Paleta de tons de banana, animações, scrollbar e dropzone ativa
│   ├── js/
│   │   ├── toast.js              # Sistema de Toasts claros e elegantes (Sucesso, Erro, Alerta, Info)
│   │   ├── modal.js              # Interceptador e substituto nativo de alert() e confirm() em Tailwind
│   │   └── app.js                # Orquestração do Frontend (Drag & Drop, detecção, AJAX e barra de progresso)
│   └── img/
│       ├── banana-icon.svg       # Ícone vetorizado da banana
│       └── banana-mascot.svg     # Mascote banana com óculos escuros estilo BananaJobs
├── index.php                     # Painel principal do Conversor Bananal
├── historico.php                 # Histórico completo de conversões com filtros e métricas
├── auditoria.php                 # Trilha de auditoria gerencial com exportação CSV e JSON
├── manual.php                    # Guia de formatos, comparativos e FAQ
├── login.php                     # Tela de login estilizada com Bcrypt
├── registro.php                  # Tela de cadastro de novos usuários com Bcrypt
├── perfil.php                    # Painel do usuário autenticado com estatísticas
├── conversor_bananal.html        # Versão standalone para execução imediata no navegador sem servidor
├── test_db.php                   # Painel utilitário de diagnóstico e criação de tabelas
├── requisitos.md                 # Documentação de requisitos do sistema
└── README.md                     # Documentação do projeto
```

---

## 🌟 Regras de Negócio e Funcionalidades

1. **Identidade Visual Bananal:**
   - Paleta rica em amarelos (`#FACC15`, `#FDE047`, `#FEF08A`, `#CA8A04`), tons de carvão escuro para contraste e detalhes de banana fresca.
   - Mascote banana vetorizado exclusivo e elementos gráficos pelo site, inspirados na estética da [BananaJobs](https://bananajobs.com.br/).

2. **Upload Amigável (Estilo Convertio):**
   - Suporte completo a **Drag & Drop** (arrastar e soltar arquivos em qualquer parte da área) ou seleção tradicional por botão.
   - Suporte a múltiplos arquivos simultâneos.

3. **Detecção Automática & Sugestões Inteligentes:**
   - Ao adicionar um arquivo, o sistema lê sua extensão e MIME type e consulta a matriz de conversão.
   - Sugere formatos compatíveis instantaneamente em um seletor visual interativo.

4. **Transformação & Download:**
   - Processamento em PHP no servidor com acompanhamento em barra de progresso.
   - Geração de link seguro de download por token exclusivo associado à sessão.

5. **Menu Lateral (Sidebar):**
   - Inspirado no Convertio, traz atalhos de ferramentas, estatísticas e histórico de conversões recentes da sessão do usuário.
   - Totalmente responsivo: gaveta retrátil no mobile e barra lateral ergonômica no desktop.

6. **Autenticação Segura com Bcrypt:**
   - Login, cadastro e edição de perfil de usuários com proteção Bcrypt nativa.

7. **Trilha de Auditoria com Exportação:**
   - Registra IP, navegador (User-Agent), usuário, ação realizada e data/hora, com exportação para **CSV** e **JSON**.

8. **Notificações em TOAST e Diálogos em MODAL:**
   - Erros e avisos nunca utilizam telas brutas: são apresentados em **Toasts animados**.
   - As caixas nativas do navegador (`alert` e `confirm`) foram substituídas por **Modais temáticos do Bananal**.

---

## 👤 Credenciais de Teste
- **E-mail:** `admin@bananal.com`
- **Senha:** `bananal123`
*(Novos usuários também podem ser cadastrados livremente na tela de registro com senhas criptografadas em Bcrypt).*

---

## 🚀 Como Executar e Testar

### Opção 1: Servidor Embutido do PHP (Recomendado)
No terminal:
```powershell
cd C:\ConversorBananal
php -S 127.0.0.1:8000
```
Acesse no navegador: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

### Opção 2: XAMPP / WampServer / Laragon
1. Aponte ou copie a pasta para o diretório web (`htdocs` ou `www`).
2. Inicie o Apache e o MySQL. O script [`config/bd.php`](file:///C:/ConversorBananal/config/bd.php) cria automaticamente o banco, todas as tabelas e o usuário administrador inicial com Bcrypt.
3. Acesse `http://localhost/ConversorBananal/index.php`.

### Opção 3: Teste Imediato Offline (Zero Servidor)
Dê um duplo clique no arquivo [`conversor_bananal.html`](file:///C:/ConversorBananal/conversor_bananal.html) para abrir diretamente no navegador (Chrome, Edge ou Firefox) e testar a conversão client-side instantânea sem precisar de servidor ativo.

---

🍌 **Conversor Bananal** — Código modular, seguro e pronto para futuros upgrades!
