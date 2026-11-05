# Requisitos do Sistema - Conversor Bananal 🍌

## 1. Visão Geral do Projeto
O **Conversor Bananal** é uma aplicação web completa inspirada na flexibilidade e usabilidade do **Convertio** e na estética amarelada, descontraída e visual da **BananaJobs**.

---

## 2. Stack Tecnológica
- **Backend:** PHP 8.x estruturado com sessões nativas (`session_start()`, cookies HttpOnly e SameSite).
- **Banco de Dados:** MySQL via PDO com Prepared Statements e suporte a fallback automático para PDO SQLite.
- **Frontend:** HTML5 semântico, PHP estruturado, CSS3 com animações personalizadas e Tailwind CSS (Mobile-First).
- **Criptografia:** Senhas protegidas exclusivamente com a função nativa `password_hash($senha, PASSWORD_BCRYPT)`.
- **Notificações & Modais:** Notificações em TOAST e substituição total de `alert()` e `confirm()` nativos por Modais elegantes.

---

## 3. Regras de Negócio (CORE)
1. **Identidade Visual Bananal:**
   - Paleta amarelada vibrante com tons de banana (`#FACC15`, `#FDE047`, `#FEF08A`, `#CA8A04`).
   - Mascote banana exclusivo e elementos gráficos pelo site.
2. **Envio de Arquivos (Convertio):**
   - Suporte a Drag & Drop (arrastar e soltar) e botão tradicional de seleção.
   - Envio de múltiplos arquivos simultaneamente.
3. **Detecção e Sugestões Inteligentes:**
   - O sistema detecta a extensão e o MIME type e sugere automaticamente para quais formatos compatíveis o arquivo pode ser convertido.
4. **Transformação e Download Seguro:**
   - Processamento no servidor e download por token exclusivo associado à sessão.
5. **Menu Lateral (Sidebar):**
   - Menu lateral retrátil com atalhos, histórico recente e status do sistema.
6. **Autenticação Segura com Bcrypt:**
   - Login, cadastro e edição de perfil de usuários com proteção Bcrypt.
7. **Trilha de Auditoria:**
   - Registro de IP, navegador (User-Agent), usuário, ação realizada e data/hora, com exportação para CSV e JSON.
8. **Versão Standalone Offline:**
   - Arquivo `conversor_bananal.html` permitindo testes imediatos no navegador com dois cliques sem depender de servidor web.

---

## 4. Credenciais de Teste
- **E-mail:** `admin@bananal.com`
- **Senha:** `bananal123`
*(Novos usuários podem ser cadastrados livremente na tela de registro).*
