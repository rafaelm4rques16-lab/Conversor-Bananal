# 🍌 Conversor Bananal Pro

Bem-vindo à nova versão expandida do **Conversor Bananal Pro**, desenvolvida em `d:\A pasta secreta` sem modificar nenhuma versão pré-existente.

Esta versão adiciona suporte completo a:
1. **Conversão de Pacote Office (Word, PowerPoint, Excel para PDF e vice-versa)**:
   - Word (`.docx`) ➔ `PDF`, `HTML`, `TXT`
   - PowerPoint (`.pptx`) ➔ `PDF`, `HTML Slides`, `TXT`
   - Excel (`.xlsx`) ➔ `PDF`, `CSV`, `JSON`, `HTML`
   - PDF (`.pdf`) ➔ Word (`.docx`), `HTML`, `TXT`
2. **Suíte de Transcrição e Mídia (Inspirada no Clipto)**:
   - **Conversor de Áudio & Vídeo para Texto**: Envie MP3, WAV, MP4, MOV e baixe em DOCX (Word), PDF, SRT ou TXT.
   - **Transcritor & Sumarizador do YouTube**: Cole o link do YouTube e obtenha a transcrição sincronizada, sumário com pontos-chave e legendas.
   - **Transcrição de Voz ao Vivo**: Fale no microfone e exporte em tempo real para DOCX, PDF, SRT ou TXT.
   - **Gerador de Legendas Profissionais**: Transforme qualquer roteiro ou fala em arquivos `.srt` e `.vtt` com minutagens automáticas.
3. **Core Bananal**:
   - Identidade visual amarelada e elementos gráficos de banana (estilo *BananaJobs*).
   - Upload com Drag & Drop e botão tradicional (estilo *Convertio*).
   - Menu lateral retrátil (*side drawer*) no mobile e fixo no desktop.
   - Banco de Dados MySQL com PDO (prepared statements) e **fallback automático para SQLite** para rodar sem configuração de banco.
   - Senhas protegidas exclusivamente com **Bcrypt**.
   - Mensagens e erros exibidos em **Toasts animados** e caixas de diálogo tratadas em **Modais universais**.

---

## 📂 Estrutura de Arquivos

```text
d:\A pasta secreta\
├── app/
│   ├── config/
│   │   └── database.php         # PDO com MySQL + Fallback automático para SQLite + Auditoria
│   ├── services/
│   │   ├── OfficeConverter.php  # Leitor e conversor nativo de Word, PowerPoint, Excel e PDF
│   │   ├── DocxGenerator.php    # Gerador nativo de arquivos Word (.docx) em PHP puro
│   │   ├── PdfGenerator.php     # Gerador nativo de arquivos PDF estruturados
│   │   ├── MediaTranscriber.php # Motor de transcrição de áudio, legendas SRT/VTT e YouTube
│   │   ├── GeneralConverter.php # Motor de conversão de imagens (GD), dados e markdown
│   │   └── Storage.php          # Gerenciador de armazenamento temporário e limpeza a cada 2h
│   └── views/
│       ├── layout/
│       │   ├── header.php       # Navbar amarelada com menu de ferramentas
│       │   ├── sidebar.php      # Menu lateral estilo Convertio com histórico recente
│       │   ├── footer.php       # Rodapé padronizado
│       │   └── modals_toasts.php# Componentes de Modal e Toasts
│       ├── home.php             # Conversor universal com drag & drop
│       ├── office.php           # Hub especializado para Word, PPT, Excel e PDF
│       ├── audio_texto.php      # Conversor de áudio e vídeo para texto
│       ├── youtube.php          # Transcritor e sumarizador de vídeos do YouTube
│       ├── vivo.php             # Transcrição ao vivo por microfone
│       ├── legendas.php         # Gerador de legendas SRT / VTT
│       ├── historico.php        # Histórico geral da sessão
│       ├── auditoria.php        # Trilha de auditoria gerencial com exportação CSV e JSON
│       ├── login.php            # Login com Bcrypt
│       ├── registro.php         # Cadastro com Bcrypt
│       └── perfil.php           # Painel de perfil do usuário
├── public/
│   └── assets/
│       ├── css/app.css          # Animações, paleta de banana e efeitos visuais
│       └── js/
│           ├── toast.js         # Sistema de Toasts reativos
│           ├── modal.js         # Sistema de Modais nativos
│           ├── converter.js     # Upload, detecção e barra de progresso
│           ├── live_speech.js   # Reconhecimento de voz em tempo real
│           └── youtube_tool.js  # Processador de links e transcrições do YouTube
├── uploads/                     # Diretório de arquivos enviados
├── converted/                   # Diretório de arquivos convertidos
├── api.php                      # Roteador central de requisições e downloads
├── index.php                    # Ponto de entrada modular da aplicação
├── standalone.html              # Versão 100% offline para executar direto no navegador
├── requisitos.md                # Documentação técnica de requisitos
└── README.md                    # Documentação do projeto
```

---

## 🚀 Como Executar

### Opção 1: Servidor Embutido do PHP (Recomendado)
Abra o terminal e execute:
```powershell
cd "d:\A pasta secreta"
php -S 127.0.0.1:8000
```
Acesse no navegador: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

*(O sistema conecta automaticamente ao MySQL ou assume o fallback SQLite local sem travar nada).*

### Opção 2: Teste Imediato Offline
Dê um duplo clique no arquivo [`standalone.html`](file:///d:/A%20pasta%20secreta/standalone.html) para testar o conversor, o microfone ao vivo e a geração de legendas de imediato, sem precisar de nenhum servidor web ativo.

---

### 👤 Credenciais de Teste
- **E-mail:** `admin@bananal.com`
- **Senha:** `bananal123`
*(Novos usuários também podem ser cadastrados na tela de registro com senhas criptografadas em Bcrypt).*
