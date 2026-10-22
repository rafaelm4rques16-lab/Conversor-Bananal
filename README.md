# Conversor Bananal 🍌 - O conversor de arquivos nada banal!

Sistema web completo para conversão de arquivos de múltiplos formatos, desenvolvido estritamente de acordo com as especificações e regras de negócio contidas em [requisitos.md](requisitos.md).

---

## 🛠️ Stack Tecnológico

- **Backend**: PHP 8.2 estruturado e modularizado com sessões nativas (`session_start()`).
- **Banco de Dados**: MySQL gerenciado via **PDO (PHP Data Objects)** com *Prepared Statements* em todas as operações, prevenindo 100% de injeções SQL (*SQL Injections*).
- **Frontend**: HTML5, PHP, CSS3, **Tailwind CSS** (design responsivo priorizando *Mobile-First*), Lucide Icons, JSZip e pdf-lib.

---

## 🔒 Regras de Negócio e Segurança (CORE)

1. **Tratamento Seguro de Senhas**:
   - Todas as senhas de usuários utilizam obrigatoriamente a função nativa `password_hash($senha, PASSWORD_BCRYPT)` para geração de hash e `password_verify()` para conferência.
2. **Página de Históricos**:
   - Registra todas as conversões de arquivos realizadas, exibindo nomes, extensões (origem e destino), tamanhos, status, usuário e data/hora.
3. **Trilha de Auditoria Contínua**:
   - Mantém logs detalhados e imutáveis de **qualquer alteração** realizada no sistema (cadastros, logins com sucesso ou com falha, alterações cadastrais de perfil, trocas de senha, conversões e exclusões de registros), salvando endereço IP e *User Agent*.
   - Exportação completa dos logs em **CSV** e **JSON**.
4. **Mensagens e Feedbacks na Interface**:
   - **TOASTS**: Notificações flutuantes e elegantes com tempo de expiração e indicador de progresso para retornos de sucesso, erro, atenção ou informação.
   - **MODAIS**: Eliminação de caixas de diálogo nativas do navegador (`alert` e `confirm`), substituídas por componentes de Modal universais e responsivos.

---

## 📁 Estrutura de Arquivos

```
├── config/
│   └── bd.php                 # Conexão segura PDO com MySQL, auto-instalação e helpers de auditoria
├── backend/
│   ├── auth_login.php         # Autenticação de usuários e registro de log
│   ├── auth_register.php      # Cadastro com hash Bcrypt e sessão automática
│   ├── auth_logout.php        # Encerramento seguro da sessão
│   ├── profile_update.php     # Atualização de nome e troca de senha segura
│   ├── convert_api.php        # API de processamento e persistência das conversões
│   ├── history_delete.php     # Exclusão unitária ou limpeza de histórico com auditoria
│   └── export_logs.php        # Exportador de auditoria para CSV e JSON
├── includes/
│   ├── header.php             # Cabeçalho global, navbar responsiva, tema banana e metadados
│   ├── footer.php             # Rodapé padronizado com inicialização dos scripts
│   ├── toasts.php             # Sistema global de notificações Toast
│   └── modals.php             # Sistema de Modais universais
├── index.php                  # Painel principal do Conversor Bananal (Passos 1 a 5)
├── historico.php              # Painel de histórico de conversões e estatísticas
├── auditoria.php              # Painel gerencial e relatório de auditoria
├── manual.php                 # Guia de formatos e documentação para o usuário
├── login.php                  # Tela de login estilizada
├── registro.php               # Tela de cadastro com Bcrypt
├── perfil.php                 # Painel do usuário autenticado
├── uploads/                   # Diretório para armazenamento local
└── requisitos.md              # Documento original de requisitos
```

---

## 🚀 Como Executar o Projeto

### Pré-requisitos
- **PHP 8+** (com extensões `pdo_mysql` e `gd` habilitadas).
- **MySQL / MariaDB** (ex: XAMPP).

### Passo a Passo

1. **Iniciar o MySQL**:
   Certifique-se de que o MySQL está rodando na porta padrão `3306`.
   *(O script `config/bd.php` cria automaticamente a base `conversor_bananal`, todas as tabelas e o usuário inicial se ainda não existirem).*

2. **Iniciar o Servidor PHP**:
   No diretório do projeto, execute no terminal:
   ```bash
   php -S 127.0.0.1:8000
   ```

3. **Acessar no Navegador**:
   Abra: [http://127.0.0.1:8000](http://127.0.0.1:8000)

### 👤 Credencial Inicial Padrão (Demonstração)
- **E-mail**: `admin@bananal.com`
- **Senha**: `bananal123`
*(Você também pode cadastrar novos usuários livremente na página de Registro).*
