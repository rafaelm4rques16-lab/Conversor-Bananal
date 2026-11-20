# Requisitos & Especificações - Conversor Bananal Pro 🍌

## 1. Visão Geral
Esta versão foi criada na pasta `d:\A pasta secreta` como uma evolução completa do **Conversor Bananal**, mantendo fidelidade às regras de negócio essenciais e integrando capacidades avançadas de conversão de documentos do pacote Office (Word, PowerPoint, Excel para PDF) e ferramentas multimídia de transcrição inspiradas nas referências fornecidas (Clipto):
- **Conversão de Áudio e Vídeo para Texto (DOCX, PDF, SRT, TXT)**
- **Transcrição e Sumarização de Vídeos do YouTube**
- **Transcrição de Voz ao Vivo pelo Microfone (Web Speech API)**
- **Gerador de Legendas Profissionais (.SRT / .VTT com minutagens)**
- **Conversão de Documentos Word (.docx), PowerPoint (.pptx) e Excel (.xlsx) para PDF**

---

## 2. Stack Tecnológica
- **Backend:** PHP 8.x estruturado com sessões nativas (`session_start()`).
- **Banco de Dados:** MySQL via PDO com *Prepared Statements* e suporte a **fallback transparente para PDO SQLite** (zero configuração necessária para inicialização imediata).
- **Frontend:** HTML5 semântico, PHP modular, CSS3 com animações amareladas e **Tailwind CSS** (design responsivo Mobile-First).
- **Segurança:** Senhas protegidas exclusivamente com `password_hash($senha, PASSWORD_BCRYPT)` e verificação via `password_verify()`.
- **Notificações & Diálogos:** Sistema flutuante de **Toasts** e substituição universal de `alert()` e `confirm()` nativos por **Modais temáticos**.

---

## 3. Matriz de Conversões Implementadas

### A. Documentos Office & PDF
- **Microsoft Word (.docx):** ➔ PDF, HTML, TXT
- **Microsoft PowerPoint (.pptx):** ➔ PDF, HTML (apresentação de slides), TXT
- **Microsoft Excel (.xlsx):** ➔ PDF, CSV (Excel compatível), JSON, HTML (Tabela estilizada)
- **Adobe PDF (.pdf):** ➔ Word (.docx nativo), HTML, TXT

### B. Multimídia & Transcrição (Clipto Style)
- **Áudio para Texto (MP3, WAV, OGG, M4A):** ➔ Word (.docx), PDF, Legendas (.srt), TXT
- **Vídeo para Texto (MP4, MOV, WEBM):** ➔ Word (.docx), PDF, Legendas (.srt), TXT
- **YouTube para Texto:** Link do YouTube ➔ Transcrição com minutagens, Sumário Executivo com IA Insights, Legendas (.srt) e DOCX
- **Voz ao Vivo:** Microfone em tempo real no navegador ➔ Exportação para Word (.docx), PDF, SRT e TXT
- **Gerador de Legendas:** Texto / Roteiro ➔ Arquivo .SRT e .VTT cronometrado

### C. Imagens & Dados
- **Imagens:** PNG, JPG, JPEG, WEBP, GIF, BMP, Base64, PDF
- **Dados:** CSV ➔ JSON / HTML / XML; JSON ➔ CSV / HTML / XML

---

## 4. Credenciais Padrão para Testes
- **E-mail:** `admin@bananal.com`
- **Senha:** `bananal123`
*(Novos usuários podem ser registrados livremente pela interface com criptografia Bcrypt).*
